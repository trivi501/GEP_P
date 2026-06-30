<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncSuperadminPermissions extends Command
{
    protected $signature = 'permisos:sync-superadmin';
    protected $description = 'Asigna todos los permisos al rol Superadmin';

    public function handle()
    {
        $role = Role::where('name', 'Superadmin')->first()
            ?? Role::where('name', 'superadmin')->first()
            ?? Role::where('name', 'Admin')->first()
            ?? Role::where('name', 'admin')->first();

        if (!$role) {
            $role = Role::create(['name' => 'Superadmin']);
            $this->info('Rol Superadmin creado.');
        }

        $permisos = Permission::all();
        $role->syncPermissions($permisos);

        $this->info($permisos->count() . ' permisos asignados al rol: ' . $role->name);
    }
}
