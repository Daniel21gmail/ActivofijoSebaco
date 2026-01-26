<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Terreno extends Model
{
    protected $fillable = [
        'activo_fijo_id',
        'area_m2',
        'folio_real',
        'catastro',
        'uso',
    ];

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class);
    }
}
