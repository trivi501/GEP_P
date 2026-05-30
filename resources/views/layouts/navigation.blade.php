<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
           
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('contribuyentes.index')" :active="request()->routeIs('contribuyentes.*')">
                        {{ __('Contribuyentes') }}
                    </x-nav-link>
                    <x-nav-link :href="route('predios.index')" :active="request()->routeIs('predios.*')">
                        {{ __('Predios') }}
                    </x-nav-link>
                    @can('ver tickets')
                    <x-nav-link :href="route('support-tickets.index')" :active="request()->routeIs('support-tickets.*')">
                        {{ __('Soporte') }}
                    </x-nav-link>
                    @endcan
                    <x-dropdown align="center" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150 {{ request()->routeIs('cajas.*') || request()->routeIs('cajeros.*') || request()->routeIs('pagos.*') || request()->routeIs('cuentas.*') || request()->routeIs('secretarias.*') || request()->routeIs('ordenes-pago.*') ? 'border-indigo-500 dark:border-indigo-400 text-gray-900 dark:text-gray-100' : '' }}">
                                <div>{{ __('Operaciones') }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('cajas.index')">
                                {{ __('Listado de Cajas') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('cajas.create')">
                                {{ __('Nueva Caja') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('cajeros.index')">
                                {{ __('Listado de Cajeros') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('cajeros.create')">
                                {{ __('Nuevo Cajero') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('pagos.index')">
                                {{ __('Historial de Caja') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('pagos.historial')">
                                {{ __('Historial de Pagos') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('cuentas.index')">
                                {{ __('Cuentas') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('ordenes-pago.index')">
                                {{ __('Órdenes de Pago') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('secretarias.index')">
                                {{ __('Secretarías') }}
                            </x-dropdown-link>
                            @can('pagosGeneral')
                            <x-dropdown-link :href="route('pagos.pagos-generales')">
                                {{ __('Pagos Generales') }}
                            </x-dropdown-link>
                            @endcan
                        </x-slot>
                    </x-dropdown>
                    @role('Super Admin')
                        <x-dropdown align="center" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 focus:outline-none focus:text-gray-700 dark:focus:text-gray-300 focus:border-gray-300 dark:focus:border-gray-700 transition duration-150 ease-in-out {{ request()->routeIs('users.*') || request()->routeIs('roles.*') || request()->routeIs('permissions.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : '' }}">
                                    {{ __('Administración') }}
                                    <svg class="fill-current h-4 w-4 ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('users.index')">
                                    {{ __('Listado de Usuarios') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('users.create')">
                                    {{ __('Nuevo Usuario') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('roles.index')">
                                    {{ __('Roles') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('permissions.index')">
                                    {{ __('Permisos') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    @endrole
                </div>
            </div>

            <!-- Theme Toggle -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <button @click="toggleTheme()" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 ease-in-out" title="Cambiar tema">
                    <svg x-show="!dark" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="dark" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
                <p> &nbsp; </p>
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }} ok</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contribuyentes.index')" :active="request()->routeIs('contribuyentes.*')">
                {{ __('Contribuyentes') }}
            </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('predios.index')" :active="request()->routeIs('predios.*')">
                    {{ __('Predios') }}
                </x-responsive-nav-link>
                @can('ver tickets')
                <x-responsive-nav-link :href="route('support-tickets.index')" :active="request()->routeIs('support-tickets.*')">
                    {{ __('Soporte') }}
                </x-responsive-nav-link>
                @endcan
                <div class="pt-2 pb-1">
                <div class="px-4 text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">Operaciones</div>
                <x-responsive-nav-link :href="route('cajas.index')" :active="request()->routeIs('cajas.index')">
                    {{ __('Listado de Cajas') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cajas.create')" :active="request()->routeIs('cajas.create')">
                    {{ __('Nueva Caja') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cajeros.index')" :active="request()->routeIs('cajeros.index')">
                    {{ __('Listado de Cajeros') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('cajeros.create')" :active="request()->routeIs('cajeros.create')">
                    {{ __('Nuevo Cajero') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pagos.index')" :active="request()->routeIs('pagos.index')">
                    {{ __('Historial de Caja') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pagos.historial')" :active="request()->routeIs('pagos.historial')">
                    {{ __('Historial de Pagos') }}
                </x-responsive-nav-link>
                @can('pagosGeneral')
                <x-responsive-nav-link :href="route('pagos.pagos-generales')" :active="request()->routeIs('pagos.pagos-generales')">
                    {{ __('Pagos Generales') }}
                </x-responsive-nav-link>
                @endcan
            </div>
            @role('Super Admin')
                <div class="px-4 text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 font-semibold">Administración</div>
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('Usuarios') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('roles.index')" :active="request()->routeIs('roles.*')">
                    {{ __('Roles') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('permissions.index')" :active="request()->routeIs('permissions.*')">
                    {{ __('Permisos') }}
                </x-responsive-nav-link>
            @endrole
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4 flex items-center justify-between">
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <button @click="toggleTheme()" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-150 ease-in-out" title="Cambiar tema">
                    <svg x-show="!dark" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <svg x-show="dark" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </button>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
