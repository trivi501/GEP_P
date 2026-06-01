import Checkbox from '@/Components/Checkbox';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Create({
    tiposContribuyente,
    paises,
    estados,
    municipios,
    colonias,
    regimenesFiscales,
}) {
    const { data, setData, post, processing, errors } = useForm({
        id_tipo_contribuyente: '',
        cuenta: '',
        nombre: '',
        primer_apellido: '',
        segundo_apellido: '',
        nombre_moral: '',
        rfc: '',
        curp_contribuyente: '',
        telefono: '',
        correo_electronico: '',
        nombre_vialidad: '',
        num_exterior: '',
        num_interior: '',
        colonia: '',
        id_pais: '',
        id_estado: '',
        id_municipio: '',
        codigo_postal: '',
        fact_id_regimen_fiscal: '',
        activo: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('contribuyentes.store'));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Crear Contribuyente
                </h2>
            }
        >
            <Head title="Crear Contribuyente" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <InputLabel htmlFor="id_tipo_contribuyente" value="Tipo Contribuyente" />
                                        <select
                                            id="id_tipo_contribuyente"
                                            name="id_tipo_contribuyente"
                                            value={data.id_tipo_contribuyente}
                                            onChange={(e) => setData('id_tipo_contribuyente', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                            <option value="">Seleccione un tipo</option>
                                            {tiposContribuyente.map((tipo) => (
                                                <option key={tipo.id} value={tipo.id}>
                                                    {tipo.name}
                                                </option>
                                            ))}
                                        </select>
                                        <InputError message={errors.id_tipo_contribuyente} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="cuenta" value="Cuenta" />
                                        <TextInput
                                            id="cuenta"
                                            type="text"
                                            name="cuenta"
                                            value={data.cuenta}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('cuenta', e.target.value)}
                                        />
                                        <InputError message={errors.cuenta} className="mt-2" />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <div>
                                        <InputLabel htmlFor="nombre" value="Nombre" />
                                        <TextInput
                                            id="nombre"
                                            type="text"
                                            name="nombre"
                                            value={data.nombre}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('nombre', e.target.value)}
                                        />
                                        <InputError message={errors.nombre} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="primer_apellido" value="Primer Apellido" />
                                        <TextInput
                                            id="primer_apellido"
                                            type="text"
                                            name="primer_apellido"
                                            value={data.primer_apellido}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('primer_apellido', e.target.value)}
                                        />
                                        <InputError message={errors.primer_apellido} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="segundo_apellido" value="Segundo Apellido" />
                                        <TextInput
                                            id="segundo_apellido"
                                            type="text"
                                            name="segundo_apellido"
                                            value={data.segundo_apellido}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('segundo_apellido', e.target.value)}
                                        />
                                        <InputError message={errors.segundo_apellido} className="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel htmlFor="nombre_moral" value="Nombre Moral" />
                                    <TextInput
                                        id="nombre_moral"
                                        type="text"
                                        name="nombre_moral"
                                        value={data.nombre_moral}
                                        className="mt-1 block w-full"
                                        onChange={(e) => setData('nombre_moral', e.target.value)}
                                    />
                                    <InputError message={errors.nombre_moral} className="mt-2" />
                                </div>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <InputLabel htmlFor="rfc" value="RFC" />
                                        <TextInput
                                            id="rfc"
                                            type="text"
                                            name="rfc"
                                            value={data.rfc}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('rfc', e.target.value)}
                                        />
                                        <InputError message={errors.rfc} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="curp_contribuyente" value="CURP" />
                                        <TextInput
                                            id="curp_contribuyente"
                                            type="text"
                                            name="curp_contribuyente"
                                            value={data.curp_contribuyente}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('curp_contribuyente', e.target.value)}
                                        />
                                        <InputError message={errors.curp_contribuyente} className="mt-2" />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <InputLabel htmlFor="telefono" value="Teléfono" />
                                        <TextInput
                                            id="telefono"
                                            type="text"
                                            name="telefono"
                                            value={data.telefono}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('telefono', e.target.value)}
                                        />
                                        <InputError message={errors.telefono} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="correo_electronico" value="Correo Electrónico" />
                                        <TextInput
                                            id="correo_electronico"
                                            type="email"
                                            name="correo_electronico"
                                            value={data.correo_electronico}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('correo_electronico', e.target.value)}
                                        />
                                        <InputError message={errors.correo_electronico} className="mt-2" />
                                    </div>
                                </div>

                                <div className="border-t pt-6">
                                    <h4 className="mb-4 text-base font-medium text-gray-900 dark:text-gray-100">
                                        Dirección
                                    </h4>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <InputLabel htmlFor="nombre_vialidad" value="Calle" />
                                            <TextInput
                                                id="nombre_vialidad"
                                                type="text"
                                                name="nombre_vialidad"
                                                value={data.nombre_vialidad}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('nombre_vialidad', e.target.value)}
                                            />
                                            <InputError message={errors.nombre_vialidad} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="num_exterior" value="Num. Exterior" />
                                            <TextInput
                                                id="num_exterior"
                                                type="text"
                                                name="num_exterior"
                                                value={data.num_exterior}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('num_exterior', e.target.value)}
                                            />
                                            <InputError message={errors.num_exterior} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="num_interior" value="Num. Interior" />
                                            <TextInput
                                                id="num_interior"
                                                type="text"
                                                name="num_interior"
                                                value={data.num_interior}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('num_interior', e.target.value)}
                                            />
                                            <InputError message={errors.num_interior} className="mt-2" />
                                        </div>
                                    </div>

                                    <div className="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <InputLabel htmlFor="colonia" value="Colonia" />
                                            <select
                                                id="colonia"
                                                name="colonia"
                                                value={data.colonia}
                                                onChange={(e) => setData('colonia', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione una colonia</option>
                                                {colonias.map((colonia) => (
                                                    <option key={colonia.id} value={colonia.name}>
                                                        {colonia.name}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.colonia} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="codigo_postal" value="Código Postal" />
                                            <TextInput
                                                id="codigo_postal"
                                                type="text"
                                                name="codigo_postal"
                                                value={data.codigo_postal}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('codigo_postal', e.target.value)}
                                            />
                                            <InputError message={errors.codigo_postal} className="mt-2" />
                                        </div>
                                    </div>

                                    <div className="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <InputLabel htmlFor="id_pais" value="País" />
                                            <select
                                                id="id_pais"
                                                name="id_pais"
                                                value={data.id_pais}
                                                onChange={(e) => setData('id_pais', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un país</option>
                                                {paises.map((pais) => (
                                                    <option key={pais.id} value={pais.id}>
                                                        {pais.name}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_pais} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_estado" value="Estado" />
                                            <select
                                                id="id_estado"
                                                name="id_estado"
                                                value={data.id_estado}
                                                onChange={(e) => setData('id_estado', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un estado</option>
                                                {estados.map((estado) => (
                                                    <option key={estado.id} value={estado.id}>
                                                        {estado.name}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_estado} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_municipio" value="Municipio" />
                                            <select
                                                id="id_municipio"
                                                name="id_municipio"
                                                value={data.id_municipio}
                                                onChange={(e) => setData('id_municipio', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un municipio</option>
                                                {municipios.map((municipio) => (
                                                    <option key={municipio.id} value={municipio.id}>
                                                        {municipio.name}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_municipio} className="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <InputLabel htmlFor="fact_id_regimen_fiscal" value="Régimen Fiscal" />
                                    <select
                                        id="fact_id_regimen_fiscal"
                                        name="fact_id_regimen_fiscal"
                                        value={data.fact_id_regimen_fiscal}
                                        onChange={(e) => setData('fact_id_regimen_fiscal', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Seleccione un régimen fiscal</option>
                                        {regimenesFiscales.map((regimen) => (
                                            <option key={regimen.id} value={regimen.id}>
                                                {regimen.name}
                                            </option>
                                        ))}
                                    </select>
                                    <InputError message={errors.fact_id_regimen_fiscal} className="mt-2" />
                                </div>

                                <div className="flex items-center gap-2">
                                    <Checkbox
                                        id="activo"
                                        name="activo"
                                        checked={data.activo}
                                        onChange={(e) => setData('activo', e.target.checked)}
                                    />
                                    <InputLabel htmlFor="activo" value="Activo" />
                                    <InputError message={errors.activo} className="mt-2" />
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>
                                        Guardar
                                    </PrimaryButton>
                                    <Link href={route('contribuyentes.index')}>
                                        <SecondaryButton type="button" disabled={processing}>
                                            Cancelar
                                        </SecondaryButton>
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
