<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    // 1 may
     use HasFactory;

    protected $fillable = [
        'nomProduit',
        'unite',
        'dateCreation',
        'categorie_id',
    ];

    // Relation : un produit appartient à une catégorie
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    // Relation : un produit possède plusieurs stocks
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    // Relation : un produit possède plusieurs mouvements de stock
    public function movementStocks()
    {
        return $this->hasMany(MovementStock::class);
    }

    public function tracabilites()
    {
        return $this->hasMany(Tracabilite::class);
    }
}
