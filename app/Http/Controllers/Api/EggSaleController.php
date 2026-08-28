<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EggProduction;
use App\Models\EggSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EggSaleController extends Controller
{
    /**
     * List egg sales for the authenticated farm.
     */
    public function index(Request $request): JsonResponse
    {
        $farmId = $request->user()->farm_id;

        $sales = EggSale::where(
            'farm_id',
            $farmId
        )
            ->orderByDesc('sale_date')
            ->orderByDesc('id')
            ->get([
                'id',
                'farm_id',
                'sale_date',
                'name',
                'quantity',
                'unit_price',
                'total_amount',
                'created_at',
                'updated_at',
            ]);

        return response()->json([
            'success' => true,
            'data' => $sales,
        ]);
    }

    /**
     * Create an egg sale for the authenticated farm.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sale_date' => [
                'required',
                'date',
            ],

            'name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        $farmId = $request->user()->farm_id;

        /*
        |--------------------------------------------------------------------------
        | Calculate Available Eggs
        |--------------------------------------------------------------------------
        */

        $totalProduced = EggProduction::where(
            'farm_id',
            $farmId
        )->sum('produced');

        $totalBroken = EggProduction::where(
            'farm_id',
            $farmId
        )->sum('broken');

        $totalSold = EggSale::where(
            'farm_id',
            $farmId
        )->sum('quantity');

        $availableEggs = max(
            0,
            $totalProduced
            - $totalBroken
            - $totalSold
        );

        /*
        |--------------------------------------------------------------------------
        | Stock Validation
        |--------------------------------------------------------------------------
        */

        if ($validated['quantity'] > $availableEggs) {

            return response()->json([
                'success' => false,
                'message' => "Insufficient eggs. Available stock: {$availableEggs}.",
                'available_eggs' => $availableEggs,
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Total Amount
        |--------------------------------------------------------------------------
        */

        $totalAmount =
            $validated['quantity']
            * $validated['unit_price'];

        /*
        |--------------------------------------------------------------------------
        | Save Sale
        |--------------------------------------------------------------------------
        */

        $sale = EggSale::create([
            'farm_id' => $farmId,
            'sale_date' => $validated['sale_date'],
            'name' => $validated['name'] ?? null,
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'],
            'total_amount' => $totalAmount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Egg sale saved successfully.',
            'data' => $sale,
            'available_eggs' => $availableEggs - $validated['quantity'],
        ], 201);
    }
}
