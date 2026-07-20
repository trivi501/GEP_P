import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';
import { useState } from 'react';

export default function Index({ permissions, filters }) {
    const [search, setSearch] = useState(filters || { name: '', nombre_mostrar: '', categoria: '', guard_name: '' });

    const handleFilter = (field, value) => {
        setSearch({ ...search, [field]: value });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('permissions.index'), search, { preserveState: true, replace: true });
    };

    const handleKeyDown = (e) => {
        if (e.key === 'Enter') {
            handleSearch(e);
        }
    };

    const inputClass = "block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-2 py-1";

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Permisos
                </h2>
            }
        >
            <Head title="Permisos" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Listado de Permisos
                                </h3>
                                <Link
                                    href={route('permissions.create')}
                                    className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-indigo-500 focus:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700"
                                >
                                    + Crear Permiso
                                </Link>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Nombre (Clave)
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Nombre Mostrar
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Categoría
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Guard Name
                                            </th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Acciones
                                            </th>
                                        </tr>
                                        <tr className="border-t border-gray-200 dark:border-gray-600">
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={search.name ?? ''}
                                                    onChange={(e) => handleFilter('name', e.target.value)}
                                                    onKeyDown={handleKeyDown}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={search.nombre_mostrar ?? ''}
                                                    onChange={(e) => handleFilter('nombre_mostrar', e.target.value)}
                                                    onKeyDown={handleKeyDown}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={search.categoria ?? ''}
                                                    onChange={(e) => handleFilter('categoria', e.target.value)}
                                                    onKeyDown={handleKeyDown}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={search.guard_name ?? ''}
                                                    onChange={(e) => handleFilter('guard_name', e.target.value)}
                                                    onKeyDown={handleKeyDown}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {permissions.data?.length > 0 ? (
                                            permissions.data.map((permission) => (
                                                <tr key={permission.id} className="hover:bg-gray-50 dark:bg-gray-700">
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {permission.name}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {permission.nombre_mostrar ?? '—'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        <span className="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                            {permission.categoria ?? '—'}
                                                        </span>
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {permission.guard_name}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                        <Link
                                                            href={route('permissions.show', permission.id)}
                                                            className="text-indigo-600 hover:text-indigo-900"
                                                        >
                                                            Ver
                                                        </Link>
                                                        <Link
                                                            href={route('permissions.edit', permission.id)}
                                                            className="ml-3 text-yellow-600 hover:text-yellow-900"
                                                        >
                                                            Editar
                                                        </Link>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td
                                                    colSpan="5"
                                                    className="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    No hay permisos registrados.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {permissions.links && <Pagination meta={permissions} />}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
