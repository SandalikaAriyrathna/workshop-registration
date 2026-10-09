<x-app-layout>
    <x-slot name="header">
        <h2 style="color:#1e293b; font-size:22px; font-weight:700; margin:0;">
            Workshop Management
        </h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .workshops-page {
            background: #f5f7fb;
            min-height: 100vh;
            padding: 40px 24px;
            color: #1e293b;
        }

        .workshops-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .workshops-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .04);
            overflow: hidden;
        }

        .workshops-card-header {
            padding: 24px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            border-bottom: 1px solid #e2e8f0;
        }

        .workshops-card-header h4 {
            margin: 0;
            color: #0f172a;
            font-size: 20px;
            font-weight: 700;
        }

        .workshops-card-body {
            padding: 8px 24px 24px;
        }

        .workshops-table {
            width: 100%;
            border-collapse: collapse;
            color: #334155;
        }

        .workshops-table th {
            padding: 16px;
            text-align: left;
            background: #f8fafc;
            color: #64748b;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .workshops-table td {
            padding: 17px 16px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            vertical-align: middle;
        }

        .workshops-table tbody tr:hover {
            background: #f8fafc;
        }

        .workshops-table tbody tr:last-child td {
            border-bottom: none;
        }

        .workshops-action-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .workshops-action-group .btn {
            white-space: nowrap;
        }

        .workshop-code {
            font-weight: 700;
            color: #2563eb;
        }

        .workshops-empty {
            padding: 48px 16px !important;
            text-align: center;
            color: #64748b;
        }

        @media (max-width: 640px) {
            .workshops-page {
                padding: 20px 12px;
            }

            .workshops-card-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .workshops-card-body {
                padding: 8px 12px 16px;
            }
        }
    </style>

    <div class="workshops-page">
        <div class="workshops-container">

            <div class="workshops-card">

                <!-- Header -->
                <div class="workshops-card-header">
                    <div>
                        <h4>All Workshops</h4>
                        <p class="text-secondary mb-0 mt-1">
                            View and manage scheduled training workshops.
                        </p>
                    </div>

                    @if (auth()->user()->role === 'manager')
                        <a href="{{ route('workshops.create') }}" class="btn btn-primary px-4 py-2">
                            + Add Workshop
                        </a>
                    @endif
                </div>


                <!-- Workshop Filters -->
                <div style="padding:20px 28px; border-bottom:1px solid #e2e8f0; ">

                    <form action="{{ route('workshops.index') }}" method="GET" class="row g-3 align-items-end">

                        <!-- From Date -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                From Date
                            </label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}"
                                class="form-control">
                        </div>

                        <!-- To Date -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                To Date
                            </label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                        </div>

                        <!-- Status -->
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Status
                            </label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>

                                <option value="scheduled" @selected(request('status') === 'scheduled')>
                                    Scheduled
                                </option>

                                <option value="completed" @selected(request('status') === 'completed')>
                                    Completed
                                </option>

                                <option value="cancelled" @selected(request('status') === 'cancelled')>
                                    Cancelled
                                </option>
                            </select>
                        </div>

                        <!-- Available Seats -->
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" name="available_only" value="1" id="available_only"
                                    class="form-check-input" @checked(request('available_only') == '1')>

                                <label for="available_only" class="form-check-label fw-semibold">
                                    Available Seats Only
                                </label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="col-12 d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                Apply Filters
                            </button>

                            <a href="{{ route('workshops.index') }}" class="btn btn-outline-secondary px-4">
                                Reset
                            </a>
                        </div>

                    </form>
                </div>


                <!-- Table -->
                <div class="workshops-card-body">
                    <div class="table-responsive">

                        <table class="workshops-table">

                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Workshop</th>
                                    <th>Instructor</th>
                                    <th>Date & Time</th>
                                    <th>Seats</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($workshops as $workshop)
                                    <tr>
                                        <td>
                                            <span class="workshop-code">
                                                {{ $workshop->code }}
                                            </span>
                                        </td>

                                        <td class="fw-semibold">
                                            {{ $workshop->title }}
                                        </td>

                                        <td>
                                            {{ $workshop->instructor }}
                                        </td>

                                        <td>
                                            {{ $workshop->starts_at->format('d M Y') }}
                                            <div class="text-secondary small">
                                                {{ $workshop->starts_at->format('h:i A') }}
                                            </div>
                                        </td>

                                        <td>
                                            @php
                                                $available = max(
                                                    0,
                                                    $workshop->capacity - $workshop->active_registrations_count,
                                                );
                                            @endphp

                                            <span style="font-weight:600;">
                                                {{ $available }} / {{ $workshop->capacity }}
                                            </span>

                                            <div style="font-size:12px; color:#64748b;">
                                                Available / Total
                                            </div>
                                        </td>

                                        <td>
                                            @if ($workshop->status === 'scheduled')
                                                <span class="badge bg-primary-subtle text-primary-emphasis px-3 py-2">
                                                    Scheduled
                                                </span>
                                            @elseif($workshop->status === 'completed')
                                                <span class="badge bg-success-subtle text-success-emphasis px-3 py-2">
                                                    Completed
                                                </span>
                                            @else
                                                <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-2">
                                                    Cancelled
                                                </span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="workshops-action-group">
                                                <a href="{{ route('workshops.show', $workshop) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    View
                                                </a>

                                                @if (auth()->user()->role === 'manager')
                                                    <a href="{{ route('workshops.edit', $workshop) }}"
                                                        class="btn btn-sm btn-outline-secondary">
                                                        Edit
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="workshops-empty">
                                            No workshops found.
                                            @if (auth()->user()->role === 'manager')
                                                <div class="mt-2">
                                                    Create your first workshop to get started.
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
