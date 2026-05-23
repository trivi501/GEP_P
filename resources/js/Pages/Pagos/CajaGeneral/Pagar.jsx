import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Pagar({ ordenPago, formasPago }) {
    const { data, setData, post, processing, errors } = useForm({
        orden_pago_id: ordenPago.id,
        formas_pagos: [{ forma_pago_id: '', monto: '' }],
    });

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

    const submit = (e) => {
        e.preventDefault();
        post(route('pagos.caja-general.guardar'));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Pagar Orden #{ordenPago.id}
                </h2>
            }
        >
            <Head title="Pagar Orden" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium">Detalle de la Orden</h3>
                                <Link
                                    href={route('pagos.caja-general')}
                                    className="inline-flex items-center rounded-md border border-transparent bg-gray-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-500"
                                >
                                    Volver
                                </Link>
                            </div>

                            <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Folio</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100 font-medium">{ordenPago.folio ?? ('#' + ordenPago.id)}</p>
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
                                    <p className="mt-1 text-lg font-bold text-gray-900 dark:text-gray-100">
                                        ${parseFloat(ordenPago.monto).toFixed(2)}
                                    </p>
                                </div>
                                <div className="sm:col-span-2">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{ordenPago.descripcion ?? '—'}</p>
                                </div>
                            </div>

                            {ordenPago.cuentas_ordenes_pago?.length > 0 && (
                                <div className="mb-6">
                                    <h4 className="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Cuentas</h4>
                                    <div className="overflow-x-auto rounded-md border border-gray-200 dark:border-gray-600">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Cuenta</th>
                                                    <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Monto</th>
                                                    <th className="px-4 py-2 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Cant.</th>
                                                    <th className="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400">Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-600">
                                                {ordenPago.cuentas_ordenes_pago.map((c) => (
                                                    <tr key={c.id}>
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
                                                    <td colSpan="3" className="px-4 py-2 text-right text-sm font-medium">Total:</td>
                                                    <td className="px-4 py-2 text-right text-sm font-bold">
                                                        ${parseFloat(ordenPago.monto).toFixed(2)}
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            )}

                            <form onSubmit={submit} className="space-y-4">
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
                                                    className="mb-0.5 inline-flex items-center justify-center rounded-full p-1 text-red-600 hover:bg-red-100 dark:hover:bg-red-900"
                                                >
                                                    <svg className="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            )}
                                        </div>
                                    ))}
                                    {errors.formas_pagos && (
                                        <p className="mt-1 text-sm text-red-600">{errors.formas_pagos}</p>
                                    )}
                                </div>

                                <div className="flex items-center justify-between border-t border-gray-200 pt-4 dark:border-gray-600">
                                    <p className="text-sm font-medium">
                                        Total: <span className={totalFormasPago < parseFloat(ordenPago.monto || 0) ? 'text-red-600' : 'text-green-600'}>
                                            ${totalFormasPago.toFixed(2)}
                                        </span>
                                        {totalFormasPago < parseFloat(ordenPago.monto || 0) && (
                                            <span className="ml-1 text-xs text-red-500">(Mínimo ${parseFloat(ordenPago.monto).toFixed(2)})</span>
                                        )}
                                    </p>
                                    <button
                                        type="submit"
                                        disabled={processing || totalFormasPago < parseFloat(ordenPago.monto || 0)}
                                        className="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-green-500 disabled:opacity-50"
                                    >
                                        {processing ? 'Procesando...' : 'Confirmar Pago'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
