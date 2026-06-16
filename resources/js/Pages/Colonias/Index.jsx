import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';

export default function Index({ colonias }) {
    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">Colonias</h2>}>
            <Head title="Colonias" />
            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium">Listado de Colonias</h3>
                                <Link href={route('colonias.create')} className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition hover:bg-indigo-500">+ Nueva Colonia</Link>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ID</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Colonia</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Activo</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {colonias.data?.length > 0 ? colonias.data.map((colonia) => (
                                            <tr key={colonia.id_colonia} className="hover:bg-gray-50 dark:bg-gray-700">
                                                <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">{colonia.id_colonia}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{colonia.COLONIA}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-center text-sm">{colonia.Activo ? <span className="text-green-600">Sí</span> : <span className="text-red-600">No</span>}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                    <Link href={route('colonias.show', colonia.id_colonia)} className="text-indigo-600 hover:text-indigo-900">Ver</Link>
                                                    <Link href={route('colonias.edit', colonia.id_colonia)} className="ml-3 text-yellow-600 hover:text-yellow-900">Editar</Link>
                                                </td>
                                            </tr>
                                        )) : (
                                            <tr><td colSpan="4" className="px-6 py-4 text-center text-sm text-gray-500">No hay colonias registradas.</td></tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            {colonias.links && <Pagination meta={colonias} />}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
