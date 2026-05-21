import { Head, Link } from '@inertiajs/react';
import ApplicationLogo from '@/Components/ApplicationLogo';

export default function Welcome({ auth }) {
    return (
        <>
            <Head title="Bienvenido" />
            <div className="relative min-h-screen flex flex-col items-center justify-center bg-white dark:bg-gray-900">
                <div className="flex flex-col items-center gap-8">
                    <div className="w-96 h-96">
                        <ApplicationLogo className="w-full h-full object-contain" />
                    </div>
                    <h1 className="text-7xl font-bold text-gray-800 dark:text-gray-100 text-center">
                        Presidencia Municipal de Guadalupe
                    </h1>
                    <nav className="mt-4">
                        {auth.user ? (
                            <Link
                                href={route('dashboard')}
                                className="rounded-md px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-500 transition dark:bg-indigo-500 dark:hover:bg-indigo-400"
                            >
                                Dashboard
                            </Link>
                        ) : (
                            <Link
                                href={route('login')}
                                className="rounded-md px-4 py-2 bg-indigo-600 text-white hover:bg-indigo-500 transition dark:bg-indigo-500 dark:hover:bg-indigo-400"
                            >
                                Iniciar Sesión
                            </Link>
                        )}
                    </nav>
                </div>
            </div>
        </>
    );
}
