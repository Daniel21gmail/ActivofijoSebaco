<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';

    protected $fillable = [
        'activo_fijo_id',
        'fecha_inicio',
        'costo',
        'proveedor_id',
        'responsable_id',
        'creado_por',
        'tipo',
    ];

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function responsable()
    {
        return $this->belongsTo(PersonalResponsable::class);
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
