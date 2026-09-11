<?php

namespace App\Models;

use Database\Factories\CanalFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Canal extends Model
{
    /** @use HasFactory<CanalFactory> */
    use HasFactory;

    protected $table = 'canales';

    protected $primaryKey = 'id_canal';

    public $timestamps = false;

    protected $fillable = ['comunidad_id', 'nombre', 'tipo', 'fecha_creacion'];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    public function comunidad(): BelongsTo
    {
        return $this->belongsTo(Comunidad::class, 'comunidad_id');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'canal_id', 'id_canal');
    }
}
