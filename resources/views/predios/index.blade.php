<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Predios') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="dataTable(@js($prediosData))">
        <div class="max-w mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <h3 class="text-lg font-medium">Listado de Predios</h3>
                        <div class="flex items-center gap-4 w-full sm:w-auto">
                            <div class="relative flex-1 sm:flex-initial">
                                <input type="text" x-model="query" @input="handleSearch" placeholder="Buscar en toda la tabla..." class="w-full sm:w-64 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 px-3 py-2 pl-9">
                                <div x-show="loading" class="absolute right-2.5 top-2.5">
                                    <svg class="animate-spin h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                </div>
                                <svg class="absolute left-2.5 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <a href="{{ route('predios.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 whitespace-nowrap">
                                + Nuevo Predio
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th @click="sort('Clave_predial')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Clave Predial <span x-text="sortIndicator('Clave_predial')" class="text-indigo-500"></span></th>
                                    <th @click="sort('cuenta')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Cuenta <span x-text="sortIndicator('cuenta')" class="text-indigo-500"></span></th>
                                    <th @click="sort('contribuyente')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Contribuyente <span x-text="sortIndicator('contribuyente')" class="text-indigo-500"></span></th>
                                    <th @click="sort('colonia')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Colonia <span x-text="sortIndicator('colonia')" class="text-indigo-500"></span></th>
                                    <th @click="sort('tipo_predio')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Tipo <span x-text="sortIndicator('tipo_predio')" class="text-indigo-500"></span></th>
                                    <th @click="sort('estado')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Estado <span x-text="sortIndicator('estado')" class="text-indigo-500"></span></th>
                                    <th @click="sort('ubicacion')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Ubicación <span x-text="sortIndicator('ubicacion')" class="text-indigo-500"></span></th>
                                    <th @click="sort('año_ultimo_pago')" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Año Pago <span x-text="sortIndicator('año_ultimo_pago')" class="text-indigo-500"></span></th>
                                    <th @click="sort('ultimo_bimestre_pago')" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Bimestre <span x-text="sortIndicator('ultimo_bimestre_pago')" class="text-indigo-500"></span></th>
                                    <th @click="sort('superficie')" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer select-none hover:text-gray-700 dark:hover:text-gray-100">Superficie <span x-text="sortIndicator('superficie')" class="text-indigo-500"></span></th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Acciones</th>
                                </tr>
                                <tr>
                                    <td class="px-1 py-1"><input type="text" x-model="f_Clave_predial" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_cuenta" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_contribuyente" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_colonia" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_tipo_predio" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_estado" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_ubicacion" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_año_ultimo_pago" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1 text-center"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_ultimo_bimestre_pago" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1 text-center"></td>
                                    <td class="px-1 py-1"><input type="text" x-model="f_superficie" @input="handleSearch" placeholder="Filtrar..." class="w-full text-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300 px-1.5 py-1 text-right"></td>
                                    <td class="px-1 py-1"></td>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <template x-for="p in filtered" :key="p.id">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium" x-text="p.Clave_predial"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="p.cuenta"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="p.contribuyente"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="p.colonia"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="p.tipo_predio"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="p.estado"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm" x-text="p.ubicacion"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center" x-text="p.año_ultimo_pago"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center" x-text="p.ultimo_bimestre_pago"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right" x-text="p.superficie.toLocaleString('es-MX', {minimumFractionDigits: 2}) + ' m²'"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a x-bind:href="p.tipo_predio && (p.tipo_predio.includes('Rústico') || p.tipo_predio.includes('MINA')) ? `/calculos-predios/pdf-rustico?id_predio=${p.id}` : `/calculos-predios/pdf?id_predio=${p.id}`" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-2" title="Cálculo Predial">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </a>
                                            <a x-bind:href="`/predios/${p.id}/pdf`" target="_blank" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 mr-2" title="ESTADO DE CUENTA">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 3v4a1 1 0 001 1h4"/></svg>
                                            </a>
                                            <a x-bind:href="`/predios/${p.id}`" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-2" title="Ver">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            </a>
                                            <a x-bind:href="`/predios/${p.id}/edit`" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 mr-2" title="Editar">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <button @click="deletePredio(p.id)" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="Eliminar">
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filtered.length === 0">
                                    <tr>
                                        <td colspan="11" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No hay predios registrados.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4" x-show="!hasFilters">
                        {{ $predios->links() }}
                    </div>
                    <div class="mt-4" x-show="hasFilters">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Mostrando <span x-text="(currentPage - 1) * 10 + 1"></span>-<span x-text="Math.min(currentPage * 10, serverTotal)"></span> de <span x-text="serverTotal"></span> registros
                            </div>
                            <div class="flex items-center gap-1 flex-nowrap">
                                <button @click="goToPage(currentPage - 1)" x-show="currentPage > 1" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-sm dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap">&laquo; Anterior</button>
                                <div class="flex items-center gap-1 flex-nowrap" x-html="paginationHtml"></div>
                                <button @click="goToPage(currentPage + 1)" x-show="currentPage < lastPage" class="px-3 py-1 rounded border border-gray-300 dark:border-gray-600 text-sm dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 whitespace-nowrap">Siguiente &raquo;</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
