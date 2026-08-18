<x-guest-layout>

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Sign in to your account</h2>
        <p class="mt-1 text-sm text-gray-500">Welcome back to COMSOC Hub</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email"
                          class="block mt-1 w-full"
                          type="email"
                          name="email"
                          :value="old('email')"
                          required
                          autofocus
                          autocomplete="username"
                          placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-comsoc-600 hover:text-comsoc-800 font-medium"
                       href="{{ route('password.request') }}">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <x-text-input id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required
                          autocomplete="current-password"
                          placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember_me"
                   type="checkbox"
                   class="rounded border-gray-300 text-comsoc-600 shadow-sm focus:ring-comsoc-500"
                   name="remember">
            <label for="remember_me" class="ms-2 text-sm text-gray-600">
                {{ __('Remember me') }}
            </label>
        </div>

        <!-- Submit -->
        <div>
            <x-primary-button class="w-full justify-center py-2.5 text-sm">
                {{ __('Sign in') }}
            </x-primary-button>
        </div>

        <!-- Register link -->
        <p class="text-center text-sm text-gray-500">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-medium text-comsoc-600 hover:text-comsoc-800">
                Create one
            </a>
        </p>
    </form>

</x-guest-layout>
