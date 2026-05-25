<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementStock extends Model
{
    // 1 may 
    use HasFactory;

    protected $fillable = [
        'dateMouvement',
        'quantite',
        'commentaire',
        'statut',
        'user_id',
        'produit_id',
        'type_mouvement_id',
        'entrepot_source_id',
        'entrepot_destination_id',
    ];

    // Relation : un mouvement appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation : un mouvement concerne un produit
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    // Relation : un mouvement possède un type
    public function typeMouvement()
    {
        return $this->belongsTo(TypeMouvement::class);
    }

    // Relation : un mouvement est effectué dans un entrepôt
    public function entrepotSource()
    {
        return $this->belongsTo(Entrepot::class, 'entrepot_source_id');
    }

    public function entrepotDestination()
    {
        return $this->belongsTo(Entrepot::class, 'entrepot_destination_id');
    }
}
