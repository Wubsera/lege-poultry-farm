<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ auth()->user()->farm->farm_name }} - Dashboard
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


        /* HEADER */

        .header {
            position: relative;
            background: #1f2937;
            color: white;
            padding: 20px 30px;
            min-height: 100px;
        }

        .header h1 {
            margin: 0;
        }

        .header p {
            margin: 5px 0 0;
            color: #d1d5db;
        }


        /* ACCOUNT DROPDOWN */

        .account-actions {
            position: absolute;
            top: 18px;
            right: 30px;
            z-index: 1000;
        }

        .account-button {
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

        .account-button:hover {
            color: #d1d5db;
        }

        .account-dropdown {
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

        .account-dropdown.show {
            display: block;
        }

        .account-dropdown a,
        .account-dropdown button {
            display: block;

            width: 100%;

            padding: 11px 14px;

            border: none;
            background: white;

            color: #374151;

            text-align: left;

            font-family: Arial, sans-serif;
            font-size: 13px;

            text-decoration: none;

            cursor: pointer;
        }

        .account-dropdown a:hover,
        .account-dropdown button:hover {
            background: #f3f4f6;
        }

        .logout-form {
            margin: 0;
            padding: 0;
        }


        /* MAIN CONTAINER */

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 25px;
        }


        /* SUCCESS MESSAGE */

        .success {
            background: #dcfce7;
            color: #166534;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-weight: bold;
        }


        /* TOP CONTROLS */

        .top-controls {
            display: grid;
            grid-template-columns: 1fr auto;

            gap: 15px;

            align-items: stretch;

            margin-bottom: 25px;
        }


        /* DATE FILTER */

        .filter {
            background: white;

            padding: 16px;

            border-radius: 8px;

            margin-bottom: 0;
        }

        .range-buttons {
            display: flex;

            gap: 8px;

            flex-wrap: wrap;

            margin-bottom: 15px;
        }

        .range-button {
            padding: 8px 15px;

            border: 1px solid #d1d5db;

            border-radius: 6px;

            background: #f9fafb;

            color: #374151;

            cursor: pointer;

            font-weight: bold;

            text-decoration: none;
        }

        .range-button:hover {
            background: #f3f4f6;
        }

        .range-button.active {
            background: #2563eb;

            color: white;

            border-color: #2563eb;
        }


        /* CUSTOM DATE FORM */

        .custom-form {
            display: flex;

            gap: 12px;

            align-items: end;

            flex-wrap: wrap;

            padding-top: 5px;

            border-top: 1px solid #eee;
        }

        .field {
            display: flex;

            flex-direction: column;

            gap: 5px;
        }

        input,
        button {
            padding: 9px;

            border: 1px solid #ccc;

            border-radius: 6px;
        }

        .generate-button {
            background: #7c3aed;

            color: white;

            border: none;

            cursor: pointer;

            font-weight: bold;
        }

        .generate-button:hover {
            background: #6d28d9;
        }


        /* QUICK ACTIONS */

        .top-actions {
            background: white;

            padding: 16px;

            border-radius: 8px;

            min-width: 350px;
        }

        .top-actions .actions {
            display: flex;

            gap: 8px;

            flex-wrap: wrap;
        }

        .top-actions .actions a {
            text-decoration: none;

            background: #2563eb;

            color: white;

            padding: 8px 12px;

            border-radius: 6px;

            font-size: 13px;

            font-weight: bold;
        }

        .top-actions .actions a:hover {
            opacity: 0.9;
        }

        .report-link {
            background: #1f2937 !important;
        }


        /* SUMMARY CARDS */

        .cards {
            display: grid;

            grid-template-columns: repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 25px;
        }

        .card {
            padding: 20px;

            border-radius: 10px;

            box-shadow:
                0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .card h3 {
            margin: 0 0 10px;

            color: #374151;

            font-size: 15px;
        }

        .card .value {
            font-size: 28px;

            font-weight: bold;
        }


        /* CARD COLORS */

        .produced {
            background: #e0f2fe;
        }

        .broken {
            background: #fee2e2;
        }

        .sold {
            background: #dbeafe;
        }

        .available {
            background: #dcfce7;
        }

        .sales {
            background: #dbeafe;
        }

        .expenses {
            background: #fef3c7;
        }

        .profit-positive {
            background: #dcfce7;
        }

        .profit-zero {
            background: #fef3c7;
        }

        .profit-negative {
            background: #fee2e2;
        }


        /* CUSTOM FORM VISIBILITY */

        .custom-hidden {
            display: none;
        }

        .custom-visible {
            display: flex;
        }


        /* TABLET */

        @media (max-width: 1000px) {

            .top-controls {
                grid-template-columns: 1fr;
            }

            .top-actions {
                min-width: 0;
            }

        }


        /* MOBILE */

        @media (max-width: 768px) {

            .cards {
                grid-template-columns: 1fr 1fr;
            }

            .container {
                padding: 15px;
            }

        }


        @media (max-width: 600px) {

            .header {
                padding: 20px;

                min-height: 100px;
            }

            .account-actions {
                top: 18px;

                right: 20px;
            }

            .header h1,
            .header p {
                padding-right: 150px;
            }

        }


        @media (max-width: 500px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .custom-form {
                flex-direction: column;

                align-items: stretch;
            }

            .field,
            input,
            .generate-button {
                width: 100%;
            }

            .top-actions .actions {
                flex-direction: column;
            }

            .top-actions .actions a {
                width: 100%;

                text-align: center;
            }

            .account-actions {
                top: 15px;

                right: 15px;
            }

            .account-button {
                font-size: 13px;
            }

        }

    </style>

</head>


<body>


<!-- HEADER -->

<div class="header">

    <h1>
        🐔 {{ auth()->user()->farm->farm_name }}
    </h1>

    <p>
        Dashboard
    </p>


    <!-- ACCOUNT DROPDOWN -->

    <div class="account-actions">

        <button
            type="button"
            class="account-button"
            onclick="toggleAccountMenu()"
        >
            👤 {{ auth()->user()->name }} ▾
        </button>


        <div
            id="accountDropdown"
            class="account-dropdown"
        >

            <!-- Profile -->

            <a href="{{ route('profile.edit') }}">
                Profile
            </a>


            <!-- Logout -->

            <form
                method="POST"
                action="{{ route('logout') }}"
                class="logout-form"
            >

                @csrf

                <button type="submit">
                    Logout
                </button>

            </form>

        </div>

    </div>

</div>


<div class="container">


    <!-- SUCCESS MESSAGE -->

    @if (session('success'))

        <div class="success">
            ✅ {{ session('success') }}
        </div>

    @endif


    <!-- TOP CONTROLS -->

    <div class="top-controls">


        <!-- DASHBOARD PERIOD -->

        <div class="filter">

            <div class="range-buttons">

                <a
                    href="{{ route(
                        'dashboard',
                        ['range' => 'lifetime']
                    ) }}"
                    class="range-button
                    {{ $range === 'lifetime' ? 'active' : '' }}"
                >
                    🔵 Lifetime
                </a>


                <a
                    href="{{ route(
                        'dashboard',
                        ['range' => 'today']
                    ) }}"
                    class="range-button
                    {{ $range === 'today' ? 'active' : '' }}"
                >
                    🟢 Today
                </a>


                <button
                    type="button"
                    id="customButton"
                    class="range-button
                    {{ $range === 'custom' ? 'active' : '' }}"
                    onclick="showCustom()"
                >
                    🟣 Custom
                </button>

            </div>


            <!-- CUSTOM DATE RANGE -->

            <form
                method="GET"
                action="{{ route('dashboard') }}"
                id="customForm"
                class="
                    custom-form
                    {{
                        $range === 'custom'
                            ? 'custom-visible'
                            : 'custom-hidden'
                    }}
                "
            >

                <input
                    type="hidden"
                    name="range"
                    value="custom"
                >


                <div class="field">

                    <label>
                        From
                    </label>

                    <input
                        type="date"
                        name="from"
                        value="{{ $from ?? '' }}"
                        required
                    >

                </div>


                <div class="field">

                    <label>
                        To
                    </label>

                    <input
                        type="date"
                        name="to"
                        value="{{ $to ?? '' }}"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="generate-button"
                >
                    🔍 Search
                </button>

            </form>

        </div>


        <!-- QUICK ACTIONS -->

        <div class="top-actions">

            <div class="actions">

                <a href="/egg-production">
                    🥚 Add Production
                </a>

                <a href="/egg-sales">
                    💰 Add Sale
                </a>

                <a href="/expenses">
                    💸 Add Expense
                </a>

                <a
                    href="/reports"
                    class="report-link"
                >
                    📊 Reports
                </a>

            </div>

        </div>

    </div>


    <!-- SUMMARY CARDS -->

    <div class="cards">


        <!-- PRODUCED -->

        <div class="card produced">

            <h3>
                🥚 Eggs Produced
            </h3>

            <div class="value">
                {{ number_format($totalProduced) }}
            </div>

        </div>


        <!-- BROKEN -->

        <div class="card broken">

            <h3>
                💔 Broken Eggs
            </h3>

            <div class="value">
                {{ number_format($totalBroken) }}
            </div>

        </div>


        <!-- SOLD -->

        <div class="card sold">

            <h3>
                💰 Eggs Sold
            </h3>

            <div class="value">
                {{ number_format($totalSold) }}
            </div>

        </div>


        <!-- AVAILABLE -->

        <div class="card available">

            <h3>
                📦 Available Eggs
            </h3>

            <div class="value">
                {{ number_format($inventory) }}
            </div>

        </div>


        <!-- SALES -->

        <div class="card sales">

            <h3>
                💵 Sales
            </h3>

            <div class="value">
                {{ number_format($totalSales, 2) }} ETB
            </div>

        </div>


        <!-- EXPENSES -->

        <div class="card expenses">

            <h3>
                💸 Expenses
            </h3>

            <div class="value">
                {{ number_format($totalExpenses, 2) }} ETB
            </div>

        </div>


        <!-- PROFIT -->

        <div
            class="card
                @if ($profit > 0)
                    profit-positive
                @elseif ($profit == 0)
                    profit-zero
                @else
                    profit-negative
                @endif
            "
        >

            <h3>
                📈 Profit
            </h3>

            <div class="value">
                {{ number_format($profit, 2) }} ETB
            </div>

        </div>


    </div>

</div>


<script>

    function showCustom() {

        const form =
            document.getElementById('customForm');

        form.classList.remove('custom-hidden');

        form.classList.add('custom-visible');

    }


    function toggleAccountMenu() {

        const dropdown =
            document.getElementById('accountDropdown');

        dropdown.classList.toggle('show');

    }


    document.addEventListener(
        'click',
        function (event) {

            const account =
                document.querySelector('.account-actions');

            const dropdown =
                document.getElementById('accountDropdown');

            if (
                account &&
                !account.contains(event.target)
            ) {
                dropdown.classList.remove('show');
            }

        }
    );

</script>


</body>

</html>
