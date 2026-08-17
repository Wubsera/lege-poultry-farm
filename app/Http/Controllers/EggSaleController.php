<?php

namespace App\Http\Controllers;

use App\Models\EggProduction;
use App\Models\EggSale;
use Illuminate\Http\Request;

class EggSaleController extends Controller
{
    public function create()
    {
        $farmId = auth()->user()->farm_id;

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
            $totalProduced - $totalBroken - $totalSold
        );

        return view(
            'egg-sales.create',
            compact('availableEggs')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
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

        $farmId = auth()->user()->farm_id;

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
            $totalProduced - $totalBroken - $totalSold
        );

        if ($request->quantity > $availableEggs) {

            return back()
                ->withInput()
                ->withErrors([
                    'quantity' =>
                        "Insufficient eggs. Available stock: {$availableEggs}.",
                ]);
        }

        $totalAmount =
            $request->quantity * $request->unit_price;

        EggSale::create([
            'farm_id' => $farmId,
            'sale_date' => $request->sale_date,
            'name' => $request->name,
            'quantity' => $request->quantity,
            'unit_price' => $request->unit_price,
            'total_amount' => $totalAmount,
        ]);

        return redirect('/dashboard')
            ->with(
                'success',
                'Egg sale saved successfully!'
            );
    }
}
