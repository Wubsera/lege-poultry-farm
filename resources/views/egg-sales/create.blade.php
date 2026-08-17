<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lege Poultry Farm - Egg Sales</title>

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

        .stock-card {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 25px;
        }

        .stock-label {
            color: #166534;
            font-size: 14px;
            font-weight: bold;
        }

        .stock-value {
            color: #166534;
            font-size: 30px;
            font-weight: bold;
            margin-top: 5px;
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

        .hint {
            margin-top: 5px;
            font-size: 13px;
            color: #6b7280;
        }

        .total-box {
            background: #dbeafe;
            border-radius: 10px;
            padding: 18px;
            margin-top: 10px;
            margin-bottom: 25px;
        }

        .total-label {
            color: #1e40af;
            font-size: 14px;
            font-weight: bold;
        }

        .total-value {
            color: #1e3a8a;
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

    <p>Egg Sales</p>

</div>


<div class="container">

    <div class="card">

        <h2>💰 Record Egg Sale</h2>


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


        <!-- AVAILABLE STOCK -->

        <div class="stock-card">

            <div class="stock-label">
                📦 Available Eggs
            </div>

            <div class="stock-value">
                {{ number_format($availableEggs) }}
            </div>

        </div>


        <form method="POST" action="/egg-sales">

            @csrf


            <!-- DATE -->

            <div class="field">

                <label for="sale_date">
                    📅 Sale Date
                </label>

                <input
                    type="date"
                    id="sale_date"
                    name="sale_date"
                    value="{{ old('sale_date', today()->format('Y-m-d')) }}"
                    required
                >

            </div>


            <!-- BUYER -->

            <div class="field">

                <label for="name">
                    👤 To / Buyer
                    <span style="font-weight: normal; color: #6b7280;">
                        (Optional)
                    </span>
                </label>

                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Customer name"
                    maxlength="255"
                >

            </div>


            <!-- QUANTITY -->

            <div class="field">

                <label for="quantity">
                    🥚 Quantity
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    value="{{ old('quantity') }}"
                    min="1"
                    required
                >

                <div class="hint">
                    Maximum available: {{ number_format($availableEggs) }} eggs
                </div>

            </div>


            <!-- UNIT PRICE -->

            <div class="field">

                <label for="unit_price">
                    💵 Unit Price (ETB)
                </label>

                <input
                    type="number"
                    id="unit_price"
                    name="unit_price"
                    value="{{ old('unit_price') }}"
                    min="0"
                    step="0.01"
                    required
                >

            </div>


            <!-- TOTAL -->

            <div class="total-box">

                <div class="total-label">
                    💰 Total Sale Amount
                </div>

                <div class="total-value" id="totalAmount">
                    0.00 ETB
                </div>

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <a href="/" class="back-button">
                    🏠 Dashboard
                </a>

                <button type="submit" class="save-button">
                    💾 SAVE SALE
                </button>

            </div>


        </form>

    </div>

</div>


<script>

    const quantityInput = document.getElementById('quantity');
    const unitPriceInput = document.getElementById('unit_price');
    const totalAmount = document.getElementById('totalAmount');

    function calculateTotal() {

        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;

        const total = quantity * unitPrice;

        totalAmount.textContent =
            total.toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' ETB';
    }

    quantityInput.addEventListener('input', calculateTotal);
    unitPriceInput.addEventListener('input', calculateTotal);

    calculateTotal();

</script>

</body>
</html>
```
