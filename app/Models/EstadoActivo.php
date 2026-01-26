<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoActivo extends Model
{
    protected $table = 'estados_activos';

    protected $fillable = ['nombre'];
}
