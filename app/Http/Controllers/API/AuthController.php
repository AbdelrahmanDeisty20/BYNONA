<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Mail};
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\{LoginRequest, RegisterRequest, editProfileRequest};
use App\Http\Requests\{EmailVerfived, resendCode};
use App\Models\{Otp, RefreshToken, User};
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());
        $user->user_type = 'user';
        $user->save();

        if ($user) {
            $code = random_int(100000, 999999);
            $codeHash = Hash::make((string) $code);
            $expiresAt = now()->addMinutes(10);
            Mail::raw("كود التحقق الخاص بتسجيل الدخول: $code\nصالح لمدة 10 دقائق.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('كود التحقق OTP')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
            Otp::create([
                'user_id' => $user->id,
                'otp' => $codeHash,
                'type' => 'register',
                'expires_at' => $expiresAt,
                'email' => $user->email,
                'verified' => false,
                'is_used' => false,
            ]);
        }
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => __('Registered successfully please check your email'),
            'data' => $user,
            'token' => $token,
        ]);
    }

    public function verfivedCode(EmailVerfived $request)
{
    $data = $request->validated();
    $code = $data['otp'];
    $email = $data['email'];
    $email = ($data['email']);

    // تحقق من وجود المستخدم أولًا
    $user = User::where('email', $email)->first();
    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'البريد الإلكتروني غير موجود'
        ], 404);
    }
    $otpRecord = Otp::where("email", $email)
        ->where("type", "register")
        ->where("verified", false)
        ->where("is_used", false)
        ->where("expires_at", ">=", now())
        ->first();

    if (!$otpRecord) {
        return response()->json([
            'success' => false,
            'message' => 'الكود غير صالح أو منتهي'
        ], 400);
    }

    // مقارنة الكود مع الهاش
    if (!Hash::check($code, $otpRecord->otp)) {
        return response()->json([
            'success' => false,
            'message' => 'الكود غير صحيح'
        ], 400);
    }

    // تحديث الـ OTP
    $otpRecord->update([
        'verified' => true,
        'is_used' => true,
        'updated_at' => now(),
    ]);

    // تحديث المستخدم إلى Verified
    $otpRecord->user->update([
        'is_verfived' => true,
        'updated_at' => now(),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'تم التحقق بنجاح، يمكنك تسجيل الدخول الآن'
    ], 200);
}



    public function resendCode(resendCode $request)
{
    $data = $request->validated();

    // جلب المستخدم
    $user = User::where('email', $data['email'])->first();

    if (!$user) {
        return response()->json([
            'success' => false,
            'message' => 'المستخدم غير موجود',
        ], 404);
    }

    // التحقق إذا المستخدم محقق بالفعل
    if ($user->is_verfived) {
        return response()->json([
            'success' => false,
            'message' => 'تم التحقق بالفعل',
        ], 200);
    }

    // تعطيل أي OTP قديم غير مستخدم
    Otp::where('user_id', $user->id)
        ->where('type', 'register')
        ->where('is_used', false)
        ->update([
            'is_used' => true,
            'verified' => false,
            'reset_token' => null,
        ]);

    // إنشاء كود OTP جديد
    $otpCode = rand(100000, 999999);
    $codeHash = Hash::make((string) $otpCode);

    $otp = Otp::create([
        'user_id' => $user->id,
        'otp' => $codeHash,
        'expires_at' => now()->addMinutes(5),
        'is_used' => false,
        'type' => 'register',
        "email" => $data["email"],
    ]);

    // إرسال الكود بالبريد
    Mail::raw("تم اعادة  كود التحقق الخاص بتسجيل الدخول: $otpCode\nصالح لمدة 10 دقائق.", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('كود التحقق OTP')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

    return response()->json([
        'success' => true,
        'message' => 'تم إعادة إرسال الكود بنجاح',
    ]);
}


    public function login(LoginRequest $request)
{
    $credentials = $request->only(['email', 'password']);

    $user = User::whereEmail($credentials['email'])->first();

    if (! $user || ! Hash::check($credentials['password'], $user->password)) {
        return response()->json([
            'status' => 'error',
            'message' => __('Invalid credentials'),
        ], 401);
    }

    if (! $user->is_verfived) {
        return response()->json([
            'status' => 'error',
            'message' => __('Please verify your email first'),
        ], 403); // Forbidden
    }

    // Generate refresh token only after verification
    $refreshToken = Str::random(64);
    RefreshToken::create([
        'user_id' => $user->id,
        'token' => hash('sha256', $refreshToken),
        'expires_at' => Carbon::now()->addDays(7),
    ]);

    // ===============================
    // تسجيل FCM Token لو موجود في Request
    // ===============================
    if ($request->has('fcm_token') && $request->has('device_type')) {
        \App\Models\UserFcmToken::updateOrCreate(
            ['fcm_token' => $request->fcm_token],
            [
                'user_id' => $user->id,
                'device_type' => $request->device_type
            ]
        );
    }

    return response()->json([
        'status' => 'success',
        'message' => __('Logged in successfully'),
        'user' => $user,
        'token' => $user->createToken('API Token')->plainTextToken,
        'refresh_token' => $refreshToken,
    ]);
}



    public function updateProfile(editProfileRequest $request)
    {
        $user = Auth::user();

        $user->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => __('updated profile successfully and new information'),
            'data' => $user,
        ]);
    }

    public function socialGoogle($provider)
    {
        request()->validate([
            'access_token' => 'required',
        ]);

        try {

            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->userFromToken(request('access_token'));

            // الاسم لو موجود
            $name = $socialUser->getName();

            if ($name) {
                $parts = explode(' ', $name, 2);

                $firstName = $parts[0] ?? '';
                $lastName = $parts[1] ?? '';
            } else {
                // fallback لو مفيش اسم
                $firstName = $socialUser->getEmail();
                $lastName = '';
            }

            // إنشاء أو جلب المستخدم
            $user = User::firstOrCreate(
                ['email' => $socialUser->getEmail()],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    // 'phone'      => $socialUser->getPhone(),
                    'password' => 'social_login',
                ]
            );

            // إنشاء التوكن
            $token = $user->createToken('api')->plainTextToken;

            return response()->json([
                'success' => true,
                'token' => $token,
                'user' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function refresh(Request $request)
    {
        $request->validate([
            'refresh_token' => 'required|string',
        ]);

        $hashedToken = hash('sha256', $request->refresh_token);

        // نبحث عن الـ refresh token الصحيح وغير منتهي
        $tokenRecord = RefreshToken::where('token', $hashedToken)
            ->where('expires_at', '>', now())
            ->first();

        if (! $tokenRecord) {
            return response()->json(['message' => 'Invalid or expired refresh token'], 401);
        }

        $user = $tokenRecord->user;

        // إنشاء Access Token جديد
        $newAccessToken = $user->createToken('API Token')->plainTextToken;

        // 🔄 Rotation: حذف الـ Refresh Token القديم
        $tokenRecord->delete();

        // إنشاء Refresh Token جديد
        $newRefreshToken = Str::random(64);
        RefreshToken::create([
            'user_id' => $user->id,
            'token' => hash('sha256', $newRefreshToken),
            'expires_at' => Carbon::now()->addDays(7), // صلاحية جديدة
        ]);

        return response()->json([
            'access_token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,  // الجديد
            'token_type' => 'Bearer',
        ]);
    }
    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            "success" => true,
            "message" => "Logged out successfully"
        ]);
    }
}