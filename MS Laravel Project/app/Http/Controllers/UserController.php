<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

class UserController extends Controller
{
    /**
     * List users with optional filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with(['student', 'userRoles.role', 'userRoles.academicYear']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $role = $request->role;
            $query->whereHas('userRoles.role', function ($q) use ($role) {
                $q->where('role_name', $role);
            });
        }

        // Academic year filter
        if ($request->filled('academic_year_id')) {
            $year = $request->academic_year_id;
            $query->whereHas('userRoles', function ($q) use ($year) {
                $q->where('academic_year_id', $year);
            });
        }

        // Active status filter
        if ($request->boolean('active')) {
            $query->whereHas('userRoles', function ($q) {
                $q->where('start_date', '<=', now())
                  ->where(function ($sq) {
                      $sq->whereNull('end_date')->orWhere('end_date', '>=', now());
                  });
            });
        }

        // Sorting (by student fields if needed)
        $sortBy = $request->get('sort_by', 'last_name');
        $sortOrder = $request->get('sort_order', 'asc');
        if (in_array($sortBy, ['first_name', 'last_name', 'email'])) {
            $query->with(['student' => function ($q) use ($sortBy, $sortOrder) {
                $q->orderBy($sortBy, $sortOrder);
            }]);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);
        $transformedData = [];
        foreach ($users->items() as $user) {
            $transformedData[] = [
                'student_number' => $user->student_number,
                'first_name' => $user->student?->first_name,
                'last_name' => $user->student?->last_name,
                'middle_initial' => $user->student?->middle_initial,
                'email' => $user->student?->email,
                'full_name' => $user->student ? ($user->student->first_name . ' ' . $user->student->last_name) : null,
                'status' => $user->status,
                'roles' => $user->userRoles->map(function ($userRole) {
                    return [
                        'role_id' => $userRole->role->role_id,
                        'role_name' => $userRole->role->role_name,
                        'academic_year_id' => $userRole->academic_year_id,
                        'start_date' => $userRole->start_date,
                        'end_date' => $userRole->end_date,
                        'is_active' => $userRole->start_date <= now() &&
                                     ($userRole->end_date === null || $userRole->end_date >= now())
                    ];
                }),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at
            ];
        }
        $responseData = [
            'current_page' => $users->currentPage(),
            'data' => $transformedData,
            'first_page_url' => $users->url(1),
            'from' => $users->firstItem(),
            'last_page' => $users->lastPage(),
            'last_page_url' => $users->url($users->lastPage()),
            'next_page_url' => $users->nextPageUrl(),
            'path' => $users->path(),
            'per_page' => $users->perPage(),
            'prev_page_url' => $users->previousPageUrl(),
            'to' => $users->lastItem(),
            'total' => $users->total()
        ];
        return response()->json([
            'success' => true,
            'data' => $responseData,
            'message' => 'Users retrieved successfully'
        ]);
    }

    /**
     * Get a specific user's details
     */
    public function show($student_number): JsonResponse
    {
        $user = User::with([
            'student',
            'userRoles.role',
            'userRoles.academicYear',
            'userRoles.role.permissions'
        ])->where('student_number', $student_number)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $userData = [
            'student_number' => $user->student_number,
            'first_name' => $user->student?->first_name,
            'last_name' => $user->student?->last_name,
            'middle_initial' => $user->student?->middle_initial,
            'email' => $user->student?->email,
            'full_name' => $user->student ? ($user->student->first_name . ' ' . $user->student->last_name) : null,
            'status' => $user->status,
            'roles' => $user->userRoles->map(function ($userRole) {
                return [
                    'role_id' => $userRole->role->role_id,
                    'role_name' => $userRole->role->role_name,
                    'description' => $userRole->role->description,
                    'academic_year_id' => $userRole->academic_year_id,
                    'start_date' => $userRole->start_date,
                    'end_date' => $userRole->end_date,
                    'is_active' => $userRole->start_date <= now() &&
                                 ($userRole->end_date === null || $userRole->end_date >= now()),
                    'permissions' => $userRole->role->permissions->map(function ($permission) {
                        return [
                            'permission_id' => $permission->permission_id,
                            'permission_name' => $permission->permission_name,
                            'description' => $permission->description
                        ];
                    })
                ];
            }),
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ];

        return response()->json([
            'success' => true,
            'data' => $userData,
            'message' => 'User retrieved successfully'
        ]);
    }

    /**
     * Update user info
     */
    public function update(Request $request, $student_number): JsonResponse
    {
        $user = User::where('student_number', $student_number)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'middle_initial' => 'sometimes|nullable|string|max:10',
            'email' => 'sometimes|required|email|max:255',
            'password' => 'sometimes|nullable|string|min:6',
            'status' => 'sometimes|required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Update student information
        $student = Student::where('student_number', $student_number)->first();
        if ($student) {
            $studentData = $request->only(['first_name', 'last_name', 'middle_initial', 'email']);
            $student->update($studentData);
        }

        // Update user information
        $userData = $request->only(['status']);
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        $user->update($userData);

        // Return updated user with student info
        $user->load('student');
        $responseData = [
            'student_number' => $user->student_number,
            'first_name' => $user->student?->first_name,
            'last_name' => $user->student?->last_name,
            'middle_initial' => $user->student?->middle_initial,
            'email' => $user->student?->email,
            'full_name' => $user->student ? ($user->student->first_name . ' ' . $user->student->last_name) : null,
            'status' => $user->status,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at
        ];

        return response()->json([
            'success' => true,
            'data' => $responseData,
            'message' => 'User updated successfully'
        ]);
    }

