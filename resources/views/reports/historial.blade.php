<!DOCTYPE html>
<html>

<head>
    <title>Historial de Acciones</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #F59E0B;
            padding-bottom: 10px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #d97706;
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
            background-color: #F59E0B;
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
                    <h2 class="title" style="margin: 5px 0; font-size: 14px; text-align: center; color: #d97706;">
                        HISTORIAL DE ACCIONES Y MOVIMIENTOS</h2>
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
                <th>Fecha</th>
                <th>Activo</th>
                <th>Origen</th>
                <th>Destino</th>
                <th>Responsable Ant.</th>
                <th>Responsable Nuevo</th>
                <th>Autorizado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $mov)
                <tr>
                    <td>{{ $mov->fecha_movimiento }}</td>
                    <td>
                        <strong>{{ $mov->activoFijo->codigo ?? 'N/A' }}</strong><br>
                        <span
                            style="font-size: 8px; color: #666;">{{ Str::limit($mov->activoFijo->descripcion ?? '', 20) }}</span>
                    </td>
                    <td>{{ $mov->ubicacionOrigen->nombre ?? '-' }}</td>
                    <td>{{ $mov->ubicacionDestino->nombre ?? '-' }}</td>
                    <td>{{ $mov->responsableOrigen->nombre_completo ?? '-' }}</td>
                    <td><strong>{{ $mov->responsableDestino->nombre_completo ?? '-' }}</strong></td>
                    <td>{{ $mov->autorizadoPor->nombre ?? 'Sistema' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No se encontraron movimientos registrados en
                        el periodo seleccionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>

</html>