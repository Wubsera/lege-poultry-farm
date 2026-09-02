<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ auth()->user()->farm->farm_name ?? 'Farm Settings' }}
    </title>

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

        /*
        |--------------------------------------------------------------------------
        | Read-only fields for Staff
        |--------------------------------------------------------------------------
        */

        input[readonly] {
            background: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
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

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .errors {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .info {
            background: #eff6ff;
            color: #1e40af;
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

        .users-link {
            display: inline-block;
            margin-top: 20px;
            margin-left: 10px;
            padding: 10px 15px;
            background: #111827;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .users-link:hover {
            background: #374151;
        }

    </style>

</head>

<body>

@include('layouts.farm-navigation')


<div class="header">

    <h1>
        🐔 Farm Settings
    </h1>

    <p>
        Farm information and registered flock
    </p>

</div>


<div class="container">

    <div class="card">

        <h2>
            🏠 Farm Information
        </h2>


        {{-- Success Message --}}

        @if (session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif


        {{-- Validation Errors --}}

        @if ($errors->any())

            <div class="errors">

                @foreach ($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        {{-- Staff Information --}}

        @if (!auth()->user()->is_admin)

            <div class="info">
                You are viewing your farm information.
                Only the Farm Administrator can update these settings.
            </div>

        @endif


        <form
            method="POST"
            action="/farm-settings"
        >

            @csrf

            @method('PUT')


            {{-- Farm Name --}}

            <div class="field">

                <label>
                    Farm Name
                </label>

                <input
                    type="text"
                    name="farm_name"
                    value="{{ old(
                        'farm_name',
                        $farm->farm_name ?? ''
                    ) }}"
                    placeholder="Enter farm name"
                    required
                    @if (!auth()->user()->is_admin)
                        readonly
                    @endif
                >

            </div>


            {{-- Registered Birds --}}

            <div class="field">

                <label>
                    Registered Birds
                </label>

                <input
                    type="number"
                    name="registered_birds"
                    value="{{ old(
                        'registered_birds',
                        $settings?->registered_birds ?? ''
                    ) }}"
                    min="1"
                    required
                    @if (!auth()->user()->is_admin)
                        readonly
                    @endif
                >

            </div>


            {{-- Registration Date --}}

            <div class="field">

                <label>
                    Registration Date
                </label>

                <input
                    type="date"
                    name="registration_date"
                    value="{{ old(
                        'registration_date',
                        $farm->registration_date
                            ? $farm->registration_date->format('Y-m-d')
                            : today()->format('Y-m-d')
                    ) }}"
                    required
                    @if (!auth()->user()->is_admin)
                        readonly
                    @endif
                >

            </div>


            {{-- Save Button --}}

            @if (auth()->user()->is_admin)

                <button type="submit">
                    💾 Save Farm Information
                </button>

            @endif


        </form>


        {{-- Admin: Manage Users --}}

        @if (auth()->user()->is_admin)

            <a
                href="{{ route('farm-settings.users.index') }}"
                class="users-link"
            >
                👥 Manage Users
            </a>

        @endif


        <a
            href="/reports"
            class="back"
        >
            ← Back to Reports
        </a>


    </div>

</div>


</body>

</html>
