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
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
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
                                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Forma Pago</th>
                                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Monto</th>
                                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Descuento</th>
                                                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Estatus</th>
                                                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Caja</th>
                                                <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Recibo</th>
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
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm">{pago.forma_pago}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">${parseFloat(pago.monto ?? 0).toFixed(2)}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-right">${parseFloat(pago.descuento ?? 0).toFixed(2)}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                        {pago.estatus === 'pagado' ? (
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Pagado</span>
                                                        ) : pago.estatus === 'cancelado' ? (
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Cancelado</span>
                                                        ) : (
                                                            <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:text-gray-100">{pago.estatus}</span>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-center">{pago.historial_caja?.caja?.nombre ?? '—'}</td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                        {pago.url_file ? (
                                                            <a
                                                                href={route('pagos.recibo', pago.id)}
                                                                target="_blank"
                                                                className="inline-flex items-center px-2 py-1 text-xs font-medium text-blue-600 border border-blue-300 rounded hover:text-blue-800"
                                                            >
                                                                Ver PDF
                                                            </a>
                                                        ) : (
                                                            <span className="text-xs text-gray-400">—</span>
                                                        )}
                                                    </td>
                                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                                {pago.estatus === 'pagado' && (
                                                            <button
                                                                onClick={() => setCancelModal(pago.id)}
                                                                className="inline-flex items-center px-2 py-1 text-xs font-medium text-red-600 border border-red-300 rounded hover:text-red-800"
                                                            >
                                                                Cancelar
                                                            </button>
                                                        )}
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