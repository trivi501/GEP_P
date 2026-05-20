<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle de Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Indetec</h4>
                            <p class="mt-1">{{ $cuenta->indetec ?? '—' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre Indetec</h4>
                            <p class="mt-1">{{ $cuenta->nom_indetect ?? '—' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Cuenta</h4>
                            <p class="mt-1">{{ $cuenta->cuenta ?? '—' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Subcuenta</h4>
                            <p class="mt-1">{{ $cuenta->subcuenta ?? '—' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</h4>
                            <p class="mt-1">{{ $cuenta->descripcion ?? '—' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Importe</h4>
                            <p class="mt-1">${{ number_format($cuenta->importe, 2) }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Cuenta Mayor</h4>
                            <p class="mt-1">{{ $cuenta->cuentaMayor->cuenta ?? '—' }} - {{ $cuenta->cuentaMayor->nom_indetect ?? '—' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Indetec Mayor ID</h4>
                            <p class="mt-1">{{ $cuenta->indetecMayor_id ?? '—' }}</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Concepto (Conac)</h4>
                            <p class="mt-1">{{ $cuenta->conac->nombre ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('cuentas.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Regresar</a>
                        <a href="{{ route('cuentas.edit', $cuenta) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
