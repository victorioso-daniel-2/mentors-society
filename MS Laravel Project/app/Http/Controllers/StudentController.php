<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\ClassModel;
use App\Models\StudentClass;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Student::with(['user', 'studentClasses']);

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('student_number', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Class filter
            if ($request->has('class_id')) {
                $query->whereHas('studentClasses', function ($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                });
            }

            // Year level filter
            if ($request->has('year_level')) {
                $query->whereHas('studentClasses', function ($q) use ($request) {
                    $q->where('year_level', $request->year_level);
                });
            }

            $students = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $students,
                'message' => 'Students retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve students',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created student
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'middle_initial' => 'nullable|string|max:5',
                'email' => 'required|email|unique:student,email',
                'password' => 'required|string|min:6',
                'student_number' => 'required|string|max:20|unique:student,student_number',
                'academic_status' => 'required|in:active,dropped,shifted,graduated',
                'course' => 'required|string|max:100',
                'year_level' => 'required|string|max:50',
                'section' => 'required|string|max:10',
                'classes' => 'nullable|array',
                'classes.*.class_id' => 'required|exists:class,class_id',
                'classes.*.academic_year_id' => 'required|exists:academic_year,academic_year_id',
                'classes.*.year_level' => 'required|string|in:First Year,Second Year,Third Year,Fourth Year,Other'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create student first
            $student = Student::create([
                'student_number' => $request->student_number,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'middle_initial' => $request->middle_initial,
                'email' => $request->email,
                'course' => $request->course,
                'year_level' => $request->year_level,
                'section' => $request->section,
                'academic_status' => $request->academic_status
            ]);

            // Create user
            $user = User::create([
                'student_number' => $request->student_number,
                'password' => Hash::make($request->password),
                'status' => 'active'
            ]);

            // Assign classes if provided
            if ($request->has('classes') && is_array($request->classes)) {
                foreach ($request->classes as $classData) {
                    StudentClass::create([
                        'student_number' => $student->student_number,
                        'class_id' => $classData['class_id'],
                        'academic_year_id' => $classData['academic_year_id'],
                        'year_level' => $classData['year_level']
                    ]);
                }
            }

            $student->load(['user', 'studentClasses']);

            return response()->json([
                'success' => true,
                'data' => $student,
                'message' => 'Student created successfully'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified student
     */
    public function show(string $student_number): JsonResponse
    {
        try {
            $student = Student::with(['user', 'studentClasses.class', 'studentClasses.academicYear'])->where('student_number', $student_number)->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $student,
                'message' => 'Student retrieved successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified student
     */
    public function update(Request $request, $student_number): JsonResponse
    {
        try {
            $student = Student::where('student_number', $student_number)->firstOrFail();
            $user = User::where('student_number', $student_number)->firstOrFail();

            $validator = Validator::make($request->all(), [
                'first_name' => 'sometimes|required|string|max:50',
                'last_name' => 'sometimes|required|string|max:50',
                'middle_initial' => 'nullable|string|max:5',
                'email' => 'sometimes|required|email|unique:student,email,' . $student_number . ',student_number',
                'academic_status' => 'sometimes|required|in:active,dropped,shifted,graduated',
                'password' => 'sometimes|nullable|string|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Update student information
            $studentData = $request->only(['first_name', 'last_name', 'middle_initial', 'email', 'academic_status']);
            $student->update($studentData);

            // Update user information
            $userData = $request->only(['password']);
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            $student->load(['user', 'studentClasses.class', 'studentClasses.academicYear']);

            return response()->json([
                'success' => true,
                'data' => $student,
                'message' => 'Student updated successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified student
     */
    public function destroy(string $student_number): JsonResponse
    {
        try {
            $student = Student::where('student_number', $student_number)->firstOrFail();

            // Delete student class assignments first
            $student->studentClasses()->delete();

            // Delete the associated user first (to avoid foreign key constraint)
            User::where('student_number', $student_number)->delete();

            // Delete student
            $student->delete();

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student's classes
     */
    public function getClasses(string $student_number): JsonResponse
    {
        try {
            $student = Student::with(['studentClasses.class', 'studentClasses.academicYear'])->where('student_number', $student_number)->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $student->studentClasses,
                'message' => 'Student classes retrieved successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student classes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign class to student
     */
    public function assignClass(Request $request, string $student_number): JsonResponse
    {
        try {
            $student = Student::where('student_number', $student_number)->firstOrFail();

            $validator = Validator::make($request->all(), [
                'class_id' => 'required|exists:class,class_id',
                'academic_year_id' => 'required|exists:academic_year,academic_year_id',
                'year_level' => 'required|string|in:First Year,Second Year,Third Year,Fourth Year,Other'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if student is already assigned to this class in this academic year
            $existingAssignment = StudentClass::where([
                'student_number' => $student->student_number,
                'class_id' => $request->class_id,
                'academic_year_id' => $request->academic_year_id
            ])->exists();

            if ($existingAssignment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is already assigned to this class for this academic year'
                ], 400);
            }

            $studentClass = StudentClass::create([
                'student_number' => $student->student_number,
                'class_id' => $request->class_id,
                'academic_year_id' => $request->academic_year_id,
                'year_level' => $request->year_level
            ]);

            $studentClass->load(['class', 'academicYear']);

            return response()->json([
                'success' => true,
                'data' => $studentClass,
                'message' => 'Class assigned successfully'
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove class assignment from student
     */
    public function removeClass(Request $request, string $student_number): JsonResponse
    {
        try {
            $student = Student::where('student_number', $student_number)->firstOrFail();

            $validator = Validator::make($request->all(), [
                'class_id' => 'required|exists:class,class_id',
                'academic_year_id' => 'required|exists:academic_year,academic_year_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $deleted = StudentClass::where([
                'student_number' => $student->student_number,
                'class_id' => $request->class_id,
                'academic_year_id' => $request->academic_year_id
            ])->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not assigned to this class'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Class assignment removed successfully'
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove class assignment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available classes
     */
    public function getAvailableClasses(): JsonResponse
    {
        try {
            $classes = ClassModel::with('academicYear')->orderBy('class_name')->get();

            return response()->json([
                'success' => true,
                'data' => $classes,
                'message' => 'Available classes retrieved successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve classes',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 