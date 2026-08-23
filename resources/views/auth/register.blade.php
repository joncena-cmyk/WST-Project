<x-guest-layout :wide="true">

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Create an account</h2>
        <p class="mt-1 text-sm text-gray-500">Join COMSOC Hub — CCIS, University of Makati</p>
    </div>

    {{-- Google Workspace SSO Button --}}
    <div class="mb-6">
        <a href="{{ route('auth.google') }}"
           class="w-full flex items-center justify-center gap-3 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-comsoc-500 shadow-sm transition duration-150 group">
            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
            </svg>
            <span>Sign up with UMak Google Account</span>
        </a>

        <div class="relative mt-6">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase">
                <span class="bg-white px-3 text-gray-400 font-medium tracking-wider">or register with email</span>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('register') }}"
          class="space-y-5"
          x-data="registerForm()"
          @submit.prevent="submit">
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
            <p class="mt-1 text-xs text-gray-400">
                Enter your name exactly as it appears on your university records — it will be used on certificates.
            </p>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
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
                          placeholder="yourname@umak.edu.ph" />
            <p class="mt-1 text-xs text-gray-400">
                Must be a valid <span class="font-medium text-gray-500">@umak.edu.ph</span> email address.
            </p>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Student ID + Program -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="student_id" :value="__('Student ID')" />
                <x-text-input id="student_id"
                              class="block mt-1 w-full"
                              type="text"
                              name="student_id"
                              :value="old('student_id')"
                              autocomplete="off"
                              placeholder="e.g. 2021-00001" />
                <p class="mt-1 text-xs text-gray-400">Optional</p>
                <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="program_id" :value="__('Program')" />
                <select id="program_id"
                        name="program_id"
                        class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm bg-white">
                    <option value="">— Optional —</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->short_name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div class="relative mt-1" x-data="{ show: false }">
                <x-text-input id="password"
                              class="block w-full pr-10"
                              x-bind:type="show ? 'text' : 'password'"
                              name="password"
                              required
                              autocomplete="new-password"
                              placeholder="Min. 8 characters"
                              x-model="password"
                              @input="checkStrength" />
                <button type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                        tabindex="-1"
                        :aria-label="show ? 'Hide password' : 'Show password'">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>

            {{-- Password strength bar --}}
            <div x-show="password.length > 0" class="mt-2 space-y-1.5" style="display:none">
                <div class="flex gap-1 h-1.5">
                    <div class="flex-1 rounded-full transition-all duration-300"
                         :class="strength >= 1 ? strengthColor : 'bg-gray-200'"></div>
                    <div class="flex-1 rounded-full transition-all duration-300"
                         :class="strength >= 2 ? strengthColor : 'bg-gray-200'"></div>
                    <div class="flex-1 rounded-full transition-all duration-300"
                         :class="strength >= 3 ? strengthColor : 'bg-gray-200'"></div>
                    <div class="flex-1 rounded-full transition-all duration-300"
                         :class="strength >= 4 ? strengthColor : 'bg-gray-200'"></div>
                </div>
                <p class="text-xs" :class="strengthTextColor" x-text="strengthLabel"></p>

                {{-- Requirement checklist --}}
                <ul class="text-xs space-y-0.5 mt-1">
                    <li class="flex items-center gap-1.5" :class="checks.length ? 'text-green-600' : 'text-gray-400'">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path x-show="checks.length" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            <path x-show="!checks.length" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12h.01"/>
                        </svg>
                        At least 8 characters
                    </li>
                    <li class="flex items-center gap-1.5" :class="checks.upper ? 'text-green-600' : 'text-gray-400'">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path x-show="checks.upper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            <path x-show="!checks.upper" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12h.01"/>
                        </svg>
                        Uppercase letter
                    </li>
                    <li class="flex items-center gap-1.5" :class="checks.lower ? 'text-green-600' : 'text-gray-400'">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path x-show="checks.lower" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            <path x-show="!checks.lower" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12h.01"/>
                        </svg>
                        Lowercase letter
                    </li>
                    <li class="flex items-center gap-1.5" :class="checks.number ? 'text-green-600' : 'text-gray-400'">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path x-show="checks.number" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            <path x-show="!checks.number" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12h.01"/>
                        </svg>
                        Number
                    </li>
                    <li class="flex items-center gap-1.5" :class="checks.symbol ? 'text-green-600' : 'text-gray-400'">
                        <svg class="w-3 h-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path x-show="checks.symbol" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            <path x-show="!checks.symbol" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12h.01"/>
                        </svg>
                        Symbol (e.g. !@#$)
                    </li>
                </ul>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm password')" />
            <div class="relative mt-1" x-data="{ show: false }">
                <x-text-input id="password_confirmation"
                              class="block w-full pr-10"
                              x-bind:type="show ? 'text' : 'password'"
                              name="password_confirmation"
                              required
                              autocomplete="new-password"
                              placeholder="Repeat your password"
                              x-model="confirm"
                              @input="checkMatch" />
                <button type="button"
                        @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none"
                        tabindex="-1"
                        :aria-label="show ? 'Hide password' : 'Show password'">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" style="display:none">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>

            {{-- Password match indicator --}}
            <div x-show="confirm.length > 0" class="mt-1.5 flex items-center gap-1.5" style="display:none">
                {{-- Match --}}
                <template x-if="passwordsMatch">
                    <span class="flex items-center gap-1 text-xs text-green-600">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        Passwords match
                    </span>
                </template>
                {{-- No match --}}
                <template x-if="!passwordsMatch">
                    <span class="flex items-center gap-1 text-xs text-red-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Passwords do not match
                    </span>
                </template>
            </div>

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms & Conditions -->
        <div>
            <label class="flex items-start gap-3 cursor-pointer group">
                <div class="relative mt-0.5">
                    <input type="checkbox"
                           name="terms"
                           id="terms"
                           x-model="terms"
                           class="rounded border-gray-300 text-comsoc-600 shadow-sm focus:ring-comsoc-500 mt-0.5" />
                </div>
                <span class="text-sm text-gray-600 leading-relaxed">
                    I agree to the
                    <a href="#" class="text-comsoc-600 hover:text-comsoc-800 font-medium underline underline-offset-2">Terms of Use</a>
                    and acknowledge that this platform is exclusively for COMSOC members and students of CCIS, University of Makati.
                </span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <!-- Submit with loading state -->
        <div>
            <button type="submit"
                    :disabled="loading || !terms"
                    class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-comsoc-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-comsoc-700 focus:bg-comsoc-700 active:bg-comsoc-800 focus:outline-none focus:ring-2 focus:ring-comsoc-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-60 disabled:cursor-not-allowed">
                <svg x-show="loading"
                     class="animate-spin w-4 h-4 text-white"
                     fill="none" viewBox="0 0 24 24" aria-hidden="true" style="display:none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor"
                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span x-text="loading ? 'Creating account…' : 'Create account'">Create account</span>
            </button>
            <p x-show="!terms && !loading" class="mt-1.5 text-center text-xs text-gray-400" style="display:none">
                You must accept the terms to continue.
            </p>
        </div>

        <!-- Login link -->
        <p class="text-center text-sm text-gray-500">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-comsoc-600 hover:text-comsoc-800">
                Sign in
            </a>
        </p>

    </form>

    @push('scripts')
    <script>
    function registerForm() {
        return {
            password:      '',
            confirm:       '',
            terms:         false,
            loading:       false,
            strength:      0,
            strengthLabel: '',
            strengthColor: 'bg-gray-200',
            strengthTextColor: 'text-gray-400',
            passwordsMatch: false,
            checks: {
                length: false,
                upper:  false,
                lower:  false,
                number: false,
                symbol: false,
            },

            checkStrength() {
                const p = this.password;
                this.checks.length = p.length >= 8;
                this.checks.upper  = /[A-Z]/.test(p);
                this.checks.lower  = /[a-z]/.test(p);
                this.checks.number = /[0-9]/.test(p);
                this.checks.symbol = /[^A-Za-z0-9]/.test(p);

                const passed = Object.values(this.checks).filter(Boolean).length;
                this.strength = passed;

                const levels = [
                    { label: '',        color: 'bg-gray-200',   text: 'text-gray-400'  },
                    { label: 'Weak',    color: 'bg-red-400',    text: 'text-red-500'   },
                    { label: 'Fair',    color: 'bg-orange-400', text: 'text-orange-500'},
                    { label: 'Good',    color: 'bg-yellow-400', text: 'text-yellow-600'},
                    { label: 'Strong',  color: 'bg-green-400',  text: 'text-green-600' },
                    { label: 'Strong',  color: 'bg-green-500',  text: 'text-green-700' },
                ];
                const l = levels[passed] || levels[0];
                this.strengthLabel     = l.label;
                this.strengthColor     = l.color;
                this.strengthTextColor = l.text;

                // Also re-check match if confirm already has a value
                if (this.confirm.length > 0) this.checkMatch();
            },

            checkMatch() {
                this.passwordsMatch = this.password === this.confirm && this.confirm.length > 0;
            },

            submit(e) {
                // Re-validate match before allowing submit
                this.checkMatch();
                if (!this.terms) return;
                this.loading = true;
                e.target.submit();
            },
        }
    }
    </script>
    @endpush

</x-guest-layout>
