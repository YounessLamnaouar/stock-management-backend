<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Produit::with('categorie')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'codeProduit' => 'required|string|unique:produits,codeProduit',
            'nomProduit' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unite' => 'required|string',
            'dateCreation' => 'nullable|date',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        return Produit::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Produit $produit)
    {
        return $produit->load('categorie', 'stocks');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produit $produit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produit $produit)
    {
        $data = $request->validate([
            'codeProduit' => 'required|string|unique:produits,codeProduit,' . $produit->id,
            'nomProduit' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unite' => 'required|string',
            'dateCreation' => 'nullable|date',
            'categorie_id' => 'required|exists:categories,id',
        ]);

        $produit->update($data);

        return $produit;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produit $produit)
    {
        $produit->delete();

        return response()->json(['message' => 'Produit supprimé']);
    }
}
