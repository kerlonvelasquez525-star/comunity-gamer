<?php

namespace App\Models;

use Database\Factories\ReporteSoporteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** @property int|null $team_id */
class ReporteSoporte extends Model
{
    /** @use HasFactory<ReporteSoporteFactory> */
    use HasFactory;

    protected $table = 'reportes_soporte';

    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    protected $fillable = ['team_id', 'id_usuario', 'asunto', 'descripcion', 'estado', 'fecha_creacion'];

    protected $casts = [
        'fecha_creacion' => 'datetime',
    ];

    /** @return BelongsTo<User, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    /** @return BelongsTo<Team, $this> */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /** @return HasMany<RespuestaSoporte, $this> */
    public function respuestas(): HasMany
    {
        return $this->hasMany(RespuestaSoporte::class, 'id_reporte', 'id_reporte');
    }

    /** @return HasMany<VotoSoporte, $this> */
    public function votos(): HasMany
    {
        return $this->hasMany(VotoSoporte::class, 'id_reporte', 'id_reporte');
    }

    /** @return BelongsToMany<Etiqueta, $this> */
    public function etiquetas(): BelongsToMany
    {
        return $this->belongsToMany(Etiqueta::class, 'reportes_etiquetas', 'id_reporte', 'id_etiqueta');
    }
}
