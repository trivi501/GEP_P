import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';

export default function Index({ ordenes, formasPago, filters: initialFilters }) {
    const [pagarModal, setPagarModal] = useState(null);
    const [errorMessage, setErrorMessage] = useState(null);
    const [filters, setFilters] = useState(initialFilters ?? {});
    const { data, setData, post, processing, errors, reset } = useForm({
        orden_pago_id: '',
        formas_pagos: [{ forma_pago_id: '1', monto: '' }],
    });

    const setFilter = (key, value) => {
        setFilters(prev => ({ ...prev, [key]: value }));
    };

    const handleSearch = (e) => {
        e.preventDefault();
        const params = new URLSearchParams();
        Object.entries(filters).forEach(([k, v]) => { if (v) params.set(k, v); });
        window.location.href = route('ordenes-pgo-cajas.index') + '?' + params.toString();
    };

    const limpiar = () => {
        window.location.href = route('ordenes-pgo-cajas.index');
    };

    const hasFilters = Object.values(filters).some(Boolean);

    const isVencida = (orden) => {
        if (!orden.fecha_vencimiento || orden.pagado) return false;
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        const vence = new Date(orden.fecha_vencimiento + 'T00:00:00');
        return hoy > vence;
    };

    const openPagarModal = (orden) => {
        setPagarModal(orden);
        setData({
            orden_pago_id: orden.id,
            formas_pagos: [{ forma_pago_id: '1', monto: '' }],
        });
    };

    const closePagarModal = () => {
        setPagarModal(null);
        setErrorMessage(null);
        reset();
    };

    const addFormaPago = () => {
        setData('formas_pagos', [...data.formas_pagos, { forma_pago_id: '', monto: '' }]);
    };

    const removeFormaPago = (index) => {
        setData('formas_pagos', data.formas_pagos.filter((_, i) => i !== index));
    };

    const updateFormaPago = (index, field, value) => {
        const updated = data.formas_pagos.map((fp, i) => (i === index ? { ...fp, [field]: value } : fp));
        setData('formas_pagos', updated);
    };

    const totalFormasPago = data.formas_pagos.reduce((sum, fp) => sum + (parseFloat(fp.monto) || 0), 0);

    const submitPago = async (e) => {
        e.preventDefault();
        setErrorMessage(null);
        try {
            const res = await fetch(route('pagos.caja-general.guardar'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                },
                body: JSON.stringify(data),
            });
            const result = await res.json();
            if (!res.ok) {
                setErrorMessage(result.error || 'Error al procesar el pago.');
            } else {
                closePagarModal();
                if (result.pago_id) {
                    window.open(route('pagos.recibo', result.pago_id), '_blank');
                }
                window.location.reload();
            }
        } catch (err) {
            setErrorMessage('Error de conexión al procesar el pago.');
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Órdenes de Pago - Cajas
                </h2>
            }
        >
            <Head title="Órdenes de Pago - Cajas" />

            <div className="py-12">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Listado de Órdenes de Pago
                                </h3>
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
                                                        {!orden.pagado && !vencida ? (
                                                            <button
                                                                type="button"
                                                                onClick={() => openPagarModal(orden)}
                                                                className="inline-flex items-center gap-1 rounded-md bg-green-600 px-3 py-1.5 text-xs font-semibold uppercase tracking-widest text-white hover:bg-green-500"
                                                            >
                                                                <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                                Pagar
                                                            </button>
                                                        ) : (
                                                            <span className="text-xs text-gray-400">—</span>
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

            {pagarModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                     <div className="mx-4 w-full max-w-4xl rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                        <div className="mb-4 flex items-center justify-between">
                            <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">Pagar Orden</h3>
                            <button onClick={closePagarModal} className="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {errorMessage && (
                            <div className="mb-4 flex items-center gap-2 rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/50 dark:text-red-200">
                                <svg className="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>{errorMessage}</span>
                            </div>
                        )}

                        <form onSubmit={submitPago}>
                             <div className="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                 <div>
                                     <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Orden</label>
                                     <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                         {pagarModal.folio ?? ('#' + pagarModal.id)} - {pagarModal.nombre}
                                     </p>
                                 </div>
                                 <div>
                                     <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Secretaría</label>
                                     <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{pagarModal.secretaria?.nombre ?? '—'}</p>
                                 </div>
                                 <div>
                                     <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                                     <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{pagarModal.fecha ?? '—'}</p>
                                 </div>
                                 <div>
                                     <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Monto</label>
                                     <p className="mt-1 text-lg font-bold text-gray-900 dark:text-gray-100">
                                         ${parseFloat(pagarModal.monto).toFixed(2)}
                                     </p>
                                 </div>
                             </div>

                             {pagarModal.cuentas_ordenes_pago?.length > 0 && (
                                 <div className="mb-4">
                                     <h4 className="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cuentas</h4>
                                     <div className="overflow-x-auto rounded-md border border-gray-200 dark:border-gray-600">
                                         <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                             <thead className="bg-gray-50 dark:bg-gray-700">
                                                 <tr>
                                                     <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Indetec</th>
                                                     <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Cuenta</th>
                                                     <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Monto</th>
                                                     <th className="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Cant.</th>
                                                     <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Subtotal</th>
                                                 </tr>
                                             </thead>
                                             <tbody className="divide-y divide-gray-200 dark:divide-gray-600">
                                                 {pagarModal.cuentas_ordenes_pago.map((c) => (
                                                     <tr key={c.id}>
                                                         <td className="px-4 py-2 text-sm font-mono">{c.cuenta?.indetec ?? '—'}</td>
                                                         <td className="px-4 py-2 text-sm">{c.cuenta?.descripcion ?? ('Cuenta #' + c.IdCuenta)}</td>
                                                         <td className="px-4 py-2 text-right text-sm">${parseFloat(c.monto).toFixed(2)}</td>
                                                         <td className="px-4 py-2 text-center text-sm">{c.cantidad}</td>
                                                         <td className="px-4 py-2 text-right text-sm font-medium">
                                                             ${(parseFloat(c.monto) * parseFloat(c.cantidad)).toFixed(2)}
                                                         </td>
                                                     </tr>
                                                 ))}
                                             </tbody>
                                             <tfoot className="bg-gray-50 dark:bg-gray-700">
                                                 <tr>
                                                     <td colSpan="4" className="px-4 py-2 text-right text-sm font-medium">Total:</td>
                                                     <td className="px-4 py-2 text-right text-sm font-bold">
                                                         ${parseFloat(pagarModal.monto).toFixed(2)}
                                                     </td>
                                                 </tr>
                                             </tfoot>
                                         </table>
                                     </div>
                                 </div>
                             )}

                             <div className="border-t border-gray-200 pt-4 dark:border-gray-600">
                                <div className="mb-2 flex items-center justify-between">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Formas de Pago
                                    </label>
                                    <button
                                        type="button"
                                        onClick={addFormaPago}
                                        className="inline-flex items-center rounded-md bg-green-600 px-2 py-1 text-xs font-semibold text-white hover:bg-green-500"
                                    >
                                        + Agregar
                                    </button>
                                </div>
                                {data.formas_pagos.map((fp, index) => (
                                    <div key={index} className="mb-2 flex items-end gap-2">
                                        <div className="flex-1">
                                            <select
                                                value={fp.forma_pago_id}
                                                onChange={(e) => updateFormaPago(index, 'forma_pago_id', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                required
                                            >
                                                <option value="">Seleccione</option>
                                                {formasPago.map((fpOpt) => (
                                                    <option key={fpOpt.id} value={fpOpt.id}>{fpOpt.Descripción}</option>
                                                ))}
                                            </select>
                                        </div>
                                        <div className="w-32">
                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0.01"
                                                value={fp.monto}
                                                onChange={(e) => updateFormaPago(index, 'monto', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                                placeholder="Monto"
                                                required
                                            />
                                        </div>
                                        {data.formas_pagos.length > 1 && (
                                            <button
                                                type="button"
                                                onClick={() => removeFormaPago(index)}
                                                className="mb-1 inline-flex items-center justify-center w-7 h-7 rounded-full text-red-600 hover:bg-red-100 dark:hover:bg-red-900"
                                            >
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        )}
                                    </div>
                                ))}
                                {data.formas_pagos.length === 0 && (
                                    <p className="text-xs text-gray-500">Agregue al menos una forma de pago.</p>
                                )}
                            </div>

                            {data.formas_pagos.length > 0 && (
                                <div className="mt-2 space-y-1 text-right text-sm text-gray-600 dark:text-gray-400">
                                    <div>
                                        Total formas pago: <strong>${totalFormasPago.toFixed(2)}</strong>
                                        {totalFormasPago < parseFloat(pagarModal.monto) && (
                                            <span className="ml-2 text-yellow-500 text-xs">(no cubre el monto total)</span>
                                        )}
                                    </div>
                                    {totalFormasPago > parseFloat(pagarModal.monto) && (
                                        <div className="text-lg font-bold text-green-600">
                                            Cambio: ${(totalFormasPago - parseFloat(pagarModal.monto)).toFixed(2)}
                                        </div>
                                    )}
                                </div>
                            )}

                            <div className="mt-6 flex justify-end gap-2">
                                <button
                                    type="button"
                                    onClick={closePagarModal}
                                    className="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing || totalFormasPago < parseFloat(pagarModal.monto) || totalFormasPago === 0}
                                    className="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-green-500 disabled:opacity-50"
                                >
                                    {processing ? 'Procesando...' : 'Confirmar Pago'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}