<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComunidadSolicitud extends Model
{
    protected $table = 'comunidad_solicitudes';

    protected $fillable = ['comunidad_id', 'user_id', 'estado'];

    /**
     * @return BelongsTo<Comunidad, $this>
     */
    public function comunidad(): BelongsTo
    {
        return $this->belongsTo(Comunidad::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
