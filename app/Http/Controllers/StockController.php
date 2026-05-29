<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        return Stock::with('produit.categorie', 'entrepot')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'quantite'      => 'required|integer|min:0',
            'dateMiseAJour' => 'nullable|date',
            'produit_id'    => 'required|exists:produits,id',
            'entrepot_id'   => 'required|exists:entrepots,id',
        ]);

        $data['dateMiseAJour'] = $data['dateMiseAJour'] ?? now()->toDateString();

        $entrepot   = Entrepot::findOrFail($data['entrepot_id']);
        $totalStock = Stock::where('entrepot_id', $data['entrepot_id'])->sum('quantite');

        if ($entrepot->capaciteMax !== null && ($totalStock + $data['quantite']) > $entrepot->capaciteMax) {
            return response()->json(['message' => "Cet entrepôt est saturé (capacité max: {$entrepot->capaciteMax})."], 422);
        }

        $stock = Stock::create($data);

        return response()->json($stock->load('produit', 'entrepot'), 201);
    }

    public function show(Stock $stock)
    {
        return $stock->load('produit.categorie', 'entrepot');
    }

    public function update(Request $request, Stock $stock)
    {
        $data = $request->validate([
            'quantite'      => 'sometimes|integer|min:0',
            'dateMiseAJour' => 'nullable|date',
            'produit_id'    => 'sometimes|exists:produits,id',
            'entrepot_id'   => 'sometimes|exists:entrepots,id',
        ]);

        $data['dateMiseAJour'] = now()->toDateString();

        if (isset($data['quantite'])) {
            $entrepotId = $data['entrepot_id'] ?? $stock->entrepot_id;
            $entrepot   = Entrepot::findOrFail($entrepotId);
            $otherStock = Stock::where('entrepot_id', $entrepotId)
                ->where('id', '!=', $stock->id)
                ->sum('quantite');

            if ($entrepot->capaciteMax !== null && ($otherStock + $data['quantite']) > $entrepot->capaciteMax) {
                return response()->json(['message' => "Cet entrepôt est saturé (capacité max: {$entrepot->capaciteMax})."], 422);
            }
        }

        DB::table('stock_user_context')->update(['user_id' => auth()->id()]);

        $stock->update($data);

        return response()->json($stock->load('produit', 'entrepot'));
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return response()->json(['message' => 'Stock supprimé']);
    }
}
