<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-bold m-0">Create Workshop</h2>
    </x-slot>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .workshop-page {
            background: #f5f7fb;
            min-height: 100vh;
            padding: 40px 20px;
            color: #1e293b;
        }

        .workshop-card {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 22px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        .workshop-card-header {
            padding: 28px 32px;
            border-bottom: 1px solid #e2e8f0;
        }

        .workshop-card-body {
            padding: 32px;
        }

        .workshop-card .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #334155;
            margin-bottom: 8px;
        }

        .workshop-card .form-control,
        .workshop-card .form-select {
            padding: 11px 13px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #1e293b;
        }

        .workshop-card .form-control:focus,
        .workshop-card .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }

        .workshop-card-footer {
            padding: 22px 32px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
    </style>

    <div class="workshop-page">
        <div class="workshop-card">

            <div class="workshop-card-header">
                <h4 class="fw-bold mb-1">Workshop Details</h4>
                <p class="text-secondary mb-0">
                    Fill in the details to schedule a new workshop.
                </p>
            </div>

            <form action="{{ route('workshops.update', $workshop) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="workshop-card-body">

                    <div class="row g-4">

                        <div class="col-md-6">
                            <label class="form-label">Workshop Code *</label>
                            <input type="text" name="code" value="{{ old('code', $workshop->code) }}"
                                class="form-control @error('code') is-invalid @enderror" placeholder="e.g. WS001"
                                required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Instructor *</label>
                            <input type="text" name="instructor"
                                value="{{ old('instructor', $workshop->instructor) }}"
                                class="form-control @error('instructor') is-invalid @enderror"
                                placeholder="Instructor name" required>
                            @error('instructor')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Workshop Title *</label>
                            <input type="text" name="title" value="{{ old('title', $workshop->title) }}"
                                class="form-control @error('title') is-invalid @enderror"
                                placeholder="e.g. Introduction to Pottery" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Date & Time *</label>
                            <input type="datetime-local" name="starts_at"
                                value="{{ old('starts_at', $workshop->starts_at->format('Y-m-d\TH:i')) }}"
                                class="form-control @error('starts_at') is-invalid @enderror" required>
                            @error('starts_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Capacity *</label>
                            <input type="number" name="capacity" min="1"
                                value="{{ old('capacity', $workshop->capacity) }}"
                                class="form-control @error('capacity') is-invalid @enderror"
                                placeholder="Number of seats" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" value="{{ old('location', $workshop->location) }}"
                                class="form-control @error('location') is-invalid @enderror"
                                placeholder="e.g. Training Centre 01">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <select name="status" class="form-select">
                            <option value="scheduled" @selected(old('status', $workshop->status) === 'scheduled')>
                                Scheduled
                            </option>

                            <option value="cancelled" @selected(old('status', $workshop->status) === 'cancelled')>
                                Cancelled
                            </option>

                            <option value="completed" @selected(old('status', $workshop->status) === 'completed')>
                                Completed
                            </option>
                        </select>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                placeholder="Enter workshop description...">{{ old('description', $workshop->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                </div>

                <div class="workshop-card-footer">
                    <a href="{{ route('workshops.index') }}" class="btn btn-outline-secondary px-4">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary px-4">
                        Update Workshop
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-app-layout>
