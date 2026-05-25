<?php

namespace App\Http\Controllers;

use App\Models\AlertStock;
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
            'quantiteDisponible' => 'required|integer|min:0',
            'seuilMin'           => 'nullable|integer|min:0',
            'dateDerniereMaj'    => 'nullable|date',
            'produit_id'         => 'required|exists:produits,id',
            'entrepot_id'        => 'required|exists:entrepots,id',
        ]);

        $data['dateDerniereMaj'] = $data['dateDerniereMaj'] ?? now()->toDateString();

        $stock = Stock::create($data);

        $this->checkAndCreateAlert($stock);

        return response()->json($stock->load('produit', 'entrepot'), 201);
    }

    public function show(Stock $stock)
    {
        return $stock->load('produit.categorie', 'entrepot');
    }

    public function update(Request $request, Stock $stock)
    {
        $data = $request->validate([
            'quantiteDisponible' => 'sometimes|integer|min:0',
            'seuilMin'           => 'nullable|integer|min:0',
            'dateDerniereMaj'    => 'nullable|date',
            'produit_id'         => 'sometimes|exists:produits,id',
            'entrepot_id'        => 'sometimes|exists:entrepots,id',
        ]);

        $data['dateDerniereMaj'] = now()->toDateString();

        // Set user context so the DB trigger can record who made the change
        DB::table('stock_user_context')->update(['user_id' => auth()->id()]);

        $stock->update($data);

        $this->checkAndCreateAlert($stock->fresh());

        return response()->json($stock->load('produit', 'entrepot'));
    }

    public function destroy(Stock $stock)
    {
        $stock->delete();
        return response()->json(['message' => 'Stock supprimé']);
    }

    private function checkAndCreateAlert(Stock $stock): void
    {
        if ($stock->seuilMin <= 0) {
            return;
        }

        if ($stock->quantiteDisponible > $stock->seuilMin) {
            return;
        }

        // Don't create a duplicate active alert
        $exists = AlertStock::where('stock_id', $stock->id)
            ->where('statut_id', 1) // Active
            ->exists();

        if ($exists) {
            return;
        }

        if ($stock->quantiteDisponible === 0) {
            $niveauId = 1; // Élevé
        } elseif ($stock->quantiteDisponible <= $stock->seuilMin * 0.5) {
            $niveauId = 2; // Moyen
        } else {
            $niveauId = 3; // Faible
        }

        AlertStock::create([
            'dateAlerte' => now()->toDateString(),
            'message'    => 'Stock faible : ' . ($stock->produit->nomProduit ?? 'produit') . ' dans ' . ($stock->entrepot->nomEntrepot ?? 'entrepôt'),
            'stock_id'   => $stock->id,
            'niveau_id'  => $niveauId,
            'statut_id'  => 1, // Active
            'produit_id' => $stock->produit_id,
        ]);
    }
}
