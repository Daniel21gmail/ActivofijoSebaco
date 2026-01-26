<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    stats: Object,
    chart_data: Object
});

const stats = computed(() => [
    { name: 'Total Activos', value: props.stats.total_activos, icon: 'cube', color: 'blue' },
    { name: 'Valor Total', value: 'C$' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(props.stats.valor_total), icon: 'currency', color: 'green' },
    { name: 'Depreciación Acum.', value: 'C$' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(props.stats.depreciacion_acumulada), icon: 'trend-down', color: 'red' },
    { name: 'Valor Neto Actual', value: 'C$' + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 }).format(props.stats.valor_neto), icon: 'wallet', color: 'indigo' },
]);

// Helper to assign colors to states dynamically
const getStateColor = (name) => {
    const n = name.toLowerCase();
    if (n.includes('bueno') || n.includes('nuevo')) return '#10B981'; // emerald-500
    if (n.includes('regular')) return '#F59E0B'; // amber-500
    if (n.includes('malo') || n.includes('fuera de servicio')) return '#EF4444'; // red-500
    if (n.includes('reparación')) return '#3B82F6'; // blue-500
    if (n.includes('baja') || n.includes('extraviado')) return '#6B7280'; // gray-500
    if (n.includes('propio')) return '#6366F1'; // indigo-500
    if (n.includes('donado') || n.includes('comodato')) return '#06B6D4'; // cyan-500
    if (n.includes('litigio')) return '#F97316'; // orange-500
    if (n.includes('arrendado')) return '#8B5CF6'; // violet-500
    return '#A855F7'; // purple-500 (default)
};

const categoryPalette = [
    '#6366F1', // indigo-500
    '#EC4899', // pink-500
    '#F59E0B', // amber-500
    '#10B981', // emerald-500
    '#3B82F6', // blue-500
    '#8B5CF6', // violet-500
    '#F97316', // orange-500
    '#06B6D4', // cyan-500
];

const hoveredState = ref(null);

const donutSegments = computed(() => {
    const total = props.stats.total_activos > 0 ? props.stats.total_activos : 1;
    let currentPercentage = 0;
    const radius = 40;
    const circumference = 2 * Math.PI * radius;

    return props.chart_data.estados.map(estado => {
        const percentage = (estado.total / total) * 100;
        const strokeDasharray = `${(percentage / 100) * circumference} ${circumference}`;
        const strokeDashoffset = - (currentPercentage / 100) * circumference;
        const color = getStateColor(estado.nombre);
        
        const segment = {
            ...estado,
            strokeDasharray,
            strokeDashoffset,
            color,
            percentage
        };
        
        currentPercentage += percentage;
        return segment;
    });
});
const lineChartPoints = computed(() => {
    const data = props.chart_data.adquisiciones;
    if (!data || data.length === 0) return '';
    
    const max = Math.max(...data.map(d => d.total), 1);
    const width = 100;
    const height = 40;
    
    let points = '';
    data.forEach((d, i) => {
        const x = (i / (data.length - 1 || 1)) * width;
        const y = height - (d.total / max) * height;
        points += `${x},${y} `;
    });
    return points;
});

