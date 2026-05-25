import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';

const allModules = [
    { name: 'Predios', href: 'predios.index', color: 'bg-blue-500', icon: '🏠', permiso: 'predios-index' },
    { name: 'Contribuyentes', href: 'contribuyentes.index', color: 'bg-green-500', icon: '👤', permiso: 'contribuyentes-index' },
    { name: 'Cajas', href: 'cajas.index', color: 'bg-yellow-500', icon: '📦', permiso: 'cajas-index' },
    { name: 'Cajeros', href: 'cajeros.index', color: 'bg-purple-500', icon: '🧑‍💼', permiso: 'cajeros-index' },
    { name: 'Cuentas', href: 'cuentas.index', color: 'bg-pink-500', icon: '📋', permiso: 'cuentas-index' },
    { name: 'Historial de Caja', href: 'pagos.index', color: 'bg-indigo-500', icon: '💰', permiso: 'caja-pago-index' },
    { name: 'Historial de Pagos', href: 'pagos.historial', color: 'bg-teal-500', icon: '📜', permiso: 'predios-pay' },
    { name: 'Órdenes de Pago', href: 'ordenes-pago.index', color: 'bg-orange-500', icon: '📄', permiso: 'ordenes-pago-index' },
    { name: 'Órdenes Pago Cajas', href: 'ordenes-pgo-cajas.index', color: 'bg-amber-500', icon: '💳', permiso: 'ordenes-pago-caja' },
    { name: 'Caja General', href: 'pagos.caja-general', color: 'bg-lime-500', icon: '🏦', permiso: 'caja-pago-index' },
    { name: 'Secretarías', href: 'secretarias.index', color: 'bg-cyan-500', icon: '🏛️', permiso: 'secretarias-index' },
    { name: 'Roles', href: 'roles.index', color: 'bg-red-500', icon: '🔐', permiso: 'roles-index' },
    { name: 'Permisos', href: 'permissions.index', color: 'bg-rose-500', icon: '⚙️', permiso: 'permissions-index' },
    { name: 'Usuarios', href: 'users.index', color: 'bg-gray-600', icon: '👥', permiso: 'users-index' },
];

export default function Dashboard() {
    const permissions = Array.isArray(usePage().props.userPermissions) ? usePage().props.userPermissions : [];
    const can = (permiso) => !permiso || permissions.includes(permiso);
    const modules = allModules.filter(m => can(m.permiso));
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Dashboard
                </h2>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {modules.map((mod) => (
                            <Link
                                key={mod.href}
                                href={route(mod.href)}
                                className="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200 dark:bg-gray-800 dark:border-gray-700"
                            >
                                <div className="flex items-center gap-4">
                                    <div className={`w-12 h-12 ${mod.color} rounded-lg flex items-center justify-center text-white text-xl`}>
                                        {mod.icon}
                                    </div>
                                    <div>
                                        <h3 className="text-lg font-semibold text-gray-800 dark:text-gray-100">{mod.name}</h3>
                                        <p className="text-sm text-gray-500 dark:text-gray-400">Ir al módulo &rarr;</p>
                                    </div>
                                </div>
                            </Link>
                        ))}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}