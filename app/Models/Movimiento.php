<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $fillable = [
        'activo_fijo_id',
        'fecha_movimiento',
        'ubicacion_origen_id',
        'ubicacion_destino_id',
        'responsable_origen_id',
        'responsable_destino_id',
        'autorizado_por',
        'creado_por',
    ];

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class);
    }

    public function ubicacionOrigen()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_origen_id');
    }

    public function ubicacionDestino()
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_destino_id');
    }

    public function responsableOrigen()
    {
        return $this->belongsTo(PersonalResponsable::class, 'responsable_origen_id');
    }

    public function responsableDestino()
    {
        return $this->belongsTo(PersonalResponsable::class, 'responsable_destino_id');
    }

    public function autorizadoPor()
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
