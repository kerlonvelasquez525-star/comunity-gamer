<?php

namespace App\Models;

use Database\Factories\ComentariosFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentarios extends Model
{
    /** @use HasFactory<ComentariosFactory> */
    use HasFactory;

    protected $table = 'comentarios';

    protected $primaryKey = 'id_comentario';

    public $timestamps = false;

    protected $fillable = ['id_publicacion', 'id_usuario', 'contenido'];
}
