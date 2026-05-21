import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Edit({ caja, usuarios }) {
    const { data, setData, patch, processing, errors } = useForm({
        nombre: caja.nombre,
        ubicacion: caja.ubicacion,
        folio: caja.folio,
        status: caja.status,
        cajeros: caja.cajeros?.map((c) => c.usuario_id) ?? [],
    });

    const submit = (e) => {
        e.preventDefault();
        patch(route('cajas.update', caja.id));
    };

    const handleCajeroChange = (usuarioId) => {
        setData('cajeros', data.cajeros.includes(usuarioId)
            ? data.cajeros.filter((id) => id !== usuarioId)
            : [...data.cajeros, usuarioId]
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Editar Caja
                </h2>
            }
        >
            <Head title="Editar Caja" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <InputLabel htmlFor="nombre" value="Nombre" />
                                    <TextInput
                                        id="nombre"
                                        type="text"
                                        name="nombre"
                                        value={data.nombre}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('nombre', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError
                                        message={errors.nombre}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="ubicacion" value="Ubicación" />
                                    <TextInput
                                        id="ubicacion"
                                        type="text"
                                        name="ubicacion"
                                        value={data.ubicacion}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('ubicacion', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError
                                        message={errors.ubicacion}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="folio" value="Folio" />
                                    <TextInput
                                        id="folio"
                                        type="text"
                                        name="folio"
                                        value={data.folio}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData('folio', e.target.value)
                                        }
                                        required
                                    />
                                    <InputError
                                        message={errors.folio}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel htmlFor="status" value="Status" />
                                    <select
                                        id="status"
                                        name="status"
                                        value={data.status}
                                        onChange={(e) =>
                                            setData('status', e.target.value)
                                        }
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value={1}>Activo</option>
                                        <option value={0}>Inactivo</option>
                                    </select>
                                    <InputError
                                        message={errors.status}
                                        className="mt-2"
                                    />
                                </div>

                                <div>
                                    <InputLabel value="Cajeros (Usuarios)" />
                                    <div className="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">
                                        {usuarios.map((usuario) => (
                                            <label
                                                key={usuario.id}
                                                className="flex items-center gap-2 rounded-md border border-gray-200 dark:border-gray-700 p-2 text-sm hover:bg-gray-50 dark:bg-gray-700"
                                            >
                                                <Checkbox
                                                    checked={data.cajeros.includes(usuario.id)}
                                                    onChange={() =>
                                                        handleCajeroChange(usuario.id)
                                                    }
                                                />
                                                {usuario.name}
                                            </label>
                                        ))}
                                    </div>
                                    <InputError
                                        message={errors.cajeros}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>
                                        Actualizar
                                    </PrimaryButton>
                                    <Link
                                        href={route('cajas.index')}
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
