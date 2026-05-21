import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';

export default function Index({ calculos, predio }) {
    const fmt = (val) => {
        const n = parseFloat(val);
        return isNaN(n) ? '0.00' : n.toFixed(2);
    };

    const rows = calculos.data ?? calculos ?? [];
    const summaries = rows.reduce(
        (acc, row) => {
            acc.terreno += parseFloat(row.imp_terreno) || 0;
            acc.construccion += parseFloat(row.imp_construccion) || 0;
            acc.baldio += parseFloat(row.imp_baldio) || 0;
            acc.cm += parseFloat(row.cm) || 0;
            acc.entero += parseFloat(row.entero) || 0;
            acc.ap += parseFloat(row.aseo_publico) || 0;
            acc.recargos += parseFloat(row.recargos) || 0;
            acc.actualizacion += parseFloat(row.actualizacion) || 0;
            acc.descuento += parseFloat(row.descuento) || 0;
            acc.total += parseFloat(row.total) || 0;
            return acc;
        },
        { terreno: 0, construccion: 0, baldio: 0, cm: 0, entero: 0, ap: 0, recargos: 0, actualizacion: 0, descuento: 0, total: 0 },
    );

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Cálculos de Predio
                </h2>
            }
        >
            <Head title="Cálculos de Predio" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            {predio && (
                                <div className="mb-6 rounded-lg border bg-gray-50 dark:bg-gray-700 p-4">
                                    <h3 className="mb-3 text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Información del Predio
                                    </h3>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Clave Catastral</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.Clave_predial}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Contribuyente</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.contribuyente?.nombre_completo ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Ubicación</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.ubicacion_completa ?? predio.ubicacion ?? '—'}
                                            </p>
                                        </div>
                                    </div>
                                    <div className="mt-4">
                                        <a
                                            href={route('calculos-predios.pdf', { id_predio: predio.id_predio })}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700"
                                        >
                                            Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            )}

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-3 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Año</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Terreno</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Construcción</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Baldío</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">C.M.</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Entero</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">A.P.</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Recargos</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actualización</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Descuento</th>
                                            <th className="px-3 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {rows.length > 0 ? (
                                            rows.map((row, i) => (
                                                <tr key={row.id_tb_predio_calculo_general ?? i} className="hover:bg-gray-50 dark:bg-gray-700">
                                                    <td className="whitespace-nowrap px-3 py-3 text-center text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {row.anho ?? row.año ?? '—'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.imp_terreno)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.imp_construccion)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.imp_baldio)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.cm)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.entero)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.aseo_publico)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.recargos)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.actualizacion)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm text-gray-700">
                                                        {fmt(row.descuento)}
                                                    </td>
                                                    <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                        {fmt(row.total)}
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="11" className="px-3 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    No hay cálculos registrados.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                    {rows.length > 0 && (
                                        <tfoot className="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <td className="whitespace-nowrap px-3 py-3 text-center text-xs font-semibold uppercase text-gray-600 dark:text-gray-400">
                                                    Totales
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.terreno)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.construccion)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.baldio)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.cm)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.entero)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.ap)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.recargos)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.actualizacion)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.descuento)}
                                                </td>
                                                <td className="whitespace-nowrap px-3 py-3 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {fmt(summaries.total)}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    )}
                                </table>
                            </div>

                            {calculos.links && <Pagination meta={calculos} />}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
