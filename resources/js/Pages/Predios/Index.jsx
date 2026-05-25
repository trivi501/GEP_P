import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState, useEffect, useRef, useCallback } from 'react';
import Pagination from '@/Components/Pagination';

export default function Index({ predios, prediosData, filters, cajaAbierta }) {
    const page = usePage();
    const permissions = Array.isArray(page.props.userPermissions) ? page.props.userPermissions : [];
    const can = (permiso) => permissions.includes(permiso);
    const [search, setSearch] = useState(page.props.search_global ?? '');
    const [columnFilters, setColumnFilters] = useState(filters ?? {});
    const [contextMenu, setContextMenu] = useState({ show: false, x: 0, y: 0, predio: null });
    const debounceRef = useRef(null);
    const menuRef = useRef(null);

    useEffect(() => {
        return () => {
            if (debounceRef.current) clearTimeout(debounceRef.current);
        };
    }, []);

    const closeContextMenu = useCallback(() => {
        setContextMenu({ show: false, x: 0, y: 0, predio: null });
    }, []);

    useEffect(() => {
        const handleClickOutside = (e) => {
            if (menuRef.current && !menuRef.current.contains(e.target)) {
                closeContextMenu();
            }
        };
        const handleScroll = () => closeContextMenu();
        const handleKeyDown = (e) => { if (e.key === 'Escape') closeContextMenu(); };

        if (contextMenu.show) {
            document.addEventListener('mousedown', handleClickOutside);
            document.addEventListener('scroll', handleScroll, true);
            document.addEventListener('keydown', handleKeyDown);
        }
        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
            document.removeEventListener('scroll', handleScroll, true);
            document.removeEventListener('keydown', handleKeyDown);
        };
    }, [contextMenu.show, closeContextMenu]);

    const handleContextMenu = (e, predio) => {
        e.preventDefault();
        const viewportW = window.innerWidth;
        const viewportH = window.innerHeight;
        let x = e.clientX;
        let y = e.clientY;
        if (x + 160 > viewportW) x = viewportW - 170;
        if (y + 180 > viewportH) y = viewportH - 190;
        setContextMenu({ show: true, x, y, predio });
    };

    const handleSearch = (e) => {
        e.preventDefault();
        const params = {};
        if (search) params.search_global = search;
        Object.entries(columnFilters).forEach(([key, val]) => {
            if (val) params[key] = val;
        });
        router.get(route('predios.index'), params);
    };

    const handleColumnFilterChange = (field, value) => {
        setColumnFilters(prev => ({ ...prev, [field]: value }));
        if (debounceRef.current) clearTimeout(debounceRef.current);
        debounceRef.current = setTimeout(() => {
            const params = {};
            const newFilters = { ...columnFilters, [field]: value };
            Object.entries(newFilters).forEach(([key, val]) => {
                if (val) params[key] = val;
            });
            if (search) params.search_global = search;
            router.get(route('predios.index'), params, { preserveState: true });
        }, 500);
    };

    const inputClass = "block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-2 py-1";

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-100">
                    Predios
                </h2>
            }
        >
            <Head title="Predios" />

            <div className="py-12">
                <div className="mx-auto max-w sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <div className="mb-6 flex items-center justify-between gap-4">
                                <h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                                    Listado de Predios
                                </h3>
                                <Link
                                    href={route('predios.create')}
                                    className="inline-flex items-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-indigo-500 focus:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:bg-indigo-700"
                                >
                                    + Crear Predio
                                </Link>
                            </div>

                            <form onSubmit={handleSearch} className="mb-4">
                                <div className="flex gap-2">
                                    <input
                                        type="text"
                                        value={search}
                                        onChange={(e) => setSearch(e.target.value)}
                                        placeholder="Búsqueda global..."
                                        className="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <button
                                        type="submit"
                                        className="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                    >
                                        Buscar
                                    </button>
                                </div>
                            </form>

                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Clave Catastral</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Cuenta</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Contribuyente</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Colonia</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Ubicación Predio</th>
                                            <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Tipo</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Año Último Pago</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Superficie</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Terreno</th>
                                            <th className="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Construcción</th>
                                            <th className="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Acciones</th>
                                        </tr>
                                        <tr className="border-t border-gray-200 dark:border-gray-600">
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.Clave_predial ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('Clave_predial', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.cuenta ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('cuenta', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.contribuyente ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('contribuyente', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.colonia ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('colonia', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.ubicacion ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('ubicacion', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.tipo_predio ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('tipo_predio', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={inputClass}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.año_ultimo_pago ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('año_ultimo_pago', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={`${inputClass} text-center`}
                                                />
                                            </th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.superficie ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('superficie', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={`${inputClass} text-right`}
                                                />
                                            </th>
                                            <th className="px-2 py-2"></th>
                                            <th className="px-2 py-2">
                                                <input
                                                    type="text"
                                                    value={columnFilters.construccion ?? ''}
                                                    onChange={(e) => handleColumnFilterChange('construccion', e.target.value)}
                                                    placeholder="Filtrar..."
                                                    className={`${inputClass} text-right`}
                                                />
                                            </th>
                                            <th className="px-2 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                        {prediosData?.length > 0 ? (
                                            prediosData.map((predio) => (
                                                <tr key={predio.id} className="hover:bg-gray-50 dark:bg-gray-700" onContextMenu={(e) => handleContextMenu(e, predio)}>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {predio.Clave_predial}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {predio.cuenta}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {predio.contribuyente}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {predio.ubicacion}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {predio.ubicacionPredio}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                        {predio.tipo_predio}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-center text-gray-500 dark:text-gray-400">
                                                        {predio.año_ultimo_pago ?? '—'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                                        {parseFloat(predio.superficie).toLocaleString('es-MX', { minimumFractionDigits: 2 }) + ' m²'}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                                        ${parseFloat(predio.terreno).toLocaleString('es-MX', { minimumFractionDigits: 2 })}
                                                    </td>
                                                    <td className="whitespace-nowrap px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                                        ${parseFloat(predio.construccion).toLocaleString('es-MX', { minimumFractionDigits: 2 })}
                                                    </td>
                                                    <td className="whitespace-nowrap px-2 py-4 text-center text-sm font-medium">
                                                        <div className="flex items-center justify-center gap-2">
                                                            {can('predios-edocuenta') && (
                                                                <a
                                                                    href={`/calculos-predios/${predio.tipo_predio?.toLowerCase().includes('rústico') ? 'pdf-rustico' : 'pdf'}?id_predio=${predio.id}`}
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    className="text-red-600 hover:text-red-900"
                                                                    title="Estado de Cuenta"
                                                            >
                                                                <svg className="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                            </a>
                                                            )}
                                                            {can('predios-cedula') && (
                                                            <a
                                                                href={route('predios.pdf', predio.id)}
                                                                target="_blank"
                                                                rel="noopener noreferrer"
                                                                className="text-blue-600 hover:text-blue-900"
                                                                title="Cédula"
                                                            >
                                                                <svg className="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                                            </a>
                                                            )}
                                                            {can('predios-descuentos') && (
                                                                <a
                                                                    href={`/descuentos?id_predio=${predio.id}`}
                                                                    className="text-purple-600 hover:text-purple-900"
                                                                    title="Descuento"
                                                                >
                                                                    <svg className="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                </a>
                                                            )}
                                                            <Link
                                                                href={route('predios.show', predio.id)}
                                                                className="text-indigo-600 hover:text-indigo-900"
                                                                title="Ver"
                                                            >
                                                                <svg className="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                            </Link>
                                                            {can('predios-show') && (
                                                            <Link
                                                                href={route('predios.edit', predio.id)}
                                                                className="text-yellow-600 hover:text-yellow-900"
                                                                title="Editar"
                                                            >
                                                                <svg className="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                            </Link>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            ))
                                        ) : (
                                            <tr>
                                                <td colSpan="11" className="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                    No hay predios registrados.
                                                </td>
                                            </tr>
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {predios.links && <Pagination meta={predios} />}
                        </div>
                    </div>
                </div>
            </div>

            {contextMenu.show && contextMenu.predio && (
                <div
                    ref={menuRef}
                    className="fixed z-50 w-44 rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5 border border-gray-200 dark:border-gray-600 py-1 text-sm"
                    style={{ left: contextMenu.x, top: contextMenu.y }}
                >
                    {can('predios-edocuenta') && (
                        <a
                            href={`/calculos-predios/${contextMenu.predio.tipo_predio?.toLowerCase().includes('rústico') ? 'pdf-rustico' : 'pdf'}?id_predio=${contextMenu.predio.id}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            onClick={closeContextMenu}
                            className="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <svg className="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Estado de Cuenta
                        </a>
                    )}
                    {can('predios-cedula') && (
                    <a
                        href={route('predios.pdf', contextMenu.predio.id)}
                        target="_blank"
                        rel="noopener noreferrer"
                        onClick={closeContextMenu}
                        className="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg className="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                        Cédula
                    </a>
                    )}
                    {(cajaAbierta && can('predios-pay')) && (
                        <a
                            href={`/pagos/cobrar?id_predio=${contextMenu.predio.id}`}
                            onClick={closeContextMenu}
                            className="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <svg className="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Cobrar
                        </a>
                    )}
                    
                    {can('crear descuentos') && (
                        <a
                            href={`/descuentos?id_predio=${contextMenu.predio.id}`}
                            onClick={closeContextMenu}
                            className="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <svg className="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Descuento
                        </a>
                    )}
                    <hr className="border-gray-200 dark:border-gray-600" />
                    {can('predio-editar') && (
                    <Link
                        href={route('predios.show', contextMenu.predio.id)}
                        onClick={closeContextMenu}
                        className="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg className="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Ver
                    </Link>
                    )}
                    {can('predio-eliminar') && (
                    <Link
                        href={route('predios.edit', contextMenu.predio.id)}
                        onClick={closeContextMenu}
                        className="flex items-center gap-3 px-4 py-2 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    >
                        <svg className="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Editar
                    </Link>
                    )}
                </div>
            )}
        </AuthenticatedLayout>
    );
}
