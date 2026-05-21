import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Show({ predio }) {
    const urbano = predio.datos_urbano;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Detalle de Predio
                </h2>
            }
        >
            <Head title="Detalle de Predio" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Predio: {predio.Clave_predial}
                                </h3>
                                <div className="flex gap-2">
                                    <Link
                                        href={route('predios.edit', predio.id_predio)}
                                        className="inline-flex items-center rounded-md border border-transparent bg-yellow-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-yellow-400 focus:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 active:bg-yellow-600"
                                    >
                                        Editar
                                    </Link>
                                    <a
                                        href={route('predios.pdf', predio.id_predio)}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700"
                                    >
                                        PDF
                                    </a>
                                </div>
                            </div>

                            <div className="space-y-6">
                                <div className="border-b pb-4">
                                    <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">General</h4>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">ID</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.id_predio}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Clave Predial</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.Clave_predial}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Tipo Predio</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.tipo_predio?.Tipo_predio ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Contribuyente</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.contribuyente?.nombre_completo ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Cuenta</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.contribuyente?.cuenta ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Régimen Propiedad</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.regimen_propiedad?.REGIMEN ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Estado Renta</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.estado_renta?.DESCRIPCION ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Estado Impuesto</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.estado_impuesto?.DESCRIPCION ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Título Propiedad</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.titulo_propiedad?.DESCRIPCION ?? '—'}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div className="border-b pb-4">
                                    <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">Ubicación</h4>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Calle</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.calle?.CALLE ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Colonia</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.colonia?.COLONIA ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Núm. Exterior</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.Numero_exterior ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Núm. Interior</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.Numero_interior ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">C.P.</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.codigo_postal ?? '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Ubicación</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {predio.ubicacion ?? '—'}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div className="border-b pb-4">
                                    <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">Datos Catastrales</h4>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Superficie</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.superficie ?? '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Construcción</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.construccion ?? '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Valor Catastral</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.valor_catastral ?? '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Valor Fiscal</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.valor_fiscal ?? '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Núm. Escritura</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.numero_de_escritura ?? '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Año Último Pago</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{predio.año_ultimo_pago ?? '—'}</p>
                                        </div>
                                    </div>
                                </div>

                                {urbano && (
                                    <div className="border-b pb-4">
                                        <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">Datos Urbanos</h4>
                                        <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Zona Urbana</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.zona_urbana?.descripcion ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Forma Predio</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.forma_predio?.descripcion ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Uso Predio</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.uso_predio?.descripcion ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Estado Físico</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.estado_fisico?.DESCRIPCION ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Pavimento</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.pavimento?.DESCRIPCION ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Baldío</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.Baldio ? 'Sí' : 'No'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Sup. Terreno (m²)</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.superficie_terreno_metros_cuadrados ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Frente (m)</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.Frente_metros ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Fondo (m)</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.Fondo_metros ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">No. Pisos</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.numero_de_pisos_construidos ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Valor Cat. Terreno</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.valor_catastral_terreno ?? '—'}
                                                </p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Valor Cat. Construido</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                    {urbano.valor_catastral_construido ?? '—'}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                            {[
                                                { key: 'servicio_agua', label: 'Agua' },
                                                { key: 'servicio_drenaje', label: 'Drenaje' },
                                                { key: 'servicio_energia_electrica', label: 'Energía Eléctrica' },
                                                { key: 'servicio_alumbrado', label: 'Alumbrado' },
                                                { key: 'cuenta_con_banqueta', label: 'Banqueta' },
                                            ].map(({ key, label }) => (
                                                <div key={key}>
                                                    <label className="block text-sm font-medium text-gray-700">{label}</label>
                                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                        {urbano[key] ? 'Sí' : 'No'}
                                                    </p>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {predio.medidas_y_colindancias?.length > 0 && (
                                    <div className="border-b pb-4">
                                        <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">Colindancias</h4>
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Orientación</th>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Medida (m)</th>
                                                    <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Colinda con</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                {predio.medidas_y_colindancias.map((m) => (
                                                    <tr key={m.id_medida_colindacion}>
                                                        <td className="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                            {m.orientacion?.descripcion ?? m.id_orientacion}
                                                        </td>
                                                        <td className="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                            {m.medida_en_metros ?? '—'}
                                                        </td>
                                                        <td className="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                            {m.colinda_con ?? '—'}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}

                                {predio.observaciones?.length > 0 && (
                                    <div className="border-b pb-4">
                                        <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">Observaciones</h4>
                                        {predio.observaciones.map((obs) => (
                                            <p key={obs.id_observacion} className="text-sm text-gray-700">
                                                {obs.observacion}
                                            </p>
                                        ))}
                                    </div>
                                )}
                            </div>

                            <div className="mt-6">
                                <Link
                                    href={route('predios.index')}
                                    className="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-900"
                                >
                                    Volver
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
