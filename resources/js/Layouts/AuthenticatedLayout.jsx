import ApplicationLogo from '@/Components/ApplicationLogo';
import Dropdown from '@/Components/Dropdown';
import NavLink from '@/Components/NavLink';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink';
import { Link, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function AuthenticatedLayout({ header, children }) {
    const user = usePage().props.auth.user;
    const isSuperAdmin = usePage().props.userRoles?.includes('SuperAdmin');
    const [darkMode, setDarkMode] = useState(() => localStorage.getItem('darkMode') === 'true');

    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);

    useEffect(() => {
        document.documentElement.classList.toggle('dark', darkMode);
        localStorage.setItem('darkMode', darkMode);
    }, [darkMode]);

    return (
        <div className="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav className="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div className="flex h-16 justify-between">
                        <div className="flex">
                            <div className="flex shrink-0 items-center">
                                <Link href="/">
                                    <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                                </Link>
                            </div>

                            <div className="hidden space-x-2 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink href={route('dashboard')} active={route().current('dashboard')}>
                                    Dashboard
                                </NavLink>

                                <NavLink href={route('contribuyentes.index')} active={route().current('contribuyentes.*')}>
                                    Contribuyentes
                                </NavLink>

                                <NavLink href={route('predios.index')} active={route().current('predios.*')}>
                                    Predios
                                </NavLink>

                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <button
                                            type="button"
                                            className={`inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none ${
                                                route().current('cajas.*') || route().current('cajeros.*') || route().current('pagos.*') || route().current('pagos.historial') || route().current('pagos.caja-general') || route().current('pagos.caja-general.*') || route().current('cuentas.*') || route().current('ordenes-pago.*') || route().current('secretarias.*')
                                                    ? 'border-indigo-400 text-gray-900 focus:border-indigo-700 dark:border-indigo-500 dark:text-gray-100'
                                                    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 focus:border-gray-300 focus:text-gray-700 dark:text-gray-400 dark:hover:border-gray-500 dark:hover:text-gray-200'
                                            }`}
                                        >
                                            Operaciones
                                            <svg className="-me-0.5 ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                                            </svg>
                                        </button>
                                    </Dropdown.Trigger>

                                    <Dropdown.Content>
                                        <Dropdown.Link href={route('cajas.index')}>
                                            Listado de Cajas
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('cajeros.index')}>
                                            Listado de Cajeros
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('pagos.index')}>
                                            Historial de Caja
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('pagos.historial')}>
                                            Historial de Pagos
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('cuentas.index')}>
                                            Cuentas
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('ordenes-pago.index')}>
                                            Órdenes de Pago
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('pagos.caja-general')}>
                                            Caja General
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('secretarias.index')}>
                                            Secretarías
                                        </Dropdown.Link>
                                    </Dropdown.Content>
                                </Dropdown>

                                {isSuperAdmin && (
                                    <Dropdown>
                                        <Dropdown.Trigger>
                                            <button
                                                type="button"
                                                className={`inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium leading-5 transition duration-150 ease-in-out focus:outline-none ${
                                                    route().current('users.*') || route().current('roles.*') || route().current('permissions.*')
                                                        ? 'border-indigo-400 text-gray-900 focus:border-indigo-700 dark:border-indigo-500 dark:text-gray-100'
                                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 focus:border-gray-300 focus:text-gray-700 dark:text-gray-400 dark:hover:border-gray-500 dark:hover:text-gray-200'
                                                }`}
                                            >
                                                Administración
                                                <svg className="-me-0.5 ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                                                </svg>
                                            </button>
                                        </Dropdown.Trigger>

                                        <Dropdown.Content>
                                            <Dropdown.Link href={route('users.index')}>
                                                Usuarios
                                            </Dropdown.Link>
                                            <Dropdown.Link href={route('roles.index')}>
                                                Roles
                                            </Dropdown.Link>
                                            <Dropdown.Link href={route('permissions.index')}>
                                                Permisos
                                            </Dropdown.Link>
                                        </Dropdown.Content>
                                    </Dropdown>
                                )}
                            </div>
                        </div>

                        <div className="hidden sm:ms-6 sm:flex sm:items-center sm:gap-2">
                            <button
                                onClick={() => setDarkMode(prev => !prev)}
                                className="inline-flex items-center rounded-md p-2 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:text-gray-400 dark:hover:text-gray-200"
                                title={darkMode ? 'Modo claro' : 'Modo oscuro'}
                            >
                                {darkMode ? (
                                    <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                ) : (
                                    <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                                )}
                            </button>

                            <div className="relative ms-3">
                                <Dropdown>
                                    <Dropdown.Trigger>
                                        <span className="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                className="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                                            >
                                                {user.name}
                                                <svg className="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fillRule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clipRule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </Dropdown.Trigger>

                                    <Dropdown.Content>
                                        <Dropdown.Link href={route('profile.edit')}>
                                            Profile
                                        </Dropdown.Link>
                                        <Dropdown.Link href={route('logout')} method="post" as="button">
                                            Log Out
                                        </Dropdown.Link>
                                    </Dropdown.Content>
                                </Dropdown>
                            </div>
                        </div>

                        <div className="-me-2 flex items-center sm:hidden">
                            <button
                                onClick={() => setDarkMode(prev => !prev)}
                                className="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:hover:bg-gray-700 dark:hover:text-gray-300"
                                title={darkMode ? 'Modo claro' : 'Modo oscuro'}
                            >
                                {darkMode ? (
                                    <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                ) : (
                                    <svg className="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                                )}
                            </button>

                            <button
                                onClick={() => setShowingNavigationDropdown(previousState => !previousState)}
                                className="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:hover:bg-gray-700 dark:hover:text-gray-300"
                            >
                                <svg className="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path className={!showingNavigationDropdown ? 'inline-flex' : 'hidden'} strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path className={showingNavigationDropdown ? 'inline-flex' : 'hidden'} strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div className={(showingNavigationDropdown ? 'block' : 'hidden') + ' sm:hidden'}>
                    <div className="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink href={route('dashboard')} active={route().current('dashboard')}>
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink href={route('contribuyentes.index')} active={route().current('contribuyentes.*')}>
                            Contribuyentes
                        </ResponsiveNavLink>
                        <ResponsiveNavLink href={route('predios.index')} active={route().current('predios.*')}>
                            Predios
                        </ResponsiveNavLink>

                        <div className="pt-2 pb-1">
                            <div className="px-4 text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">Operaciones</div>
                            <ResponsiveNavLink href={route('cajas.index')} active={route().current('cajas.*')}>
                                Listado de Cajas
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('cajeros.index')} active={route().current('cajeros.*')}>
                                Listado de Cajeros
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('pagos.index')} active={route().current('pagos.index')}>
                                Historial de Caja
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('pagos.historial')} active={route().current('pagos.historial')}>
                                Historial de Pagos
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('cuentas.index')} active={route().current('cuentas.*')}>
                                Cuentas
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('ordenes-pago.index')} active={route().current('ordenes-pago.*')}>
                                Órdenes de Pago
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('pagos.caja-general')} active={route().current('pagos.caja-general') || route().current('pagos.caja-general.*')}>
                                Caja General
                            </ResponsiveNavLink>
                            <ResponsiveNavLink href={route('secretarias.index')} active={route().current('secretarias.*')}>
                                Secretarías
                            </ResponsiveNavLink>
                        </div>


                        {isSuperAdmin && (
                            <div className="pt-2 pb-1">
                                <div className="px-4 text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">Administración</div>
                                <ResponsiveNavLink href={route('users.index')} active={route().current('users.*')}>
                                    Usuarios
                                </ResponsiveNavLink>
                                <ResponsiveNavLink href={route('roles.index')} active={route().current('roles.*')}>
                                    Roles
                                </ResponsiveNavLink>
                                <ResponsiveNavLink href={route('permissions.index')} active={route().current('permissions.*')}>
                                    Permisos
                                </ResponsiveNavLink>
                            </div>
                        )}
                    </div>

                    <div className="border-t border-gray-200 pb-1 pt-4 dark:border-gray-700">
                        <div className="px-4">
                            <div className="text-base font-medium text-gray-800 dark:text-gray-200">{user.name}</div>
                            <div className="text-sm font-medium text-gray-500 dark:text-gray-400">{user.email}</div>
                        </div>

                        <div className="mt-3 space-y-1">
                            <ResponsiveNavLink href={route('profile.edit')}>Profile</ResponsiveNavLink>
                            <ResponsiveNavLink method="post" href={route('logout')} as="button">Log Out</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            {header && (
                <header className="bg-white shadow dark:bg-gray-800 dark:shadow-gray-900/50">
                    <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {header}
                    </div>
                </header>
            )}

            <main>{children}</main>
        </div>
    );
}
