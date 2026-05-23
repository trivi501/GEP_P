import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Show({ ordenPago }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Orden de Pago {ordenPago.folio ?? ('#' + ordenPago.id)}
                </h2>
            }
        >
            <Head title="Orden de Pago" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium">Detalle de Orden de Pago</h3>
                                <div className="flex gap-2">
                                    {!ordenPago.pagado && (
                                        <Link
                                            href={route('ordenes-pago.edit', ordenPago.id)}
                                            className="inline-flex items-center rounded-md border border-transparent bg-yellow-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-yellow-400"
                                        >
                                            Editar
                                        </Link>
                                    )}
                                    <Link
                                        href={route('ordenes-pago.index')}
                                        className="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700"
                                    >
                                        Volver
                                    </Link>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Folio</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.folio ?? ('#' + ordenPago.id)}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.nombre}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Área</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.area ?? '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.fecha ?? '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Vigencia</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.fecha_vencimiento ?? '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Monto</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100 font-bold">${parseFloat(ordenPago.monto).toFixed(2)}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Usuario</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.user?.name ?? '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Secretaría</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.secretaria?.nombre ?? '—'}</p>
                                </div>
                                <div className="sm:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.descripcion ?? '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Estatus</label>
                                    <p className="mt-1">
                                        {ordenPago.pagado ? (
                                            <span className="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-200">Pagado</span>
                                        ) : (
                                            <span className="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Pendiente</span>
                                        )}
                                    </p>
                                </div>
                                {ordenPago.pagado && ordenPago.fecha_pago && (
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Fecha de Pago</label>
                                        <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.fecha_pago}</p>
                                    </div>
                                )}
                                {ordenPago.pagado && ordenPago.pagos?.length > 0 && (
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Pago Caja General</label>
                                        <p className="mt-1">
                                            <Link href={route('pagos.caja-general.show', ordenPago.pagos[0].id)} className="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                                Ver pago #{ordenPago.pagos[0].id}
                                            </Link>
                                        </p>
                                    </div>
                                )}
                            </div>

                            <div className="mt-8">
                                <h4 className="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Cuentas asociadas</h4>
                                {ordenPago.cuentas_ordenes_pago?.length > 0 ? (
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cuenta</th>
                                                    <th className="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Monto</th>
                                                    <th className="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cantidad</th>
                                                    <th className="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Descuento</th>
                                                    <th className="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                {ordenPago.cuentas_ordenes_pago.map((c, i) => {
                                                    const subtotal = (parseFloat(c.monto) * parseFloat(c.cantidad)) - parseFloat(c.descuento);
                                                    return (
                                                        <tr key={i} className="hover:bg-gray-50 dark:bg-gray-700">
                                                            <td className="whitespace-nowrap px-4 py-2 text-sm">
                                                                {c.cuenta?.descripcion || c.cuenta?.cuenta || `Cuenta #${c.IdCuenta}`}
                                                            </td>
                                                            <td className="whitespace-nowrap px-4 py-2 text-sm text-right">${parseFloat(c.monto).toFixed(2)}</td>
                                                            <td className="whitespace-nowrap px-4 py-2 text-sm text-right">{parseFloat(c.cantidad).toFixed(2)}</td>
                                                            <td className="whitespace-nowrap px-4 py-2 text-sm text-right">${parseFloat(c.descuento).toFixed(2)}</td>
                                                            <td className="whitespace-nowrap px-4 py-2 text-sm text-right font-medium">${subtotal.toFixed(2)}</td>
                                                        </tr>
                                                    );
                                                })}
                                            </tbody>
                                            <tfoot>
                                                <tr className="bg-gray-50 dark:bg-gray-700 font-bold">
                                                    <td colSpan="4" className="px-4 py-2 text-sm text-right">Total</td>
                                                    <td className="px-4 py-2 text-sm text-right">${parseFloat(ordenPago.monto).toFixed(2)}</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                ) : (
                                    <p className="text-sm text-gray-500">No hay cuentas asociadas.</p>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
