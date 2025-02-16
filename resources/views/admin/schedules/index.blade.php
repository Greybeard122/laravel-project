@extends('layouts.default')
<link href="{{ asset('css/students2.css') }}" rel="stylesheet">
@section('content')
<div class="container mx-auto px-4">
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <!-- Pending Requests -->
        <a href="{{ route('admin.schedules.pending') }}" class="block bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-lg font-semibold text-gray-600">Pending Requests</h5>
                    <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $pendingRequests }}</h2>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Today's Appointments -->
        <a href="{{ route('admin.schedules.today') }}" class="block bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-lg font-semibold text-gray-600">Today's Appointments</h5>
                    <h2 class="text-3xl font-bold text-green-600 mt-2">{{ $todayAppointments }}</h2>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Weekly Schedule -->
        <a href="{{ route('admin.schedules.weekly') }}" class="block bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <h5 class="text-lg font-semibold text-gray-600">Weekly Schedule</h5>
                    <h2 class="text-3xl font-bold text-blue-600 mt-2">{{ $weeklyAppointments }}</h2>

                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4 bg-white bg-opacity-30 backdrop-blur-sm shadow-lg rounded-lg p-6">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-gray-700 font-semibold">Date</label>
                <input type="date" class="form-control w-full" name="date" value="{{ request('date') }}">
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">File Type</label>
                <select class="form-control w-full" name="file_id">
                    <option value="">All Files</option>
                    @foreach($files as $file)
                        <option value="{{ $file->id }}" {{ request('file_id') == $file->id ? 'selected' : '' }}>
                            {{ $file->file_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-semibold">Status</label>
                <select class="form-control w-full" name="status">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="btn btn-primary w-full">Filter</button>
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-secondary w-full">Clear</a>
            </div>
        </form>
    </div>

    <!-- Schedules Table -->
    <div class="card bg-white bg-opacity-30 backdrop-blur-sm shadow-lg rounded-lg p-6">
        <div class="table-responsive">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th><a href="#">ID</a></th>
                        <th><a href="#">Student</a></th>
                        <th><a href="#">File</a></th>
                        <th><a href="#">Date</a></th>
                        <th><a href="#">Time</a></th>
                        <th><a href="#">Status</a></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        <tr>
                            <td>{{ $schedule->id }}</td>
                            <td>{{ $schedule->student->first_name }} {{ $schedule->student->last_name }}</td>
                            <td>{{ optional($schedule->file)->file_name ?? 'No File' }}</td>
                            <td>{{ $schedule->preferred_date }}</td>
                            <td>{{ ucfirst($schedule->preferred_time) }}</td>
                            <td class="capitalize">{{ $schedule->status }}</td>
                            <td>
                                <form action="{{ route('schedules.approve', $schedule->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('schedules.reject', $schedule->id) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $schedules->links() }}
    </div>
</div>
@endsection