const lineChartArea = computed(() => {
    const points = lineChartPoints.value;
    if (!points) return '';
    return `0,40 ${points} 100,40`;
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="space-y-8">
            <!-- Welcome Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Bienvenido, {{ $page.props.auth.user.nombre }}</h1>
                    <p class="mt-1 text-lg text-gray-500">Sistema de Activo Fijo — Alcaldía Municipal de Sébaco</p>
                </div>
                <div class="flex items-center gap-2 rounded-full bg-white px-4 py-2 shadow-sm">
                    <span class="flex h-3 w-3 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-sm font-medium text-gray-600">Sistema en línea</span>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <div v-for="stat in stats" :key="stat.name" 
                     class="relative overflow-hidden rounded-2xl bg-white p-6 shadow-sm transition-all hover:shadow-md group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500">{{ stat.name }}</p>
                            <p class="mt-2 text-lg font-bold text-gray-900 tracking-tight">{{ stat.value }}</p>
                        </div>
                        <div :class="{
                            'bg-blue-50 text-blue-600': stat.color === 'blue',
                            'bg-green-50 text-green-600': stat.color === 'green',
                            'bg-red-50 text-red-600': stat.color === 'red',
                            'bg-indigo-50 text-indigo-600': stat.color === 'indigo',
                        }" class="rounded-xl p-3 group-hover:scale-110 transition-transform duration-300">
                            <!-- Cube Icon -->
                            <svg v-if="stat.icon === 'cube'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <!-- Currency Icon -->
                            <svg v-if="stat.icon === 'currency'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <!-- Trend Down Icon -->
                            <svg v-if="stat.icon === 'trend-down'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/></svg>
                            <!-- Wallet Icon -->
                            <svg v-if="stat.icon === 'wallet'" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <!-- Bar Chart: Por Categoría -->
                <div class="lg:col-span-1 rounded-2xl bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Por Categoría</h3>
                    </div>
                    <!-- Mock Bar Chart -->
                    <div class="space-y-3">
                        <div v-for="(cat, index) in props.chart_data.categorias" :key="cat.nombre" class="flex items-center gap-3">
                            <span class="w-28 text-[9px] font-black text-gray-600 uppercase tracking-tighter truncate" :title="cat.nombre">
                                {{ cat.nombre }}
                            </span>
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden shadow-inner relative group">
                                <div :style="{ 
                                        width: (cat.total / props.stats.total_activos * 100) + '%',
                                        backgroundColor: categoryPalette[index % categoryPalette.length]
                                     }" 
                                     class="h-full rounded-full transition-all duration-1000 ease-out"></div>
                            </div>
                            <span class="w-6 text-[10px] font-black text-gray-400 text-right">
                                {{ cat.total }}
                            </span>
                        </div>
                        <div v-if="props.chart_data.categorias.length === 0" class="text-center text-sm text-gray-400 py-4">
                            No hay activos registrados
                        </div>
                    </div>
                </div>

                <!-- Donut Chart: Estado de Activos -->
                <div class="lg:col-span-1 rounded-2xl bg-white p-6 shadow-sm flex flex-col items-center">
                    <div class="w-full flex items-center gap-3 mb-6">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Estado de Activos</h3>
                    </div>
                    <!-- Mock Donut Chart -->
                    <!-- Dynamic SVG Donut Chart -->
                    <div v-if="props.chart_data.estados.length > 0" class="flex flex-col items-center">
                        <div class="relative w-56 h-56 flex items-center justify-center group">
                            <!-- SVG Circle -->
                            <svg viewBox="0 0 100 100" class="w-full h-full transform -rotate-90">
                                <circle v-for="segment in donutSegments" 
                                        :key="segment.nombre"
                                        cx="50" cy="50" r="40"
                                        fill="transparent"
                                        stroke-width="12"
                                        :stroke="segment.color"
                                        :stroke-dasharray="segment.strokeDasharray"
                                        :stroke-dashoffset="segment.strokeDashoffset"
                                        class="transition-all duration-300 cursor-pointer hover:stroke-[14px]"
                                        @mouseenter="hoveredState = segment"
                                        @mouseleave="hoveredState = null"
                                />
                            </svg>

                            <!-- Inner Circle Content -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                <Transition name="fade" mode="out-in">
                                    <div :key="hoveredState ? hoveredState.nombre : 'total'" class="flex flex-col items-center">
                                        <span class="text-4xl font-black text-gray-900 tracking-tighter">
                                            {{ hoveredState ? hoveredState.total : props.stats.total_activos }}
                                        </span>
                                        <span class="text-[10px] uppercase font-black tracking-[0.2em]" 
                                              :class="hoveredState ? 'text-indigo-600' : 'text-gray-400'">
                                            {{ hoveredState ? hoveredState.nombre : 'TOTAL ACTIVOS' }}
                                        </span>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="flex flex-wrap gap-x-4 gap-y-2 justify-center mt-8">
                            <div v-for="estado in props.chart_data.estados" 
                                 :key="estado.nombre" 
                                 class="flex items-center gap-2 px-2 py-1 rounded-md transition-all cursor-default"
                                 :class="hoveredState?.nombre === estado.nombre ? 'bg-gray-50 scale-105' : 'opacity-70 hover:opacity-100'"
                                 @mouseenter="hoveredState = estado"
                                 @mouseleave="hoveredState = null"
                            >
                                <span class="h-2.5 w-2.5 rounded-full" :style="{ backgroundColor: getStateColor(estado.nombre) }"></span>
                                <span class="text-[11px] text-gray-600 font-bold uppercase tracking-tight">
                                    {{ estado.nombre }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center text-sm text-gray-400 py-10 flex flex-col items-center">
                        <div class="w-32 h-32 rounded-full border-4 border-gray-100 flex items-center justify-center mb-2">
                            <span class="text-gray-300 text-2xl">0</span>
                        </div>
                        Sin datos para mostrar
                    </div>
                </div>

                <!-- Line Chart: Adquisiciones -->
                <div class="lg:col-span-1 rounded-2xl bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                            <h3 class="text-lg font-bold text-gray-800 uppercase tracking-tight">Adquisiciones</h3>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded">Últimos 12 meses</span>
                    </div>
                     <div class="mt-4">
                        <!-- SVG Line Chart Trend -->
                        <div v-if="props.chart_data.adquisiciones.length > 0" class="h-24 w-full mb-6">
                            <svg viewBox="0 0 100 40" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                                <defs>
                                    <linearGradient id="line-gradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.3" />
                                        <stop offset="100%" stop-color="#3B82F6" stop-opacity="0" />
                                    </linearGradient>
                                </defs>
                                <polyline
                                    fill="url(#line-gradient)"
                                    stroke="none"
                                    :points="lineChartArea"
                                />
                                <polyline
                                    fill="none"
                                    stroke="#3B82F6"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    :points="lineChartPoints"
                                    class="transition-all duration-700"
                                />
                            </svg>
                        </div>

                         <div class="space-y-3 overflow-y-auto max-h-48 custom-scrollbar">
                            <div v-for="acq in props.chart_data.adquisiciones" :key="acq.mes" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg group hover:bg-blue-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                    <span class="text-xs font-bold text-gray-700 uppercase tracking-tighter">{{ acq.mes }}</span>
                                </div>
                                <span class="text-xs text-blue-600 font-black font-mono">C$ {{ new Intl.NumberFormat('en-US').format(acq.total) }}</span>
                            </div>
                            <div v-if="props.chart_data.adquisiciones.length === 0" class="text-center text-sm text-gray-400 py-4 italic border-2 border-dashed border-gray-100 rounded-xl">
                                Sin adquisiciones registradas.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e2e8f0;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #cbd5e1;
}
</style>
