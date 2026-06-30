import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, usePage } from '@inertiajs/react';

export default function Edit({ ordenPago, cuentas, secretarias, userSecretariaId, hoy }) {
    const permissions = Array.isArray(usePage().props.userPermissions) ? usePage().props.userPermissions : [];
    const can = (p) => permissions.includes(p);
    const userSecretaria = secretarias.find(s => s.id === userSecretariaId);
    const { data, setData, patch, processing, errors } = useForm({
        nombre: ordenPago.nombre,
        descripcion: ordenPago.descripcion ?? '',
        monto: ordenPago.monto,
        secretaria_id: ordenPago.secretaria_id ?? '',
        fecha: hoy ?? ordenPago.fecha ?? '',
        cuentas: ordenPago.cuentas_ordenes_pago?.map((c) => ({
            IdCuenta: c.IdCuenta,
            monto: c.monto,
            cantidad: c.cantidad,
            descuento: c.descuento ?? '',
        })) ?? [],
    });

    const totalCalculado = data.cuentas.reduce((sum, c) => {
        const subtotal = (parseFloat(c.monto) || 0) * (parseFloat(c.cantidad) || 0);
        const desc = parseFloat(c.descuento) || 0;
        return sum + subtotal * (1 - desc / 100);
    }, 0);

    const submit = (e) => {
        e.preventDefault();
        setData('monto', totalCalculado);
        patch(route('ordenes-pago.update', ordenPago.id));
    };

    const addCuenta = () => {
        setData('cuentas', [...data.cuentas, { IdCuenta: '', monto: '', cantidad: 1, descuento: '' }]);
    };

    const removeCuenta = (index) => {
        const updated = data.cuentas.filter((_, i) => i !== index);
        const newTotal = updated.reduce((sum, c) => {
            const subtotal = (parseFloat(c.monto) || 0) * (parseFloat(c.cantidad) || 0);
            const desc = parseFloat(c.descuento) || 0;
            return sum + subtotal * (1 - desc / 100);
        }, 0);
        setData('cuentas', updated);
        setData('monto', newTotal);
    };

    const updateCuenta = (index, field, value) => {
        const updated = data.cuentas.map((c, i) => (i === index ? { ...c, [field]: value } : c));
        const newTotal = updated.reduce((sum, c) => {
            const subtotal = (parseFloat(c.monto) || 0) * (parseFloat(c.cantidad) || 0);
            const desc = parseFloat(c.descuento) || 0;
            return sum + subtotal * (1 - desc / 100);
        }, 0);
        setData('cuentas', updated);
        setData('monto', newTotal);
    };

    const selectedIds = data.cuentas.map((c) => String(c.IdCuenta)).filter(Boolean);

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Editar Orden de Pago {ordenPago.folio ?? ('#' + ordenPago.id)}
                </h2>
            }
        >
            <Head title="Editar Orden de Pago" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div className="grid grid-cols-1 gap-6 sm:grid-cols-2">
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
                                        <InputLabel htmlFor="secretaria_id" value="Secretaría" />
                                        <input
                                            id="secretaria_id"
                                            type="text"
                                            value={userSecretaria?.nombre ?? '—'}
                                            disabled
                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm opacity-70 cursor-not-allowed"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="folio" value="Folio" />
                                        <input
                                            id="folio"
                                            type="text"
                                            value={ordenPago.folio ?? '—'}
                                            disabled
                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm opacity-70 cursor-not-allowed"
                                        />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="monto" value="Monto Total" />
                                        <input
                                            id="monto"
                                            type="number"
                                            step="0.01"
                                            value={data.monto}
                                            disabled
                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm opacity-70 cursor-not-allowed font-bold"
                                        />
                                        <InputError message={errors.monto} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="fecha" value="Fecha" />
                                        <input
                                            id="fecha"
                                            type="date"
                                            value={data.fecha}
                                            disabled
                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm opacity-70 cursor-not-allowed"
                                        />
                                        <InputError message={errors.fecha} className="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel htmlFor="vencimiento" value="Vigencia" />
                                        <input
                                            id="vencimiento"
                                            type="date"
                                            value={ordenPago.fecha_vencimiento ?? ''}
                                            disabled
                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm opacity-70 cursor-not-allowed"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <InputLabel htmlFor="descripcion" value="Descripción" />
                                    <textarea
                                        id="descripcion"
                                        rows="3"
                                        value={data.descripcion}
                                        onChange={(e) => setData('descripcion', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <InputError message={errors.descripcion} className="mt-2" />
                                </div>

                                <div className="border-t border-gray-200 dark:border-gray-600 pt-6">
                                    <div className="flex items-center justify-between mb-4">
                                        <h4 className="text-md font-semibold text-gray-900 dark:text-gray-100">Cuentas</h4>
                                        <button
                                            type="button"
                                            onClick={addCuenta}
                                            className="inline-flex items-center rounded-md border border-transparent bg-green-600 px-3 py-1 text-xs font-semibold uppercase tracking-widest text-white hover:bg-green-500"
                                        >
                                            + Agregar cuenta
                                        </button>
                                    </div>

                                    {data.cuentas.length > 0 ? (
                                        <div className="space-y-4">
                                            {data.cuentas.map((cuenta, index) => (
                                                <div key={index} className="flex items-start gap-4 p-4 border border-gray-200 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-700">
                                                    <div className="flex-1">
                                                        <InputLabel value="Cuenta" />
                                                        <select
                                                            value={cuenta.IdCuenta}
                                                            onChange={(e) => updateCuenta(index, 'IdCuenta', e.target.value)}
                                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            required
                                                        >
                                                            <option value="">Seleccione una cuenta</option>
                                                            {cuentas
                                                                .filter((c) => !selectedIds.includes(String(c.id)) || String(c.id) === String(cuenta.IdCuenta))
                                                                .map((c) => (
                                                                    <option key={c.id} value={c.id}>
                                                                        {c.descripcion || c.cuenta || `Cuenta #${c.id}`} {c.indetec ? `(${c.indetec})` : ''}
                                                                    </option>
                                                                ))}
                                                        </select>
                                                    </div>
                                                    <div className="w-32">
                                                        <InputLabel value="Monto" />
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            min="0"
                                                            value={cuenta.monto}
                                                            onChange={(e) => updateCuenta(index, 'monto', e.target.value)}
                                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            required
                                                        />
                                                    </div>
                                                    <div className="w-24">
                                                        <InputLabel value="Cantidad" />
                                                        <input
                                                            type="number"
                                                            step="1"
                                                            min="1"
                                                            value={cuenta.cantidad}
                                                            onChange={(e) => updateCuenta(index, 'cantidad', e.target.value)}
                                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                                            required
                                                        />
                                                    </div>
                                                    <div className="w-24">
                                                        <InputLabel value="Desc. %" />
                                                        <input
                                                            type="number"
                                                            step="0.01"
                                                            min="0"
                                                            max="100"
                                                            value={cuenta.descuento}
                                                            onChange={(e) => updateCuenta(index, 'descuento', e.target.value)}
                                                            disabled={!can('editar-descuentos')}
                                                            className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                                        />
                                                    </div>
                                                    <button
                                                        type="button"
                                                        onClick={() => removeCuenta(index)}
                                                        className="mt-6 inline-flex items-center justify-center w-8 h-8 rounded-full text-red-600 hover:bg-red-100 dark:hover:bg-red-900"
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                    </button>
                                                </div>
                                            ))}
                                        </div>
                                    ) : (
                                        <p className="text-sm text-gray-500 dark:text-gray-400">No hay cuentas agregadas. Presione "+ Agregar cuenta" para añadir una.</p>
                                    )}
                                    <InputError message={errors.cuentas} className="mt-2" />
                                </div>

                                <div className="flex items-center gap-4">
                                    <PrimaryButton disabled={processing}>Actualizar</PrimaryButton>
                                    <Link
                                        href={route('ordenes-pago.index')}
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
