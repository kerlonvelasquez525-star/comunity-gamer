<?php

namespace App\Models;

use Database\Factories\ComunidadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $team_id
 */
class Comunidad extends Model
{
    /** @use HasFactory<ComunidadFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'comunidades';

    protected $fillable = ['team_id', 'nombre', 'descripcion', 'creador_id'];

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
    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creador_id');
    }

    /**
     * @return BelongsToMany<User, $this, Pivot, 'pivot'>
     */
    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'miembros_comunidad', 'comunidad_id', 'user_id')
            ->withPivot('rol')
            ->withTimestamps();
    }
}
