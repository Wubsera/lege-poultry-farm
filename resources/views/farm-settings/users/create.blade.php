<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add User - {{ $farm->farm_name ?? 'Farm' }}</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .header {
            background: #1f2937;
            color: white;
            padding: 20px 30px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            margin: 5px 0 0;
            color: #d1d5db;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .card h2 {
            margin-top: 0;
        }

        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 15px;
        }

        .help {
            margin-top: 6px;
            color: #6b7280;
            font-size: 13px;
        }

        button {
            padding: 12px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
        }

        button:hover {
            background: #1d4ed8;
        }

        .errors {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .back {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #2563eb;
        }

    </style>

</head>

<body>

@include('layouts.farm-navigation')


<div class="header">

    <h1>
        👤 Add User
    </h1>

    <p>
        Add a staff user to {{ $farm->farm_name ?? 'your farm' }}
    </p>

</div>


<div class="container">

    <div class="card">

        <h2>
            User Information
        </h2>


        @if ($errors->any())

            <div class="errors">

                @foreach ($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('farm-settings.users.store') }}"
        >

            @csrf


            <!-- Name -->

            <div class="field">

                <label for="name">
                    Name
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter user name"
                    required
                >

            </div>


            <!-- Mobile Number -->

            <div class="field">

                <label for="mobile_number">
                    Mobile Number
                </label>

                <input
                    type="tel"
                    id="mobile_number"
                    name="mobile_number"
                    value="{{ old('mobile_number') }}"
                    placeholder="09xxxxxxxx"
                    required
                >

                <div class="help">
                    You can enter 09xxxxxxxx, 9xxxxxxxx,
                    2519xxxxxxxx, or +2519xxxxxxxx.
                </div>

            </div>


            <!-- Email -->

            <div class="field">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="user@example.com"
                >

                <div class="help">
                    Optional. Used for PIN recovery in the future.
                </div>

            </div>


            <!-- PIN -->

            <div class="field">

                <label for="pin">
                    PIN
                </label>

                <input
                    type="password"
                    id="pin"
                    name="pin"
                    inputmode="numeric"
                    autocomplete="new-password"
                    placeholder="Enter PIN"
                    required
                >

                <div class="help">
                    The user will use their mobile number and PIN to log in.
                </div>

            </div>


            <!-- Confirm PIN -->

            <div class="field">

                <label for="pin_confirmation">
                    Confirm PIN
                </label>

                <input
                    type="password"
                    id="pin_confirmation"
                    name="pin_confirmation"
                    inputmode="numeric"
                    autocomplete="new-password"
                    placeholder="Confirm PIN"
                    required
                >

            </div>


            <button type="submit">
                Save User
            </button>

        </form>


        <a
            href="{{ route('farm-settings.users.index') }}"
            class="back"
        >
            ← Back to Users
        </a>


    </div>

</div>


</body>

</html>
