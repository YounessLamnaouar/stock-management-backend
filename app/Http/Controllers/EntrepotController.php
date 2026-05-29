<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntrepotController extends Controller
{
    public function index()
    {
        return Entrepot::with('stocks.produit')->get();
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomEntrepot' => 'required|string|max:255',
            'adresse'     => 'nullable|string',
            'capaciteMax' => 'nullable|integer|min:0',
        ]);

        return Entrepot::create($data);
    }

    public function show(Entrepot $entrepot)
    {
        return $entrepot->load('stocks.produit');
    }

    public function edit(Entrepot $entrepot)
    {
        //
    }

    public function update(Request $request, Entrepot $entrepot)
    {
        $data = $request->validate([
            'nomEntrepot' => 'required|string|max:255',
            'adresse'     => 'nullable|string',
            'capaciteMax' => 'nullable|integer|min:0',
        ]);

        $entrepot->update($data);
        return $entrepot;
    }

    public function destroy(Entrepot $entrepot)
    {
        $entrepot->delete();
        return response()->json(['message' => 'Entrepôt supprimé']);
    }
}
