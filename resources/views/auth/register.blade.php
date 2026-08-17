<x-guest-layout>

    <form method="POST" action="{{ route('register') }}">

        @csrf


        <!-- Account Information -->

        <div>

            <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Create Your Account') }}
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('Enter your account and farm information.') }}
            </p>

        </div>


        <!-- Name -->

        <div class="mt-6">

            <x-input-label
                for="name"
                :value="__('Your Name')"
            />

            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />

            <x-input-error
                :messages="$errors->get('name')"
                class="mt-2"
            />

        </div>


        <!-- Mobile Number -->

        <div class="mt-4">

            <x-input-label
                for="mobile_number"
                :value="__('Mobile Number')"
            />

            <x-text-input
                id="mobile_number"
                class="block mt-1 w-full"
                type="tel"
                name="mobile_number"
                :value="old('mobile_number')"
                required
                autocomplete="tel"
                inputmode="tel"
                placeholder="09XXXXXXXX"
            />

            <x-input-error
                :messages="$errors->get('mobile_number')"
                class="mt-2"
            />

        </div>


        <!-- PIN -->

        <div class="mt-4">

            <x-input-label
                for="pin"
                :value="__('PIN')"
            />

            <x-text-input
                id="pin"
                class="block mt-1 w-full"
                type="password"
                name="pin"
                required
                autocomplete="new-password"
                inputmode="numeric"
                pattern="[0-9]+"
                placeholder="Enter PIN"
            />

            <x-input-error
                :messages="$errors->get('pin')"
                class="mt-2"
            />

        </div>


        <!-- Confirm PIN -->

        <div class="mt-4">

            <x-input-label
                for="pin_confirmation"
                :value="__('Confirm PIN')"
            />

            <x-text-input
                id="pin_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="pin_confirmation"
                required
                autocomplete="new-password"
                inputmode="numeric"
                pattern="[0-9]+"
                placeholder="Confirm PIN"
            />

            <x-input-error
                :messages="$errors->get('pin_confirmation')"
                class="mt-2"
            />

        </div>


        <!-- Farm Information -->

        <div class="mt-8 border-t pt-6">

            <h3 class="text-md font-semibold text-gray-900">
                {{ __('Farm Information') }}
            </h3>

            <p class="mt-1 text-sm text-gray-600">
                {{ __('This farm will be connected to your account.') }}
            </p>

        </div>


        <!-- Farm Name -->

        <div class="mt-4">

            <x-input-label
                for="farm_name"
                :value="__('Farm Name')"
            />

            <x-text-input
                id="farm_name"
                class="block mt-1 w-full"
                type="text"
                name="farm_name"
                :value="old('farm_name')"
                required
            />

            <x-input-error
                :messages="$errors->get('farm_name')"
                class="mt-2"
            />

        </div>


        <!-- Registered Birds -->

        <div class="mt-4">

            <x-input-label
                for="registered_birds"
                :value="__('Registered Birds')"
            />

            <x-text-input
                id="registered_birds"
                class="block mt-1 w-full"
                type="number"
                name="registered_birds"
                :value="old('registered_birds')"
                min="1"
                step="1"
                required
            />

            <x-input-error
                :messages="$errors->get('registered_birds')"
                class="mt-2"
            />

        </div>


        <!-- Registration Date -->

        <div class="mt-4">

            <x-input-label
                for="registration_date"
                :value="__('Farm Registration Date')"
            />

            <x-text-input
                id="registration_date"
                class="block mt-1 w-full"
                type="date"
                name="registration_date"
                :value="old('registration_date', today()->format('Y-m-d'))"
                required
            />

            <x-input-error
                :messages="$errors->get('registration_date')"
                class="mt-2"
            />

        </div>


        <!-- Actions -->

        <div class="flex items-center justify-end mt-6">

            <a
                class="underline text-sm text-gray-600 hover:text-gray-900
                rounded-md focus:outline-none
                focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}"
            >
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>

        </div>

    </form>

</x-guest-layout>
