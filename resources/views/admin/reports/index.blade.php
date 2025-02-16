@extends('layouts.admin')

@section('content')

<link rel="stylesheet" href="{{ asset('css/reports.css') }}">

<div class="container">
    <h2>Schedule Reports</h2>

    <!-- Filters Section -->
    <div class="card bg-white bg-opacity-50 backdrop-blur-sm shadow-lg rounded-lg p-8 mb-6">
        <form method="GET" action="{{ route('admin.reports') }}" class="filter-box">
            <div class="grid">
                <!-- Date From & Date To -->
                <div class="filter-group">
                    <label class="text-gray-900 font-bold">Date From</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="filter-group">
                    <label class="text-gray-900 font-bold">Date To</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>

                <!-- File Name & Status -->
                <div class="filter-group">
                    <label class="text-gray-900 font-bold">File Name</label>
                    <select class="form-control" name="file_id">
                        <option value="">All Files</option>
                        @foreach($files as $file)
                            <option value="{{ $file->id }}" {{ request('file_id') == $file->id ? 'selected' : '' }}>
                                {{ $file->file_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <label class="text-gray-900 font-bold">Status</label>
                    <select class="form-control" name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>

            <!-- Buttons Section -->
            <div class="filter-buttons">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.reports') }}" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>

    <!-- Reports Table with Shadow & Borders -->
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th><a href="#" onclick="sortTable(0)">Student ⬍</a></th>
                    <th><a href="#" onclick="sortTable(1)">File Name ⬍</a></th>
                    <th><a href="#" onclick="sortTable(2)">Request Count ⬍</a></th>
                    <th><a href="#" onclick="sortTable(3)">Status ⬍</a></th>
                    <th><a href="#" onclick="sortTable(4)">Date Requested ⬍</a></th>
                </tr>
            </thead>
            <tbody>
                @foreach($reportData as $row)
                    <tr>
                        <td>
                            <a href="{{ route('admin.reports.student', $row->student_id) }}" class="student-link">
                                {{ $row->student_name }}
                            </a>
                        </td>
                        <td class="font-semibold">{{ $row->file_name }}</td>
                        <td class="text-center font-extrabold text-black">{{ $row->request_count }}</td>
                        <td class="capitalize font-semibold">{{ ucfirst($row->status) }}</td>
                        <td>{{ \Carbon\Carbon::parse($row->latest_request_date)->format('M d, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $reportData->links() }}
    </div>
</div>

<!-- Sort Table JavaScript -->
<script>
    function sortTable(columnIndex) {
        let table = document.querySelector("table");
        let rows = Array.from(table.rows).slice(1);
        let sortedRows = rows.sort((rowA, rowB) => {
            let cellA = rowA.cells[columnIndex].textContent.trim();
            let cellB = rowB.cells[columnIndex].textContent.trim();

            if (columnIndex === 2) {
                return parseInt(cellA) - parseInt(cellB);
            }
            if (columnIndex === 4) {
                return new Date(cellA) - new Date(cellB);
            }
            return cellA.localeCompare(cellB, undefined, { numeric: true });
        });

        let tbody = table.querySelector("tbody");
        tbody.innerHTML = "";
        sortedRows.forEach(row => tbody.appendChild(row));
    }
</script>

<style>
/* Filters Section */
.filter-box {
    width: 100%;
    padding: 1.5rem;
    border-radius: 8px;
}

/* Grid layout for filters */
.filter-box .grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

/* Filter Group Styling */
.filter-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

/* Full-width buttons */
.filter-buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-top: 1rem;
}

/* Form Control Styling */
.form-control {
    font-size: 1rem;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    transition: all 0.2s ease-in-out;
}

/* Focus Effect */
.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
    outline: none;
}

/* Button Styling */
.btn {
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    padding: 10px 16px;
    text-align: center;
    transition: all 0.2s ease-in-out;
}

.btn-primary:hover {
    background-color: #2563eb;
}

.btn-secondary:hover {
    background-color: #4b5563;
}
</style>
@endsection
