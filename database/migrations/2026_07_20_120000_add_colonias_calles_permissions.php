<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'colonias-index', 'nombre_mostrar' => 'Ver Colonias', 'categoria' => 'Catastro'],
            ['name' => 'colonias-create', 'nombre_mostrar' => 'Crear Colonias', 'categoria' => 'Catastro'],
            ['name' => 'colonias-edit', 'nombre_mostrar' => 'Editar Colonias', 'categoria' => 'Catastro'],
            ['name' => 'colonias-delete', 'nombre_mostrar' => 'Eliminar Colonias', 'categoria' => 'Catastro'],
            ['name' => 'calles-index', 'nombre_mostrar' => 'Ver Calles', 'categoria' => 'Catastro'],
            ['name' => 'calles-create', 'nombre_mostrar' => 'Crear Calles', 'categoria' => 'Catastro'],
            ['name' => 'calles-edit', 'nombre_mostrar' => 'Editar Calles', 'categoria' => 'Catastro'],
            ['name' => 'calles-delete', 'nombre_mostrar' => 'Eliminar Calles', 'categoria' => 'Catastro'],
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
            'colonias-index', 'colonias-create', 'colonias-edit', 'colonias-delete',
            'calles-index', 'calles-create', 'calles-edit', 'calles-delete',
        ])->delete();
    }
};
