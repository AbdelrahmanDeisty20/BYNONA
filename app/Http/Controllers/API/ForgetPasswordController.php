<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\{Hash, Mail};
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\{passwordRequest, codeRequest, forgetPasswordRequest};
use App\Http\Requests\resendCode;
use App\Models\{Otp, User};
use Carbon\Carbon;

class ForgetPasswordController extends Controller
{
    // إرسال OTP للإيميل
    public function sendEmail(forgetPasswordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود',
            ], 404);
        }

        // تعطيل أي OTP قديم غير مستخدم
        Otp::where('user_id', $user->id)
            ->where('type', 'forgot_password')
            ->where('is_used', false)
            ->update([
                'is_used' => true,
                'verified' => false,
                'reset_token' => null,
            ]);

        // توليد OTP جديد
        $otpCode = random_int(100000, 999999);
        $otpHash = Hash::make((string)$otpCode);

        $otp = Otp::create([
            'user_id' => $user->id,
            'otp' => $otpHash,
            'expires_at' => now()->addMinutes(10),
            'email' => $user->email,
            'verified' => false,
            'type' => 'forgot_password',
            'is_used' => false,
        ]);

        // إرسال الكود بالبريد
        Mail::raw("كود التحقق الخاص بالباسورد : $otpCode\nصالح لمدة 10 دقائق.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('كود التحقق OTP')
                ->from(config('mail.from.address'), config('mail.from.name'));
        });

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال كود التحقق إلى بريدك الإلكتروني',
        ]);
    }

    // تحقق من OTP وإعطاء reset_token
    public function sendCode(codeRequest $request)
    {
        $data = $request->validated();

        // البحث عن آخر OTP صالح
        $otp = Otp::where('email', $data['email'])
            ->where('is_used', false)
            ->where('expires_at', '>=', now())
            ->where('type', 'forgot_password')
            ->latest()
            ->first();

        if (!$otp || !Hash::check($data['otp'], $otp->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'الكود غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        // توليد reset_token مشفر
        $resetToken = Str::random(64);
        $otp->update([
            'verified' => true,
            'is_used' => true,
            'reset_token' => Hash::make($resetToken), // مشفر
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق بنجاح',
            'reset_token' => $resetToken, // المستخدم يستخدمه لتغيير كلمة المرور
        ]);
    }

    // إعادة تعيين كلمة المرور
    public function password(passwordRequest $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود',
            ], 404);
        }

        // التحقق من reset_token مع آخر OTP صالح
        $otp = Otp::where('user_id', $user->id)
            ->where('verified', true)
            ->where('expires_at', '>=', now())
            ->latest()
            ->first();

        if (!$otp || !Hash::check($data['reset_token'], $otp->reset_token)) {
            return response()->json([
                'success' => false,
                'message' => 'رمز إعادة تعيين كلمة المرور غير صالح أو منتهي',
            ], 422);
        }

        // تحديث كلمة المرور
        $user->password = $data['password'];
        $user->save();

        // حذف كل الأكواد القديمة
        Otp::where('user_id', $user->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث كلمة المرور بنجاح',
        ]);
    }

    // إعادة إرسال OTP
    public function resendCode(resendCode $request)
    {
        $data = $request->validated();
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موجود',
            ], 404);
        }

        // تعطيل أي OTP قديم
        Otp::where('user_id', $user->id)
            ->where('type', 'forgot_password')
            ->where('is_used', false)
            ->update([
                'is_used' => true,
                'verified' => false,
                'reset_token' => null,
            ]);

        // توليد OTP جديد
        $otpCode = random_int(100000, 999999);
        $otpHash = Hash::make((string)$otpCode);

        Otp::create([
            'user_id' => $user->id,
            'otp' => $otpHash,
            'expires_at' => now()->addMinutes(10),
            'email' => $user->email,
            'verified' => false,
            'type' => 'forgot_password',
            'is_used' => false,
        ]);

        // إرسال الكود بالبريد
        Mail::raw("تم اعادة ارسال كود التحقق الخاص بالباسورد :  $otpCode\nصالح لمدة 10 دقائق.", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('كود التحقق OTP')
                ->from(config('mail.from.address'), config('mail.from.name'));
        });

        return response()->json([
            'success' => true,
            'message' => 'تم إعادة إرسال الكود بنجاح',
        ]);
    }
}