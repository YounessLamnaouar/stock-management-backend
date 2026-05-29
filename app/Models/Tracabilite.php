<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Entrepot;

class Tracabilite extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'ancienneQuantite',
        'nouvelleQuantite',
        'dateAction',
        'user_id',
        'produit_id',
        'entrepot_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function entrepot()
    {
        return $this->belongsTo(Entrepot::class);
    }
}
