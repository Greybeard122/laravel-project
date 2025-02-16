<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Student;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
{
    $pendingRequests = Schedule::where('status', 'pending')->count();
    $todayAppointments = Schedule::whereDate('preferred_date', now())->count();

    // Fetch schedules for FullCalendar
    $schedules = Schedule::where('status', 'approved')->get(['id', 'preferred_date', 'preferred_time']);


    // Format data for FullCalendar
    $events = $schedules->map(function ($schedule) {
        return [
            'id' => $schedule->id,
            'title' => 'Student Appointment', 
            'start' => $schedule->preferred_date . 'T' . $schedule->time_slot, // Format YYYY-MM-DDTHH:MM
            'color' => '#4A90E2' // Custom color for approved events
        ];
    });

    return view('admin.dashboard', compact('pendingRequests', 'todayAppointments', 'events'));
}
}