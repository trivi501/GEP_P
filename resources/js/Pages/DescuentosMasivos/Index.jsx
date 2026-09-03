import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

const csrfToken = () => document.querySelector('meta[name=csrf-token]')?.content;

const lastDayOfMonth = () => {
    const now = new Date();
    const last = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    return last.toISOString().slice(0, 10);
};

export default function Index() {
    const [cuenta, setCuenta] = useState('');
    const [contribuyentes, setContribuyentes] = useState([]);
    const [predios, setPredios] = useState([]);
    const [selected, setSelected] = useState({});
    const [searching, setSearching] = useState(false);
    const [applying, setApplying] = useState(false);
    const [searched, setSearched] = useState(false);
    const [feedback, setFeedback] = useState(null);
    const [errors, setErrors] = useState({});
    const [form, setForm] = useState({
        multas: '0',
        actualizaciones: '0',
        recargos: '0',
        aseo_publico: '0',
        gastos_cobranza: '0',
        fecha_expiracion: lastDayOfMonth(),
        activo: true,
    });

    const handleSearch = async (e) => {
        e.preventDefault();
        if (!cuenta.trim()) return;
        setSearching(true);
        setSearched(true);
        setSelected({});
        setFeedback(null);
        try {
            const res = await fetch(route('descuentos-masivos.search'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
                body: JSON.stringify({ cuenta }),
            });
            const data = await res.json();
            setContribuyentes(data.contribuyentes ?? []);
            setPredios(data.predios ?? []);
        } catch (err) {
            console.error(err);
        } finally {
            setSearching(false);
        }
    };

    const toggleSelect = (id) => {
        setSelected((prev) => ({ ...prev, [id]: !prev[id] }));
    };

    const selectAll = () => {
        const allSelected = predios.every((p) => selected[p.id]);
        const newSelected = {};
        if (!allSelected) {
            predios.forEach((p) => { newSelected[p.id] = true; });
        }
        setSelected(newSelected);
    };

    const allSelected = predios.length > 0 && predios.every((p) => selected[p.id]);
    const selectedCount = Object.values(selected).filter(Boolean).length;

    const handleAplicar = async () => {
        const ids = Object.entries(selected).filter(([, v]) => v).map(([k]) => k);
        if (ids.length === 0) return;

        const confirmMsg = `¿Aplicar este descuento a ${ids.length} predio(s)? Cualquier descuento activo existente en esos predios será reemplazado.`;
        if (!window.confirm(confirmMsg)) return;

        setApplying(true);
        setFeedback(null);
        setErrors({});
        try {
            const res = await fetch(route('descuentos-masivos.aplicar'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ predios: ids, ...form }),
            });

            if (res.status === 422) {
                const data = await res.json();
                setErrors(data.errors ?? {});
                setFeedback({ type: 'error', message: 'Revisa los datos del formulario.' });
                return;
            }

            if (!res.ok) {
                setFeedback({ type: 'error', message: 'Ocurrió un error al aplicar el descuento masivo.' });
                return;
            }

            const data = await res.json();
            setFeedback({ type: 'success', message: `Descuento aplicado a ${data.aplicados} predio(s).` });
            setPredios((prev) => prev.map((p) => (selected[p.id] ? { ...p, tiene_descuento_activo: true } : p)));
            setSelected({});
        } catch (err) {
            console.error(err);
            setFeedback({ type: 'error', message: 'Ocurrió un error al aplicar el descuento masivo.' });
        } finally {
            setApplying(false);
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Descuentos Masivos
                </h2>
            }
        >
            <Head title="Descuentos Masivos" />

            <div className="py-12">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                    Búsqueda por Cuenta
                                </h3>
                                <form onSubmit={handleSearch} className="flex gap-2">
                                    <input
                                        type="text"
                                        value={cuenta}
                                        onChange={(e) => setCuenta(e.target.value)}
                                        placeholder="Ingrese el número de cuenta del contribuyente..."
                                        className="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <button
                                        type="submit"
                                        disabled={searching}
                                        className="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                    >
                                        {searching ? 'Buscando...' : 'Buscar'}
                                    </button>
                                </form>
                            </div>

                            {contribuyentes.length > 0 && (
                                <div className="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h4 className="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                        Contribuyente(s) encontrado(s):
                                    </h4>
                                    {contribuyentes.map((c) => (
                                        <p key={c.id_contribuyente} className="text-sm text-gray-600 dark:text-gray-400">
                                            <span className="font-medium">{c.cuenta}</span> — {c.nombre_completo}
                                        </p>
                                    ))}
                                </div>
                            )}

                            {feedback && (
                                <div className={`mb-6 p-4 rounded-lg text-sm ${feedback.type === 'success' ? 'bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-50 text-red-800 dark:bg-red-900/30 dark:text-red-300'}`}>
                                    {feedback.message}
                                </div>
                            )}

                            {predios.length > 0 && (
                                <>
                                    <div className="mb-4 flex items-center justify-between">
                                        <div className="flex items-center gap-4">
                                            <button
                                                onClick={selectAll}
                                                className="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600"
                                            >
                                                {allSelected ? 'Deseleccionar Todos' : 'Seleccionar Todos'}
                                            </button>
                                            <span className="text-sm text-gray-500 dark:text-gray-400">
                                                {predios.length} predio(s) encontrados
                                            </span>
                                        </div>
                                    </div>

                                    <div className="overflow-x-auto mb-6">
                                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead className="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th className="px-4 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 w-10">
                                                        <input
                                                            type="checkbox"
                                                            checked={allSelected}
                                                            onChange={selectAll}
                                                            className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                        />
                                                    </th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Clave Catastral</th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cuenta</th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Contribuyente</th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Colonia</th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                                                    <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Año Último Pago</th>
                                                    <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Descuento Activo</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                {predios.map((predio) => (
                                                    <tr
                                                        key={predio.id}
                                                        className={`hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer ${selected[predio.id] ? 'bg-indigo-50 dark:bg-indigo-900/20' : ''}`}
                                                        onClick={() => toggleSelect(predio.id)}
                                                    >
                                                        <td className="px-4 py-4 text-center" onClick={(e) => e.stopPropagation()}>
                                                            <input
                                                                type="checkbox"
                                                                checked={!!selected[predio.id]}
                                                                onChange={() => toggleSelect(predio.id)}
                                                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                                            />
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {predio.Clave_predial}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                            {predio.cuenta}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                            {predio.contribuyente}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                            {predio.colonia}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                            {predio.tipo_predio}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                                            {predio.año_ultimo_pago}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-center">
                                                            {predio.tiene_descuento_activo && (
                                                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                                                    Sí
                                                                </span>
                                                            )}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>

                                    <div className="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                                            Datos del Descuento a Aplicar
                                        </h3>

                                        <div className="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Multas %</label>
                                                <input type="number" step="0.01" min="0" max="100" value={form.multas}
                                                    onChange={(e) => setForm({ ...form, multas: e.target.value })}
                                                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                                {errors.multas && <p className="mt-1 text-xs text-red-600">{errors.multas[0]}</p>}
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Actualizaciones %</label>
                                                <input type="number" step="0.01" min="0" max="100" value={form.actualizaciones}
                                                    onChange={(e) => setForm({ ...form, actualizaciones: e.target.value })}
                                                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                                {errors.actualizaciones && <p className="mt-1 text-xs text-red-600">{errors.actualizaciones[0]}</p>}
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recargos %</label>
                                                <input type="number" step="0.01" min="0" max="100" value={form.recargos}
                                                    onChange={(e) => setForm({ ...form, recargos: e.target.value })}
                                                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                                {errors.recargos && <p className="mt-1 text-xs text-red-600">{errors.recargos[0]}</p>}
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">A.P. %</label>
                                                <input type="number" step="0.01" min="0" max="100" value={form.aseo_publico}
                                                    onChange={(e) => setForm({ ...form, aseo_publico: e.target.value })}
                                                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                                {errors.aseo_publico && <p className="mt-1 text-xs text-red-600">{errors.aseo_publico[0]}</p>}
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gtos Cobranza %</label>
                                                <input type="number" step="0.01" min="0" max="100" value={form.gastos_cobranza}
                                                    onChange={(e) => setForm({ ...form, gastos_cobranza: e.target.value })}
                                                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                                {errors.gastos_cobranza && <p className="mt-1 text-xs text-red-600">{errors.gastos_cobranza[0]}</p>}
                                            </div>
                                        </div>

                                        <div className="flex flex-wrap items-end gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Expiración</label>
                                                <input type="date" value={form.fecha_expiracion}
                                                    onChange={(e) => setForm({ ...form, fecha_expiracion: e.target.value })}
                                                    className="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                                                {errors.fecha_expiracion && <p className="mt-1 text-xs text-red-600">{errors.fecha_expiracion[0]}</p>}
                                            </div>
                                            <label className="flex items-center gap-2 pb-2">
                                                <input type="checkbox" checked={form.activo}
                                                    onChange={(e) => setForm({ ...form, activo: e.target.checked })}
                                                    className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Activo</span>
                                            </label>

                                            <button
                                                onClick={handleAplicar}
                                                disabled={selectedCount === 0 || applying}
                                                className="ml-auto inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:bg-red-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                            >
                                                {applying ? 'Aplicando...' : `Aplicar Descuento (${selectedCount})`}
                                            </button>
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between">
                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                            {selectedCount} de {predios.length} predio(s) seleccionados
                                        </span>
                                    </div>
                                </>
                            )}

                            {!searching && searched && predios.length === 0 && contribuyentes.length === 0 && (
                                <div className="text-center py-8 text-gray-500 dark:text-gray-400">
                                    No se encontraron resultados para la cuenta ingresada.
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
