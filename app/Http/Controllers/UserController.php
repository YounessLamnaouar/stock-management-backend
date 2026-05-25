<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::with('role')->get();
    }

    public function show(User $user)
    {
        return $user->load('role');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'prenom'   => 'nullable|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id'  => 'required|exists:roles,id',
            'statut'   => 'nullable|string',
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['statut']   = $data['statut'] ?? 'Actif';

        $user = User::create($data);

        return response()->json($user->load('role'), 201);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'    => 'sometimes|string|max:255',
            'prenom'  => 'nullable|string|max:255',
            'email'   => 'sometimes|email|unique:users,email,' . $user->id,
            'password'=> 'nullable|string|min:6',
            'role_id' => 'sometimes|exists:roles,id',
            'statut'  => 'nullable|string',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user->load('role'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['message' => 'Impossible de supprimer votre propre compte.'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé']);
    }
}
