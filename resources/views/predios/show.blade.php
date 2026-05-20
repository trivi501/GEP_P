<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">{{ __('Detalle del Predio') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ $predio->Clave_predial }}</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('calculos-predios.index', ['id_predio' => $predio->id_predio]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Cálculo Predial</a>
                            <a href="{{ route('predios.pdf', $predio) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">PDF</a>
                            <a href="{{ route('predios.edit', $predio) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Editar</a>
                            <a href="{{ route('predios.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Volver</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Datos del Contribuyente</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    <div><span class="text-xs text-gray-400 block">Nombre</span><span class="text-sm font-medium">{{ $predio->contribuyente->nombre_completo ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">RFC</span><span class="text-sm font-medium">{{ $predio->contribuyente->rfc ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">CURP</span><span class="text-sm font-medium">{{ $predio->contribuyente->curp_contribuyente ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Teléfono</span><span class="text-sm font-medium">{{ $predio->contribuyente->telefono ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Correo</span><span class="text-sm font-medium">{{ $predio->contribuyente->correo_electronico ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Tipo</span><span class="text-sm font-medium">{{ $predio->contribuyente->tipoContribuyente->descripcion ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Domicilio</span><span class="text-sm font-medium">{{ $predio->contribuyente->domicilio->domicilio_completo ?? '—' }}</span></div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Información General</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    <div><span class="text-xs text-gray-400 block">Clave Predial</span><span class="text-sm font-medium">{{ $predio->Clave_predial }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Clave Predial (catálogo)</span><span class="text-sm font-medium">{{ $predio->clavePredial->clave_predial_completa ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Contribuyente</span><span class="text-sm font-medium">{{ $predio->contribuyente->nombre_completo ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Cuenta</span><span class="text-sm font-medium">{{ $predio->contribuyente->cuenta ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Tipo Predio</span><span class="text-sm font-medium">{{ $predio->tipoPredio->Tipo_predio ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Régimen de Propiedad</span><span class="text-sm font-medium">{{ $predio->regimenPropiedad->REGIMEN ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Estado de Renta</span><span class="text-sm font-medium">{{ $predio->estadoRenta->DESCRIPCION ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Estatus Cobro</span><span class="text-sm font-medium">{{ $predio->estadoImpuesto->DESCRIPCION ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Título de Propiedad</span><span class="text-sm font-medium">{{ $predio->tituloPropiedad->DESCRIPCION ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Escritura</span><span class="text-sm font-medium">{{ $predio->numero_de_escritura ?? '—' }}</span></div>
                                </div>
                            </div>
                            
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ubicación</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    <div><span class="text-xs text-gray-400 block">Colonia</span><span class="text-sm font-medium">{{ $predio->colonia->COLONIA ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Calle</span><span class="text-sm font-medium">{{ $predio->calle->CALLE ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Ubicación</span><span class="text-sm font-medium">{{ $predio->ubicacion ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Núm. Exterior / Interior</span><span class="text-sm font-medium">{{ $predio->Numero_exterior ?? '—' }}{{ $predio->Numero_interior ? ' Int. ' . $predio->Numero_interior : '' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Código Postal</span><span class="text-sm font-medium">{{ $predio->codigo_postal ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Entre calles</span><span class="text-sm font-medium">{{ $predio->Referencia_entre_calle1 ?? '—' }}{{ $predio->Referncia_entre_calle2 ? ' y ' . $predio->Referncia_entre_calle2 : '' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Coordenadas</span><span class="text-sm font-medium">{{ $predio->latitud ? $predio->latitud . ', ' . $predio->longitud : '—' }}</span></div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Información de Registro</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    <div><span class="text-xs text-gray-400 block">Fecha de Alta</span><span class="text-sm font-medium">{{ $predio->fecha_de_alta ? \Carbon\Carbon::parse($predio->fecha_de_alta)->format('d/m/Y H:i') : '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Usuario</span><span class="text-sm font-medium">{{ $predio->id_usuario ?? '—' }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Último pago</span><span class="text-sm font-medium">{{ $predio->año_ultimo_pago ?? '—' }}</span></div>
                                </div>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valores</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    <div><span class="text-xs text-gray-400 block">Valor Catastral</span><span class="text-sm font-medium">$ {{ number_format($predio->valor_catastral, 2) }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Valor Fiscal</span><span class="text-sm font-medium">$ {{ number_format($predio->valor_fiscal, 2) }}</span></div>
                                    <div><span class="text-xs text-gray-400 block">Superficie</span><span class="text-sm font-medium">{{ number_format($predio->superficie, 4) }} m²</span></div>
                                    <div><span class="text-xs text-gray-400 block">Construcción</span><span class="text-sm font-medium">{{ number_format($predio->construccion, 4) }} m²</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Medidas y Colindancias</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Orientación</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Medida (m)</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Colinda con</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($predio->medidasYColindancias as $m)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-4 py-2">{{ $m->orientacion->descripcion ?? $m->id_orientacion }}</td>
                                            <td class="px-4 py-2">{{ number_format($m->medida_en_metros, 4) }}</td>
                                            <td class="px-4 py-2">{{ $m->colinda_con ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">Sin registros de medidas y colindancias</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @php $u = $predio->datosUrbano; @endphp
                    @if($u)
                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Datos Urbano</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div><span class="text-xs text-gray-400 block">Zona Urbana</span><span class="text-sm font-medium">{{ $u->zonaUrbana->descripcion ?? '—' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Forma del Predio</span><span class="text-sm font-medium">{{ $u->formaPredio->descripcion ?? '—' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Uso del Predio</span><span class="text-sm font-medium">{{ $u->usoPredio->descripcion ?? '—' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Estado Físico</span><span class="text-sm font-medium">{{ $u->estadoFisico->DESCRIPCION ?? '—' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Núm. Pisos</span><span class="text-sm font-medium">{{ $u->numero_de_pisos_construidos ?? '—' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Superficie Terreno</span><span class="text-sm font-medium">{{ number_format($u->superficie_terreno_metros_cuadrados, 2) ?? '—' }} m²</span></div>
                            <div><span class="text-xs text-gray-400 block">Frente</span><span class="text-sm font-medium">{{ number_format($u->Frente_metros, 2) ?? '—' }} m</span></div>
                            <div><span class="text-xs text-gray-400 block">Fondo</span><span class="text-sm font-medium">{{ number_format($u->Fondo_metros, 2) ?? '—' }} m</span></div>
                            <div><span class="text-xs text-gray-400 block">Pavimentación</span><span class="text-sm font-medium">{{ $u->pavimento->DESCRIPCION ?? '—' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Baldío</span><span class="text-sm font-medium">{{ $u->Baldio ? 'Sí' : 'No' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Agua</span><span class="text-sm font-medium">{{ $u->servicio_agua ? 'Sí' : 'No' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Drenaje</span><span class="text-sm font-medium">{{ $u->servicio_drenaje ? 'Sí' : 'No' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Energía Eléctrica</span><span class="text-sm font-medium">{{ $u->servicio_energia_electrica ? 'Sí' : 'No' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Alumbrado</span><span class="text-sm font-medium">{{ $u->servicio_alumbrado ? 'Sí' : 'No' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Banqueta</span><span class="text-sm font-medium">{{ $u->cuenta_con_banqueta ? 'Sí' : 'No' }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Valor Cat. Terreno</span><span class="text-sm font-medium">$ {{ number_format($u->valor_catastral_terreno, 2) }}</span></div>
                            <div><span class="text-xs text-gray-400 block">Valor Cat. Construido</span><span class="text-sm font-medium">$ {{ number_format($u->valor_catastral_construido, 2) }}</span></div>
                        </div>
                    </div>
                    @endif

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Observaciones</h4>
                        @forelse ($predio->observaciones->sortByDesc('fecha_registro') as $obs)
                            <div class="mb-2 p-3 bg-gray-50 dark:bg-gray-900 rounded-lg text-sm">
                                <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($obs->fecha_registro)->format('d/m/Y H:i') }}</span>
                                <p class="mt-1 whitespace-pre-wrap">{{ $obs->observacion }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Sin observaciones registradas</p>
                        @endforelse
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Historial de Modificaciones</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Fecha</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Tipo</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Campo</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Valor Anterior</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Valor Nuevo</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Usuario</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($predio->historico->sortByDesc('fecha_modificacion')->take(50) as $h)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-4 py-2 whitespace-nowrap">{{ \Carbon\Carbon::parse($h->fecha_modificacion)->format('d/m/Y H:i') }}</td>
                                            <td class="px-4 py-2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    @if($h->tipo_operacion == 'CREATE') bg-green-100 text-green-800
                                                    @elseif($h->tipo_operacion == 'UPDATE') bg-blue-100 text-blue-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ $h->tipo_operacion }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-2">{{ $h->campo_modificado }}</td>
                                            <td class="px-4 py-2 whitespace-pre-wrap text-sm">{{ $h->valor_anterior ?? '—' }}</td>
                                            <td class="px-4 py-2 whitespace-pre-wrap text-sm">{{ $h->valor_nuevo ?? '—' }}</td>
                                            <td class="px-4 py-2">{{ $h->usuarioModifica->name ?? $h->id_usuario_modifica ?? '—' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-4 text-center text-gray-500">Sin modificaciones registradas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Anotaciones</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Fecha</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Nota</th>
                                        <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Contestación</th>
                                        <th class="px-4 py-2 text-center font-medium text-gray-500 dark:text-gray-400">Activo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse ($predio->anotaciones as $a)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                            <td class="px-4 py-2 whitespace-nowrap">{{ $a->fecha_registro ? \Carbon\Carbon::parse($a->fecha_registro)->format('d/m/Y') : '—' }}</td>
                                            <td class="px-4 py-2 whitespace-pre-wrap text-sm">{{ $a->nota ?? '—' }}</td>
                                            <td class="px-4 py-2 whitespace-pre-wrap text-sm">{{ $a->contestacion ?? '—' }}</td>
                                            <td class="px-4 py-2 text-center">{{ $a->activo ? 'Sí' : 'No' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Sin anotaciones registradas</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
