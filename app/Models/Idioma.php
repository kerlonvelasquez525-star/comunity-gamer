<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Idioma extends Model
{
    protected $table = 'idiomas';

    protected $fillable = ['codigo', 'nombre'];

    public function juegos(): BelongsToMany
    {
        return $this->belongsToMany(Juego::class, 'juego_idioma', 'idioma_id', 'juego_id');
    }
}
