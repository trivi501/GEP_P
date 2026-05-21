import { Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Pagination({ meta }) {
    if (!meta?.links) return null;

    const [jumpPage, setJumpPage] = useState('');

    const handleJump = (e) => {
        e.preventDefault();
        const page = parseInt(jumpPage, 10);
        if (page >= 1 && page <= meta.last_page) {
            const url = meta.links.find((l) => l.label == page)?.url;
            if (url) {
                router.visit(url, { preserveScroll: true });
            }
        }
        setJumpPage('');
    };

    return (
        <div className="mt-6 flex flex-col items-center gap-4 sm:flex-row sm:justify-between">
            <div className="text-sm text-gray-600">
                Mostrando <span className="font-medium">{meta.from ?? 0}</span> a{' '}
                <span className="font-medium">{meta.to ?? 0}</span> de{' '}
                <span className="font-medium">{meta.total ?? 0}</span> registros
                <span className="ml-2 text-gray-400">
                    (pág. {meta.current_page} de {meta.last_page})
                </span>
            </div>

            <div className="flex items-center gap-1">
                {meta.links.map((link, i) => {
                    if (!link.url) {
                        return (
                            <span
                                key={i}
                                className="px-3 py-1 text-sm text-gray-400 cursor-not-allowed"
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        );
                    }

                    return (
                        <Link
                            key={i}
                            href={link.url}
                            preserveScroll
                            className={`px-3 py-1 text-sm rounded border ${
                                link.active
                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                    : 'text-gray-700 border-gray-300 hover:bg-gray-100'
                            }`}
                            dangerouslySetInnerHTML={{ __html: link.label }}
                        />
                    );
                })}
            </div>

            <form onSubmit={handleJump} className="flex items-center gap-1 text-sm">
                <label className="text-gray-500">Ir a:</label>
                <input
                    type="number"
                    min={1}
                    max={meta.last_page}
                    value={jumpPage}
                    onChange={(e) => setJumpPage(e.target.value)}
                    placeholder="#"
                    className="w-16 rounded border-gray-300 text-center text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                />
                <button
                    type="submit"
                    className="rounded border border-gray-300 px-2 py-1 text-gray-700 hover:bg-gray-100"
                >
                    Ir
                </button>
            </form>
        </div>
    );
}