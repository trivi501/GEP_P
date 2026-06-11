import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';

const historicoFieldLabels = {
    id_predio: 'ID',
    Clave_predial: 'Clave Predial',
    id_contribuyente: 'Contribuyente',
    id_tipo_predio: 'Tipo Predio',
    id_colonia: 'Colonia',
    id_calle: 'Calle',
    ubicacion: 'Ubicación',
    codigo_postal: 'C.P.',
    Numero_exterior: 'Núm. Exterior',
    Numero_interior: 'Núm. Interior',
    id_regimen_propiedad: 'Régimen Propiedad',
    id_estado_renta: 'Estado Renta',
    id_estaus_cobro_predial: 'Estatus Cobro Predial',
    id_titulo_propiedad: 'Título Propiedad',
    numero_de_escritura: 'Núm. Escritura',
    valor_catastral: 'Valor Catastral',
    valor_fiscal: 'Valor Fiscal',
    superficie: 'Superficie',
    construccion: 'Construcción',
    año_ultimo_pago: 'Año Último Pago',
    observacion: 'Observación',
    medidas_y_colindancias: 'Medidas y Colindancias',
};

function InfoGroup({ title, children, className }) {
    return (
        <div className={`border rounded-lg p-4 ${className ?? ''}`}>
            <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">{title}</h4>
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                {children}
            </div>
        </div>
    );
}

function InfoField({ label, children }) {
    return (
        <div>
            <label className="block text-sm font-medium text-gray-500 dark:text-gray-400">{label}</label>
            <p className="mt-0.5 text-sm text-gray-900 dark:text-gray-100">{children ?? '—'}</p>
        </div>
    );
}

