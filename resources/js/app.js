import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.data('dataTable', (initialData) => ({
    data: initialData,
    query: '',
    results: [],
    serverTotal: 0,
    loading: false,
    debounceTimer: null,
    sortColumn: 'Clave_predial',
    sortDirection: 'asc',
    currentPage: 1,
    lastPage: 1,

    f_Clave_predial: '',
    f_cuenta: '',
    f_contribuyente: '',
    f_colonia: '',
    f_tipo_predio: '',
    f_estado: '',
    f_ubicacion: '',
    f_año_ultimo_pago: '',
    f_ultimo_bimestre_pago: '',
    f_superficie: '',

    get hasFilters() {
        return this.query.trim() ||
            this.f_Clave_predial || this.f_cuenta || this.f_contribuyente ||
            this.f_colonia || this.f_tipo_predio || this.f_estado ||
            this.f_ubicacion || this.f_año_ultimo_pago ||
            this.f_ultimo_bimestre_pago || this.f_superficie;
    },

    get displayItems() {
        return this.hasFilters ? this.results : this.data;
    },

    get filtered() {
        return [...this.displayItems].sort((a, b) => {
            let aVal = a[this.sortColumn], bVal = b[this.sortColumn];
            if (typeof aVal === 'number' && typeof bVal === 'number') {
                return this.sortDirection === 'asc' ? aVal - bVal : bVal - aVal;
            }
            aVal = String(aVal ?? '').toLowerCase();
            bVal = String(bVal ?? '').toLowerCase();
            return this.sortDirection === 'asc'
                ? aVal.localeCompare(bVal, 'es')
                : bVal.localeCompare(aVal, 'es');
        });
    },

    get total() {
        return this.hasFilters ? this.serverTotal : this.data.length;
    },

    get paginationHtml() {
        const total = this.lastPage;
        const cur = this.currentPage;
        const cls = (p) =>
            p === cur
                ? 'px-3 py-1 rounded text-sm bg-indigo-600 text-white'
                : 'px-3 py-1 rounded text-sm border border-gray-300 dark:border-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
        const btn = (p) => `<button class="${cls(p)}" onclick="window.__goToPage(${p})">${p}</button>`;
        let html = '';
        if (total <= 7) {
            for (let i = 1; i <= total; i++) html += btn(i);
            return html;
        }
        html += btn(1);
        if (cur > 3) html += '<span class="px-2 py-1 text-sm text-gray-500">...</span>';
        const start = Math.max(2, cur - 1);
        const end = Math.min(total - 1, cur + 1);
        for (let i = start; i <= end; i++) html += btn(i);
        if (cur < total - 2) html += '<span class="px-2 py-1 text-sm text-gray-500">...</span>';
        html += btn(total);
        return html;
    },

    sort(col) {
        if (this.sortColumn === col) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = col;
            this.sortDirection = 'asc';
        }
        if (this.hasFilters) {
            this.loading = true;
            this.fetchData(this.currentPage);
        }
    },

    sortIndicator(col) {
        if (this.sortColumn !== col) return '';
        return this.sortDirection === 'asc' ? '↑' : '↓';
    },

    handleSearch() {
        this.currentPage = 1;
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.loading = true;
            this.fetchData(1);
        }, 400);
    },

    goToPage(page) {
        if (page < 1 || page > this.lastPage || page === this.currentPage) return;
        this.loading = true;
        this.fetchData(page);
    },

    init() {
        window.__goToPage = (page) => this.goToPage(page);

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('search_global')) this.query = urlParams.get('search_global');

        const filterMap = {
            Clave_predial: 'f_Clave_predial',
            cuenta: 'f_cuenta',
            contribuyente: 'f_contribuyente',
            colonia: 'f_colonia',
            tipo_predio: 'f_tipo_predio',
            estado: 'f_estado',
            ubicacion: 'f_ubicacion',
            año_ultimo_pago: 'f_año_ultimo_pago',
            ultimo_bimestre_pago: 'f_ultimo_bimestre_pago',
            superficie: 'f_superficie',
        };

        for (const [param, prop] of Object.entries(filterMap)) {
            if (urlParams.has(param)) this[prop] = urlParams.get(param);
        }
        if (urlParams.has('sort_column')) this.sortColumn = urlParams.get('sort_column');
        if (urlParams.has('sort_direction')) this.sortDirection = urlParams.get('sort_direction');
        if (urlParams.has('page')) this.currentPage = parseInt(urlParams.get('page'), 10) || 1;

        if (this.hasFilters) {
            this.loading = true;
            this.fetchData(this.currentPage);
        }
    },

    async fetchData(page) {
        this.results = [];
        this.serverTotal = 0;

        if (!this.hasFilters) {
            this.loading = false;
            return;
        }

        const params = new URLSearchParams();
        params.set('page', page);
        params.set('sort_column', this.sortColumn);
        params.set('sort_direction', this.sortDirection);
        if (this.query.trim()) params.set('search_global', this.query.trim());

        const colMap = {
            f_Clave_predial: 'Clave_predial',
            f_cuenta: 'cuenta',
            f_contribuyente: 'contribuyente',
            f_colonia: 'colonia',
            f_tipo_predio: 'tipo_predio',
            f_estado: 'estado',
            f_ubicacion: 'ubicacion',
            f_año_ultimo_pago: 'año_ultimo_pago',
            f_ultimo_bimestre_pago: 'ultimo_bimestre_pago',
            f_superficie: 'superficie',
        };

        for (const [prop, param] of Object.entries(colMap)) {
            if (this[prop].trim()) params.set(param, this[prop].trim());
        }

        try {
            const response = await fetch(`/predios?${params}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const json = await response.json();
            this.results = json.data;
            this.serverTotal = json.total;
            this.currentPage = json.current_page;
            this.lastPage = json.last_page;
            window.history.replaceState({}, '', `/predios?${params}`);
        } catch (e) {
            console.error('Error en búsqueda:', e);
        } finally {
            this.loading = false;
        }
    },

    deletePredio(id) {
        if (confirm('¿Estás seguro de eliminar este predio?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/predios/${id}`;
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrf);
            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    }
}));

Alpine.start();
