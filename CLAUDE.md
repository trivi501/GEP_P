# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Descripción del proyecto

GEP_P (`gpe_predial`) es un sistema municipal de administración del **impuesto predial** (impuesto sobre inmuebles), construido con Laravel 13 + Inertia.js + React. Gestiona contribuyentes, predios (urbanos y rústicos), cálculo de impuestos, cobro en cajas, convenios de pago, órdenes de pago y estados de cuenta. La interfaz de usuario y el dominio están en español.

## Comandos

### Entorno de desarrollo
```bash
composer dev
```
Levanta en paralelo el servidor PHP (`php artisan serve`), el worker de colas (`php artisan queue:listen --tries=1`) y Vite (`npm run dev`). Es el comando habitual para desarrollar.

### PHP / Laravel
```bash
php artisan serve            # solo el servidor HTTP
php artisan queue:listen --tries=1   # solo el worker de colas (necesario para EstadoCuentaMasivo/PDFs en background)
php artisan migrate
php artisan tinker
```

### Frontend
```bash
npm run dev        # Vite en modo desarrollo
npm run build       # build de producción
npm run build:ssr    # build con SSR
```

### Tests (Pest)
```bash
composer test                       # limpia config y corre php artisan test
php artisan test                    # equivalente directo
php artisan test --filter=NombreTest       # correr un test o clase específica
php artisan test tests/Feature/ProfileTest.php   # correr un archivo específico
```
Los tests usan sqlite en memoria (`phpunit.xml`) y Pest (`pestphp/pest`, `pest-plugin-laravel`).

### Lint / formato
```bash
vendor/bin/pint           # formateo de código PHP (Laravel Pint)
```

### Laravel Boost (MCP)
El proyecto tiene instalado `laravel/boost`. `opencode.json` registra un servidor MCP local vía `php artisan boost:mcp`, que expone herramientas para introspección de rutas, modelos, DB, logs, etc. Útil si el agente tiene acceso a MCP.

## Arquitectura

### Stack
- **Backend**: Laravel 13 (PHP 8.3), autenticación con Laravel UI/Breeze, autorización con `spatie/laravel-permission` (roles y permisos).
- **Frontend**: Inertia.js v2 + React 18 (sin API REST separada: los controladores devuelven `Inertia::render(...)` directamente).
- **Estilos**: Tailwind CSS v3 + Bootstrap 5 conviven en el proyecto.
- **Build**: Vite con `laravel-vite-plugin` y `@vitejs/plugin-react`.
- **PDFs**: `barryvdh/laravel-dompdf` y `setasign/fpdi-fpdf` para recibos, cédulas y estados de cuenta.
- **Excel**: `maatwebsite/excel` para exportaciones (ver `app/Exports/`).
- **QR**: `simplesoftwareio/simple-qrcode`.
- **Logs**: `opcodesio/log-viewer` y `rap2hpoutre/laravel-log-viewer` (ruta `/logs`).

### Flujo de página (Inertia)
Cada recurso sigue el patrón: `routes/web.php` → `app/Http/Controllers/XController.php` → `Inertia::render('Recurso/Accion', [...])` → `resources/js/Pages/Recurso/Accion.jsx`. Las páginas comparten `Layouts/AuthenticatedLayout.jsx` o `Layouts/GuestLayout.jsx`. No existe una capa de API JSON tradicional para el frontend; los datos viajan como props de Inertia en cada respuesta.

Props globales compartidas en cada request (`app/Http/Middleware/HandleInertiaRequests.php`): `auth.user` (con su `secretaria`), `userRoles` y `userPermissions`. En el frontend, `resources/js/Hooks/usePermissions.js` expone `can(permiso)` para condicionar UI; `'Super Admin'` y `'Admin'` tienen acceso total.

### Autorización
- Middleware `superadmin` (`app/Http/Middleware/SuperAdminMiddleware.php`) protege gestión de permisos, roles y usuarios (`routes/web.php`).
- Middlewares `role`, `permission`, `role_or_permission` de Spatie están registrados como alias en `bootstrap/app.php` y se usan por ruta (ej. `permission:ExportarPagos`).
- `App\Console\Commands\SyncSuperadminPermissions` sincroniza permisos del superadmin.

### Dominio del predial (núcleo del negocio)
- **Contribuyente**: persona/entidad que paga el impuesto. Puede tener varios `Predio`s.
- **Predio**: inmueble gravado. Se subdivide en `DatosPredioUrbano` o `DatosPredioRustico` según tipo (`TipoPredio`), con relaciones a colonia (`CatColonia`), calle (`CatCalle`), clave catastral (`ClavePredial`), zona (`CatZonaPredio`), etc.
- **Cálculo del impuesto**: `app/Services/CalculoPredialUrbanoService.php` es la lógica central de cálculo urbano — combina valor del terreno (superficie × tasa de zona × UMA), recargo por baldío, valor de construcción y un mínimo por UMA. Usa catálogos anuales (`CatUma`, `CatTasaImpuestoPorSuperficieUrbano`, `CatTasaXBaldioUrbano`, `CatTasaXConstruccion`, `CatFactoresRequerimiento`) que varían por año fiscal — cualquier cambio en reglas fiscales normalmente implica actualizar catálogos, no la fórmula. El resultado se persiste en `PredioCalculoGeneral`. `CalculosPrediosController` expone el cálculo bajo demanda y genera PDFs (urbano/rústico).
- **Pagos y cajas**: `PagosController` (el controlador más grande del proyecto) maneja cobro individual, caja general, historial, recibos, cancelaciones y corte de caja. `Caja`/`Cajero` representan las cajas registradoras y sus operadores; `CorteCaja`/`HistorialCaja` registran cortes de turno. `MultiPagosController` permite pagar varios predios en una sola operación.
- **Convenios**: `ConvenioMaster`/`ConvenioDetalle`/`ConvenioPago` modelan convenios de pago a plazos, con `CatEstadoConvenio` y `CatPeriodicidadConvenio`.
- **Órdenes de pago y cuentas**: `OrdenPagoController`/`CuentasController` gestionan órdenes de pago y cuentas por secretaría (`CuentasPorSecretaria`), con exportación a Excel (`CuentasExport`, `PagosGeneralesExport`).
- **Estado de cuenta masivo**: generación de PDFs en background vía jobs (`app/Jobs/GenerateEstadoCuentaChunk.php`, `app/Jobs/FinalizeEstadoCuentaPdf.php`) y comando (`app/Console/Commands/GenerateEstadoCuentaPdf.php`), con progreso consultable vía token (`EstadoCuentaMasivoController::progress/download`). Requiere el worker de colas corriendo (`composer dev` ya lo incluye).
- **Descuentos**: `DescuentosController`/`Descuento` aplican descuentos a predios específicos, buscados por predio.
- **Soporte**: `SupportTicketController` implementa un sistema simple de tickets con comentarios y notificaciones (`app/Notifications/`), reflejado en `NotificationBell.jsx` del frontend.

### Convenciones a tener en cuenta
- Muchos nombres de columnas y campos de catálogos están en mayúsculas o mezclan mayúsculas/minúsculas de forma inconsistente (ej. `ANIO` vs `año`, `Baldio`) — revisar el modelo/migración exacta antes de asumir el nombre de una columna.
- El dominio y la UI están en español; mantener nombres de variables, rutas y componentes nuevos en el mismo idioma que el código circundante para consistencia.
- No hay API REST separada: al añadir una funcionalidad nueva, el patrón esperado es controlador Laravel + página Inertia/React, no un endpoint JSON consumido por fetch (salvo los pocos endpoints de "search"/autocomplete usados por `SearchSelect.jsx`).
