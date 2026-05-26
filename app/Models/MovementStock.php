<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovementStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'dateMouvement',
        'quantite',
        'user_id',
        'produit_id',
        'status_mouvement_id',
        'entrepot_source_id',
        'entrepot_destination_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    public function statusMouvement()
    {
        return $this->belongsTo(StatusMouvement::class);
    }

    public function entrepotSource()
    {
        return $this->belongsTo(Entrepot::class, 'entrepot_source_id');
    }

    public function entrepotDestination()
    {
        return $this->belongsTo(Entrepot::class, 'entrepot_destination_id');
    }
}
