<?php

namespace Database\Seeders;

use App\Models\Authorization\User;
use App\Services\Authorization\RolePermissionService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSetupSeeder extends Seeder
{
    /**
     * Seed initial admin and baseline departments.
     */
    public function run(): void
    {
        $now = now();

        DB::table('departments')->upsert([
            ['name' => 'Sales', 'slug' => 'sales', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Support', 'slug' => 'support', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Logistics', 'slug' => 'logistics', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Finance', 'slug' => 'finance', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ], ['slug'], ['name', 'is_active', 'updated_at']);

        $adminEmail = env('BIOGENIX_ADMIN_EMAIL', 'admin@biogenix.local');
        $adminName = env('BIOGENIX_ADMIN_NAME', 'System Admin');
        $adminPassword = env('BIOGENIX_ADMIN_PASSWORD', 'Admin@12345');

        $existingAdminId = DB::table('users')->where('email', $adminEmail)->value('id');

        if ($existingAdminId) {
            DB::table('users')
                ->where('id', $existingAdminId)
                ->update([
                    'name' => $adminName,
                    'user_type' => 'admin',
                    'status' => 'active',
                    'approved_at' => $now,
                    'updated_at' => $now,
                ]);

            $adminUserId = (int) $existingAdminId;
        } else {
            $adminUserId = DB::table('users')->insertGetId([
                'name' => $adminName,
                'email' => $adminEmail,
                'user_type' => 'admin',
                'b2b_type' => null,
                'company_id' => null,
                'status' => 'active',
                'approved_at' => $now,
                'approved_by_user_id' => null,
                'created_by_user_id' => null,
                'email_verified_at' => $now,
                'password' => Hash::make($adminPassword),
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $adminUser = User::query()->findOrFail($adminUserId);
        app(RolePermissionService::class)->assignRole($adminUser, 'admin');
    }
}
