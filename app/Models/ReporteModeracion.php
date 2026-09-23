<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @property int|null $team_id */
class ReporteModeracion extends Model
{
    protected $table = 'reportes_moderacion';

    protected $primaryKey = 'id_reporte';

    public $timestamps = false;

    protected $fillable = ['team_id', 'id_reportador', 'id_reportado', 'motivo', 'estado', 'fecha'];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    /** @return BelongsTo<Team, $this> */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /** @return BelongsTo<User, $this> */
    public function reportador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_reportador');
    }

    /** @return BelongsTo<User, $this> */
    public function reportado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_reportado');
    }
}
