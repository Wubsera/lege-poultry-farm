<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EggProduction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EggProductionController extends Controller
{
    /**
     * List egg production records for the authenticated farm.
     */
    public function index(Request $request): JsonResponse
    {
        $farmId = $request->user()->farm_id;

        $productions = EggProduction::where(
            'farm_id',
            $farmId
        )
            ->orderByDesc('production_date')
            ->orderByDesc('id')
            ->get([
                'id',
                'farm_id',
                'production_date',
                'produced',
                'broken',
                'created_at',
                'updated_at',
            ]);

        return response()->json([
            'success' => true,
            'data' => $productions,
        ]);
    }

    /**
     * Store a new egg production record.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'production_date' => [
                'required',
                'date',
            ],

            'produced' => [
                'required',
                'integer',
                'min:0',
            ],

            'broken' => [
                'required',
                'integer',
                'min:0',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Validate Broken Eggs
        |--------------------------------------------------------------------------
        */

        if ($validated['broken'] > $validated['produced']) {
            return response()->json([
                'success' => false,
                'message' => 'Broken eggs cannot be greater than produced eggs.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Record
        |--------------------------------------------------------------------------
        */

        $production = EggProduction::create([
            'farm_id' => $request->user()->farm_id,
            'production_date' => $validated['production_date'],
            'produced' => $validated['produced'],
            'broken' => $validated['broken'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Egg production saved successfully.',
            'data' => $production,
        ], 201);
    }
}
