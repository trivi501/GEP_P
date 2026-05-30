import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router, usePage } from '@inertiajs/react';
import { useState } from 'react';

export default function Show({ ticket, users, canAssign }) {
    const [editing, setEditing] = useState(false);
    const { data, setData, put, processing } = useForm({
        status: ticket.status,
        priority: ticket.priority,
        assigned_to: ticket.assigned_to ?? '',
    });

    const { data: commentData, setData: setCommentData, post: postComment, processing: commentProcessing } = useForm({
        comment: '',
    });

    const statusColors = {
        abierto: 'bg-yellow-100 text-yellow-800',
        en_proceso: 'bg-blue-100 text-blue-800',
        resuelto: 'bg-green-100 text-green-800',
        cerrado: 'bg-gray-100 text-gray-800',
    };

    const priorityColors = {
        baja: 'bg-gray-100 text-gray-600',
        media: 'bg-blue-100 text-blue-600',
        alta: 'bg-orange-100 text-orange-600',
        urgente: 'bg-red-100 text-red-600',
    };

    const formatDateTime = (iso) => {
        if (!iso) return '';
        const d = new Date(iso);
        const pad = (n) => String(n).padStart(2, '0');
        return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`;
    };

    const submitUpdate = (e) => {
        e.preventDefault();
        put(route('support-tickets.update', ticket.id), {
            onSuccess: () => setEditing(false),
        });
    };

    const submitComment = (e) => {
        e.preventDefault();
        postComment(route('support-tickets.comment', ticket.id), {
            onSuccess: () => setCommentData('comment', ''),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Ticket #{ticket.id}
                </h2>
            }
        >
            <Head title={`Ticket #${ticket.id}`} />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between">
                                <h3 className="text-lg font-medium">{ticket.title}</h3>
                                <div className="flex gap-2">
                                    {canAssign && !editing && (
                                        <button
                                            onClick={() => setEditing(true)}
                                            className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500"
                                        >
                                            Editar
                                        </button>
                                    )}
                                    <Link
                                        href={route('support-tickets.index')}
                                        className="inline-flex items-center rounded-md bg-gray-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-500"
                                    >
                                        Volver
                                    </Link>
                                </div>
                            </div>

                            {editing && canAssign ? (
                                <form onSubmit={submitUpdate} className="mb-6 space-y-4 rounded-md border border-gray-200 dark:border-gray-600 p-4">
                                    <div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Estatus</label>
                                            <select
                                                value={data.status}
                                                onChange={(e) => setData('status', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="abierto">Abierto</option>
                                                <option value="en_proceso">En Proceso</option>
                                                <option value="resuelto">Resuelto</option>
                                                <option value="cerrado">Cerrado</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                                            <select
                                                value={data.priority}
                                                onChange={(e) => setData('priority', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="baja">Baja</option>
                                                <option value="media">Media</option>
                                                <option value="alta">Alta</option>
                                                <option value="urgente">Urgente</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Asignar a</label>
                                            <select
                                                value={data.assigned_to}
                                                onChange={(e) => setData('assigned_to', e.target.value)}
                                                className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">Sin asignar</option>
                                                {users.map((u) => (
                                                    <option key={u.id} value={u.id}>{u.name}</option>
                                                ))}
                                            </select>
                                        </div>
                                    </div>
                                    <div className="flex justify-end gap-2">
                                        <button
                                            type="button"
                                            onClick={() => setEditing(false)}
                                            className="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                        >
                                            Cancelar
                                        </button>
                                        <button
                                            type="submit"
                                            disabled={processing}
                                            className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500 disabled:opacity-50"
                                        >
                                            {processing ? 'Guardando...' : 'Guardar'}
                                        </button>
                                    </div>
                                </form>
                            ) : (
                                <div className="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Creado por</label>
                                        <p className="mt-1 text-sm">{ticket.user?.name ?? '—'}</p>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Asignado a</label>
                                        <p className="mt-1 text-sm">{ticket.assigned_user?.name ?? 'Sin asignar'}</p>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                                        <p className="mt-1">
                                            <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${priorityColors[ticket.priority] ?? ''}`}>
                                                {ticket.priority}
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Estatus</label>
                                        <p className="mt-1">
                                            <span className={`inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ${statusColors[ticket.status] ?? ''}`}>
                                                {ticket.status === 'en_proceso' ? 'En Proceso' : ticket.status}
                                            </span>
                                        </p>
                                    </div>
                                    <div className="sm:col-span-2">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                        <p className="mt-1 text-sm whitespace-pre-wrap">{ticket.description}</p>
                                    </div>
                                    {ticket.url && (
                                        <div className="sm:col-span-2">
                                            <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">URL afectada</label>
                                            <a href={ticket.url} target="_blank" rel="noopener noreferrer" className="mt-1 text-sm text-indigo-600 hover:text-indigo-500">
                                                {ticket.url}
                                            </a>
                                        </div>
                                    )}
                                    <div className="sm:col-span-2">
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Creado</label>
                                        <p className="mt-1 text-sm text-gray-500">{formatDateTime(ticket.created_at)}</p>
                                    </div>
                                </div>
                            )}

                            <div className="border-t border-gray-200 dark:border-gray-600 pt-6">
                                <h4 className="mb-4 text-sm font-medium text-gray-700 dark:text-gray-300">Comentarios</h4>

                                <div className="mb-4 space-y-3">
                                    {ticket.comments?.length > 0 ? (
                                        ticket.comments.map((comment) => (
                                            <div key={comment.id} className="rounded-md bg-gray-50 dark:bg-gray-700 p-3">
                                                <div className="mb-1 flex items-center justify-between">
                                                    <span className="text-xs font-medium text-gray-900 dark:text-gray-100">{comment.user?.name ?? '—'}</span>
                                                    <span className="text-xs text-gray-500">{formatDateTime(comment.created_at)}</span>
                                                </div>
                                                <p className="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{comment.comment}</p>
                                            </div>
                                        ))
                                    ) : (
                                        <p className="text-sm text-gray-500">Sin comentarios aún.</p>
                                    )}
                                </div>

                                <form onSubmit={submitComment}>
                                    <textarea
                                        value={commentData.comment}
                                        onChange={(e) => setCommentData('comment', e.target.value)}
                                        rows="3"
                                        placeholder="Escribe un comentario..."
                                        className="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                        required
                                    />
                                    <div className="mt-2 flex justify-end">
                                        <button
                                            type="submit"
                                            disabled={commentProcessing}
                                            className="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500 disabled:opacity-50"
                                        >
                                            {commentProcessing ? 'Enviando...' : 'Comentar'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
