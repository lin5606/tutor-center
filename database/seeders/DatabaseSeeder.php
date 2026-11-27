<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Subject;
use App\Models\TutorProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seed baseline RBAC data, a small subject catalog, and local demo accounts.
 * Demo credentials are for local/testing environments only.
 */
class DatabaseSeeder extends Seeder
{
    /** TODO: Create roles, permissions, subjects, and safe local test accounts. */
    public function run(): void
    {
        $roles = collect([
            ['name' => 'Quản trị viên', 'slug' => 'admin'],
            ['name' => 'Gia sư', 'slug' => 'tutor'],
            ['name' => 'Phụ huynh', 'slug' => 'parent'],
        ])->mapWithKeys(fn (array $role) => [
            $role['slug'] => Role::updateOrCreate(['slug' => $role['slug']], $role),
        ]);

        $permissionRows = [
            ['name' => 'Xem bảng điều khiển', 'slug' => 'dashboard.view', 'module' => 'dashboard'],
            ['name' => 'Quản lý tài khoản', 'slug' => 'users.manage', 'module' => 'users'],
            ['name' => 'Quản lý vai trò và quyền', 'slug' => 'roles.manage', 'module' => 'roles'],
            ['name' => 'Duyệt hồ sơ gia sư', 'slug' => 'tutors.approve', 'module' => 'tutors'],
            ['name' => 'Duyệt và giao lớp', 'slug' => 'classes.manage', 'module' => 'classes'],
            ['name' => 'Xác nhận thanh toán', 'slug' => 'payments.manage', 'module' => 'payments'],
            ['name' => 'Xử lý hoàn phí', 'slug' => 'refunds.manage', 'module' => 'refunds'],
        ];

        $permissions = collect($permissionRows)->map(
            fn (array $permission) => Permission::updateOrCreate(
                ['slug' => $permission['slug']],
                $permission
            )
        );
        $roles['admin']->permissions()->sync($permissions->pluck('id')->all());

        foreach (['Toán', 'Ngữ văn', 'Tiếng Anh', 'Vật lý', 'Hóa học'] as $subject) {
            Subject::firstOrCreate(['name' => $subject]);
        }

        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        // TODO: Local/testing demo accounts share Password123!; never enable this block in production.
        $accounts = [
            ['full_name' => 'Administrator Demo', 'email' => 'admin@example.test', 'phone' => '0900000001', 'role' => 'admin'],
            ['full_name' => 'Tutor Demo', 'email' => 'tutor@example.test', 'phone' => '0900000002', 'role' => 'tutor'],
            ['full_name' => 'Parent Demo', 'email' => 'parent@example.test', 'phone' => '0900000003', 'role' => 'parent'],
        ];

        foreach ($accounts as $account) {
            $roleSlug = $account['role'];
            unset($account['role']);
            $user = User::updateOrCreate(
                ['email' => $account['email']],
                [...$account, 'password' => Hash::make('Password123!'), 'status' => true]
            );
            $user->roles()->syncWithoutDetaching([$roles[$roleSlug]->id]);

            if ($roleSlug === 'tutor') {
                TutorProfile::firstOrCreate(
                    ['user_id' => $user->id],
                    ['approval_status' => 'pending']
                );
            }
        }
    }
}
