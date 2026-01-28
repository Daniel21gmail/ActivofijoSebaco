<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { ClipboardList, BarChart3, Layers, Activity, BookOpen, CalendarDays, Wrench, Users } from 'lucide-vue-next';

const reportOptions = [
    { id: 'inventario', label: 'Inventario General', desc: 'Listado completo y detallado de todos los activos fijos registrados.', icon: ClipboardList, color: 'text-blue-500', bg: 'bg-blue-50 dark:bg-blue-900/20' },
    { id: 'depreciacion', label: 'Cálculo Depreciación', desc: 'Análisis del valor actual, desgaste acumulado y proyección contable.', icon: BarChart3, color: 'text-emerald-500', bg: 'bg-emerald-50 dark:bg-emerald-900/20' },
    { id: 'categoria', label: 'Por Categoría', desc: 'Clasificación organizada de bienes según su naturaleza técnica.', icon: Layers, color: 'text-indigo-500', bg: 'bg-indigo-50 dark:bg-indigo-900/20' },
    { id: 'acciones', label: 'Movimientos de Activos', desc: 'Auditoría completa de movimientos, cambios y gestiones realizadas.', icon: Activity, color: 'text-amber-500', bg: 'bg-amber-50 dark:bg-amber-900/20' },
    { id: 'mantenimientos', label: 'Mantenimientos', desc: 'Reporte detallado de mantenimientos preventivos y correctivos.', icon: Wrench, color: 'text-rose-500', bg: 'bg-rose-50 dark:bg-rose-900/20' },
    { id: 'usuarios', label: 'Usuarios y Accesos', desc: 'Listado de usuarios del sistema con roles asignados y registro de accesos.', icon: Users, color: 'text-teal-500', bg: 'bg-teal-50 dark:bg-teal-900/20' },
    { id: 'libro_activos', label: 'Libro de Activos', desc: 'Registro pormenorizado para cumplimiento de normativas externas.', icon: BookOpen, color: 'text-purple-500', bg: 'bg-purple-50 dark:bg-purple-900/20' },
    { id: 'resumen_mensual', label: 'Resumen Mensual', desc: 'Consolidado de altas y bajas durante el ciclo de operación actual.', icon: CalendarDays, color: 'text-cyan-500', bg: 'bg-cyan-50 dark:bg-cyan-900/20' },
];

const selectedReport = ref(reportOptions[0]);
const showReportView = ref(false); // Default to GRID view

const props = defineProps({
    catalogs: Object,
    preview_data: Object,
    filters: Object
});

const filters = ref({
    departamento_id: props.filters?.departamento_id || '',
    ubicacion_id: props.filters?.ubicacion_id || '',
    responsable_id: props.filters?.responsable_id || '',
    categoria_id: props.filters?.categoria_id || '',
    estado_activo_id: props.filters?.estado_activo_id || '',
    fecha_inicio: props.filters?.fecha_inicio || '',
    fecha_fin: props.filters?.fecha_fin || ''
});

// Initialize view based on URL params
const updateReportFromParams = () => {
    if (props.filters?.report_type) {
        const found = reportOptions.find(r => r.id === props.filters.report_type);
        if (found) {
            selectedReport.value = found;
            showReportView.value = true;
        }
    }
};

updateReportFromParams();

watch(() => props.filters, () => {
    updateReportFromParams();
    // Also update local filters ref to match URL
    filters.value = {
        departamento_id: props.filters?.departamento_id || '',
        ubicacion_id: props.filters?.ubicacion_id || '',
        responsable_id: props.filters?.responsable_id || '',
        categoria_id: props.filters?.categoria_id || '',
        estado_activo_id: props.filters?.estado_activo_id || '',
        fecha_inicio: props.filters?.fecha_inicio || '',
        fecha_fin: props.filters?.fecha_fin || ''
    };
}, { deep: true });

const selectReport = (option) => {
    selectedReport.value = option;
    showReportView.value = true;
    filters.value = {
        departamento_id: '',
        ubicacion_id: '',
        responsable_id: '',
        categoria_id: '',
        estado_activo_id: '',
        fecha_inicio: '',
        fecha_fin: ''
    };
    applyFilters(); // Initial load for that report
};

