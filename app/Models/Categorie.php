<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    // 1 may
    use HasFactory;

    protected $fillable = [
        'nomCategorie',
        'description',
    ];

    // Relation : une catégorie contient plusieurs produits
    public function produits()
    {
        return $this->hasMany(Produit::class);
    }
}
