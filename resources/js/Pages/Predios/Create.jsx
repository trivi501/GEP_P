import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Create({
    tiposPredio,
    regimenesPropiedad,
    estadosRenta,
    estadosImpuesto,
    titulosPropiedad,
    orientaciones,
    zonasUrbana,
    formasPredio,
    usosPredioUrbano,
    estadosFisicos,
    pavimentos,
}) {
    const { data, setData, post, processing, errors } = useForm({
        Clave_predial: '',
        id_tipo_predio: '',
        id_contribuyente: '',
        contribuyente_search: '',
        id_calle: '',
        calle_search: '',
        id_colonia: '',
        colonia_search: '',
        Numero_exterior: '',
        Numero_interior: '',
        ubicacion: '',
        codigo_postal: '',
        superficie: '',
        construccion: '',
        id_zona_catastral: '',
        valor_catastral: '',
        valor_fiscal: '',
        id_regimen_propiedad: '',
        id_estado_renta: '',
        id_estaus_cobro_predial: 1,
        id_titulo_propiedad: '',
        numero_de_escritura: '',
        año_ultimo_pago: '',
        latitud: '',
        longitud: '',
        observacion: '',
        id_zona_urbana: '',
        numero_de_pisos_construidos: '',
        superficie_terreno_metros_cuadrados: '',
        Frente_metros: '',
        Fondo_metros: '',
        Baldio: false,
        id_forma_predio: '',
        id_uso_predio: '',
        id_estado_fisico: '',
        servicio_agua: false,
        servicio_drenaje: false,
        servicio_energia_electrica: false,
        servicio_alumbrado: false,
        id_pavimientacion: '',
        cuenta_con_banqueta: false,
        valor_catastral_terreno: '',
        valor_catastral_construido: '',
        medidas: [],
    });

    const [contribuyentes, setContribuyentes] = useState([]);
    const [showContribuyentes, setShowContribuyentes] = useState(false);
    const [callesResults, setCallesResults] = useState([]);
    const [showCalles, setShowCalles] = useState(false);
    const [coloniasResults, setColoniasResults] = useState([]);
    const [showColonias, setShowColonias] = useState(false);

    const searchContribuyente = async (q) => {
        setData('contribuyente_search', q);
        if (q.length < 2) {
            setContribuyentes([]);
            setShowContribuyentes(false);
            return;
        }
        try {
            const res = await fetch(route('contribuyentes.search') + '?q=' + encodeURIComponent(q) + '&solo_catastro=1');
            const json = await res.json();
            setContribuyentes(json);
            setShowContribuyentes(true);
        } catch {
            setContribuyentes([]);
        }
    };

    const searchCalle = async (q) => {
        setData('calle_search', q);
        if (q.length < 2) {
            setCallesResults([]);
            setShowCalles(false);
            return;
        }
        try {
            const res = await fetch(route('predios.calle-search') + '?q=' + encodeURIComponent(q));
            const json = await res.json();
            setCallesResults(json);
            setShowCalles(true);
        } catch {
            setCallesResults([]);
        }
    };

    const searchColonia = async (q) => {
        setData('colonia_search', q);
        if (q.length < 2) {
            setColoniasResults([]);
            setShowColonias(false);
            return;
        }
        try {
            const res = await fetch(route('predios.colonia-search') + '?q=' + encodeURIComponent(q));
            const json = await res.json();
            setColoniasResults(json);
            setShowColonias(true);
        } catch {
            setColoniasResults([]);
        }
    };

    const selectContribuyente = (c) => {
        setData('id_contribuyente', c.id_contribuyente);
        setData('contribuyente_search', c.nombre_completo + ' (' + c.cuenta + ')');
        setShowContribuyentes(false);
    };

    const selectCalle = (c) => {
        setData('id_calle', c.id_calle);
        setData('calle_search', c.CALLE);
        setShowCalles(false);
    };

    const selectColonia = (c) => {
        setData('id_colonia', c.id_colonia);
        setData('colonia_search', c.COLONIA);
        setShowColonias(false);
    };

    const addMedida = () => {
        setData('medidas', [...data.medidas, { id_orientacion: '', medida_en_metros: '', colinda_con: '' }]);
    };

    const removeMedida = (index) => {
        setData('medidas', data.medidas.filter((_, i) => i !== index));
    };

    const setMedida = (index, field, value) => {
        const updated = data.medidas.map((m, i) => (i === index ? { ...m, [field]: value } : m));
        setData('medidas', updated);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('predios.store'));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Crear Predio
                </h2>
            }
        >
            <Head title="Crear Predio" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <InputLabel htmlFor="Clave_predial" value="Clave Predial" />
                                        <TextInput
                                            id="Clave_predial"
                                            type="text"
                                            name="Clave_predial"
                                            value={data.Clave_predial}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('Clave_predial', e.target.value)}
                                            required
                                        />
                                        <InputError message={errors.Clave_predial} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="id_tipo_predio" value="Tipo Predio" />
                                        <select
                                            id="id_tipo_predio"
                                            name="id_tipo_predio"
                                            value={data.id_tipo_predio}
                                            onChange={(e) => setData('id_tipo_predio', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                            <option value="">Seleccione un tipo</option>
                                            {tiposPredio.map((t) => (
                                                <option key={t.id_tipo_predio} value={t.id_tipo_predio}>
                                                    {t.Tipo_predio}
                                                </option>
                                            ))}
                                        </select>
                                        <InputError message={errors.id_tipo_predio} className="mt-2" />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel htmlFor="contribuyente_search" value="Contribuyente" />
                                    <div className="relative">
                                        <TextInput
                                            id="contribuyente_search"
                                            type="text"
                                            name="contribuyente_search"
                                            value={data.contribuyente_search}
                                            className="mt-1 block w-full"
                                            onChange={(e) => searchContribuyente(e.target.value)}
                                            placeholder="Buscar por nombre o cuenta..."
                                            onFocus={() => data.contribuyente_search.length >= 2 && setShowContribuyentes(true)}
                                            onBlur={() => setTimeout(() => setShowContribuyentes(false), 200)}
                                        />
                                        {showContribuyentes && contribuyentes.length > 0 && (
                                            <ul className="absolute z-10 mt-1 max-h-48 w-full overflow-auto rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 shadow-lg">
                                                {contribuyentes.map((c) => (
                                                    <li
                                                        key={c.id_contribuyente}
                                                        onClick={() => selectContribuyente(c)}
                                                        className="cursor-pointer px-3 py-2 text-sm hover:bg-indigo-50"
                                                    >
                                                        {c.nombre_completo} ({c.cuenta})
                                                    </li>
                                                ))}
                                            </ul>
                                        )}
                                    </div>
                                    <InputError message={errors.id_contribuyente} className="mt-2" />
                                </div>

                                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div className="relative">
                                        <InputLabel htmlFor="calle_search" value="Calle" />
                                        <TextInput
                                            id="calle_search"
                                            type="text"
                                            name="calle_search"
                                            value={data.calle_search}
                                            className="mt-1 block w-full"
                                            onChange={(e) => searchCalle(e.target.value)}
                                            placeholder="Buscar calle..."
                                            onFocus={() => data.calle_search.length >= 2 && setShowCalles(true)}
                                            onBlur={() => setTimeout(() => setShowCalles(false), 200)}
                                        />
                                        {showCalles && callesResults.length > 0 && (
                                            <ul className="absolute z-10 mt-1 max-h-48 w-full overflow-auto rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 shadow-lg">
                                                {callesResults.map((c) => (
                                                    <li
                                                        key={c.id_calle}
                                                        onClick={() => selectCalle(c)}
                                                        className="cursor-pointer px-3 py-2 text-sm hover:bg-indigo-50 dark:hover:bg-gray-600"
                                                    >
                                                        {c.CALLE}
                                                    </li>
                                                ))}
                                            </ul>
                                        )}
                                        <InputError message={errors.id_calle} className="mt-2" />
                                    </div>

                                    <div className="relative">
                                        <InputLabel htmlFor="colonia_search" value="Colonia" />
                                        <TextInput
                                            id="colonia_search"
                                            type="text"
                                            name="colonia_search"
                                            value={data.colonia_search}
                                            className="mt-1 block w-full"
                                            onChange={(e) => searchColonia(e.target.value)}
                                            placeholder="Buscar colonia..."
                                            onFocus={() => data.colonia_search.length >= 2 && setShowColonias(true)}
                                            onBlur={() => setTimeout(() => setShowColonias(false), 200)}
                                        />
                                        {showColonias && coloniasResults.length > 0 && (
                                            <ul className="absolute z-10 mt-1 max-h-48 w-full overflow-auto rounded-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 shadow-lg">
                                                {coloniasResults.map((c) => (
                                                    <li
                                                        key={c.id_colonia}
                                                        onClick={() => selectColonia(c)}
                                                        className="cursor-pointer px-3 py-2 text-sm hover:bg-indigo-50 dark:hover:bg-gray-600"
                                                    >
                                                        {c.COLONIA}
                                                    </li>
                                                ))}
                                            </ul>
                                        )}
                                        <InputError message={errors.id_colonia} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="Numero_exterior" value="Número Exterior" />
                                        <TextInput
                                            id="Numero_exterior"
                                            type="text"
                                            name="Numero_exterior"
                                            value={data.Numero_exterior}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('Numero_exterior', e.target.value)}
                                        />
                                        <InputError message={errors.Numero_exterior} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="Numero_interior" value="Número Interior" />
                                        <TextInput
                                            id="Numero_interior"
                                            type="text"
                                            name="Numero_interior"
                                            value={data.Numero_interior}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('Numero_interior', e.target.value)}
                                        />
                                        <InputError message={errors.Numero_interior} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="ubicacion" value="Ubicación" />
                                        <TextInput
                                            id="ubicacion"
                                            type="text"
                                            name="ubicacion"
                                            value={data.ubicacion}
                                            className="mt-1 block w-full"
                                            onChange={(e) => setData('ubicacion', e.target.value)}
                                        />
                                        <InputError message={errors.ubicacion} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="codigo_postal" value="C.P." />
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

                                <div className="border-t pt-4">
                                    <h4 className="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Datos Catastrales</h4>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <InputLabel htmlFor="superficie" value="Superficie" />
                                            <TextInput
                                                id="superficie"
                                                type="number"
                                                name="superficie"
                                                value={data.superficie}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('superficie', e.target.value)}
                                                step="0.0001"
                                            />
                                            <InputError message={errors.superficie} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="construccion" value="Construcción" />
                                            <TextInput
                                                id="construccion"
                                                type="number"
                                                name="construccion"
                                                value={data.construccion}
                                                className="mt-1 block w-full disabled:bg-gray-100 dark:disabled:bg-gray-800"
                                                onChange={(e) => setData('construccion', e.target.value)}
                                                step="0.0001"
                                                disabled={data.Baldio}
                                            />
                                            {data.Baldio && (
                                                <p className="mt-1 text-xs text-gray-500 dark:text-gray-400">No aplica: el predio está marcado como baldío.</p>
                                            )}
                                            <InputError message={errors.construccion} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="valor_catastral" value="Valor Catastral" />
                                            <TextInput
                                                id="valor_catastral"
                                                type="number"
                                                name="valor_catastral"
                                                value={data.valor_catastral}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('valor_catastral', e.target.value)}
                                                step="0.01"
                                            />
                                            <InputError message={errors.valor_catastral} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="valor_fiscal" value="Valor Fiscal" />
                                            <TextInput
                                                id="valor_fiscal"
                                                type="number"
                                                name="valor_fiscal"
                                                value={data.valor_fiscal}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('valor_fiscal', e.target.value)}
                                                step="0.01"
                                            />
                                            <InputError message={errors.valor_fiscal} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="numero_de_escritura" value="Número de Escritura" />
                                            <TextInput
                                                id="numero_de_escritura"
                                                type="text"
                                                name="numero_de_escritura"
                                                value={data.numero_de_escritura}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('numero_de_escritura', e.target.value)}
                                            />
                                            <InputError message={errors.numero_de_escritura} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="año_ultimo_pago" value="Año Último Pago" />
                                            <TextInput
                                                id="año_ultimo_pago"
                                                type="number"
                                                name="año_ultimo_pago"
                                                value={data.año_ultimo_pago}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('año_ultimo_pago', e.target.value)}
                                            />
                                            <InputError message={errors.año_ultimo_pago} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="latitud" value="Latitud" />
                                            <TextInput
                                                id="latitud"
                                                type="number"
                                                name="latitud"
                                                value={data.latitud}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('latitud', e.target.value)}
                                                step="any"
                                            />
                                            <InputError message={errors.latitud} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="longitud" value="Longitud" />
                                            <TextInput
                                                id="longitud"
                                                type="number"
                                                name="longitud"
                                                value={data.longitud}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('longitud', e.target.value)}
                                                step="any"
                                            />
                                            <InputError message={errors.longitud} className="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div className="border-t pt-4">
                                    <h4 className="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Información Legal</h4>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <InputLabel htmlFor="id_regimen_propiedad" value="Régimen Propiedad" />
                                            <select
                                                id="id_regimen_propiedad"
                                                name="id_regimen_propiedad"
                                                value={data.id_regimen_propiedad}
                                                onChange={(e) => setData('id_regimen_propiedad', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un régimen</option>
                                                {regimenesPropiedad.map((r) => (
                                                    <option key={r.id_regimen_propiedad} value={r.id_regimen_propiedad}>
                                                        {r.REGIMEN}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_regimen_propiedad} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_estado_renta" value="Estado Renta" />
                                            <select
                                                id="id_estado_renta"
                                                name="id_estado_renta"
                                                value={data.id_estado_renta}
                                                onChange={(e) => setData('id_estado_renta', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un estado</option>
                                                {estadosRenta.map((r) => (
                                                    <option key={r.id_estado_renta} value={r.id_estado_renta}>
                                                        {r.DESCRIPCION}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_estado_renta} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_estaus_cobro_predial" value="Estatus Cobro Predial" />
                                            <select
                                                id="id_estaus_cobro_predial"
                                                name="id_estaus_cobro_predial"
                                                value={data.id_estaus_cobro_predial}
                                                onChange={(e) => setData('id_estaus_cobro_predial', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un estado</option>
                                                {estadosImpuesto.map((e) => (
                                                    <option key={e.id_estaus_cobro_predial} value={e.id_estaus_cobro_predial}>
                                                        {e.DESCRIPCION}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_estaus_cobro_predial} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_titulo_propiedad" value="Título Propiedad" />
                                            <select
                                                id="id_titulo_propiedad"
                                                name="id_titulo_propiedad"
                                                value={data.id_titulo_propiedad}
                                                onChange={(e) => setData('id_titulo_propiedad', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un título</option>
                                                {titulosPropiedad.map((t) => (
                                                    <option key={t.id_titulo_propiedad} value={t.id_titulo_propiedad}>
                                                        {t.DESCRIPCION}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_titulo_propiedad} className="mt-2" />
                                        </div>
                                    </div>
                                </div>

                                <div className="border-t pt-4">
                                    <h4 className="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Datos Urbanos</h4>
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <InputLabel htmlFor="id_zona_urbana" value="Zona Urbana" />
                                            <select
                                                id="id_zona_urbana"
                                                name="id_zona_urbana"
                                                value={data.id_zona_urbana}
                                                onChange={(e) => setData('id_zona_urbana', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione una zona</option>
                                                {zonasUrbana.map((z) => (
                                                    <option key={z.id_zona_urbana} value={z.id_zona_urbana}>
                                                        {z.descripcion}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_zona_urbana} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_forma_predio" value="Forma Predio" />
                                            <select
                                                id="id_forma_predio"
                                                name="id_forma_predio"
                                                value={data.id_forma_predio}
                                                onChange={(e) => setData('id_forma_predio', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione una forma</option>
                                                {formasPredio.map((f) => (
                                                    <option key={f.id_forma_predio} value={f.id_forma_predio}>
                                                        {f.descripcion}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_forma_predio} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_uso_predio" value="Uso Predio" />
                                            <select
                                                id="id_uso_predio"
                                                name="id_uso_predio"
                                                value={data.id_uso_predio}
                                                onChange={(e) => setData('id_uso_predio', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un uso</option>
                                                {usosPredioUrbano.map((u) => (
                                                    <option key={u.id_uso_predio} value={u.id_uso_predio}>
                                                        {u.descripcion}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_uso_predio} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_estado_fisico" value="Estado Físico" />
                                            <select
                                                id="id_estado_fisico"
                                                name="id_estado_fisico"
                                                value={data.id_estado_fisico}
                                                onChange={(e) => setData('id_estado_fisico', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un estado</option>
                                                {estadosFisicos.map((ef) => (
                                                    <option key={ef.id_estado_fisico} value={ef.id_estado_fisico}>
                                                        {ef.DESCRIPCION}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_estado_fisico} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="id_pavimientacion" value="Pavimento" />
                                            <select
                                                id="id_pavimientacion"
                                                name="id_pavimientacion"
                                                value={data.id_pavimientacion}
                                                onChange={(e) => setData('id_pavimientacion', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Seleccione un pavimento</option>
                                                {pavimentos.map((p) => (
                                                    <option key={p.id_pavimientacion} value={p.id_pavimientacion}>
                                                        {p.DESCRIPCION}
                                                    </option>
                                                ))}
                                            </select>
                                            <InputError message={errors.id_pavimientacion} className="mt-2" />
                                        </div>

                                        <div className="flex items-end">
                                            <label className="flex items-center gap-2">
                                                <input
                                                    type="checkbox"
                                                    checked={data.Baldio}
                                                    onChange={(e) => {
                                                        const checked = e.target.checked;
                                                        setData(prev => ({ ...prev, Baldio: checked, construccion: checked ? '' : prev.construccion }));
                                                    }}
                                                    className="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                />
                                                <span className="text-sm font-medium text-gray-700">Baldío</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div className="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <InputLabel htmlFor="superficie_terreno_metros_cuadrados" value="Sup. Terreno (m²)" />
                                            <TextInput
                                                id="superficie_terreno_metros_cuadrados"
                                                type="number"
                                                name="superficie_terreno_metros_cuadrados"
                                                value={data.superficie_terreno_metros_cuadrados}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('superficie_terreno_metros_cuadrados', e.target.value)}
                                                step="0.01"
                                            />
                                            <InputError message={errors.superficie_terreno_metros_cuadrados} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="Frente_metros" value="Frente (m)" />
                                            <TextInput
                                                id="Frente_metros"
                                                type="number"
                                                name="Frente_metros"
                                                value={data.Frente_metros}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('Frente_metros', e.target.value)}
                                                step="0.01"
                                            />
                                            <InputError message={errors.Frente_metros} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="Fondo_metros" value="Fondo (m)" />
                                            <TextInput
                                                id="Fondo_metros"
                                                type="number"
                                                name="Fondo_metros"
                                                value={data.Fondo_metros}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('Fondo_metros', e.target.value)}
                                                step="0.01"
                                            />
                                            <InputError message={errors.Fondo_metros} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="numero_de_pisos_construidos" value="No. Pisos Construidos" />
                                            <TextInput
                                                id="numero_de_pisos_construidos"
                                                type="number"
                                                name="numero_de_pisos_construidos"
                                                value={data.numero_de_pisos_construidos}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('numero_de_pisos_construidos', e.target.value)}
                                            />
                                            <InputError message={errors.numero_de_pisos_construidos} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="valor_catastral_terreno" value="Valor Catastral Terreno" />
                                            <TextInput
                                                id="valor_catastral_terreno"
                                                type="number"
                                                name="valor_catastral_terreno"
                                                value={data.valor_catastral_terreno}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('valor_catastral_terreno', e.target.value)}
                                                step="0.01"
                                            />
                                            <InputError message={errors.valor_catastral_terreno} className="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel htmlFor="valor_catastral_construido" value="Valor Catastral Construido" />
                                            <TextInput
                                                id="valor_catastral_construido"
                                                type="number"
                                                name="valor_catastral_construido"
                                                value={data.valor_catastral_construido}
                                                className="mt-1 block w-full"
                                                onChange={(e) => setData('valor_catastral_construido', e.target.value)}
                                                step="0.01"
                                            />
                                            <InputError message={errors.valor_catastral_construido} className="mt-2" />
                                        </div>
                                    </div>

                                    <div className="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        {[
                                            { key: 'servicio_agua', label: 'Agua' },
                                            { key: 'servicio_drenaje', label: 'Drenaje' },
                                            { key: 'servicio_energia_electrica', label: 'Energía Eléctrica' },
                                            { key: 'servicio_alumbrado', label: 'Alumbrado' },
                                            { key: 'cuenta_con_banqueta', label: 'Banqueta' },
                                        ].map(({ key, label }) => (
                                            <div key={key} className="flex items-end">
                                                <label className="flex items-center gap-2">
                                                    <input
                                                        type="checkbox"
                                                        checked={data[key]}
                                                        onChange={(e) => setData(key, e.target.checked)}
                                                        className="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                    />
                                                    <span className="text-sm font-medium text-gray-700">{label}</span>
                                                </label>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                <div className="border-t pt-4">
                                    <h4 className="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Orientaciones y Medidas</h4>
                                    {data.medidas.map((medida, index) => (
                                        <div key={index} className="mb-3 grid grid-cols-1 gap-3 sm:grid-cols-4">
                                            <div>
                                                <InputLabel value="Orientación" />
                                                <select
                                                    value={medida.id_orientacion}
                                                    onChange={(e) => setMedida(index, 'id_orientacion', e.target.value)}
                                                    className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                >
                                                    <option value="">Seleccione</option>
                                                    {orientaciones.map((o) => (
                                                        <option key={o.id_orientacion} value={o.id_orientacion}>
                                                            {o.descripcion || o.ORIENTA}
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>
                                            <div>
                                                <InputLabel value="Medida (m)" />
                                                <TextInput
                                                    type="number"
                                                    value={medida.medida_en_metros}
                                                    className="mt-1 block w-full"
                                                    onChange={(e) => setMedida(index, 'medida_en_metros', e.target.value)}
                                                    step="0.0001"
                                                />
                                            </div>
                                            <div>
                                                <InputLabel value="Colinda con" />
                                                <TextInput
                                                    type="text"
                                                    value={medida.colinda_con}
                                                    className="mt-1 block w-full"
                                                    onChange={(e) => setMedida(index, 'colinda_con', e.target.value)}
                                                />
                                            </div>
                                            <div className="flex items-end">
                                                <button
                                                    type="button"
                                                    onClick={() => removeMedida(index)}
                                                    className="mb-1 rounded-md bg-red-500 px-3 py-2 text-xs font-semibold text-white hover:bg-red-400"
                                                >
                                                    Eliminar
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                    <button
                                        type="button"
                                        onClick={addMedida}
                                        className="rounded-md bg-gray-100 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition duration-150 ease-in-out hover:bg-gray-200"
                                    >
                                        + Agregar Medida
                                    </button>
                                </div>

                                <div className="border-t pt-4">
                                    <div>
                                        <InputLabel htmlFor="observacion" value="Observación" />
                                        <textarea
                                            id="observacion"
                                            name="observacion"
                                            value={data.observacion}
                                            onChange={(e) => setData('observacion', e.target.value)}
                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            rows="3"
                                        />
                                        <InputError message={errors.observacion} className="mt-2" />
                                    </div>
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>
                                        Guardar
                                    </PrimaryButton>
                                    <Link
                                        href={route('predios.index')}
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
