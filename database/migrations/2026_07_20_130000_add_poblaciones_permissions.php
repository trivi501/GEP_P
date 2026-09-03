<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'poblaciones-index', 'nombre_mostrar' => 'Ver Poblaciones', 'categoria' => 'Catastro'],
            ['name' => 'poblaciones-create', 'nombre_mostrar' => 'Crear Poblaciones', 'categoria' => 'Catastro'],
            ['name' => 'poblaciones-edit', 'nombre_mostrar' => 'Editar Poblaciones', 'categoria' => 'Catastro'],
            ['name' => 'poblaciones-delete', 'nombre_mostrar' => 'Eliminar Poblaciones', 'categoria' => 'Catastro'],
        ];

        foreach ($permissions as $data) {
            Permission::firstOrCreate(
                ['name' => $data['name'], 'guard_name' => 'web'],
                ['nombre_mostrar' => $data['nombre_mostrar'], 'categoria' => $data['categoria']]
            );
        }

        $permissionNames = array_column($permissions, 'name');

        foreach (['Admin', 'Super Admin', 'Catastro'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            $role?->givePermissionTo($permissionNames);
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', [
            'poblaciones-index', 'poblaciones-create', 'poblaciones-edit', 'poblaciones-delete',
        ])->delete();
    }
};
