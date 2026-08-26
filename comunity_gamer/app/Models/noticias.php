<?php

namespace App\Models;

use Database\Factories\NoticiasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class noticias extends Model
{
    /** @use HasFactory<NoticiasFactory> */
    use HasFactory;

    protected $fillable = ['titulo', 'contenido', 'categoria', 'imagen_url'];
}
