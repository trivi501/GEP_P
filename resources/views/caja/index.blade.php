<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Caja - Cobros') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Historial de Cobros</h3>
                        <a href="{{ route('caja.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            + Nuevo Cobro
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Folio</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Contribuyente</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Fecha</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Subtotal</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Descuento</th>
                                    <th class="px-4 py-2 text-right font-medium text-gray-500 dark:text-gray-400">Total</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Forma Pago</th>
                                    <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Usuario</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($pagos as $pago)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-4 py-2 whitespace-nowrap font-medium">{{ $pago->folio_pago }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $pago->contribuyente ?? '—' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $pago->fecha_pago ? \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y H:i') : '—' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">$ {{ number_format($pago->sub_total_pago, 2) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right">$ {{ number_format($pago->total_descuento, 2) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right font-semibold">$ {{ number_format($pago->total_pago, 2) }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">{{ $pago->formaPago?->Descripción ?? '—' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-center">{{ $pago->id_usuario_registra ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">No hay cobros registrados.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $pagos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
