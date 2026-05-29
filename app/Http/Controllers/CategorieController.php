<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Categorie::with('produits')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'nomCategorie' => 'required|string|max:255',
        ]);

        return Categorie::create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(Categorie $category)
    {
        return $category->load('produits');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categorie $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categorie $category)
    {
        $data = $request->validate([
            'nomCategorie' => 'required|string|max:255',
        ]);

        $category->update($data);
        return $category;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categorie $category)
    {
        $category->delete();

        return response()->json(['message' => 'Catégorie supprimée']);
    }
}
