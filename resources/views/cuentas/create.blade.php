<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nueva Cuenta') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('cuentas.store') }}">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="indetec" class="block text-sm font-medium">Indetec</label>
                                <input type="text" name="indetec" id="indetec" value="{{ old('indetec') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                @error('indetec') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="nom_indetect" class="block text-sm font-medium">Nombre Indetec</label>
                                <input type="text" name="nom_indetect" id="nom_indetect" value="{{ old('nom_indetect') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                @error('nom_indetect') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="cuenta" class="block text-sm font-medium">Cuenta</label>
                                <input type="text" name="cuenta" id="cuenta" value="{{ old('cuenta') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                @error('cuenta') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="subcuenta" class="block text-sm font-medium">Subcuenta</label>
                                <input type="text" name="subcuenta" id="subcuenta" value="{{ old('subcuenta') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                @error('subcuenta') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4 md:col-span-2">
                                <label for="descripcion" class="block text-sm font-medium">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="3" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ old('descripcion') }}</textarea>
                                @error('descripcion') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="importe" class="block text-sm font-medium">Importe</label>
                                <input type="number" step="0.01" min="0" name="importe" id="importe" value="{{ old('importe') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                @error('importe') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="cuentaMayor_id" class="block text-sm font-medium">Cuenta Mayor</label>
                                <select name="cuentaMayor_id" id="cuentaMayor_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">Sin cuenta mayor</option>
                                    @foreach ($cuentasMayor as $item)
                                        <option value="{{ $item->id }}" {{ old('cuentaMayor_id') == $item->id ? 'selected' : '' }}>{{ $item->cuenta }} - {{ $item->nom_indetect }}</option>
                                    @endforeach
                                </select>
                                @error('cuentaMayor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="indetecMayor_id" class="block text-sm font-medium">Indetec Mayor ID</label>
                                <input type="number" name="indetecMayor_id" id="indetecMayor_id" value="{{ old('indetecMayor_id') }}" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                @error('indetecMayor_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="conac_id" class="block text-sm font-medium">Concepto (Conac)</label>
                                <select name="conac_id" id="conac_id" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">Sin concepto</option>
                                    @foreach ($conacs as $item)
                                        <option value="{{ $item->id }}" {{ old('conac_id') == $item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('conac_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-4">
                            <a href="{{ route('cuentas.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">Cancelar</a>
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
