<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ auth()->user()->farm->farm_name ?? 'Farm Settings' }}</title>

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


        @if (session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif


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
            action="/farm-settings"
        >

            @csrf

            @method('PUT')


            <!-- Farm Name -->

            <div class="field">

                <label>
                    Farm Name
                </label>

                <input
                    type="text"
                    name="farm_name"
                    value="{{ old(
                        'farm_name',
                        auth()->user()->farm->farm_name ?? ''
                    ) }}"
                    placeholder="Enter farm name"
                    required
                >

            </div>


            <!-- Registered Birds -->

            <div class="field">

                <label>
                    Registered Birds
                </label>

                <input
                    type="number"
                    name="registered_birds"
                    value="{{ old(
                        'registered_birds',
                        auth()->user()->farm?->setting?->registered_birds ?? ''
                    ) }}"
                    min="1"
                    required
                >

            </div>


            <!-- Registration Date -->

            <div class="field">

                <label>
                    Registration Date
                </label>

                <input
                    type="date"
                    name="registration_date"
                    value="{{ old(
                        'registration_date',
                        isset(auth()->user()->farm->registration_date)
                            ? auth()->user()->farm->registration_date->format('Y-m-d')
                            : today()->format('Y-m-d')
                    ) }}"
                    required
                >

            </div>


            <button type="submit">
                💾 Save Farm Information
            </button>

        </form>


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
