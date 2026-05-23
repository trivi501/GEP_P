import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Edit({ secretaria, cuentas }) {
    const { data, setData, patch, processing, errors } = useForm({
        nombre: secretaria.nombre,
        cuentas: secretaria.cuentas?.map((c) => c.id) ?? [],
    });

    const submit = (e) => {
        e.preventDefault();
        patch(route('secretarias.update', secretaria.id));
    };

    const toggleCuenta = (cuentaId) => {
        const current = data.cuentas;
        if (current.includes(cuentaId)) {
            setData('cuentas', current.filter((id) => id !== cuentaId));
        } else {
            setData('cuentas', [...current, cuentaId]);
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Editar Secretaría
                </h2>
            }
        >
            <Head title="Editar Secretaría" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="nombre" value="Nombre" />
                                    <input
                                        id="nombre"
                                        type="text"
                                        value={data.nombre}
                                        onChange={(e) => setData('nombre', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    <InputError message={errors.nombre} className="mt-2" />
                                </div>

                                <div>
                                    <InputLabel value="Cuentas disponibles" />
                                    <div className="mt-2 max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-md p-2">
                                        {cuentas.length > 0 ? (
                                            cuentas.map((cuenta) => (
                                                <label key={cuenta.id} className="flex items-center gap-2 py-1 px-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer">
                                                    <input
                                                        type="checkbox"
                                                        checked={data.cuentas.includes(cuenta.id)}
                                                        onChange={() => toggleCuenta(cuenta.id)}
                                                        className="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                    />
                                                    <span className="text-sm text-gray-700 dark:text-gray-200">
                                                        {cuenta.descripcion || cuenta.cuenta || `Cuenta #${cuenta.id}`}
                                                        {cuenta.indetec ? ` (${cuenta.indetec})` : ''}
                                                    </span>
                                                </label>
                                            ))
                                        ) : (
                                            <p className="text-sm text-gray-500 py-2">No hay cuentas disponibles.</p>
                                        )}
                                    </div>
                                    <InputError message={errors.cuentas} className="mt-2" />
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>Actualizar</PrimaryButton>
                                    <Link
                                        href={route('secretarias.index')}
                                        className="rounded-md bg-gray-100 dark:bg-gray-700 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600"
                                    >
                                        Cancelar
                                    </Link>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
