<?php

namespace App\Models;

use Database\Factories\HiloFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hilo extends Model
{
    /** @use HasFactory<HiloFactory> */
    use HasFactory;

    protected $table = 'hilos';

    protected $primaryKey = 'id_hilo';

    public $timestamps = false;

    protected $fillable = ['id_foro', 'id_usuario', 'titulo', 'contenido', 'fecha_creacion'];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    /** @return BelongsTo<Foro, $this> */
    public function foro(): BelongsTo
    {
        return $this->belongsTo(Foro::class, 'id_foro', 'id_foro');
    }

    /** @return BelongsTo<User, $this> */
    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /** @return HasMany<Aporte, $this> */
    public function aportes(): HasMany
    {
        return $this->hasMany(Aporte::class, 'id_hilo', 'id_hilo');
    }
}
