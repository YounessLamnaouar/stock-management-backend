<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracabilite extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'action',
        'description',
        'dateAction',
        'user_id',
    ];

    // Relation : une trace appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
