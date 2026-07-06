import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import axios from 'axios';

export default function Cobrar({ cajaAbierta, formasPago, predioId }) {
    const [search, setSearch] = useState('');
    const [results, setResults] = useState([]);
    const [showResults, setShowResults] = useState(false);
    const [selectedPredio, setSelectedPredio] = useState(null);
    const [conceptos, setConceptos] = useState([]);
    const [anios, setAnios] = useState([]);
    const [selectedAnios, setSelectedAnios] = useState([]);
    const [total, setTotal] = useState(0);
    const [contribuyenteData, setContribuyenteData] = useState({});
    const [formasPagosData, setFormasPagosData] = useState([{ forma_pago_id: '1', monto: '' }]);
    const [loading, setLoading] = useState(false);
    const [esRustico, setEsRustico] = useState(false);
    const [selectedDescuento, setSelectedDescuento] = useState(null);
    const [error, setError] = useState(null);
    const [showPagadoModal, setShowPagadoModal] = useState(false);
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const searchRef = useRef(null);
    const timeoutRef = useRef(null);

    useEffect(() => {
        if (search.length < 2) {
            setResults([]);
            setShowResults(false);
            return;
        }

        clearTimeout(timeoutRef.current);
        timeoutRef.current = setTimeout(() => {
            axios.get(route('pagos.search-predio'), { params: { q: search } })
                .then((r) => {
                    setResults(r.data);
                    setShowResults(true);
                })
                .catch(() => setResults([]));
        }, 300);
    }, [search]);

    useEffect(() => {
        const handleClick = (e) => {
            if (searchRef.current && !searchRef.current.contains(e.target)) {
                setShowResults(false);
            }
        };
        document.addEventListener('click', handleClick);
        return () => document.removeEventListener('click', handleClick);
    }, []);

    useEffect(() => {
        if (!predioId) return;
        axios.get(route('pagos.get-calculo'), { params: { id: predioId } })
            .then((r) => {
                const data = r.data;
                if (data.predio) {
                    const rustico = data.predio.es_rustico ?? false;
                    setEsRustico(rustico);
                    setSelectedPredio({ id: predioId, clave_catastral: data.predio.clave_catastral ?? '', contribuyente: data.predio.contribuyente ?? '', cuenta: data.predio.cuenta, domicilio: data.predio.domicilio, ultimo_pago: data.predio.ultimo_pago });
                    setSearch(`${data.predio.clave_catastral ?? ''} - ${data.predio.contribuyente ?? ''}`);
                    const items = data.conceptos.filter((c) => c.concepto !== 'TOTAL');
                    setConceptos(items);
                    const yrs = data.anios || [];
                    setAnios(yrs);
                    setSelectedAnios(yrs.map((a) => a.anho));
                    setTotal(data.total);
                    setContribuyenteData({
                        id_contribuyente: data.predio.id_contribuyente,
                        rfc: data.predio.rfc || '—',
                        nombre: data.predio.contribuyente_nombre || data.predio.contribuyente || '—',
                    });
                    setFormasPagosData([{ forma_pago_id: '1', monto: '' }]);
                    if (data.total <= 0) {
                        setShowPagadoModal(true);
                    }
                }
            })
            .catch(() => setError('Error al cargar datos del predio.'));
    }, [predioId]);

    const selectPredio = (p) => {
        setSelectedPredio(p);
        setSearch(`${p.clave_catastral} - ${p.contribuyente}`);
        setShowResults(false);
        setConceptos([]);
        setAnios([]);
        setSelectedAnios([]);
        setTotal(0);
        setError(null);
        setFormasPagosData([]);
        setSelectedDescuento(null);

        axios.get(route('pagos.get-calculo'), { params: { id: p.id } })
            .then((r) => {
                const data = r.data;
                if (data.predio) {
                    setEsRustico(data.predio.es_rustico ?? false);
                    const items = data.conceptos.filter((c) => c.concepto !== 'TOTAL');
                    setConceptos(items);
                    const yrs = data.anios || [];
                    setAnios(yrs);
                    setSelectedAnios(yrs.map((a) => a.anho));
                    setTotal(data.total);
                    setContribuyenteData({
                        id_contribuyente: data.predio.id_contribuyente,
                        rfc: data.predio.rfc || '—',
                        nombre: data.predio.contribuyente_nombre || data.predio.contribuyente || '—',
                    });
                    setFormasPagosData([{ forma_pago_id: '1', monto: '' }]);

                    if (data.total <= 0) {
                        setShowPagadoModal(true);
                    }
                }
            })
            .catch(() => setError('Error al obtener cálculo'));
    };

    const toggleAnio = (anho) => {
        setSelectedAnios((prev) => {
            const next = prev.includes(anho) ? prev.filter((a) => a !== anho) : [...prev, anho];
            return next.sort((a, b) => a - b);
        });
    };

    const conceptosSeleccionados = (() => {
        const sel = [];
        anios.forEach((a) => {
            if (selectedAnios.includes(a.anho)) a.conceptos.forEach((c) => sel.push(c));
        });
        return sel;
    })();

    const totalSeleccionado = anios.reduce((sum, a) => selectedAnios.includes(a.anho) ? sum + (parseFloat(a.total) || 0) : sum, 0);

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

    const sumaFormasPagos = formasPagosData.reduce(
        (sum, row) => sum + (parseFloat(row.monto) || 0), 0
    );

    const itemsVisibles = anios.length > 0 ? conceptosSeleccionados : conceptos;
    const totalVisible = anios.length > 0 ? totalSeleccionado : total;
    const descuentoMonto = selectedDescuento
        ? itemsVisibles.filter((c) => c.concepto.includes('Predial') && c.concepto.includes(String(new Date().getFullYear()))).reduce((s, c) => s + parseFloat(c.monto), 0) * 0.10
        : 0;
    const conceptosConDescuento = selectedDescuento && descuentoMonto > 0
        ? [...itemsVisibles, { concepto: 'Descuento', monto: -descuentoMonto }]
        : itemsVisibles;
    const totalConDescuento = totalVisible - descuentoMonto;

    const formasValidas = formasPagosData.every((row) => row.forma_pago_id && parseFloat(row.monto) > 0);
    const suficiente = sumaFormasPagos >= totalConDescuento - 0.01;
    const cambio = Math.max(0, sumaFormasPagos - totalConDescuento);

    const abrirConfirmacion = () => {
        if (!selectedPredio || (anios.length > 0 ? selectedAnios.length === 0 : conceptos.length === 0)) {
            alert('Selecciona un predio y espera el cálculo.');
            return;
        }
        if (!formasValidas) {
            alert('Completa todas las formas de pago con montos válidos.');
            return;
        }
        if (!suficiente) {
            alert(`La suma de las formas de pago ($${sumaFormasPagos.toFixed(2)}) es menor al total ($${total.toFixed(2)}).`);
            return;
        }

        setShowConfirmModal(true);
    };

    const confirmarPago = () => {
        setShowConfirmModal(false);
        setLoading(true);
        setError(null);

        const payload = {
            id_predio: selectedPredio.id,
            id_contribuyente: contribuyenteData.id_contribuyente,
            monto: totalConDescuento,
            descuento: descuentoMonto,
            nombre: contribuyenteData.nombre,
            rfc: contribuyenteData.rfc,
            descripcion: esRustico ? 'Pago predial rústico' : 'Pago predial urbano',
            forma_pago: formasPagosData[0]?.forma_pago_id || '',
            tipo_pago: esRustico ? 'predial_rustico' : 'predial_urbano',
            conceptos: conceptosConDescuento,
            anios_pagados: anios.length > 0 ? selectedAnios : undefined,
            formas_pagos: formasPagosData.map((row) => ({
                forma_pago_id: parseInt(row.forma_pago_id),
                monto: parseFloat(row.monto),
            })),
        };

        axios.post(route('pagos.guardar'), payload)
            .then((result) => {
                if (result.data.success) {
                    const reciboUrl = route('pagos.recibo', result.data.pago_id);
                    window.open(reciboUrl, '_blank');
                    if (predioId) {
                        window.location.href = '/predios';
                    } else {
                        window.location.reload();
                    }
                } else {
                    alert('Error: ' + (result.data.error || 'Error desconocido'));
                }
            })
            .catch((err) => {
                alert('Error al registrar el pago: ' + (err.response?.data?.error || 'Error de conexión'));
            })
            .finally(() => setLoading(false));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    {esRustico ? 'Pago Predial Rústico' : 'Pago Predial Urbano'}
                </h2>
            }
        >
            <Head title={esRustico ? 'Pago Predial Rústico' : 'Pago Predial Urbano'} />

            <div className="py-12 w-full">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {error && (
                        <div className="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">{error}</div>
                    )}

                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">Cobro {esRustico ? 'Rústico' : 'Urbano'} - Caja {cajaAbierta?.caja?.nombre ?? ''}</h3>
                                <a href="/pagos" className="text-sm text-gray-600 dark:text-gray-400 hover:underline">&larr; Volver</a>
                            </div>

                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                <div className="space-y-4" ref={searchRef}>
                                    <div className="relative">
                                        <label className="block text-sm font-medium mb-1">Buscar por Cuenta o Clave Catastral</label>
                                        <input
                                            type="text"
                                            value={search}
                                            onChange={(e) => setSearch(e.target.value)}
                                            placeholder="Escribe para buscar..."
                                            className="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                                        />
                                        {showResults && (
                                            <div className="mt-1 absolute z-50 left-0 right-0 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 rounded shadow-lg max-h-60 overflow-y-auto">
                                                {results.length === 0 ? (
                                                    <div className="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Sin resultados</div>
                                                ) : (
                                                    results.map((p) => (
                                                        <div
                                                            key={p.id}
                                                            onClick={() => selectPredio(p)}
                                                            className="px-3 py-2 cursor-pointer hover:bg-gray-100 text-sm border-b border-gray-200 dark:border-gray-700"
                                                        >
                                                            <strong>{p.clave_catastral}</strong> — {p.contribuyente}{' '}
                                                            <span className="text-gray-500 dark:text-gray-400">({p.cuenta})</span>
                                                        </div>
                                                    ))
                                                )}
                                            </div>
                                        )}
                                    </div>

                                    {selectedPredio && (
                                        <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg space-y-2 text-sm">
                                            <p><strong>Predial Cuenta:</strong> {selectedPredio.cuenta}</p>
                                            <p><strong>Clave Catastral:</strong> {selectedPredio.clave_catastral}</p>
                                            <p><strong>Contribuyente:</strong> {selectedPredio.contribuyente}</p>
                                            <p><strong>Domicilio:</strong> {selectedPredio.domicilio}</p>
                                            <p><strong>Última Fecha de Pago:</strong> {selectedPredio.ultimo_pago || '—'}</p>
                                        </div>
                                    )}
                                </div>

                                <div>
                                    <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h4 className="text-sm font-semibold mb-3">Descuentos</h4>
                                        <div className="space-y-2">
                                            {['Jubilado', 'Pensionado', 'Adulto Mayor'].map((d) => (
                                                <label key={d} className="flex items-center gap-2 text-sm cursor-pointer">
                                                    <input type="checkbox" checked={selectedDescuento === d}
                                                        onChange={() => setSelectedDescuento(selectedDescuento === d ? null : d)}
                                                        className="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
                                                    {d}
                                                </label>
                                            ))}
                                        </div>
                                        {selectedDescuento && (
                                            <p className="mt-2 text-xs text-green-600 font-medium">10% desc. año actual: -${descuentoMonto.toFixed(2)}</p>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {anios.length > 0 ? (
                                <div className="mt-6">
                                    <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h4 className="text-sm font-semibold mb-3">Años a pagar</h4>
                                        <div className="overflow-x-auto">
                                            <table className="w-full text-xs">
                                                <thead>
                                                    <tr className="border-b border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600">
                                                        <th className="text-left py-1 pr-2">Año</th>
                                                        <th className="text-right py-1 px-1">Subtotal</th>
                                                        <th className="text-right py-1 px-1">Recargos</th>
                                                        <th className="text-right py-1 px-1">Actualiz.</th>
                                                        <th className="text-right py-1 px-1">Multa</th>
                                                        <th className="text-right py-1 px-1">Ejecución</th>
                                                        <th className="text-right py-1 px-1">Total</th>
                                                        <th className="text-center py-1 pl-2">Pagar</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {anios.map((a) => {
                                                        const c = {};
                                                        a.conceptos.forEach((x) => {
                                                            if (x.concepto.includes('Predial') || x.concepto.includes('Rústico') || x.concepto.includes('Aseo')) {
                                                                if (x.concepto.includes('Aseo')) c.aseo = (c.aseo || 0) + parseFloat(x.monto);
                                                                else c.subtotal = (c.subtotal || 0) + parseFloat(x.monto);
                                                            } else if (x.concepto.includes('Recargos')) c.recargos = parseFloat(x.monto);
                                                            else if (x.concepto.includes('Actualización')) c.actualizacion = parseFloat(x.monto);
                                                            else if (x.concepto.includes('Multa')) c.multa = parseFloat(x.monto);
                                                            else if (x.concepto.includes('Ejecución') || x.concepto.includes('Gastos')) c.ejecucion = parseFloat(x.monto);
                                                        });
                                                        return (
                                                            <tr key={a.anho} className="border-b border-gray-200 dark:border-gray-700">
                                                                <td className="py-1 pr-2 font-medium">{a.anho}</td>
                                                                <td className="py-1 px-1 text-right">${(c.subtotal || 0).toFixed(2)}</td>
                                                                <td className="py-1 px-1 text-right">${(c.recargos || 0).toFixed(2)}</td>
                                                                <td className="py-1 px-1 text-right">${(c.actualizacion || 0).toFixed(2)}</td>
                                                                <td className="py-1 px-1 text-right">${(c.multa || 0).toFixed(2)}</td>
                                                                <td className="py-1 px-1 text-right">${(c.ejecucion || 0).toFixed(2)}</td>
                                                                <td className="py-1 px-1 text-right font-bold">${parseFloat(a.total || 0).toFixed(2)}</td>
                                                                <td className="py-1 pl-2 text-center">
                                                                    <input type="checkbox" checked={selectedAnios.includes(a.anho)} onChange={() => toggleAnio(a.anho)} className="rounded border-gray-300 dark:border-gray-600" />
                                                                </td>
                                                            </tr>
                                                        );
                                                    })}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            ) : conceptos.length > 0 && (
                                <div className="mt-6">
                                    <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h4 className="text-sm font-semibold mb-3">Cálculo del Predial</h4>
                                        <table className="w-full text-sm">
                                            <thead>
                                                <tr className="border-b border-gray-300 dark:border-gray-600">
                                                    <th className="text-left py-1 pr-2">Concepto</th>
                                                    <th className="text-right py-1 pl-2">Monto</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {conceptosConDescuento.map((c, i) => (
                                                    <tr key={i} className="border-b border-gray-200 dark:border-gray-700">
                                                        <td className="py-1 pr-2">{c.concepto}</td>
                                                        <td className={`text-right py-1 pl-2 ${c.monto < 0 ? 'text-red-500' : ''}`}>${parseFloat(c.monto).toFixed(2)}</td>
                                                    </tr>
                                                ))}
                                                <tr>
                                                    <td className="py-1 pr-2 font-bold">Total</td>
                                                    <td className="text-right py-1 pl-2 font-bold">${totalConDescuento.toFixed(2)}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {((anios.length > 0 && totalSeleccionado > 0) || (anios.length === 0 && conceptos.length > 0)) && (
                                <div className="mt-6">
                                    <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h4 className="text-sm font-semibold mb-3">Pago</h4>
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div className="space-y-3">
                                                <div>
                                                    <label className="block text-sm font-medium mb-1">Total a pagar</label>
                                                    <p className="text-2xl font-bold text-green-600 dark:text-green-400">${totalConDescuento.toFixed(2)}</p>
                                                </div>
                                            </div>
                                            <div>
                                                <div className="flex items-center justify-between mb-2">
                                                    <label className="block text-sm font-medium">Formas de Pago</label>
                                                    <button type="button" onClick={addFormaPagoRow} className="text-sm text-indigo-600 hover:text-indigo-900">+ Agregar</button>
                                                </div>
                                                {formasPagosData.map((row, i) => (
                                                    <div key={i} className="flex gap-2 items-end mb-2">
                                                        <div className="flex-1">
                                                            <select value={row.forma_pago_id} onChange={(e) => updateFormaPagoRow(i, 'forma_pago_id', e.target.value)} className="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm">
                                                                <option value="">Seleccionar...</option>
                                                                {formasPago.map((fp) => (<option key={fp.id} value={fp.id}>{fp.Descripción}</option>))}
                                                            </select>
                                                        </div>
                                                        <div className="w-32">
                                                            <input type="number" step="0.01" min="0" value={row.monto} onChange={(e) => updateFormaPagoRow(i, 'monto', e.target.value)} placeholder="Monto" className="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm" />
                                                        </div>
                                                        {formasPagosData.length > 1 && (
                                                            <button type="button" onClick={() => removeFormaPagoRow(i)} className="text-red-500 text-sm pb-1">X</button>
                                                        )}
                                                    </div>
                                                ))}
                                                <div className="mt-1 text-sm">
                                                    <span className="text-gray-600">Recibido: </span>
                                                    <span className={suficiente ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'}>${sumaFormasPagos.toFixed(2)}</span>
                                                    {suficiente && cambio > 0 && <span className="ml-2 text-green-600 text-xs">cambio: ${cambio.toFixed(2)}</span>}
                                                </div>
                                            </div>
                                        </div>
                                        <div className="mt-4">
                                            <button onClick={abrirConfirmacion} disabled={loading || !formasValidas || !suficiente} className="w-full inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                                {loading ? 'Procesando...' : 'Revisar y Pagar'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {showPagadoModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                        <h3 className="text-lg font-semibold mb-2">Predial al día</h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            Este predio no tiene adeudos. El cálculo total es $0.00, lo que indica que ya está pagado.
                        </p>
                        <div className="flex justify-end">
                            <button
                                onClick={() => setShowPagadoModal(false)}
                                className="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-500"
                            >
                                Aceptar
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {showConfirmModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                    <div className="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md shadow-xl">
                        <h3 className="text-lg font-semibold mb-4">Confirmar pago</h3>

                        <div className="space-y-3 text-sm">
                            <div className="flex justify-between">
                                <span className="text-gray-500">Total a pagar:</span>
                                <span className="font-semibold">${totalConDescuento.toFixed(2)}</span>
                            </div>
                            {descuentoMonto > 0 && (
                                <div className="flex justify-between text-green-600">
                                    <span>Descuento ({selectedDescuento}):</span>
                                    <span className="font-bold">-${descuentoMonto.toFixed(2)}</span>
                                </div>
                            )}
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
                            <div className="text-xs text-gray-500">
                                <p>Contribuyente: {contribuyenteData.nombre}</p>
                                <p>Clave catastral: {selectedPredio?.clave_catastral}</p>
                            </div>
                        </div>

                        <div className="flex justify-end gap-2 mt-6">
                            <button
                                onClick={() => setShowConfirmModal(false)}
                                className="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Cancelar
                            </button>
                            <button
                                onClick={confirmarPago}
                                disabled={loading}
                                className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-500 disabled:opacity-50"
                            >
                                {loading ? 'Procesando...' : 'Confirmar pago'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}