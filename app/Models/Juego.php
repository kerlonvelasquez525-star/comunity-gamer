<?php

namespace App\Models;

use Database\Factories\JuegoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Juego extends Model
{
    /** @use HasFactory<JuegoFactory> */
    use HasFactory;

    protected $table = 'juegos';

    protected $fillable = ['titulo', 'descripcion', 'desarrollador', 'fecha_lanzamiento'];

    protected $casts = [
        'fecha_lanzamiento' => 'date',
    ];

    /** @return BelongsToMany<Plataforma, $this> */
    public function plataformas(): BelongsToMany
    {
        return $this->belongsToMany(Plataforma::class, 'juego_plataforma', 'juego_id', 'plataforma_id');
    }

    /** @return BelongsToMany<Idioma, $this> */
    public function idiomas(): BelongsToMany
    {
        return $this->belongsToMany(Idioma::class, 'juego_idioma', 'juego_id', 'idioma_id');
    }
}
