<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    // 1 may
    use HasFactory;

    protected $fillable = [
        'nomRole',
    ];

    // Relation : un rôle possède plusieurs utilisateurs
    public function users()
    {
        return $this->hasMany(User::class);
    }
}