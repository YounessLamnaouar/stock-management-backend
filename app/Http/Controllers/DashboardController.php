<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use App\Models\MovementStock;
use App\Models\Produit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $data = Cache::remember('api_dashboard', 60, function () {
            // 4 counts in a single DB roundtrip
            $counts = DB::selectOne('
                SELECT
                    (SELECT COUNT(*) FROM produits)                         AS totalProduits,
                    (SELECT COUNT(*) FROM entrepots)                        AS totalEntrepots,
                    (SELECT COUNT(*) FROM movement_stocks)                  AS totalMouvements,
                    (SELECT COUNT(*) FROM stock_produit WHERE quantite = 0) AS ruptures
            ');

            $stockParEntrepot = Entrepot::select('id', 'nomEntrepot', 'capaciteMax')
                ->withSum('stocks', 'quantite')
                ->get()
                ->map(fn($e) => [
                    'name'        => $e->nomEntrepot,
                    'stock'       => (int) ($e->stocks_sum_quantite ?? 0),
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

            $recentMouvements = MovementStock::with([
                    'produit:id,nomProduit',
                    'statusMouvement:id,nomStatus',
                    'entrepotSource:id,nomEntrepot',
                    'entrepotDestination:id,nomEntrepot',
                ])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'produit_id', 'quantite', 'dateMouvement',
                       'status_mouvement_id', 'entrepot_source_id', 'entrepot_destination_id']);

            $dates  = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->toDateString());
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

            return [
                'totalProduits'        => (int) $counts->totalProduits,
                'totalEntrepots'       => (int) $counts->totalEntrepots,
                'totalMouvements'      => (int) $counts->totalMouvements,
                'ruptures'             => (int) $counts->ruptures,
                'stockParEntrepot'     => $stockParEntrepot,
                'produitsParCategorie' => $produitsParCategorie,
                'recentMouvements'     => $recentMouvements,
                'mouvementsParJour'    => $mouvementsParJour,
            ];
        });

        return response()->json($data);
    }
}
