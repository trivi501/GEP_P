import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Show({ secretaria }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Detalle de Secretaría
                </h2>
            }
        >
            <Head title="Detalle de Secretaría" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium">Secretaría: {secretaria.nombre}</h3>
                                <Link
                                    href={route('secretarias.edit', secretaria.id)}
                                    className="inline-flex items-center rounded-md border border-transparent bg-yellow-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-yellow-400"
                                >
                                    Editar
                                </Link>
                            </div>

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">ID</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{secretaria.id}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{secretaria.nombre}</p>
                                </div>
                            </div>

                            <div className="mt-8">
                                <h4 className="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Cuentas asignadas</h4>
                                {secretaria.cuentas?.length > 0 ? (
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">ID</th>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cuenta</th>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Descripción</th>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Indetec</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                {secretaria.cuentas.map((cuenta) => (
                                                    <tr key={cuenta.id} className="hover:bg-gray-50 dark:bg-gray-700">
                                                        <td className="whitespace-nowrap px-4 py-2 text-sm">{cuenta.id}</td>
                                                        <td className="whitespace-nowrap px-4 py-2 text-sm">{cuenta.cuenta ?? '—'}</td>
                                                        <td className="whitespace-nowrap px-4 py-2 text-sm">{cuenta.descripcion ?? '—'}</td>
                                                        <td className="whitespace-nowrap px-4 py-2 text-sm">{cuenta.indetec ?? '—'}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                ) : (
                                    <p className="text-sm text-gray-500">No hay cuentas asignadas.</p>
                                )}
                            </div>

                            <div className="mt-8">
                                <h4 className="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Usuarios en esta secretaría</h4>
                                {secretaria.users?.length > 0 ? (
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">ID</th>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Nombre</th>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Email</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                {secretaria.users.map((user) => (
                                                    <tr key={user.id} className="hover:bg-gray-50 dark:bg-gray-700">
                                                        <td className="whitespace-nowrap px-4 py-2 text-sm">{user.id}</td>
                                                        <td className="whitespace-nowrap px-4 py-2 text-sm">{user.name}</td>
                                                        <td className="whitespace-nowrap px-4 py-2 text-sm">{user.email}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                ) : (
                                    <p className="text-sm text-gray-500">No hay usuarios en esta secretaría.</p>
                                )}
                            </div>

                            <div className="mt-6">
                                <Link
                                    href={route('secretarias.index')}
                                    className="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700"
                                >
                                    Volver
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
