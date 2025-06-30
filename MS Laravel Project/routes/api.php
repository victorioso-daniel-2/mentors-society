<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| API Routes for Mentors Society
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Authentication routes (if needed)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// =============================================
// Authentication Routes
// =============================================
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);           // Login with student number and password
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']); // Forgot password
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);   // Reset password
});

// =============================================
// Protected Routes (require authentication)
// =============================================
Route::middleware('auth:sanctum')->group(function () {
    
    // =============================================
    // Protected Authentication Routes
    // =============================================
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);         // Logout
        Route::post('/refresh', [AuthController::class, 'refresh']);       // Refresh token
        Route::get('/me', [AuthController::class, 'me']);                  // Get current user info
        Route::post('/change-password', [AuthController::class, 'changePassword']); // Change password
    });
    
    // Get authenticated user info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // =============================================
    // Academic Year Management
    // =============================================
    // Route::prefix('academic-years')->group(function () {
    //     Route::get('/', 'AcademicYearController@index');
    //     Route::get('/{id}', 'AcademicYearController@show');
    //     Route::post('/', 'AcademicYearController@store');
    //     Route::put('/{id}', 'AcademicYearController@update');
    //     Route::delete('/{id}', 'AcademicYearController@destroy');
    //     Route::get('/current/active', 'AcademicYearController@getCurrentActive');
    // });

    // =============================================
    // User Management
    // =============================================
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{id}', [UserController::class, 'update']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
        Route::get('/{id}/roles', [UserController::class, 'getUserRoles']);
        Route::post('/{id}/roles', [UserController::class, 'assignRole']);
        Route::delete('/{id}/roles/{roleId}', [UserController::class, 'removeRole']);
        Route::get('/role/{roleName}', [UserController::class, 'getUsersByRole']);
    });

    // =============================================
    // User Search (separate route for search functionality)
    // =============================================
    Route::get('/users-search', [UserController::class, 'search']);

    // =============================================
    // Role and Permission Management
    // =============================================
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::get('/{id}', [RoleController::class, 'show']);
        Route::post('/', [RoleController::class, 'store']);
        Route::put('/{id}', [RoleController::class, 'update']);
        Route::delete('/{id}', [RoleController::class, 'destroy']);
        Route::get('/{id}/permissions', [RoleController::class, 'getPermissions']);
        Route::post('/{id}/permissions', [RoleController::class, 'assignPermissions']);
        Route::get('/permissions/all', [RoleController::class, 'getAllPermissions']);
    });

    Route::prefix('permissions')->group(function () {
        Route::get('/', 'PermissionController@index');
        Route::get('/{id}', 'PermissionController@show');
        Route::post('/', 'PermissionController@store');
        Route::put('/{id}', 'PermissionController@update');
        Route::delete('/{id}', 'PermissionController@destroy');
    });

    // =============================================
    // Student Management
    // =============================================
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('/{id}', [StudentController::class, 'show']);
        Route::post('/', [StudentController::class, 'store']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
        Route::get('/{id}/classes', [StudentController::class, 'getClasses']);
        Route::post('/{id}/classes', [StudentController::class, 'assignClass']);
        Route::delete('/{id}/classes', [StudentController::class, 'removeClass']);
        Route::get('/classes/available', [StudentController::class, 'getAvailableClasses']);
    });

    Route::prefix('classes')->group(function () {
        Route::get('/', 'ClassController@index');
        Route::get('/{id}', 'ClassController@show');
        Route::post('/', 'ClassController@store');
        Route::put('/{id}', 'ClassController@update');
        Route::delete('/{id}', 'ClassController@destroy');
        Route::get('/{id}/students', 'ClassController@getClassStudents');
    });

    // =============================================
    // Event Management
    // =============================================
    Route::prefix('events')->group(function () {
        Route::get('/', 'EventController@index');
        Route::get('/{id}', 'EventController@show');
        Route::post('/', 'EventController@store');
        Route::put('/{id}', 'EventController@update');
        Route::delete('/{id}', 'EventController@destroy');
        Route::get('/{id}/registrations', 'EventController@getEventRegistrations');
        Route::get('/{id}/participants', 'EventController@getEventParticipants');
        Route::get('/{id}/evaluations', 'EventController@getEventEvaluations');
        Route::get('/upcoming', 'EventController@getUpcomingEvents');
        Route::get('/past', 'EventController@getPastEvents');
        Route::get('/by-status/{statusId}', 'EventController@getEventsByStatus');
    });

    Route::prefix('event-registrations')->group(function () {
        Route::get('/', 'EventRegistrationController@index');
        Route::get('/{id}', 'EventRegistrationController@show');
        Route::post('/', 'EventRegistrationController@store');
        Route::put('/{id}', 'EventRegistrationController@update');
        Route::delete('/{id}', 'EventRegistrationController@destroy');
        Route::get('/student/{studentId}', 'EventRegistrationController@getStudentRegistrations');
        Route::get('/event/{eventId}', 'EventRegistrationController@getEventRegistrations');
    });

    Route::prefix('event-participations')->group(function () {
        Route::get('/', 'EventParticipationController@index');
        Route::get('/{id}', 'EventParticipationController@show');
        Route::post('/', 'EventParticipationController@store');
        Route::put('/{id}', 'EventParticipationController@update');
        Route::delete('/{id}', 'EventParticipationController@destroy');
        Route::post('/{id}/feedback', 'EventParticipationController@addFeedback');
        Route::get('/event/{eventId}/attendance', 'EventParticipationController@getEventAttendance');
    });

    Route::prefix('event-evaluations')->group(function () {
        Route::get('/', 'EventEvaluationController@index');
        Route::get('/{id}', 'EventEvaluationController@show');
        Route::post('/', 'EventEvaluationController@store');
        Route::put('/{id}', 'EventEvaluationController@update');
        Route::delete('/{id}', 'EventEvaluationController@destroy');
        Route::get('/participation/{participationId}', 'EventEvaluationController@getParticipationEvaluations');
    });

    Route::prefix('event-statuses')->group(function () {
        Route::get('/', 'EventStatusController@index');
        Route::get('/{id}', 'EventStatusController@show');
        Route::post('/', 'EventStatusController@store');
        Route::put('/{id}', 'EventStatusController@update');
        Route::delete('/{id}', 'EventStatusController@destroy');
    });

    // =============================================
    // Sponsor Management
    // =============================================
    Route::prefix('sponsors')->group(function () {
        Route::get('/', 'SponsorController@index');
        Route::get('/{id}', 'SponsorController@show');
        Route::post('/', 'SponsorController@store');
        Route::put('/{id}', 'SponsorController@update');
        Route::delete('/{id}', 'SponsorController@destroy');
        Route::get('/{id}/events', 'SponsorController@getSponsorEvents');
        Route::post('/{id}/events/{eventId}', 'SponsorController@assignToEvent');
        Route::delete('/{id}/events/{eventId}', 'SponsorController@removeFromEvent');
    });

    // =============================================
    // Financial Management
    // =============================================
    Route::prefix('transactions')->group(function () {
        Route::get('/', 'TransactionController@index');
        Route::get('/{id}', 'TransactionController@show');
        Route::post('/', 'TransactionController@store');
        Route::put('/{id}', 'TransactionController@update');
        Route::delete('/{id}', 'TransactionController@destroy');
        Route::get('/event/{eventId}', 'TransactionController@getEventTransactions');
        Route::get('/type/{typeId}', 'TransactionController@getTransactionsByType');
        Route::post('/{id}/verify', 'TransactionController@verifyTransaction');
    });

    Route::prefix('financial-records')->group(function () {
        Route::get('/', 'FinancialRecordController@index');
        Route::get('/{id}', 'FinancialRecordController@show');
        Route::post('/', 'FinancialRecordController@store');
        Route::put('/{id}', 'FinancialRecordController@update');
        Route::delete('/{id}', 'FinancialRecordController@destroy');
        Route::get('/event/{eventId}', 'FinancialRecordController@getEventRecords');
        Route::get('/summary', 'FinancialRecordController@getFinancialSummary');
    });

    Route::prefix('transaction-types')->group(function () {
        Route::get('/', 'TransactionTypeController@index');
        Route::get('/{id}', 'TransactionTypeController@show');
        Route::post('/', 'TransactionTypeController@store');
        Route::put('/{id}', 'TransactionTypeController@update');
        Route::delete('/{id}', 'TransactionTypeController@destroy');
    });

    // =============================================
    // Inventory Management
    // =============================================
    Route::prefix('inventory')->group(function () {
        Route::get('/', 'InventoryItemController@index');
        Route::get('/{id}', 'InventoryItemController@show');
        Route::post('/', 'InventoryItemController@store');
        Route::put('/{id}', 'InventoryItemController@update');
        Route::delete('/{id}', 'InventoryItemController@destroy');
        Route::get('/{id}/borrowings', 'InventoryItemController@getItemBorrowings');
        Route::get('/{id}/conditions', 'InventoryItemController@getItemConditions');
        Route::get('/available', 'InventoryItemController@getAvailableItems');
        Route::get('/borrowed', 'InventoryItemController@getBorrowedItems');
    });

    Route::prefix('item-borrowings')->group(function () {
        Route::get('/', 'ItemBorrowingController@index');
        Route::get('/{id}', 'ItemBorrowingController@show');
        Route::post('/', 'ItemBorrowingController@store');
        Route::put('/{id}', 'ItemBorrowingController@update');
        Route::delete('/{id}', 'ItemBorrowingController@destroy');
        Route::post('/{id}/return', 'ItemBorrowingController@returnItem');
        Route::get('/student/{studentId}', 'ItemBorrowingController@getStudentBorrowings');
        Route::get('/item/{itemId}', 'ItemBorrowingController@getItemBorrowings');
        Route::get('/overdue', 'ItemBorrowingController@getOverdueBorrowings');
    });

    Route::prefix('item-conditions')->group(function () {
        Route::get('/', 'ItemConditionController@index');
        Route::get('/{id}', 'ItemConditionController@show');
        Route::post('/', 'ItemConditionController@store');
        Route::put('/{id}', 'ItemConditionController@update');
        Route::delete('/{id}', 'ItemConditionController@destroy');
        Route::get('/item/{itemId}', 'ItemConditionController@getItemConditions');
    });

    // =============================================
    // Communication and Task Management
    // =============================================
    Route::prefix('social-media')->group(function () {
        Route::get('/', 'SocialMediaController@index');
        Route::get('/{id}', 'SocialMediaController@show');
        Route::post('/', 'SocialMediaController@store');
        Route::put('/{id}', 'SocialMediaController@update');
        Route::delete('/{id}', 'SocialMediaController@destroy');
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/', 'TaskController@index');
        Route::get('/{id}', 'TaskController@show');
        Route::post('/', 'TaskController@store');
        Route::put('/{id}', 'TaskController@update');
        Route::delete('/{id}', 'TaskController@destroy');
        Route::get('/assigned/{userId}', 'TaskController@getAssignedTasks');
        Route::get('/by-status/{status}', 'TaskController@getTasksByStatus');
        Route::post('/{id}/complete', 'TaskController@completeTask');
    });

    // =============================================
    // Reports and Analytics
    // =============================================
    Route::prefix('reports')->group(function () {
        Route::get('/events/summary', 'ReportController@getEventsSummary');
        Route::get('/financial/summary', 'ReportController@getFinancialSummary');
        Route::get('/inventory/summary', 'ReportController@getInventorySummary');
        Route::get('/student/participation', 'ReportController@getStudentParticipation');
        Route::get('/event/{eventId}/detailed', 'ReportController@getEventDetailedReport');
        Route::get('/export/events', 'ReportController@exportEvents');
        Route::get('/export/financial', 'ReportController@exportFinancial');
        Route::get('/export/inventory', 'ReportController@exportInventory');
    });

    // =============================================
    // Dashboard and Statistics
    // =============================================
    Route::prefix('dashboard')->group(function () {
        Route::get('/overview', 'DashboardController@getOverview');
        Route::get('/events/stats', 'DashboardController@getEventStats');
        Route::get('/financial/stats', 'DashboardController@getFinancialStats');
        Route::get('/inventory/stats', 'DashboardController@getInventoryStats');
        Route::get('/student/stats', 'DashboardController@getStudentStats');
        Route::get('/recent-activities', 'DashboardController@getRecentActivities');
    });

    Route::get('/sanctum-test', function () {
        return response()->json(['message' => 'Authenticated'], 200);
    });
});

Route::middleware('api')->group(function () {
    // =============================================
    // Authentication
    // =============================================
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

    // =============================================
    // Simple test route for authentication
    // =============================================
    Route::get('/test-auth', function () {
        return response()->json(['message' => 'Authenticated'], 200);
    })->middleware('auth:sanctum');

    // =============================================
    // Dashboard
    // =============================================
});
