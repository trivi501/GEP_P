import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ formasPago }) {
    const [cuenta, setCuenta] = useState('');
    const [contribuyentes, setContribuyentes] = useState([]);
    const [predios, setPredios] = useState([]);
    const [selected, setSelected] = useState({});
    const [calculos, setCalculos] = useState({});
    const [loadingCalc, setLoadingCalc] = useState({});
    const [searching, setSearching] = useState(false);
    const [paying, setPaying] = useState(false);
    const [formasPagosData, setFormasPagosData] = useState([{ forma_pago_id: formasPago[0]?.id ?? 1, monto: '0' }]);
    const [result, setResult] = useState(null);
    const [showConfirm, setShowConfirm] = useState(false);

    const handleSearch = async (e) => {
        e.preventDefault();
        if (!cuenta.trim()) return;
        setSearching(true);
        setSelected({});
        setCalculos({});
        setResult(null);
        try {
            const res = await fetch(route('multi-pagos.search'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
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

    const toggleSelect = async (id) => {
        const newSelected = { ...selected, [id]: !selected[id] };
        setSelected(newSelected);
        if (newSelected[id] && !calculos[id]) {
            setLoadingCalc((prev) => ({ ...prev, [id]: true }));
            try {
                const res = await fetch(route('multi-pagos.get-calculo'), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                    body: JSON.stringify({ id }),
                });
                const data = await res.json();
                if (data?.conceptos) {
                    setCalculos((prev) => ({ ...prev, [id]: data }));
                }
            } catch (err) {
                console.error(err);
            } finally {
                setLoadingCalc((prev) => ({ ...prev, [id]: false }));
            }
        }
    };

    const selectAll = async () => {
        const allSelected = predios.every((p) => selected[p.id]);
        if (allSelected) {
            setSelected({});
            return;
        }
        const newSelected = {};
        predios.forEach((p) => { newSelected[p.id] = true; });
        setSelected(newSelected);
        for (const p of predios) {
            if (!calculos[p.id]) {
                setLoadingCalc((prev) => ({ ...prev, [p.id]: true }));
                try {
                    const res = await fetch(route('multi-pagos.get-calculo'), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                        body: JSON.stringify({ id: p.id }),
                    });
                    const data = await res.json();
                    if (data?.conceptos) {
                        setCalculos((prev) => ({ ...prev, [p.id]: data }));
                    }
                } catch (err) {
                    console.error(err);
                } finally {
                    setLoadingCalc((prev) => ({ ...prev, [p.id]: false }));
                }
            }
        }
    };

    const allSelected = predios.length > 0 && predios.every((p) => selected[p.id]);

    const addFormaPagoRow = () => {
        setFormasPagosData((prev) => [...prev, { forma_pago_id: '', monto: '' }]);
    };

    const removeFormaPagoRow = (index) => {
        setFormasPagosData((prev) => prev.filter((_, i) => i !== index));
    };

    const updateFormaPagoRow = (index, field, value) => {
        setFormasPagosData((prev) =>
            prev.map((row, i) => (i === index ? { ...row, [field]: value } : row))
        );
    };

    const selectedCount = Object.values(selected).filter(Boolean).length;
    const totalSelected = Object.entries(selected)
        .filter(([, v]) => v)
        .reduce((sum, [id]) => sum + (calculos[id]?.total ?? 0), 0);

    const sumaFormasPagos = formasPagosData.reduce((sum, row) => sum + (parseFloat(row.monto) || 0), 0);
    const formasValidas = formasPagosData.every((row) => row.forma_pago_id && parseFloat(row.monto) > 0);
    const suficiente = sumaFormasPagos >= totalSelected - 0.01;
    const cambio = Math.max(0, sumaFormasPagos - totalSelected);

    const handlePay = async () => {
        if (!formasValidas) {
            alert('Completa todas las formas de pago con montos válidos.');
            return;
        }
        if (!suficiente) {
            alert(`La suma de las formas de pago ($${sumaFormasPagos.toFixed(2)}) es menor al total ($${totalSelected.toFixed(2)}).`);
            return;
        }

        setShowConfirm(true);
    };

    const confirmarPago = async () => {
        setShowConfirm(false);
        const ids = Object.entries(selected).filter(([, v]) => v).map(([k]) => k);
        if (ids.length === 0) return;

        const prediosData = ids.map((id) => {
            const p = predios.find((pr) => pr.id === id);
            const c = calculos[id];
            const esRustico = c?.es_rustico ?? false;
            return {
                id,
                id_contribuyente: p.id_contribuyente,
                monto: c?.total ?? 0,
                descuento: 0,
                conceptos: c?.conceptos ?? [],
                nombre: p.contribuyente,
                rfc: p.rfc ?? '',
                tipo_pago: esRustico ? 'predial_rustico' : 'predial_urbano',
                descripcion: esRustico ? 'Pago predial rústico' : 'Pago predial urbano',
            };
        });

        setPaying(true);
        try {
            const res = await fetch(route('multi-pagos.pagar'), {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content },
                body: JSON.stringify({
                    predios: prediosData,
                    formas_pagos: formasPagosData.map((row) => ({
                        forma_pago_id: parseInt(row.forma_pago_id),
                        monto: parseFloat(row.monto),
                    })),
                }),
            });
            const data = await res.json();
            if (data.success) {
                setResult(data);
                setSelected({});
                setCalculos({});
                data.folios_ids?.forEach((f) => {
                    window.open(route('pagos.recibo', f.id), '_blank');
                });
            } else {
                alert(data.error || 'Error al procesar pagos');
            }
        } catch (err) {
            alert('Error de conexión');
        } finally {
            setPaying(false);
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Multi Pagos Predial
                </h2>
            }
        >
            <Head title="Multi Pagos Predial" />

            <div className="py-12">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">

                            {result ? (
                                <div className="text-center py-8">
                                    <div className="text-green-600 text-lg font-semibold mb-4">{result.message}</div>
                                    <p className="text-sm text-gray-500 mb-4">Folios: {result.folios?.join(', ')}</p>
                                    <div className="flex justify-center gap-4">
                                        <a
                                            href={result.pdf_url}
                                            target="_blank"
                                            className="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-xs font-semibold text-white hover:bg-red-500"
                                        >
                                            Descargar PDF Consolidado
                                        </a>
                                        <button
                                            onClick={() => { setResult(null); setPredios([]); setContribuyentes([]); setCuenta(''); }}
                                            className="inline-flex items-center rounded-md bg-gray-600 px-4 py-2 text-xs font-semibold text-white hover:bg-gray-500"
                                        >
                                            Nueva Búsqueda
                                        </button>
                                    </div>
                                </div>
                            ) : (
                                <>
                                    <div className="mb-6">
                                        <h3 className="text-lg font-medium mb-4">Búsqueda por Cuenta</h3>
                                        <form onSubmit={handleSearch} className="flex gap-2">
                                            <input
                                                type="text"
                                                value={cuenta}
                                                onChange={(e) => setCuenta(e.target.value)}
                                                placeholder="Ingrese el número de cuenta..."
                                                className="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            />
                                            <button
                                                type="submit"
                                                disabled={searching}
                                                className="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700 disabled:opacity-50"
                                            >
                                                {searching ? 'Buscando...' : 'Buscar'}
                                            </button>
                                        </form>
                                    </div>

                                    {contribuyentes.length > 0 && (
                                        <div className="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <h4 className="text-sm font-semibold mb-2">Contribuyente(s):</h4>
                                            {contribuyentes.map((c) => (
                                                <p key={c.id_contribuyente} className="text-sm">
                                                    <span className="font-medium">{c.cuenta}</span> — {c.nombre_completo}
                                                </p>
                                            ))}
                                        </div>
                                    )}

                                    {predios.length > 0 && (
                                        <>
                                            <div className="mb-4 flex items-center justify-between flex-wrap gap-2">
                                                <div className="flex items-center gap-4">
                                                    <button onClick={selectAll} className="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-xs font-medium hover:bg-gray-50 dark:hover:bg-gray-600">
                                                        {allSelected ? 'Deseleccionar Todos' : 'Seleccionar Todos'}
                                                    </button>
                                                    <span className="text-sm text-gray-500">{predios.length} predio(s) pendientes</span>
                                                </div>
                                            </div>

                                            <div className="overflow-x-auto mb-4">
                                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th className="px-4 py-3 text-center w-10">
                                                                <input type="checkbox" checked={allSelected} onChange={selectAll} className="rounded border-gray-300 text-indigo-600" />
                                                            </th>
                                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Clave Catastral</th>
                                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Ubicación</th>
                                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Último Pago</th>
                                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                        {predios.map((predio) => (
                                                            <tr key={predio.id} className={`hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer ${selected[predio.id] ? 'bg-indigo-50 dark:bg-indigo-900/20' : ''}`} onClick={() => toggleSelect(predio.id)}>
                                                                <td className="px-4 py-4 text-center" onClick={(e) => e.stopPropagation()}>
                                                                    <input type="checkbox" checked={!!selected[predio.id]} onChange={() => toggleSelect(predio.id)} className="rounded border-gray-300 text-indigo-600" />
                                                                </td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm font-medium">{predio.Clave_predial}</td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{predio.ubicacionPredio}</td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{predio.tipo_predio}</td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-center text-gray-500">{predio.año_ultimo_pago}</td>
                                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-right font-medium">
                                                                    {loadingCalc[predio.id] ? (
                                                                        <span className="text-gray-400">Calculando...</span>
                                                                    ) : calculos[predio.id] ? (
                                                                        <span className="text-green-600">${calculos[predio.id].total.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</span>
                                                                    ) : selected[predio.id] ? (
                                                                        <span className="text-gray-400">—</span>
                                                                    ) : (
                                                                        <span className="text-gray-400">—</span>
                                                                    )}
                                                                </td>
                                                            </tr>
                                                        ))}
                                                    </tbody>
                                                </table>
                                            </div>

                                            {selectedCount > 0 && (
                                                <div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                                    <h4 className="text-sm font-semibold mb-3">Resumen de Selección</h4>
                                                    {Object.entries(selected).filter(([, v]) => v).map(([id]) => {
                                                        const p = predios.find((pr) => pr.id === id);
                                                        const c = calculos[id];
                                                        return (
                                                            <div key={id} className="mb-2 p-2 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600">
                                                                <div className="flex justify-between items-center">
                                                                    <div>
                                                                        <span className="font-medium text-sm">{p?.Clave_predial}</span>
                                                                        <span className="text-xs text-gray-500 ml-2">{p?.contribuyente}</span>
                                                                    </div>
                                                                    <span className="font-bold text-sm">${(c?.total ?? 0).toLocaleString('es-MX', { minimumFractionDigits: 2 })}</span>
                                                                </div>
                                                                {c?.conceptos && c.conceptos.length > 0 && (
                                                                    <div className="mt-1 text-xs text-gray-500">
                                                                        {c.conceptos.map((cx, i) => (
                                                                            <span key={i} className="mr-3">{cx.concepto}: ${cx.monto.toFixed(2)}</span>
                                                                        ))}
                                                                    </div>
                                                                )}
                                                            </div>
                                                        );
                                                    })}

                                                    <div className="mt-3 pt-3 border-t border-gray-300 dark:border-gray-600">
                                                        <div className="mt-4">
                                                            <div className="flex items-center justify-between mb-2">
                                                                <label className="block text-sm font-medium">Formas de Pago</label>
                                                                <button
                                                                    type="button"
                                                                    onClick={addFormaPagoRow}
                                                                    className="text-sm text-indigo-600 hover:text-indigo-900"
                                                                >
                                                                    + Agregar otra forma
                                                                </button>
                                                            </div>
                                                            {formasPagosData.map((row, i) => (
                                                                <div key={i} className="flex gap-3 items-end mb-2">
                                                                    <div className="flex-1">
                                                                        <select
                                                                            value={row.forma_pago_id}
                                                                            onChange={(e) => updateFormaPagoRow(i, 'forma_pago_id', e.target.value)}
                                                                            className="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm"
                                                                        >
                                                                            <option value="">Seleccionar...</option>
                                                                            {formasPago.map((fp) => (
                                                                                <option key={fp.id} value={fp.id}>{fp.Descripción}</option>
                                                                            ))}
                                                                        </select>
                                                                    </div>
                                                                    <div className="w-40">
                                                                        <input
                                                                            type="number"
                                                                            step="0.01"
                                                                            min="0"
                                                                            value={row.monto}
                                                                            onChange={(e) => updateFormaPagoRow(i, 'monto', e.target.value)}
                                                                            placeholder="Monto"
                                                                            className="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm"
                                                                        />
                                                                    </div>
                                                                    {formasPagosData.length > 1 && (
                                                                        <button
                                                                            type="button"
                                                                            onClick={() => removeFormaPagoRow(i)}
                                                                            className="text-red-500 hover:text-red-700 text-sm pb-1"
                                                                        >
                                                                            Quitar
                                                                        </button>
                                                                    )}
                                                                </div>
                                                            ))}
                                                            <div className="mt-1 text-sm">
                                                                <span className="text-gray-600 dark:text-gray-400">Suma formas de pago: </span>
                                                                <span className={suficiente ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'}>
                                                                    ${sumaFormasPagos.toFixed(2)}
                                                                </span>
                                                                {!suficiente && (
                                                                    <span className="ml-2 text-red-500 text-xs">
                                                                        (debe ser ≥ ${totalSelected.toFixed(2)})
                                                                    </span>
                                                                )}
                                                                {suficiente && cambio > 0 && (
                                                                    <span className="ml-2 text-green-600 text-xs">
                                                                        cambio: ${cambio.toFixed(2)}
                                                                    </span>
                                                                )}
                                                            </div>
                                                        </div>
                                                        <div className="flex justify-between items-center mt-3 pt-3 border-t border-gray-300 dark:border-gray-600">
                                                            <span className="text-lg font-bold">Total: ${totalSelected.toLocaleString('es-MX', { minimumFractionDigits: 2 })}</span>
                                                            <button
                                                                onClick={handlePay}
                                                                disabled={paying || !formasValidas || !suficiente}
                                                                className="inline-flex items-center rounded-md border border-transparent bg-green-600 px-6 py-2 text-sm font-semibold text-white hover:bg-green-500 disabled:opacity-50"
                                                            >
                                                                {paying ? 'Procesando...' : `Revisar y Pagar (${selectedCount})`}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}
                                        </>
                                    )}

                                    {!searching && cuenta && predios.length === 0 && contribuyentes.length === 0 && (
                                        <div className="text-center py-8 text-gray-500">No se encontraron resultados.</div>
                                    )}
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {showConfirm && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                        <h3 className="text-lg font-semibold mb-4">Confirmar pago múltiple</h3>

                        <div className="space-y-3 text-sm">
                            <div className="flex justify-between">
                                <span className="text-gray-500">Predios a pagar:</span>
                                <span className="font-semibold">{selectedCount}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">Total a pagar:</span>
                                <span className="font-semibold">${totalSelected.toFixed(2)}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">Recibido:</span>
                                <span className="font-semibold">${sumaFormasPagos.toFixed(2)}</span>
                            </div>
                            {cambio > 0 && (
                                <div className="flex justify-between text-green-600">
                                    <span>Cambio:</span>
                                    <span className="font-bold">${cambio.toFixed(2)}</span>
                                </div>
                            )}
                            <hr className="border-gray-300 dark:border-gray-600" />
                            <div className="text-xs text-gray-500 max-h-32 overflow-y-auto">
                                {Object.entries(selected).filter(([, v]) => v).map(([id]) => {
                                    const p = predios.find((pr) => pr.id === id);
                                    const c = calculos[id];
                                    return (
                                        <p key={id}>{p?.Clave_predial} — ${(c?.total ?? 0).toFixed(2)}</p>
                                    );
                                })}
                            </div>
                        </div>

                        <div className="flex justify-end gap-2 mt-6">
                            <button
                                onClick={() => setShowConfirm(false)}
                                className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={confirmarPago}
                                disabled={paying}
                                className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500 disabled:opacity-50"
                            >
                                {paying ? 'Procesando...' : 'Confirmar pago'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