const generateUrl = (type) => {
    const params = new URLSearchParams();
    
    // Add filtering params
    if (filters.value.departamento_id) params.append('departamento_id', filters.value.departamento_id);
    if (filters.value.ubicacion_id) params.append('ubicacion_id', filters.value.ubicacion_id);
    if (filters.value.responsable_id) params.append('responsable_id', filters.value.responsable_id);
    if (filters.value.estado_activo_id) params.append('estado_activo_id', filters.value.estado_activo_id);
    if (filters.value.categoria_id) params.append('categoria_id', filters.value.categoria_id);
    if (filters.value.fecha_inicio) params.append('fecha_inicio', filters.value.fecha_inicio);
    if (filters.value.fecha_fin) params.append('fecha_fin', filters.value.fecha_fin);

    // Add report type
    params.append('report_type', selectedReport.value.id);

    const baseUrl = type === 'pdf' ? route('reportes.pdf') : route('reportes.excel');
    return `${baseUrl}?${params.toString()}`;
};

const downloadPdf = () => {
    window.open(generateUrl('pdf'), '_blank');
};

const downloadExcel = () => {
    window.open(generateUrl('excel'), '_blank');
};

const applyFilters = () => {
    // Reload Inertia with new filters to update preview
    // Note: We are using Inertia requests which will refresh the page prop 'preview_data'
    const reportType = selectedReport.value.id;
    
    const params = {
        report_type: reportType,
        ...filters.value
    };

    // Remove empty filters
    Object.keys(params).forEach(key => !params[key] && delete params[key]);

    // Use router to visit index with params (preserved state)
    router.get(route('reportes.index'), params, {
        preserveState: true,
        preserveScroll: true,
        only: ['preview_data', 'filters'] // Ideally only partial reload
    });
};

const printReport = () => {
    window.print();
};

const clearFilters = () => {
    filters.value = {
        departamento_id: '',
        ubicacion_id: '',
        responsable_id: '',
        categoria_id: '',
        estado_activo_id: '',
        fecha_inicio: '',
        fecha_fin: ''
    };
    applyFilters();
};
</script>

