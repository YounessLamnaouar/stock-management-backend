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

        $dates = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->toDateString());

        $rawMvt = MovementStock::selectRaw('DATE(dateMouvement) as date, COUNT(*) as total')
            ->whereBetween('dateMouvement', [$dates->first(), $dates->last()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $mouvementsParJour = $dates->map(fn($d) => [
            'date'  => $d,
            'total' => $rawMvt->has($d) ? (int) $rawMvt[$d]->total : 0,
        ])->values();

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
