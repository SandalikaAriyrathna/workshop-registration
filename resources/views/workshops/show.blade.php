<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size:22px; font-weight:700; color:#1e293b;">
            Workshop Details
        </h2>
    </x-slot>

    @if (session('error'))
        <div
            style="background:#fee2e2; color:#991b1b;
                padding:12px; border-radius:8px; margin-bottom:20px;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div
            style="background:#dcfce7; color:#166534;
                padding:12px; border-radius:8px; margin-bottom:20px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background:#f5f7fb; min-height:100vh; padding:40px 20px;">

        <div style="max-width:900px; margin:auto;">


            <!-- Registration History -->
            <div
                style="background:white; border-radius:14px;
            padding:28px; margin-top:24px;
            box-shadow:0 4px 20px rgba(0,0,0,.06);
            color:#1e293b;">

                <h3 style="font-size:20px; font-weight:700; margin-bottom:8px;">
                    Registration History
                </h3>

                <p style="color:#64748b; margin-bottom:20px;">
                    Active: {{ $activeCount }} /
                    {{ $workshop->capacity }} |
                    Available Seats: {{ $availableSeats }}
                </p>

                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; text-align:left;">

                        <thead>
                            <tr style="background:#f1f5f9;">
                                <th style="padding:12px;">Attendee</th>
                                <th style="padding:12px;">Status</th>
                                <th style="padding:12px;">Registered By</th>
                                <th style="padding:12px;">Registered At</th>
                                <th style="padding:12px;">Cancellation</th>
                                <th style="padding:12px;">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($registrations as $registration)
                                <tr style="border-bottom:1px solid #e2e8f0;">

                                    <td style="padding:12px;">
                                        <strong>{{ $registration->attendee_name }}</strong>

                                        <div style="font-size:12px; color:#64748b;">
                                            {{ $registration->attendee_email }}
                                        </div>
                                    </td>

                                    <td style="padding:12px;">
                                        @if ($registration->status === 'active')
                                            <span style="color:#15803d; font-weight:600;">
                                                Active
                                            </span>
                                        @else
                                            <span style="color:#dc2626; font-weight:600;">
                                                Cancelled
                                            </span>
                                        @endif
                                    </td>

                                    <td style="padding:12px;">
                                        {{ $registration->registeredBy?->name ?? 'Unknown' }}
                                    </td>

                                    <td style="padding:12px; white-space:nowrap;">
                                        {{ $registration->registered_at?->format('d M Y, h:i A') }}
                                    </td>

                                    <td style="padding:12px;">
                                        @if ($registration->status === 'cancelled')
                                            <div>
                                                By: {{ $registration->cancelledBy?->name ?? 'Unknown' }}
                                            </div>

                                            <div style="font-size:12px; color:#64748b;">
                                                {{ $registration->cancelled_at?->format('d M Y, h:i A') }}
                                            </div>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td style="padding:12px;">
                                        @if ($registration->status === 'active')
                                            <form action="{{ route('registrations.cancel', $registration) }}"
                                                method="POST" onsubmit="return confirm('Cancel this registration?')">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    style="background:#dc2626;
                                                   color:white;
                                                   padding:8px 12px;
                                                   border:none;
                                                   border-radius:6px;
                                                   cursor:pointer;
                                                   white-space:nowrap;">
                                                    Cancel
                                                </button>
                                            </form>
                                        @else
                                            <span style="color:#64748b;">—</span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="padding:24px; text-align:center; color:#64748b;">
                                        No registrations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>


            <div
                style="background:white; border-radius:14px;
                        padding:32px; box-shadow:0 4px 20px rgba(0,0,0,.06);
                        color:#1e293b;">

                <!-- Heading -->
                <div
                    style="display:flex; justify-content:space-between;
                            align-items:center; gap:16px; flex-wrap:wrap;
                            border-bottom:1px solid #e2e8f0; padding-bottom:20px;">

                    <div>
                        <h2 style="font-size:24px; font-weight:700; margin:0;">
                            {{ $workshop->title }}
                        </h2>

                        <p style="color:#64748b; margin-top:6px;">
                            Workshop Code: {{ $workshop->code }}
                        </p>
                    </div>

                    <span
                        style="background:#dbeafe; color:#1d4ed8;
                                 padding:8px 14px; border-radius:20px;
                                 font-size:13px; font-weight:600;">
                        {{ ucfirst($workshop->status) }}
                    </span>
                </div>

                <!-- Workshop Information -->
                <div
                    style="display:grid;
                            grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
                            gap:24px; margin-top:28px;">

                    <div>
                        <p style="color:#64748b; font-size:13px;">Instructor</p>
                        <p style="font-weight:600;">{{ $workshop->instructor }}</p>
                    </div>

                    <div>
                        <p style="color:#64748b; font-size:13px;">Date & Time</p>
                        <p style="font-weight:600;">
                            {{ $workshop->starts_at->format('d M Y, h:i A') }}
                        </p>
                    </div>

                    <div>
                        <p style="color:#64748b; font-size:13px;">Capacity</p>
                        <p style="font-weight:600;">
                            {{ $workshop->capacity }} seats
                        </p>
                    </div>

                    <div>
                        <p style="color:#64748b; font-size:13px;">Location</p>
                        <p style="font-weight:600;">
                            {{ $workshop->location ?? 'Not specified' }}
                        </p>
                    </div>

                </div>

                <!-- Description -->
                <div
                    style="margin-top:30px; padding-top:24px;
                            border-top:1px solid #e2e8f0;">

                    <h3 style="font-size:17px; font-weight:700; margin-bottom:12px;">
                        Description
                    </h3>

                    <p style="color:#475569; line-height:1.7;">
                        {{ $workshop->description ?? 'No description available.' }}
                    </p>
                </div>

                <!-- Actions -->
                <div style="display:flex; gap:12px; margin-top:32px;">

                    <a href="{{ route('workshops.index') }}"
                        style="background:#e2e8f0; color:#334155;
                              padding:10px 20px; border-radius:8px;
                              text-decoration:none; font-weight:600;">
                        Back to Workshops
                    </a>

                    @if (auth()->user()->role === 'manager')
                        <a href="{{ route('workshops.edit', $workshop) }}"
                            style="background:#2563eb; color:white;
                                  padding:10px 20px; border-radius:8px;
                                  text-decoration:none; font-weight:600;">
                            Edit Workshop
                        </a>
                    @endif

                    @if (
                        $workshop->status === 'scheduled' &&
                            $workshop->starts_at->isFuture() &&
                            $workshop->activeRegistrations()->count() < $workshop->capacity)
                        <a href="{{ route('registrations.create', $workshop) }}"
                            style="background:#16a34a; color:white;
                                padding:10px 20px; border-radius:8px;
                                text-decoration:none; font-weight:600;">
                            + Register Attendee
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
