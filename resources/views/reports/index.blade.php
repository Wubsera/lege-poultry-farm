<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lege Poultry Farm - Reports</title>

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
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        /* TOP CONTROLS */

        .top-controls {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: stretch;
            margin-bottom: 15px;
        }

        .filter {
            background: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 0;
        }

        .filter-title {
            margin: 0 0 12px;
            font-size: 17px;
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

        .top-actions {
            background: white;
            padding: 16px;
            border-radius: 8px;
            min-width: 320px;
        }

        .top-actions-title {
            margin: 0 0 12px;
            font-size: 17px;
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
            font-weight: bold;
            font-size: 13px;
        }

        .top-actions .actions a:hover {
            opacity: 0.9;
        }

        /* COMPACT CARDS */

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 10px;
        }

        .card {
            padding: 11px 13px;
            border-radius: 7px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .card h3 {
            margin: 0 0 4px;
            color: #374151;
            font-size: 12px;
            font-weight: 600;
        }

        .card .value {
            font-size: 20px;
            font-weight: bold;
        }

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

        /* COMPACT SECTIONS */

        .section {
            background: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .section h2 {
            margin: 0 0 8px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 9px 10px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f9fafb;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .custom-hidden {
            display: none;
        }

        .custom-visible {
            display: flex;
        }

        .farm-edit-link {
            text-decoration: none;
            color: #2563eb;
        }

        .farm-edit-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 1000px) {

            .top-controls {
                grid-template-columns: 1fr;
            }

            .top-actions {
                min-width: 0;
            }

        }

        @media (max-width: 768px) {

            .cards {
                grid-template-columns: 1fr 1fr;
            }

            .card {
                padding: 10px;
            }

            .card .value {
                font-size: 19px;
            }

            .container {
                padding: 15px;
            }

            table {
                font-size: 14px;
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

            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>

<body>

@include('layouts.farm-navigation')

<div class="header">

    <h1>🐔 Lege Poultry Farm</h1>

    <p>Reports</p>

</div>


<div class="container">


    <!-- TOP CONTROLS -->

    <div class="top-controls">


        <!-- DATE FILTER -->

        <div class="filter">

            <div class="range-buttons">

                <a
                    href="/reports?range=today"
                    class="range-button {{ $range === 'today' ? 'active' : '' }}"
                >
                    🟢 Today
                </a>


                <a
                    href="/reports?range=lifetime"
                    class="range-button {{ $range === 'lifetime' ? 'active' : '' }}"
                >
                    🔵 Lifetime
                </a>


                <button
                    type="button"
                    id="customButton"
                    class="range-button {{ $range === 'custom' ? 'active' : '' }}"
                    onclick="showCustom()"
                >
                    🟣 Custom
                </button>

            </div>


            <!-- CUSTOM DATE RANGE -->

            <form
                method="GET"
                action="/reports"
                id="customForm"
                class="custom-form {{ $range === 'custom' ? 'custom-visible' : 'custom-hidden' }}"
            >

                <input
                    type="hidden"
                    name="range"
                    value="custom"
                >


                <div class="field">

                    <label>From</label>

                    <input
                        type="date"
                        name="from"
                        value="{{ $from }}"
                        required
                    >

                </div>


                <div class="field">

                    <label>To</label>

                    <input
                        type="date"
                        name="to"
                        value="{{ $to }}"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="generate-button"
                >
                    🔍 Generate Report
                </button>

            </form>

        </div>


        <!-- QUICK ACTIONS -->

        <div class="top-actions">

            <div class="actions">

                <a href="/egg-production">
                    🥚 Production
                </a>

                <a href="/egg-sales">
                    💰 Sale
                </a>

                <a href="/expenses">
                    💸 Expense
                </a>

                <a href="/flock-records">
                    🐔 Flock
                </a>

                <a href="/">
                    🏠 Dashboard
                </a>

            </div>

        </div>

    </div>


    <!-- SECTION 1: EGG -->

    <div class="section">

        <h2>🥚 Egg Inventory</h2>

        <div class="cards">

            <div class="card produced">

                <h3>🥚 Eggs Produced</h3>

                <div class="value">
                    {{ number_format($totalProduced) }}
                </div>

            </div>


            <div class="card broken">

                <h3>💔 Broken Eggs</h3>

                <div class="value">
                    {{ number_format($totalBroken) }}
                </div>

            </div>


            <div class="card sold">

                <h3>💰 Eggs Sold</h3>

                <div class="value">
                    {{ number_format($totalSold) }}
                </div>

            </div>


            <div class="card available">

                <h3>📦 Available Eggs</h3>

                <div class="value">
                    {{ number_format($availableEggs) }}
                </div>

            </div>

        </div>

    </div>


    <!-- SECTION 2: FINANCIAL -->

    <div class="section">

        <h2>💰 Financial Summary</h2>

        <div class="cards">

            <div class="card sales">

                <h3>💵 Sales</h3>

                <div class="value">
                    {{ number_format($totalSales, 2) }} ETB
                </div>

            </div>


            <div class="card expenses">

                <h3>💸 Expenses</h3>

                <div class="value">
                    {{ number_format($totalExpenses, 2) }} ETB
                </div>

            </div>


            <div class="card
                @if ($profit > 0)
                    profit-positive
                @elseif ($profit == 0)
                    profit-zero
                @else
                    profit-negative
                @endif
            ">

                <h3>📈 Profit</h3>

                <div class="value">
                    {{ number_format($profit, 2) }} ETB
                </div>

            </div>

        </div>

    </div>


    <!-- PRODUCTION -->

    <div class="section">

        <h2>🥚 Production</h2>

        <table>

            <thead>

                <tr>
                    <th>Date</th>
                    <th>Produced</th>
                    <th>Broken</th>
                    <th>Net Eggs</th>
                </tr>

            </thead>

            <tbody>

                @forelse ($productions as $production)

                    <tr>

                        <td>
                            {{ $production->production_date }}
                        </td>

                        <td>
                            {{ number_format($production->produced) }}
                        </td>

                        <td>
                            {{ number_format($production->broken) }}
                        </td>

                        <td>
                            {{ number_format($production->produced - $production->broken) }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4">
                            No production records found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <!-- SALES -->

    <div class="section">

        <h2>💰 Sales</h2>

        <table>

            <thead>

                <tr>
                    <th>Date</th>
                    <th>To / Buyer</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>

            </thead>


            <tbody>

                @forelse ($sales as $sale)

                    <tr>

                        <td>
                            {{ $sale->sale_date }}
                        </td>

                        <td>
                            {{ $sale->name ?: '—' }}
                        </td>

                        <td>
                            {{ number_format($sale->quantity) }}
                        </td>

                        <td>
                            {{ number_format($sale->unit_price, 2) }} ETB
                        </td>

                        <td>
                            {{ number_format($sale->total_amount, 2) }} ETB
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5">
                            No sales records found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <!-- EXPENSES -->

    <div class="section">

        <h2>💸 Expenses</h2>

        <table>

            <thead>

                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>

            </thead>


            <tbody>

                @forelse ($expenses as $expense)

                    <tr>

                        <td>
                            {{ $expense->expense_date }}
                        </td>

                        <td>
                            {{ $expense->type }}
                        </td>

                        <td>
                            {{ $expense->description }}
                        </td>

                        <td>
                            {{ number_format($expense->amount, 2) }} ETB
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4">
                            No expense records found.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <!-- SECTION 3: FLOCK -->

    <div class="section">

        <h2>🐔 Flock Status</h2>

        <div class="cards">

            <div class="card available">

                <h3>🐔 Registered Birds</h3>

                <div class="value">
                    {{ number_format($registeredBirds) }}
                </div>

            </div>


            <div class="card broken">

                <h3>🤒 Sick Birds</h3>

                <div class="value">
                    {{ number_format($sickBirds) }}
                </div>

            </div>


            <div class="card produced">

                <h3>💚 Recovered</h3>

                <div class="value">
                    {{ number_format($totalRecovered) }}
                </div>

            </div>


            <div class="card broken">

                <h3>💀 Dead Birds</h3>

                <div class="value">
                    {{ number_format($totalDead) }}
                </div>

            </div>


            <div class="card available">

                <h3>🐔 Available Birds</h3>

                <div class="value">
                    {{ number_format(max(0, $availableBirds)) }}
                </div>

            </div>

        </div>

    </div>


</div>


<script>

    function showCustom() {

        const form = document.getElementById('customForm');

        form.classList.remove('custom-hidden');

        form.classList.add('custom-visible');

    }

</script>

</body>
</html>
