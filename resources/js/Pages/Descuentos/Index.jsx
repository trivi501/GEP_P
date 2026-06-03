import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import Pagination from '@/Components/Pagination';

export default function Index({ descuentos, filters, id_predio, existingDescuento }) {
    const permissions = Array.isArray(usePage().props.userPermissions) ? usePage().props.userPermissions : [];
    const can = (permiso) => permissions.includes(permiso);
    const [showModal, setShowModal] = useState(false);
    const [editing, setEditing] = useState(null);
    const [search, setSearch] = useState(filters?.search ?? '');
    const [predioSearch, setPredioSearch] = useState('');
    const [selectedPredio, setSelectedPredio] = useState(null);
    const [predioResults, setPredioResults] = useState([]);
    const [showPredioResults, setShowPredioResults] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deleteTarget, setDeleteTarget] = useState(null);

    const formatDate = (dateStr) => dateStr ? dateStr.slice(0, 10) : '';

    useEffect(() => {
        if (id_predio && existingDescuento) {
            openEdit(existingDescuento);
        } else if (id_predio) {
            axios.get(route('descuentos.search-predio'), { params: { q: id_predio } })
                .then((r) => {
                    const found = r.data.find(p => p.id === id_predio);
                    if (found) {
                        setSelectedPredio(found);
                        setPredioSearch(found.text);
                        setForm({
                            idPredio: found.id,
                            multas: '0',
                            actualizaciones: '0',
                            gastos_cobranza: '0',
                            recargos: '0',
                            fecha_expiracion: lastDayOfMonth(),
                            activo: true,
                        });
                        setShowModal(true);
                    }
                })
                .catch(() => {});
        }
    }, [id_predio, existingDescuento]);
    const predioSearchRef = useRef(null);
    const timeoutRef = useRef(null);
    const [form, setForm] = useState({
        idPredio: '',
        multas: '0',
        actualizaciones: '0',
        gastos_cobranza: '0',
        recargos: '0',
        fecha_expiracion: '',
        activo: true,
    });

    useEffect(() => {
        const handleClick = (e) => {
            if (predioSearchRef.current && !predioSearchRef.current.contains(e.target)) {
                setShowPredioResults(false);
            }
        };
        document.addEventListener('click', handleClick);
        return () => document.removeEventListener('click', handleClick);
    }, []);

    useEffect(() => {
        if (predioSearch.length < 2) {
            setPredioResults([]);
            setShowPredioResults(false);
            return;
        }
        clearTimeout(timeoutRef.current);
        timeoutRef.current = setTimeout(() => {
            axios.get(route('descuentos.search-predio'), { params: { q: predioSearch } })
                .then((r) => {
                    setPredioResults(r.data);
                    setShowPredioResults(true);
                })
                .catch(() => setPredioResults([]));
        }, 300);
    }, [predioSearch]);

    const openCreate = () => {
        setEditing(null);
        setForm({ idPredio: '', multas: '0', actualizaciones: '0', gastos_cobranza: '0', recargos: '0', fecha_expiracion: lastDayOfMonth(), activo: true });
        setSelectedPredio(null);
        setPredioSearch('');
        setShowModal(true);
    };

    const openEdit = (d) => {
        setEditing(d);
        setForm({
            idPredio: d.idPredio,
            multas: String(d.multas),
            actualizaciones: String(d.actualizaciones),
            gastos_cobranza: String(d.gastos_cobranza),
            recargos: String(d.recargos),
            fecha_expiracion: formatDate(d.fecha_expiracion) ?? '',
            activo: d.activo ?? true,
        });
        setSelectedPredio({ id: d.idPredio, text: d.predio?.Clave_predial + ' - ' + d.predio?.contribuyente?.cuenta });
        setPredioSearch(d.predio?.Clave_predial + ' - ' + d.predio?.contribuyente?.cuenta);
        setShowModal(true);
    };

    const csrfToken = () => document.querySelector('meta[name=csrf-token]')?.content;
    const headers = { 'X-CSRF-TOKEN': csrfToken(), 'Content-Type': 'application/json' };

    const lastDayOfMonth = () => {
        const now = new Date();
        const last = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        return last.toISOString().slice(0, 10);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (!selectedPredio) return alert('Selecciona un predio');

        const data = { ...form, idPredio: selectedPredio.id };

        if (editing) {
            if (!editing?.id) {
                alert('Error: ID del descuento no disponible. Recarga la página e intenta de nuevo.');
                return;
            }
            router.put(route('descuentos.update', editing.id), data, {
                onSuccess: () => setShowModal(false),
                onError: (err) => alert(Object.values(err).join('\n') || 'Error al actualizar'),
            });
        } else {
            router.post(route('descuentos.store'), data, {
                onSuccess: () => setShowModal(false),
                onError: (err) => alert(Object.values(err).join('\n') || 'Error al crear'),
            });
        }
    };

    const handleDelete = (d) => {
        setDeleteTarget(d);
        setShowDeleteModal(true);
    };

    const confirmDelete = () => {
        if (!deleteTarget?.id) return;
        router.delete(route('descuentos.destroy', deleteTarget.id), {
            onSuccess: () => { setShowDeleteModal(false); setDeleteTarget(null); },
            onError: () => alert('Error al eliminar'),
        });
    };

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">Descuentos</h2>}>
            <Head title="Descuentos" />
            <div className="py-12">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between gap-4 flex-wrap">
                                <h3 className="text-lg font-medium">Listado de Descuentos</h3>
                                <div className="flex gap-2">
                                    <input
                                        type="text"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter') {
                                                router.get(route('descuentos.index'), { search: e.target.value }, { preserveState: true, replace: true });
                                            }
                                        }}
                                        placeholder="Buscar por cuenta, nombre o clave... (Enter)"
                                        className="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    />
                                    {can('crear-descuentos') && (
                                        <button onClick={openCreate} className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500">+ Nuevo Descuento</button>
                                    )}
                                </div>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Predio</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Contribuyente</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Multas %</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actualizaciones %</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Recargos %</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Gtos Cobranza %</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Expira</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Estado</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Creado por</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {descuentos.data?.length > 0 ? descuentos.data.map((d) => (
                                            <tr key={d.id} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{d.predio?.Clave_predial ?? d.idPredio}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{d.predio?.contribuyente?.nombre_completo ?? d.predio?.contribuyente?.nombre_moral ?? '—'}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{d.multas}%</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{d.actualizaciones}%</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{d.recargos}%</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{d.gastos_cobranza}%</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{formatDate(d.fecha_expiracion) || '—'}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-center">
                                                    <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${d.activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                                        {d.activo ? 'Activo' : 'Inactivo'}
                                                    </span>
                                                </td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{d.user?.name ?? '—'}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-right text-sm">
                                                    {can('editar descuentos') && (
                                                        <button onClick={() => openEdit(d)} className="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                                    )}
                                                    {can('eliminar descuentos') && (
                                                        <button onClick={() => handleDelete(d)} className="text-red-600 hover:text-red-900">Eliminar</button>
                                                    )}
                                                </td>
                                            </tr>
                                        )) : (
                                            <tr><td colSpan="10" className="px-6 py-4 text-center text-sm text-gray-500">Sin descuentos registrados</td></tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            <Pagination links={descuentos} />
                        </div>
                    </div>
                </div>
            </div>

            {showDeleteModal && deleteTarget && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
                        <h3 className="text-lg font-medium mb-2">Confirmar Eliminación</h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            ¿Eliminar el descuento de <strong>{deleteTarget.predio?.Clave_predial ?? deleteTarget.idPredio}</strong>?
                            <br />Esta acción no se puede deshacer.
                        </p>
                        <div className="flex justify-end gap-2">
                            <button onClick={() => { setShowDeleteModal(false); setDeleteTarget(null); }}
                                className="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</button>
                            <button onClick={confirmDelete}
                                className="px-4 py-2 text-sm rounded-md bg-red-600 text-white hover:bg-red-500">Eliminar</button>
                        </div>
                    </div>
                </div>
            )}

            {showModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl p-6">
                        <h3 className="text-lg font-medium mb-4">{editing ? 'Editar Descuento' : 'Nuevo Descuento'}</h3>
                        <form onSubmit={handleSubmit}>
                            <div className="mb-4 relative" ref={predioSearchRef}>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Predio *</label>
                                <input
                                    type="text"
                                    value={predioSearch}
                                    onChange={(e) => {
                                        setPredioSearch(e.target.value);
                                        if (!e.target.value) setSelectedPredio(null);
                                    }}
                                    placeholder="Buscar por clave catastral, cuenta o nombre..."
                                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    required={!editing}
                                    disabled={!!editing}
                                />
                                {showPredioResults && predioResults.length > 0 && (
                                    <ul className="absolute z-10 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg mt-1 max-h-48 overflow-y-auto">
                                        {predioResults.map((p) => (
                                            <li
                                                key={p.id}
                                                className="px-3 py-2 hover:bg-indigo-50 dark:hover:bg-gray-600 cursor-pointer text-sm"
                                                onClick={() => {
                                                    setSelectedPredio(p);
                                                    setPredioSearch(p.text);
                                                    setShowPredioResults(false);
                                                }}
                                            >{p.text}</li>
                                        ))}
                                    </ul>
                                )}
                            </div>

                            <div className="grid grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Multas %</label>
                                    <input type="number" step="0.01" min="0" max="100" value={form.multas}
                                        onChange={(e) => setForm({ ...form, multas: e.target.value })}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Actualizaciones %</label>
                                    <input type="number" step="0.01" min="0" max="100" value={form.actualizaciones}
                                        onChange={(e) => setForm({ ...form, actualizaciones: e.target.value })}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recargos %</label>
                                    <input type="number" step="0.01" min="0" max="100" value={form.recargos}
                                        onChange={(e) => setForm({ ...form, recargos: e.target.value })}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gastos Cobranza %</label>
                                    <input type="number" step="0.01" min="0" max="100" value={form.gastos_cobranza}
                                        onChange={(e) => setForm({ ...form, gastos_cobranza: e.target.value })}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                </div>
                            </div>

                            <div className="mb-4">
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha de Expiración</label>
                                <input type="date" value={form.fecha_expiracion}
                                    onChange={(e) => setForm({ ...form, fecha_expiracion: e.target.value })}
                                    className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" />
                            </div>

                            <div className="mb-4">
                                <label className="flex items-center gap-2">
                                    <input type="checkbox" checked={form.activo}
                                        onChange={(e) => setForm({ ...form, activo: e.target.checked })}
                                        className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" />
                                    <span className="text-sm font-medium text-gray-700 dark:text-gray-300">Activo</span>
                                </label>
                            </div>

                            <div className="flex justify-end gap-2">
                                <button type="button" onClick={() => setShowModal(false)}
                                    className="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</button>
                                <button type="submit"
                                    className="px-4 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                                    {editing ? 'Actualizar' : 'Guardar'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
