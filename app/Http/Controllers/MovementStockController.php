<?php

namespace App\Http\Controllers;

use App\Models\MovementStock;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementStockController extends Controller
{
    public function index()
    {
        return MovementStock::with('produit', 'user', 'statusMouvement', 'entrepotSource', 'entrepotDestination')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function show(MovementStock $movementStock)
    {
        return $movementStock->load('produit', 'user', 'statusMouvement', 'entrepotSource', 'entrepotDestination');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id'              => 'required|exists:produits,id',
            'quantite'                => 'required|integer|min:1',
            'entrepot_source_id'      => 'nullable|exists:entrepots,id',
            'entrepot_destination_id' => 'nullable|exists:entrepots,id',
            'dateMouvement'           => 'required|date',
            'status_mouvement_id'     => 'required|exists:status_mouvements,id',
        ]);

        if (empty($data['entrepot_source_id']) && empty($data['entrepot_destination_id'])) {
            return response()->json(['message' => 'Au moins une source ou une destination est requise.'], 422);
        }

        $data['user_id'] = auth()->id();

        $mouvement = null;

        DB::transaction(function () use ($data, &$mouvement) {
            DB::table('stock_user_context')->update(['user_id' => $data['user_id']]);

            $mouvement = MovementStock::create($data);

            $this->applyStockChanges($data);
        });

        return response()->json(
            $mouvement->load('produit', 'user', 'statusMouvement', 'entrepotSource', 'entrepotDestination'),
            201
        );
    }

    public function update(Request $request, MovementStock $movementStock)
    {
        $data = $request->validate([
            'status_mouvement_id'     => 'nullable|exists:status_mouvements,id',
            'quantite'                => 'nullable|integer|min:1',
            'dateMouvement'           => 'nullable|date',
            'entrepot_source_id'      => 'nullable|exists:entrepots,id',
            'entrepot_destination_id' => 'nullable|exists:entrepots,id',
        ]);

        $movementStock->update($data);

        return response()->json($movementStock->load('produit', 'user', 'statusMouvement', 'entrepotSource', 'entrepotDestination'));
    }

    public function destroy(MovementStock $movementStock)
    {
        $movementStock->delete();
        return response()->json(['message' => 'Mouvement supprimé']);
    }

    public function export()
    {
        $movements = MovementStock::with('produit', 'user', 'statusMouvement', 'entrepotSource', 'entrepotDestination')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mouvements.csv"',
        ];

        $callback = function () use ($movements) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Produit', 'Quantité', 'Source', 'Destination', 'Date', 'Agent', 'Statut']);

            foreach ($movements as $m) {
                fputcsv($handle, [
                    $m->id,
                    $m->produit->nomProduit ?? '',
                    $m->quantite,
                    $m->entrepotSource->nomEntrepot ?? '—',
                    $m->entrepotDestination->nomEntrepot ?? '—',
                    $m->dateMouvement,
                    ($m->user->prenom ?? '') . ' ' . ($m->user->name ?? ''),
                    $m->statusMouvement->nomStatus ?? '',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function applyStockChanges(array $data): void
    {
        $produitId = $data['produit_id'];
        $quantite  = $data['quantite'];

        if (!empty($data['entrepot_source_id'])) {
            $stock = Stock::where('produit_id', $produitId)
                ->where('entrepot_id', $data['entrepot_source_id'])
                ->first();

            if ($stock) {
                $stock->update([
                    'quantite'      => max(0, $stock->quantite - $quantite),
                    'dateMiseAJour' => now()->toDateString(),
                ]);
            }
        }

        if (!empty($data['entrepot_destination_id'])) {
            $stock = Stock::firstOrCreate(
                ['produit_id' => $produitId, 'entrepot_id' => $data['entrepot_destination_id']],
                ['quantite' => 0, 'dateMiseAJour' => now()->toDateString()]
            );

            $stock->update([
                'quantite'      => $stock->quantite + $quantite,
                'dateMiseAJour' => now()->toDateString(),
            ]);
        }
    }
}
