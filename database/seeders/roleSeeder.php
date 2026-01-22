<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};

class roleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Admin → كل الصلاحيات
        $admin->syncPermissions(Permission::all());

        // User → صلاحيات العرض فقط
        $viewPermissions = Permission::where('name', 'like', 'view_%')->get();
        $user->syncPermissions($viewPermissions);
    }
}