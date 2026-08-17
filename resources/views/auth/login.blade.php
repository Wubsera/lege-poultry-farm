<x-guest-layout>

    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">
            {{ __('Farm Login') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Use your mobile number and PIN to continue.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Mobile Number -->
        <div>
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
                autofocus
                autocomplete="tel"
                placeholder="09xxxxxxxx"
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
                inputmode="numeric"
                pattern="[0-9]*"
                required
                autocomplete="current-password"
            />

            <x-input-error
                :messages="$errors->get('pin')"
                class="mt-2"
            />

        </div>


        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember" class="inline-flex items-center">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                >

                <span class="ms-2 text-sm text-gray-600">
                    {{ __('Remember me') }}
                </span>
            </label>
        </div>


        <!-- Actions -->
        <div class="flex items-center justify-end mt-6">

            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('register') }}"
            >
                {{ __('Create account') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Login') }}
            </x-primary-button>

        </div>

    </form>

</x-guest-layout>
