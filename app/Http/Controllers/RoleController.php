<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:roles.view')->only(['index', 'show']);
        $this->middleware('permission:roles.create')->only(['store']);
        $this->middleware('permission:roles.update')->only(['update']);
        $this->middleware('permission:roles.delete')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::with('permissions')->get();

        return response()->json([
            'data' => $roles
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->givePermissionTo($validated['permissions']);
        }

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role->load('permissions')
        ], 201);
    }

    public function show(Role $role)
    {
        return response()->json([
            'data' => $role->load('permissions')
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role->load('permissions')
        ]);
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete role that is assigned to users'
            ], 422);
        }

        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }

    public function assignPermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $role->givePermissionTo($validated['permission']);

        return response()->json([
            'message' => 'Permission assigned successfully',
            'data' => $role->load('permissions')
        ]);
    }

    public function removePermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        $role->revokePermissionTo($validated['permission']);

        return response()->json([
            'message' => 'Permission removed successfully',
            'data' => $role->load('permissions')
        ]);
    }
}