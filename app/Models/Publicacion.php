<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Database\Factories\PublicacionFactory;

class Publicacion extends Model
{
    /** @use HasFactory<PublicacionFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'publicaciones';

    protected $fillable = ['team_id', 'user_id', 'titulo', 'contenido'];

    /** @return BelongsTo<Team, $this> */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /** @return BelongsTo<User, $this> */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return HasMany<Comentarios, $this> */
    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentarios::class, 'id_publicacion', 'id');
    }

    /** @return HasMany<Reaccion, $this> */
    public function reacciones(): HasMany
    {
        return $this->hasMany(Reaccion::class, 'id_publicacion', 'id');
    }
}
