<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Comentarios generados en noticias.
 *
 * Permite que usuarios autenticados respondan a una noticia oficial o interna,
 * manteniendo la relación con la noticia y con el autor del comentario.
 */
class NoticiasComentario extends Model
{
    protected $table = 'noticias_comentarios';

    // Campos que pueden ser guardados desde la creación del comentario.
    protected $fillable = ['noticia_id', 'user_id', 'contenido'];

    /**
     * Relación con la noticia a la que pertenece el comentario.
     *
     * @return BelongsTo<noticias, $this>
     */
    public function noticia(): BelongsTo
    {
        return $this->belongsTo(noticias::class, 'noticia_id');
    }

    /**
     * Relación con el usuario que escribió el comentario.
     *
     * @return BelongsTo<User, $this>
     */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
