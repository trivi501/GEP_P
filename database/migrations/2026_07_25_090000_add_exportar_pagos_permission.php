<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Permission::firstOrCreate(
            ['name' => 'ExportarPagos', 'guard_name' => 'web'],
            ['nombre_mostrar' => 'Exportar Pagos Generales', 'categoria' => 'Cajas']
        );

        foreach (['Admin', 'Super Admin', 'Cajero'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            $role?->givePermissionTo('ExportarPagos');
        }
    }

    public function down(): void
    {
        Permission::where('name', 'ExportarPagos')->delete();
    }
};
