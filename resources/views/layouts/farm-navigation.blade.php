@if (auth()->check())

    <style>

        .farm-account {
            position: absolute;
            top: 18px;
            right: 30px;
            z-index: 1000;
        }

        .farm-account-button {
            background: transparent;
            border: none;
            color: white;

            padding: 0;
            margin: 0;

            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: bold;

            cursor: pointer;
        }

        .farm-account-button:hover {
            color: #d1d5db;
        }

        .farm-account-dropdown {
            display: none;

            position: absolute;
            top: 30px;
            right: 0;

            min-width: 150px;

            background: white;
            border-radius: 8px;

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);

            overflow: hidden;
        }

        .farm-account-dropdown.show {
            display: block;
        }

        .farm-account-dropdown a,
        .farm-account-dropdown button {
            display: block;

            width: 100%;

            padding: 11px 14px;

            border: none;
            background: white;

            color: #374151;

            text-align: left;
            text-decoration: none;

            font-family: Arial, sans-serif;
            font-size: 13px;

            cursor: pointer;
        }

        .farm-account-dropdown a:hover,
        .farm-account-dropdown button:hover {
            background: #f3f4f6;
        }

        .farm-logout-form {
            margin: 0;
            padding: 0;
        }

        @media (max-width: 600px) {

            .farm-account {
                top: 18px;
                right: 20px;
            }

            .farm-account-button {
                font-size: 13px;
            }

        }

        @media (max-width: 500px) {

            .farm-account {
                top: 15px;
                right: 15px;
            }

        }

    </style>


    <div class="farm-account">

        <button
            type="button"
            class="farm-account-button"
            onclick="toggleFarmAccountMenu()"
        >
            👤 {{ auth()->user()->name }} ▾
        </button>


        <div
            id="farmAccountDropdown"
            class="farm-account-dropdown"
        >

            <a href="{{ route('profile.edit') }}">
                Profile
            </a>


            <form
                method="POST"
                action="{{ route('logout') }}"
                class="farm-logout-form"
            >

                @csrf

                <button type="submit">
                    Logout
                </button>

            </form>

        </div>

    </div>


    <script>

        function toggleFarmAccountMenu() {

            const dropdown =
                document.getElementById('farmAccountDropdown');

            dropdown.classList.toggle('show');

        }


        document.addEventListener(
            'click',
            function (event) {

                const account =
                    document.querySelector('.farm-account');

                const dropdown =
                    document.getElementById(
                        'farmAccountDropdown'
                    );

                if (
                    account &&
                    !account.contains(event.target)
                ) {
                    dropdown.classList.remove('show');
                }

            }
        );

    </script>

@endif
