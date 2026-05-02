<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertStock extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'dateAlerte',
        'message',
        'stock_id',
        'niveau_id',
        'statut_id',
        'produit_id',
    ];

    // Relation : une alerte appartient à un stock
    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    // Relation : une alerte appartient à un niveau
    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    // Relation : une alerte possède un statut
    public function statut()
    {
        return $this->belongsTo(Statut::class);
    }

    // Relation : une alerte concerne un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }
}
