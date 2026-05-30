<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generando PDF - Estado de Cuenta Masivo</title>
    @if ($status === 'processing')
    <meta http-equiv="refresh" content="3">
    @endif
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: #f3f4f6;
            color: #374151;
        }
        .card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 480px;
            width: 90%;
        }
        .spinner {
            border: 4px solid #e5e7eb;
            border-top: 4px solid #ef4444;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            animation: spin 1s linear infinite;
            margin: 0 auto 24px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        h1 { font-size: 1.25rem; margin: 0 0 8px; }
        p { color: #6b7280; margin: 0 0 24px; line-height: 1.5; }
        .btn {
            display: inline-block;
            background: #ef4444;
            color: white;
            padding: 12px 32px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
        }
        .btn:hover { background: #dc2626; }
        .error-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
        .error-detail {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 12px;
            font-size: 0.8rem;
            color: #dc2626;
            margin-top: 16px;
            text-align: left;
            word-break: break-all;
        }
        .check-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="card">
        @if ($status === 'processing')
            <div class="spinner"></div>
            <h1>Generando PDF</h1>
            <p>Estamos procesando {{ $totalPredios ?? 'los' }} predios seleccionados.</p>
            <p style="font-size:0.9rem;color:#6b7280;">
                @if(!empty($totalChunks))
                    Lote {{ $completedChunks ?? 0 }} de {{ $totalChunks }}
                @endif
            </p>
            <p style="font-size:0.8rem;color:#9ca3af;">La página se actualizará automáticamente.</p>
        @elseif ($status === 'ready')
            <div class="check-icon">&#10003;</div>
            <h1>PDF Listo</h1>
            <p>El archivo se ha generado correctamente.</p>
            <a href="{{ route('estado-cuenta-masivo.download', ['token' => $token]) }}" class="btn">Descargar PDF</a>
        @elseif ($status === 'error')
            <div class="error-icon">&#10007;</div>
            <h1>Error al generar PDF</h1>
            <p>Ocurrió un problema durante la generación.</p>
            @if (!empty($error))
                <div class="error-detail">{{ $error }}</div>
            @endif
            <p style="margin-top:24px;">
                <a href="{{ route('estado-cuenta-masivo.index') }}" style="color:#ef4444;">Volver al listado</a>
            </p>
        @endif
    </div>
</body>
</html>