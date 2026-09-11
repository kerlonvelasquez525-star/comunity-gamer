<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etiqueta extends Model
{
    protected $table = 'etiquetas';

    protected $primaryKey = 'id_etiqueta';

    protected $fillable = ['nombre'];
}
