<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Nuevo Cobro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Buscador de Contribuyente --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar por Contribuyente</label>
                            <div class="relative">
                                <input type="text" id="search-contribuyente" placeholder="Nombre o cuenta..." class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <div id="search-results" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Buscar por Clave Catastral</label>
                            <div class="relative">
                                <input type="text" id="search-clave-catastral" placeholder="Clave catastral..." class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <div id="search-clave-results" class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Datos del Contribuyente --}}
                    <div id="contribuyente-info" class="hidden mb-6 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg">
                        <h4 class="font-medium text-lg mb-2">Datos del Contribuyente</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <span class="text-xs text-gray-500 block">Nombre</span>
                                <span class="font-medium" id="info-nombre"></span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Cuenta</span>
                                <span class="font-medium" id="info-cuenta"></span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">RFC</span>
                                <span class="font-medium" id="info-rfc"></span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 block">Predios</span>
                                <span class="font-medium" id="info-predios"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Tabs --}}
                    <div id="predios-section" class="hidden">
                        <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                            <nav class="flex space-x-4" role="tablist">
                                <button type="button" class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400" data-tab="urbano" role="tab">
                                    Predial Urbano
                                </button>
                                <button type="button" class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" data-tab="rustico" role="tab">
                                    Predial Rústico
                                </button>
                            </nav>
                        </div>

                        {{-- Tab Urbano --}}
                        <div id="tab-urbano" class="tab-content">
                            <div id="predios-urbano" class="space-y-2 mb-4"></div>
                            <div id="calculos-urbano" class="hidden">
                                <h4 class="font-medium mb-2">Cálculo Predial Urbano</h4>
                                <div class="overflow-x-auto mb-4">
                                    <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700" id="tabla-urbano">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-3 py-2 text-left">Año</th>
                                                <th class="px-3 py-2 text-right">Terreno</th>
                                                <th class="px-3 py-2 text-right">Const.</th>
                                                <th class="px-3 py-2 text-right">Baldío</th>
                                                <th class="px-3 py-2 text-right">C.M.</th>
                                                <th class="px-3 py-2 text-right">Entero</th>
                                                <th class="px-3 py-2 text-right">A.P.</th>
                                                <th class="px-3 py-2 text-right">Recargos</th>
                                                <th class="px-3 py-2 text-right">Actual.</th>
                                                <th class="px-3 py-2 text-right">Desc.</th>
                                                <th class="px-3 py-2 text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-urbano-body"></tbody>
                                        <tfoot id="tabla-urbano-foot"></tfoot>
                                    </table>
                                </div>
                                <button type="button" class="add-urbano-btn inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-500" data-total="0">
                                    + Agregar al cobro
                                </button>
                            </div>
                        </div>

                        {{-- Tab Rústico --}}
                        <div id="tab-rustico" class="tab-content hidden">
                            <div id="predios-rustico" class="space-y-2 mb-4"></div>
                            <div id="calculos-rustico" class="hidden">
                                <h4 class="font-medium mb-2">Cálculo Predial Rústico</h4>
                                <div class="overflow-x-auto mb-4">
                                    <table class="min-w-full text-sm border border-gray-200 dark:border-gray-700" id="tabla-rustico">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-3 py-2 text-left">Año</th>
                                                <th class="px-3 py-2 text-right">UMA</th>
                                                <th class="px-3 py-2 text-right">Ha</th>
                                                <th class="px-3 py-2 text-right">Subtotal</th>
                                                <th class="px-3 py-2 text-right">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tabla-rustico-body"></tbody>
                                        <tfoot id="tabla-rustico-foot"></tfoot>
                                    </table>
                                </div>
                                <button type="button" class="add-rustico-btn inline-flex items-center px-3 py-1 bg-emerald-600 text-white text-xs rounded hover:bg-emerald-500" data-total="0">
                                    + Agregar al cobro
                                </button>
                            </div>
                        </div>

                        {{-- Resumen de cobro --}}
                        <form method="POST" action="{{ route('caja.store') }}" id="pago-form" class="border-t dark:border-gray-700 pt-6 mt-4">
                            @csrf
                            <input type="hidden" name="id_contribuyente" id="id_contribuyente" value="">

                            <h4 class="font-medium text-lg mb-3">Resumen del Cobro</h4>
                            <div id="items-cobro" class="space-y-2 mb-4"></div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtotal</label>
                                    <input type="number" step="0.01" min="0" name="sub_total_pago" id="sub_total_pago" value="0" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100 dark:bg-gray-600" readonly>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Descuento</label>
                                    <input type="number" step="0.01" min="0" name="total_descuento" id="total_descuento" value="0" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total</label>
                                    <input type="number" step="0.01" min="0" name="total_pago" id="total_pago" value="0" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 bg-gray-100 dark:bg-gray-600" readonly>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Formas de Pago</label>
                                <div id="formas-pago-container" class="space-y-2">
                                    <div class="forma-pago-row flex items-center gap-2">
                                        <select name="formas_pago[id_f4_c_formapago][]" class="w-1/2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="">Seleccionar...</option>
                                            @foreach($formasPago as $fp)
                                                <option value="{{ $fp->id }}">{{ $fp->Descripción }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" min="0" name="formas_pago[monto][]" placeholder="Monto" class="w-1/3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                        <button type="button" class="remove-forma-pago text-red-500 hover:text-red-700 text-sm font-medium">Eliminar</button>
                                    </div>
                                </div>
                                <button type="button" id="add-forma-pago" class="mt-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                                    + Agregar otra forma de pago
                                </button>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notas</label>
                                <textarea name="notas" rows="2" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('caja.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 dark:bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Cancelar
                                </a>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Registrar Pago
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let searchTimeout;
        let currentContribuyenteId = null;
        let itemsCobro = [];

        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => {
                    b.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                    b.classList.add('border-transparent', 'text-gray-500');
                });
                this.classList.remove('border-transparent', 'text-gray-500');
                this.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');

                document.querySelectorAll('.tab-content').forEach(tc => tc.classList.add('hidden'));
                document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
            });
        });

        // Search contribuyente
        document.getElementById('search-contribuyente').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) {
                document.getElementById('search-results').classList.add('hidden');
                return;
            }
            searchTimeout = setTimeout(() => {
                fetch(`{{ route("caja.search-contribuyente") }}?q=${encodeURIComponent(q)}`)
                    .then(r => r.json())
                    .then(data => {
                        const results = document.getElementById('search-results');
                        results.innerHTML = '';
                        results.classList.remove('hidden');
                        if (data.length === 0) {
                            results.innerHTML = '<div class="px-4 py-2 text-gray-500">Sin resultados</div>';
                            return;
                        }
                        data.forEach(c => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 cursor-pointer hover:bg-indigo-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600';
                            div.innerHTML = `<strong>${c.nombre_completo}</strong> <span class="text-gray-500 text-sm">${c.cuenta || ''}</span>`;
                            div.dataset.id = c.id_contribuyente;
                            div.dataset.nombre = c.nombre_completo;
                            div.dataset.cuenta = c.cuenta;
                            div.addEventListener('click', function() {
                                selectContribuyente(this.dataset.id, this.dataset.nombre, this.dataset.cuenta);
                            });
                            results.appendChild(div);
                        });
                    });
            }, 300);
        });

        // Search by clave catastral
        document.getElementById('search-clave-catastral').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const q = this.value.trim();
            if (q.length < 2) {
                document.getElementById('search-clave-results').classList.add('hidden');
                return;
            }
            searchTimeout = setTimeout(() => {
                fetch(`{{ route("caja.search-clave-catastral") }}?q=${encodeURIComponent(q)}`)
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        const results = document.getElementById('search-clave-results');
                        results.innerHTML = '';
                        results.classList.remove('hidden');
                        if (data.length === 0) {
                            results.innerHTML = '<div class="px-4 py-2 text-gray-500">Sin resultados</div>';
                            return;
                        }
                        data.forEach(p => {
                            const div = document.createElement('div');
                            div.className = 'px-4 py-2 cursor-pointer hover:bg-emerald-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600';
                            div.innerHTML = `<strong>${p.clave_predial}</strong><br><span class="text-xs text-gray-500">${p.contribuyente} (${p.cuenta})</span>`;
                            div.dataset.id = p.id_contribuyente;
                            div.dataset.nombre = p.contribuyente;
                            div.dataset.cuenta = p.cuenta;
                            div.addEventListener('click', function() {
                                selectContribuyente(this.dataset.id, this.dataset.nombre, this.dataset.cuenta);
                            });
                            results.appendChild(div);
                        });
                    })
                    .catch(err => {
                        console.error('Error al buscar clave catastral:', err);
                    });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            const results = document.getElementById('search-results');
            const claveResults = document.getElementById('search-clave-results');
            if (!results.contains(e.target) && e.target.id !== 'search-contribuyente') {
                results.classList.add('hidden');
            }
            if (!claveResults.contains(e.target) && e.target.id !== 'search-clave-catastral') {
                claveResults.classList.add('hidden');
            }
        });

        function selectContribuyente(id, nombre, cuenta) {
            currentContribuyenteId = id;
            document.getElementById('id_contribuyente').value = id;
            document.getElementById('search-contribuyente').value = nombre;
            document.getElementById('search-results').classList.add('hidden');
            document.getElementById('info-nombre').textContent = nombre;
            document.getElementById('info-cuenta').textContent = cuenta || '—';
            resetCalculos();

            fetch(`{{ route("caja.get-contribuyente") }}?id=${id}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('info-rfc').textContent = data.rfc || '—';
                    document.getElementById('info-predios').textContent = data.predios?.length || 0;
                    document.getElementById('contribuyente-info').classList.remove('hidden');

                    const urbanos = (data.predios || []).filter(p =>
                        p.tipo_predio?.Tipo_predio && (p.tipo_predio.Tipo_predio.includes('Urbano') || p.tipo_predio.Tipo_predio.includes('Urbana'))
                    );
                    const rusticos = (data.predios || []).filter(p =>
                        p.tipo_predio?.Tipo_predio && (p.tipo_predio.Tipo_predio.includes('Rústico') || p.tipo_predio.Tipo_predio.includes('Rustico') || p.tipo_predio.Tipo_predio.includes('MINA'))
                    );

                    renderPredios('predios-urbano', urbanos, 'urbano');
                    renderPredios('predios-rustico', rusticos, 'rustico');

                    document.getElementById('predios-section').classList.remove('hidden');
                });
        }

        function renderPredios(containerId, predios, tipo) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            if (predios.length === 0) {
                container.innerHTML = '<p class="text-sm text-gray-500">Sin predios de este tipo.</p>';
                return;
            }
            predios.forEach(p => {
                const div = document.createElement('div');
                div.className = 'p-3 border border-gray-200 dark:border-gray-600 rounded-lg flex justify-between items-center';
                div.innerHTML = `
                    <div>
                        <strong>${p.clave_predial?.clave_predial_completa || '—'}</strong>
                        <span class="text-sm text-gray-500 ml-2">${p.tipo_predio?.Tipo_predio || '—'}</span>
                    </div>
                    <button type="button" class="calc-btn px-3 py-1 text-xs rounded text-white ${tipo === 'urbano' ? 'bg-indigo-600 hover:bg-indigo-500' : 'bg-emerald-600 hover:bg-emerald-500'}"
                        data-id="${p.id_predio}" data-tipo="${tipo}">Calcular</button>
                `;
                container.appendChild(div);
            });

            container.querySelectorAll('.calc-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const idPredio = this.dataset.id;
                    const tipoCalc = this.dataset.tipo;
                    const url = tipoCalc === 'urbano' ? '{{ route("caja.calcular-urbano") }}' : '{{ route("caja.calcular-rustico") }}';
                    fetch(`${url}?id_predio=${idPredio}`)
                        .then(r => {
                            if (!r.ok) throw new Error('HTTP ' + r.status);
                            return r.json();
                        })
                        .then(data => {
                            if (tipoCalc === 'urbano') mostrarCalculosUrbano(data.calculos, idPredio);
                            else mostrarCalculosRustico(data.calculos, idPredio);
                        })
                        .catch(err => {
                            console.error('Error al calcular:', err);
                            alert('Error al calcular: ' + err.message);
                        });
                });
            });
        }

        function mostrarCalculosUrbano(calculos, idPredio) {
            const body = document.getElementById('tabla-urbano-body');
            const foot = document.getElementById('tabla-urbano-foot');
            body.innerHTML = '';
            let totalGeneral = 0;

            calculos.forEach(c => {
                totalGeneral += c.total;
                body.innerHTML += `<tr class="border-b dark:border-gray-700">
                    <td class="px-3 py-1">${c.anho}</td>
                    <td class="px-3 py-1 text-right">$${c.imp_terreno.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right">$${c.imp_construccion.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right">$${c.baldio ? c.imp_construccion.toFixed(2) : '0.00'}</td>
                    <td class="px-3 py-1 text-right">$${c.cm.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right">$${c.entero.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right">$${c.aseo_publico.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right">$${c.recargos.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right">$${c.actualizacion.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right">$${c.descuento.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right font-semibold">$${c.total.toFixed(2)}</td>
                </tr>`;
            });

            foot.innerHTML = `<tr class="font-bold bg-gray-100 dark:bg-gray-700">
                <td class="px-3 py-1">Total</td>
                <td class="px-3 py-1 text-right">$${calculos.reduce((s,c) => s + c.imp_terreno, 0).toFixed(2)}</td>
                <td class="px-3 py-1 text-right">$${calculos.reduce((s,c) => s + c.imp_construccion, 0).toFixed(2)}</td>
                <td class="px-3 py-1 text-right">—</td>
                <td class="px-3 py-1 text-right">$${calculos.reduce((s,c) => s + c.cm, 0).toFixed(2)}</td>
                <td class="px-3 py-1 text-right">$${calculos.reduce((s,c) => s + c.entero, 0).toFixed(2)}</td>
                <td class="px-3 py-1 text-right">$${calculos.reduce((s,c) => s + c.aseo_publico, 0).toFixed(2)}</td>
                <td class="px-3 py-1 text-right">$${calculos.reduce((s,c) => s + c.recargos, 0).toFixed(2)}</td>
                <td class="px-3 py-1 text-right">$${calculos.reduce((s,c) => s + c.actualizacion, 0).toFixed(2)}</td>
                <td class="px-3 py-1 text-right">$${calculos.reduce((s,c) => s + c.descuento, 0).toFixed(2)}</td>
                <td class="px-3 py-1 text-right">$${totalGeneral.toFixed(2)}</td>
            </tr>`;

            document.getElementById('calculos-urbano').classList.remove('hidden');
            const btn = document.querySelector('.add-urbano-btn');
            btn.dataset.total = totalGeneral;
            btn.dataset.idPredio = idPredio;
        }

        function mostrarCalculosRustico(calculos, idPredio) {
            const body = document.getElementById('tabla-rustico-body');
            const foot = document.getElementById('tabla-rustico-foot');
            body.innerHTML = '';
            let totalGeneral = 0;

            calculos.forEach(c => {
                totalGeneral += c.total;
                body.innerHTML += `<tr class="border-b dark:border-gray-700">
                    <td class="px-3 py-1">${c.anho}</td>
                    <td class="px-3 py-1 text-right">$${c.uma.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right">${c.hectareas.toFixed(4)}</td>
                    <td class="px-3 py-1 text-right">$${c.subtotal.toFixed(2)}</td>
                    <td class="px-3 py-1 text-right font-semibold">$${c.total.toFixed(2)}</td>
                </tr>`;
            });

            foot.innerHTML = `<tr class="font-bold bg-gray-100 dark:bg-gray-700">
                <td class="px-3 py-1">Total</td>
                <td class="px-3 py-1"></td>
                <td class="px-3 py-1"></td>
                <td class="px-3 py-1 text-right">$${totalGeneral.toFixed(2)}</td>
                <td class="px-3 py-1 text-right">$${totalGeneral.toFixed(2)}</td>
            </tr>`;

            document.getElementById('calculos-rustico').classList.remove('hidden');
            const btn = document.querySelector('.add-rustico-btn');
            btn.dataset.total = totalGeneral;
            btn.dataset.idPredio = idPredio;
        }

        // Add items to cobro (event delegation on the predios-section)
        document.getElementById('predios-section').addEventListener('click', function(e) {
            const btn = e.target.closest('.add-urbano-btn, .add-rustico-btn');
            if (!btn) return;
            const total = parseFloat(btn.dataset.total) || 0;
            if (total <= 0) return;
            const esUrbano = btn.classList.contains('add-urbano-btn');
            itemsCobro.push({
                concepto: (esUrbano ? 'Predial Urbano' : 'Predial Rústico') + ' - ' + (btn.dataset.idPredio || ''),
                monto: total
            });
            renderItemsCobro();
        });

        function renderItemsCobro() {
            const container = document.getElementById('items-cobro');
            container.innerHTML = '';
            let subtotal = 0;

            itemsCobro.forEach((item, i) => {
                subtotal += item.monto;
                container.innerHTML += `
                    <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-900 rounded">
                        <span class="text-sm">${item.concepto}</span>
                        <span class="text-sm font-semibold">$${item.monto.toFixed(2)}</span>
                        <button type="button" class="text-red-500 text-xs" onclick="removeItem(${i})">Eliminar</button>
                    </div>
                `;
            });

            document.getElementById('sub_total_pago').value = subtotal.toFixed(2);
            actualizarTotal();
        }

        function removeItem(index) {
            itemsCobro.splice(index, 1);
            renderItemsCobro();
        }

        document.getElementById('total_descuento').addEventListener('input', actualizarTotal);

        // Dynamic payment methods
        document.getElementById('add-forma-pago').addEventListener('click', function() {
            const container = document.getElementById('formas-pago-container');
            const row = container.querySelector('.forma-pago-row').cloneNode(true);
            row.querySelector('select').value = '';
            row.querySelector('input').value = '';
            container.appendChild(row);
        });

        document.getElementById('formas-pago-container').addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-forma-pago')) {
                const rows = this.querySelectorAll('.forma-pago-row');
                if (rows.length > 1) {
                    e.target.closest('.forma-pago-row').remove();
                }
            }
        });

        document.getElementById('pago-form').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.forma-pago-row');
            let sumaMontos = 0;
            let valido = true;
            rows.forEach(row => {
                const select = row.querySelector('select');
                const monto = parseFloat(row.querySelector('input').value) || 0;
                if (select.value && monto > 0) {
                    sumaMontos += monto;
                } else if (select.value || monto > 0) {
                    valido = false;
                }
            });
            const total = parseFloat(document.getElementById('total_pago').value) || 0;
            if (!valido) {
                alert('Cada forma de pago debe tener un método y un monto.');
                e.preventDefault();
                return;
            }
            if (rows.length > 0 && sumaMontos > 0 && Math.abs(sumaMontos - total) > 0.01) {
                alert('La suma de los montos de las formas de pago ($' + sumaMontos.toFixed(2) + ') debe ser igual al total ($' + total.toFixed(2) + ').');
                e.preventDefault();
                return;
            }
        });

        function actualizarTotal() {
            const sub = parseFloat(document.getElementById('sub_total_pago').value) || 0;
            const desc = parseFloat(document.getElementById('total_descuento').value) || 0;
            document.getElementById('total_pago').value = Math.max(0, sub - desc).toFixed(2);
        }

        function resetCalculos() {
            document.getElementById('calculos-urbano').classList.add('hidden');
            document.getElementById('calculos-rustico').classList.add('hidden');
            document.getElementById('tabla-urbano-body').innerHTML = '';
            document.getElementById('tabla-urbano-foot').innerHTML = '';
            document.getElementById('tabla-rustico-body').innerHTML = '';
            document.getElementById('tabla-rustico-foot').innerHTML = '';
            document.querySelector('.add-urbano-btn').dataset.total = '0';
            document.querySelector('.add-rustico-btn').dataset.total = '0';
            document.querySelector('.add-urbano-btn').dataset.idPredio = '';
            document.querySelector('.add-rustico-btn').dataset.idPredio = '';
        }
    </script>
    @endpush
</x-app-layout>
