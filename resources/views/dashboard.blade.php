<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-3">Welcome, {{ auth()->user()->name }}</h3>
                    @if (auth()->user()->role === 'admin')
                        <p class="mb-4">Create staff accounts and assign Manager or Staff access.</p>
                        <a class="text-blue-600 underline" href="{{ route('users.index') }}">Manage user accounts</a>
                    @else
                        <p class="mb-4">Find workshops, register attendees, and view registration history.</p>
                        <a class="text-blue-600 underline" href="{{ route('workshops.index') }}">Browse workshops</a>
                        @if (auth()->user()->role === 'manager')
                            <a class="text-blue-600 underline ml-4" href="{{ route('workshops.create') }}">Add a workshop</a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
