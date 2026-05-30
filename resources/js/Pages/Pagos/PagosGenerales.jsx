import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';

export default function PagosGenerales({ pagos, filters: initialFilters }) {
    const [cancelModal, setCancelModal] = useState(null);
    const [canceling, setCanceling] = useState(false);
    const [localPagos, setLocalPagos] = useState(pagos);
    const [filters, setFilters] = useState(initialFilters ?? {});

    const setFilter = (key, value) => {
        setFilters(prev => ({ ...prev, [key]: value }));
    };

    const handleSearch = (e) => {
        e.preventDefault();
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([k, v]) => { if (v) params.set(k, v); });
        window.location.href = route('pagos.pagos-generales') + '?' + params.toString();
    };

    const limpiar = () => {
        window.location.href = route('pagos.pagos-generales');
    };

    const hasFilters = Object.values(filters).some(Boolean);

    const handleCancel = async () => {
        if (!cancelModal) return;
        setCanceling(true);

        const routeName = cancelModal.orden_pago_id
            ? 'pagos.caja-general.cancelar'
            : 'pagos.cancelar';

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            const response = await fetch(route(routeName, cancelModal.id), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                setLocalPagos(prev => ({
                    ...prev,
                    data: prev.data.map(p =>
                        p.id === cancelModal.id ? { ...p, estatus: 'cancelado' } : p
                    ),
                }));
            }
        } catch (e) {
            console.error('Error al cancelar', e);
        } finally {
            setCanceling(false);
            setCancelModal(null);
        }
    };

    const pagosToRender = localPagos;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Pagos Generales
                </h2>
            }
        >
            <Head title="Pagos Generales" />

            <div className="py-12 w-full">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">Todos los Pagos Realizados</h3>
                            </div>

                            <form onSubmit={handleSearch} className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Folio</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nombre</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Monto</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estatus</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acción</th>
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
                                                    type="date"
                                                    value={filters.search_fecha ?? ''}
                                                    onChange={(e) => setFilter('search_fecha', e.target.value)}
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
                                                <select
                                                    value={filters.search_tipo_pago ?? ''}
                                                    onChange={(e) => setFilter('search_tipo_pago', e.target.value)}
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                >
                                                    <option value="">Todos</option>
                                                    <option value="predial_urbano">Urbano</option>
                                                    <option value="predial_rustico">Rústico</option>
                                                    <option value="Ingresos">Ingresos</option>
                                                </select>
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={filters.search_monto ?? ''}
                                                    onChange={(e) => setFilter('search_monto', e.target.value)}
                                                    placeholder="Monto"
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <select
                                                    value={filters.search_estatus ?? ''}
                                                    onChange={(e) => setFilter('search_estatus', e.target.value)}
                                                    className="block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                >
                                                    <option value="">Todos</option>
                                                    <option value="pagado">Pagado</option>
                                                    <option value="cancelado">Cancelado</option>
                                                </select>
                                            </th>
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
                                    <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        {pagosToRender.data?.length > 0 ? (
                                            pagosToRender.data.map((pago) => (
                                                <tr key={pago.id} className="hover:bg-gray-50 dark:bg-gray-700">
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">{pago.folio}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">{pago.fecha ?? '—'}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm max-w-[200px] truncate">{pago.nombre}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                        {pago.tipo_pago === 'predial_rustico' ? (
                                                            <span className="inline-flex items-center gap-1 text-xs font-medium text-green-700">Rústico</span>
                                                        ) : pago.tipo_pago === 'predial_urbano' ? (
                                                            <span className="inline-flex items-center gap-1 text-xs font-medium text-blue-700">Urbano</span>
                                                        ) : (
                                                            <span className="inline-flex items-center gap-1 text-xs font-medium text-purple-700">{pago.tipo_pago}</span>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">${parseFloat(pago.monto ?? 0).toFixed(2)}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                        {pago.estatus === 'pagado' ? (
                                                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Pagado</span>
                                                        ) : pago.estatus === 'cancelado' ? (
                                                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Cancelado</span>
                                                        ) : (
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{pago.estatus}</span>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                        <div className="flex items-center justify-center gap-2">
                                                            <a
                                                                href={route('pagos.recibo', pago.id)}
                                                                target="_blank"
                                                                className="inline-flex items-center justify-center w-7 h-7 rounded text-blue-600 hover:bg-blue-50"
                                                                title="Ver Recibo"
                                                            >
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                            </a>
                                                            {pago.estatus === 'pagado' && (
                                                                <button
                                                                    type="button"
                                                                    onClick={() => setCancelModal(pago)}
                                                                    className="inline-flex items-center justify-center w-7 h-7 rounded text-red-600 hover:bg-red-50"
                                                                    title="Cancelar Pago"
                                                                >
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                </button>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="7" className="px-6 py-4 text-center text-sm text-gray-500">
                                                    No hay pagos registrados.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </form>

                            {pagos.links && <Pagination meta={pagos} />}
                        </div>
                    </div>
                </div>
            </div>

            {cancelModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                        <h3 className="text-lg font-semibold mb-2">Cancelar pago</h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            ¿Estás seguro de cancelar este pago?
                        </p>
                        <p className="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            <strong>Folio:</strong> {cancelModal.folio}<br />
                            <strong>Monto:</strong> ${parseFloat(cancelModal.monto ?? 0).toFixed(2)}<br />
                            <strong>Fecha:</strong> {cancelModal.fecha}
                        </p>
                        <div className="flex justify-end gap-2">
                            <button
                                onClick={() => setCancelModal(null)}
                                className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                No, volver
                            </button>
                            <button
                                onClick={handleCancel}
                                disabled={canceling}
                                className="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-500 disabled:opacity-50"
                            >
                                {canceling ? 'Cancelando...' : 'Sí, cancelar pago'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
