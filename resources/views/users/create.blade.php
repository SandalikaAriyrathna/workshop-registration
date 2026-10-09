
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            Create Staff Account
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">

                <form action="{{ route('users.store') }}" method="POST">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200 mb-2">
                            Name
                        </label>

                        <input type="text" name="name"
                            value="{{ old('name') }}"
                            class="w-full rounded border-gray-300"
                            required>

                        @error('name')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200 mb-2">
                            Email
                        </label>

                        <input type="email" name="email"
                            value="{{ old('email') }}"
                            class="w-full rounded border-gray-300"
                            required>

                        @error('email')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role -->
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200 mb-2">
                            Role
                        </label>

                        <select name="role"
                            class="w-full rounded border-gray-300"
                            required>
                            <option value="">Select Role</option>
                            <option value="manager"
                                @selected(old('role') === 'manager')>
                                Manager
                            </option>
                            <option value="staff"
                                @selected(old('role') === 'staff')>
                                Staff
                            </option>
                        </select>

                        @error('role')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-200 mb-2">
                            Password
                        </label>

                        <input type="password" name="password"
                            class="w-full rounded border-gray-300"
                            required>

                        @error('password')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label class="block text-gray-700 dark:text-gray-200 mb-2">
                            Confirm Password
                        </label>

                        <input type="password"
                            name="password_confirmation"
                            class="w-full rounded border-gray-300"
                            required>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded">
                            Create Account
                        </button>

                        <a href="{{ route('users.index') }}"
                            class="inline-block bg-gray-500 text-white px-5 py-2 rounded">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
