<?php

namespace App\Models;

use Database\Factories\ComunidadFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comunidad extends Model
{
    /** @use HasFactory<ComunidadFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'comunidades';

    protected $fillable = ['nombre', 'descripcion', 'creador_id'];

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
