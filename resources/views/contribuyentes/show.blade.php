<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detalle del Contribuyente') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">{{ $contribuyente->nombre_completo }}</h3>
                        <div class="flex gap-2">
                            <a href="{{ route('contribuyentes.edit', $contribuyente) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Editar
                            </a>
                            <a href="{{ route('contribuyentes.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Volver
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Información General</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    <div>
                                        <span class="text-xs text-gray-400 block">Nombre Completo</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->nombre_completo }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 block">Cuenta</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->cuenta }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 block">Tipo Contribuyente</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->tipoContribuyente->area_contribuyente ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 block">RFC</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->rfc ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 block">CURP</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->curp_contribuyente ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 block">Estado</span>
                                        @if ($contribuyente->activo)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Activo</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">Inactivo</span>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 block">Exento</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->exento ? 'Sí' : 'No' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Contacto</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    <div>
                                        <span class="text-xs text-gray-400 block">Teléfono</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->telefono ?? '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 block">Correo Electrónico</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->correo_electronico ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Domicilio</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    @if ($contribuyente->domicilio)
                                        <div>
                                            <span class="text-xs text-gray-400 block">Domicilio Completo</span>
                                            <span class="text-sm font-medium">{{ $contribuyente->domicilio->domicilio_completo ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">País</span>
                                            <span class="text-sm font-medium">{{ $contribuyente->domicilio->pais->nombre_pais ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">Estado</span>
                                            <span class="text-sm font-medium">{{ $contribuyente->domicilio->estado->nombre_estado ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">Municipio</span>
                                            <span class="text-sm font-medium">{{ $contribuyente->domicilio->municipio->nombre_municipio ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">Colonia</span>
                                            <span class="text-sm font-medium">{{ $contribuyente->domicilio->colonia ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">Vialidad</span>
                                            <span class="text-sm font-medium">{{ $contribuyente->domicilio->nombre_vialidad ?? '—' }} {{ $contribuyente->domicilio->num_exterior ?? '' }}{{ $contribuyente->domicilio->num_interior ? ' Int. ' . $contribuyente->domicilio->num_interior : '' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">Código Postal</span>
                                            <span class="text-sm font-medium">{{ $contribuyente->domicilio->codigo_postal ?? '—' }}</span>
                                        </div>
                                        
                                    @else
                                        <p class="text-sm text-gray-400">Sin domicilio registrado.</p>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Datos de Facturación</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    @forelse ($contribuyente->datosFacturacion as $facturacion)
                                        <div>
                                            <span class="text-xs text-gray-400 block">RFC Facturación</span>
                                            <span class="text-sm font-medium">{{ $facturacion->rfc_facturacion ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">Razón Social</span>
                                            <span class="text-sm font-medium">{{ $facturacion->razon_social ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">Correo Facturación</span>
                                            <span class="text-sm font-medium">{{ $facturacion->correo ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-xs text-gray-400 block">CP Domicilio Fiscal</span>
                                            <span class="text-sm font-medium">{{ $facturacion->CP_DomicilioFiscal_contribuyente ?? '—' }}</span>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-400">Sin datos de facturación.</p>
                                    @endforelse
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Información de Registro</h4>
                                <div class="mt-2 bg-gray-50 dark:bg-gray-900 rounded-lg p-4 space-y-3">
                                    <div>
                                        <span class="text-xs text-gray-400 block">Fecha de Alta</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->fecha_alta ? \Carbon\Carbon::parse($contribuyente->fecha_alta)->format('d/m/Y H:i') : '—' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-xs text-gray-400 block">Registrado por</span>
                                        <span class="text-sm font-medium">{{ $contribuyente->id_user_registra ?? '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
