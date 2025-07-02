<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Student;
use App\Models\Event;
use App\Models\Task;
use App\Models\FinancialRecord;
use App\Models\EventRegistration;

class DashboardController extends Controller
{
    // Officer dashboard
    public function overview(Request $request)
    {
        $totalUsers = Student::count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'active_events' => 0,
                'pending_tasks' => 0,
                'total_budget' => 0,
                'recent_activities' => [],
                'upcoming_events' => []
            ]
        ]);
    }

    // Student dashboard
    public function studentOverview(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'events_registered' => 0,
                'events_attended' => 0,
                'recent_activities' => [],
                'upcoming_events' => []
            ]
        ]);
    }
} 