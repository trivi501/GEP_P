<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Cédula Catastral - {{ $predio->Clave_predial }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10pt; }
        h1 { text-align: center; font-size: 14pt; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: left; }
        th { background: #eee; }
        .label { font-weight: bold; width: 35%; }
    </style>
</head>
<body>
    <h1>Cédula Catastral</h1>
    <table>
        <tr><td class="label">Clave Catastral</td><td>{{ $predio->Clave_predial }}</td></tr>
        <tr><td class="label">Ubicación</td><td>{{ $predio->ubicacion }}</td></tr>
        <tr><td class="label">Contribuyente</td><td>{{ $predio->contribuyente?->nombre_completo ?? '—' }}</td></tr>
        <tr><td class="label">RFC</td><td>{{ $predio->contribuyente?->rfc ?? '—' }}</td></tr>
        <tr><td class="label">Superficie</td><td>{{ number_format($predio->superficie, 2) }} m²</td></tr>
        <tr><td class="label">Construcción</td><td>{{ number_format($predio->construccion, 2) }} m²</td></tr>
        <tr><td class="label">Tipo Predio</td><td>{{ $predio->tipoPredio?->Tipo_predio ?? '—' }}</td></tr>
        <tr><td class="label">Valor Catastral</td><td>${{ number_format($predio->valor_catastral ?? 0, 2) }}</td></tr>
    </table>
</body>
</html>