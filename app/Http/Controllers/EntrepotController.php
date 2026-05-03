<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntrepotController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Entrepot::with('stocks.produit')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nomEntrepot' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string',
            'capacite' => 'nullable|integer',
        ]);

        return Entrepot::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Entrepot $entrepot)
    {
        return $entrepot->load('stocks.produit');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entrepot $entrepot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Entrepot $entrepot)
    {
        $data = $request->validate([
            'nomEntrepot' => 'required|string|max:255',
            'adresse' => 'nullable|string',
            'ville' => 'nullable|string',
            'capacite' => 'nullable|integer',
        ]);

        $entrepot->update($data);
        return $entrepot;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entrepot $entrepot)
    {
        $entrepot->delete();
        return response()->json(['message' => 'Entrepôt supprimé']);
    }
}
