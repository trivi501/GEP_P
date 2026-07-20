import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import usePermissions from '@/Hooks/usePermissions';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import Pagination from '@/Components/Pagination';

export default function Index({ tickets, filters: initialFilters, users }) {
    const { can } = usePermissions();

    const [filters, setFilters] = useState(initialFilters ?? {});

    const setFilter = (key, value) => {
        setFilters(prev => ({ ...prev, [key]: value }));
    };

    const handleSearch = (e) => {
        e.preventDefault();
        const params = {};
        Object.entries(filters).forEach(([k, v]) => { if (v) params[k] = v; });
        router.get(route('support-tickets.index'), params);
    };

    const statusColors = {
        'abierto': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
        'en_proceso': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        'resuelto': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        'cerrado': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
    };

    const formatDateTime = (iso) => {
        if (!iso) return '';
        const d = new Date(iso);
        const pad = (n) => String(n).padStart(2, '0');
        return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    const priorityColors = {
        baja: 'bg-gray-100 text-gray-600',
        media: 'bg-blue-100 text-blue-600',
        alta: 'bg-orange-100 text-orange-600',
        urgente: 'bg-red-100 text-red-600',
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Tickets de Soporte
                </h2>
            }
        >
            <Head title="Tickets de Soporte" />

            <div className="py-12 w-full">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium">Mis Tickets</h3>
                                {can('tickets-create') && (
                                    <Link
                                        href={route('support-tickets.create')}
                                        className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500"
                                    >
                                        + Nuevo Ticket
                                    </Link>
                                )}
                            </div>

                            <form onSubmit={handleSearch} className="mb-4">
                                <div className="flex flex-wrap gap-2">
                                    <input
                                        type="text"
                                        value={filters.search ?? ''}
                                        onChange={(e) => setFilter('search', e.target.value)}
                                        placeholder="Buscar por título..."
                                        className="block rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <select
                                        value={filters.status ?? ''}
                                        onChange={(e) => setFilter('status', e.target.value)}
                                        className="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Todos los estados</option>
                                        <option value="abierto">Abierto</option>
                                        <option value="en_proceso">En Proceso</option>
                                        <option value="resuelto">Resuelto</option>
                                        <option value="cerrado">Cerrado</option>
                                    </select>
                                    <select
                                        value={filters.priority ?? ''}
                                        onChange={(e) => setFilter('priority', e.target.value)}
                                        className="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Todas las prioridades</option>
                                        <option value="baja">Baja</option>
                                        <option value="media">Media</option>
                                        <option value="alta">Alta</option>
                                        <option value="urgente">Urgente</option>
                                    </select>
                                    <button
                                        type="submit"
                                        className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-500"
                                    >
                                        Filtrar
                                    </button>
                                </div>
                            </form>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">ID</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Título</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Creado por</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Asignado a</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Prioridad</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Estatus</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Fecha</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {tickets.data?.length > 0 ? (
                                            tickets.data.map((ticket) => (
                                                <tr key={ticket.id} className="hover:bg-gray-50 dark:bg-gray-700 cursor-pointer" onClick={() => router.visit(route('support-tickets.show', ticket.id))}>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium">#{ticket.id}</td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm max-w-[300px] truncate">{ticket.title}</td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm">{ticket.user?.name ?? '—'}</td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm">{ticket.assigned_user?.name ?? '—'}</td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-center">
                                                        <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${priorityColors[ticket.priority] ?? ''}`}>
                                                            {ticket.priority}
                                                        </span>
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-center">
                                                        <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${statusColors[ticket.status] ?? ''}`}>
                                                            {ticket.status === 'en_proceso' ? 'En Proceso' : ticket.status}
                                                        </span>
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-right text-sm text-gray-500">{formatDateTime(ticket.created_at)}</td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="7" className="px-6 py-4 text-center text-sm text-gray-500">
                                                    No hay tickets registrados.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {tickets.links && <Pagination meta={tickets} />}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
