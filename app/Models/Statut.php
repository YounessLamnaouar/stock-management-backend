<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statut extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'nomStatut',
        'description',
    ];

    // Relation : un statut possède plusieurs alertes
    public function alertStocks()
    {
        return $this->hasMany(AlertStock::class);
    }
}
