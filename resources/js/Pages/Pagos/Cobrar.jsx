import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import axios from 'axios';

export default function Cobrar({ cajaAbierta, formasPago }) {
    const [search, setSearch] = useState('');
    const [results, setResults] = useState([]);
    const [showResults, setShowResults] = useState(false);
    const [selectedPredio, setSelectedPredio] = useState(null);
    const [conceptos, setConceptos] = useState([]);
    const [total, setTotal] = useState(0);
    const [contribuyenteData, setContribuyenteData] = useState({});
    const [formasPagosData, setFormasPagosData] = useState([{ forma_pago_id: '1', monto: '' }]);
    const [loading, setLoading] = useState(false);
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

    const selectPredio = (p) => {
        setSelectedPredio(p);
        setSearch(`${p.clave_catastral} - ${p.contribuyente}`);
        setShowResults(false);
        setConceptos([]);
        setTotal(0);
        setError(null);
        setFormasPagosData([]);

        axios.get(route('pagos.get-calculo'), { params: { id: p.id } })
            .then((r) => {
                const data = r.data;
                if (data.predio) {
                    const items = data.conceptos.filter((c) => c.concepto !== 'TOTAL');
                    setConceptos(items);
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

    const formasValidas = formasPagosData.every((row) => row.forma_pago_id && parseFloat(row.monto) > 0);
    const suficiente = sumaFormasPagos >= total - 0.01;
    const cambio = Math.max(0, sumaFormasPagos - total);

    const abrirConfirmacion = () => {
        if (!selectedPredio || conceptos.length === 0) {
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
            monto: total,
            descuento: 0,
            nombre: contribuyenteData.nombre,
            rfc: contribuyenteData.rfc,
            descripcion: 'Pago predial urbano',
            forma_pago: formasPagosData[0]?.forma_pago_id || '',
            tipo_pago: 'predial_urbano',
            conceptos,
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
                    window.location.reload();
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
                    Pago Predial Urbano
                </h2>
            }
        >
            <Head title="Pago Predial Urbano" />

            <div className="py-12 w-full">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {error && (
                        <div className="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded">{error}</div>
                    )}

                    <div className="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="flex justify-between items-center mb-6">
                                <h3 className="text-lg font-medium">Cobro - Caja {cajaAbierta?.caja?.nombre ?? ''}</h3>
                                <Link href={route('pagos.index')} className="text-sm text-gray-600 dark:text-gray-400 hover:underline">&larr; Volver</Link>
                            </div>

                            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <div className="lg:col-span-1 space-y-4" ref={searchRef}>
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

                                <div className="lg:col-span-1">
                                    <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <h4 className="text-sm font-semibold mb-3">Descuentos</h4>
                                        <div className="space-y-2">
                                            {['Jubilado', 'Pensionado', 'Adulto Mayor'].map((d) => (
                                                <label key={d} className="flex items-center gap-2 text-sm">
                                                    <input type="checkbox" className="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200" />
                                                    {d}
                                                </label>
                                            ))}
                                        </div>
                                    </div>
                                </div>

                                <div className="lg:col-span-1">
                                    {conceptos.length > 0 && (
                                        <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <h4 className="text-sm font-semibold mb-3">Cálculo del Predial</h4>
                                            <table className="w-full text-sm">
                                                <thead>
                                                    <tr className="border-b border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                                        <th className="text-left py-1 pr-2">Concepto</th>
                                                        <th className="text-right py-1 pl-2">Monto</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {conceptos.map((c, i) => (
                                                        <tr key={i} className="border-b border-gray-200 dark:border-gray-700">
                                                            <td className="py-1 pr-2">{c.concepto}</td>
                                                            <td className="text-right py-1 pl-2">${parseFloat(c.monto).toFixed(2)}</td>
                                                        </tr>
                                                    ))}
                                                    <tr className="border-b border-gray-200 dark:border-gray-700">
                                                        <td className="py-1 pr-2 font-bold">Total</td>
                                                        <td className="text-right py-1 pl-2 font-bold">${total.toFixed(2)}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {conceptos.length > 0 && total > 0 && (
                                <div className="mt-6 border-t border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 pt-6">
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
                                                        className="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm"
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
                                                        className="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm"
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
                                            <span className="text-gray-600 dark:text-gray-400">
                                                Suma formas de pago:{' '}
                                            </span>
                                            <span className={suficiente ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'}>
                                                ${sumaFormasPagos.toFixed(2)}
                                            </span>
                                            {!suficiente && (
                                                <span className="ml-2 text-red-500 text-xs">
                                                    (debe ser ≥ ${total.toFixed(2)})
                                                </span>
                                            )}
                                            {suficiente && cambio > 0 && (
                                                <span className="ml-2 text-green-600 text-xs">
                                                    cambio: ${cambio.toFixed(2)}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                                        <div>
                                            <label className="block text-sm font-medium mb-1">Total a pagar</label>
                                            <p className="text-2xl font-bold text-green-600">${total.toFixed(2)}</p>
                                        </div>
                                        <div className="text-right">
                                            <button
                                                onClick={abrirConfirmacion}
                                                disabled={loading || !formasValidas || !suficiente}
                                                className="inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
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
                                <span className="font-semibold">${total.toFixed(2)}</span>
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