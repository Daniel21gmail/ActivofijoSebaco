<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = [
        'fecha_inicio',
        'responsable_id',
        'nombre',
        'estado',
    ];
}
