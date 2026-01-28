<!DOCTYPE html>
<html>

<head>
    <title>Libro de Activos Fijos</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 9px;
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

        .subtitle {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #2D2E83;
            color: white;
            padding: 6px;
            text-align: left;
            font-size: 8px;
            border: 1px solid #1a1b4b;
        }

        td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 8px;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 30px;
            font-size: 8px;
            text-align: center;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 style="margin: 0; font-size: 14px; text-transform: uppercase;">ALCALDÍA MUNICIPAL DE SÉBACO</h1>
        <h2 class="title">LIBRO DE REGISTRO DE ACTIVOS FIJOS</h2>
        <p class="subtitle">Generado el: {{ date('d/m/Y H:i') }} | Usuario: {{ auth()->user()->nombre }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>CÓDIGO</th>
                <th>DESCRIPCIÓN</th>
                <th>FECHA ADQ.</th>
                <th>VIDA ÚTIL</th>
                <th>VALOR ADQUISICIÓN</th>
                <th>VALOR RESIDUAL</th>
                <th>DEPRECIACIÓN ACUM.</th>
                <th>VALOR EN LIBROS</th>
                <th>ESTADO</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activos as $activo)
                <tr>
                    <td>{{ $activo->codigo }}</td>
                    <td>{{ Str::limit($activo->descripcion, 40) }}</td>
                    <td>{{ $activo->fecha_adquisicion }}</td>
                    <td class="text-right">{{ $activo->vida_util }} años</td>
                    <td class="text-right">C$ {{ number_format($activo->valor_adquisicion, 2) }}</td>
                    <td class="text-right">C$ {{ number_format($activo->valor_residual, 2) }}</td>
                    <td class="text-right">C$ {{ number_format($activo->depreciacion_acumulada, 2) }}</td>
                    <td class="text-right" style="font-weight: bold;">C$ {{ number_format($activo->valor_libros, 2) }}</td>
                    <td>{{ $activo->estadoActivo->nombre ?? '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f3f4f6; font-weight: bold;">
                <td colspan="4" class="text-right">TOTALES:</td>
                <td class="text-right">C$ {{ number_format($activos->sum('valor_adquisicion'), 2) }}</td>
                <td class="text-right">-</td>
                <td class="text-right">C$ {{ number_format($activos->sum('depreciacion_acumulada'), 2) }}</td>
                <td class="text-right">C$ {{ number_format($activos->sum('valor_libros'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>