<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Role::with(['permissions']);

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('role_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Priority filter
            if ($request->has('priority')) {
                $query->where('role_priority', $request->priority);
            }

            // Sort by priority (ascending)
            $query->orderBy('role_priority', 'asc');

            $roles = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $roles,
                'message' => 'Roles retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'role_name' => 'required|string|max:50|unique:role,role_name',
                'description' => 'nullable|string|max:255',
                'role_priority' => 'required|integer|min:1|max:99',
                'permissions' => 'nullable|array',
                'permissions.*' => 'exists:permission,permission_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role = Role::create([
                'role_name' => $request->role_name,
                'description' => $request->description,
                'role_priority' => $request->role_priority
            ]);

            // Assign permissions if provided
            if ($request->has('permissions') && is_array($request->permissions)) {
                $role->permissions()->attach($request->permissions);
            }

            $role->load('permissions');

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Role created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified role
     */
    public function show(int $id): JsonResponse
    {
        try {
            $role = Role::with(['permissions'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Role retrieved successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'role_name' => 'sometimes|required|string|max:50|unique:role,role_name,' . $id . ',role_id',
                'description' => 'nullable|string|max:255',
                'role_priority' => 'sometimes|required|integer|min:1|max:99'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $role->update($request->only(['role_name', 'description', 'role_priority']));

            $role->load('permissions');

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Role updated successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified role
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);

            // Check if role is assigned to any users
            if ($role->userRoles()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role that is assigned to users'
                ], 400);
            }

            // Remove all permissions from role
            $role->permissions()->detach();

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign permissions to a role
     */
    public function assignPermissions(Request $request, int $id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permission,permission_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Sync permissions (this will replace existing permissions)
            $role->permissions()->sync($request->permissions);

            $role->load('permissions');

            return response()->json([
                'success' => true,
                'data' => $role,
                'message' => 'Permissions assigned successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all permissions for a role
     */
    public function getPermissions(int $id): JsonResponse
    {
        try {
            $role = Role::with(['permissions'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $role->permissions,
                'message' => 'Role permissions retrieved successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve role permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available permissions
     */
    public function getAllPermissions(): JsonResponse
    {
        try {
            $permissions = Permission::orderBy('permission_name')->get();

            return response()->json([
                'success' => true,
                'data' => $permissions,
                'message' => 'Permissions retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve permissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 