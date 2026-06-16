import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import SearchSelect from '@/Components/SearchSelect';

export default function Create({ colonias }) {
    const { data, setData, post, processing, errors } = useForm({ CALLE: '', ID_COLONIA: '', Activo: true });

    const submit = (e) => { e.preventDefault(); post(route('calles.store')); };

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">Nueva Calle</h2>}>
            <Head title="Nueva Calle" />
            <div className="py-12">
                <div className="mx-auto max-w-lg sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="CALLE" value="Nombre de la Calle" />
                                    <input id="CALLE" type="text" value={data.CALLE} onChange={(e) => setData('CALLE', e.target.value)} className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
                                    <InputError message={errors.CALLE} className="mt-2" />
                                </div>
                                <div>
                                    <InputLabel htmlFor="ID_COLONIA" value="Colonia" />
                                    <SearchSelect value={data.ID_COLONIA} onChange={(v) => setData('ID_COLONIA', v)} options={colonias} valueKey="id_colonia" labelKey="COLONIA" placeholder="Seleccione una colonia" />
                                    <InputError message={errors.ID_COLONIA} className="mt-2" />
                                </div>
                                <div className="flex items-center gap-2">
                                    <input type="checkbox" id="Activo" checked={data.Activo} onChange={(e) => setData('Activo', e.target.checked)} className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                    <InputLabel htmlFor="Activo" value="Activo" />
                                </div>
                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>Guardar</PrimaryButton>
                                    <Link href={route('calles.index')}><SecondaryButton type="button" disabled={processing}>Cancelar</SecondaryButton></Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
