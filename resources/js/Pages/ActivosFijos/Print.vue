<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    item: Object,
    qrCode: String,
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-NI', { style: 'currency', currency: 'NIO' }).format(value);
};

const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const print = () => {
    window.print();
};

const close = () => {
    window.close();
};

onMounted(() => {
    // Optional: Auto-print on load
    // setTimeout(() => window.print(), 500);
});
</script>

<template>
    <Head title="Ficha de Activo Fijo" />
    
    <div class="print-container bg-white min-h-screen text-gray-900 font-sans p-8">
        <!-- Control Bar (Hidden when printing) -->
        <div class="print:hidden flex justify-between items-center mb-8 pb-4 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">Vista Previa - Ficha de Activo</h1>
            <div class="flex gap-4">
                <button @click="close" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-bold transition-colors">
                    Cerrar
                </button>
                <button @click="print" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold shadow-md transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Imprimir
                </button>
            </div>
        </div>

        <!-- Printable Content -->
        <div class="max-w-[8.5in] mx-auto bg-white p-8 print:p-0" id="printable-area">
            
            <!-- Header from Image -->
            <div class="flex justify-between items-center mb-6 border-b-2 border-black pb-6">
                <!-- Logo -->
                <div class="w-1/4">
                    <img src="/images/logo_alcaldia.jpg" alt="Logo Alcaldía" class="h-28 w-auto object-contain" />
                </div>
                
                <!-- Center Text -->
                <div class="w-2/4 text-center">
                    <h1 class="text-2xl font-bold uppercase text-black leading-tight">
                        Alcaldía Municipal de Sébaco
                    </h1>
                    <h2 class="text-sm font-bold uppercase text-gray-700 mt-1">
                        Departamento de Activos Fijos
                    </h2>
                    <div class="mt-4">
                        <span class="text-xl font-black uppercase tracking-wide border-b-2 border-black pb-1">
                            Ficha Técnica de Activo Fijo
                        </span>
                    </div>
                </div>

                <!-- QR Box -->
                <div class="w-1/4 flex justify-end">
                    <div class="w-32 h-32 border-2 border-black flex flex-col items-center justify-center p-2 rounded-lg">
                        <div class="flex-1 w-full flex flex-col items-center justify-center">
                            <img v-if="qrCode" :src="'data:image/svg+xml;base64,' + qrCode" class="w-24 h-24" />
                            <div v-else class="text-xs text-gray-400">Error QR</div>
                        </div>
                        <span class="text-[9px] font-bold uppercase text-black mt-1">Escanear Detalle</span>
                    </div>
                </div>
            </div>

            <!-- I. DATOS DE IDENTIFICACIÓN -->
            <div class="mb-6">
                <div class="bg-gray-100 border-2 border-black border-b-0 px-4 py-1.5 text-center">
                    <h3 class="text-xs font-bold uppercase text-black tracking-widest">I. Datos de Identificación</h3>
                </div>
                <table class="w-full border-2 border-black border-collapse tabular-nums">
                    <tr class="border-b-2 border-black">
                        <td class="w-1/4 px-4 py-3 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Código Inventario:</p>
                        </td>
                        <td class="w-1/4 px-4 py-3 bg-white border-r-2 border-black">
                            <p class="text-xl font-black text-black">{{ item.codigo }}</p>
                        </td>
                        <td class="w-1/4 px-4 py-3 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Categoría:</p>
                        </td>
                        <td class="w-1/4 px-4 py-3 bg-white">
                            <p class="text-xs font-bold text-black uppercase">{{ item.categoria?.nombre || 'General' }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-3 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Nombre del Bien:</p>
                        </td>
                        <td class="px-4 py-3 bg-white border-r-2 border-black">
                            <p class="text-xs font-bold text-black uppercase">{{ item.descripcion }}</p>
                        </td>
                        <td class="px-4 py-3 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Estado Actual:</p>
                        </td>
                        <td class="px-4 py-3 bg-white">
                            <p class="text-xs font-bold text-black uppercase">{{ item.estado_activo?.nombre }}</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- II. ESPECIFICACIONES TÉCNICAS -->
            <div class="mb-6">
                <div class="bg-gray-100 border-2 border-black border-b-0 px-4 py-1.5 text-center">
                    <h3 class="text-xs font-bold uppercase text-black tracking-widest">II. Especificaciones Técnicas</h3>
                </div>
                <table class="w-full border-2 border-black border-collapse">
                    <tr class="border-b-2 border-black">
                        <td class="w-1/5 px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Marca:</p>
                        </td>
                        <td class="w-1/3 px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-xs font-bold text-gray-800 uppercase">{{ item.marca?.nombre }}</p>
                        </td>
                        <td class="w-1/5 px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Modelo:</p>
                        </td>
                        <td class="px-4 py-2.5 bg-white">
                            <p class="text-xs font-bold text-gray-800 uppercase">{{ item.modelo?.nombre }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Color:</p>
                        </td>
                        <td class="px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-xs font-bold text-gray-800 uppercase">{{ item.color?.nombre }}</p>
                        </td>
                        <td class="px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Serie:</p>
                        </td>
                        <td class="px-4 py-2.5 bg-white">
                            <p class="text-xs font-mono font-bold text-gray-800 uppercase">{{ item.serie || 'S/N' }}</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- III. UBICACIÓN Y RESPONSABILIDAD -->
            <div class="mb-10">
                <div class="bg-gray-100 border-2 border-black border-b-0 px-4 py-1.5 text-center">
                    <h3 class="text-xs font-bold uppercase text-black tracking-widest">III. Ubicación y Responsabilidad</h3>
                </div>
                <table class="w-full border-2 border-black border-collapse">
                    <tr class="border-b-2 border-black">
                        <td class="w-1/4 px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Departamento / Área:</p>
                        </td>
                        <td class="px-4 py-2.5 bg-white">
                            <p class="text-xs font-bold text-black uppercase">{{ item.departamento?.nombre }}</p>
                        </td>
                    </tr>
                    <tr class="border-b-2 border-black">
                        <td class="px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Ubicación Física:</p>
                        </td>
                        <td class="px-4 py-2.5 bg-white">
                            <p class="text-xs font-bold text-black uppercase">{{ item.ubicacion?.nombre }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4 py-2.5 bg-white border-r-2 border-black">
                            <p class="text-[10px] font-bold text-black uppercase">Responsable Asignado:</p>
                        </td>
                        <td class="px-4 py-2.5 bg-white">
                            <p class="text-sm font-black text-black uppercase">{{ item.responsable?.nombre_completo || 'SIN ASIGNAR' }}</p>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Extra Info: Financial and Image (Not in main refs but useful) -->
             <div class="grid grid-cols-2 gap-8 mb-12">
                 <div class="border-2 border-black rounded shadow-sm overflow-hidden">
                    <div class="bg-gray-100 border-b-2 border-black px-4 py-1 text-center">
                        <p class="text-[9px] font-bold uppercase text-black">Fotografía del Bien</p>
                    </div>
                    <div class="h-40 flex items-center justify-center p-2">
                        <img v-if="item.imagen" :src="'/storage/' + item.imagen" class="h-full w-full object-contain" />
                        <div v-else class="text-gray-300 italic text-[10px]">Sin imagen cargada</div>
                    </div>
                 </div>
                 <div class="border-2 border-black rounded shadow-sm overflow-hidden">
                    <div class="bg-gray-100 border-b-2 border-black px-4 py-1 text-center">
                        <p class="text-[9px] font-bold uppercase text-black">Datos Adicionales / Financieros</p>
                    </div>
                    <div class="p-4 space-y-4">
                        <div class="flex justify-between items-end border-b border-gray-100 pb-1">
                            <span class="text-[9px] font-bold text-gray-500 uppercase">Valor de Adquisición:</span>
                            <span class="text-xs font-black text-black">{{ formatCurrency(item.valor_adquisicion).replace('NIO', 'C$') }}</span>
                        </div>
                        <div class="flex justify-between items-end border-b border-gray-100 pb-1">
                            <span class="text-[9px] font-bold text-gray-500 uppercase">Vida Útil:</span>
                            <span class="text-xs font-black text-black">{{ item.vida_util }} Años</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-[9px] font-bold text-indigo-600 uppercase">Valor Neto Actual:</span>
                            <span class="text-sm font-black text-indigo-700">{{ formatCurrency(item.valor_libros).replace('NIO', 'C$') }}</span>
                        </div>
                    </div>
                 </div>
             </div>

            <!-- Signatures Section -->
            <div class="mt-20 pt-8">
                <div class="grid grid-cols-2 gap-24">
                    <div class="text-center">
                        <div class="border-b-2 border-black mb-2 w-full mx-auto"></div>
                        <p class="text-[10px] font-black text-black uppercase tracking-widest">Firma del Responsable</p>
                        <p class="text-[9px] text-gray-500 font-medium uppercase mt-1">{{ item.responsable?.nombre_completo || 'SIN ASIGNAR' }}</p>
                    </div>
                    <div class="text-center">
                        <div class="border-b-2 border-black mb-2 w-full mx-auto"></div>
                        <p class="text-[10px] font-black text-black uppercase tracking-widest">Autorizado (Bienes Municipales)</p>
                        <p class="text-[9px] text-gray-500 font-medium uppercase mt-1">SIAFSEB - SEBACO</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-12 text-center">
                 <p class="text-[8px] text-gray-400 font-mono tracking-widest uppercase">
                     Generado el {{ new Date().toLocaleString('es-ES') }} | Documento Oficial de Inventario
                 </p>
            </div>

        </div>
    </div>
</template>

<style>
@media print {
    @page { 
        size: letter;
        margin: 0.5in;
    }
    body { 
        print-color-adjust: exact; 
        -webkit-print-color-adjust: exact; 
    }
    .print-container {
        padding: 0;
        margin: 0;
        background: white;
    }
}
</style>
