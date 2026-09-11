<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaccion extends Model
{
    protected $table = 'reacciones';

    protected $primaryKey = 'id_reaccion';

    public $timestamps = false;

    protected $fillable = ['id_usuario', 'id_publicacion', 'tipo', 'fecha'];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class, 'id_publicacion', 'id');
    }
}
