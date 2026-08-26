<?php

namespace App\Models;

use Database\Factories\ComentariosFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comentarios extends Model
{
    /** @use HasFactory<ComentariosFactory> */
    use HasFactory;

    protected $table = 'comentarios';

    protected $primaryKey = 'id_comentario';

    public $timestamps = false;

    protected $fillable = ['team_id', 'id_publicacion', 'id_usuario', 'contenido'];

    /**
     * @return BelongsTo<Team, $this>
     */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
