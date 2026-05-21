import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Show({ contribuyente }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Detalle de Contribuyente
                </h2>
            }
        >
            <Head title="Detalle de Contribuyente" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Contribuyente #{contribuyente.cuenta}
                                </h3>
                                <Link
                                    href={route('contribuyentes.edit', contribuyente.id_contribuyente)}
                                    className="inline-flex items-center rounded-md border border-transparent bg-yellow-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-yellow-400 focus:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 active:bg-yellow-600"
                                >
                                    Editar
                                </Link>
                            </div>

                            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">ID</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.id_contribuyente}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Cuenta</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.cuenta}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Tipo Contribuyente</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {contribuyente.tipo_contribuyente?.area_contribuyente || contribuyente.id_tipo_contribuyente}
                                    </p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Nombre</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.nombre}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Primer Apellido</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.primer_apellido}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Segundo Apellido</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.segundo_apellido}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.nombre_completo}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Nombre Moral</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.nombre_moral || '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">RFC</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.rfc || '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">CURP</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.curp_contribuyente || '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Teléfono</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.telefono || '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.correo_electronico || '—'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Exento</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.exento ? 'Sí' : 'No'}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Activo</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                        {contribuyente.activo ? (
                                            <span className="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                Activo
                                            </span>
                                        ) : (
                                            <span className="inline-flex rounded-full bg-red-100 px-2 text-xs font-semibold leading-5 text-red-800">
                                                Inactivo
                                            </span>
                                        )}
                                    </p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Fecha Alta</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.fecha_alta}</p>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700">Nivel Gobierno</label>
                                    <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.nivel_gobierno || '—'}</p>
                                </div>
                            </div>

                            {contribuyente.domicilio && (
                                <div className="mt-8 border-t pt-6">
                                    <h4 className="mb-4 text-base font-medium text-gray-900 dark:text-gray-100">Domicilio</h4>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Calle</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.domicilio.nombre_vialidad || '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Num. Exterior</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.domicilio.num_exterior || '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Num. Interior</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.domicilio.num_interior || '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Colonia</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.domicilio.colonia || '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Código Postal</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.domicilio.codigo_postal || '—'}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">País</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {contribuyente.domicilio.pais?.nombre_pais || contribuyente.domicilio.id_pais || '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Estado</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {contribuyente.domicilio.estado?.nombre_estado || contribuyente.domicilio.id_estado || '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Municipio</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                                {contribuyente.domicilio.municipio?.nombre_municipio || contribuyente.domicilio.id_municipio || '—'}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">Domicilio Completo</label>
                                            <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{contribuyente.domicilio.domicilio_completo || '—'}</p>
                                        </div>
                                    </div>
                                </div>
                            )}

                            {contribuyente.datos_facturacion?.length > 0 && (
                                <div className="mt-8 border-t pt-6">
                                    <h4 className="mb-4 text-base font-medium text-gray-900 dark:text-gray-100">Datos de Facturación</h4>
                                    {contribuyente.datos_facturacion.map((fact) => (
                                        <div key={fact.id_datos_facturacion} className="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">RFC Facturación</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{fact.rfc_facturacion || '—'}</p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Razón Social</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{fact.razon_social || '—'}</p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">Correo</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{fact.correo || '—'}</p>
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">CP Domicilio Fiscal</label>
                                                <p className="mt-1 text-sm text-gray-900 dark:text-gray-100">{fact.CP_DomicilioFiscal_contribuyente || '—'}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}

                            {contribuyente.predios?.length > 0 && (
                                <div className="mt-8 border-t pt-6">
                                    <h4 className="mb-4 text-base font-medium text-gray-900 dark:text-gray-100">Predios</h4>
                                    <div className="overflow-x-auto">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Cuenta
                                                    </th>
                                                    <th className="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Dirección
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                {contribuyente.predios.map((predio) => (
                                                    <tr key={predio.id_predio} className="hover:bg-gray-50 dark:bg-gray-700">
                                                        <td className="whitespace-nowrap px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                                            {predio.cuenta}
                                                        </td>
                                                        <td className="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                                            {predio.direccion || '—'}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            <div className="mt-6">
                                <Link
                                    href={route('contribuyentes.index')}
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
