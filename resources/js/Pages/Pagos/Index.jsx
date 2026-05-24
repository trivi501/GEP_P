import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, usePage } from '@inertiajs/react';
import { useState, useRef } from 'react';
import Pagination from '@/Components/Pagination';

export default function Index({ historial, cajero, cajaAbierta }) {
    const user = usePage().props.auth.user;
    const [showModal, setShowModal] = useState(false);
    const [showCerrarModal, setShowCerrarModal] = useState(false);
    const [fondo, setFondo] = useState('');
    const [errors, setErrors] = useState({});
    const cerrarFormRef = useRef(null);

    const abrirCaja = (e) => {
        e.preventDefault();
        setErrors({});
        fetch(route('pagos.store'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content,
            },
            body: JSON.stringify({ fondo }),
        })
            .then((r) => {
                if (!r.ok) throw new Error('Error');
                window.location.reload();
            })
            .catch(() => setErrors({ fondo: 'Error al abrir caja' }));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Historial de Caja
                </h2>
            }
        >
            <Head title="Historial de Caja" />

            <div className="py-12 w-full">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {window.sessionStorage?.getItem('success') && (
                        <div className="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded">
                            {window.sessionStorage.getItem('success')}
                        </div>
                    )}

                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">Historial de Caja - {user.name}</h3>
                                {cajero && !cajaAbierta && (
                                    <button
                                        onClick={() => setShowModal(true)}
                                        className="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500"
                                    >
                                        + Abrir Caja
                                    </button>
                                )}
                                {cajero && cajaAbierta && (
                                    <div className="flex gap-2">
                                        <Link
                                            href={route('pagos.cobrar')}
                                            className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500"
                                        >
                                            + Nuevo Pago
                                        </Link>
                                        <Link
                                            href={route('pagos.historial')}
                                            className="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-50 dark:bg-gray-7000"
                                        >
                                            Historial de Pagos
                                        </Link>
                                        <button
                                            onClick={() => setShowCerrarModal(true)}
                                            className="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500"
                                        >
                                            Cerrar Caja
                                        </button>
                                    </div>
                                )}
                            </div>

                            {cajaAbierta && (
                                <div className="mb-4 p-4 bg-green-50 rounded-lg">
                                    <p className="text-sm">
                                        <strong>Caja activa:</strong> {cajaAbierta.caja?.nombre} &mdash;
                                        <strong>Fondo:</strong> ${parseFloat(cajaAbierta.fondo ?? 0).toFixed(2)} &mdash;
                                        <strong>Total Ingresos:</strong> ${parseFloat(cajaAbierta.total_ingreso ?? 0).toFixed(2)}
                                    </p>
                                </div>
                            )}

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Caja</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Apertura</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cierre</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Fondo</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ingresos</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        {historial.data?.length > 0 ? historial.data.map((h) => (
                                            <tr key={h.id} className="hover:bg-gray-50 dark:bg-gray-700">
                                                <td className="px-6 py-4 whitespace-nowrap text-sm">{h.caja?.nombre ?? '—'}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm">{h.datetime_apertura ?? '—'}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm">{h.datetime_cierre ?? '—'}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-right">${parseFloat(h.fondo ?? 0).toFixed(2)}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-right">${parseFloat(h.total_ingreso ?? 0).toFixed(2)}</td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                    {h.datetime_cierre ? (
                                                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:text-gray-100">Cerrada</span>
                                                    ) : (
                                                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Abierta</span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                    {h.datetime_cierre ? (
                                                        <a
                                                            href={route('pagos.corte-pdf', h.id)}
                                                            className="inline-flex items-center px-3 py-1 bg-purple-600 text-white text-xs font-medium rounded hover:bg-purple-500"
                                                            target="_blank"
                                                        >
                                                            Reporte
                                                        </a>
                                                    ) : (
                                                        <span className="text-gray-400 text-xs">—</span>
                                                    )}
                                                </td>
                                            </tr>
                                        )) : (
                                            <tr>
                                                <td colSpan="7" className="px-6 py-4 text-sm text-gray-500 dark:text-gray-400 text-center">No hay registros.</td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {historial.links && <Pagination meta={historial} />}
                        </div>
                    </div>
                </div>
            </div>

            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                        <h3 className="text-lg font-medium mb-4">Abrir Caja</h3>
                        <form onSubmit={abrirCaja}>
                            <div className="mb-4">
                                <label className="block text-sm font-medium mb-1">Fondo Inicial</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value={fondo}
                                    onChange={(e) => setFondo(e.target.value)}
                                    className="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                                    required
                                />
                                {errors.fondo && <p className="text-sm text-red-500 mt-1">{errors.fondo}</p>}
                            </div>
                            <div className="flex justify-end gap-2">
                                <button
                                    type="button"
                                    onClick={() => setShowModal(false)}
                                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    className="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-500"
                                >
                                    Abrir
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showCerrarModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
                        <h3 className="text-lg font-medium mb-4">Cerrar Caja</h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            ¿Estás seguro de cerrar la caja? Se generará un reporte con el resumen de pagos.
                        </p>
                        <div className="flex justify-end gap-2">
                            <button
                                type="button"
                                onClick={() => setShowCerrarModal(false)}
                                className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Cancelar
                            </button>
                            <button
                                type="button"
                                onClick={() => {
                                    setShowCerrarModal(false);
                                    cerrarFormRef.current.submit();
                                }}
                                className="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-500"
                            >
                                Cerrar y generar reporte
                            </button>
                        </div>
                        <form ref={cerrarFormRef} method="POST" action={route('pagos.cerrar')} className="hidden">
                            <input type="hidden" name="_token" value={document.querySelector('meta[name=csrf-token]')?.content} />
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}