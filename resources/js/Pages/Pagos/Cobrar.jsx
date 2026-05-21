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
    const [formaPago, setFormaPago] = useState('');
    const [pagoCon, setPagoCon] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
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
        }, 100);
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
                }
            })
            .catch(() => setError('Error al obtener cálculo'));
    };

    const pagar = () => {
        if (!selectedPredio || conceptos.length === 0) {
            alert('Selecciona un predio y espera el cálculo.');
            return;
        }
        if (!formaPago) {
            alert('Selecciona una forma de pago.');
            return;
        }

        setLoading(true);
        setError(null);

        const monto = conceptos.reduce((sum, c) => sum + parseFloat(c.monto || 0), 0);
        const payload = {
            id_predio: selectedPredio.id,
            id_contribuyente: contribuyenteData.id_contribuyente,
            monto,
            descuento: 0,
            nombre: contribuyenteData.nombre,
            rfc: contribuyenteData.rfc,
            descripcion: 'Pago predial urbano',
            forma_pago: formaPago,
            tipo_pago: 'predial_urbano',
            conceptos,
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

                            {conceptos.length > 0 && (
                                <div className="mt-6 border-t border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 pt-6">
                                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                        <div>
                                            <label className="block text-sm font-medium mb-1">Total</label>
                                            <p className="text-2xl font-bold text-green-600">${total.toFixed(2)}</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium mb-1">Forma de Pago</label>
                                            <select
                                                value={formaPago}
                                                onChange={(e) => setFormaPago(e.target.value)}
                                                className="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                                            >
                                                <option value="">Seleccionar...</option>
                                                {formasPago.map((fp) => (
                                                    <option key={fp.id} value={fp.id}>{fp.Descripción}</option>
                                                ))}
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium mb-1">Pago con</label>
                                            <input
                                                type="text"
                                                value={pagoCon}
                                                onChange={(e) => setPagoCon(e.target.value)}
                                                placeholder="Monto recibido"
                                                className="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                                            />
                                        </div>
                                        <div>
                                            <button
                                                onClick={pagar}
                                                disabled={loading || !formaPago}
                                                className="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                            >
                                                {loading ? 'Procesando...' : 'Pagar'}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}