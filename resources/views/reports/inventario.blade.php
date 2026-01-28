<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Reporte de Activos Fijos</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 2px 0;
            color: #666;
        }

        .filters {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #eee;
        }

        .filters strong {
            display: inline-block;
            width: 100px;
        }

        .filters div {
            margin-bottom: 4px;
        }

        .summary {
            margin-top: 20px;
            text-align: right;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <table style="width: 100%; border-bottom: 2px solid #333; margin-bottom: 10px;">
            <tr>
                <td style="width: 20%; border: none; text-align: left; vertical-align: middle;">
                    <img src="{{ public_path('logo_sebaco.jpg') }}" alt="Logo" style="max-width: 80px; height: auto;">
                </td>
                <td style="width: 60%; border: none; text-align: center; vertical-align: middle;">
                    <h1 style="margin: 0; font-size: 16px; text-transform: uppercase; color: #111;">ALCALDÍA MUNICIPAL
                        DE SÉBACO</h1>
                    <p style="font-size: 12px; font-weight: bold; color: #2D2E83; margin: 5px 0;">DEPARTAMENTO DE
                        ACTIVOS FIJOS</p>
                    <h2 style="margin: 5px 0; font-size: 14px; color: #444;">Reporte General de Activos Fijos</h2>
                    <p style="margin: 0; font-size: 10px; color: #666;">SIAFSEB - Sistema de Activos Fijos</p>
                </td>
                <td style="width: 20%; border: none;"></td>
            </tr>
        </table>

        <div style="margin-top: 5px;">
            <div style="float: left; width: 50%; text-align: left;">
                <strong>Fecha:</strong> {{ date('d/m/Y H:i') }}
            </div>
            <div style="float: right; width: 50%; text-align: right;">
                <strong>Usuario:</strong> {{ auth()->user()->nombre ?? 'N/A' }}
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>



    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th>Marca / Modelo</th>
                <th>Ubicación</th>
                <th>Responsable</th>
                <th>F. Adq.</th>
                <th>Valor Adq.</th>
                <th>Depreciación</th>
                <th>Valor Neto</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
                <tr>
                    <td>{{ $activo->codigo }}</td>
                    <td>
                        <b>{{ $activo->descripcion }}</b><br>
                        <small>Serie: {{ $activo->serie }}</small>
                    </td>
                    <td>{{ $activo->marca->nombre ?? '-' }} / {{ $activo->modelo->nombre ?? '-' }}</td>
                    <td>
                        {{ $activo->ubicacion->nombre ?? '-' }}<br>
                        <small>{{ $activo->departamento->nombre ?? '-' }}</small>
                    </td>
                    <td>{{ $activo->responsable->nombre_completo ?? 'Sin Asignar' }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($activo->fecha_adquisicion)->format('d/m/Y') }}</td>
                    <td class="text-right">C$ {{ number_format($activo->valor_adquisicion, 2) }}</td>
                    <td class="text-right">C$ {{ number_format($activo->depreciacion_acumulada, 2) }}</td>
                    <td class="text-right"><b>C$ {{ number_format($activo->valor_libros, 2) }}</b></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><strong>Totales:</strong></td>
                <td class="text-right"><strong>C$ {{ number_format($activos->sum('valor_adquisicion'), 2) }}</strong>
                </td>
                <td class="text-right"><strong>C$
                        {{ number_format($activos->sum('depreciacion_acumulada'), 2) }}</strong></td>
                <td class="text-right"><strong>C$ {{ number_format($activos->sum('valor_libros'), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary">
        <p>Total de Activos: {{ count($activos) }}</p>
    </div>
</body>

</html>