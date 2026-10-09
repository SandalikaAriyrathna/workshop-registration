
<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size:22px; font-weight:700; color:#1e293b;">
            Register Attendee
        </h2>
    </x-slot>

    <div style="background:#f5f7fb; min-height:100vh; padding:40px 20px;">
        <div style="max-width:650px; margin:auto;">

            <div style="background:white; padding:32px;
                        border-radius:14px;
                        box-shadow:0 4px 20px rgba(0,0,0,.06);
                        color:#1e293b;">

                <h2 style="font-size:22px; font-weight:700;">
                    {{ $workshop->title }}
                </h2>

                <p style="color:#64748b; margin-top:8px;">
                    {{ $workshop->starts_at->format('d M Y, h:i A') }}
                </p>

                <p style="color:#64748b; margin-bottom:24px;">
                    Available Seats:
                    <strong>
                        {{ max(0, $workshop->capacity - $workshop->activeRegistrations()->count()) }}
                    </strong>
                </p>

                @if($errors->any())
                    <div style="background:#fee2e2; color:#991b1b;
                                padding:12px; border-radius:8px; margin-bottom:20px;">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form action="{{ route('registrations.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="workshop_id"
                           value="{{ $workshop->id }}">

                    <div style="margin-bottom:20px;">
                        <label for="attendee_name"
                               style="display:block; font-weight:600; margin-bottom:8px;">
                            Attendee Name *
                        </label>

                        <input type="text"
                               id="attendee_name"
                               name="attendee_name"
                               value="{{ old('attendee_name') }}"
                               required maxlength="255"
                               style="width:100%; padding:12px; color:#1e293b;
                                      background:white; border:1px solid #cbd5e1;
                                      border-radius:8px;">
                    </div>

                    <div style="margin-bottom:28px;">
                        <label for="attendee_email"
                               style="display:block; font-weight:600; margin-bottom:8px;">
                            Attendee Email *
                        </label>

                        <input type="email"
                               id="attendee_email"
                               name="attendee_email"
                               value="{{ old('attendee_email') }}"
                               required maxlength="255"
                               style="width:100%; padding:12px; color:#1e293b;
                                      background:white; border:1px solid #cbd5e1;
                                      border-radius:8px;">
                    </div>

                    <div style="display:flex; gap:12px; flex-wrap:wrap;">

                        <a href="{{ route('workshops.show', $workshop) }}"
                           style="background:#e2e8f0; color:#334155;
                                  padding:12px 20px; border-radius:8px;
                                  text-decoration:none; font-weight:600;">
                            Cancel
                        </a>

                        <button type="submit"
                                style="background:#2563eb; color:white;
                                       padding:12px 20px; border-radius:8px;
                                       font-weight:600; border:none; cursor:pointer;">
                            Confirm Registration
                        </button>

                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
