<?php

namespace App\Models;

use Database\Factories\ProblemasFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Problemas extends Model
{
    /** @use HasFactory<ProblemasFactory> */
    use HasFactory;

    protected $fillable = ['titulo', 'descripcion', 'estado', 'prioridad', 'plataforma', 'user_id'];
}
