<x-guest-layout>

    <div class="mb-6 flex items-center justify-center w-14 h-14 rounded-full bg-comsoc-100 mx-auto">
        <svg class="w-7 h-7 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Confirm your password</h2>
        <p class="mt-2 text-sm text-gray-500">
            This area requires your password to continue.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password"
                          class="block mt-1 w-full"
                          type="password"
                          name="password"
                          required
                          autocomplete="current-password"
                          placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-primary-button class="w-full justify-center py-2.5 text-sm">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>

</x-guest-layout>
