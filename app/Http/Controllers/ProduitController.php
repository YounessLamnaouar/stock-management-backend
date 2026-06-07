<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProduitController extends Controller
{
    public function index()
    {
        return Cache::remember('api_produits', 60, function () {
            return Produit::with('categorie:id,nomCategorie')->get();
        });
    }

    public function create() {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomProduit'   => 'required|string|max:255',
            'unite'        => 'required|string',
            'dateCreation' => 'nullable|date',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        $data['dateCreation'] = $data['dateCreation'] ?? now()->toDateString();

        $produit = Produit::create($data);
        Cache::forget('api_produits');
        Cache::forget('api_dashboard');

        return response()->json($produit->load('categorie:id,nomCategorie'), 201);
    }

    public function show(Produit $produit)
    {
        return $produit->load('categorie', 'stocks');
    }

    public function edit(Produit $produit) {}

    public function update(Request $request, Produit $produit)
    {
        $data = $request->validate([
            'nomProduit'   => 'required|string|max:255',
            'unite'        => 'required|string',
            'dateCreation' => 'nullable|date',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        $produit->update($data);
        Cache::forget('api_produits');
        Cache::forget('api_dashboard');

        return $produit->load('categorie:id,nomCategorie');
    }

    public function destroy(Produit $produit)
    {
        $produit->delete();
        Cache::forget('api_produits');
        Cache::forget('api_dashboard');

        return response()->json(['message' => 'Produit supprimé']);
    }
}
