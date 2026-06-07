<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategorieController extends Controller
{
    public function index()
    {
        return Cache::remember('api_categories', 60, function () {
            return Categorie::select('id', 'nomCategorie', 'created_at', 'updated_at')->get();
        });
    }

    public function create(Request $request) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'nomCategorie' => 'required|string|max:255',
        ]);

        $categorie = Categorie::create($data);
        Cache::forget('api_categories');
        Cache::forget('api_produits');

        return $categorie;
    }

    public function show(Categorie $category)
    {
        return $category->load('produits');
    }

    public function edit(Categorie $category) {}

    public function update(Request $request, Categorie $category)
    {
        $data = $request->validate([
            'nomCategorie' => 'required|string|max:255',
        ]);

        $category->update($data);
        Cache::forget('api_categories');
        Cache::forget('api_produits');

        return $category;
    }

    public function destroy(Categorie $category)
    {
        $category->delete();
        Cache::forget('api_categories');
        Cache::forget('api_produits');

        return response()->json(['message' => 'Catégorie supprimée']);
    }
}
