<?php

namespace App\Http\Controllers;

use App\Models\AlertStock;
use Illuminate\Http\Request;

class AlertStockController extends Controller
{
    public function index()
    {
        return AlertStock::with('produit', 'stock.entrepot', 'niveau', 'statut')
            ->orderBy('dateAlerte', 'desc')
            ->get();
    }

    public function show(AlertStock $alertStock)
    {
        return $alertStock->load('produit', 'stock.entrepot', 'niveau', 'statut');
    }

    // Only statut can be updated (clôturer an alert)
    public function update(Request $request, AlertStock $alertStock)
    {
        $data = $request->validate([
            'statut_id' => 'required|exists:statuts,id',
        ]);

        $alertStock->update($data);

        return response()->json($alertStock->load('produit', 'stock.entrepot', 'niveau', 'statut'));
    }

    public function destroy(AlertStock $alertStock)
    {
        $alertStock->delete();
        return response()->json(['message' => 'Alerte supprimée']);
    }
}
