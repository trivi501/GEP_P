import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index() {
    const [cuenta, setCuenta] = useState('');
    const [contribuyentes, setContribuyentes] = useState([]);
    const [predios, setPredios] = useState([]);
    const [selected, setSelected] = useState({});
    const [searching, setSearching] = useState(false);
    const [generating, setGenerating] = useState(false);

    const handleSearch = async (e) => {
        e.preventDefault();
        if (!cuenta.trim()) return;
        setSearching(true);
        setSelected({});
        try {
            const res = await fetch(route('estado-cuenta-masivo.search'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({ cuenta }),
            });
            const data = await res.json();
            setContribuyentes(data.contribuyentes ?? []);
            const nuevosPredios = data.predios ?? [];
            setPredios(nuevosPredios);
            const preseleccion = {};
            nuevosPredios.forEach((p) => { if (p.tiene_descuento) preseleccion[p.id] = true; });
            setSelected(preseleccion);
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

    const handleGeneratePdf = () => {
        const ids = Object.entries(selected).filter(([, v]) => v).map(([k]) => k);
        if (ids.length === 0) return;
        setGenerating(true);
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = route('estado-cuenta-masivo.pdf');
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = document.querySelector('meta[name=csrf-token]')?.content;
        form.appendChild(tokenInput);
        ids.forEach((id) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'predios[]';
            input.value = id;
            form.appendChild(input);
        });
        form.target = '_blank';
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
        setTimeout(() => setGenerating(false), 1000);
    };

    const selectedCount = Object.values(selected).filter(Boolean).length;

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Estado de Cuenta Masivo
                </h2>
            }
        >
            <Head title="Estado de Cuenta Masivo" />

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
                                        <button
                                            onClick={handleGeneratePdf}
                                            disabled={selectedCount === 0 || generating}
                                            className="inline-flex items-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-red-500 focus:bg-red-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50"
                                        >
                                            {generating
                                                ? 'Generando...'
                                                : `Generar PDF (${selectedCount})`}
                                        </button>
                                    </div>

                                    <div className="overflow-x-auto">
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
                                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Ubicación</th>
                                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                                                    <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Año Último Pago</th>
                                                    <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Superficie</th>
                                                    <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Descuento</th>
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
                                                            {predio.ubicacionPredio}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                            {predio.tipo_predio}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                                            {predio.año_ultimo_pago}
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                                            {parseFloat(predio.superficie).toLocaleString('es-MX', { minimumFractionDigits: 2 })} m²
                                                        </td>
                                                        <td className="whitespace-nowrap px-6 py-4 text-center text-sm">
                                                            {predio.tiene_descuento ? (
                                                                <span className="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                                                                    Sí
                                                                </span>
                                                            ) : (
                                                                <span className="text-gray-400 dark:text-gray-500">—</span>
                                                            )}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>

                                    <div className="mt-4 flex items-center justify-between">
                                        <span className="text-sm text-gray-500 dark:text-gray-400">
                                            {selectedCount} de {predios.length} predio(s) seleccionados
                                        </span>
                                    </div>
                                </>
                            )}

                            {!searching && cuenta && predios.length === 0 && contribuyentes.length === 0 && (
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
