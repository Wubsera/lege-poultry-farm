<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $request->validate([
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
                'min:0',
            ],
        ]);

        Expense::create([
            'farm_id' => auth()->user()->farm_id,
            'expense_date' => $request->expense_date,
            'type' => $request->type,
            'description' => $request->description,
            'amount' => $request->amount,
        ]);

        return redirect('/dashboard')
            ->with('success', 'Expense saved successfully!');
    }
}
