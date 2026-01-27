<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Mantenimientos</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2D2E83;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #2D2E83;
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            color: #666;
        }

        .filters {
            margin-bottom: 15px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
        }

        .filters span {
            font-weight: bold;
            color: #2D2E83;
            margin-right: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #2D2E83;
            color: white;
            text-transform: uppercase;
            padding: 8px;
            font-size: 9px;
            text-align: left;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 9px;
        }

        .tr-even {
            background: #f2f2f2;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: right;
            font-size: 8px;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>SIAFSEB - Reporte de Mantenimientos</h1>
        <p>Alcaldía Municipal de Sébaco</p>
        <p>Fecha de Generación: {{ date('d/m/Y H:i A') }}</p>
    </div>

    <div class="filters">
        <span>Rango: {{ $filters['fecha_inicio'] ?? 'N/A' }} al {{ $filters['fecha_fin'] ?? 'N/A' }}</span>
        <span>Responsable: {{ $filters['responsable'] }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Código Activo</th>
                <th>Descripción del Activo</th>
                <th>Tipo</th>
                <th>Costo</th>
                <th>Proveedor</th>
                <th>Responsable</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mantenimientos as $index => $m)
                <tr class="{{ $index % 2 == 0 ? '' : 'tr-even' }}">
                    <td>{{ \Carbon\Carbon::parse($m->fecha_inicio)->format('d/m/Y') }}</td>
                    <td class="font-bold">{{ $m->activoFijo->codigo }}</td>
                    <td>{{ $m->activoFijo->descripcion }}</td>
                    <td>{{ $m->tipo }}</td>
                    <td class="text-right font-bold">C$ {{ number_format($m->costo, 2) }}</td>
                    <td>{{ $m->proveedor->nombre }}</td>
                    <td>{{ $m->responsable->nombre_completo }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background: #eee;">
                <td colspan="4" class="text-right font-bold">TOTAL:</td>
                <td class="text-right font-bold">C$ {{ number_format($mantenimientos->sum('costo'), 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Página 1 de 1 - Generado por Sistema de Activos Fijos
    </div>
</body>

</html>