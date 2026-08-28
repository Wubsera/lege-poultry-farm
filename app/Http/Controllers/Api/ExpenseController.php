<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * List expenses for the authenticated farm.
     */
    public function index(Request $request): JsonResponse
    {
        $farmId = $request->user()->farm_id;

        $expenses = Expense::where(
            'farm_id',
            $farmId
        )
            ->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->get([
                'id',
                'farm_id',
                'expense_date',
                'type',
                'description',
                'amount',
                'created_at',
                'updated_at',
            ]);

        return response()->json([
            'success' => true,
            'data' => $expenses,
        ]);
    }

    /**
     * Create an expense for the authenticated farm.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'expense_date' => [
                'required',
                'date',
            ],

            'type' => [
                'required',
                'string',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ]);

        $expense = Expense::create([
            'farm_id' => $request->user()->farm_id,
            'expense_date' => $validated['expense_date'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'amount' => $validated['amount'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense saved successfully.',
            'data' => $expense,
        ], 201);
    }
}