export default function Show({ predio }) {
    const urbano = predio.datos_urbano;
    const historico = predio.historico ?? [];
    const [histPage, setHistPage] = useState(1);
    const perPage = 10;
    const totalPages = Math.ceil(historico.length / perPage);
    const paginated = historico.slice((histPage - 1) * perPage, histPage * perPage);

    const badgeColors = {
        CREATE: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        UPDATE: 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    };

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
                            <div className="mb-6 flex flex-wrap items-center justify-between gap-4">

                                <div>
                                    <Link
                                        href={route('predios.index')}
                                        className="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-500 focus:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:bg-blue-700"
                                    >
                                        Volver al listado
                                    </Link>
                                    <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        Predio: {predio.Clave_predial}
                                    </h3>
                                    <p className="text-sm text-gray-500 dark:text-gray-400">
                                        ID: {predio.id_predio}
                                    </p>
                                </div>
                                <div className="flex gap-2">
                                    <a
                                        href={`/calculos-predios/${predio.tipo_predio?.Tipo_predio?.toLowerCase().includes('rústico') ? 'pdf-rustico' : 'pdf'}?id_predio=${predio.id_predio}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:bg-red-700"
                                    >
                                        Estado de Cuenta
                                    </a>
                                    <a
                                        href={route('predios.pdf', predio.id_predio)}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-blue-500 focus:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 active:bg-blue-700"
                                    >
                                        Cédula
                                    </a>
                                    <Link
                                        href={route('predios.edit', predio.id_predio)}
                                        className="inline-flex items-center rounded-md border border-transparent bg-yellow-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-yellow-400 focus:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 active:bg-yellow-600"
                                    >
                                        Editar
                                    </Link>
                                    
                                </div>
                            </div>

                            <div className="space-y-6">
                                <InfoGroup title="General">
                                    <InfoField label="Clave Predial">{predio.Clave_predial}</InfoField>
                                    <InfoField label="Tipo Predio">{predio.tipo_predio?.Tipo_predio}</InfoField>
                                    <InfoField label="Contribuyente">{predio.contribuyente?.nombre_completo}</InfoField>
                                    <InfoField label="Cuenta">{predio.contribuyente?.cuenta}</InfoField>
                                    <InfoField label="Régimen Propiedad">{predio.regimen_propiedad?.REGIMEN}</InfoField>
                                    <InfoField label="Estado Renta">{predio.estado_renta?.DESCRIPCION}</InfoField>
                                    <InfoField label="Estatus Cobro Predial">{predio.estado_impuesto?.DESCRIPCION}</InfoField>
                                    <InfoField label="Título Propiedad">{predio.titulo_propiedad?.DESCRIPCION}</InfoField>
                                    <InfoField label="Núm. Escritura">{predio.numero_de_escritura}</InfoField>
                                </InfoGroup>

                                <InfoGroup title="Ubicación">
                                    <InfoField label="Calle">{predio.calle?.CALLE}</InfoField>
                                    <InfoField label="Colonia">{predio.colonia?.COLONIA}</InfoField>
                                    <InfoField label="Núm. Exterior">{predio.Numero_exterior}</InfoField>
                                    <InfoField label="Núm. Interior">{predio.Numero_interior}</InfoField>
                                    <InfoField label="C.P.">{predio.codigo_postal}</InfoField>
                                    <InfoField label="Ubicación">{predio.ubicacion}</InfoField>
                                </InfoGroup>

                                <InfoGroup title="Datos Catastrales">
                                    <InfoField label="Superficie (m²)">
                                        {predio.superficie != null ? `${parseFloat(predio.superficie).toLocaleString('es-MX', { minimumFractionDigits: 2 })}` : '—'}
                                    </InfoField>
                                    <InfoField label="Construcción (m²)">
                                        {predio.construccion != null ? `${parseFloat(predio.construccion).toLocaleString('es-MX', { minimumFractionDigits: 2 })}` : '—'}
                                    </InfoField>
                                    <InfoField label="Valor Catastral">
                                        {predio.valor_catastral != null ? `$${parseFloat(predio.valor_catastral).toLocaleString('es-MX', { minimumFractionDigits: 2 })}` : '—'}
                                    </InfoField>
                                    <InfoField label="Valor Fiscal">
                                        {predio.valor_fiscal != null ? `$${parseFloat(predio.valor_fiscal).toLocaleString('es-MX', { minimumFractionDigits: 2 })}` : '—'}
                                    </InfoField>
                                    <InfoField label="Año Último Pago">{predio.año_ultimo_pago}</InfoField>
                                    <InfoField label="Latitud">{predio.latitud}</InfoField>
                                    <InfoField label="Longitud">{predio.longitud}</InfoField>
                                </InfoGroup>

                                {urbano && (
                                    <InfoGroup title="Datos Urbanos">
                                        <InfoField label="Zona Urbana">{urbano.zona_urbana?.descripcion}</InfoField>
                                        <InfoField label="Forma Predio">{urbano.forma_predio?.descripcion}</InfoField>
                                        <InfoField label="Uso Predio">{urbano.uso_predio?.descripcion}</InfoField>
                                        <InfoField label="Estado Físico">{urbano.estado_fisico?.DESCRIPCION}</InfoField>
                                        <InfoField label="Pavimento">{urbano.pavimento?.DESCRIPCION}</InfoField>
                                        <InfoField label="Baldío">{urbano.Baldio ? 'Sí' : 'No'}</InfoField>
                                        <InfoField label="Sup. Terreno (m²)">{urbano.superficie_terreno_metros_cuadrados}</InfoField>
                                        <InfoField label="Frente (m)">{urbano.Frente_metros}</InfoField>
                                        <InfoField label="Fondo (m)">{urbano.Fondo_metros}</InfoField>
                                        <InfoField label="No. Pisos">{urbano.numero_de_pisos_construidos}</InfoField>
                                        <InfoField label="Valor Cat. Terreno">
                                            {urbano.valor_catastral_terreno != null ? `$${parseFloat(urbano.valor_catastral_terreno).toLocaleString('es-MX', { minimumFractionDigits: 2 })}` : '—'}
                                        </InfoField>
                                        <InfoField label="Valor Cat. Construido">
                                            {urbano.valor_catastral_construido != null ? `$${parseFloat(urbano.valor_catastral_construido).toLocaleString('es-MX', { minimumFractionDigits: 2 })}` : '—'}
                                        </InfoField>
                                        <div className="col-span-3">
                                            <div className="flex flex-wrap gap-6">
                                                {[
                                                    { key: 'servicio_agua', label: 'Agua' },
                                                    { key: 'servicio_drenaje', label: 'Drenaje' },
                                                    { key: 'servicio_energia_electrica', label: 'Energía Eléctrica' },
                                                    { key: 'servicio_alumbrado', label: 'Alumbrado' },
                                                    { key: 'cuenta_con_banqueta', label: 'Banqueta' },
                                                ].map(({ key, label }) => (
                                                    <div key={key} className="flex items-center gap-2">
                                                        <span className={`inline-block w-2.5 h-2.5 rounded-full ${urbano[key] ? 'bg-green-500' : 'bg-red-400'}`} />
                                                        <span className="text-sm text-gray-700 dark:text-gray-300">{label}</span>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    </InfoGroup>
                                )}

                                {predio.medidas_y_colindancias?.length > 0 && (
                                    <div className="border rounded-lg p-4">
                                        <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">Colindancias</h4>
                                        <div className="overflow-x-auto">
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
                                    </div>
                                )}

                                {predio.niveles_construidos?.length > 0 && (
                                    <div className="border rounded-lg p-4">
                                        <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">Niveles Construidos</h4>
                                        <div className="overflow-x-auto">
                                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead className="bg-gray-50 dark:bg-gray-700">
                                                    <tr>
                                                        <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                                                        <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Uso</th>
                                                        <th className="px-4 py-2 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Superficie (m²)</th>
                                                        <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Estado</th>
                                                        <th className="px-4 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Calidad</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                    {predio.niveles_construidos.map((n) => (
                                                        <tr key={n.id_nivel_construido}>
                                                            <td className="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {n.tipo_construccion?.descripcion ?? '—'}
                                                            </td>
                                                            <td className="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {n.uso_construccion?.descripcion ?? '—'}
                                                            </td>
                                                            <td className="px-4 py-2 text-sm text-right text-gray-900 dark:text-gray-100">
                                                                {n.superficie_metros_cuadrados ?? '—'}
                                                            </td>
                                                            <td className="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {n.estado_construccion ?? '—'}
                                                            </td>
                                                            <td className="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {n.calidad_construccion ?? '—'}
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                )}

                                {predio.observaciones?.length > 0 && (
                                    <div className="border rounded-lg p-4">
                                        <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">Observaciones</h4>
                                        <div className="space-y-2">
                                            {predio.observaciones.map((obs) => (
                                                <div key={obs.id_observacion} className="border-l-4 border-gray-300 dark:border-gray-600 pl-3">
                                                    <p className="text-sm text-gray-500 dark:text-gray-400">{obs.fecha_registro}</p>
                                                    <p className="text-sm text-gray-900 dark:text-gray-100">{obs.observacion}</p>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}

                                {historico.length > 0 && (
                                    <div className="border rounded-lg p-4">
                                        <h4 className="mb-3 text-base font-semibold text-gray-800 dark:text-gray-100">
                                            Historial de Cambios
                                            <span className="ml-2 text-sm font-normal text-gray-500">({historico.length} registros)</span>
                                        </h4>
                                        <div className="overflow-x-auto">
                                            <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead className="bg-gray-50 dark:bg-gray-700">
                                                    <tr>
                                                        <th className="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                                                        <th className="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Usuario</th>
                                                        <th className="px-3 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                                                        <th className="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Campo</th>
                                                        <th className="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Valor Anterior</th>
                                                        <th className="px-3 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Valor Nuevo</th>
                                                    </tr>
                                                </thead>
                                                <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                    {paginated.map((h) => (
                                                        <tr key={h.id_historico} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                            <td className="whitespace-nowrap px-3 py-2 text-xs text-gray-500 dark:text-gray-400">
                                                                {h.fecha_modificacion}
                                                            </td>
                                                            <td className="whitespace-nowrap px-3 py-2 text-sm text-gray-900 dark:text-gray-100">
                                                                {h.usuario_modifica?.name ?? h.id_usuario_modifica ?? '—'}
                                                            </td>
                                                            <td className="px-3 py-2 text-center">
                                                                <span className={`inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ${badgeColors[h.tipo_operacion] ?? 'bg-gray-100 text-gray-800'}`}>
                                                                    {h.tipo_operacion}
                                                                </span>
                                                            </td>
                                                            <td className="px-3 py-2 text-sm text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                                                {historicoFieldLabels[h.campo_modificado] ?? h.campo_modificado}
                                                            </td>
                                                            <td className="px-3 py-2 text-sm text-gray-500 break-words max-w-xs">
                                                                {h.valor_anterior ?? '—'}
                                                            </td>
                                                            <td className="px-3 py-2 text-sm text-gray-900 dark:text-gray-100 font-medium break-words max-w-xs">
                                                                {h.valor_nuevo ?? '—'}
                                                            </td>
                                                        </tr>
                                                    ))}
                                                </tbody>
                                            </table>
                                        </div>
                                        {totalPages > 1 && (
                                            <div className="mt-3 flex items-center justify-between text-sm">
                                                <span className="text-gray-500">
                                                    Página {histPage} de {totalPages}
                                                </span>
                                                <div className="flex gap-1">
                                                    <button
                                                        onClick={() => setHistPage(p => Math.max(1, p - 1))}
                                                        disabled={histPage === 1}
                                                        className="rounded border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    >
                                                        Anterior
                                                    </button>
                                                    <button
                                                        onClick={() => setHistPage(p => Math.min(totalPages, p + 1))}
                                                        disabled={histPage === totalPages}
                                                        className="rounded border border-gray-300 px-3 py-1 text-gray-700 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
                                                    >
                                                        Siguiente
                                                    </button>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
