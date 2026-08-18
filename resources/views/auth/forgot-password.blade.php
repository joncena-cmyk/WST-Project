<x-guest-layout>

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Reset your password</h2>
        <p class="mt-1 text-sm text-gray-500">
            Enter your email and we'll send you a reset link.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
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
                          placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center py-2.5 text-sm">
                {{ __('Send reset link') }}
            </x-primary-button>
        </div>

        <p class="text-center text-sm text-gray-500">
            Remembered it?
            <a href="{{ route('login') }}" class="font-medium text-comsoc-600 hover:text-comsoc-800">
                Back to sign in
            </a>
        </p>
    </form>

</x-guest-layout>
