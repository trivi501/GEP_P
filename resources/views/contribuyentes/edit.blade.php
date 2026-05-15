<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Editar Contribuyente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('contribuyentes.update', $contribuyente) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="nombre_completo" :value="__('Nombre Completo')" />
                                <x-text-input id="nombre_completo" class="block mt-1 w-full" type="text" name="nombre_completo" :value="old('nombre_completo', $contribuyente->nombre_completo)" />
                                <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="cuenta" :value="__('Cuenta')" />
                                <x-text-input id="cuenta" class="block mt-1 w-full" type="text" name="cuenta" :value="old('cuenta', $contribuyente->cuenta)" required />
                                <x-input-error :messages="$errors->get('cuenta')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nombre" :value="__('Nombre')" />
                                <x-text-input id="nombre" class="block mt-1 w-full" type="text" name="nombre" :value="old('nombre', $contribuyente->nombre)" />
                                <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nombre_moral" :value="__('Nombre Moral')" />
                                <x-text-input id="nombre_moral" class="block mt-1 w-full" type="text" name="nombre_moral" :value="old('nombre_moral', $contribuyente->nombre_moral)" />
                                <x-input-error :messages="$errors->get('nombre_moral')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="primer_apellido" :value="__('Primer Apellido')" />
                                <x-text-input id="primer_apellido" class="block mt-1 w-full" type="text" name="primer_apellido" :value="old('primer_apellido', $contribuyente->primer_apellido)" />
                                <x-input-error :messages="$errors->get('primer_apellido')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="segundo_apellido" :value="__('Segundo Apellido')" />
                                <x-text-input id="segundo_apellido" class="block mt-1 w-full" type="text" name="segundo_apellido" :value="old('segundo_apellido', $contribuyente->segundo_apellido)" />
                                <x-input-error :messages="$errors->get('segundo_apellido')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="rfc" :value="__('RFC')" />
                                <x-text-input id="rfc" class="block mt-1 w-full" type="text" name="rfc" :value="old('rfc', $contribuyente->rfc)" />
                                <x-input-error :messages="$errors->get('rfc')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="curp_contribuyente" :value="__('CURP')" />
                                <x-text-input id="curp_contribuyente" class="block mt-1 w-full" type="text" name="curp_contribuyente" :value="old('curp_contribuyente', $contribuyente->curp_contribuyente)" />
                                <x-input-error :messages="$errors->get('curp_contribuyente')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="id_tipo_contribuyente" :value="__('Tipo Contribuyente')" />
                                <select id="id_tipo_contribuyente" name="id_tipo_contribuyente" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach ($tiposContribuyente as $tipo)
                                        <option value="{{ $tipo->id_tipo_contribuyente }}" {{ old('id_tipo_contribuyente', $contribuyente->id_tipo_contribuyente) == $tipo->id_tipo_contribuyente ? 'selected' : '' }}>
                                            {{ $tipo->area_contribuyente }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_tipo_contribuyente')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="nivel_gobierno" :value="__('Nivel Gobierno')" />
                                <x-text-input id="nivel_gobierno" class="block mt-1 w-full" type="text" name="nivel_gobierno" :value="old('nivel_gobierno', $contribuyente->nivel_gobierno)" />
                                <x-input-error :messages="$errors->get('nivel_gobierno')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="telefono" :value="__('Teléfono')" />
                                <x-text-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono', $contribuyente->telefono)" />
                                <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="correo_electronico" :value="__('Correo Electrónico')" />
                                <x-text-input id="correo_electronico" class="block mt-1 w-full" type="email" name="correo_electronico" :value="old('correo_electronico', $contribuyente->correo_electronico)" />
                                <x-input-error :messages="$errors->get('correo_electronico')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex items-center space-x-2 p-3 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox" name="activo" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    {{ old('activo', $contribuyente->activo) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Activo</span>
                            </label>

                            <label class="flex items-center space-x-2 p-3 rounded border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                <input type="checkbox" name="exento" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800"
                                    {{ old('exento', $contribuyente->exento) ? 'checked' : '' }}>
                                <span class="text-sm text-gray-700 dark:text-gray-300">Exento</span>
                            </label>
                        </div>

                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium mb-4">{{ __('Domicilio') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="id_pais" :value="__('País')" />
                                    <select id="id_pais" name="id_pais" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar país...</option>
                                        @foreach ($paises as $pais)
                                            <option value="{{ $pais->id_pais }}" {{ old('id_pais', $contribuyente->domicilio->id_pais ?? '') == $pais->id_pais ? 'selected' : '' }}>{{ $pais->nombre_pais }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_pais')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_estado" :value="__('Estado')" />
                                    <select id="id_estado" name="id_estado" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar estado...</option>
                                        @foreach ($estados as $estado)
                                            <option value="{{ $estado->id_estado }}" {{ old('id_estado', $contribuyente->domicilio->id_estado ?? '') == $estado->id_estado ? 'selected' : '' }}>{{ $estado->nombre_estado }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_estado')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_municipio" :value="__('Municipio')" />
                                    <select id="id_municipio" name="id_municipio" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar municipio...</option>
                                        @foreach ($municipios as $municipio)
                                            <option value="{{ $municipio->id_municipio }}" data-estado="{{ $municipio->id_estado }}" {{ old('id_municipio', $contribuyente->domicilio->id_municipio ?? '') == $municipio->id_municipio ? 'selected' : '' }}>{{ $municipio->nombre_municipio }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_municipio')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="colonia" :value="__('Colonia')" />
                                    <select id="colonia" name="colonia" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar colonia...</option>
                                        @foreach ($colonias as $colonia)
                                            <option value="{{ $colonia->COLONIA }}" {{ old('colonia', $contribuyente->domicilio->colonia ?? '') == $colonia->COLONIA ? 'selected' : '' }}>{{ $colonia->COLONIA }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('colonia')" class="mt-2" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="nombre_vialidad" :value="__('Calle / Vialidad')" />
                                    <x-text-input id="nombre_vialidad" class="block mt-1 w-full" type="text" name="nombre_vialidad" :value="old('nombre_vialidad', $contribuyente->domicilio->nombre_vialidad ?? '')" />
                                    <x-input-error :messages="$errors->get('nombre_vialidad')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="num_exterior" :value="__('Núm. Exterior')" />
                                    <x-text-input id="num_exterior" class="block mt-1 w-full" type="text" name="num_exterior" :value="old('num_exterior', $contribuyente->domicilio->num_exterior ?? '')" />
                                    <x-input-error :messages="$errors->get('num_exterior')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="num_interior" :value="__('Núm. Interior')" />
                                    <x-text-input id="num_interior" class="block mt-1 w-full" type="text" name="num_interior" :value="old('num_interior', $contribuyente->domicilio->num_interior ?? '')" />
                                    <x-input-error :messages="$errors->get('num_interior')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="codigo_postal" :value="__('Código Postal')" />
                                    <x-text-input id="codigo_postal" class="block mt-1 w-full" type="text" name="codigo_postal" :value="old('codigo_postal', $contribuyente->domicilio->codigo_postal ?? '')" />
                                    <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
                                </div>
                                <div class="md:col-span-3">
                                    <x-input-label for="domicilio_completo" :value="__('Domicilio Completo')" />
                                    <x-text-input id="domicilio_completo" class="block mt-1 w-full" type="text" name="domicilio_completo" :value="old('domicilio_completo', $contribuyente->domicilio->domicilio_completo ?? '')" placeholder="Ej: Calle 123, Col. Centro, CP 80000" />
                                    <x-input-error :messages="$errors->get('domicilio_completo')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium mb-4">{{ __('Datos de Facturación') }}</h4>
                            @php $fact = $contribuyente->datosFacturacion->first(); @endphp
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="fact_rfc" :value="__('RFC Facturación')" />
                                    <x-text-input id="fact_rfc" class="block mt-1 w-full" type="text" name="fact_rfc" :value="old('fact_rfc', $fact->rfc_facturacion ?? '')" />
                                    <x-input-error :messages="$errors->get('fact_rfc')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="fact_razon_social" :value="__('Razón Social')" />
                                    <x-text-input id="fact_razon_social" class="block mt-1 w-full" type="text" name="fact_razon_social" :value="old('fact_razon_social', $fact->razon_social ?? '')" />
                                    <x-input-error :messages="$errors->get('fact_razon_social')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="fact_correo" :value="__('Correo Facturación')" />
                                    <x-text-input id="fact_correo" class="block mt-1 w-full" type="email" name="fact_correo" :value="old('fact_correo', $fact->correo ?? '')" />
                                    <x-input-error :messages="$errors->get('fact_correo')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="fact_id_regimen_fiscal" :value="__('Régimen Fiscal')" />
                                    <select id="fact_id_regimen_fiscal" name="fact_id_regimen_fiscal" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar régimen...</option>
                                        @foreach ($regimenesFiscales as $regimen)
                                            <option value="{{ $regimen->id }}" {{ old('fact_id_regimen_fiscal', $fact->id_f4_c_regimenfiscal ?? '') == $regimen->id ? 'selected' : '' }}>{{ $regimen->c_RegimenFiscal }} - {{ $regimen->Descripción }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('fact_id_regimen_fiscal')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="fact_cp_domicilio_fiscal" :value="__('CP Domicilio Fiscal')" />
                                    <x-text-input id="fact_cp_domicilio_fiscal" class="block mt-1 w-full" type="text" name="fact_cp_domicilio_fiscal" :value="old('fact_cp_domicilio_fiscal', $fact->CP_DomicilioFiscal_contribuyente ?? '')" />
                                    <x-input-error :messages="$errors->get('fact_cp_domicilio_fiscal')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const estadoSelect = document.getElementById('id_estado');
                                const municipioSelect = document.getElementById('id_municipio');
                                const allMunicipios = JSON.parse(document.getElementById('municipios-data').textContent);
                                function filtrarMunicipios() {
                                    const estadoId = parseInt(estadoSelect.value);
                                    const oldValue = municipioSelect.value;
                                    municipioSelect.innerHTML = '<option value="">Seleccionar municipio...</option>';
                                    allMunicipios.filter(m => m.id_estado === estadoId).forEach(m => {
                                        const opt = document.createElement('option');
                                        opt.value = m.id_municipio;
                                        opt.textContent = m.nombre_municipio;
                                        if (opt.value === oldValue) opt.selected = true;
                                        municipioSelect.appendChild(opt);
                                    });
                                }
                                estadoSelect.addEventListener('change', filtrarMunicipios);
                                if (estadoSelect.value) filtrarMunicipios();
                            });
                        </script>
                        <script id="municipios-data" type="application/json">{!! json_encode($municipios->map(fn($m) => ['id_municipio' => $m->id_municipio, 'id_estado' => $m->id_estado, 'nombre_municipio' => $m->nombre_municipio])) !!}</script>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('contribuyentes.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <x-primary-button>
                                {{ __('Actualizar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
