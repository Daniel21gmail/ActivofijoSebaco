<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    item: Object,
});

const formatDate = (date) => {
    return new Date().toLocaleDateString('es-NI', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Verificación Oficial de Activo" />
    
    <div class="min-h-screen bg-gray-50 text-gray-900 font-sans pb-12">
        <!-- Header / Navbar-like -->
        <div class="bg-white shadow-sm border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-2">
                <img src="/images/logo_alcaldia.jpg" alt="Logo" class="h-10 w-auto object-contain" />
                <div class="leading-tight">
                    <h1 class="text-xs font-black uppercase text-gray-800">Alcaldía Municipal</h1>
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Sébaco - Matagalpa</p>
                </div>
            </div>
            <div class="flex flex-col items-end">
                <div class="flex items-center gap-1.5 bg-green-100 text-green-800 px-2 py-1 rounded-full border border-green-200">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-wide">Verificado</span>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-md mx-auto p-4 space-y-4">
            
            <!-- Asset Identity Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-indigo-600 px-4 py-3">
                    <h2 class="text-white text-xs font-bold uppercase tracking-widest opacity-90">Código de Inventario</h2>
                    <p class="text-white text-2xl font-black mt-0.5 font-mono">{{ item.codigo }}</p>
                </div>
                <div class="p-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Descripción del Bien</h3>
                    <p class="text-lg font-bold text-gray-900 leading-tight">{{ item.descripcion }}</p>
                    
                    <div class="mt-4 flex gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                            {{ item.categoria?.nombre || 'General' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                            {{ item.estado_activo?.nombre || 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Image Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" v-if="item.imagen">
                 <div class="bg-gray-50 border-b border-gray-100 px-4 py-2">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Fotografía Registrada</h3>
                </div>
                <div class="aspect-video bg-gray-100 flex items-center justify-center overflow-hidden">
                    <img :src="'/storage/' + item.imagen" class="w-full h-full object-cover" />
                </div>
            </div>

            <!-- Critical Info: Location & Responsibility -->
            <div class="grid grid-cols-1 gap-4">
                <!-- Ubicacion -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-start gap-4">
                    <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ubicación Actual</h3>
                        <p class="text-sm font-bold text-gray-900">{{ item.ubicacion?.nombre }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ item.departamento?.nombre }}</p>
                    </div>
                </div>

                <!-- Responsable -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 flex items-start gap-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Responsable Oficial</h3>
                        <p class="text-sm font-bold text-gray-900">{{ item.responsable?.nombre_completo || 'SIN ASIGNAR' }}</p>
                        <p class="text-xs text-green-600 font-medium mt-1">● Activo en sistema</p>
                    </div>
                </div>
            </div>

            <!-- Technical Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-100 px-4 py-2">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Detalles Técnicos</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-sm text-gray-500">Marca</span>
                        <span class="text-sm font-medium text-gray-900">{{ item.marca?.nombre || 'N/A' }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-sm text-gray-500">Modelo</span>
                        <span class="text-sm font-medium text-gray-900">{{ item.modelo?.nombre || 'N/A' }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-sm text-gray-500">Serie</span>
                        <span class="text-sm font-mono font-medium text-gray-900">{{ item.serie || 'N/A' }}</span>
                    </div>
                     <div class="px-4 py-3 flex justify-between">
                        <span class="text-sm text-gray-500">Color</span>
                        <span class="text-sm font-medium text-gray-900">{{ item.color?.nombre || 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center pt-8 pb-4">
                <p class="text-[10px] text-gray-400 uppercase tracking-wider mb-2">Consulta Pública Segura</p>
                <div class="flex items-center justify-center gap-2 mb-4">
                    <div class="h-px bg-gray-300 w-12"></div>
                    <span class="text-xs font-bold text-gray-500">SIAFSEB</span>
                    <div class="h-px bg-gray-300 w-12"></div>
                </div>
                <p class="text-[9px] text-gray-400">
                    Consulta generada el {{ formatDate() }} <br>
                    Este es un documento digital oficial de la Alcaldía de Sébaco.
                </p>
            </div>
            
        </div>
    </div>
</template>
