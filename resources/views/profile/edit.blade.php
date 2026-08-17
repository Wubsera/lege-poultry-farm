<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center gap-3">

            <a
                href="{{ route('dashboard') }}"
                class="flex items-center"
                title="Back to Dashboard"
            >
                <!-- <span class="text-4xl leading-none">
                    🐔
                </span> -->
            </a>

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Profile') }}
            </h2>

        </div>

    </x-slot>


    <div class="py-12">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">


            <!-- Profile Information -->

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="max-w-xl">

                    @include(
                        'profile.partials.update-profile-information-form'
                    )

                </div>

            </div>


            <!-- Update PIN -->

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="max-w-xl">

                    @include(
                        'profile.partials.update-password-form'
                    )

                </div>

            </div>


            <!-- Delete Account -->

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <div class="max-w-xl">

                    @include(
                        'profile.partials.delete-user-form'
                    )

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
