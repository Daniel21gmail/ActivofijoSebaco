<!DOCTYPE html>
<html>

<head>
    <title>Resumen Mensual de Movimientos</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2D2E83;
            padding-bottom: 10px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #2D2E83;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2D2E83;
            margin-top: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th {
            background-color: #2D2E83;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }

        td {
            border-bottom: 1px solid #eee;
            padding: 6px;
            font-size: 9px;
        }

        .text-right {
            text-align: right;
        }

        .empty-msg {
            color: #888;
            font-style: italic;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 14px; text-transform: uppercase;">ALCALDÍA MUNICIPAL DE SÉBACO</h1>
        <h2 class="title">RESUMEN MENSUAL DE ACTIVOS</h2>
        <p style="margin: 5px 0;">Periodo: <strong>{{ $periodo }}</strong></p>
    </div>

    <!-- ALTAS -->
    <div class="section-title">ALTAS (NUEVOS ACTIVOS)</div>
    @if($altas->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>CÓDIGO</th>
                    <th>DESCRIPCIÓN</th>
                    <th>VALOR</th>
                    <th>UBICACIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($altas as $item)
                    <tr>
                        <td>{{ $item->fecha_adquisicion }}</td>
                        <td>{{ $item->codigo }}</td>
                        <td>{{ $item->descripcion }}</td>
                        <td>C$ {{ number_format($item->valor_adquisicion, 2) }}</td>
                        <td>{{ $item->ubicacion->nombre ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-msg">No se registraron altas en este periodo.</div>
    @endif

    <!-- BAJAS -->
    <div class="section-title" style="color: #dc2626; border-color: #dc2626;">BAJAS (ACTIVOS RETIRADOS)</div>
    @if($bajas->count() > 0)
        <table>
            <thead>
                <tr style="background-color: #dc2626;">
                    <th>FECHA BAJA</th>
                    <th>ACTIVO</th>
                    <th>MOTIVO</th>
                    <th>OBSERVACIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bajas as $item)
                    <tr>
                        <td>{{ $item->fecha_baja }}</td>
                        <td>{{ $item->activoFijo->codigo ?? 'N/A' }} - {{ $item->activoFijo->descripcion ?? '' }}</td>
                        <td>{{ $item->motivo }}</td>
                        <td>{{Str::limit($item->observaciones, 50) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-msg">No se registraron bajas en este periodo.</div>
    @endif

    <div style="margin-top: 40px; border-top: 1px solid #ccc; padding-top: 10px;">
        <p style="font-size: 10px;"><strong>Resumen:</strong> Altas: {{ $altas->count() }} | Bajas:
            {{ $bajas->count() }}</p>
    </div>
</body>

</html>