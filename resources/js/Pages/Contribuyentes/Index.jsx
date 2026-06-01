import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';

export default function Index({ contribuyentes, filters = {} }) {
    const [search, setSearch] = useState({
        nombre_completo: filters.nombre_completo || '',
        cuenta: filters.cuenta || '',
        tipo: filters.tipo || '',
        telefono: filters.telefono || '',
        correo_electronico: filters.correo_electronico || '',
        activo: filters.activo ?? '',
    });

    const handleFilter = (key, value) => {
        const updated = { ...search, [key]: value };
        setSearch(updated);
    };

    const applyFilters = (e) => {
        e?.preventDefault();
        router.get(route('contribuyentes.index'), search, { preserveState: true, replace: true });
    };

    const clearFilters = () => {
        setSearch({ nombre_completo: '', cuenta: '', tipo: '', telefono: '', correo_electronico: '', activo: '' });
        router.get(route('contribuyentes.index'), {}, { preserveState: true, replace: true });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Contribuyentes
                </h2>
            }
        >
            <Head title="Contribuyentes" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between gap-4 flex-wrap">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Listado de Contribuyentes
                                </h3>
                                <div className="flex gap-2">
                                    <Link
                                        href={route('contribuyentes.create')}
                                        className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-indigo-500 focus:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700"
                                    >
                                        <svg className="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 4v16m8-8H4"/></svg>
                                        Crear Contribuyente
                                    </Link>
                                </div>
                            </div>

                            <form onSubmit={applyFilters} className="mb-4">
                                <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2">
                                    <div>
                                        <input
                                            type="text"
                                            placeholder="Cuenta"
                                            value={search.cuenta}
                                            onChange={(e) => handleFilter('cuenta', e.target.value)}
                                            className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs"
                                        />
                                    </div>
                                    <div>
                                        <input
                                            type="text"
                                            placeholder="Nombre"
                                            value={search.nombre_completo}
                                            onChange={(e) => handleFilter('nombre_completo', e.target.value)}
                                            className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs"
                                        />
                                    </div>
                                    <div>
                                        <input
                                            type="text"
                                            placeholder="Tipo"
                                            value={search.tipo}
                                            onChange={(e) => handleFilter('tipo', e.target.value)}
                                            className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs"
                                        />
                                    </div>
                                    <div>
                                        <input
                                            type="text"
                                            placeholder="Teléfono"
                                            value={search.telefono}
                                            onChange={(e) => handleFilter('telefono', e.target.value)}
                                            className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs"
                                        />
                                    </div>
                                    <div>
                                        <input
                                            type="text"
                                            placeholder="Correo"
                                            value={search.correo_electronico}
                                            onChange={(e) => handleFilter('correo_electronico', e.target.value)}
                                            className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs"
                                        />
                                    </div>
                                    <div className="flex gap-1">
                                        <select
                                            value={search.activo}
                                            onChange={(e) => handleFilter('activo', e.target.value)}
                                            className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-xs"
                                        >
                                            <option value="">Todos</option>
                                            <option value="1">Activo</option>
                                            <option value="0">Inactivo</option>
                                        </select>
                                        <button type="submit" className="px-3 py-1.5 bg-indigo-600 text-white rounded-md text-xs hover:bg-indigo-500">Buscar</button>
                                        <button type="button" onClick={clearFilters} className="px-3 py-1.5 bg-gray-300 text-gray-700 rounded-md text-xs hover:bg-gray-400">Limpiar</button>
                                    </div>
                                </div>
                            </form>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cuenta</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Nombre</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">RFC</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Teléfono</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Activo</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {contribuyentes.data?.length > 0 ? (
                                            contribuyentes.data.map((contribuyente) => (
                                                <tr key={contribuyente.id_contribuyente} className="hover:bg-gray-50 dark:bg-gray-700">
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {contribuyente.cuenta}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {contribuyente.nombre_completo}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {contribuyente.rfc}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {contribuyente.telefono}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm">
                                                        {contribuyente.activo ? (
                                                            <span className="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                                Activo
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                                                Inactivo
                                                            </span>
                                                        )}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                        <div className="flex items-center justify-end gap-3">
                                                            <Link
                                                                href={route('contribuyentes.show', contribuyente.id_contribuyente)}
                                                                className="text-indigo-600 hover:text-indigo-900"
                                                                title="Ver"
                                                            >
                                                                <svg className="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            </Link>
                                                            <Link
                                                                href={route('contribuyentes.edit', contribuyente.id_contribuyente)}
                                                                className="text-yellow-600 hover:text-yellow-900"
                                                                title="Editar"
                                                            >
                                                                <svg className="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                            </Link>
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td
                                                    colSpan="6"
                                                    className="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    No hay contribuyentes registrados.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {contribuyentes.links && <Pagination meta={contribuyentes} />}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
