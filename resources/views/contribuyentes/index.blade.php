<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Contribuyentes') }}
        </h2>
    </x-slot>

    <div id="loading-overlay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-12 w-12 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="mt-3 text-white font-medium">Cargando...</span>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('#search-form input, #search-form select').forEach(el => {
                el.addEventListener('change', function () { document.getElementById('loading-overlay').classList.remove('hidden'); document.getElementById('loading-overlay').classList.add('flex'); });
            });
            document.querySelectorAll('#search-form input[type="text"]').forEach(el => {
                el.addEventListener('input', function () { document.getElementById('loading-overlay').classList.remove('hidden'); document.getElementById('loading-overlay').classList.add('flex'); });
            });
        });
    </script>

    <div class="py-12 w-full">
        <div class="">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Listado de Contribuyentes</h3>
                        <a href="{{ route('contribuyentes.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            + Nuevo Contribuyente
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cuenta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Teléfono</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Correo</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Activo</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                                <tr>
                                    <form method="GET" action="{{ route('contribuyentes.index') }}" id="search-form">
                                        <td class="px-2 py-1"><input type="text" name="nombre_completo" value="{{ request('nombre_completo') }}" placeholder="Buscar nombre..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-2 py-1" oninput="this.form.submit()"></td>
                                        <td class="px-2 py-1"><input type="text" name="cuenta" value="{{ request('cuenta') }}" placeholder="Buscar cuenta..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-2 py-1" oninput="this.form.submit()"></td>
                                        <td class="px-2 py-1"><input type="text" name="tipo" value="{{ request('tipo') }}" placeholder="Buscar tipo..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-2 py-1" oninput="this.form.submit()"></td>
                                        <td class="px-2 py-1"><input type="text" name="telefono" value="{{ request('telefono') }}" placeholder="Buscar teléfono..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-2 py-1" oninput="this.form.submit()"></td>
                                        <td class="px-2 py-1"><input type="text" name="correo_electronico" value="{{ request('correo_electronico') }}" placeholder="Buscar correo..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-2 py-1" oninput="this.form.submit()"></td>
                                        <td class="px-2 py-1 text-center">
                                            <select name="activo" class="text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1 py-1" onchange="this.form.submit()">
                                                <option value="">Todos</option>
                                                <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                        </td>
                                        <td class="px-2 py-1 text-right">
                                            <a href="{{ route('contribuyentes.index') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">Limpiar</a>
                                        </td>
                                    </form>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($contribuyentes as $contribuyente)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $contribuyente->nombre_completo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $contribuyente->cuenta }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $contribuyente->tipoContribuyente->area_contribuyente ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $contribuyente->telefono ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $contribuyente->correo_electronico ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            @if ($contribuyente->activo)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Activo</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">Inactivo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('contribuyentes.show', $contribuyente) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-2" title="Ver">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a href="{{ route('contribuyentes.edit', $contribuyente) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-2" title="Editar">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <form action="{{ route('contribuyentes.destroy', $contribuyente) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de desactivar este contribuyente?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Desactivar">
                                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No hay contribuyentes registrados.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $contribuyentes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
