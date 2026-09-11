<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Plataforma extends Model
{
    protected $table = 'plataformas';

    protected $fillable = ['nombre'];

    public function juegos(): BelongsToMany
    {
        return $this->belongsToMany(Juego::class, 'juego_plataforma', 'plataforma_id', 'juego_id');
    }
}
