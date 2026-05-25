<?php

namespace App\Http\Controllers;

use App\Models\AlertStock;
use App\Models\MovementStock;
use App\Models\Stock;
use App\Models\TypeMouvement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovementStockController extends Controller
{
    public function index()
    {
        return MovementStock::with('produit', 'user', 'typeMouvement', 'entrepotSource', 'entrepotDestination')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function show(MovementStock $movementStock)
    {
        return $movementStock->load('produit', 'user', 'typeMouvement', 'entrepotSource', 'entrepotDestination');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'produit_id'              => 'required|exists:produits,id',
            'quantite'                => 'required|integer|min:1',
            'entrepot_source_id'      => 'nullable|exists:entrepots,id',
            'entrepot_destination_id' => 'nullable|exists:entrepots,id',
            'dateMouvement'           => 'required|date',
            'commentaire'             => 'nullable|string',
            'statut'                  => 'nullable|string',
        ]);

        if (empty($data['entrepot_source_id']) && empty($data['entrepot_destination_id'])) {
            return response()->json(['message' => 'Au moins une source ou une destination est requise.'], 422);
        }

        // Auto-detect type from source/destination
        if (!empty($data['entrepot_source_id']) && empty($data['entrepot_destination_id'])) {
            $typeNom = 'Sortie';
        } elseif (empty($data['entrepot_source_id']) && !empty($data['entrepot_destination_id'])) {
            $typeNom = 'Entrée';
        } else {
            $typeNom = 'Transfert';
        }

        $type = TypeMouvement::where('nomType', $typeNom)->first();
        $data['type_mouvement_id'] = $type ? $type->id : 3;
        $data['user_id']           = auth()->id();
        $data['statut']            = $data['statut'] ?? 'En cours';

        $mouvement = null;

        DB::transaction(function () use ($data, &$mouvement) {
            DB::table('stock_user_context')->update(['user_id' => $data['user_id']]);

            $mouvement = MovementStock::create($data);

            $this->applyStockChanges($data);
        });

        return response()->json(
            $mouvement->load('produit', 'user', 'typeMouvement', 'entrepotSource', 'entrepotDestination'),
            201
        );
    }

    public function update(Request $request, MovementStock $movementStock)
    {
        $data = $request->validate([
            'statut'                  => 'nullable|string',
            'commentaire'             => 'nullable|string',
            'quantite'                => 'nullable|integer|min:1',
            'dateMouvement'           => 'nullable|date',
            'entrepot_source_id'      => 'nullable|exists:entrepots,id',
            'entrepot_destination_id' => 'nullable|exists:entrepots,id',
        ]);

        $movementStock->update($data);

        return response()->json($movementStock->load('produit', 'user', 'typeMouvement', 'entrepotSource', 'entrepotDestination'));
    }

    public function destroy(MovementStock $movementStock)
    {
        $movementStock->delete();
        return response()->json(['message' => 'Mouvement supprimé']);
    }

    public function export()
    {
        $movements = MovementStock::with('produit', 'user', 'typeMouvement', 'entrepotSource', 'entrepotDestination')
            ->orderBy('created_at', 'desc')
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mouvements.csv"',
        ];

        $callback = function () use ($movements) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Produit', 'Type', 'Quantité', 'Source', 'Destination', 'Date', 'Agent', 'Statut', 'Commentaire']);

            foreach ($movements as $m) {
                fputcsv($handle, [
                    $m->id,
                    $m->produit->nomProduit ?? '',
                    $m->typeMouvement->nomType ?? '',
                    $m->quantite,
                    $m->entrepotSource->nomEntrepot ?? '—',
                    $m->entrepotDestination->nomEntrepot ?? '—',
                    $m->dateMouvement,
                    ($m->user->prenom ?? '') . ' ' . ($m->user->name ?? ''),
                    $m->statut,
                    $m->commentaire ?? '',
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
                    'quantiteDisponible' => max(0, $stock->quantiteDisponible - $quantite),
                    'dateDerniereMaj'    => now()->toDateString(),
                ]);
                $this->checkAlert($stock->fresh());
            }
        }

        if (!empty($data['entrepot_destination_id'])) {
            $stock = Stock::firstOrCreate(
                ['produit_id' => $produitId, 'entrepot_id' => $data['entrepot_destination_id']],
                ['quantiteDisponible' => 0, 'seuilMin' => 0, 'dateDerniereMaj' => now()->toDateString()]
            );

            $stock->update([
                'quantiteDisponible' => $stock->quantiteDisponible + $quantite,
                'dateDerniereMaj'    => now()->toDateString(),
            ]);
        }
    }

    private function checkAlert(Stock $stock): void
    {
        if ($stock->seuilMin <= 0 || $stock->quantiteDisponible > $stock->seuilMin) {
            return;
        }

        $exists = AlertStock::where('stock_id', $stock->id)->where('statut_id', 1)->exists();
        if ($exists) {
            return;
        }

        if ($stock->quantiteDisponible === 0) {
            $niveauId = 1;
        } elseif ($stock->quantiteDisponible <= $stock->seuilMin * 0.5) {
            $niveauId = 2;
        } else {
            $niveauId = 3;
        }

        AlertStock::create([
            'dateAlerte' => now()->toDateString(),
            'message'    => 'Stock faible : ' . ($stock->produit->nomProduit ?? '') . ' dans ' . ($stock->entrepot->nomEntrepot ?? ''),
            'stock_id'   => $stock->id,
            'niveau_id'  => $niveauId,
            'statut_id'  => 1,
            'produit_id' => $stock->produit_id,
        ]);
    }
}
