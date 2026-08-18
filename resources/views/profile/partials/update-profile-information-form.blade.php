<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update your name, email, student ID, and program.') }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post"
          action="{{ route('profile.update') }}"
          enctype="multipart/form-data"
          class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Profile picture --}}
        <div>
            <x-input-label :value="__('Profile Picture')" />
            <div class="mt-2 flex items-center gap-4">
                <img src="{{ $user->getProfilePictureUrl() }}"
                     alt="{{ $user->name }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-gray-200" />
                <div class="flex-1">
                    <input type="file"
                           id="profile_picture"
                           name="profile_picture"
                           accept="image/jpeg,image/png,image/webp"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-3
                                  file:rounded-md file:border-0
                                  file:text-xs file:font-medium
                                  file:bg-comsoc-50 file:text-comsoc-700
                                  hover:file:bg-comsoc-100 cursor-pointer" />
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG or WebP. Max 2MB.</p>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('profile_picture')" />
        </div>

        {{-- Name --}}
        <div>
            <x-input-label for="name" :value="__('Full name')" />
            <x-text-input id="name"
                          name="name"
                          type="text"
                          class="mt-1 block w-full"
                          :value="old('name', $user->name)"
                          required
                          autofocus
                          autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Student ID --}}
        <div>
            <x-input-label for="student_id" :value="__('Student ID')" />
            <x-text-input id="student_id"
                          name="student_id"
                          type="text"
                          class="mt-1 block w-full"
                          :value="old('student_id', $user->student_id)"
                          placeholder="e.g. 2021-00001" />
            <x-input-error class="mt-2" :messages="$errors->get('student_id')" />
        </div>

        {{-- Program --}}
        <div>
            <x-input-label for="program_id" :value="__('Program')" />
            <select id="program_id"
                    name="program_id"
                    class="mt-1 block w-full border-gray-300 focus:border-comsoc-500 focus:ring-comsoc-500 rounded-md shadow-sm text-sm">
                <option value="">— Not specified —</option>
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}"
                        {{ old('program_id', $user->program_id) == $program->id ? 'selected' : '' }}>
                        {{ $program->short_name }} ({{ $program->code }})
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('program_id')" />
        </div>

        {{-- Email --}}
        <div>
            <x-input-label for="email" :value="__('Email address')" />
            <x-text-input id="email"
                          name="email"
                          type="email"
                          class="mt-1 block w-full"
                          :value="old('email', $user->email)"
                          required
                          autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-800">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification"
                                class="underline font-medium hover:text-yellow-900 focus:outline-none">
                            {{ __('Resend verification email') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-sm text-green-600 font-medium">
                            {{ __('A new verification link has been sent.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Role badge (read-only) --}}
        <div>
            <x-input-label :value="__('Role')" />
            <div class="mt-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-comsoc-100 text-comsoc-800">
                    {{ $user->getRoleLabel() }}
                </span>
            </div>
            <p class="mt-1 text-xs text-gray-500">Role is managed by system administrators.</p>
        </div>

        {{-- Save button --}}
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-sm text-green-600 font-medium">
                    {{ __('Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
