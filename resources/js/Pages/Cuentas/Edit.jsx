import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Edit({ cuenta, conacs, cuentasMayor }) {
    const { data, setData, patch, processing, errors } = useForm({
        indetec: cuenta.indetec ?? '',
        nom_indetect: cuenta.nom_indetect ?? '',
        cuenta: cuenta.cuenta,
        subcuenta: cuenta.subcuenta,
        descripcion: cuenta.descripcion ?? '',
        importe: cuenta.importe ?? '',
        cuentaMayor_id: cuenta.cuentaMayor_id ?? '',
        conac_id: cuenta.conac_id ?? '',
    });

    const submit = (e) => {
        e.preventDefault();
        patch(route('cuentas.update', cuenta.id));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Editar Cuenta
                </h2>
            }
        >
            <Head title="Editar Cuenta" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="indetec" value="Indetec" />
                                    <TextInput
                                        id="indetec"
                                        type="text"
                                        name="indetec"
                                        value={data.indetec}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('indetec', e.target.value)
                                        }
                                    />
                                    <InputError
                                        message={errors.indetec}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="nom_indetect" value="Nombre Indetec" />
                                    <TextInput
                                        id="nom_indetect"
                                        type="text"
                                        name="nom_indetect"
                                        value={data.nom_indetect}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('nom_indetect', e.target.value)
                                        }
                                    />
                                    <InputError
                                        message={errors.nom_indetect}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="cuenta" value="Cuenta" />
                                    <TextInput
                                        id="cuenta"
                                        type="text"
                                        name="cuenta"
                                        value={data.cuenta}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('cuenta', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError
                                        message={errors.cuenta}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="subcuenta" value="Subcuenta" />
                                    <TextInput
                                        id="subcuenta"
                                        type="text"
                                        name="subcuenta"
                                        value={data.subcuenta}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('subcuenta', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError
                                        message={errors.subcuenta}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="descripcion" value="Descripción" />
                                    <textarea
                                        id="descripcion"
                                        name="descripcion"
                                        value={data.descripcion}
                                        onChange={(e) =>
                                            setData('descripcion', e.target.value)
                                        }
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="3"
                                    />
                                    <InputError
                                        message={errors.descripcion}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="importe" value="Importe" />
                                    <TextInput
                                        id="importe"
                                        type="number"
                                        name="importe"
                                        value={data.importe}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('importe', e.target.value)
                                        }
                                        step="0.01"
                                    />
                                    <InputError
                                        message={errors.importe}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="cuentaMayor_id" value="Cuenta Mayor" />
                                    <select
                                        id="cuentaMayor_id"
                                        name="cuentaMayor_id"
                                        value={data.cuentaMayor_id}
                                        onChange={(e) =>
                                            setData('cuentaMayor_id', e.target.value)
                                        }
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Seleccione una cuenta mayor</option>
                                        {cuentasMayor.map((cm) => (
                                            <option key={cm.id} value={cm.id}>
                                                {cm.descripcion || cm.cuenta}
                                            </option>
                                        ))}
                                    </select>
                                    <InputError
                                        message={errors.cuentaMayor_id}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="conac_id" value="Conac" />
                                    <select
                                        id="conac_id"
                                        name="conac_id"
                                        value={data.conac_id}
                                        onChange={(e) =>
                                            setData('conac_id', e.target.value)
                                        }
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Seleccione un conac</option>
                                        {conacs.map((conac) => (
                                            <option key={conac.id} value={conac.id}>
                                                {conac.descripcion || conac.nombre}
                                            </option>
                                        ))}
                                    </select>
                                    <InputError
                                        message={errors.conac_id}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>
                                        Actualizar
                                    </PrimaryButton>
                                    <Link
                                        href={route('cuentas.index')}
                                        className="rounded-md bg-gray-100 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition duration-150 ease-in-out hover:bg-gray-200 focus:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-gray-300"
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
