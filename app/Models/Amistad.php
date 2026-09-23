<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Amistad extends Model
{
    protected $table = 'amistades';

    protected $fillable = ['user_id', 'amigo_id', 'estado'];

    /** @return BelongsTo<User, $this> */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** @return BelongsTo<User, $this> */
    public function amigo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'amigo_id');
    }
}
