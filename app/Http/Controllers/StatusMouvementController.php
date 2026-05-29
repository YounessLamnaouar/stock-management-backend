<?php

namespace App\Http\Controllers;

use App\Models\StatusMouvement;
use Illuminate\Http\Request;

class StatusMouvementController extends Controller
{
    public function index()
    {
        return StatusMouvement::all();
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nomStatus' => 'required|string|max:255']);
        return response()->json(StatusMouvement::create($data), 201);
    }

    public function show(StatusMouvement $statusMouvement)
    {
        return $statusMouvement;
    }

    public function update(Request $request, StatusMouvement $statusMouvement)
    {
        $data = $request->validate(['nomStatus' => 'required|string|max:255']);
        $statusMouvement->update($data);
        return response()->json($statusMouvement);
    }

    public function destroy(StatusMouvement $statusMouvement)
    {
        $statusMouvement->delete();
        return response()->json(['message' => 'Statut supprimé']);
    }
}
