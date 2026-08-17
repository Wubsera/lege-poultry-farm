<?php

namespace App\Http\Controllers;

use App\Models\EggProduction;
use Illuminate\Http\Request;

class EggProductionController extends Controller
{
    public function create()
    {
        return view('egg-production.create');
    }

    public function store(Request $request)
    {
        $request->validate([
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

        $farmId = auth()->user()->farm_id;

        EggProduction::create([
            'farm_id' => $farmId,
            'production_date' => $request->production_date,
            'produced' => $request->produced,
            'broken' => $request->broken,
        ]);

        return redirect('/dashboard')
            ->with(
                'success',
                'Egg production saved successfully!'
            );
    }
}
