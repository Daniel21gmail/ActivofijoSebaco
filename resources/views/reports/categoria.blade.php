<!DOCTYPE html>
<html>

<head>
    <title>Reporte por Categoría</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #6366F1;
            padding-bottom: 10px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #6366F1;
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
            background-color: #6366F1;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
            font-size: 10px;
        }

        .total-row {
            font-weight: bold;
            background-color: #f3f4f6;
            font-size: 11px;
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
        <table style="width: 100%; border-bottom: 2px solid #6366F1; margin-bottom: 10px;">
            <tr>
                <td style="width: 20%; border: none; text-align: left; vertical-align: middle;">
                    <img src="{{ public_path('logo_sebaco.jpg') }}" alt="Logo" style="max-width: 80px; height: auto;">
                </td>
                <td style="width: 60%; border: none; text-align: center; vertical-align: middle;">
                    <h1 style="margin: 0; font-size: 16px; text-transform: uppercase; color: #111;">ALCALDÍA MUNICIPAL
                        DE SÉBACO</h1>
                    <p style="font-size: 12px; font-weight: bold; color: #2D2E83; margin: 5px 0;">DEPARTAMENTO DE
                        ACTIVOS FIJOS</p>
                    <h2 class="title" style="margin: 5px 0; font-size: 14px; text-align: center; color: #6366F1;">
                        Reporte de Activos por Categoría</h2>
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
                <th>Categoría</th>
                <th class="text-center">Cantidad de Activos</th>
                <th class="text-right">Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categorias as $categoria)
                <tr>
                    <td><strong>{{ $categoria->nombre }}</strong></td>
                    <td class="text-center">{{ $categoria->activos_count }}</td>
                    <td class="text-right">C$ {{ number_format($categoria->valor_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>TOTALES GENERALES:</td>
                <td class="text-center">{{ $categorias->sum('activos_count') }}</td>
                <td class="text-right">C$ {{ number_format($categorias->sum('valor_total'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>

</html>