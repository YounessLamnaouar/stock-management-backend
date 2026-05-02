<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    // 1 may
     use HasFactory;

    protected $fillable = [
        'codeProduit',
        'nomProduit',
        'description',
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

    // Relation : un produit peut déclencher plusieurs alertes
    public function alertStocks()
    {
        return $this->hasMany(AlertStock::class);
    }
}
