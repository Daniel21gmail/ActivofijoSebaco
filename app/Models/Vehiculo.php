<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    protected $fillable = [
        'activo_fijo_id',
        'placa',
        'chasis',
        'motor',
        'numero_circulacion',
    ];

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class);
    }
}
