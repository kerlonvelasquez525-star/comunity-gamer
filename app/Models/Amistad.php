<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Amistad extends Model
{
    protected $table = 'amistades';

    protected $fillable = ['user_id', 'amigo_id', 'estado'];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function amigo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'amigo_id');
    }
}
