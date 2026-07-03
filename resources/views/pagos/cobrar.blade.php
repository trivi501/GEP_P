<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pago Predial Urbano') }}
        </h2>
    </x-slot>

    <div class="py-12 w-full">
        <div class="">
            @if (session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-visible shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Cobro - Caja {{ $cajaAbierta->caja->nombre ?? '' }}</h3>
                        <a href="{{ route('pagos.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">&larr; Volver</a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-1 space-y-4">
                            <div class="relative">
                                <label for="search" class="block text-sm font-medium mb-1">Buscar por Cuenta o Clave Catastral</label>
                                <input type="text" id="search" autocomplete="off" placeholder="Escribe para buscar..." class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                <div id="search-results" class="mt-1 hidden absolute z-50 left-0 right-0 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded shadow-lg max-h-60 overflow-y-auto"></div>
                            </div>

                            <div id="predio-info" class="hidden p-4 bg-gray-50 dark:bg-gray-700 rounded-lg space-y-2 text-sm">
                                <p><strong>Predial Cuenta:</strong> <span id="info-cuenta"></span></p>
                                <p><strong>Clave Catastral:</strong> <span id="info-clave"></span></p>
                                <p><strong>Contribuyente:</strong> <span id="info-contribuyente"></span></p>
                                <p><strong>Domicilio:</strong> <span id="info-domicilio"></span></p>
                                <p><strong>Última Fecha de Pago:</strong> <span id="info-ultimo-pago"></span></p>
                            </div>
                        </div>

                        <div class="lg:col-span-1">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <h4 class="text-sm font-semibold mb-3">Descuentos</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600" value="jubilado">
                                        Jubilado
                                    </label>
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600" value="pensionado">
                                        Pensionado
                                    </label>
                                    <label class="flex items-center gap-2 text-sm">
                                        <input type="checkbox" class="rounded border-gray-300 dark:border-gray-600" value="adulto_mayor">
                                        Adulto Mayor
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="lg:col-span-1">
                            <div id="calculo-container" class="hidden">
                                <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <h4 class="text-sm font-semibold mb-3">Cálculo del Predial</h4>
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-gray-300 dark:border-gray-600">
                                                <th class="text-left py-1 pr-2">Concepto</th>
                                                <th class="text-right py-1 pl-2">Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody id="conceptos-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="pago-section" class="hidden mt-6 border-t border-gray-300 dark:border-gray-600 pt-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div>
                                <label class="block text-sm font-medium mb-1">Total</label>
                                <p id="total-display" class="text-2xl font-bold text-green-600 dark:text-green-400">$0.00</p>
                            </div>
                            <div>
                                <label for="forma_pago" class="block text-sm font-medium mb-1">Forma de Pago</label>
                                <select id="forma_pago" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">Seleccionar...</option>
                                    @foreach ($formasPago as $fp)
                                        <option value="{{ $fp->id }}">{{ $fp->Descripción }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="pago_con" class="block text-sm font-medium mb-1">Pago con</label>
                                <input type="text" id="pago_con" placeholder="Monto recibido" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            </div>
                            <div>
                                <button id="btn-pagar" disabled class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                                    Pagar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let selectedPredioId = null;
        let currentConceptos = [];
        let currentAnios = [];
        let selectedAnios = [];
        let contribuyenteData = {};

        const searchInput = document.getElementById('search');
        const resultsContainer = document.getElementById('search-results');
        const predioInfo = document.getElementById('predio-info');
        const calculoContainer = document.getElementById('calculo-container');
        const pagoSection = document.getElementById('pago-section');
        const conceptosBody = document.getElementById('conceptos-body');
        const totalDisplay = document.getElementById('total-display');
        const btnPagar = document.getElementById('btn-pagar');
        const formaPago = document.getElementById('forma_pago');
        const pagoCon = document.getElementById('pago_con');

        let searchTimeout;

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimeout);
            const q = this.value.trim();

            if (q.length < 2) {
                resultsContainer.classList.add('hidden');
                resultsContainer.innerHTML = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                const url = `{{ route('pagos.search-predio') }}?q=${encodeURIComponent(q)}`;
                fetch(url)
                    .then(r => {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(data => {
                        resultsContainer.innerHTML = '';
                        if (data.length === 0) {
                            resultsContainer.innerHTML = '<div class="px-3 py-2 text-sm text-gray-500">Sin resultados</div>';
                        } else {
                            data.forEach(p => {
                                const div = document.createElement('div');
                                div.className = 'px-3 py-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 text-sm border-b border-gray-200 dark:border-gray-600';
                                div.innerHTML = `<strong>${p.clave_catastral}</strong> — ${p.contribuyente} <span class="text-gray-500">(${p.cuenta})</span>`;
                                div.addEventListener('click', () => selectPredio(p));
                                resultsContainer.appendChild(div);
                            });
                        }
                        resultsContainer.classList.remove('hidden');
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        resultsContainer.innerHTML = '<div class="px-3 py-2 text-sm text-red-500">Error al buscar</div>';
                        resultsContainer.classList.remove('hidden');
                    });
            }, 100);
        });

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#search') && !e.target.closest('#search-results')) {
                resultsContainer.classList.add('hidden');
            }
        });

        function recalcTotal() {
            let total = 0;
            currentConceptos = [];
            currentAnios.forEach(anio => {
                if (selectedAnios.includes(anio.anho)) {
                    anio.conceptos.forEach(c => currentConceptos.push(c));
                    total += parseFloat(anio.total || 0);
                }
            });
            totalDisplay.textContent = '$' + total.toFixed(2);
        }

        function renderAnios() {
            conceptosBody.innerHTML = '';
            if (!currentAnios.length) return;

            const headerTr = document.createElement('tr');
            headerTr.className = 'border-b border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-600';
            headerTr.innerHTML = `
                <td class="py-1 pr-2 text-xs font-bold">Año</td>
                <td class="py-1 px-2 text-right text-xs font-bold">Subtotal</td>
                <td class="py-1 px-2 text-right text-xs font-bold">Recargos</td>
                <td class="py-1 px-2 text-right text-xs font-bold">Actualiz.</td>
                <td class="py-1 px-2 text-right text-xs font-bold">Multa</td>
                <td class="py-1 px-2 text-right text-xs font-bold">Ejecución</td>
                <td class="py-1 px-2 text-right text-xs font-bold">Total</td>
                <td class="py-1 pl-2 text-center text-xs font-bold">Pagar</td>
            `;
            conceptosBody.appendChild(headerTr);

            currentAnios.forEach(anio => {
                const tr = document.createElement('tr');
                tr.className = 'border-b border-gray-200 dark:border-gray-600';

                const c = {};
                anio.conceptos.forEach(x => {
                    if (x.concepto.includes('Predial') || x.concepto.includes('Rústico')) c.subtotal = x.monto;
                    else if (x.concepto.includes('Aseo')) c.aseo = x.monto;
                    else if (x.concepto.includes('Recargos')) c.recargos = x.monto;
                    else if (x.concepto.includes('Actualización')) c.actualizacion = x.monto;
                    else if (x.concepto.includes('Multa')) c.multa = x.monto;
                    else if (x.concepto.includes('Ejecución') || x.concepto.includes('Gastos')) c.ejecucion = x.monto;
                });

                const checked = selectedAnios.includes(anio.anho) ? 'checked' : '';

                tr.innerHTML = `
                    <td class="py-1 pr-2 font-medium">${anio.anho}</td>
                    <td class="py-1 px-2 text-right">$${parseFloat(c.subtotal || 0).toFixed(2)}</td>
                    <td class="py-1 px-2 text-right">$${parseFloat(c.recargos || 0).toFixed(2)}</td>
                    <td class="py-1 px-2 text-right">$${parseFloat(c.actualizacion || 0).toFixed(2)}</td>
                    <td class="py-1 px-2 text-right">$${parseFloat(c.multa || 0).toFixed(2)}</td>
                    <td class="py-1 px-2 text-right">$${parseFloat(c.ejecucion || 0).toFixed(2)}</td>
                    <td class="py-1 px-2 text-right font-bold">$${parseFloat(anio.total || 0).toFixed(2)}</td>
                    <td class="py-1 pl-2 text-center">
                        <input type="checkbox" class="anio-check rounded border-gray-300 dark:border-gray-600" data-anho="${anio.anho}" ${checked}>
                    </td>
                `;
                conceptosBody.appendChild(tr);
            });

            document.querySelectorAll('.anio-check').forEach(cb => {
                cb.addEventListener('change', function () {
                    const anho = parseInt(this.dataset.anho);
                    if (this.checked) {
                        if (!selectedAnios.includes(anho)) selectedAnios.push(anho);
                    } else {
                        selectedAnios = selectedAnios.filter(a => a !== anho);
                    }
                    selectedAnios.sort((a, b) => a - b);
                    recalcTotal();
                });
            });

            recalcTotal();
        }

        function selectPredio(p) {
            selectedPredioId = p.id;
            searchInput.value = p.clave_catastral + ' - ' + p.contribuyente;
            resultsContainer.classList.add('hidden');

            document.getElementById('info-cuenta').textContent = p.cuenta || '—';
            document.getElementById('info-clave').textContent = p.clave_catastral || '—';
            document.getElementById('info-contribuyente').textContent = p.contribuyente || '—';
            document.getElementById('info-domicilio').textContent = p.domicilio || '—';
            document.getElementById('info-ultimo-pago').textContent = p.ultimo_pago || '—';
            predioInfo.classList.remove('hidden');

            calculoContainer.classList.add('hidden');
            pagoSection.classList.add('hidden');
            btnPagar.disabled = true;

            fetch(`{{ route('pagos.get-calculo') }}?id=${p.id}`)
                .then(r => {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.json();
                })
                .then(data => {
                    if (data.predio) {
                        currentConceptos = data.conceptos.filter(c => c.concepto !== 'TOTAL');
                        currentAnios = data.anios || [];
                        selectedAnios = currentAnios.map(a => a.anho);
                        contribuyenteData = {
                            id_contribuyente: data.predio.id_contribuyente,
                            rfc: data.predio.rfc || '—',
                            nombre: data.predio.contribuyente_nombre || data.predio.contribuyente || '—',
                            es_rustico: data.predio.es_rustico || false,
                        };

                        renderAnios();

                        calculoContainer.classList.remove('hidden');
                        pagoSection.classList.remove('hidden');
                        btnPagar.disabled = false;
                    }
                })
                .catch(err => console.error('Calculo error:', err));
        }

        btnPagar.addEventListener('click', function () {
            if (!selectedPredioId || currentConceptos.length === 0) {
                alert('Selecciona un predio y espera el cálculo.');
                return;
            }

            if (!formaPago.value) {
                alert('Selecciona una forma de pago.');
                formaPago.focus();
                return;
            }

            const monto = currentConceptos.reduce((sum, c) => sum + parseFloat(c.monto || 0), 0);
            const descuento = 0;

            this.disabled = true;
            this.textContent = 'Procesando...';

            const payload = {
                id_predio: selectedPredioId,
                id_contribuyente: contribuyenteData.id_contribuyente,
                monto: monto,
                descuento: descuento,
                nombre: contribuyenteData.nombre,
                rfc: contribuyenteData.rfc,
                descripcion: 'Pago predial ' + (contribuyenteData.es_rustico ? 'rústico' : 'urbano'),
                forma_pago: formaPago.value,
                tipo_pago: contribuyenteData.es_rustico ? 'predial_rustico' : 'predial_urbano',
                conceptos: currentConceptos,
                anios_pagados: selectedAnios,
            };

            fetch('{{ route('pagos.guardar') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(payload),
            })
                .then(r => {
                    if (!r.ok) return r.json().then(e => Promise.reject(e));
                    return r.json();
                })
                .then(result => {
                    if (result.success) {
                        const reciboUrl = '{{ route('pagos.recibo', ['id' => '__ID__']) }}'.replace('__ID__', result.pago_id);
                        window.open(reciboUrl, '_blank');
                        location.reload();
                    } else {
                        alert('Error: ' + (result.error || 'Error desconocido'));
                    }
                })
                .catch(err => {
                    console.error('Pago error:', err);
                    alert('Error al registrar el pago: ' + (err.error || 'Error de conexión'));
                })
                .finally(() => {
                    btnPagar.disabled = false;
                    btnPagar.textContent = 'Pagar';
                });
        });
    </script>
    @endpush
</x-app-layout>
