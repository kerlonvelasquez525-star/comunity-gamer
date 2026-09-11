<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RespuestaSoporte extends Model
{
    protected $table = 'respuestas_soporte';

    protected $primaryKey = 'id_respuesta';

    public $timestamps = false;

    protected $fillable = ['id_reporte', 'id_usuario', 'mensaje', 'fecha_respuesta'];

    protected $casts = [
        'fecha_respuesta' => 'datetime',
    ];

    public function reporte(): BelongsTo
    {
        return $this->belongsTo(ReporteSoporte::class, 'id_reporte', 'id_reporte');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
