<?php

namespace App\Models;

use Database\Factories\ReporteSoporteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReporteSoporte extends Model
{
    /** @use HasFactory<ReporteSoporteFactory> */
    use HasFactory;

    protected $table = 'reportes_soporte';

    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    protected $fillable = ['id_usuario', 'asunto', 'descripcion', 'estado', 'fecha_creacion'];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaSoporte::class, 'id_reporte', 'id_reporte');
    }

    public function votos(): HasMany
    {
        return $this->hasMany(VotoSoporte::class, 'id_reporte', 'id_reporte');
    }

    public function etiquetas(): BelongsToMany
    {
        return $this->belongsToMany(Etiqueta::class, 'reportes_etiquetas', 'id_reporte', 'id_etiqueta');
    }
}
