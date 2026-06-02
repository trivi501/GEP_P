import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        description: '',
        url: '',
        image: null,
        priority: 'media',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('support-tickets.store'));
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Nuevo Ticket de Soporte
                </h2>
            }
        >
            <Head title="Nuevo Ticket" />

            <div className="py-12">
                <div className="mx-auto max-w-2xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit} className="space-y-6">
                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Título</label>
                                    <input
                                        type="text"
                                        value={data.title}
                                        onChange={(e) => setData('title', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    {errors.title && <p className="mt-1 text-sm text-red-600">{errors.title}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Descripción</label>
                                    <textarea
                                        value={data.description}
                                        onChange={(e) => setData('description', e.target.value)}
                                        rows="5"
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    />
                                    {errors.description && <p className="mt-1 text-sm text-red-600">{errors.description}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">URL afectada</label>
                                    <input
                                        type="url"
                                        value={data.url}
                                        onChange={(e) => setData('url', e.target.value)}
                                        placeholder="https://..."
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    {errors.url && <p className="mt-1 text-sm text-red-600">{errors.url}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Imagen (opcional)</label>
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                                        onChange={(e) => setData('image', e.target.files[0])}
                                        className="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900 dark:file:text-indigo-300"
                                    />
                                    {data.image && (
                                        <div className="mt-2">
                                            <img
                                                src={URL.createObjectURL(data.image)}
                                                alt="Preview"
                                                className="max-h-48 rounded border"
                                            />
                                        </div>
                                    )}
                                    {errors.image && <p className="mt-1 text-sm text-red-600">{errors.image}</p>}
                                </div>

                                <div>
                                    <label className="block text-sm font-medium text-gray-700 dark:text-gray-300">Prioridad</label>
                                    <select
                                        value={data.priority}
                                        onChange={(e) => setData('priority', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="baja">Baja</option>
                                        <option value="media">Media</option>
                                        <option value="alta">Alta</option>
                                        <option value="urgente">Urgente</option>
                                    </select>
                                </div>

                                <div className="flex items-center justify-end gap-2">
                                    <Link
                                        href={route('support-tickets.index')}
                                        className="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    >
                                        Cancelar
                                    </Link>
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-indigo-500 disabled:opacity-50"
                                    >
                                        {processing ? 'Guardando...' : 'Crear Ticket'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
