<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Depreciación</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #059669;
            padding-bottom: 10px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #059669;
        }

        .subtitle {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #059669;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }

        td {
            border-bottom: 1px solid #ddd;
            padding: 6px;
            font-size: 9px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f3f4f6;
        }

        .page-break {
            page-break-after: always;
        }

        .meta {
            margin-bottom: 15px;
            font-size: 11px;
            color: #444;
        }
    </style>
</head>

<body>
    <div class="header">
        <table style="width: 100%; border-bottom: 2px solid #F59E0B; margin-bottom: 10px;">
            <tr>
                <td style="width: 20%; border: none; text-align: left; vertical-align: middle;">
                    <img src="{{ public_path('logo_sebaco.jpg') }}" alt="Logo" style="max-width: 80px; height: auto;">
                </td>
                <td style="width: 60%; border: none; text-align: center; vertical-align: middle;">
                    <h1 style="margin: 0; font-size: 16px; text-transform: uppercase; color: #111;">ALCALDÍA MUNICIPAL
                        DE SÉBACO</h1>
                    <p style="font-size: 12px; font-weight: bold; color: #2D2E83; margin: 5px 0;">DEPARTAMENTO DE
                        ACTIVOS FIJOS</p>
                    <h2 class="title" style="margin: 5px 0; font-size: 14px; text-align: center; color: #d97706;">
                        Reporte de Cálculo de Depreciación</h2>
                    <p class="subtitle" style="margin: 0; font-size: 10px; text-align: center; color: #666;">SIAFSEB -
                        Sistema de Activos Fijos</p>
                </td>
                <td style="width: 20%; border: none;"></td>
            </tr>
        </table>

        <div style="margin-top: 5px;">
            <div style="float: left; width: 50%; text-align: left; font-size: 10px;">
                <strong>Generado el:</strong> {{ date('d/m/Y H:i') }}
            </div>
            <div style="float: right; width: 50%; text-align: right; font-size: 10px;">
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
                <th>Fecha Adq.</th>
                <th>Vida Útil</th>
                <th>Valor Adq.</th>
                <th>Val. Residual</th>
                <th>Dep. Mensual</th>
                <th>Dep. Acumulada</th>
                <th>Valor Libros</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
                @php
                    $vidaUtilMeses = $activo->vida_util * 12;
                    $base = $activo->valor_adquisicion - $activo->valor_residual;
                    $deprecMensual = $vidaUtilMeses > 0 ? $base / $vidaUtilMeses : 0;
                @endphp
                <tr>
                    <td>{{ $activo->codigo }}</td>
                    <td>{{ Str::limit($activo->descripcion, 30) }}</td>
                    <td>{{ $activo->fecha_adquisicion }}</td>
                    <td>{{ $activo->vida_util }} años</td>
                    <td>C$ {{ number_format($activo->valor_adquisicion, 2) }}</td>
                    <td>C$ {{ number_format($activo->valor_residual, 2) }}</td>
                    <td>C$ {{ number_format($deprecMensual, 2) }}</td>
                    <td>C$ {{ number_format($activo->depreciacion_acumulada, 2) }}</td>
                    <td><strong>C$ {{ number_format($activo->valor_libros, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" style="text-align: right;">TOTALES GENERALES:</td>
                <td>C$ {{ number_format($activos->sum('valor_adquisicion'), 2) }}</td>
                <td>C$ {{ number_format($activos->sum('valor_residual'), 2) }}</td>
                <td></td>
                <td>C$ {{ number_format($activos->sum('depreciacion_acumulada'), 2) }}</td>
                <td>C$ {{ number_format($activos->sum('valor_libros'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>