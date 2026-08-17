<?php

namespace App\Http\Controllers;

use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\Expense;
use Illuminate\Http\Request;

class DashboardController extends Controller
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

        $range = $request->input('range', 'lifetime');


        /*
        |--------------------------------------------------------------------------
        | Lifetime
        |--------------------------------------------------------------------------
        */

        if ($range === 'lifetime') {

            $from = null;
            $to = null;

        }


        /*
        |--------------------------------------------------------------------------
        | Today
        |--------------------------------------------------------------------------
        */

        elseif ($range === 'today') {

            $from = today()->format('Y-m-d');
            $to = today()->format('Y-m-d');

        }


        /*
        |--------------------------------------------------------------------------
        | Custom Date Range
        |--------------------------------------------------------------------------
        */

        else {

            $range = 'custom';

            $from = $request->input(
                'from',
                today()->format('Y-m-d')
            );

            $to = $request->input(
                'to',
                today()->format('Y-m-d')
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Current Inventory
        |--------------------------------------------------------------------------
        |
        | Inventory always represents the current actual stock
        | of the logged-in farm.
        |
        */

        $totalProducedLifetime = EggProduction::where(
            'farm_id',
            $farmId
        )->sum('produced');

        $totalBrokenLifetime = EggProduction::where(
            'farm_id',
            $farmId
        )->sum('broken');

        $totalSoldLifetime = EggSale::where(
            'farm_id',
            $farmId
        )->sum('quantity');

        $inventory = max(
            0,
            $totalProducedLifetime
            - $totalBrokenLifetime
            - $totalSoldLifetime
        );


        /*
        |--------------------------------------------------------------------------
        | Dashboard Summary
        |--------------------------------------------------------------------------
        */

        $productionQuery = EggProduction::where(
            'farm_id',
            $farmId
        );

        $salesQuery = EggSale::where(
            'farm_id',
            $farmId
        );

        $expenseQuery = Expense::where(
            'farm_id',
            $farmId
        );


        /*
        |--------------------------------------------------------------------------
        | Apply Selected Date Range
        |--------------------------------------------------------------------------
        */

        if ($from && $to) {

            $productionQuery->whereBetween(
                'production_date',
                [$from, $to]
            );

            $salesQuery->whereBetween(
                'sale_date',
                [$from, $to]
            );

            $expenseQuery->whereBetween(
                'expense_date',
                [$from, $to]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Totals
        |--------------------------------------------------------------------------
        */

        $totalProduced = $productionQuery->sum('produced');

        $totalBroken = $productionQuery->sum('broken');

        $totalSold = $salesQuery->sum('quantity');

        $totalSales = $salesQuery->sum('total_amount');

        $totalExpenses = $expenseQuery->sum('amount');


        /*
        |--------------------------------------------------------------------------
        | Profit
        |--------------------------------------------------------------------------
        */

        $profit = $totalSales - $totalExpenses;


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view('dashboard', [

            'totalProduced' => $totalProduced,

            'totalBroken' => $totalBroken,

            'totalSold' => $totalSold,

            'totalSales' => $totalSales,

            'totalExpenses' => $totalExpenses,

            'inventory' => $inventory,

            'profit' => $profit,

            'range' => $range,

            'from' => $from,

            'to' => $to,
        ]);
    }
}
