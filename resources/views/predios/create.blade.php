<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Nuevo Predio') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('predios.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <x-input-label for="clave_predial_search" :value="__('Clave Predial (catálogo)')" />
                                <div class="relative flex gap-2">
                                    <div class="relative flex-1">
                                        <input id="clave_predial_search" type="text" placeholder="Buscar clave predial..." value="{{ old('clave_predial_nombre') }}" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" autocomplete="off">
                                        <input id="id_clave_predial" name="id_clave_predial" type="hidden" value="{{ old('id_clave_predial') }}">
                                        <div id="clave_predial_results" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-y-auto"></div>
                                    </div>
                                    <button type="button" onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'crear-clave-predial' }))" class="mt-1 inline-flex items-center px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">+ Nueva</button>
                                </div>
                                <x-input-error :messages="$errors->get('id_clave_predial')" class="mt-2" />
                                <p id="clave_predial_selected" class="text-xs text-green-600 dark:text-green-400 mt-1 {{ old('id_clave_predial') ? '' : 'hidden' }}">✓ Clave predial seleccionada</p>
                                <div id="clave_predial_details" class="mt-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg text-xs space-y-1 {{ old('id_clave_predial') ? '' : 'hidden' }}">
                                    <div><span class="text-gray-500">Completa:</span> <span id="cp_completa" class="font-medium"></span></div>
                                    <div><span class="text-gray-500">Población:</span> <span id="cp_poblacion"></span></div>
                                    <div><span class="text-gray-500">Sección:</span> <span id="cp_seccion"></span></div>
                                    <div><span class="text-gray-500">Manzana:</span> <span id="cp_manzana"></span></div>
                                    <div><span class="text-gray-500">Lote:</span> <span id="cp_lote"></span></div>
                                    <div><span class="text-gray-500">SubLote:</span> <span id="cp_subLote"></span></div>
                                    <div><span class="text-gray-500">Parcela:</span> <span id="cp_Parcela"></span></div>
                                    <div><span class="text-gray-500">Prefijo:</span> <span id="cp_prefijo"></span></div>
                                    <div><span class="text-gray-500">Manzana rústico:</span> <span id="cp_manzana_rustico"></span></div>
                                    <div><span class="text-gray-500">Lote rústico:</span> <span id="cp_lote_rustico"></span></div>
                                </div>
                            </div>
                            <div>
                                <x-input-label for="Clave_predial" :value="__('Clave Predial')" />
                                <x-text-input id="Clave_predial" class="block mt-1 w-full" type="text" name="Clave_predial" :value="old('Clave_predial')" readonly required />
                                <x-input-error :messages="$errors->get('Clave_predial')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="contribuyente_search" :value="__('Contribuyente')" />
                                <div class="relative">
                                    <input id="contribuyente_search" type="text" placeholder="Buscar contribuyente..." value="{{ old('contribuyente_nombre') }}" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" autocomplete="off">
                                    <input id="id_contribuyente" name="id_contribuyente" type="hidden" value="{{ old('id_contribuyente') }}">
                                    <div id="contribuyente_results" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-y-auto"></div>
                                </div>
                                <x-input-error :messages="$errors->get('id_contribuyente')" class="mt-2" />
                                <p id="contribuyente_selected" class="text-xs text-green-600 dark:text-green-400 mt-1 {{ old('id_contribuyente') ? '' : 'hidden' }}">✓ Contribuyente seleccionado</p>
                            </div>
                            <div>
                                <x-input-label for="id_tipo_predio" :value="__('Tipo Predio')" />
                                <select id="id_tipo_predio" name="id_tipo_predio" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" required>
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach ($tiposPredio as $t)
                                        <option value="{{ $t->id_tipo_predio }}" {{ old('id_tipo_predio') == $t->id_tipo_predio ? 'selected' : '' }}>{{ $t->Tipo_predio }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('id_tipo_predio')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="colonia_search" :value="__('Colonia')" />
                                <div class="relative">
                                    <input id="colonia_search" type="text" placeholder="Buscar colonia..." value="{{ old('colonia_nombre') }}" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" autocomplete="off">
                                    <input id="id_colonia" name="id_colonia" type="hidden" value="{{ old('id_colonia') }}">
                                    <div id="colonia_results" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-y-auto"></div>
                                </div>
                                <x-input-error :messages="$errors->get('id_colonia')" class="mt-2" />
                                <p id="colonia_selected" class="text-xs text-green-600 dark:text-green-400 mt-1 {{ old('id_colonia') ? '' : 'hidden' }}">✓ Colonia seleccionada</p>
                            </div>
                            <div>
                                <x-input-label for="calle_search" :value="__('Calle')" />
                                <div class="relative">
                                    <input id="calle_search" type="text" placeholder="Buscar calle..." value="{{ old('calle_nombre') }}" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" autocomplete="off">
                                    <input id="id_calle" name="id_calle" type="hidden" value="{{ old('id_calle') }}">
                                    <div id="calle_results" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-48 overflow-y-auto"></div>
                                </div>
                                <x-input-error :messages="$errors->get('id_calle')" class="mt-2" />
                                <p id="calle_selected" class="text-xs text-green-600 dark:text-green-400 mt-1 {{ old('id_calle') ? '' : 'hidden' }}">✓ Calle seleccionada</p>
                            </div>
                            <div>
                                <x-input-label for="ubicacion" :value="__('Ubicación')" />
                                <x-text-input id="ubicacion" class="block mt-1 w-full" type="text" name="ubicacion" :value="old('ubicacion')" />
                                <x-input-error :messages="$errors->get('ubicacion')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="codigo_postal" :value="__('Código Postal')" />
                                <x-text-input id="codigo_postal" class="block mt-1 w-full" type="text" name="codigo_postal" :value="old('codigo_postal')" />
                                <x-input-error :messages="$errors->get('codigo_postal')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="Numero_exterior" :value="__('Núm. Exterior')" />
                                <x-text-input id="Numero_exterior" class="block mt-1 w-full" type="text" name="Numero_exterior" :value="old('Numero_exterior')" />
                                <x-input-error :messages="$errors->get('Numero_exterior')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="Numero_interior" :value="__('Núm. Interior')" />
                                <x-text-input id="Numero_interior" class="block mt-1 w-full" type="text" name="Numero_interior" :value="old('Numero_interior')" />
                                <x-input-error :messages="$errors->get('Numero_interior')" class="mt-2" />
                            </div>
                            <div class="md:col-span-3">
                                <x-input-label for="Referencia_entre_calle1" :value="__('Referencia entre calles')" />
                                <div class="grid grid-cols-2 gap-4">
                                    <x-text-input id="Referencia_entre_calle1" class="block mt-1 w-full" type="text" name="Referencia_entre_calle1" :value="old('Referencia_entre_calle1')" placeholder="Entre calle 1" />
                                    <x-text-input id="Referncia_entre_calle2" class="block mt-1 w-full" type="text" name="Referncia_entre_calle2" :value="old('Referncia_entre_calle2')" placeholder="Entre calle 2" />
                                </div>
                                <x-input-error :messages="$errors->get('Referencia_entre_calle1')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium mb-4">{{ __('Características del Predio') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="superficie" :value="__('Superficie (m²)')" />
                                    <x-text-input id="superficie" class="block mt-1 w-full" type="number" step="0.0001" name="superficie" :value="old('superficie')" />
                                    <x-input-error :messages="$errors->get('superficie')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="construccion" :value="__('Construcción (m²)')" />
                                    <x-text-input id="construccion" class="block mt-1 w-full" type="number" step="0.0001" name="construccion" :value="old('construccion')" />
                                    <x-input-error :messages="$errors->get('construccion')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="valor_catastral" :value="__('Valor Catastral')" />
                                    <x-text-input id="valor_catastral" class="block mt-1 w-full" type="number" step="0.01" name="valor_catastral" :value="old('valor_catastral')" />
                                    <x-input-error :messages="$errors->get('valor_catastral')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="valor_fiscal" :value="__('Valor Fiscal')" />
                                    <x-text-input id="valor_fiscal" class="block mt-1 w-full" type="number" step="0.01" name="valor_fiscal" :value="old('valor_fiscal')" />
                                    <x-input-error :messages="$errors->get('valor_fiscal')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_regimen_propiedad" :value="__('Régimen de Propiedad')" />
                                    <select id="id_regimen_propiedad" name="id_regimen_propiedad" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar régimen...</option>
                                        @foreach ($regimenesPropiedad as $r)
                                            <option value="{{ $r->id_regimen_propiedad }}" {{ old('id_regimen_propiedad') == $r->id_regimen_propiedad ? 'selected' : '' }}>{{ $r->REGIMEN }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_regimen_propiedad')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_estado_renta" :value="__('Estado de Renta')" />
                                    <select id="id_estado_renta" name="id_estado_renta" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar estado...</option>
                                        @foreach ($estadosRenta as $e)
                                            <option value="{{ $e->id_estado_renta }}" {{ old('id_estado_renta') == $e->id_estado_renta ? 'selected' : '' }}>{{ $e->DESCRIPCION }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_estado_renta')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_estaus_cobro_predial" :value="__('Estatus Cobro')" />
                                    <select id="id_estaus_cobro_predial" name="id_estaus_cobro_predial" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar estatus...</option>
                                        @foreach ($estadosImpuesto as $e)
                                            <option value="{{ $e->id_estaus_cobro_predial }}" {{ old('id_estaus_cobro_predial') == $e->id_estaus_cobro_predial ? 'selected' : '' }}>{{ $e->DESCRIPCION }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_estaus_cobro_predial')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_titulo_propiedad" :value="__('Título de Propiedad')" />
                                    <select id="id_titulo_propiedad" name="id_titulo_propiedad" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar título...</option>
                                        @foreach ($titulosPropiedad as $t)
                                            <option value="{{ $t->id_titulo_propiedad }}" {{ old('id_titulo_propiedad') == $t->id_titulo_propiedad ? 'selected' : '' }}>{{ $t->DESCRIPCION }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_titulo_propiedad')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="numero_de_escritura" :value="__('Número de Escritura')" />
                                    <x-text-input id="numero_de_escritura" class="block mt-1 w-full" type="text" name="numero_de_escritura" :value="old('numero_de_escritura')" />
                                    <x-input-error :messages="$errors->get('numero_de_escritura')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="latitud" :value="__('Latitud')" />
                                    <x-text-input id="latitud" class="block mt-1 w-full" type="text" step="any" name="latitud" :value="old('latitud')" />
                                    <x-input-error :messages="$errors->get('latitud')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="longitud" :value="__('Longitud')" />
                                    <x-text-input id="longitud" class="block mt-1 w-full" type="text" step="any" name="longitud" :value="old('longitud')" />
                                    <x-input-error :messages="$errors->get('longitud')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium mb-4">{{ __('Información de Registro') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label :value="__('Fecha de Alta')" />
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Se asignará automáticamente</p>
                                </div>
                                <div>
                                    <x-input-label :value="__('Usuario')" />
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ auth()->user()->name ?? auth()->user()->id ?? '—' }}</p>
                                </div>
                                <div>
                                    <x-input-label for="año_ultimo_pago" :value="__('Último año de pago')" />
                                    <x-text-input id="año_ultimo_pago" class="block mt-1 w-full" type="number" name="año_ultimo_pago" :value="old('año_ultimo_pago')" />
                                    <x-input-error :messages="$errors->get('año_ultimo_pago')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium mb-4">{{ __('Datos Urbano') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <x-input-label for="id_zona_urbana" :value="__('Zona Urbana')" />
                                    <select id="id_zona_urbana" name="id_zona_urbana" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar zona...</option>
                                        @foreach ($zonasUrbana as $z)
                                            <option value="{{ $z->id_zona_urbana }}" {{ old('id_zona_urbana') == $z->id_zona_urbana ? 'selected' : '' }}>{{ $z->descripcion }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_zona_urbana')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_forma_predio" :value="__('Forma del Predio')" />
                                    <select id="id_forma_predio" name="id_forma_predio" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar forma...</option>
                                        @foreach ($formasPredio as $f)
                                            <option value="{{ $f->id_forma_predio }}" {{ old('id_forma_predio') == $f->id_forma_predio ? 'selected' : '' }}>{{ $f->descripcion }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_forma_predio')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_uso_predio" :value="__('Uso del Predio')" />
                                    <select id="id_uso_predio" name="id_uso_predio" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar uso...</option>
                                        @foreach ($usosPredioUrbano as $u)
                                            <option value="{{ $u->id_uso_predio }}" {{ old('id_uso_predio') == $u->id_uso_predio ? 'selected' : '' }}>{{ $u->descripcion }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_uso_predio')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_estado_fisico" :value="__('Estado Físico')" />
                                    <select id="id_estado_fisico" name="id_estado_fisico" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar estado...</option>
                                        @foreach ($estadosFisicos as $e)
                                            <option value="{{ $e->id_estado_fisico }}" {{ old('id_estado_fisico') == $e->id_estado_fisico ? 'selected' : '' }}>{{ $e->DESCRIPCION }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_estado_fisico')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="numero_de_pisos_construidos" :value="__('Número de Pisos')" />
                                    <x-text-input id="numero_de_pisos_construidos" class="block mt-1 w-full" type="number" min="0" max="255" name="numero_de_pisos_construidos" :value="old('numero_de_pisos_construidos')" />
                                    <x-input-error :messages="$errors->get('numero_de_pisos_construidos')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="superficie_terreno_metros_cuadrados" :value="__('Superficie Terreno (m²)')" />
                                    <x-text-input id="superficie_terreno_metros_cuadrados" class="block mt-1 w-full" type="number" step="0.01" name="superficie_terreno_metros_cuadrados" :value="old('superficie_terreno_metros_cuadrados')" />
                                    <x-input-error :messages="$errors->get('superficie_terreno_metros_cuadrados')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="Frente_metros" :value="__('Frente (m)')" />
                                    <x-text-input id="Frente_metros" class="block mt-1 w-full" type="number" step="0.01" name="Frente_metros" :value="old('Frente_metros')" />
                                    <x-input-error :messages="$errors->get('Frente_metros')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="Fondo_metros" :value="__('Fondo (m)')" />
                                    <x-text-input id="Fondo_metros" class="block mt-1 w-full" type="number" step="0.01" name="Fondo_metros" :value="old('Fondo_metros')" />
                                    <x-input-error :messages="$errors->get('Fondo_metros')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="id_pavimientacion" :value="__('Pavimentación')" />
                                    <select id="id_pavimientacion" name="id_pavimientacion" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar pavimento...</option>
                                        @foreach ($pavimentos as $p)
                                            <option value="{{ $p->id_pavimientacion }}" {{ old('id_pavimientacion') == $p->id_pavimientacion ? 'selected' : '' }}>{{ $p->DESCRIPCION }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_pavimientacion')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="valor_catastral_terreno" :value="__('Valor Catastral Terreno')" />
                                    <x-text-input id="valor_catastral_terreno" class="block mt-1 w-full" type="number" step="0.01" name="valor_catastral_terreno" :value="old('valor_catastral_terreno')" />
                                    <x-input-error :messages="$errors->get('valor_catastral_terreno')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="valor_catastral_construido" :value="__('Valor Catastral Construido')" />
                                    <x-text-input id="valor_catastral_construido" class="block mt-1 w-full" type="number" step="0.01" name="valor_catastral_construido" :value="old('valor_catastral_construido')" />
                                    <x-input-error :messages="$errors->get('valor_catastral_construido')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="Baldio" :value="__('Baldío')" />
                                    <select id="Baldio" name="Baldio" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar...</option>
                                        <option value="1" {{ old('Baldio') == '1' ? 'selected' : '' }}>Sí</option>
                                        <option value="0" {{ old('Baldio') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('Baldio')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="servicio_agua" :value="__('Agua')" />
                                    <select id="servicio_agua" name="servicio_agua" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar...</option>
                                        <option value="1" {{ old('servicio_agua') == '1' ? 'selected' : '' }}>Sí</option>
                                        <option value="0" {{ old('servicio_agua') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('servicio_agua')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="servicio_drenaje" :value="__('Drenaje')" />
                                    <select id="servicio_drenaje" name="servicio_drenaje" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar...</option>
                                        <option value="1" {{ old('servicio_drenaje') == '1' ? 'selected' : '' }}>Sí</option>
                                        <option value="0" {{ old('servicio_drenaje') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('servicio_drenaje')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="servicio_energia_electrica" :value="__('Energía Eléctrica')" />
                                    <select id="servicio_energia_electrica" name="servicio_energia_electrica" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar...</option>
                                        <option value="1" {{ old('servicio_energia_electrica') == '1' ? 'selected' : '' }}>Sí</option>
                                        <option value="0" {{ old('servicio_energia_electrica') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('servicio_energia_electrica')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="servicio_alumbrado" :value="__('Alumbrado')" />
                                    <select id="servicio_alumbrado" name="servicio_alumbrado" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar...</option>
                                        <option value="1" {{ old('servicio_alumbrado') == '1' ? 'selected' : '' }}>Sí</option>
                                        <option value="0" {{ old('servicio_alumbrado') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('servicio_alumbrado')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="cuenta_con_banqueta" :value="__('Banqueta')" />
                                    <select id="cuenta_con_banqueta" name="cuenta_con_banqueta" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                                        <option value="">Seleccionar...</option>
                                        <option value="1" {{ old('cuenta_con_banqueta') == '1' ? 'selected' : '' }}>Sí</option>
                                        <option value="0" {{ old('cuenta_con_banqueta') == '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('cuenta_con_banqueta')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h4 class="text-lg font-medium mb-4">{{ __('Observaciones') }}</h4>
                            <textarea name="observacion" rows="3" class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Agregar observación...">{{ old('observacion') }}</textarea>
                        </div>

                        <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-medium">{{ __('Medidas y Colindancias') }}</h4>
                                <button type="button" id="agregar-medida" class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">+ Agregar</button>
                            </div>
                            <div id="medidas-container" class="space-y-2">
                                <p id="medidas-empty" class="text-sm text-gray-500 dark:text-gray-400">Sin medidas registradas</p>
                            </div>
                            <template id="medida-template">
                                <div class="medida-row flex gap-2 items-end p-2 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500">Orientación</label>
                                        <select name="medidas[__INDEX__][id_orientacion]" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">Seleccionar...</option>
                                            @foreach ($orientaciones as $o)
                                                <option value="{{ $o->id_orientacion }}">{{ $o->descripcion }} ({{ $o->ORIENTA }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="w-32">
                                        <label class="text-xs text-gray-500">Medida (m)</label>
                                        <input type="number" step="0.0001" name="medidas[__INDEX__][medida_en_metros]" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="0.00">
                                    </div>
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500">Colinda con</label>
                                        <input type="text" name="medidas[__INDEX__][colinda_con]" maxlength="150" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm" placeholder="Nombre del colindante">
                                    </div>
                                    <button type="button" class="eliminar-medida mt-1 inline-flex items-center px-2 py-2 bg-red-100 dark:bg-red-900 border border-transparent rounded-md font-semibold text-xs text-red-600 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">&times;</button>
                                </div>
                            </template>
                            <div id="medidas-error" class="text-sm text-red-600 dark:text-red-400 mt-2 hidden">Debe agregar al menos una medida con orientación</div>
                        </div>

                        <div class="flex items-center justify-end gap-4 mt-6">
                            <a href="{{ route('predios.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Cancelar</a>
                            <x-primary-button>{{ __('Guardar') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="crear-clave-predial" maxWidth="lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Nueva Clave Predial') }}</h3>
            <form id="form-crear-clave-predial" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label value="Clave Predial Completa" />
                        <x-text-input id="cp_modal_completa" class="block mt-1 w-full" type="text" maxlength="18" required />
                    </div>
                    <div>
                        <x-input-label value="Tipo Predio" />
                        <select id="cp_modal_tipo_predio" class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Seleccionar...</option>
                            @foreach ($tiposPredio as $t)
                                <option value="{{ $t->id_tipo_predio }}">{{ $t->Tipo_predio }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label value="Población (ID)" />
                        <x-text-input id="cp_modal_poblacion" class="block mt-1 w-full cp-input" type="number" oninput="autoFillClave()" />
                    </div>
                    <div>
                        <x-input-label value="Sección (ID)" />
                        <x-text-input id="cp_modal_seccion" class="block mt-1 w-full cp-input" type="number" oninput="autoFillClave()" />
                    </div>
                    <div>
                        <x-input-label value="Manzana (ID)" />
                        <x-text-input id="cp_modal_manzana" class="block mt-1 w-full cp-input" type="number" oninput="autoFillClave()" />
                    </div>
                    <div>
                        <x-input-label value="Lote (ID)" />
                        <x-text-input id="cp_modal_lote" class="block mt-1 w-full cp-input" type="number" oninput="autoFillClave()" />
                    </div>
                    <div>
                        <x-input-label value="SubLote" />
                        <x-text-input id="cp_modal_subLote" class="block mt-1 w-full cp-input" type="text" maxlength="2" oninput="autoFillClave()" />
                    </div>
                    <div>
                        <x-input-label value="Parcela" />
                        <x-text-input id="cp_modal_Parcela" class="block mt-1 w-full cp-input" type="text" maxlength="6" oninput="autoFillClave()" />
                    </div>
                    <div>
                        <x-input-label value="Prefijo" />
                        <x-text-input id="cp_modal_prefijo" class="block mt-1 w-full cp-input" type="text" maxlength="6" oninput="autoFillClave()" />
                    </div>
                    <div>
                        <x-input-label value="Manzana Rústico" />
                        <x-text-input id="cp_modal_manzana_rustico" class="block mt-1 w-full cp-input" type="text" maxlength="3" oninput="autoFillClave()" />
                    </div>
                    <div>
                        <x-input-label value="Lote Rústico" />
                        <x-text-input id="cp_modal_lote_rustico" class="block mt-1 w-full cp-input" type="text" maxlength="2" oninput="autoFillClave()" />
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" x-on:click="$dispatch('close-modal', 'crear-clave-predial')" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Cancelar</button>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Guardar</button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
    function autoFillClave() {
        var parts = ['cp_modal_prefijo', 'cp_modal_poblacion', 'cp_modal_seccion', 'cp_modal_manzana', 'cp_modal_lote', 'cp_modal_subLote', 'cp_modal_Parcela', 'cp_modal_manzana_rustico', 'cp_modal_lote_rustico'];
        var values = parts.map(function(id) {
            var el = document.getElementById(id);
            return el ? el.value : '';
        });
        var completa = values.filter(function(v) { return v !== ''; }).join('');
        document.getElementById('cp_modal_completa').value = completa;
    }

    (function() {
        console.log('Contribuyente search script loaded');
        var searchInput = document.getElementById('contribuyente_search');
        var hiddenInput = document.getElementById('id_contribuyente');
        var resultsDiv = document.getElementById('contribuyente_results');
        var selectedP = document.getElementById('contribuyente_selected');
        var debounceTimer;
        var cache = {};

        if (!searchInput) return;

        function hideResults() {
            resultsDiv.classList.add('hidden');
        }

        function selectContribuyente(id, nombre) {
            hiddenInput.value = id;
            searchInput.value = nombre;
            selectedP.classList.remove('hidden');
            cache = {};
            hideResults();
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var q = this.value.trim();
            if (q.length < 3) {
                if (q.length === 0) { cache = {}; }
                hideResults();
                return;
            }
            debounceTimer = setTimeout(function () {
                if (cache[q]) {
                    renderResults(cache[q]);
                    return;
                }
                var url = '{{ route("contribuyentes.search") }}' + '?q=' + encodeURIComponent(q);
                fetch(url)
                    .then(function (r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        cache = {};
                        cache[q] = data;
                        renderResults(data);
                    })
                    .catch(function (err) {
                        console.error('Search error:', err);
                        resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-red-500">Error: ' + err.message + '</div>';
                        resultsDiv.classList.remove('hidden');
                    });
            }, 400);
        });

        function renderResults(data) {
            resultsDiv.innerHTML = '';
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Sin resultados</div>';
            } else {
                data.forEach(function (c) {
                    var div = document.createElement('div');
                    div.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-600 last:border-b-0';
                    div.textContent = c.nombre_completo + ' (' + c.cuenta + ')';
                    div.dataset.id = c.id_contribuyente;
                    div.dataset.nombre = c.nombre_completo;
                    div.addEventListener('click', function () {
                        selectContribuyente(this.dataset.id, this.dataset.nombre);
                    });
                    resultsDiv.appendChild(div);
                });
            }
            resultsDiv.classList.remove('hidden');
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#contribuyente_search') && !e.target.closest('#contribuyente_results')) {
                hideResults();
            }
        });

        searchInput.addEventListener('blur', function () {
            setTimeout(hideResults, 200);
        });
    })();

    (function() {
        var searchInput = document.getElementById('clave_predial_search');
        var hiddenInput = document.getElementById('id_clave_predial');
        var resultsDiv = document.getElementById('clave_predial_results');
        var selectedP = document.getElementById('clave_predial_selected');
        var debounceTimer;

        if (!searchInput) return;

        function hideResults() {
            resultsDiv.classList.add('hidden');
        }

        window.selectClavePredial = function selectClavePredial(id, data) {
            hiddenInput.value = id;
            searchInput.value = data.clave_predial_completa;
            selectedP.classList.remove('hidden');
            hideResults();
            var det = document.getElementById('clave_predial_details');
            if (det) {
                det.classList.remove('hidden');
                document.getElementById('cp_completa').textContent = data.clave_predial_completa || '—';
                document.getElementById('cp_poblacion').textContent = data.id_poblacion || '—';
                document.getElementById('cp_seccion').textContent = data.id_seccion || '—';
                document.getElementById('cp_manzana').textContent = data.id_manzana || '—';
                document.getElementById('cp_lote').textContent = data.id_lote || '—';
                document.getElementById('cp_subLote').textContent = data.subLote || '—';
                document.getElementById('cp_Parcela').textContent = data.Parcela || '—';
                document.getElementById('cp_prefijo').textContent = data.prefijo || '—';
                document.getElementById('cp_manzana_rustico').textContent = data.manzana_rustico || '—';
                document.getElementById('cp_lote_rustico').textContent = data.lote_rustico || '—';
            }
            var clavePredialInput = document.getElementById('Clave_predial');
            if (clavePredialInput) {
                clavePredialInput.value = data.clave_predial_completa;
            }
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var q = this.value.trim();
            if (q.length < 2) {
                hideResults();
                return;
            }
            debounceTimer = setTimeout(function () {
                var url = '{{ route("predios.clave-predial-search") }}' + '?q=' + encodeURIComponent(q);
                console.log('Fetching clave:', url);
                fetch(url)
                    .then(function (r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        resultsDiv.innerHTML = '';
                        if (data.length === 0) {
                            resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Sin resultados</div>';
                        } else {
                            data.forEach(function (c) {
                                var div = document.createElement('div');
                                div.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-600 last:border-b-0';
                                div.textContent = c.clave_predial_completa;
                                div.dataset.id = c.id_clave_predial;
                                div.dataset.data = JSON.stringify(c);
                                div.addEventListener('click', function () {
                                    selectClavePredial(this.dataset.id, JSON.parse(this.dataset.data));
                                });
                                resultsDiv.appendChild(div);
                            });
                        }
                        resultsDiv.classList.remove('hidden');
                    })
                    .catch(function (err) {
                        console.error('Search error:', err);
                        resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-red-500">Error: ' + err.message + '</div>';
                        resultsDiv.classList.remove('hidden');
                    });
            }, 300);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#clave_predial_search') && !e.target.closest('#clave_predial_results')) {
                hideResults();
            }
        });

        searchInput.addEventListener('blur', function () {
            setTimeout(hideResults, 200);
        });
    })();

    (function() {
        var searchInput = document.getElementById('calle_search');
        var hiddenInput = document.getElementById('id_calle');
        var resultsDiv = document.getElementById('calle_results');
        var selectedP = document.getElementById('calle_selected');
        var debounceTimer;
        var cache = {};

        if (!searchInput) return;

        function hideResults() { resultsDiv.classList.add('hidden'); }

        function selectCalle(id, nombre) {
            hiddenInput.value = id;
            searchInput.value = nombre;
            selectedP.classList.remove('hidden');
            cache = {};
            hideResults();
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var q = this.value.trim();
            if (q.length < 2) {
                if (q.length === 0) { cache = {}; }
                hideResults();
                return;
            }
            debounceTimer = setTimeout(function () {
                if (cache[q]) { renderResults(cache[q]); return; }
                var url = '{{ route("predios.calle-search") }}' + '?q=' + encodeURIComponent(q);
                fetch(url)
                    .then(function (r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        cache = {};
                        cache[q] = data;
                        renderResults(data);
                    })
                    .catch(function (err) {
                        console.error('Calle search error:', err);
                        resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-red-500">Error: ' + err.message + '</div>';
                        resultsDiv.classList.remove('hidden');
                    });
            }, 400);
        });

        function renderResults(data) {
            resultsDiv.innerHTML = '';
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Sin resultados</div>';
            } else {
                data.forEach(function (c) {
                    var div = document.createElement('div');
                    div.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-600 last:border-b-0';
                    div.textContent = c.CALLE;
                    div.dataset.id = c.id_calle;
                    div.dataset.nombre = c.CALLE;
                    div.addEventListener('click', function () { selectCalle(this.dataset.id, this.dataset.nombre); });
                    resultsDiv.appendChild(div);
                });
            }
            resultsDiv.classList.remove('hidden');
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#calle_search') && !e.target.closest('#calle_results')) hideResults();
        });

        searchInput.addEventListener('blur', function () { setTimeout(hideResults, 200); });
    })();

    (function() {
        var searchInput = document.getElementById('colonia_search');
        var hiddenInput = document.getElementById('id_colonia');
        var resultsDiv = document.getElementById('colonia_results');
        var selectedP = document.getElementById('colonia_selected');
        var debounceTimer;
        var cache = {};

        if (!searchInput) return;

        function hideResults() { resultsDiv.classList.add('hidden'); }

        function selectColonia(id, nombre) {
            hiddenInput.value = id;
            searchInput.value = nombre;
            selectedP.classList.remove('hidden');
            cache = {};
            hideResults();
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            var q = this.value.trim();
            if (q.length < 2) {
                if (q.length === 0) { cache = {}; }
                hideResults();
                return;
            }
            debounceTimer = setTimeout(function () {
                if (cache[q]) { renderResults(cache[q]); return; }
                var url = '{{ route("predios.colonia-search") }}' + '?q=' + encodeURIComponent(q);
                fetch(url)
                    .then(function (r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        cache = {};
                        cache[q] = data;
                        renderResults(data);
                    })
                    .catch(function (err) {
                        console.error('Colonia search error:', err);
                        resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-red-500">Error: ' + err.message + '</div>';
                        resultsDiv.classList.remove('hidden');
                    });
            }, 400);
        });

        function renderResults(data) {
            resultsDiv.innerHTML = '';
            if (data.length === 0) {
                resultsDiv.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Sin resultados</div>';
            } else {
                data.forEach(function (c) {
                    var div = document.createElement('div');
                    div.className = 'px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-600 last:border-b-0';
                    div.textContent = c.COLONIA;
                    div.dataset.id = c.id_colonia;
                    div.dataset.nombre = c.COLONIA;
                    div.addEventListener('click', function () { selectColonia(this.dataset.id, this.dataset.nombre); });
                    resultsDiv.appendChild(div);
                });
            }
            resultsDiv.classList.remove('hidden');
        }

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#colonia_search') && !e.target.closest('#colonia_results')) hideResults();
        });

        searchInput.addEventListener('blur', function () { setTimeout(hideResults, 200); });
    })();

    (function() {
        var form = document.getElementById('form-crear-clave-predial');
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            function intVal(id) { var v = document.getElementById(id).value; return v ? parseInt(v, 10) : null; }

            var data = {
                clave_predial_completa: document.getElementById('cp_modal_completa').value,
                id_tipo_predio: intVal('cp_modal_tipo_predio'),
                id_poblacion: intVal('cp_modal_poblacion'),
                id_seccion: intVal('cp_modal_seccion'),
                id_manzana: intVal('cp_modal_manzana'),
                id_lote: intVal('cp_modal_lote'),
                subLote: document.getElementById('cp_modal_subLote').value || null,
                Parcela: document.getElementById('cp_modal_Parcela').value || null,
                prefijo: document.getElementById('cp_modal_prefijo').value || null,
                manzana_rustico: document.getElementById('cp_modal_manzana_rustico').value || null,
                lote_rustico: document.getElementById('cp_modal_lote_rustico').value || null,
            };

            var submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Guardando...';

            fetch('{{ route("clave-predial.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(function(r) {
                if (!r.ok) return r.json().then(function(err) {
                    var msg = err.message || 'Error al guardar';
                    if (err.errors) {
                        var details = Object.values(err.errors).flat().join('; ');
                        if (details) msg += ': ' + details;
                    }
                    throw new Error(msg);
                });
                return r.json();
            })
            .then(function(clave) {
                var searchInput = document.getElementById('clave_predial_search');
                var selectFn = window.selectClavePredial;
                if (selectFn) {
                    selectFn(clave.id_clave_predial, clave);
                } else {
                    document.getElementById('id_clave_predial').value = clave.id_clave_predial;
                    searchInput.value = clave.clave_predial_completa;
                }
                form.reset();
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'crear-clave-predial' }));
            })
            .catch(function(err) {
                alert('Error: ' + err.message);
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Guardar';
            });
        });
    })();

    (function() {
        var container = document.getElementById('medidas-container');
        var template = document.getElementById('medida-template');
        var btnAgregar = document.getElementById('agregar-medida');
        var empty = document.getElementById('medidas-empty');
        var index = 0;

        if (!container || !template) return;

        function actualizarEmpty() {
            var rows = container.querySelectorAll('.medida-row');
            empty.classList.toggle('hidden', rows.length > 0);
        }

        function agregarFila(data) {
            var html = template.innerHTML.replace(/__INDEX__/g, index);
            var div = document.createElement('div');
            div.innerHTML = html;
            var row = div.firstElementChild;
            if (data) {
                row.querySelector('[name$="[id_orientacion]"]').value = data.id_orientacion || '';
                row.querySelector('[name$="[medida_en_metros]"]').value = data.medida_en_metros || '';
                row.querySelector('[name$="[colinda_con]"]').value = data.colinda_con || '';
            }
            row.querySelector('.eliminar-medida').addEventListener('click', function() {
                row.remove();
                actualizarEmpty();
            });
            container.appendChild(row);
            index++;
            actualizarEmpty();
        }

        btnAgregar.addEventListener('click', function() { agregarFila(); });
    })();
    </script>
</x-app-layout>
