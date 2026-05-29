<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use App\Models\MovementStock;
use App\Models\StatusMouvement;
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
        ]);

        if (empty($data['entrepot_source_id']) && empty($data['entrepot_destination_id'])) {
            return response()->json(['message' => 'Au moins une source ou une destination est requise.'], 422);
        }

        $enCours = StatusMouvement::where('nomStatus', 'En cours')->first();
        $data['status_mouvement_id'] = $enCours?->id ?? 1;
        $data['user_id'] = auth()->id();

        $mouvement = MovementStock::create($data);

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

        $oldStatusId = $movementStock->status_mouvement_id;
        $newStatusId = $data['status_mouvement_id'] ?? $oldStatusId;

        $valideeId     = StatusMouvement::where('nomStatus', 'Validée')->value('id');
        $shouldApply   = $valideeId
            && (int)$newStatusId  === (int)$valideeId
            && (int)$oldStatusId  !== (int)$valideeId;

        if ($shouldApply) {
            try {
                DB::transaction(function () use ($movementStock, $data) {
                    $movementStock->update($data);
                    DB::table('stock_user_context')->update(['user_id' => auth()->id()]);
                    $this->applyStockChanges([
                        'produit_id'              => $movementStock->produit_id,
                        'quantite'                => $movementStock->quantite,
                        'entrepot_source_id'      => $movementStock->entrepot_source_id,
                        'entrepot_destination_id' => $movementStock->entrepot_destination_id,
                    ]);
                });
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 422);
            }
        } else {
            $movementStock->update($data);
        }

        return response()->json($movementStock->fresh()->load('produit', 'user', 'statusMouvement', 'entrepotSource', 'entrepotDestination'));
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

            $available = $stock?->quantite ?? 0;
            if (!$stock || $available < $quantite) {
                throw new \Exception(
                    "Stock insuffisant dans l'entrepôt source. Disponible : {$available}, demandé : {$quantite}."
                );
            }

            $stock->update([
                'quantite'      => $available - $quantite,
                'dateMiseAJour' => now()->toDateString(),
            ]);
        }

        if (!empty($data['entrepot_destination_id'])) {
            $destId   = $data['entrepot_destination_id'];
            $entrepot = Entrepot::findOrFail($destId);

            $existingStock = Stock::firstOrNew(
                ['produit_id' => $produitId, 'entrepot_id' => $destId],
                ['quantite' => 0, 'dateMiseAJour' => now()->toDateString()]
            );

            $newQty     = $existingStock->quantite + $quantite;
            $totalStock = Stock::where('entrepot_id', $destId)
                ->where('produit_id', '!=', $produitId)
                ->sum('quantite');

            if ($entrepot->capaciteMax !== null && ($totalStock + $newQty) > $entrepot->capaciteMax) {
                throw new \Exception("Cet entrepôt est saturé (capacité max: {$entrepot->capaciteMax}).");
            }

            if (!$existingStock->exists) {
                $existingStock->save();
            }

            $existingStock->update([
                'quantite'      => $newQty,
                'dateMiseAJour' => now()->toDateString(),
            ]);
        }
    }
}
