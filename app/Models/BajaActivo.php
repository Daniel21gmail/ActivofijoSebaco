<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BajaActivo extends Model
{
    protected $table = 'bajas_activos';

    protected $fillable = [
        'activo_fijo_id',
        'fecha_baja',
        'autorizado_por',
        'creado_por',
        'motivo',
    ];

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class);
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
