import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Show({ pago }) {
    const [showCancelModal, setShowCancelModal] = useState(false);
    const { post, processing } = useForm();

    const cancelar = () => {
        post(route('pagos.caja-general.cancelar', pago.id), {
            onSuccess: () => setShowCancelModal(false),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Pago Caja General #{pago.id}
                </h2>
            }
        >
            <Head title="Pago Caja General" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium">Detalle del Pago</h3>
                                <div className="flex gap-2">
                                    {pago.estatus !== 'cancelado' && (
                                        <button
                                            onClick={() => setShowCancelModal(true)}
                                            className="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-red-500"
                                        >
                                            Cancelar
                                        </button>
                                    )}
                                    <a
                                        href={route('pagos.recibo', pago.id)}
                                        target="_blank"
                                        className="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-blue-500"
                                    >
                                        Ver Recibo
                                    </a>
                                    <Link
                                        href={route('pagos.caja-general')}
                                        className="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-500"
                                    >
                                        Volver
                                    </Link>
                                </div>
                            </div>

                            <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Folio</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{pago.folio}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{pago.fecha ?? '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Orden de Pago</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {pago.orden_pago ? (
                                            <Link href={route('ordenes-pago.show', pago.orden_pago.id)} className="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                                #{pago.orden_pago.id} - {pago.orden_pago.nombre}
                                            </Link>
                                        ) : '—'}
                                    </p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Estatus</label>
                                    <p className="mt-1">
                                        {pago.estatus === 'cancelado' ? (
                                            <span className="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800 dark:bg-red-900 dark:text-red-200">Cancelado</span>
                                        ) : (
                                            <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">Pagado</span>
                                        )}
                                    </p>
                                </div>
                                <div className="sm:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre / Concepto</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{pago.nombre}</p>
                                </div>
                                {pago.descripcion && (
                                    <div className="sm:col-span-2">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                        <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{pago.descripcion}</p>
                                    </div>
                                )}
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Monto Total</label>
                                    <p className="mt-1 text-lg font-bold text-gray-900 dark:text-gray-100">${parseFloat(pago.monto).toFixed(2)}</p>
                                </div>
                            </div>

                            {pago.cuentas_pagos?.length > 0 && (
                                <div className="mb-6">
                                    <h4 className="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cuentas Pagadas</h4>
                                    <div className="overflow-x-auto rounded-md border border-gray-200 dark:border-gray-600">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Concepto</th>
                                                    <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-600">
                                                {pago.cuentas_pagos.map((cp) => (
                                                    <tr key={cp.id}>
                                                        <td className="px-4 py-2 text-sm">{cp.concepto ?? '—'}</td>
                                                        <td className="px-4 py-2 text-right text-sm">${parseFloat(cp.monto).toFixed(2)}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {pago.formas_pagos_cada?.length > 0 && (
                                <div className="mb-6">
                                    <h4 className="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Formas de Pago</h4>
                                    <div className="overflow-x-auto rounded-md border border-gray-200 dark:border-gray-600">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Forma</th>
                                                    <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-600">
                                                {pago.formas_pagos_cada.map((fp) => (
                                                    <tr key={fp.id}>
                                                        <td className="px-4 py-2 text-sm">{fp.forma_pago?.Descripción ?? '—'}</td>
                                                        <td className="px-4 py-2 text-right text-sm">${parseFloat(fp.monto).toFixed(2)}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {showCancelModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50">
                    <div className="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                        <h3 className="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Cancelar Pago</h3>
                        <p className="mb-4 text-sm text-gray-600 dark:text-gray-400">
                            ¿Está seguro de cancelar este pago? La orden volverá a estar pendiente.
                        </p>
                        <div className="flex justify-end gap-2">
                            <button
                                onClick={() => setShowCancelModal(false)}
                                className="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                            >
                                No
                            </button>
                            <button
                                onClick={cancelar}
                                disabled={processing}
                                className="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-red-500 disabled:opacity-50"
                            >
                                {processing ? 'Cancelando...' : 'Sí, Cancelar'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
