<?php

namespace App\Models;

use Database\Factories\AporteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aporte extends Model
{
    /** @use HasFactory<AporteFactory> */
    use HasFactory;

    protected $table = 'aportes';

    protected $primaryKey = 'id_aporte';

    public $timestamps = false;

    protected $fillable = ['id_hilo', 'id_usuario', 'contenido', 'fecha_aporte'];

    protected $casts = [
        'fecha_aporte' => 'datetime',
    ];

    public function hilo(): BelongsTo
    {
        return $this->belongsTo(Hilo::class, 'id_hilo', 'id_hilo');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
