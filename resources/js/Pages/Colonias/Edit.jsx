import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';

export default function Edit({ colonia }) {
    const { data, setData, patch, processing, errors } = useForm({
        COLONIA: colonia.COLONIA ?? '',
        id_poblacion: colonia.id_poblacion ?? '',
        id_cat_zona_predio: colonia.id_cat_zona_predio ?? '',
        Activo: colonia.Activo ?? true,
    });

    const submit = (e) => { e.preventDefault(); patch(route('colonias.update', colonia.id_colonia)); };

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">Editar Colonia</h2>}>
            <Head title="Editar Colonia" />
            <div className="py-12">
                <div className="mx-auto max-w-lg sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="COLONIA" value="Nombre de la Colonia" />
                                    <input id="COLONIA" type="text" value={data.COLONIA} onChange={(e) => setData('COLONIA', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                                    <InputError message={errors.COLONIA} className="mt-2" />
                                </div>
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <InputLabel htmlFor="id_poblacion" value="ID Población" />
                                        <input id="id_poblacion" type="number" value={data.id_poblacion} onChange={(e) => setData('id_poblacion', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <InputError message={errors.id_poblacion} className="mt-2" />
                                    </div>
                                    <div>
                                        <InputLabel htmlFor="id_cat_zona_predio" value="ID Zona Predio" />
                                        <input id="id_cat_zona_predio" type="number" value={data.id_cat_zona_predio} onChange={(e) => setData('id_cat_zona_predio', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        <InputError message={errors.id_cat_zona_predio} className="mt-2" />
                                    </div>
                                </div>
                                <div className="flex items-center gap-2">
                                    <input type="checkbox" id="Activo" checked={data.Activo} onChange={(e) => setData('Activo', e.target.checked)} className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                    <InputLabel htmlFor="Activo" value="Activo" />
                                </div>
                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>Actualizar</PrimaryButton>
                                    <Link href={route('colonias.index')}><SecondaryButton type="button" disabled={processing}>Cancelar</SecondaryButton></Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