<template>
    <Head title="Reportes" />

    <div class="flex h-screen bg-[#F8F9FE] font-sans">
        <AuthenticatedLayout>
             <!-- DASHBOARD VIEW (GRID) -->
             <div v-if="!showReportView" class="p-8 h-[calc(100vh-4rem)] overflow-y-auto">
                <div class="max-w-7xl mx-auto">
                    <div class="mb-10 text-center">
                        <h2 class="text-3xl font-black text-[#2D2E83] mb-3 uppercase tracking-tight">Centro de Reportes</h2>
                        <p class="text-gray-500 max-w-2xl mx-auto text-sm">Seleccione el módulo que desea consultar para generar reportes detallados, exportar datos y visualizar estadísticas en tiempo real.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div v-for="option in reportOptions" :key="option.id" 
                             @click="selectReport(option)"
                             class="group relative bg-white p-6 rounded-2xl shadow-sm border border-gray-100 cursor-pointer hover:shadow-xl hover:border-indigo-100 transition-all duration-300 hover:-translate-y-1"
                        >
                            <div class="absolute top-0 right-0 p-6 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </div>

                            <div class="p-4 rounded-xl inline-block mb-4 transition-colors" :class="option.bg">
                                <component :is="option.icon" class="w-8 h-8" :class="option.color" />
                            </div>
                            
                            <h3 class="font-bold text-gray-900 text-lg mb-2 group-hover:text-[#2D2E83] transition-colors">{{ option.label }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ option.desc }}</p>

                            <div class="mt-4 pt-4 border-t border-gray-50 flex items-center justify-between text-xs font-medium text-gray-400 group-hover:text-indigo-500">
                                <span>Configurar reporte</span>
                                <span class="group-hover:translate-x-1 transition-transform">Start &rarr;</span>
                            </div>
                        </div>
                    </div>
                </div>
             </div>

             <!-- DETAIL VIEW (SIDEBAR + PREVIEW) -->
             <div v-else class="p-6 flex gap-6 h-[calc(100vh-4rem)]">
                
                <!-- Left Sidebar: Filters (COMPACT) -->
                <div class="w-72 flex-shrink-0 flex flex-col gap-4">
                    <!-- Title Section -->
                    <div class="flex flex-col gap-1">
                        <button @click="showReportView = false" class="text-gray-400 hover:text-[#2D2E83] flex items-center gap-2 text-xs font-bold uppercase tracking-widest transition-colors mb-2">
                             <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                             VOLVER AL MENÚ
                        </button>
                        <h2 class="text-[#2D2E83] font-bold text-base flex items-center gap-2">
                             <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                             FILTROS DE DATOS
                        </h2>
                    </div>

                    <!-- Filter Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-5 h-full overflow-y-auto custom-scrollbar">
                        
                        <!-- Report Type Selector (Compact) -->
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">REPORTE ACTUAL</label>
                            <div class="relative bg-indigo-50/50 rounded-lg p-3 border border-indigo-100">
                                <div class="flex items-center gap-3">
                                    <component :is="selectedReport.icon" class="w-5 h-5 text-indigo-600" />
                                    <span class="font-bold text-sm text-indigo-900">{{ selectedReport.label }}</span>
                                </div>
                            </div>
                        </div>

                         <!-- Dynamic Filters -->
                        <div class="space-y-4">
                            <!-- Category Filter -->
                            <div v-if="selectedReport.id !== 'acciones' && selectedReport.id !== 'mantenimientos'">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">CATEGORÍA</label>
                                <select v-model="filters.categoria_id" class="w-full bg-gray-50 border border-gray-200 text-gray-700 py-2 px-3 rounded-lg focus:outline-none focus:border-[#2D2E83] transition-colors text-xs">
                                    <option value="">Todas</option>
                                    <option v-for="c in catalogs.categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                                </select>
                            </div>

                            <!-- Department Filter -->
                            <div v-if="selectedReport.id !== 'acciones' && selectedReport.id !== 'mantenimientos'">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">DEPARTAMENTO</label>
                                <select v-model="filters.departamento_id" class="w-full bg-gray-50 border border-gray-200 text-gray-700 py-2 px-3 rounded-lg focus:outline-none focus:border-[#2D2E83] transition-colors text-xs">
                                    <option value="">Todos</option>
                                    <option v-for="d in catalogs.departamentos" :key="d.id" :value="d.id">{{ d.nombre }}</option>
                                </select>
                            </div>

                             <!-- Location Filter -->
                            <div v-if="selectedReport.id !== 'acciones' && selectedReport.id !== 'mantenimientos'">
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">UBICACIÓN</label>
                                <select v-model="filters.ubicacion_id" class="w-full bg-gray-50 border border-gray-200 text-gray-700 py-2 px-3 rounded-lg focus:outline-none focus:border-[#2D2E83] transition-colors text-xs">
                                    <option value="">Todas</option>
                                    <option v-for="u in catalogs.ubicaciones" :key="u.id" :value="u.id">{{ u.nombre }}</option>
                                </select>
                            </div>

                             <!-- Responsible Filter -->
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">RESPONSABLE</label>
                                <select v-model="filters.responsable_id" class="w-full bg-gray-50 border border-gray-200 text-gray-700 py-2 px-3 rounded-lg focus:outline-none focus:border-[#2D2E83] transition-colors text-xs">
                                    <option value="">Todos</option>
                                    <option v-for="r in catalogs.responsables" :key="r.id" :value="r.id">{{ r.nombre_completo }}</option>
                                </select>
                            </div>

                            <!-- Date Range -->
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1.5">RANGO DE FECHAS</label>
                                <div class="grid grid-cols-2 gap-2">
                                     <input type="date" v-model="filters.fecha_inicio" class="w-full bg-gray-50 border border-gray-200 text-gray-700 py-2 px-2 rounded-lg focus:outline-none focus:border-[#2D2E83] text-xs">
                                     <input type="date" v-model="filters.fecha_fin" class="w-full bg-gray-50 border border-gray-200 text-gray-700 py-2 px-2 rounded-lg focus:outline-none focus:border-[#2D2E83] text-xs">
                                </div>
                            </div>
                        </div>

                        <div class="mt-auto pt-4">
                            <button @click="applyFilters" 
                                    class="w-full bg-[#1e1b4b] hover:bg-[#2D2E83] text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-900/10 flex items-center justify-center gap-2 transition-all transform hover:scale-[1.02] text-xs uppercase tracking-wider">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                ACTUALIZAR
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Content: Report Preview (COMPACT) -->
                <div class="flex-1 flex flex-col gap-4 min-w-0">
                    
                    <!-- Header Card -->
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                             <div class="h-12 w-12 bg-blue-50/50 rounded-xl flex items-center justify-center text-blue-600">
                                <component :is="selectedReport.icon" class="w-6 h-6" />
                             </div>
                             <div>
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-md text-[10px] font-bold tracking-wider uppercase">VISTA PREVIA</span>
                                    <span class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">{{ new Date().toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                </div>
                                <h1 class="text-xl font-black text-gray-900 tracking-tight uppercase">{{ selectedReport.label }}</h1>
                             </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button @click="downloadExcel" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-lg font-bold text-[10px] uppercase tracking-wider hover:bg-gray-50 hover:text-gray-900 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                Excel
                            </button>
                            <button @click="downloadPdf" class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-red-500 rounded-lg font-bold text-[10px] uppercase tracking-wider hover:bg-red-50 hover:border-red-100 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                PDF
                            </button>

                        </div>
                    </div>

                    <!-- Report Content Area -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex-1 flex flex-col min-h-0">
                        <!-- Table Container -->
                        <div class="flex-1 overflow-auto relative custom-scrollbar">
                             <table class="w-full text-left">
                                <thead class="bg-gray-50/50 sticky top-0 z-10">
                                    <tr>
                                        <!-- Dynamic Headers -->
                                        <template v-if="selectedReport.id === 'acciones'">
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Activo</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Origen</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Destino</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Responsable</th>
                                        </template>

                                        <template v-else-if="selectedReport.id === 'mantenimientos'">
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fecha</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Activo</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tipo</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Costo</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Responsable</th>
                                        </template>

                                        <template v-else-if="selectedReport.id === 'depreciacion'">
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">ID</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nombre</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Valor Adq.</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Vida Útil</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Deprec. Acum.</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Valor Libros</th>
                                        </template>

                                        <!-- Headers for Categoria -->
                                        <template v-else-if="selectedReport.id === 'categoria'">
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Categoría</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Cantidad</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Valor Total</th>
                                        </template>

                                        <!-- Headers for Libro de Activos -->
                                        <template v-else-if="selectedReport.id === 'libro_activos'">
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Código</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Activo</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Valor Adq.</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Deprec.</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Valor Libros</th>
                                        </template>

                                        <!-- Headers for Resumen Mensual -->
                                        <template v-else-if="selectedReport.id === 'resumen_mensual'">
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Fecha Alta</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Código</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Descripción</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Valor</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ubicación</th>
                                        </template>

                                        <!-- Headers for Usuarios -->
                                        <template v-else-if="selectedReport.id === 'usuarios'">
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Usuario</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Correo</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Rol</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Estado</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Último Acceso</th>
                                        </template>

                                        <!-- Default Headers -->
                                        <template v-else>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">ID</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Nombre</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Categoría</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Depto</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Ubicación</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Responsable</th>
                                            <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Estado</th>
                                        </template>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr v-for="item in preview_data.data" :key="item.id" class="hover:bg-gray-50/50 transition-colors group">
                                         
                                         <!-- Rows for History -->
                                         <template v-if="selectedReport.id === 'acciones'">
                                            <td class="px-6 py-3 text-xs font-semibold text-gray-900">{{ item.fecha_movimiento }}</td>
                                            <td class="px-6 py-3">
                                                <div class="text-xs font-bold text-gray-900">{{ item.activo_fijo?.codigo }}</div>
                                            </td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.ubicacion_origen?.nombre || '-' }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.ubicacion_destino?.nombre || '-' }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.responsable_destino?.nombre_completo || '-' }}</td>
                                         </template>

                                         <template v-else-if="selectedReport.id === 'mantenimientos'">
                                            <td class="px-6 py-3 text-xs font-semibold text-gray-900">{{ item.fecha_inicio }}</td>
                                            <td class="px-6 py-3">
                                                <div class="text-xs font-bold text-gray-900">{{ item.activo_fijo?.codigo }}</div>
                                            </td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.tipo }}</td>
                                            <td class="px-6 py-3 text-xs font-bold text-rose-600">C$ {{ item.costo }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.responsable?.nombre_completo || '-' }}</td>
                                         </template>

                                          <!-- Rows for Depreciation -->
                                         <template v-else-if="selectedReport.id === 'depreciacion'">
                                            <td class="px-6 py-3 text-xs font-semibold text-gray-400">{{ item.codigo }}</td>
                                            <td class="px-6 py-3 text-xs font-bold text-gray-700">{{ item.descripcion }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">C$ {{ item.valor_adquisicion }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.vida_util }} años</td>
                                            <td class="px-6 py-3 text-xs text-amber-600 font-bold">C$ {{ item.depreciacion_acumulada }}</td>
                                            <td class="px-6 py-3 text-xs text-emerald-600 font-bold">C$ {{ item.valor_libros }}</td>
                                         </template>

                                         <!-- Rows for Categoria -->
                                         <template v-else-if="selectedReport.id === 'categoria'">
                                            <td class="px-6 py-3 text-xs font-bold text-gray-700">{{ item.nombre }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500 text-center">{{ item.activos_count }}</td>
                                            <td class="px-6 py-3 text-xs text-emerald-600 font-bold text-right">C$ {{ item.valor_total?.toFixed(2) }}</td>
                                         </template>

                                         <!-- Rows for Libro de Activos -->
                                         <template v-else-if="selectedReport.id === 'libro_activos'">
                                            <td class="px-6 py-3 text-xs font-semibold text-gray-900">{{ item.codigo }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.descripcion }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-900 text-right font-medium">C$ {{ item.valor_adquisicion ? parseFloat(item.valor_adquisicion).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00' }}</td>
                                            <td class="px-6 py-3 text-xs text-amber-600 text-right font-medium">C$ {{ item.depreciacion_acumulada ? parseFloat(item.depreciacion_acumulada).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00' }}</td>
                                            <td class="px-6 py-3 text-xs text-emerald-600 text-right font-bold">C$ {{ item.valor_libros ? parseFloat(item.valor_libros).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00' }}</td>
                                         </template>

                                         <!-- Rows for Resumen Mensual -->
                                         <template v-else-if="selectedReport.id === 'resumen_mensual'">
                                            <td class="px-6 py-3 text-xs font-semibold text-gray-900">{{ item.created_at ? new Date(item.created_at).toLocaleDateString('es-ES') : '-' }}</td>
                                            <td class="px-6 py-3 text-xs font-bold text-gray-700">{{ item.codigo }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.descripcion }}</td>
                                            <td class="px-6 py-3 text-xs text-emerald-600 font-bold text-right">C$ {{ item.valor_adquisicion ? parseFloat(item.valor_adquisicion).toLocaleString('en-US', {minimumFractionDigits: 2}) : '0.00' }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.ubicacion?.nombre || '-' }}</td>
                                         </template>

                                         <!-- Rows for Usuarios -->
                                         <template v-else-if="selectedReport.id === 'usuarios'">
                                            <td class="px-6 py-3 text-xs font-bold text-gray-700">{{ item.nombre }}</td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.email }}</td>
                                            <td class="px-6 py-3 text-xs text-indigo-600 font-bold">{{ item.role?.nombre || item.rol || 'Sin Rol' }}</td>
                                            <td class="px-6 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                                      :class="item.activo ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'">
                                                    {{ item.activo ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-3 text-xs text-gray-500">{{ item.last_login_at ? new Date(item.last_login_at).toLocaleString('es-NI') : 'Nunca' }}</td>
                                         </template>

                                         <template v-else>
                                            <td class="px-6 py-3 text-xs font-semibold text-gray-400">{{ item.codigo }}</td>
                                            <td class="px-6 py-3">
                                                <div class="text-xs font-bold text-gray-700 truncate max-w-[150px]">{{ item.descripcion }}</div>
                                            </td>
                                            <td class="px-6 py-3 text-[10px] text-gray-500 font-medium uppercase">{{ item.categoria?.nombre }}</td>
                                            <td class="px-6 py-3 text-[10px] text-gray-500 font-medium uppercase">{{ item.departamento?.nombre }}</td>
                                            <td class="px-6 py-3 text-[10px] text-gray-500">{{ item.ubicacion?.nombre }}</td>
                                            <td class="px-6 py-3 text-[10px] text-gray-500 truncate max-w-[100px]">{{ item.responsable?.nombre_completo || 'N/A' }}</td>
                                            <td class="px-6 py-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider"
                                                      :class="item.estado_activo?.nombre === 'Bueno' ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500'">
                                                    {{ item.estado_activo?.nombre }}
                                                </span>
                                            </td>
                                         </template>
                                    </tr>
                                </tbody>
                             </table>
                        </div>
                    </div>
                </div>
             </div>
        </AuthenticatedLayout>
    </div>
</template>
