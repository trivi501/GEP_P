import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Show({ calle }) {
    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">Detalle de Calle</h2>}>
            <Head title={`Calle ${calle.CALLE}`} />
            <div className="py-12">
                <div className="mx-auto max-w-lg sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <div>
                                    <h3 className="text-lg font-medium">{calle.CALLE}</h3>
                                    <p className="text-sm text-gray-500">ID: {calle.id_calle}</p>
                                </div>
                                <Link href={route('calles.index')} className="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition hover:bg-gray-50">Volver</Link>
                            </div>
                            <div className="border-t pt-6 space-y-4">
                                <div><label className="block text-sm font-medium text-gray-500">Colonia</label><p className="mt-0.5 text-sm text-gray-900">{calle.colonia?.COLONIA ?? '—'}</p></div>
                                <div><label className="block text-sm font-medium text-gray-500">Activo</label><p className="mt-0.5 text-sm">{calle.Activo ? <span className="text-green-600">Sí</span> : <span className="text-red-600">No</span>}</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
