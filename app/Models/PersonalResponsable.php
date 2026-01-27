<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalResponsable extends Model
{
    protected $fillable = [
        'cargo_id',
        'nombre_completo',
        'email',
        'telefono',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }
}
