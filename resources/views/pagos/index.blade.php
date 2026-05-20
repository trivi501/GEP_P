<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Historial de Caja') }}
        </h2>
    </x-slot>

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
                        <h3 class="text-lg font-medium">Historial de Caja - {{ auth()->user()->name }}</h3>
                        @if ($cajero)
                            @if (!$cajaAbierta)
                                <button onclick="document.getElementById('modal-abrir-caja').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    + Abrir Caja
                                </button>
                            @else
                                <a href="{{ route('pagos.cobrar') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cobrar
                                </a>
                            @endif
                        @endif
                    </div>

                    @if ($cajero)
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm"><strong>Caja asignada:</strong> {{ $cajero->caja->nombre ?? '—' }} (Folio: {{ $cajero->caja->folio ?? '—' }})</p>
                        </div>
                    @else
                        <p class="text-sm text-red-500 mb-4">No tienes un cajero asignado. Contacta al administrador.</p>
                    @endif

                    @if ($historial->total() === 0)
                        <p class="text-sm text-gray-500 dark:text-gray-400">No tienes historial de caja.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Caja</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fondo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Ingreso</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Apertura</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cierre</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($historial as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $item->id }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->caja->nombre ?? '—' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">${{ number_format($item->fondo, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">${{ number_format($item->total_ingreso, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->datetime_apertura ?? '—' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->datetime_cierre ?? '—' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                @if ($item->datetime_apertura && !$item->datetime_cierre)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">Abierta</span>
                                                @elseif ($item->datetime_cierre)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">Cerrada</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $historial->links() }}
                        </div>
                    @endif


                </div>
            </div>
        </div>
    </div>

    <div id="modal-abrir-caja" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Abrir Caja</h3>
                <form method="POST" action="{{ route('pagos.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="fondo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fondo inicial</label>
                        <input type="number" step="0.01" min="0" name="fondo" id="fondo" value="{{ old('fondo') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" required>
                        @error('fondo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modal-abrir-caja').classList.add('hidden')" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Abrir Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
