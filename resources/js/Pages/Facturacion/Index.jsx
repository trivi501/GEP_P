import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function FacturacionIndex({ pagos, currentPage, lastPage, filters: initialFilters }) {
    const [rows, setRows] = useState(pagos);
    const [page, setPage] = useState(currentPage);
    const [totalPages, setTotalPages] = useState(lastPage);
    const [loadingMore, setLoadingMore] = useState(false);
    const [filters, setFilters] = useState(initialFilters ?? {});

    const [facturaModal, setFacturaModal] = useState(null);
    const [facturaData, setFacturaData] = useState(null);
    const [facturaError, setFacturaError] = useState(null);
    const [loadingFactura, setLoadingFactura] = useState(false);
    const [copied, setCopied] = useState(false);

    useEffect(() => {
        setRows(pagos);
        setPage(currentPage);
        setTotalPages(lastPage);
    }, [pagos, currentPage, lastPage]);

    const setFilter = (key, value) => {
        setFilters((prev) => ({ ...prev, [key]: value }));
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('facturacion.index'), filters, { preserveState: true, replace: true });
    };

    const limpiar = () => {
        setFilters({});
        router.get(route('facturacion.index'), {}, { preserveState: true, replace: true });
    };

    const hasFilters = Object.values(filters).some(Boolean);

    const cargarMas = async () => {
        if (loadingMore || page >= totalPages) return;
        setLoadingMore(true);
        try {
            const params = new URLSearchParams({ ...filters, page: page + 1 });
            const res = await fetch(route('facturacion.listar') + '?' + params.toString(), {
                headers: { Accept: 'application/json' },
            });
            const json = await res.json();
            setRows((prev) => [...prev, ...json.data]);
            setPage(json.current_page);
            setTotalPages(json.last_page);
        } catch {
            // no-op: user can retry with the button
        } finally {
            setLoadingMore(false);
        }
    };

    const abrirFacturar = async (pago) => {
        setFacturaModal(pago);
        setFacturaData(null);
        setFacturaError(null);
        setCopied(false);
        setLoadingFactura(true);
        try {
            const res = await fetch(route('facturacion.datos', pago.folio), {
                headers: { Accept: 'application/json' },
            });
            if (!res.ok) {
                const err = await res.json();
                setFacturaError(err.error || 'No se pudo obtener la información del pago.');
                return;
            }
            const json = await res.json();
            setFacturaData(json);
        } catch {
            setFacturaError('Error de conexión al obtener la información del pago.');
        } finally {
            setLoadingFactura(false);
        }
    };

    const copiarJson = () => {
        if (!facturaData) return;
        navigator.clipboard.writeText(JSON.stringify(facturaData, null, 2)).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Facturación
                </h2>
            }
        >
            <Head title="Facturación" />

            <div className="py-12 w-full">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">Recibos de Pago</h3>
                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                    Presiona Enter en cualquier filtro para buscar
                                </span>
                            </div>

                            <form onSubmit={handleSearch} className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Folio</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Contribuyente</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Clave Catastral</th>
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
                                            <th className="px-2 py-2"></th>
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
                                            <th className="px-2 py-2"></th>
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
                                                <div className="flex gap-1 justify-end">
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
                                        {rows.length > 0 ? (
                                            rows.map((pago) => (
                                                <tr key={pago.id} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">{pago.folio}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">{pago.fecha ?? '—'}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm max-w-[200px] truncate">{pago.nombre}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">{pago.clave_catastral ?? '—'}</td>
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
                                                        <button
                                                            type="button"
                                                            onClick={() => abrirFacturar(pago)}
                                                            disabled={pago.estatus !== 'pagado'}
                                                            className="inline-flex items-center rounded bg-emerald-600 px-3 py-1 text-xs font-semibold text-white hover:bg-emerald-500 disabled:opacity-40 disabled:cursor-not-allowed"
                                                            title={pago.estatus !== 'pagado' ? 'Solo se pueden facturar pagos con estatus Pagado' : 'Facturar'}
                                                        >
                                                            Facturar
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="8" className="px-6 py-4 text-center text-sm text-gray-500">
                                                    No hay pagos registrados.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </form>

                            {page < totalPages && (
                                <div className="mt-6 flex justify-center">
                                    <button
                                        type="button"
                                        onClick={cargarMas}
                                        disabled={loadingMore}
                                        className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-6 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-50"
                                    >
                                        {loadingMore ? 'Cargando...' : 'Cargar más'}
                                    </button>
                                </div>
                            )}
                            {rows.length > 0 && (
                                <p className="mt-3 text-center text-xs text-gray-500 dark:text-gray-400">
                                    Mostrando {rows.length} pago{rows.length !== 1 ? 's' : ''} (página {page} de {totalPages})
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {facturaModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-2xl shadow-xl max-h-[85vh] flex flex-col">
                        <div className="flex justify-between items-start mb-4">
                            <div>
                                <h3 className="text-lg font-semibold">Facturar — {facturaModal.folio}</h3>
                                <p className="text-sm text-gray-500 dark:text-gray-400">{facturaModal.nombre}</p>
                            </div>
                            <button
                                onClick={() => setFacturaModal(null)}
                                className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                            >
                                ✕
                            </button>
                        </div>

                        {loadingFactura && (
                            <p className="text-sm text-gray-500 dark:text-gray-400">Cargando datos del pago...</p>
                        )}

                        {facturaError && (
                            <div className="px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                                {facturaError}
                            </div>
                        )}

                        {facturaData && (
                            <>
                                <p className="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Datos listos para enviar a FacturAPI. Copia el JSON o consulta el mismo dato vía la API (<code>GET /api/pagos/{facturaModal.folio}</code>) una vez conectada la integración real.
                                </p>
                                <pre className="flex-1 overflow-auto bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded p-3 text-xs text-gray-800 dark:text-gray-200">
                                    {JSON.stringify(facturaData, null, 2)}
                                </pre>
                            </>
                        )}

                        <div className="flex justify-end gap-2 mt-4">
                            <button
                                onClick={() => setFacturaModal(null)}
                                className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Cerrar
                            </button>
                            {facturaData && (
                                <button
                                    onClick={copiarJson}
                                    className="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-500"
                                >
                                    {copied ? '¡Copiado!' : 'Copiar JSON'}
                                </button>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
