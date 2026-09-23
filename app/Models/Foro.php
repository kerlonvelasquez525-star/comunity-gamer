<?php

namespace App\Models;

use Database\Factories\ForoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Foro extends Model
{
    /** @use HasFactory<ForoFactory> */
    use HasFactory;

    protected $table = 'foros';

    protected $primaryKey = 'id_foro';

    public $timestamps = false;

    protected $fillable = ['comunidad_id', 'nombre', 'descripcion', 'fecha_creacion'];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    /** @return BelongsTo<Comunidad, $this> */
    public function comunidad(): BelongsTo
    {
        return $this->belongsTo(Comunidad::class, 'comunidad_id');
    }

    /** @return HasMany<Hilo, $this> */
    public function hilos(): HasMany
    {
        return $this->hasMany(Hilo::class, 'id_foro', 'id_foro');
    }
}
