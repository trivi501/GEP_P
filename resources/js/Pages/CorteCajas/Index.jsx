import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import usePermissions from '@/Hooks/usePermissions';
import { Head, Link, router } from '@inertiajs/react';
import { useState, useEffect } from 'react';
import axios from 'axios';
import Pagination from '@/Components/Pagination';

export default function Index({ cortes, historialCajasSinCorte }) {
    const { can } = usePermissions();
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [editing, setEditing] = useState(null);
    const [deleteTarget, setDeleteTarget] = useState(null);
    const [selectedHistoriales, setSelectedHistoriales] = useState([]);
    const [form, setForm] = useState({
        fecha: new Date().toISOString().slice(0, 10),
        ingresos: '0',
        urbano: '0',
        rustico: '0',
        recibos_efectivos: '0',
        recibos_cancelados: '0',
        historial_ids: [],
    });

    useEffect(() => {
        if (selectedHistoriales.length === 0) {
            setForm((prev) => ({ ...prev, ingresos: '0', urbano: '0', rustico: '0', recibos_efectivos: '0', recibos_cancelados: '0' }));
            return;
        }
        const selected = historialCajasSinCorte.filter((h) => selectedHistoriales.includes(h.id));
        const ingresos = selected.reduce((s, h) => s + parseFloat(h.total_ingreso), 0);
        const urbano = selected.reduce((s, h) => s + parseFloat(h.urbano), 0);
        const rustico = selected.reduce((s, h) => s + parseFloat(h.rustico), 0);
        const efectivos = selected.reduce((s, h) => s + h.recibos_efectivos, 0);
        const cancelados = selected.reduce((s, h) => s + h.recibos_cancelados, 0);
        setForm((prev) => ({
            ...prev,
            ingresos: ingresos.toFixed(2),
            urbano: urbano.toFixed(2),
            rustico: rustico.toFixed(2),
            recibos_efectivos: String(efectivos),
            recibos_cancelados: String(cancelados),
        }));
    }, [selectedHistoriales, historialCajasSinCorte]);

    const csrfToken = () => document.querySelector('meta[name=csrf-token]')?.content;
    const headers = { 'X-CSRF-TOKEN': csrfToken(), 'Content-Type': 'application/json' };

    const openCreate = () => {
        setEditing(null);
        setSelectedHistoriales([]);
        setForm({
            fecha: new Date().toISOString().slice(0, 10),
            ingresos: '0',
            urbano: '0',
            rustico: '0',
            recibos_efectivos: '0',
            recibos_cancelados: '0',
            historial_ids: [],
        });
        setShowCreateModal(true);
    };

    const openEdit = (c) => {
        setEditing(c);
        setForm({
            fecha: c.fecha.slice(0, 10),
            ingresos: String(c.ingresos),
            urbano: String(c.urbano),
            rustico: String(c.rustico),
            recibos_efectivos: String(c.recibos_efectivos),
            recibos_cancelados: String(c.recibos_cancelados),
        });
        setShowEditModal(true);
    };

    const handleCreate = (e) => {
        e.preventDefault();
        if (selectedHistoriales.length === 0) return alert('Selecciona al menos un corte de caja para agrupar.');

        const data = {
            ...form,
            ingresos: parseFloat(form.ingresos),
            urbano: parseFloat(form.urbano),
            rustico: parseFloat(form.rustico),
            recibos_efectivos: parseInt(form.recibos_efectivos),
            recibos_cancelados: parseInt(form.recibos_cancelados),
            historial_ids: selectedHistoriales,
        };

        axios.post(route('corte-cajas.store'), data, { headers })
            .then((res) => {
                if (res.data.pdf_url) {
                    window.open(res.data.pdf_url, '_blank');
                }
                window.location.href = res.data.redirect || route('corte-cajas.index');
            })
            .catch((err) => alert(err.response?.data?.message || 'Error al crear'));
    };

    const handleUpdate = (e) => {
        e.preventDefault();
        if (!editing?.id) return;

        const data = {
            ...form,
            ingresos: parseFloat(form.ingresos),
            urbano: parseFloat(form.urbano),
            rustico: parseFloat(form.rustico),
            recibos_efectivos: parseInt(form.recibos_efectivos),
            recibos_cancelados: parseInt(form.recibos_cancelados),
        };

        axios.post(route('corte-cajas.update', editing.id), { ...data, _method: 'PUT' }, { headers })
            .then(() => window.location.reload())
            .catch((err) => alert(err.response?.data?.message || 'Error al actualizar'));
    };

    const handleDelete = (c) => {
        setDeleteTarget(c);
        setShowDeleteModal(true);
    };

    const confirmDelete = () => {
        if (!deleteTarget?.id) return;
        axios.post(route('corte-cajas.destroy', deleteTarget.id), { _method: 'DELETE' }, { headers })
            .then(() => window.location.reload())
            .catch(() => alert('Error al eliminar'));
    };

    const toggleHistorial = (id) => {
        setSelectedHistoriales((prev) =>
            prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id]
        );
    };

    return (
        <AuthenticatedLayout header={<h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">Cortes de Caja</h2>}>
            <Head title="Cortes de Caja" />
            <div className="py-12">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between gap-4 flex-wrap">
                                <h3 className="text-lg font-medium">Listado de Cortes de Caja</h3>
                                <button onClick={openCreate} className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold text-white hover:bg-indigo-500">+ Nuevo Corte</button>
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">ID</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Fecha</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Ingresos</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Urbano</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Rústico</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Recibos Efectivos</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Cancelados</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">Cajas</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {cortes.data?.length > 0 ? cortes.data.map((c) => (
                                            <tr key={c.id} className="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{c.id}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm">{c.fecha}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-right">${parseFloat(c.ingresos).toFixed(2)}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-right">${parseFloat(c.urbano).toFixed(2)}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-right">${parseFloat(c.rustico).toFixed(2)}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-center">{c.recibos_efectivos}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-center">{c.recibos_cancelados}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-sm text-center">{c.historial_cajas_count}</td>
                                                <td className="whitespace-nowrap px-6 py-4 text-right text-sm">
                                                    <button onClick={() => openEdit(c)} className="text-indigo-600 hover:text-indigo-900 mr-3">Editar</button>
                                                    <button onClick={() => handleDelete(c)} className="text-red-600 hover:text-red-900">Eliminar</button>
                                                </td>
                                            </tr>
                                        )) : (
                                            <tr><td colSpan="9" className="px-6 py-4 text-center text-sm text-gray-500">Sin cortes registrados</td></tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>
                            <Pagination meta={cortes} />
                        </div>
                    </div>
                </div>
            </div>

            {showCreateModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-4xl p-6 max-h-[95vh] overflow-y-auto">
                        <h3 className="text-lg font-medium mb-4">Nuevo Corte de Caja</h3>
                        <form onSubmit={handleCreate}>
                            {historialCajasSinCorte.length > 0 && (
                                <div className="mb-4">
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Selecciona cortes de caja cerrados para agrupar
                                    </label>
                                    <div className="max-h-48 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-md">
                                        <table className="w-full text-sm">
                                            <thead className="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                                <tr>
                                                    <th className="px-3 py-2 text-left w-8">
                                                        <input type="checkbox" onChange={(e) => {
                                                            if (e.target.checked) {
                                                                setSelectedHistoriales(historialCajasSinCorte.map((h) => h.id));
                                                            } else {
                                                                setSelectedHistoriales([]);
                                                            }
                                                        }} checked={selectedHistoriales.length === historialCajasSinCorte.length && historialCajasSinCorte.length > 0} />
                                                    </th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium uppercase">Caja</th>
                                                    <th className="px-3 py-2 text-left text-xs font-medium uppercase">Cajero</th>
                                                    <th className="px-3 py-2 text-right text-xs font-medium uppercase">Ingreso</th>
                                                    <th className="px-3 py-2 text-right text-xs font-medium uppercase">Urbano</th>
                                                    <th className="px-3 py-2 text-right text-xs font-medium uppercase">Rústico</th>
                                                    <th className="px-3 py-2 text-center text-xs font-medium uppercase">Recibos</th>
                                                    <th className="px-3 py-2 text-center text-xs font-medium uppercase">Cancelados</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {historialCajasSinCorte.map((h) => (
                                                    <tr key={h.id} className={`border-t border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 ${selectedHistoriales.includes(h.id) ? 'bg-indigo-50 dark:bg-indigo-900/20' : ''}`}
                                                        onClick={() => toggleHistorial(h.id)}>
                                                        <td className="px-3 py-2">
                                                            <input type="checkbox" checked={selectedHistoriales.includes(h.id)} onChange={() => toggleHistorial(h.id)} onClick={(e) => e.stopPropagation()} />
                                                        </td>
                                                        <td className="px-3 py-2">{h.caja}</td>
                                                        <td className="px-3 py-2">{h.cajero}</td>
                                                        <td className="px-3 py-2 text-right">${parseFloat(h.total_ingreso).toFixed(2)}</td>
                                                        <td className="px-3 py-2 text-right">${parseFloat(h.urbano).toFixed(2)}</td>
                                                        <td className="px-3 py-2 text-right">${parseFloat(h.rustico).toFixed(2)}</td>
                                                        <td className="px-3 py-2 text-center">{h.recibos_efectivos}</td>
                                                        <td className="px-3 py-2 text-center">{h.recibos_cancelados}</td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {historialCajasSinCorte.length === 0 && (
                                <div className="mb-4 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded text-sm">
                                    No hay cortes de caja cerrados sin agrupar.
                                </div>
                            )}

                            <div className="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                                    <input type="date" value={form.fecha}
                                        onChange={(e) => setForm({ ...form, fecha: e.target.value })}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ingresos Totales</label>
                                    <input type="number" step="0.01" value={form.ingresos}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urbano</label>
                                    <input type="number" step="0.01" value={form.urbano}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rústico</label>
                                    <input type="number" step="0.01" value={form.rustico}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recibos Efectivos</label>
                                    <input type="number" value={form.recibos_efectivos}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recibos Cancelados</label>
                                    <input type="number" value={form.recibos_cancelados}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                            </div>

                            <div className="flex justify-end gap-2">
                                <button type="button" onClick={() => setShowCreateModal(false)}
                                    className="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</button>
                                <button type="submit"
                                    className="px-4 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-500">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showEditModal && editing && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg p-6">
                        <h3 className="text-lg font-medium mb-4">Editar Corte de Caja #{editing.id}</h3>
                        <form onSubmit={handleUpdate}>
                            <div className="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fecha</label>
                                    <input type="date" value={form.fecha}
                                        onChange={(e) => setForm({ ...form, fecha: e.target.value })}
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" required />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ingresos Totales</label>
                                    <input type="number" step="0.01" value={form.ingresos}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urbano</label>
                                    <input type="number" step="0.01" value={form.urbano}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rústico</label>
                                    <input type="number" step="0.01" value={form.rustico}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recibos Efectivos</label>
                                    <input type="number" value={form.recibos_efectivos}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Recibos Cancelados</label>
                                    <input type="number" value={form.recibos_cancelados}
                                        readOnly
                                        className="w-full rounded-md border-gray-300 shadow-sm bg-gray-100 text-sm cursor-not-allowed" />
                                </div>
                            </div>

                            <div className="flex justify-end gap-2">
                                <button type="button" onClick={() => setShowEditModal(false)}
                                    className="px-4 py-2 text-sm rounded-md border border-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">Cancelar</button>
                                <button type="submit"
                                    className="px-4 py-2 text-sm rounded-md bg-indigo-600 text-white hover:bg-indigo-500">Actualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {showDeleteModal && deleteTarget && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md p-6">
                        <h3 className="text-lg font-medium mb-2">Confirmar Eliminación</h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mb-6">
                            ¿Eliminar el corte de caja #<strong>{deleteTarget.id}</strong> del {deleteTarget.fecha}?
                            <br />Las cajas asociadas quedarán sin asignación de corte.
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
        </AuthenticatedLayout>
    );
}
