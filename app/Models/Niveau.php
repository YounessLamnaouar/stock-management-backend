<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'nomNiveau',
        'description',
    ];

    // Relation : un niveau possède plusieurs alertes
    public function alertStocks()
    {
        return $this->hasMany(AlertStock::class);
    }
}
