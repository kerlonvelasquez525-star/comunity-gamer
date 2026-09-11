<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteModeracion extends Model
{
    protected $table = 'reportes_moderacion';

    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    protected $fillable = ['id_reportador', 'id_reportado', 'motivo', 'estado', 'fecha'];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function reportador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_reportador');
    }

    public function reportado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_reportado');
    }
}
