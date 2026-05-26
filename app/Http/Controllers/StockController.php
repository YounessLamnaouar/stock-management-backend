<?php

namespace App\Http\Controllers;

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
            'quantite'     => 'required|integer|min:0',
            'dateMiseAJour' => 'nullable|date',
            'produit_id'   => 'required|exists:produits,id',
            'entrepot_id'  => 'required|exists:entrepots,id',
        ]);

        $data['dateMiseAJour'] = $data['dateMiseAJour'] ?? now()->toDateString();

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
            'quantite'     => 'sometimes|integer|min:0',
            'dateMiseAJour' => 'nullable|date',
            'produit_id'   => 'sometimes|exists:produits,id',
            'entrepot_id'  => 'sometimes|exists:entrepots,id',
        ]);

        $data['dateMiseAJour'] = now()->toDateString();

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
