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

    sort(col) {
        if (this.sortColumn === col) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = col;
            this.sortDirection = 'asc';
        }
    },

    sortIndicator(col) {
        if (this.sortColumn !== col) return '';
        return this.sortDirection === 'asc' ? '↑' : '↓';
    },

    handleSearch() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.loading = true;
            this.fetchData();
        }, 400);
    },

    async fetchData() {
        this.results = [];
        this.serverTotal = 0;

        if (!this.hasFilters) {
            this.loading = false;
            return;
        }

        const params = new URLSearchParams();
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
