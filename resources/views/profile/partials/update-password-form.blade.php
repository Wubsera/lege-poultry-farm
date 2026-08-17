<section>

    <header>

        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update PIN') }}
        </h2>

        <!-- <p class="mt-1 text-sm text-gray-600">
            {{ __('Use a simple PIN to protect your farm account.') }}
        </p> -->

    </header>


    <form
        method="post"
        action="{{ route('password.update') }}"
        class="mt-6 space-y-6"
    >

        @csrf

        @method('put')


        <!-- Current PIN -->

        <div>

            <x-input-label
                for="current_password"
                :value="__('Current PIN')"
            />

            <x-text-input
                id="current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
                inputmode="numeric"
                required
            />

            <x-input-error
                :messages="$errors->updatePassword->get('current_password')"
                class="mt-2"
            />

        </div>


        <!-- New PIN -->

        <div>

            <x-input-label
                for="password"
                :value="__('New PIN')"
            />

            <x-text-input
                id="password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                inputmode="numeric"
                required
            />

            <x-input-error
                :messages="$errors->updatePassword->get('password')"
                class="mt-2"
            />

        </div>


        <!-- Confirm PIN -->

        <div>

            <x-input-label
                for="password_confirmation"
                :value="__('Confirm New PIN')"
            />

            <x-text-input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
                inputmode="numeric"
                required
            />

            <x-input-error
                :messages="$errors->updatePassword->get('password_confirmation')"
                class="mt-2"
            />

        </div>


        <div class="flex items-center gap-4">

            <x-primary-button>
                {{ __('Save PIN') }}
            </x-primary-button>


            @if (session('status') === 'password-updated')

                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >
                    {{ __('PIN saved.') }}
                </p>

            @endif

        </div>

    </form>

</section>
