import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Edit({ permission, categorias }) {
    const { data, setData, patch, processing, errors } = useForm({
        name: permission.name,
        nombre_mostrar: permission.nombre_mostrar ?? '',
        categoria: permission.categoria ?? '',
        guard_name: permission.guard_name,
    });

    const submit = (e) => {
        e.preventDefault();
        patch(route('permissions.update', permission.id));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Editar Permiso
                </h2>
            }
        >
            <Head title="Editar Permiso" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="name" value="Nombre (Clave)" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        name="name"
                                        value={data.name}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('name', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError
                                        message={errors.name}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="nombre_mostrar" value="Nombre Mostrar" />
                                    <TextInput
                                        id="nombre_mostrar"
                                        type="text"
                                        name="nombre_mostrar"
                                        value={data.nombre_mostrar}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('nombre_mostrar', e.target.value)
                                        }
                                    />
                                    <InputError
                                        message={errors.nombre_mostrar}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="categoria" value="Categoría" />
                                    <div className="mt-1 flex gap-2">
                                        <input
                                            type="text"
                                            id="categoria"
                                            name="categoria"
                                            value={data.categoria}
                                            onChange={(e) =>
                                                setData('categoria', e.target.value)
                                            }
                                            list="categorias-list"
                                            className="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                        <datalist id="categorias-list">
                                            {categorias.map((cat) => (
                                                <option key={cat} value={cat} />
                                            ))}
                                        </datalist>
                                    </div>
                                    <InputError
                                        message={errors.categoria}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel
                                        htmlFor="guard_name"
                                        value="Guard Name"
                                    />
                                    <select
                                        id="guard_name"
                                        name="guard_name"
                                        value={data.guard_name}
                                        onChange={(e) =>
                                            setData(
                                                'guard_name',
                                                e.target.value,
                                            )
                                        }
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="web">web</option>
                                        <option value="api">api</option>
                                    </select>
                                    <InputError
                                        message={errors.guard_name}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>
                                        Actualizar
                                    </PrimaryButton>
                                    <Link
                                        href={route('permissions.index')}
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
