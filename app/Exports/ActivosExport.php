<?php

namespace App\Exports;

use App\Models\ActivoFijo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivosExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = ActivoFijo::with([
            'categoria',
            'departamento',
            'ubicacion',
            'estadoActivo',
            'responsable',
            'marca',
            'modelo'
        ]);

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

        if (!empty($this->filters['fecha_inicio']) && !empty($this->filters['fecha_fin'])) {
            $query->whereBetween('fecha_adquisicion', [$this->filters['fecha_inicio'], $this->filters['fecha_fin']]);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Código',
            'Descripción',
            'Categoría',
            'Marca',
            'Modelo',
            'Serie',
            'Departamento',
            'Ubicación',
            'Responsable',
            'Estado',
            'Fecha Adquisición',
            'Vida Útil (Años)',
            'Valor Adquisición',
            'Valor Residual',
            'Depreciación Acum.',
            'Valor Libros'
        ];
    }

    public function map($activo): array
    {
        return [
            $activo->id,
            $activo->codigo,
            $activo->descripcion,
            $activo->categoria->nombre ?? 'N/A',
            $activo->marca->nombre ?? 'N/A',
            $activo->modelo->nombre ?? 'N/A',
            $activo->serie ?? 'N/A',
            $activo->departamento->nombre ?? 'N/A',
            $activo->ubicacion->nombre ?? 'N/A',
            $activo->responsable->nombre_completo ?? 'Sin Asignar',
            $activo->estadoActivo->nombre ?? 'N/A',
            $activo->fecha_adquisicion,
            $activo->vida_util,
            $activo->valor_adquisicion,
            $activo->valor_residual,
            $activo->depreciacion_acumulada,
            $activo->valor_libros
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']]],
        ];
    }
}
