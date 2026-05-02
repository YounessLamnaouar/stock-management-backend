<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'quantiteDisponible',
        'seuilMin',
        'dateDerniereMaj',
        'produit_id',
        'entrepot_id',
    ];

    // Relation : un stock appartient à un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // Relation : un stock appartient à un entrepôt
    public function entrepot()
    {
        return $this->belongsTo(Entrepot::class);
    }

    // Relation : un stock peut avoir plusieurs alertes
    public function alertStocks()
    {
        return $this->hasMany(AlertStock::class);
    }
}
