<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Cálculos Prediales') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">{{ session('error') }}</div>
            @endif

            @if($predio)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-medium">{{ $predio->Clave_predial }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $predio->contribuyente->nombre_completo ?? '—' }} | 
                                {{ $predio->datosUrbano?->zonaUrbana?->descripcion ?? '—' }}
                            </p>
                        </div>
                        <button type="button" 
                            class="calcular-btn inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            data-predio-id="{{ $predio->id_predio }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            Calcular
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Historial de Cálculos</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Año</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Clave Predial</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Contribuyente</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Zona</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Superficie</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Total</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Valor UMA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($calculos as $c)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-2 whitespace-nowrap font-medium">{{ $c->año }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $c->Clave_predial }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $c->contribuyente }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $c->Zona }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">{{ $c->Superficie_texto }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right font-semibold">$ {{ number_format($c->Total, 2) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">$ {{ number_format($c->valor_uma, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No hay cálculos registrados.</td>
                                </tr>
                                @endforelse
                                @if ($calculos->count() > 0)
                                <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                    <td colspan="5" class="px-4 py-2 whitespace-nowrap text-right uppercase">Sumatoria</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">$ {{ number_format($calculos->sum('Total'), 2) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right"></td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $calculos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

   
</x-app-layout>
