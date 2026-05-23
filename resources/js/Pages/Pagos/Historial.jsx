import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';

export default function Historial({ pagos, cajero, cajaAbierta }) {
    const { post } = useForm();
    const [cancelModal, setCancelModal] = useState(null);

    const confirmarCancelacion = () => {
        if (cancelModal) {
            post(route('pagos.cancelar', cancelModal));
            setCancelModal(null);
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Historial de Pagos
                </h2>
            }
        >
            <Head title="Historial de Pagos" />

            <div className="py-12 w-full">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">Historial de Pagos</h3>
                                {cajero && cajaAbierta && (
                                    <Link
                                        href={route('pagos.cobrar')}
                                        className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500"
                                    >
                                        + Nuevo Pago
                                    </Link>
                                )}
                            </div>

                            {!cajero && (
                                <p className="text-sm text-red-500 mb-4">No tienes un cajero asignado.</p>
                            )}

                            {(!pagos.data || pagos.data.length === 0) ? (
                                <p className="text-sm text-gray-500 dark:text-gray-400">No hay pagos registrados.</p>
                            ) : (
                                <div className="overflow-x-auto">
                                    <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead className="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Folio</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Clave Catastral</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Fecha</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Contribuyente</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">RFC</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipo</th>
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Forma Pago</th>
                                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Monto</th>
                                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Desc.</th>
                                                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estatus</th>
                                                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            {pagos.data.map((pago) => (
                                                <tr key={pago.id} className="hover:bg-gray-50 dark:bg-gray-700">
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">{pago.folio}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">{pago.predio?.Clave_predial ?? '—'}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">{pago.fecha ?? '—'}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm max-w-[200px] truncate">{pago.nombre}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">{pago.rfc}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">
                                                        {pago.tipo_pago === 'predial_rustico' ? (
                                                            <span className="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                                                <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                                                Rústico
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex items-center gap-1 text-xs font-medium text-blue-700">
                                                                <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                                Urbano
                                                            </span>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">{pago.forma_pago}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">${parseFloat(pago.monto ?? 0).toFixed(2)}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-right">${parseFloat(pago.descuento ?? 0).toFixed(2)}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                        {pago.estatus === 'pagado' ? (
                                                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7"/></svg>
                                                                Pagado
                                                            </span>
                                                        ) : pago.estatus === 'cancelado' ? (
                                                            <span className="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                Cancelado
                                                            </span>
                                                        ) : (
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:text-gray-100">{pago.estatus}</span>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                        <div className="flex items-center justify-center gap-2">
                                                            {pago.url_file && (
                                                                <a
                                                                    href={route('pagos.recibo', pago.id)}
                                                                    target="_blank"
                                                                    className="inline-flex items-center justify-center w-7 h-7 rounded text-blue-600 hover:bg-blue-50"
                                                                    title="Ver Recibo"
                                                                >
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                                </a>
                                                            )}
                                                            {pago.estatus === 'pagado' && (
                                                                <button
                                                                    onClick={() => setCancelModal(pago.id)}
                                                                    className="inline-flex items-center justify-center w-7 h-7 rounded text-red-600 hover:bg-red-50"
                                                                    title="Cancelar Pago"
                                                                >
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                </button>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            )}

                            {pagos.links && <Pagination meta={pagos} />}
                        </div>
                    </div>
                </div>
            </div>

            {cancelModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                        <h3 className="text-lg font-semibold mb-2">Cancelar pago</h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            ¿Estás seguro de cancelar este pago? Se restaurarán los datos anteriores del predio (<strong>año y bimestre de último pago</strong>).
                        </p>
                        <div className="flex justify-end gap-2">
                            <button
                                onClick={() => setCancelModal(null)}
                                className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                No, volver
                            </button>
                            <button
                                onClick={confirmarCancelacion}
                                className="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-500"
                            >
                                Sí, cancelar pago
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}