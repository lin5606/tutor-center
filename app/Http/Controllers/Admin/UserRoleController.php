<?php

/**
 * TODO: Project scaffold for tutor-center; implementation guidance is recorded in inline TODO notes.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Administrator account controls and CRUD for dynamic roles and permission grants. */
class UserRoleController extends Controller
{
    /** TODO: Paginate users and load the role/permission catalogs for management screens. */
    public function index(): View
    {
        return view('admin.users_roles', [
            'users' => User::with('roles')->latest()->paginate(20),
            'roles' => Role::with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::orderBy('module')->orderBy('name')->get(),
        ]);
    }

    /** TODO: Toggle an account's active status without allowing administrators to lock themselves out. */
    public function toggleUser(Request $request, User $user): RedirectResponse
    {
        abort_if($request->user()->is($user), 422, 'Không thể tự khóa tài khoản đang đăng nhập.');
        $user->update(['status' => ! $user->status]);

        return back()->with('success', 'Đã cập nhật trạng thái tài khoản.');
    }

    /** TODO: Replace the selected account's role assignments after validating every role ID. */
    public function assignRoles(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'distinct', 'exists:roles,id'],
        ]);
        $user->roles()->sync($data['roles']);

        return back()->with('success', 'Đã cập nhật vai trò tài khoản.');
    }

    /** TODO: Validate and create a role slug for the RBAC catalog. */
    public function storeRole(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['required', 'alpha_dash', 'max:50', 'unique:roles,slug'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);
        Role::create($data);

        return back()->with('success', 'Đã tạo vai trò.');
    }

    /** TODO: Update a role's display data and replace its permission grants. */
    public function updateRole(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'slug' => ['required', 'alpha_dash', 'max:50', 'unique:roles,slug,'.$role->id],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);
        $role->update($data);
        $role->permissions()->sync($permissions);

        return back()->with('success', 'Đã cập nhật vai trò và quyền.');
    }

    /** TODO: Delete a role only when policy permits and dependent assignments are handled. */
    public function destroyRole(Role $role): RedirectResponse
    {
        abort_if(in_array($role->slug, ['admin', 'tutor', 'parent'], true), 422, 'Không thể xóa vai trò mặc định.');
        $role->delete();

        return back()->with('success', 'Đã xóa vai trò.');
    }
}
