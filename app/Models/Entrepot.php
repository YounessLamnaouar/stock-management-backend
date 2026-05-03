<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrepot extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'nomEntrepot',
        'adresse',
        'ville',
        'capacite',
    ];

    // Relation : un entrepôt possède plusieurs stocks
    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    // Relation : un entrepôt possède plusieurs mouvements de stock
    public function mouvementsSource()
    {
        return $this->hasMany(MovementStock::class, 'entrepot_source_id');
    }

    public function mouvementsDestination()
    {
        return $this->hasMany(MovementStock::class, 'entrepot_destination_id');
    }
}
