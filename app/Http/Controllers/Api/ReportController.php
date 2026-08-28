<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\Expense;
use App\Models\FlockRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Support\MoneyFormatter;

class ReportController extends Controller
{
    /**
     * Return farm reports for the authenticated farm.
     */
    public function index(Request $request): JsonResponse
    {
        $farmId = $request->user()->farm_id;

        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        $range = $request->input('range', 'today');

        if ($range === 'lifetime') {

            $from = null;
            $to = today()->format('Y-m-d');

        } elseif ($range === 'custom') {

            $from = $request->input(
                'from',
                today()->format('Y-m-d')
            );

            $to = $request->input(
                'to',
                today()->format('Y-m-d')
            );

        } else {

            $range = 'today';

            $from = today()->format('Y-m-d');
            $to = today()->format('Y-m-d');
        }

        /*
        |--------------------------------------------------------------------------
        | Egg Production
        |--------------------------------------------------------------------------
        */

        $productionQuery = EggProduction::where(
            'farm_id',
            $farmId
        );

        if ($from && $to) {
            $productionQuery->whereBetween(
                'production_date',
                [$from, $to]
            );
        }

        $productions = $productionQuery
            ->selectRaw('
                production_date,
                SUM(produced) as produced,
                SUM(broken) as broken
            ')
            ->groupBy('production_date')
            ->orderBy('production_date', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Egg Sales
        |--------------------------------------------------------------------------
        */

        $salesQuery = EggSale::where(
            'farm_id',
            $farmId
        );

        if ($from && $to) {
            $salesQuery->whereBetween(
                'sale_date',
                [$from, $to]
            );
        }

        $sales = $salesQuery
            ->select([
                'id',
                'sale_date',
                'name',
                'quantity',
                'unit_price',
                'total_amount',
            ])
            ->orderBy('sale_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Expenses
        |--------------------------------------------------------------------------
        */

        $expensesQuery = Expense::where(
            'farm_id',
            $farmId
        );

        if ($from && $to) {
            $expensesQuery->whereBetween(
                'expense_date',
                [$from, $to]
            );
        }

        $expenses = $expensesQuery
            ->orderBy('expense_date', 'desc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Summary
        |--------------------------------------------------------------------------
        */

        $totalProduced = $productions->sum('produced');

        $totalBroken = $productions->sum('broken');

        $totalSold = $sales->sum('quantity');

        $totalSales = $sales->sum('total_amount');

        $totalExpenses = $expenses->sum('amount');

        $profit = $totalSales - $totalExpenses;

        /*
        |--------------------------------------------------------------------------
        | Current Egg Inventory
        |--------------------------------------------------------------------------
        */

        $allProduced = EggProduction::where(
            'farm_id',
            $farmId
        )->sum('produced');

        $allBroken = EggProduction::where(
            'farm_id',
            $farmId
        )->sum('broken');

        $allSold = EggSale::where(
            'farm_id',
            $farmId
        )->sum('quantity');

        $availableEggs = max(
            0,
            $allProduced - $allBroken - $allSold
        );

        /*
        |--------------------------------------------------------------------------
        | Farm
        |--------------------------------------------------------------------------
        */

        $farm = $request->user()->farm;

        $farmSettings = $farm?->setting;

        $registeredBirds = (int) (
            $farmSettings?->registered_birds ?? 0
        );

        /*
        |--------------------------------------------------------------------------
        | Flock
        |--------------------------------------------------------------------------
        */

        $flockQuery = FlockRecord::where(
            'farm_id',
            $farmId
        );

        if ($from && $to) {
            $flockQuery->whereBetween(
                'record_date',
                [$from, $to]
            );
        }

        $flockRecords = $flockQuery
            ->orderBy('record_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $latestFlockRecord = $flockRecords
            ->sortByDesc('record_date')
            ->sortByDesc('id')
            ->first();

        $sickBirds = $latestFlockRecord
            ? (int) $latestFlockRecord->sick
            : 0;

        $totalRecovered = (int) $flockRecords->sum(
            'recovered'
        );

        $totalDead = (int) $flockRecords->sum(
            'dead'
        );

        $deathsUntilDate = FlockRecord::where(
            'farm_id',
            $farmId
        )
            ->when(
                $to,
                fn ($query) => $query->whereDate(
                    'record_date',
                    '<=',
                    $to
                )
            )
            ->sum('dead');

        $availableBirds = max(
            0,
            $registeredBirds - $deathsUntilDate
        );

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'success' => true,

            'range' => $range,

            'from' => $from,

            'to' => $to,

            /*
            |--------------------------------------------------------------------------
            | Summary
            |--------------------------------------------------------------------------
            */

            'summary' => [

                'total_produced' => (int) $totalProduced,

                'total_broken' => (int) $totalBroken,

                'total_sold' => (int) $totalSold,

                'total_sales' => (float) $totalSales,

                'total_sales_formatted' =>
                    MoneyFormatter::format($totalSales),

                'total_expenses' => (float) $totalExpenses,

                'total_expenses_formatted' =>
                    MoneyFormatter::format($totalExpenses),

                'profit' => (float) $profit,

                'profit_formatted' =>
                    MoneyFormatter::format($profit),

                'available_eggs' => (int) $availableEggs,
            ],

            /*
            |--------------------------------------------------------------------------
            | Flock
            |--------------------------------------------------------------------------
            */

            'flock' => [

                'registered_birds' => $registeredBirds,

                'sick_birds' => $sickBirds,

                'recovered_birds' => $totalRecovered,

                'dead_birds' => $totalDead,

                'available_birds' => $availableBirds,
            ],

            /*
            |--------------------------------------------------------------------------
            | Productions
            |--------------------------------------------------------------------------
            */

            'productions' => $productions,

            /*
            |--------------------------------------------------------------------------
            | Sales
            |--------------------------------------------------------------------------
            */

            'sales' => $sales->map(
                function ($sale) {

                    return [

                        'id' => $sale->id,

                        'sale_date' => $sale->sale_date,

                        'name' => $sale->name,

                        'quantity' => (int) $sale->quantity,

                        'unit_price' => (float) $sale->unit_price,

                        'unit_price_formatted' =>
                            MoneyFormatter::format(
                                $sale->unit_price
                            ),

                        'total_amount' =>
                            (float) $sale->total_amount,

                        'total_amount_formatted' =>
                            MoneyFormatter::format(
                                $sale->total_amount
                            ),
                    ];
                }
            ),

            /*
            |--------------------------------------------------------------------------
            | Expenses
            |--------------------------------------------------------------------------
            */

            'expenses' => $expenses->map(
                function ($expense) {

                    return [

                        'id' => $expense->id,

                        'expense_date' =>
                            $expense->expense_date,

                        'type' =>
                            $expense->type,

                        'description' =>
                            $expense->description,

                        'amount' =>
                            (float) $expense->amount,

                        'amount_formatted' =>
                            MoneyFormatter::format(
                                $expense->amount
                            ),
                    ];
                }
            ),
        ]);
    }
}
