<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\users\create;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\{Role, Permission};

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $users = User::with('roles')->where('id', '!=', $user->id)->paginate(10);

        return view('dashboard.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($perm) {
            // Group by the first part of the permission name (e.g., view_products -> products)
            $parts = explode('_', $perm->name);
            return count($parts) > 1 ? $parts[1] : 'general';
        });

        return view('dashboard.users.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(create $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request) {
            $currentUser = Auth::user();

            // Restriction for subAdmin
            if ($currentUser->hasRole('subAdmin')) {
                // Cannot assign admin role
                if ($data['user_type'] === 'admin') {
                    abort(403, 'Sub Admin cannot create Admin users.');
                }
                // Cannot sync any permissions (as per user request "ranks and permissions")
                $data['permissions'] = [];
            }

            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'phone' => $data['phone'],
                'user_type' => in_array($data['user_type'], ['admin', 'subAdmin']) ? 'admin' : 'user',
                'is_verfived' => true,
            ]);

            // Assign Role
            $user->assignRole($data['user_type']);

            // Sync Permissions
            if ($data['user_type'] === 'admin') {
                $user->syncPermissions(Permission::all());
            } elseif ($data['user_type'] === 'subAdmin') {
                // Only full Admin can sync permissions for subAdmin
                if ($currentUser->hasRole('admin')) {
                    $user->syncPermissions($request->input('permissions', []));
                } else {
                    $user->syncPermissions([]);  // SubAdmin cannot assign permissions to other subAdmins
                }
            } else {  // user type
                $user->syncPermissions([]);
            }
        });

        return response()->json(['message' => __('User created successfully')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::with(['roles', 'permissions'])->findOrFail($id);
        $currentUser = Auth::user();

        // Prevent subAdmin from editing full Admin
        if ($currentUser->hasRole('subAdmin') && $user->hasRole('admin')) {
            abort(403, 'Sub Admin cannot edit full Admin users.');
        }

        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($perm) {
            $parts = explode('_', $perm->name);
            return count($parts) > 1 ? $parts[1] : 'general';
        });

        return view('dashboard.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|string|max:20',
            'user_type' => 'required|in:user,admin,subAdmin',
            'password' => 'nullable|string|min:6'
        ]);

        DB::transaction(function () use ($user, $data, $request) {
            $currentUser = Auth::user();

            // Restriction for subAdmin
            if ($currentUser->hasRole('subAdmin')) {
                // Cannot change user type (role)
                if ($data['user_type'] !== $user->roles->first()?->name) {
                    abort(403, 'Sub Admin cannot change user roles.');
                }
                // Target cannot be admin
                if ($user->hasRole('admin')) {
                    abort(403, 'Sub Admin cannot update Admin users.');
                }
            }

            $user->update([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'user_type' => in_array($data['user_type'], ['admin', 'subAdmin']) ? 'admin' : 'user',
            ]);

            if (!empty($data['password'])) {
                $user->password = $data['password'];
                $user->save();
            }

            // Roles and Permissions management (Admin only)
            if ($currentUser->hasRole('admin')) {
                $user->syncRoles([$data['user_type']]);

                if ($data['user_type'] === 'admin') {
                    $user->syncPermissions(Permission::all());
                } elseif ($data['user_type'] === 'subAdmin') {
                    $user->syncPermissions($request->input('permissions', []));
                } else {
                    $user->syncPermissions([]);
                }
            }
        });

        return response()->json(['message' => __('User updated successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back();
    }
}
