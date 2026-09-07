<?php

namespace App\Models;

use Database\Factories\NoticiasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo principal para las noticias del sistema.
 *
 * Se usa tanto para publicaciones del equipo como para noticias oficiales
 * importadas desde fuentes externas. La columna team_id permite distinguir
 * ambos tipos de contenido dentro del mismo modelo.
 */
class noticias extends Model
{
    /** @use HasFactory<NoticiasFactory> */
    use HasFactory;

    // Campos permitidos para crear o actualizar una noticia desde el controlador.
    protected $fillable = ['team_id', 'user_id', 'titulo', 'contenido', 'categoria', 'imagen_url', 'fuente_nombre', 'fuente_url', 'fuente_hash', 'es_oficial'];

    /**
     * Convierte el campo es_oficial a boolean para facilitar filtros y validaciones.
     */
    protected function casts(): array
    {
        return ['es_oficial' => 'boolean'];
    }

    /**
     * Relación con el equipo al que pertenece la noticia.
     *
     * @return BelongsTo<Team, $this>
     */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * Relación con el usuario que publicó la noticia.
     *
     * @return BelongsTo<User, $this>
     */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Comentarios asociados a esta noticia.
     *
     * @return HasMany<NoticiasComentario, $this>
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(NoticiasComentario::class, 'noticia_id');
    }
}
