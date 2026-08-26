<?php

namespace App\Models;

use Database\Factories\ProblemasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Problemas extends Model
{
    /** @use HasFactory<ProblemasFactory> */
    use HasFactory;

    protected $fillable = ['team_id', 'titulo', 'descripcion', 'estado', 'prioridad', 'plataforma', 'user_id'];

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
        return $this->belongsTo(User::class, 'user_id');
    }
}