    /**
     * List all roles for a user
     */
    public function getUserRoles($student_number): JsonResponse
    {
        $user = User::with(['userRoles.role', 'userRoles.academicYear'])->where('student_number', $student_number)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        $roles = $user->userRoles->map(function ($userRole) {
            return [
                'user_role_id' => $userRole->user_role_id,
                'role_id' => $userRole->role->role_id,
                'role_name' => $userRole->role->role_name,
                'description' => $userRole->role->description,
                'academic_year_id' => $userRole->academic_year_id,
                'start_date' => $userRole->start_date,
                'end_date' => $userRole->end_date,
                'is_active' => $userRole->start_date <= now() &&
                             ($userRole->end_date === null || $userRole->end_date >= now())
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $roles,
            'message' => 'User roles retrieved successfully'
        ]);
    }

    /**
     * Assign a role to a user for a specific academic year
     */
    public function assignRole(Request $request, $student_number): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|exists:role,role_id',
            'academic_year_id' => 'required|exists:academic_year,academic_year_id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }
        $existingRole = UserRole::where('student_number', $student_number)
            ->where('role_id', $request->role_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->first();
        if ($existingRole) {
            return response()->json([
                'success' => false,
                'message' => 'User already has this role for the specified academic year'
            ], 422);
        }
        $userRole = UserRole::create([
            'student_number' => $student_number,
            'role_id' => $request->role_id,
            'academic_year_id' => $request->academic_year_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date
        ]);
        $userRole->load(['role', 'academicYear']);
        return response()->json([
            'success' => true,
            'data' => [
                'user_role_id' => $userRole->user_role_id,
                'student_number' => $userRole->student_number,
                'role_id' => $userRole->role->role_id,
                'role_name' => $userRole->role->role_name,
                'academic_year_id' => $userRole->academic_year_id,
                'start_date' => $userRole->start_date,
                'end_date' => $userRole->end_date
            ],
            'message' => 'Role assigned successfully'
        ], 201);
    }

    /**
     * Remove a role from a user
     */
    public function removeRole($student_number, $userRoleId): JsonResponse
    {
        $userRole = UserRole::where('student_number', $student_number)
            ->where('user_role_id', $userRoleId)
            ->first();
        if (!$userRole) {
            return response()->json([
                'success' => false,
                'message' => 'User role not found'
            ], 404);
        }
        $userRole->delete();
        return response()->json([
            'success' => true,
            'message' => 'Role removed successfully'
        ]);
    }

    /**
     * List users by role
     */
    public function getUsersByRole($roleName): JsonResponse
    {
        $users = User::with(['student', 'userRoles.role', 'userRoles.academicYear'])
            ->whereHas('userRoles.role', function ($q) use ($roleName) {
                $q->where('role_name', $roleName);
            })
            ->get();
        $usersData = $users->map(function ($user) {
            return [
                'student_number' => $user->student_number,
                'first_name' => $user->student?->first_name,
                'last_name' => $user->student?->last_name,
                'middle_initial' => $user->student?->middle_initial,
                'email' => $user->student?->email,
                'full_name' => $user->student ? ($user->student->first_name . ' ' . $user->student->last_name) : null,
                'status' => $user->status,
                'roles' => $user->userRoles->map(function ($userRole) {
                    return [
                        'role_id' => $userRole->role->role_id,
                        'role_name' => $userRole->role->role_name,
                        'academic_year_id' => $userRole->academic_year_id,
                        'start_date' => $userRole->start_date,
                        'end_date' => $userRole->end_date,
                        'is_active' => $userRole->start_date <= now() &&
                                     ($userRole->end_date === null || $userRole->end_date >= now())
                    ];
                })
            ];
        });
        return response()->json([
            'success' => true,
            'data' => $usersData,
            'message' => "Users with role '{$roleName}' retrieved successfully"
        ]);
    }

    /**
     * Search users by name/email/student number
     */
    public function search(Request $request): JsonResponse
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:2',
            'limit' => 'sometimes|integer|min:1|max:50'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $query = $request->get('q');
        $limit = $request->get('limit', 10);
        
        // Search students first, then get associated users
        $students = Student::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('student_number', 'like', "%{$query}%")
            ->limit($limit)
            ->get();
            
        $usersData = [];
        foreach ($students as $student) {
            // Get the associated user for this student
            $user = User::with(['userRoles.role'])->where('student_number', $student->student_number)->first();
            
            if ($user) {
                $usersData[] = [
                    'student_number' => $user->student_number,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'middle_initial' => $student->middle_initial,
                    'email' => $student->email,
                    'full_name' => $student->first_name . ' ' . $student->last_name,
                    'status' => $user->status,
                    'roles' => $user->userRoles->map(function ($userRole) {
                        return [
                            'role_id' => $userRole->role->role_id,
                            'role_name' => $userRole->role->role_name,
                            'academic_year_id' => $userRole->academic_year_id,
                            'start_date' => $userRole->start_date,
                            'end_date' => $userRole->end_date,
                            'is_active' => $userRole->start_date <= now() &&
                                         ($userRole->end_date === null || $userRole->end_date >= now())
                        ];
                    })
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $usersData,
            'message' => 'Search completed successfully'
        ]);
    }

    /**
     * Create a new user
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_number' => 'required|string|max:20|exists:student,student_number|unique:user,student_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_initial' => 'sometimes|nullable|string|max:10',
            'password' => 'required|string|min:6',
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 400);
        }

        $userData = $request->only(['student_number', 'first_name', 'last_name', 'middle_initial', 'password', 'status']);
        $userData['password'] = Hash::make($userData['password']);
        $user = User::create($userData);

        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User created successfully'
        ], 201);
    }

    /**
     * Delete a user
     */
    public function destroy($id): JsonResponse
    {
        $user = User::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
} 