import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';
import { useState } from 'react';

export default function Index({ calles }) {
    const params = new URLSearchParams(window.location.search);
    const [search, setSearch] = useState(params.get('search') ?? '');

    const handleSearch = (e) => {
        if (e.key === 'Enter') {
            router.get(route('calles.index'), search ? { search } : {}, { preserveState: true, replace: true });
        }
    };

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">Calles</h2>}>
            <Head title="Calles" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium">Listado de Calles</h3>
                                <Link href={route('calles.create')} className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-500">+ Nueva Calle</Link>
                            </div>
                            <div className="mb-4">
                                <input
                                    type="text"
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    onKeyDown={handleSearch}
                                    placeholder="Buscar por calle o colonia... (Enter)"
                                    className="block w-full max-w-md rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ID</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Calle</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Colonia</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Activo</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {calles.data?.length > 0 ? calles.data.map((calle) => (
                                            <tr key={calle.id_calle} className="hover:bg-gray-50 dark:bg-gray-700">
                                                <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{calle.id_calle}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{calle.CALLE}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{calle.colonia?.COLONIA ?? '—'}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-center text-sm">{calle.Activo ? <span className="text-green-600">Sí</span> : <span className="text-red-600">No</span>}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                    <Link href={route('calles.show', calle.id_calle)} className="text-indigo-600 hover:text-indigo-900">Ver</Link>
                                                    <Link href={route('calles.edit', calle.id_calle)} className="ml-3 text-yellow-600 hover:text-yellow-900">Editar</Link>
                                                </td>
                                            </tr>
                                        )) : (
                                            <tr><td colSpan="5" className="px-6 py-4 text-center text-sm text-gray-500">No hay calles registradas.</td></tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            {calles.links && <Pagination meta={calles} />}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
