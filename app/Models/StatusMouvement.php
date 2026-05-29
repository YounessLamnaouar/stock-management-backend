<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusMouvement extends Model
{
    use HasFactory;

    protected $fillable = ['nomStatus'];

    public function movementStocks()
    {
        return $this->hasMany(MovementStock::class);
    }
}
