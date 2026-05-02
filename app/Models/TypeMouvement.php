<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeMouvement extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'nomType',
        'description',
    ];

    // Relation : un type de mouvement possède plusieurs mouvements de stock
    public function movementStocks()
    {
        return $this->hasMany(MovementStock::class);
    }
}
