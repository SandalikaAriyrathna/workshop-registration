<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-800">
            User Management
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-5">
                <a href="{{ route('users.create') }}"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg">
                    + Create User
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow mt-4">

                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                    Staff Accounts
                </h3>



                @if (session('success'))
                    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-900 dark:text-gray-200">
                        <thead>
                            <tr class="border-b dark:border-gray-600">
                                <th class="p-3">Name</th>
                                <th class="p-3">Email</th>
                                <th class="p-3">Role</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($users as $user)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="p-3">
                                        {{ $user->name }}
                                    </td>

                                    <td class="p-3">
                                        {{ $user->email }}
                                    </td>

                                    <td class="p-3">
                                        {{ ucfirst($user->role) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="p-4 text-center">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
