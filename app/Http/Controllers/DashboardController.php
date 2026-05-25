<?php

namespace App\Http\Controllers;

use App\Models\AlertStock;
use App\Models\Entrepot;
use App\Models\MovementStock;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stats()
    {
        $totalProduits  = Produit::count();
        $totalEntrepots = Entrepot::count();
        $totalTransferts = MovementStock::whereHas('typeMouvement', fn($q) => $q->where('nomType', 'Transfert'))->count();
        $alertesActives = AlertStock::where('statut_id', 1)->count();

        $stockFaible = Stock::whereColumn('quantiteDisponible', '<=', 'seuilMin')
            ->where('quantiteDisponible', '>', 0)
            ->where('seuilMin', '>', 0)
            ->count();

        $ruptures = Stock::where('quantiteDisponible', 0)->count();

        // Stock by warehouse for bar chart
        $stockParEntrepot = Entrepot::withSum('stocks', 'quantiteDisponible')->get()
            ->map(fn($e) => [
                'name'     => $e->nomEntrepot,
                'stock'    => (int) $e->stocks_sum_quantite_disponible,
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

        // Recent transfers
        $recentTransferts = MovementStock::with('produit', 'user', 'entrepotSource', 'entrepotDestination')
            ->whereHas('typeMouvement', fn($q) => $q->where('nomType', 'Transfert'))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent alerts
        $recentAlertes = AlertStock::with('produit', 'stock.entrepot', 'niveau', 'statut')
            ->orderBy('dateAlerte', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'totalProduits'        => $totalProduits,
            'totalEntrepots'       => $totalEntrepots,
            'totalTransferts'      => $totalTransferts,
            'alertesActives'       => $alertesActives,
            'stockFaible'          => $stockFaible,
            'ruptures'             => $ruptures,
            'stockParEntrepot'     => $stockParEntrepot,
            'produitsParCategorie' => $produitsParCategorie,
            'recentTransferts'     => $recentTransferts,
            'recentAlertes'        => $recentAlertes,
        ]);
    }
}
