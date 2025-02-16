<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\File;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $files = File::all();

        $reportData = Schedule::select(
                'students.id as student_id',
                'students.last_name',
                'students.first_name',
                'files.file_name',
                'schedules.status',
                DB::raw('COUNT(schedules.id) as request_count'),
                DB::raw('MAX(schedules.created_at) as latest_request_date')
            )
            ->join('students', 'schedules.student_id', '=', 'students.id')
            ->join('files', 'schedules.file_id', '=', 'files.id')
            ->when($request->date_from, function ($query) use ($request) {
                return $query->whereDate('schedules.created_at', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                return $query->whereDate('schedules.created_at', '<=', $request->date_to);
            })
            ->when($request->file_id, function ($query) use ($request) {
                return $query->where('schedules.file_id', $request->file_id);
            })
            ->when($request->status, function ($query) use ($request) {
                return $query->where('schedules.status', $request->status);
            })
            ->groupBy('students.id', 'students.last_name', 'students.first_name', 'files.file_name', 'schedules.status')
            ->orderBy('latest_request_date', 'desc')
            ->paginate(10);

        // Transform the data to include the concatenated name
        $reportData->getCollection()->transform(function ($item) {
            $item->student_name = $item->last_name . ', ' . $item->first_name;
            unset($item->last_name, $item->first_name);
            return $item;
        });

        return view('admin.reports.index', compact('reportData', 'files'));
    }

    public function studentReport($studentId)
    {
        $student = Student::findOrFail($studentId);

        $studentSchedules = Schedule::where('student_id', $studentId)
            ->join('files', 'schedules.file_id', '=', 'files.id')
            ->select('schedules.*', 'files.file_name')
            ->orderBy('schedules.created_at', 'desc')
            ->paginate(10);

        return view('admin.reports.student', compact('student', 'studentSchedules'));
    }
}