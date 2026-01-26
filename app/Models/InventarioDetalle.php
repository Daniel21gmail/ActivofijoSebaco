<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioDetalle extends Model
{
    protected $fillable = [
        'inventario_id',
        'activo_fijo_id',
        'ubicacion_origen_id',
        'ubicacion_verificada_id',
        'responsable_verificado_id',
        'estado_fisico_id',
        'verificado',
        'verificado_por',
    ];
}
