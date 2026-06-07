<?php

namespace App\Http\Controllers;

use App\Models\Entrepot;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class EntrepotController extends Controller
{
    public function index()
    {
        return Cache::remember('api_entrepots', 60, function () {
            return Entrepot::select('id', 'nomEntrepot', 'adresse', 'capaciteMax', 'created_at', 'updated_at')->get();
        });
    }

    public function create() {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomEntrepot' => 'required|string|max:255',
            'adresse'     => 'nullable|string',
            'capaciteMax' => 'nullable|integer|min:0',
        ]);

        $entrepot = Entrepot::create($data);
        Cache::forget('api_entrepots');
        Cache::forget('api_dashboard');

        return $entrepot;
    }

    public function show(Entrepot $entrepot)
    {
        return $entrepot->load('stocks.produit');
    }

    public function edit(Entrepot $entrepot) {}

    public function update(Request $request, Entrepot $entrepot)
    {
        $data = $request->validate([
            'nomEntrepot' => 'required|string|max:255',
            'adresse'     => 'nullable|string',
            'capaciteMax' => 'nullable|integer|min:0',
        ]);

        $entrepot->update($data);
        Cache::forget('api_entrepots');
        Cache::forget('api_dashboard');

        return $entrepot;
    }

    public function destroy(Entrepot $entrepot)
    {
        $entrepot->delete();
        Cache::forget('api_entrepots');
        Cache::forget('api_stocks');
        Cache::forget('api_dashboard');

        return response()->json(['message' => 'Entrepôt supprimé']);
    }
}
