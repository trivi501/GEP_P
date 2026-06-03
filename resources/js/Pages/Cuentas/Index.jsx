import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';

export default function Index({ cuentas }) {
    const [deleteModal, setDeleteModal] = useState({ show: false, id: null });
    const [fechaInicio, setFechaInicio] = useState('');
    const [fechaFin, setFechaFin] = useState('');

    const confirmDelete = (id) => setDeleteModal({ show: true, id });
    const handleDelete = () => {
        router.delete(route('cuentas.destroy', deleteModal.id));
        setDeleteModal({ show: false, id: null });
    };

    const exportarExcel = () => {
        if (!fechaInicio || !fechaFin) return;
        const url = route('cuentas.exportar-excel', { fecha_inicio: fechaInicio, fecha_fin: fechaFin });
        window.open(url, '_blank');
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Cuentas
                </h2>
            }
        >
            <Head title="Cuentas" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Listado de Cuentas
                                </h3>
                                <Link
                                    href={route('cuentas.create')}
                                    className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-indigo-500 focus:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700"
                                >
                                    + Crear Cuenta
                                </Link>
                            </div>

                            <div className="mb-4 flex flex-wrap items-center gap-3 rounded-lg bg-gray-50 dark:bg-gray-700 p-3">
                                <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha Inicio:
                                    <input
                                        type="date"
                                        value={fechaInicio}
                                        onChange={(e) => setFechaInicio(e.target.value)}
                                        className="ml-2 rounded-md border border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                                    />
                                </label>
                                <label className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Fecha Fin:
                                    <input
                                        type="date"
                                        value={fechaFin}
                                        onChange={(e) => setFechaFin(e.target.value)}
                                        className="ml-2 rounded-md border border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                                    />
                                </label>
                                <button
                                    onClick={exportarExcel}
                                    disabled={!fechaInicio || !fechaFin}
                                    className="inline-flex items-center rounded-md border border-transparent bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-green-500 focus:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 active:bg-green-700 disabled:opacity-50"
                                >
                                    Exportar Excel
                                </button>
                                <span className="ml-auto text-xs text-gray-500 dark:text-gray-400">
                                    *El reporte incluye la suma de pagos en el rango seleccionado
                                </span>
                            </div>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Indetec
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Cuenta
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Subcuenta
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Descripción
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Importe
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Total Pagado
                                            </th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                Acciones
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {cuentas.data?.length > 0 ? (
                                            cuentas.data.map((cuenta) => (
                                                <tr key={cuenta.id} className="hover:bg-gray-50 dark:bg-gray-700">
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {cuenta.indetec ?? '—'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {cuenta.cuenta}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {cuenta.subcuenta}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {cuenta.descripcion}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        ${Number(cuenta.importe).toFixed(2)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        ${Number(cuenta.cuentas_pagos_sum_monto ?? 0).toFixed(2)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-right text-sm font-medium">
                                                        <Link
                                                            href={route('cuentas.show', cuenta.id)}
                                                            className="text-indigo-600 hover:text-indigo-900"
                                                        >
                                                            Ver
                                                        </Link>
                                                        <Link
                                                            href={route('cuentas.edit', cuenta.id)}
                                                            className="ml-3 text-yellow-600 hover:text-yellow-900"
                                                        >
                                                            Editar
                                                        </Link>
                                                        <button
                                                            onClick={() => confirmDelete(cuenta.id)}
                                                            className="ml-3 text-red-600 hover:text-red-900"
                                                        >
                                                            Eliminar
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td
                                                    colSpan="7"
                                                    className="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400"
                                                >
                                                    No hay cuentas registradas.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {cuentas.links && <Pagination meta={cuentas} />}
                        </div>
                    </div>
                </div>
            </div>

            {deleteModal.show && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="mx-4 w-full max-w-sm rounded-lg bg-white dark:bg-gray-800 p-6 shadow-xl">
                        <h3 className="text-lg font-semibold text-gray-900 dark:text-gray-100">Confirmar eliminación</h3>
                        <p className="mt-2 text-sm text-gray-600 dark:text-gray-400">
                            ¿Está seguro de eliminar esta cuenta? Esta acción no se puede deshacer.
                        </p>
                        <div className="mt-4 flex justify-end gap-3">
                            <button
                                onClick={() => setDeleteModal({ show: false, id: null })}
                                className="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={handleDelete}
                                className="rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-500"
                            >
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
