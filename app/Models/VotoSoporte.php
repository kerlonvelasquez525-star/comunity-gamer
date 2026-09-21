<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VotoSoporte extends Model
{
    protected $table = 'votos_soporte';

    protected $primaryKey = 'id_voto';

    public $timestamps = false;

    protected $fillable = ['id_reporte', 'id_usuario', 'voto'];

    /** @return BelongsTo<ReporteSoporte, $this> */
    public function reporte(): BelongsTo
    {
        return $this->belongsTo(ReporteSoporte::class, 'id_reporte', 'id_reporte');
    }

    /** @return BelongsTo<User, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }
}
