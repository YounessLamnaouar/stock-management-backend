<?php

namespace App\Http\Controllers;

use App\Models\Tracabilite;
use Illuminate\Http\Request;

class TracabiliteController extends Controller
{
    // Read-only: records are auto-inserted by the DB trigger (after_stocks_qty_update)
    public function index(Request $request)
    {
        $query = Tracabilite::with('user', 'produit', 'entrepot')
            ->orderBy('dateAction', 'desc');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('dateAction', $request->date);
        }

        return $query->get();
    }

    public function show(Tracabilite $tracabilite)
    {
        return $tracabilite->load('user', 'produit', 'entrepot');
    }
}
