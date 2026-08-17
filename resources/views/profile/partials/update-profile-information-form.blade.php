<section>

    <header>

        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update your name and mobile number.') }}
        </p>

    </header>


    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="mt-6 space-y-6"
    >

        @csrf

        @method('patch')


        <!-- Name -->

        <div>

            <x-input-label
                for="name"
                :value="__('Name')"
            />

            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('name')"
            />

        </div>


        <!-- Mobile Number -->

        <div>

            <x-input-label
                for="mobile_number"
                :value="__('Mobile Number')"
            />

            <x-text-input
                id="mobile_number"
                name="mobile_number"
                type="tel"
                class="mt-1 block w-full"
                :value="old('mobile_number', $user->mobile_number)"
                required
                autocomplete="tel"
                inputmode="tel"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('mobile_number')"
            />

        </div>


        <!-- Save -->

        <div class="flex items-center gap-4">

            <x-primary-button>
                {{ __('Save') }}
            </x-primary-button>


            @if (session('status') === 'profile-updated')

                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >
                    {{ __('Saved.') }}
                </p>

            @endif

        </div>

    </form>

</section>
