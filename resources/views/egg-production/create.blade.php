<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lege Poultry Farm - Egg Production</title>

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
            max-width: 650px;
            margin: auto;
            padding: 30px 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .card h2 {
            margin-top: 0;
            margin-bottom: 25px;
        }

        .field {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #374151;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        .error-box {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .success-box {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .buttons {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .save-button {
            flex: 1;
            padding: 13px;
            border: none;
            border-radius: 7px;
            background: #16a34a;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .save-button:hover {
            background: #15803d;
        }

        .back-button {
            flex: 1;
            padding: 13px;
            border-radius: 7px;
            background: #e5e7eb;
            color: #374151;
            text-decoration: none;
            text-align: center;
            font-weight: bold;
        }

        .back-button:hover {
            background: #d1d5db;
        }

        .hint {
            margin-top: 5px;
            font-size: 13px;
            color: #6b7280;
        }

        @media (max-width: 500px) {

            .container {
                padding: 20px 15px;
            }

            .card {
                padding: 20px;
            }

            .buttons {
                flex-direction: column;
            }
        }
    </style>
    @include('layouts.farm-navigation')
</head>

<body>
<div class="header">

    <h1>🐔 Lege Poultry Farm</h1>

    <p>Egg Production</p>

</div>


<div class="container">

    <div class="card">

        <h2>🥚 Record Egg Production</h2>


        @if ($errors->any())

            <div class="error-box">

                <strong>Please fix the following:</strong>

                <ul>

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        @if (session('success'))

            <div class="success-box">

                {{ session('success') }}

            </div>

        @endif


        <form method="POST" action="/egg-production">

            @csrf


            <div class="field">

                <label for="production_date">
                    📅 Production Date
                </label>

                <input
                    type="date"
                    id="production_date"
                    name="production_date"
                    value="{{ old('production_date', today()->format('Y-m-d')) }}"
                    required
                >

            </div>


            <div class="field">

                <label for="produced">
                    🥚 Eggs Produced
                </label>

                <input
                    type="number"
                    id="produced"
                    name="produced"
                    value="{{ old('produced') }}"
                    min="0"
                    required
                >

                <div class="hint">
                    Enter the total number of eggs collected.
                </div>

            </div>


            <div class="field">

                <label for="broken">
                    💔 Broken Eggs
                </label>

                <input
                    type="number"
                    id="broken"
                    name="broken"
                    value="{{ old('broken', 0) }}"
                    min="0"
                    required
                >

                <div class="hint">
                    Enter the number of broken or damaged eggs.
                </div>

            </div>


            <div class="buttons">

                <a href="/" class="back-button">
                    🏠 Dashboard
                </a>

                <button type="submit" class="save-button">
                    💾 SAVE PRODUCTION
                </button>

            </div>


        </form>

    </div>

</div>

</body>
</html>
```
