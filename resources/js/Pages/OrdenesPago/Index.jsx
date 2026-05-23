import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';

export default function Index({ ordenes, filters: initialFilters }) {
    const [filters, setFilters] = useState(initialFilters ?? {});

    const setFilter = (key, value) => {
        setFilters(prev => ({ ...prev, [key]: value }));
    };

    const handleSearch = (e) => {
        e.preventDefault();
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([k, v]) => { if (v) params.set(k, v); });
        window.location.href = route('ordenes-pago.index') + '?' + params.toString();
    };

    const limpiar = () => {
        window.location.href = route('ordenes-pago.index');
    };

    const hasFilters = Object.values(filters).some(Boolean);

    const isVencida = (orden) => {
        if (!orden.fecha_vencimiento || orden.pagado) return false;
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        const vence = new Date(orden.fecha_vencimiento + 'T00:00:00');
        return hoy > vence;
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Órdenes de Pago
                </h2>
            }
        >
            <Head title="Órdenes de Pago" />

            <div className="py-12">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Listado de Órdenes de Pago
                                </h3>
                                <Link
                                    href={route('ordenes-pago.create')}
                                    className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-indigo-500 focus:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700"
                                >
                                    + Nueva Orden
                                </Link>
                            </div>

                            <form onSubmit={handleSearch} className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Folio</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Nombre</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Secretaría</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Vence</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Monto</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Estatus</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Usuario</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cuentas</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                                        </tr>
                                        <tr className="bg-gray-100 dark:bg-gray-800">
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={filters.search_folio ?? ''}
                                                    onChange={(e) => setFilter('search_folio', e.target.value)}
                                                    placeholder="Folio"
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={filters.search_nombre ?? ''}
                                                    onChange={(e) => setFilter('search_nombre', e.target.value)}
                                                    placeholder="Nombre"
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={filters.search_secretaria ?? ''}
                                                    onChange={(e) => setFilter('search_secretaria', e.target.value)}
                                                    placeholder="Secretaría"
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="date"
                                                    value={filters.search_fecha ?? ''}
                                                    onChange={(e) => setFilter('search_fecha', e.target.value)}
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                />
                                            </th>
                                            <th className="px-2 py-2"></th>
                                            <th className="px-2 py-2"></th>
                                            <th className="px-2 py-2">
                                                <select
                                                    value={filters.search_estatus ?? ''}
                                                    onChange={(e) => setFilter('search_estatus', e.target.value)}
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                >
                                                    <option value="">Todos</option>
                                                    <option value="pendiente">Pendiente</option>
                                                    <option value="pagado">Pagado</option>
                                                </select>
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={filters.search_usuario ?? ''}
                                                    onChange={(e) => setFilter('search_usuario', e.target.value)}
                                                    placeholder="Usuario"
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                />
                                            </th>
                                            <th className="px-2 py-2"></th>
                                            <th className="px-2 py-2 text-right">
                                                <div className="flex gap-1">
                                                    <button
                                                        type="submit"
                                                        className="inline-flex items-center rounded bg-indigo-600 px-2 py-1 text-xs font-semibold text-white hover:bg-indigo-500"
                                                    >
                                                        Buscar
                                                    </button>
                                                    {hasFilters && (
                                                        <button
                                                            type="button"
                                                            onClick={limpiar}
                                                            className="inline-flex items-center rounded bg-gray-500 px-2 py-1 text-xs font-semibold text-white hover:bg-gray-400"
                                                        >
                                                            X
                                                        </button>
                                                    )}
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {ordenes.data?.length > 0 ? (
                                            ordenes.data.map((orden) => {
                                                const vencida = isVencida(orden);
                                                return (
                                                <tr key={orden.id} className={`hover:bg-gray-50 dark:bg-gray-700 ${vencida ? 'opacity-60' : ''}`}>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {orden.folio ?? ('#' + orden.id)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400 max-w-[200px] truncate">
                                                        {orden.nombre}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {orden.secretaria?.nombre ?? '—'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {orden.fecha ?? '—'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm">
                                                        {orden.fecha_vencimiento ? (
                                                            <span className={vencida ? 'text-red-600 font-semibold' : 'text-gray-500 dark:text-gray-400'}>
                                                                {orden.fecha_vencimiento}{vencida ? ' (Vencida)' : ''}
                                                            </span>
                                                        ) : '—'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                                        ${parseFloat(orden.monto).toFixed(2)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-center">
                                                        {orden.pagado ? (
                                                            <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">Pagado</span>
                                                        ) : vencida ? (
                                                            <span className="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">Vencida</span>
                                                        ) : (
                                                            <span className="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pendiente</span>
                                                        )}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {orden.user?.name ?? '—'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                                        {orden.cuentas_ordenes_pago?.length ?? 0}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                        <Link href={route('ordenes-pago.show', orden.id)} className="text-indigo-600 hover:text-indigo-900">Ver</Link>
                                                        {!orden.pagado && !vencida && (
                                                            <Link href={route('ordenes-pago.edit', orden.id)} className="ml-3 text-yellow-600 hover:text-yellow-900">Editar</Link>
                                                        )}
                                                    </td>
                                                </tr>
                                                );
                                            })
                                        ) : (
                                            <tr>
                                                <td colSpan="10" className="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    No hay órdenes de pago registradas.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </form>

                            {ordenes.links && <Pagination meta={ordenes} />}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
