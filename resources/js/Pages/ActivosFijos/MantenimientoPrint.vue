<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    mantenimiento: Object
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-NI', {
        style: 'currency',
        currency: 'NIO',
    }).format(value);
};

onMounted(() => {
    // Automatically open print dialog
    setTimeout(() => {
        window.print();
    }, 500);
});
</script>

<template>
    <Head title="Imprimir Mantenimiento" />

    <div class="min-h-screen bg-white p-8 font-sans print:p-0">
        <div class="max-w-4xl mx-auto border-4 border-double border-black p-6">
            <!-- Header -->
            <div class="flex items-center justify-between border-b-2 border-black pb-4 mb-6">
                <img src="/logo_sebaco.jpg" alt="Logo" class="h-20 w-auto">
                <div class="text-center flex-1">
                    <h1 class="text-xl font-black uppercase">Alcaldía Municipal de Sébaco</h1>
                    <h2 class="text-lg font-bold text-gray-700">Comprobante de Mantenimiento</h2>
                    <p class="text-xs text-gray-500 uppercase">SIAFSEB - Sistema de Control de Activos Fijos</p>
                </div>
                <div class="text-right">
                    <div class="border-2 border-black p-2 bg-gray-50">
                        <p class="text-[10px] font-black uppercase mb-1">Registro No.</p>
                        <p class="text-lg font-black text-rose-600">MT-{{ mantenimiento.id.toString().padStart(5, '0') }}</p>
                    </div>
                </div>
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <!-- Asset Info -->
                <div class="col-span-2 bg-gray-900 text-white px-4 py-2 text-xs font-black uppercase tracking-widest mb-2">
                    I. Información del Activo Fijo
                </div>
                <div class="border-2 border-black p-3">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Código Inventario:</p>
                    <p class="text-sm font-black">{{ mantenimiento.activo_fijo?.codigo }}</p>
                </div>
                <div class="border-2 border-black p-3">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Descripción:</p>
                    <p class="text-sm font-bold uppercase">{{ mantenimiento.activo_fijo?.descripcion }}</p>
                </div>

                <!-- Maintenance Info -->
                <div class="col-span-2 bg-gray-900 text-white px-4 py-2 text-xs font-black uppercase tracking-widest my-2">
                    II. Detalles del Mantenimiento
                </div>
                <div class="border-2 border-black p-3">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Fecha Realizado:</p>
                    <p class="text-sm font-black">{{ mantenimiento.fecha_inicio }}</p>
                </div>
                <div class="border-2 border-black p-3">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Tipo de Intervención:</p>
                    <p class="text-sm font-black uppercase bg-yellow-100 inline-block px-2">{{ mantenimiento.tipo }}</p>
                </div>
                <div class="border-2 border-black p-3">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Costo del Servicio:</p>
                    <p class="text-sm font-black">{{ formatCurrency(mantenimiento.costo) }}</p>
                </div>
                <div class="border-2 border-black p-3">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Proveedor / Taller:</p>
                    <p class="text-sm font-black uppercase">{{ mantenimiento.proveedor?.nombre }}</p>
                </div>

                 <!-- Persons Info -->
                 <div class="col-span-2 bg-gray-900 text-white px-4 py-2 text-xs font-black uppercase tracking-widest my-2">
                    III. Asignación y Control
                </div>
                <div class="border-2 border-black p-3">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Responsable Directo:</p>
                    <p class="text-sm font-black uppercase">{{ mantenimiento.responsable?.nombre_completo }}</p>
                </div>
                <div class="border-2 border-black p-3">
                    <p class="text-[10px] font-bold text-gray-500 uppercase">Digitado por:</p>
                    <p class="text-sm font-bold uppercase">{{ mantenimiento.creado_por?.name }}</p>
                </div>
            </div>

            <!-- Signatures -->
            <div class="mt-20 grid grid-cols-2 gap-24 px-10">
                <div class="text-center border-t-2 border-black pt-2">
                    <p class="text-[10px] font-black uppercase">Responsable de Bienes</p>
                </div>
                <div class="text-center border-t-2 border-black pt-2">
                    <p class="text-[10px] font-black uppercase">Firma Recibido (Taller/Prov)</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-12 text-center text-[9px] text-gray-400 border-t border-gray-100 pt-4 uppercase tracking-tighter">
                Este documento es un comprobante oficial de mantenimiento. Por favor archívelo junto al expediente del activo.
                <br>
                Generado el {{ new Date().toLocaleString() }}
            </div>
        </div>
        
        <!-- Print helper -->
        <div class="mt-8 text-center no-print">
            <button onclick="window.print()" class="bg-indigo-600 text-white px-6 py-2 rounded-lg font-bold shadow-lg hover:bg-indigo-700 transition-colors uppercase text-sm">
                Imprimir Nuevamente
            </button>
            <p class="mt-4 text-gray-400 text-xs italic">Si el diálogo de impresión no aparece, use el botón de arriba.</p>
        </div>
    </div>
</template>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: white !important;
    }
}
</style>
