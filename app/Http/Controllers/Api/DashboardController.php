<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EggProduction;
use App\Models\EggSale;
use App\Models\Expense;
use App\Models\FlockRecord;
use App\Models\FarmSetting;
use App\Support\MoneyFormatter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Return dashboard data for the authenticated farm.
     */
    public function index(Request $request): JsonResponse
    {
        /*
        |--------------------------------------------------------------------------
        | Current User / Farm
        |--------------------------------------------------------------------------
        */

        $user = $request->user();

        $farmId = $user->farm_id;

        /*
        |--------------------------------------------------------------------------
        | Date Range
        |--------------------------------------------------------------------------
        */

        $range = $request->input('range', 'lifetime');

        if ($range === 'today') {

            $from = today()->format('Y-m-d');
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

            $range = 'lifetime';

            $from = null;
            $to = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Current Egg Inventory
        |--------------------------------------------------------------------------
        |
        | Inventory is always the current actual stock of the farm.
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

        $availableEggs = max(
            0,
            $totalProducedLifetime
            - $totalBrokenLifetime
            - $totalSoldLifetime
        );

        /*
        |--------------------------------------------------------------------------
        | Queries
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
        | Date Filter
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

        $profit = $totalSales - $totalExpenses;

        /*
        |--------------------------------------------------------------------------
        | Farm Settings
        |--------------------------------------------------------------------------
        */

        $farmSetting = FarmSetting::where(
            'farm_id',
            $farmId
        )->first();

        $registeredBirds = $farmSetting
            ? (int) $farmSetting->registered_birds
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Flock Status
        |--------------------------------------------------------------------------
        */

        $sickBirds = 0;
        $totalRecovered = 0;
        $totalDead = 0;

        if (class_exists(FlockRecord::class)) {

            $flockQuery = FlockRecord::where(
                'farm_id',
                $farmId
            );

            $latestFlockRecord = $flockQuery
                ->orderBy('record_date', 'desc')
                ->orderBy('id', 'desc')
                ->first();

            $sickBirds = $latestFlockRecord
                ? (int) $latestFlockRecord->sick
                : 0;

            $totalRecovered = (int) $flockQuery->sum(
                'recovered'
            );

            $totalDead = (int) $flockQuery->sum(
                'dead'
            );
        }

        $availableBirds = max(
            0,
            $registeredBirds - $totalDead
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

            'farm' => [
                'id' => $farmId,
                'name' => $user->farm?->farm_name,
            ],

            /*
            |--------------------------------------------------------------------------
            | Eggs
            |--------------------------------------------------------------------------
            */

            'eggs' => [
                'produced' => (int) $totalProduced,
                'broken' => (int) $totalBroken,
                'sold' => (int) $totalSold,
                'available' => (int) $availableEggs,
            ],

            /*
            |--------------------------------------------------------------------------
            | Financial
            |--------------------------------------------------------------------------
            |
            | Keep both raw numeric values and formatted values.
            | Raw values are useful for calculations.
            | Formatted values are ready for display.
            |
            */

            'financial' => [
                'sales' => (float) $totalSales,
                'sales_formatted' =>
                    MoneyFormatter::format($totalSales),

                'expenses' => (float) $totalExpenses,
                'expenses_formatted' =>
                    MoneyFormatter::format($totalExpenses),

                'profit' => (float) $profit,
                'profit_formatted' =>
                    MoneyFormatter::format($profit),
            ],

            /*
            |--------------------------------------------------------------------------
            | Flock
            |--------------------------------------------------------------------------
            */

            'flock' => [
                'registered' => $registeredBirds,
                'sick' => $sickBirds,
                'recovered' => $totalRecovered,
                'dead' => $totalDead,
                'available' => $availableBirds,
            ],
        ]);
    }
}
