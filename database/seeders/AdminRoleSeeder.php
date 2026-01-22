<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        User::where('email', 'admin@admin.com')->delete();
        Role::whereIn('name', ['admin', 'subAdmin', 'user'])->delete();
        Permission::query()->delete();

        // =============================
        // Permissions mapped to routes
        // =============================
        $modules = [
            'dashboard' => [
                'view_dashboard' => 'dashbord.dashboard,dashboard.categories.sort,dashboard.categories.sort.update,charts/orders,charts/revenue,dashboard.lang.switch',
            ],
            'categories' => [
                'view_categories' => 'categories.index,categories.show',
                'create_categories' => 'categories.create,categories.store',
                'edit_categories' => 'categories.edit,categories.update',
                'delete_categories' => 'categories.destroy',
            ],
            'products' => [
                'view_products' => 'products.index,products.show',
                'create_products' => 'products.create,products.store',
                'edit_products' => 'products.edit,products.update',
                'delete_products' => 'products.destroy',
            ],
            'attributes' => [
                'view_attributes' => 'attributes.index,attributes.show',
                'create_attributes' => 'attributes.create,attributes.store',
                'edit_attributes' => 'attributes.edit,attributes.update',
                'delete_attributes' => 'attributes.destroy',
            ],
            'orders' => [
                'view_orders' => 'orders.index,orders.show',
                'edit_orders' => 'orders.cancelOrder,orders.processingOrder,orders.compeleteOrder',
                'delete_orders' => 'orders.destroy',
            ],
            'users' => [
                'view_users' => 'users.index,users.show',
                'create_users' => 'users.create,users.store',
                'edit_users' => 'users.edit,users.update',
                'delete_users' => 'users.destroy',
            ],
            'offers' => [
                'view_offers' => 'offers.index,offers.show',
                'create_offers' => 'offers.create,offers.store',
                'edit_offers' => 'offers.edit,offers.update',
                'delete_offers' => 'offers.destroy',
            ],
            'banners' => [
                'view_banners' => 'banners.index,banners.show',
                'create_banners' => 'banners.create,banners.store',
                'edit_banners' => 'banners.edit,banners.update',
                'delete_banners' => 'banners.destroy',
            ],
            'product_banners' => [
                'view_product_banners' => 'productBanners.index,productBanners.show',
                'create_product_banners' => 'productBanners.create,productBanners.store',
                'edit_product_banners' => 'productBanners.edit,productBanners.update',
                'delete_product_banners' => 'productBanners.destroy',
            ],
            'brands' => [
                'view_brands' => 'brands.index,brands.show',
                'create_brands' => 'brands.create,brands.store',
                'edit_brands' => 'brands.edit,brands.update',
                'delete_brands' => 'brands.destroy',
            ],
            'contacts' => [
                'view_contacts' => 'contacts.index',
                'edit_contacts' => 'contacts.edit,contacts.update',
            ],
            'settings' => [
                'view_settings' => 'settings.index,settings.show',
                'edit_settings' => 'settings.edit,settings.update',
            ],
            'notifications' => [
                'view_notifications' => 'dashboard.notifications.index,dashboard.notifications.fetch',
                'edit_notifications' => 'dashboard.notifications.markAsRead,dashboard.notifications.markAllAsRead',
            ],
            'reviews' => [
                'view_reviews' => 'reviews.index',
                'delete_reviews' => 'reviews.destroy',
            ],
        ];

        $allPermissions = [];

        foreach ($modules as $permissions) {
            foreach ($permissions as $name => $routes) {
                $perm = Permission::updateOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    ['routes' => $routes]
                );
                $allPermissions[] = $perm;
            }
        }

        // =============================
        // Roles
        // =============================
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $subAdminRole = Role::create(['name' => 'subAdmin', 'guard_name' => 'web']);
        $userRole = Role::create(['name' => 'user', 'guard_name' => 'web']);

        $viewPermissions = Permission::where('name', 'like', 'view_%')->get();
        $adminRole->syncPermissions($allPermissions);
        $subAdminRole->syncPermissions($viewPermissions);
        $userRole->syncPermissions($viewPermissions);

        // =============================
        // Admin User
        // =============================
        $admin = User::create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '01000000000',
            'user_type' => 'admin',
            'is_verfived' => true,
            'password' => 123456789,
        ]);

        $admin->assignRole($adminRole);
        $admin->syncPermissions($allPermissions);
    }
}
