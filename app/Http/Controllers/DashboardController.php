<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use App\Models\MovementStock;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalProduits   = Produit::count();
        $totalEntrepots  = Entrepot::count();
        $totalMouvements = MovementStock::count();
        $ruptures        = Stock::where('quantite', 0)->count();

        // Stock by warehouse for bar chart
        $stockParEntrepot = Entrepot::withSum('stocks', 'quantite')->get()
            ->map(fn($e) => [
                'name'     => $e->nomEntrepot,
                'stock'    => (int) $e->stocks_sum_quantite,
                'capacite' => $e->capacite ?? 0,
            ]);

        // Products by category for pie chart
        $produitsParCategorie = Produit::select('categorie_id', DB::raw('count(*) as total'))
            ->with('categorie:id,nomCategorie')
            ->groupBy('categorie_id')
            ->get()
            ->map(fn($p) => [
                'name'  => $p->categorie->nomCategorie ?? 'Sans catégorie',
                'value' => $p->total,
            ]);

        // Recent movements
        $recentMouvements = MovementStock::with('produit', 'user', 'statusMouvement', 'entrepotSource', 'entrepotDestination')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'totalProduits'        => $totalProduits,
            'totalEntrepots'       => $totalEntrepots,
            'totalMouvements'      => $totalMouvements,
            'ruptures'             => $ruptures,
            'stockParEntrepot'     => $stockParEntrepot,
            'produitsParCategorie' => $produitsParCategorie,
            'recentMouvements'     => $recentMouvements,
        ]);
    }
}
