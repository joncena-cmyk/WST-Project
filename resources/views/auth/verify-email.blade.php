<x-guest-layout>

    <div class="mb-6 flex items-center justify-center w-14 h-14 rounded-full bg-comsoc-100 mx-auto">
        <svg class="w-7 h-7 text-comsoc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">Check your email</h2>
        <p class="mt-2 text-sm text-gray-500">
            We sent a verification link to your email address. Click the link to activate your account.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm text-green-700 font-medium">
                A new verification link has been sent to your email address.
            </p>
        </div>
    @endif

    <div class="space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full justify-center py-2.5 text-sm">
                {{ __('Resend verification email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full text-center text-sm text-gray-500 hover:text-gray-700 py-2">
                {{ __('Log out') }}
            </button>
        </form>
    </div>

</x-guest-layout>
