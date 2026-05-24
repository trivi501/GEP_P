<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Permisos
            ['name' => 'ver permisos', 'nombre_mostrar' => 'Ver Permisos', 'categoria' => 'Permisos'],
            ['name' => 'crear permisos', 'nombre_mostrar' => 'Crear Permisos', 'categoria' => 'Permisos'],
            ['name' => 'editar permisos', 'nombre_mostrar' => 'Editar Permisos', 'categoria' => 'Permisos'],
            ['name' => 'eliminar permisos', 'nombre_mostrar' => 'Eliminar Permisos', 'categoria' => 'Permisos'],

            // Roles
            ['name' => 'ver roles', 'nombre_mostrar' => 'Ver Roles', 'categoria' => 'Roles'],
            ['name' => 'crear roles', 'nombre_mostrar' => 'Crear Roles', 'categoria' => 'Roles'],
            ['name' => 'editar roles', 'nombre_mostrar' => 'Editar Roles', 'categoria' => 'Roles'],
            ['name' => 'eliminar roles', 'nombre_mostrar' => 'Eliminar Roles', 'categoria' => 'Roles'],

            // Usuarios
            ['name' => 'ver usuarios', 'nombre_mostrar' => 'Ver Usuarios', 'categoria' => 'Usuarios'],
            ['name' => 'crear usuarios', 'nombre_mostrar' => 'Crear Usuarios', 'categoria' => 'Usuarios'],
            ['name' => 'editar usuarios', 'nombre_mostrar' => 'Editar Usuarios', 'categoria' => 'Usuarios'],
            ['name' => 'eliminar usuarios', 'nombre_mostrar' => 'Eliminar Usuarios', 'categoria' => 'Usuarios'],

            // Contribuyentes
            ['name' => 'ver contribuyentes', 'nombre_mostrar' => 'Ver Contribuyentes', 'categoria' => 'Contribuyentes'],
            ['name' => 'crear contribuyentes', 'nombre_mostrar' => 'Crear Contribuyentes', 'categoria' => 'Contribuyentes'],
            ['name' => 'editar contribuyentes', 'nombre_mostrar' => 'Editar Contribuyentes', 'categoria' => 'Contribuyentes'],
            ['name' => 'eliminar contribuyentes', 'nombre_mostrar' => 'Eliminar Contribuyentes', 'categoria' => 'Contribuyentes'],

            // Predios
            ['name' => 'ver predios', 'nombre_mostrar' => 'Ver Predios', 'categoria' => 'Predios'],
            ['name' => 'crear predios', 'nombre_mostrar' => 'Crear Predios', 'categoria' => 'Predios'],
            ['name' => 'editar predios', 'nombre_mostrar' => 'Editar Predios', 'categoria' => 'Predios'],
            ['name' => 'eliminar predios', 'nombre_mostrar' => 'Eliminar Predios', 'categoria' => 'Predios'],
            ['name' => 'descargar pdf predios', 'nombre_mostrar' => 'Descargar PDF Predios', 'categoria' => 'Predios'],
            ['name' => 'ver cedula predios', 'nombre_mostrar' => 'Ver Cédula Predios', 'categoria' => 'Predios'],

            // Cajas
            ['name' => 'ver cajas', 'nombre_mostrar' => 'Ver Cajas', 'categoria' => 'Cajas'],
            ['name' => 'crear cajas', 'nombre_mostrar' => 'Crear Cajas', 'categoria' => 'Cajas'],
            ['name' => 'editar cajas', 'nombre_mostrar' => 'Editar Cajas', 'categoria' => 'Cajas'],
            ['name' => 'eliminar cajas', 'nombre_mostrar' => 'Eliminar Cajas', 'categoria' => 'Cajas'],

            // Cajeros
            ['name' => 'ver cajeros', 'nombre_mostrar' => 'Ver Cajeros', 'categoria' => 'Cajeros'],
            ['name' => 'crear cajeros', 'nombre_mostrar' => 'Crear Cajeros', 'categoria' => 'Cajeros'],
            ['name' => 'editar cajeros', 'nombre_mostrar' => 'Editar Cajeros', 'categoria' => 'Cajeros'],
            ['name' => 'eliminar cajeros', 'nombre_mostrar' => 'Eliminar Cajeros', 'categoria' => 'Cajeros'],

            // Cuentas
            ['name' => 'ver cuentas', 'nombre_mostrar' => 'Ver Cuentas', 'categoria' => 'Cuentas'],
            ['name' => 'crear cuentas', 'nombre_mostrar' => 'Crear Cuentas', 'categoria' => 'Cuentas'],
            ['name' => 'editar cuentas', 'nombre_mostrar' => 'Editar Cuentas', 'categoria' => 'Cuentas'],
            ['name' => 'eliminar cuentas', 'nombre_mostrar' => 'Eliminar Cuentas', 'categoria' => 'Cuentas'],

            // Pagos
            ['name' => 'ver pagos', 'nombre_mostrar' => 'Ver Pagos', 'categoria' => 'Pagos'],
            ['name' => 'registrar pago', 'nombre_mostrar' => 'Registrar Pago', 'categoria' => 'Pagos'],
            ['name' => 'cobrar pago', 'nombre_mostrar' => 'Cobrar Pago', 'categoria' => 'Pagos'],
            ['name' => 'cancelar pago', 'nombre_mostrar' => 'Cancelar Pago', 'categoria' => 'Pagos'],
            ['name' => 'ver recibo pago', 'nombre_mostrar' => 'Ver Recibo de Pago', 'categoria' => 'Pagos'],
            ['name' => 'corte caja', 'nombre_mostrar' => 'Corte de Caja', 'categoria' => 'Pagos'],
            ['name' => 'caja general', 'nombre_mostrar' => 'Caja General', 'categoria' => 'Pagos'],

            // Cálculos Prediales
            ['name' => 'ver calculos prediales', 'nombre_mostrar' => 'Ver Cálculos Prediales', 'categoria' => 'Cálculos Prediales'],
            ['name' => 'calcular predio', 'nombre_mostrar' => 'Calcular Predio', 'categoria' => 'Cálculos Prediales'],
            ['name' => 'descargar pdf calculos', 'nombre_mostrar' => 'Descargar PDF Cálculos', 'categoria' => 'Cálculos Prediales'],

            // Secretarías
            ['name' => 'ver secretarias', 'nombre_mostrar' => 'Ver Secretarías', 'categoria' => 'Secretarías'],
            ['name' => 'crear secretarias', 'nombre_mostrar' => 'Crear Secretarías', 'categoria' => 'Secretarías'],
            ['name' => 'editar secretarias', 'nombre_mostrar' => 'Editar Secretarías', 'categoria' => 'Secretarías'],
            ['name' => 'eliminar secretarias', 'nombre_mostrar' => 'Eliminar Secretarías', 'categoria' => 'Secretarías'],

            // Órdenes de Pago
            ['name' => 'ver ordenes pago', 'nombre_mostrar' => 'Ver Órdenes de Pago', 'categoria' => 'Órdenes de Pago'],
            ['name' => 'crear ordenes pago', 'nombre_mostrar' => 'Crear Órdenes de Pago', 'categoria' => 'Órdenes de Pago'],
            ['name' => 'editar ordenes pago', 'nombre_mostrar' => 'Editar Órdenes de Pago', 'categoria' => 'Órdenes de Pago'],
            ['name' => 'eliminar ordenes pago', 'nombre_mostrar' => 'Eliminar Órdenes de Pago', 'categoria' => 'Órdenes de Pago'],

            // Reportes
            ['name' => 'ver reportes', 'nombre_mostrar' => 'Ver Reportes', 'categoria' => 'Reportes'],
            ['name' => 'descargar reportes pdf', 'nombre_mostrar' => 'Descargar Reportes PDF', 'categoria' => 'Reportes'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'web'],
                ['nombre_mostrar' => $permission['nombre_mostrar'], 'categoria' => $permission['categoria']]
            );
        }
    }
}
