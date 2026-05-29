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

        $stockParEntrepot = Entrepot::withSum('stocks', 'quantite')->get()
            ->map(fn($e) => [
                'name'        => $e->nomEntrepot,
                'stock'       => (int) $e->stocks_sum_quantite,
                'capaciteMax' => $e->capaciteMax ?? 0,
            ]);

        $produitsParCategorie = Produit::select('categorie_id', DB::raw('count(*) as total'))
            ->with('categorie:id,nomCategorie')
            ->groupBy('categorie_id')
            ->get()
            ->map(fn($p) => [
                'name'  => $p->categorie->nomCategorie ?? 'Sans catégorie',
                'value' => $p->total,
            ]);

        $recentMouvements = MovementStock::with('produit', 'user', 'statusMouvement', 'entrepotSource', 'entrepotDestination')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $mouvementsParJour = MovementStock::selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($m) => ['date' => $m->date, 'total' => (int) $m->total]);

        return response()->json([
            'totalProduits'        => $totalProduits,
            'totalEntrepots'       => $totalEntrepots,
            'totalMouvements'      => $totalMouvements,
            'ruptures'             => $ruptures,
            'stockParEntrepot'     => $stockParEntrepot,
            'produitsParCategorie' => $produitsParCategorie,
            'recentMouvements'     => $recentMouvements,
            'mouvementsParJour'    => $mouvementsParJour,
        ]);
    }
}
