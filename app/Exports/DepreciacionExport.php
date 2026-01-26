<?php

namespace App\Exports;

use App\Models\ActivoFijo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DepreciacionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ActivoFijo::with(['categoria', 'estadoActivo']);

        if (!empty($this->filters['departamento_id'])) {
            $query->where('departamento_id', $this->filters['departamento_id']);
        }
        if (!empty($this->filters['ubicacion_id'])) {
            $query->where('ubicacion_id', $this->filters['ubicacion_id']);
        }
        if (!empty($this->filters['responsable_id'])) {
            $query->where('responsable_id', $this->filters['responsable_id']);
        }
        if (!empty($this->filters['estado_activo_id'])) {
            $query->where('estado_activo_id', $this->filters['estado_activo_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Código',
            'Descripción',
            'F. Adquisición',
            'Valor Adquisición',
            'Vida Útil (Años)',
            'Tasa Anual',
            'Deprec. Mensual',
            'Meses Deprec.',
            'Deprec. Acumulada',
            'Valor en Libros'
        ];
    }

    public function map($activo): array
    {
        $vidaUtilMeses = $activo->vida_util * 12;
        $deprecMensual = $vidaUtilMeses > 0 ? ($activo->valor_adquisicion - $activo->valor_residual) / $vidaUtilMeses : 0;

        return [
            $activo->codigo,
            $activo->descripcion,
            $activo->fecha_adquisicion,
            $activo->valor_adquisicion,
            $activo->vida_util,
            round((100 / ($activo->vida_util ?: 1)), 2) . '%',
            round($deprecMensual, 2),
            $activo->fecha_adquisicion ? \Carbon\Carbon::parse($activo->fecha_adquisicion)->diffInMonths(now()) : 0,
            $activo->depreciacion_acumulada,
            $activo->valor_libros
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '059669']]], // Emerald color
        ];
    }
}
