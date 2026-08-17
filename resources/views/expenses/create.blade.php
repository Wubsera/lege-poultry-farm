<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lege Poultry Farm - Expenses</title>

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

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-size: 16px;
            background: white;
        }

        input:focus,
        select:focus {
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

        .hint {
            margin-top: 5px;
            font-size: 13px;
            color: #6b7280;
        }

        .amount-box {
            background: #fef3c7;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 18px;
            margin-top: 5px;
            margin-bottom: 25px;
        }

        .amount-label {
            color: #92400e;
            font-size: 14px;
            font-weight: bold;
        }

        .amount-value {
            color: #78350f;
            font-size: 28px;
            font-weight: bold;
            margin-top: 5px;
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
            background: #f59e0b;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .save-button:hover {
            background: #d97706;
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
</head>

<body>
@include('layouts.farm-navigation')
<div class="header">

    <h1>🐔 Lege Poultry Farm</h1>

    <p>Farm Expenses</p>

</div>


<div class="container">

    <div class="card">

        <h2>💸 Record Farm Expense</h2>


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


        <form method="POST" action="/expenses">

            @csrf


            <!-- DATE -->

            <div class="field">

                <label for="expense_date">
                    📅 Expense Date
                </label>

                <input
                    type="date"
                    id="expense_date"
                    name="expense_date"
                    value="{{ old('expense_date', today()->format('Y-m-d')) }}"
                    required
                >

            </div>


            <!-- TYPE -->

            <div class="field">

                <label for="type">
                    🏷️ Expense Type
                </label>

                <select
                    id="type"
                    name="type"
                    required
                >

                    <option value="Meal"
                        {{ old('type') == 'Meal' ? 'selected' : '' }}>
                        🍚 Meal / Feed
                    </option>

                    <option value="Water"
                        {{ old('type') == 'Water' ? 'selected' : '' }}>
                        💧 Water
                    </option>

                    <option value="Medication"
                        {{ old('type') == 'Medication' ? 'selected' : '' }}>
                        💊 Medication
                    </option>

                    <option value="Salary"
                        {{ old('type') == 'Salary' ? 'selected' : '' }}>
                        👷 Salary
                    </option>

                    <option value="Other"
                        {{ old('type') == 'Other' ? 'selected' : '' }}>
                        📦 Other
                    </option>

                </select>

            </div>


            <!-- DESCRIPTION -->

            <div class="field">

                <label for="description">
                    📝 Description
                </label>

                <input
                    type="text"
                    id="description"
                    name="description"
                    value="{{ old('description') }}"
                    placeholder="e.g. 20 bags of chicken feed"
                >

                <div class="hint">
                    Add a short description to help identify the expense later.
                </div>

            </div>


            <!-- AMOUNT -->

            <div class="field">

                <label for="amount">
                    💵 Amount (ETB)
                </label>

                <input
                    type="number"
                    id="amount"
                    name="amount"
                    value="{{ old('amount') }}"
                    min="0"
                    step="0.01"
                    placeholder="0.00"
                    required
                >

            </div>


            <!-- AMOUNT PREVIEW -->

            <div class="amount-box">

                <div class="amount-label">
                    💸 Expense Amount
                </div>

                <div class="amount-value" id="amountPreview">
                    0.00 ETB
                </div>

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <a href="/" class="back-button">
                    🏠 Dashboard
                </a>

                <button type="submit" class="save-button">
                    💾 SAVE EXPENSE
                </button>

            </div>


        </form>

    </div>

</div>


<script>

    const amountInput = document.getElementById('amount');
    const amountPreview = document.getElementById('amountPreview');

    function updateAmount() {

        const amount = parseFloat(amountInput.value) || 0;

        amountPreview.textContent =
            amount.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' ETB';
    }

    amountInput.addEventListener('input', updateAmount);

    updateAmount();

</script>

</body>
</html>
```
