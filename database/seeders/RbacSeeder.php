<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RbacSeeder extends Seeder
{
    /**
     * Seed role and permission data.
     */
    public function run(): void
    {
        $now = now();

        $roles = [
            ['name' => 'Guest', 'slug' => 'guest', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'B2C Customer', 'slug' => 'b2c_customer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'B2B User', 'slug' => 'b2b_user', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Internal User', 'slug' => 'internal_user', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Internal User - Sales', 'slug' => 'internal_user_sales', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Internal User - Finance', 'slug' => 'internal_user_finance', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Internal User - Support', 'slug' => 'internal_user_support', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Internal User - Logistics', 'slug' => 'internal_user_logistics', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Admin', 'slug' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Delegated Admin', 'slug' => 'delegated_admin', 'created_at' => $now, 'updated_at' => $now],
        ];

        $permissions = [
            ['name' => 'home', 'slug' => 'home', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'home.page', 'slug' => 'home.page', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'products.index', 'slug' => 'products.index', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'products.productDetails', 'slug' => 'products.productDetails', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'products.technical-resources.download', 'slug' => 'products.technical-resources.download', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cart.data', 'slug' => 'cart.data', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cart.items.store', 'slug' => 'cart.items.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cart.items.update', 'slug' => 'cart.items.update', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cart.items.destroy', 'slug' => 'cart.items.destroy', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'cart.checkout.submit', 'slug' => 'cart.checkout.submit', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'guest-cart.data', 'slug' => 'guest-cart.data', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'guest-cart.items.store', 'slug' => 'guest-cart.items.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'guest-cart.items.update', 'slug' => 'guest-cart.items.update', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'guest-cart.items.destroy', 'slug' => 'guest-cart.items.destroy', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'checkout.page', 'slug' => 'checkout.page', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'checkout.submit', 'slug' => 'checkout.submit', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'checkout.coupon.validate', 'slug' => 'checkout.coupon.validate', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'checkout.reorder.pricing', 'slug' => 'checkout.reorder.pricing', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'checkout.buy-now', 'slug' => 'checkout.buy-now', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'guest.checkout.buy-now', 'slug' => 'guest.checkout.buy-now', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'orders.index', 'slug' => 'orders.index', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'orders.store', 'slug' => 'orders.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'orders.reorder.checkout', 'slug' => 'orders.reorder.checkout', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'orders.reorder.checkout.submit', 'slug' => 'orders.reorder.checkout.submit', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'orders.reorder', 'slug' => 'orders.reorder', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'orders.show', 'slug' => 'orders.show', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'orders.update', 'slug' => 'orders.update', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'orders.destroy', 'slug' => 'orders.destroy', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'pi-quotation.generate', 'slug' => 'pi-quotation.generate', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'pi-quotation.store', 'slug' => 'pi-quotation.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'quotation.create', 'slug' => 'quotation.create', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'quotation.store', 'slug' => 'quotation.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'diagnostic-quiz', 'slug' => 'diagnostic-quiz', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'diagnostic-quiz.store', 'slug' => 'diagnostic-quiz.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'book-meeting', 'slug' => 'book-meeting', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'book-meeting.store', 'slug' => 'book-meeting.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'about', 'slug' => 'about', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'contact', 'slug' => 'contact', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'contact.store', 'slug' => 'contact.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'privacy', 'slug' => 'privacy', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'terms', 'slug' => 'terms', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'refund-policy', 'slug' => 'refund-policy', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'faq', 'slug' => 'faq', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'maintenance', 'slug' => 'maintenance', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer.profile.preview', 'slug' => 'customer.profile.preview', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer.profile.update', 'slug' => 'customer.profile.update', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer.profile.password.update', 'slug' => 'customer.profile.password.update', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer.addresses.preview', 'slug' => 'customer.addresses.preview', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer.orders.preview', 'slug' => 'customer.orders.preview', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer.addresses.store', 'slug' => 'customer.addresses.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'customer.addresses.update', 'slug' => 'customer.addresses.update', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'support-tickets.index', 'slug' => 'support-tickets.index', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'support-tickets.store', 'slug' => 'support-tickets.store', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'support-tickets.attachments.download', 'slug' => 'support-tickets.attachments.download', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'support-tickets.show', 'slug' => 'support-tickets.show', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'order.confirmation', 'slug' => 'order.confirmation', 'created_at' => $now, 'updated_at' => $now],
        ];

        $guestRolePermissions = [
            'home',
            'home.page',
            'products.index',
            'products.productDetails',
            'products.technical-resources.download',
            'cart.data',
            'cart.items.store',
            'cart.items.update',
            'cart.items.destroy',
            'guest-cart.data',
            'guest-cart.items.store',
            'guest-cart.items.update',
            'guest-cart.items.destroy',
            'checkout.page',
            'checkout.buy-now',
            'guest.checkout.buy-now',
            'quotation.create',
            'quotation.store',
            'diagnostic-quiz',
            'diagnostic-quiz.store',
            'about',
            'contact',
            'contact.store',
            'privacy',
            'terms',
            'refund-policy',
            'faq',
            'maintenance',
        ];

        $signedInRolePermissions = [
            'home',
            'home.page',
            'products.index',
            'products.productDetails',
            'products.technical-resources.download',
            'cart.data',
            'cart.items.store',
            'cart.items.update',
            'cart.items.destroy',
            'cart.checkout.submit',
            'guest-cart.data',
            'guest-cart.items.store',
            'guest-cart.items.update',
            'guest-cart.items.destroy',
            'checkout.page',
            'checkout.submit',
            'checkout.coupon.validate',
            'checkout.reorder.pricing',
            'checkout.buy-now',
            'guest.checkout.buy-now',
            'orders.index',
            'orders.store',
            'orders.reorder.checkout',
            'orders.reorder.checkout.submit',
            'orders.reorder',
            'orders.show',
            'orders.update',
            'orders.destroy',
            'pi-quotation.generate',
            'pi-quotation.store',
            'quotation.create',
            'quotation.store',
            'diagnostic-quiz',
            'diagnostic-quiz.store',
            'book-meeting',
            'book-meeting.store',
            'about',
            'contact',
            'contact.store',
            'privacy',
            'terms',
            'refund-policy',
            'faq',
            'maintenance',
            'customer.profile.preview',
            'customer.profile.update',
            'customer.profile.password.update',
            'customer.addresses.preview',
            'customer.orders.preview',
            'customer.addresses.store',
            'customer.addresses.update',
            'support-tickets.index',
            'support-tickets.store',
            'support-tickets.attachments.download',
            'support-tickets.show',
            'order.confirmation',
        ];

        $rolePermissions = [
            'guest' => $guestRolePermissions,
            'b2c_customer' => $signedInRolePermissions,
            'b2b_user' => $signedInRolePermissions,
            'internal_user' => $signedInRolePermissions,
            'internal_user_sales' => $signedInRolePermissions,
            'internal_user_finance' => $signedInRolePermissions,
            'internal_user_support' => $signedInRolePermissions,
            'internal_user_logistics' => $signedInRolePermissions,
            'admin' => $signedInRolePermissions,
            'delegated_admin' => $signedInRolePermissions,
        ];

        $permissionSlugs = [];

        foreach ($permissions as $permission) {
            $permissionSlugs[] = $permission['slug'];
        }

        DB::table('roles')->upsert($roles, ['slug'], ['name', 'updated_at']);

        $permissionIdsToRemove = DB::table('permissions')
            ->whereNotIn('slug', $permissionSlugs)
            ->pluck('id');

        if ($permissionIdsToRemove->isNotEmpty()) {
            DB::table('permission_role')->whereIn('permission_id', $permissionIdsToRemove)->delete();
            DB::table('user_permissions')->whereIn('permission_id', $permissionIdsToRemove)->delete();
            DB::table('permissions')->whereIn('id', $permissionIdsToRemove)->delete();
        }

        DB::table('permissions')->upsert($permissions, ['slug'], ['name', 'updated_at']);

        $roleIds = DB::table('roles')->pluck('id', 'slug');
        $permissionIds = DB::table('permissions')->pluck('id', 'slug');

        foreach ($rolePermissions as $roleSlug => $allowedPermissionSlugs) {
            $roleId = $roleIds[$roleSlug] ?? null;

            if (! $roleId) {
                continue;
            }

            DB::table('permission_role')->where('role_id', $roleId)->delete();

            foreach ($allowedPermissionSlugs as $permissionSlug) {
                $permissionId = $permissionIds[$permissionSlug] ?? null;

                if (! $permissionId) {
                    continue;
                }

                DB::table('permission_role')->updateOrInsert(
                    ['permission_id' => $permissionId, 'role_id' => $roleId],
                    ['updated_at' => $now, 'created_at' => $now],
                );
            }
        }
    }
}
