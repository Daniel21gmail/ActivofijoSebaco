<?php

namespace App\Exports;

use App\Models\Movimiento;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HistorialExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Movimiento::with([
            'activoFijo',
            'ubicacionOrigen',
            'ubicacionDestino',
            'responsableOrigen',
            'responsableDestino',
            'autorizadoPor'
        ])->orderBy('fecha_movimiento', 'desc');

        if (!empty($this->filters['fecha_inicio']) && !empty($this->filters['fecha_fin'])) {
            $query->whereBetween('fecha_movimiento', [$this->filters['fecha_inicio'], $this->filters['fecha_fin']]);
        }

        // Additional filters if needed, e.g., by Activo or User

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Fecha Movimiento',
            'Código Activo',
            'Descripción Activo',
            'Ubicación Origen',
            'Ubicación Destino',
            'Responsable Anterior',
            'Nuevo Responsable',
            'Autorizado Por'
        ];
    }

    public function map($movimiento): array
    {
        return [
            $movimiento->fecha_movimiento,
            $movimiento->activoFijo->codigo ?? 'N/A',
            $movimiento->activoFijo->descripcion ?? 'N/A',
            $movimiento->ubicacionOrigen->nombre ?? 'N/A',
            $movimiento->ubicacionDestino->nombre ?? 'N/A',
            $movimiento->responsableOrigen->nombre_completo ?? 'N/A',
            $movimiento->responsableDestino->nombre_completo ?? 'N/A',
            $movimiento->autorizadoPor->nombre ?? 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F59E0B']]], // Amber color
        ];
    }
}
