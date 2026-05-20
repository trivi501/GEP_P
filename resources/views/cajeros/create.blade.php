<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nuevo Cajero') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('cajeros.store') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="usuario_id" class="block text-sm font-medium">Usuario</label>
                            <select name="usuario_id" id="usuario_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" required>
                                <option value="">Seleccionar usuario</option>
                                @foreach ($usuarios as $usuario)
                                    <option value="{{ $usuario->id }}" {{ old('usuario_id') == $usuario->id ? 'selected' : '' }}>{{ $usuario->name }}</option>
                                @endforeach
                            </select>
                            @error('usuario_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="caja_id" class="block text-sm font-medium">Caja</label>
                            <select name="caja_id" id="caja_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300" required>
                                <option value="">Seleccionar caja</option>
                                @foreach ($cajas as $caja)
                                    <option value="{{ $caja->id }}" {{ old('caja_id') == $caja->id ? 'selected' : '' }}>{{ $caja->nombre }} ({{ $caja->numero }})</option>
                                @endforeach
                            </select>
                            @error('caja_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium">Status</label>
                            <div class="mt-1">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="1" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('status', 1) == 1 ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">Activo</span>
                                </label>
                                <label class="inline-flex items-center ml-4">
                                    <input type="radio" name="status" value="0" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('status') === '0' ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">Inactivo</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('cajeros.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
