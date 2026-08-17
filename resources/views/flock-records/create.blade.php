<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Flock Management</title>

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
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .status-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .status-card {
            padding: 18px;
            border-radius: 10px;
        }

        .status-card h3 {
            margin: 0 0 8px;
            font-size: 14px;
            color: #374151;
        }

        .status-card .value {
            font-size: 24px;
            font-weight: bold;
        }

        .total {
            background: #e0f2fe;
        }

        .sick {
            background: #fef3c7;
        }

        .recovered {
            background: #dcfce7;
        }

        .dead {
            background: #fee2e2;
        }

        .available {
            background: #dcfce7;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
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

        .info {
            background: #eff6ff;
            color: #1e40af;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .setting-card {
            background: #dbeafe;
            padding: 12px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .setting-card h3 {
            margin: 0 0 6px;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
        }

        .setting-value {
            font-size: 18px;
            font-weight: bold;
        }

        .farm-edit-link {
            text-decoration: none;
            color: #2563eb;
        }

        .farm-edit-link:hover {
            text-decoration: underline;
        }

        .bottom-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-top: 20px;
        }

        .back {
            display: inline-block;
            text-decoration: none;
            color: #2563eb;
        }

        @media (max-width: 850px) {

            .status-cards {
                grid-template-columns: 1fr 1fr 1fr;
            }

        }

        @media (max-width: 700px) {

            .status-cards {
                grid-template-columns: 1fr 1fr;
            }

            .container {
                margin: 20px auto;
                padding: 15px;
            }

        }

        @media (max-width: 600px) {

            .bottom-actions {
                flex-direction: column;
                align-items: stretch;
            }

            .setting-card {
                width: 100%;
            }

            .back {
                text-align: center;
            }

        }

        @media (max-width: 450px) {

            .status-cards {
                grid-template-columns: 1fr;
            }

        }

    </style>

</head>

<body>
@include('layouts.farm-navigation')

<div class="header">

    <!-- <h1>🐔 Lege Poultry Farm</h1> -->
     <h1>🐔 {{ $farm?->farm_name ?? 'Poultry Farm' }}</h1>

    <p>Flok Record</p>

</div>


<div class="container">


    <!-- CURRENT FLOCK STATUS -->

    <div class="status-cards">

        <div class="status-card total">

            <h3>🐔 Total Birds</h3>

            <div class="value">
                {{ number_format($totalBirds) }}
            </div>

        </div>


        <div class="status-card sick">

            <h3>🤒 Sick Birds</h3>

            <div class="value">
                {{ number_format($sickBirds) }}
            </div>

        </div>


        <div class="status-card recovered">

            <h3>💚 Recovered Birds</h3>

            <div class="value">
                {{ number_format($recoveredBirds) }}
            </div>

        </div>


        <div class="status-card dead">

            <h3>💀 Dead Birds</h3>

            <div class="value">
                {{ number_format($deadBirds) }}
            </div>

        </div>


        <div class="status-card available">

            <h3>🐔 Available Birds</h3>

            <div class="value">
                {{ number_format($availableBirds) }}
            </div>

        </div>

    </div>


    <div class="card">


        <div class="info">

            <strong>Daily flock record</strong><br>

            Enter only the health changes for this date.
            Total birds are registered separately and do not need
            to be entered every day.

        </div>


        @if (session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif


        @if ($errors->any())

            <div class="errors">

                @foreach ($errors->all() as $error)

                    <div>{{ $error }}</div>

                @endforeach

            </div>

        @endif


        <form method="POST" action="/flock-records">

            @csrf


            <div class="field">

                <label>Date</label>

                <input
                    type="date"
                    name="record_date"
                    value="{{ old('record_date', today()->format('Y-m-d')) }}"
                    required
                >

            </div>


            <div class="field">

                <label>🤒 Sick Birds</label>

                <input
                    type="number"
                    name="sick"
                    value="{{ old('sick', 0) }}"
                    min="0"
                    required
                >

            </div>


            <div class="field">

                <label>💚 Recovered Birds</label>

                <input
                    type="number"
                    name="recovered"
                    value="{{ old('recovered', 0) }}"
                    min="0"
                    required
                >

            </div>


            <div class="field">

                <label>💀 Dead Birds</label>

                <input
                    type="number"
                    name="dead"
                    value="{{ old('dead', 0) }}"
                    min="0"
                    required
                >

            </div>


            <button type="submit">
                💾 SAVE FLOCK RECORD
            </button>

        </form>


        <!-- BOTTOM ACTIONS -->

        <div class="bottom-actions">


            <!-- BACK TO REPORTS -->

            <a href="/reports" class="back">
                ← Back to Reports
            </a>


            <!-- FARM SETTING -->

            <div class="setting-card">

                <div class="setting-value">

                    <a
                        href="/farm-settings"
                        class="farm-edit-link"
                    >
                        ⚙️ Setting
                    </a>

                </div>

            </div>


        </div>


    </div>


</div>


</body>

</html>
