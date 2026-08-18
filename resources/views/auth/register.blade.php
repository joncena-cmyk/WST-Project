<x-guest-layout>

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Create an account</h2>
        <p class="mt-1 text-sm text-gray-500">Join COMSOC Hub — CCIS, University of Makati</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Full Name -->
        <div>
            <x-input-label for="name" :value="__('Full name')" />
            <x-text-input id="name"
                          class="block mt-1 w-full"
                          type="text"
                          name="name"
                          :value="old('name')"
                          required
                          autofocus
                          autocomplete="name"
                          placeholder="Juan dela Cruz" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Student ID -->
        <div>
            <x-input-label for="student_id" :value="__('Student ID')" />
            <x-text-input id="student_id"
                          class="block mt-1 w-full"
                          type="text"
                          name="student_id"
                          :value="old('student_id')"
                          autocomplete="off"
                          placeholder="e.g. 2021-00001 (optional)" />
            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
        </div>

        <!-- Program -->
        <div>
            <x-input-label for="program_id" :value="__('Program')" />
            <select id="program_id"
                    name="program_id"
                    class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                <option value="">— Select your program (optional) —</option>
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                        {{ $program->short_name }} ({{ $program->code }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email"
                          class="block mt-1 w-full"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autocomplete="username"
                          placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required
                          autocomplete="new-password"
                          placeholder="Min. 8 characters" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <x-text-input id="password_confirmation"
                          class="block mt-1 w-full"
                          type="password"
                          name="password_confirmation"
                          required
                          autocomplete="new-password"
                          placeholder="Repeat your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit -->
        <div>
            <x-primary-button class="w-full justify-center py-2.5 text-sm">
                {{ __('Create account') }}
            </x-primary-button>
        </div>

        <!-- Login link -->
        <p class="text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-comsoc-600 hover:text-comsoc-800">
                Sign in
            </a>
        </p>
    </form>

</x-guest-layout>
