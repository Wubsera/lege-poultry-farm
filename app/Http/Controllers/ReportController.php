<?php

namespace App\Http\Controllers;

use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\Expense;
use App\Models\FlockRecord;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Current Farm
        |--------------------------------------------------------------------------
        */

        $farmId = auth()->user()->farm_id;

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
        | Production - Selected Report Range
        |--------------------------------------------------------------------------
        */

        $productionQuery = EggProduction::where(
            'farm_id',
            $farmId
        );

        if ($from && $to) {

            $productionQuery->whereDate(
                'production_date',
                '>=',
                $from
            );

            $productionQuery->whereDate(
                'production_date',
                '<=',
                $to
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
        | Sales - Selected Report Range
        |--------------------------------------------------------------------------
        */

        $salesQuery = EggSale::where(
            'farm_id',
            $farmId
        );

        if ($from && $to) {

            $salesQuery->whereDate(
                'sale_date',
                '>=',
                $from
            );

            $salesQuery->whereDate(
                'sale_date',
                '<=',
                $to
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
        | Expenses - Selected Report Range
        |--------------------------------------------------------------------------
        */

        $expensesQuery = Expense::where(
            'farm_id',
            $farmId
        );

        if ($from && $to) {

            $expensesQuery->whereDate(
                'expense_date',
                '>=',
                $from
            );

            $expensesQuery->whereDate(
                'expense_date',
                '<=',
                $to
            );
        }

        $expenses = $expensesQuery
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Summary - Selected Range
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
        | Available Eggs - Current Inventory
        |--------------------------------------------------------------------------
        |
        | Inventory is independent of the selected report period.
        |
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
        | Farm Information
        |--------------------------------------------------------------------------
        */

        $farm = auth()->user()->farm;

        $farmSettings = $farm?->setting;

        $registeredBirds = $farmSettings?->registered_birds ?? 0;


        /*
        |--------------------------------------------------------------------------
        | Flock Records - Selected Report Range
        |--------------------------------------------------------------------------
        */

        $flockQuery = FlockRecord::where(
            'farm_id',
            $farmId
        );

        if ($from && $to) {

            $flockQuery->whereDate(
                'record_date',
                '>=',
                $from
            );

            $flockQuery->whereDate(
                'record_date',
                '<=',
                $to
            );
        }

        $flockRecords = $flockQuery
            ->orderBy('record_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Sick Birds
        |--------------------------------------------------------------------------
        */

        $latestFlockRecord = $flockRecords
            ->sortByDesc('record_date')
            ->sortByDesc('id')
            ->first();

        $sickBirds = $latestFlockRecord
            ? $latestFlockRecord->sick
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Recovered Birds
        |--------------------------------------------------------------------------
        */

        $totalRecovered = $flockRecords->sum('recovered');


        /*
        |--------------------------------------------------------------------------
        | Dead Birds
        |--------------------------------------------------------------------------
        */

        $totalDead = $flockRecords->sum('dead');


        /*
        |--------------------------------------------------------------------------
        | Available Birds
        |--------------------------------------------------------------------------
        |
        | Only deaths reduce the flock.
        |
        */

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

        return view('reports.index', [

            'range' => $range,

            'from' => $from,
            'to' => $to,

            'productions' => $productions,
            'sales' => $sales,
            'expenses' => $expenses,

            'totalProduced' => $totalProduced,
            'totalBroken' => $totalBroken,
            'totalSold' => $totalSold,

            'totalSales' => $totalSales,
            'totalExpenses' => $totalExpenses,

            'profit' => $profit,

            'availableEggs' => $availableEggs,

            'farm' => $farmSettings,

            'registeredBirds' => $registeredBirds,
            'sickBirds' => $sickBirds,
            'totalRecovered' => $totalRecovered,
            'totalDead' => $totalDead,
            'availableBirds' => $availableBirds,
        ]);
    }
}
