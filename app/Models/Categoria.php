<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $fillable = ['codigo', 'subcategoria', 'clase', 'nombre'];

    public function activosFijos()
    {
        return $this->hasMany(ActivoFijo::class, 'categoria_id');
    }
}
