<!DOCTYPE html>
<html>

<head>
    <title>Reporte de Usuarios y Accesos</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #2D2E83;
            /* Official Navy Blue */
            padding-bottom: 10px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            color: #2D2E83;
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
            background-color: #2D2E83;
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

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <table style="width: 100%; border-bottom: 2px solid #2D2E83; margin-bottom: 10px;">
            <tr>
                <td style="width: 20%; border: none; text-align: left; vertical-align: middle;">
                    <img src="{{ public_path('logo_sebaco.jpg') }}" alt="Logo" style="max-width: 80px; height: auto;">
                </td>
                <td style="width: 60%; border: none; text-align: center; vertical-align: middle;">
                    <h1 style="margin: 0; font-size: 16px; text-transform: uppercase; color: #111;">ALCALDÍA MUNICIPAL
                        DE SÉBACO</h1>
                    <p style="font-size: 12px; font-weight: bold; color: #2D2E83; margin: 5px 0;">DEPARTAMENTO DE
                        ACTIVOS FIJOS</p>
                    <h2 class="title" style="margin: 5px 0; font-size: 14px; text-align: center; color: #2D2E83;">
                        Reporte de Usuarios y Accesos</h2>
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
                <th>Nombre</th>
                <th>Correo Electrónico</th>
                <th>Rol Asignado</th>
                <th class="text-center">Estado</th>
                <th>Último Ingreso</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $user)
                <tr>
                    <td><strong>{{ $user->nombre }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->nombre ?? ($user->rol ?? 'Sin Rol') }}</td>
                    <td class="text-center">
                        @if($user->activo)
                            <span style="color: #059669; font-weight: bold;">Activo</span>
                        @else
                            <span style="color: #dc2626; font-weight: bold;">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        {{ $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at)->format('d/m/Y h:i A') : 'Nunca' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>