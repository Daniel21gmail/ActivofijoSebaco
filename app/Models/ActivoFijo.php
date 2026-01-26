<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivoFijo extends Model
{
    protected $table = 'activos_fijos';

    protected $appends = ['depreciacion_acumulada', 'valor_libros', 'depreciacion_mensual', 'depreciacion_anual'];

    protected $fillable = [
        'codigo',
        'descripcion',
        'serie',
        'categoria_id',
        'departamento_id',
        'fecha_adquisicion',
        'valor_adquisicion',
        'vida_util',
        'valor_residual',
        'marca_id',
        'modelo_id',
        'color_id',
        'fuente_id',
        'proveedor_id',
        'responsable_id',
        'estado_activo_id',
        'ubicacion_id',
        'imagen',
        'creado_por',
        'modificado_por',
    ];

    public function marca()
    {
        return $this->belongsTo(Marca::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function fuente()
    {
        return $this->belongsTo(Fuente::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function responsable()
    {
        return $this->belongsTo(PersonalResponsable::class, 'responsable_id');
    }

    public function estadoActivo()
    {
        return $this->belongsTo(EstadoActivo::class, 'estado_activo_id');
    }

    public function ubicacion()
    {
        return $this->belongsTo(Ubicacion::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function modificador()
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class, 'activo_fijo_id')->orderBy('fecha_movimiento', 'desc');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'activo_fijo_id')->orderBy('fecha_inicio', 'desc');
    }

    public function bajaActivo()
    {
        return $this->hasOne(BajaActivo::class, 'activo_fijo_id');
    }

    public function vehiculo()
    {
        return $this->hasOne(Vehiculo::class, 'activo_fijo_id');
    }

    public function terreno()
    {
        return $this->hasOne(Terreno::class, 'activo_fijo_id');
    }

    public function getDepreciacionAcumuladaAttribute()
    {
        $fechaAdquisicion = \Carbon\Carbon::parse($this->fecha_adquisicion);
        $hoy = \Carbon\Carbon::now();

        $mesesTranscurridos = $fechaAdquisicion->diffInMonths($hoy);
        if ($hoy < $fechaAdquisicion)
            $mesesTranscurridos = 0;

        $baseDepreciable = $this->valor_adquisicion - $this->valor_residual;
        $vidaUtilMeses = $this->vida_util * 12;

        if ($vidaUtilMeses <= 0)
            return 0;

        $depreciacionMensual = $baseDepreciable / $vidaUtilMeses;
        $acumulada = $depreciacionMensual * min($mesesTranscurridos, $vidaUtilMeses);

        return round($acumulada, 2);
    }

    public function getValorLibrosAttribute()
    {
        return round($this->valor_adquisicion - $this->depreciacion_acumulada, 2);
    }

    public function getDepreciacionMensualAttribute()
    {
        $baseDepreciable = $this->valor_adquisicion - $this->valor_residual;
        $vidaUtilMeses = $this->vida_util * 12;

        if ($vidaUtilMeses <= 0)
            return 0;

        return round($baseDepreciable / $vidaUtilMeses, 2);
    }

    public function getDepreciacionAnualAttribute()
    {
        $baseDepreciable = $this->valor_adquisicion - $this->valor_residual;

        if ($this->vida_util <= 0)
            return 0;

        return round($baseDepreciable / $this->vida_util, 2);
    }
}
