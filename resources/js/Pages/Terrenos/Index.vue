<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    items: Array,
});

const confirmingDeletion = ref(false);
const itemToDelete = ref(null);
const viewingItem = ref(null);
const viewModalOpen = ref(false);

const form = useForm({});

const openViewModal = (item) => {
    viewingItem.value = item;
    viewModalOpen.value = true;
};

const closeViewModal = () => {
    viewModalOpen.value = false;
    viewingItem.value = null;
};

const confirmDeletion = (item) => {
    itemToDelete.value = item;
    confirmingDeletion.value = true;
};

const deleteItem = () => {
    form.delete(route('terrenos.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            confirmingDeletion.value = false;
            itemToDelete.value = null;
        },
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-NI', { style: 'currency', currency: 'NIO' }).format(value);
};
</script>

<template>
    <Head title="Gestión de Terrenos" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Terrenos e Inmuebles</h1>
                    <p class="mt-1 text-sm text-gray-500 font-medium italic">Gestión de áreas y propiedades territoriales vinculadas a Activos Fijos.</p>
                </div>
                <Link :href="route('terrenos.create')" class="inline-flex items-center px-6 py-3 bg-emerald-600 border border-transparent rounded-2xl font-black text-xs text-white uppercase tracking-widest hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-100">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    REGISTRAR TERRENO
                </Link>
            </div>

            <div class="bg-white overflow-hidden shadow-2xl shadow-gray-200/50 sm:rounded-[32px] border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-8 py-6 text-[11px] font-black text-blue-600 uppercase tracking-widest text-center">Folio Real</th>
                                <th class="px-6 py-6 text-[11px] font-black text-blue-600 uppercase tracking-widest text-center">Código</th>
                                <th class="px-6 py-6 text-[11px] font-black text-blue-600 uppercase tracking-widest text-center">Imagen</th>
                                <th class="px-6 py-6 text-[11px] font-black text-blue-600 uppercase tracking-widest">Terreno / Uso de Suelo</th>
                                <th class="px-6 py-6 text-[11px] font-black text-blue-600 uppercase tracking-widest text-center">Área (m²) / Código Catastral</th>
                                <th class="px-6 py-6 text-[11px] font-black text-blue-600 uppercase tracking-widest text-center border-l border-gray-50">Valor Neto</th>
                                <th class="px-8 py-6 text-[11px] font-black text-blue-600 uppercase tracking-widest text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="item in items" :key="item.id" class="hover:bg-emerald-50/20 transition-all duration-300 group">
                                <td class="px-8 py-6 text-sm font-black text-emerald-800 text-center uppercase tracking-tighter">
                                    <span class="bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 shadow-sm">{{ item.folio_real }}</span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <span class="inline-block text-xs font-black text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 shadow-sm uppercase tracking-tight">{{ item.activo_fijo?.codigo }}</span>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <div class="inline-flex h-16 w-16 rounded-2xl bg-gray-50 border border-gray-100 overflow-hidden shadow-sm group-hover:shadow-md transition-shadow">
                                        <img v-if="item.activo_fijo?.imagen" :src="'/storage/' + item.activo_fijo.imagen" class="h-full w-full object-cover" />
                                        <div v-else class="h-full w-full flex items-center justify-center text-gray-200">
                                            <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ item.activo_fijo?.descripcion }}</div>
                                    <div class="text-[10px] text-emerald-600 font-black mt-1 uppercase tracking-widest">{{ item.uso }}</div>
                                    <div class="text-[10px] text-gray-400 font-bold mt-1 uppercase">{{ item.activo_fijo?.ubicacion?.nombre }}</div>
                                </td>
                                <td class="px-6 py-6 text-center">
                                    <div class="text-lg font-black text-gray-900 tracking-tighter">{{ item.area_m2 }} <span class="text-[10px] font-bold text-gray-300 uppercase">m²</span></div>
                                    <div class="inline-block text-[9px] text-gray-500 font-black mt-1 uppercase bg-gray-100 px-2 py-0.5 rounded-lg border border-gray-200">{{ item.catastro }}</div>
                                </td>
                                <td class="px-6 py-6 text-center border-l border-gray-50 bg-gray-50/20">
                                    <div class="text-[9px] text-gray-400 mb-1 font-black uppercase tracking-tighter">Valor en Libros</div>
                                    <div class="text-base text-emerald-600 font-black tracking-tighter">{{ formatCurrency(item.activo_fijo?.valor_libros).replace('NIO', '').replace('C$', 'C$ ') }}</div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 outline-none">
                                        <button @click="openViewModal(item)" class="p-2 text-indigo-600 hover:text-white hover:bg-indigo-600 rounded-xl transition-all duration-300 shadow-sm border border-indigo-100 bg-white" title="Ver Detalles">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <Link :href="route('terrenos.edit', item.id)" class="p-2 text-amber-600 hover:text-white hover:bg-amber-600 rounded-xl transition-all duration-300 shadow-sm border border-amber-100 bg-white" title="Editar">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </Link>
                                        <button @click="confirmDeletion(item)" class="p-2 text-red-600 hover:text-white hover:bg-red-600 rounded-xl transition-all duration-300 shadow-sm border border-red-100 bg-white" title="Eliminar">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="items.length === 0">
                                <td colspan="7" class="px-8 py-20 text-center text-gray-400 italic bg-white rounded-b-3xl">
                                    <div class="flex flex-col items-center gap-4">
                                        <svg class="h-16 w-16 text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        <span class="font-bold text-gray-300 uppercase tracking-widest text-xs">No se encontraron terrenos registrados</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View Detail Modal -->
        <Modal :show="viewModalOpen" @close="closeViewModal" max-width="5xl">
            <div class="p-0 overflow-hidden rounded-[40px] bg-white border border-gray-100" v-if="viewingItem">
                <div class="p-10 bg-white border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-8">
                     <div class="flex items-center gap-6">
                        <div class="h-20 w-20 bg-emerald-600 rounded-[30px] flex items-center justify-center text-white shadow-2xl shadow-emerald-200">
                             <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.3em] mb-1">EXPEDIENTE DEL INMUEBLE</p>
                            <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tight">
                                {{ viewingItem.activo_fijo?.descripcion }}
                            </h2>
                            <p class="text-sm font-bold text-gray-400 mt-1">Folio Real: <span class="text-indigo-600 uppercase">{{ viewingItem.folio_real }}</span></p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <Link :href="route('terrenos.edit', viewingItem.id)" class="inline-flex items-center px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white text-xs font-black rounded-2xl transition-all shadow-xl shadow-amber-100 uppercase tracking-widest">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Editar Expediente
                        </Link>
                        <button @click="closeViewModal" class="p-3 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-2xl transition-all">
                            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <div class="p-10 bg-gray-50/30">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                        <div class="lg:col-span-4 space-y-8">
                            <div class="bg-white rounded-[40px] overflow-hidden shadow-2xl shadow-gray-200/50 border border-gray-100 p-2 text-center group">
                                <div class="aspect-square rounded-[36px] overflow-hidden bg-gray-50 flex items-center justify-center border border-gray-50 mb-4 transition-transform duration-500 group-hover:scale-105">
                                    <img v-if="viewingItem.activo_fijo?.imagen" :src="'/storage/' + viewingItem.activo_fijo.imagen" class="w-full h-full object-cover" />
                                    <svg v-else class="h-24 w-24 text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            </div>

                             <div class="bg-indigo-600 rounded-[35px] p-8 text-white shadow-2xl shadow-indigo-200">
                                <p class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-2">Valor en Libros Actual</p>
                                <p class="text-4xl font-black tracking-tighter leading-none mb-1">C$ {{ formatCurrency(viewingItem.activo_fijo?.valor_libros || 0).replace('NIO', '').replace('C$', '') }}</p>
                                <p class="text-[10px] font-bold text-indigo-300/80 uppercase tracking-tighter">Moneda Nacional de Nicaragua</p>
                            </div>
                        </div>

                        <div class="lg:col-span-8 space-y-12">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10">
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-l-4 border-emerald-500 pl-3">Área Superficial</p>
                                    <p class="text-2xl font-black text-gray-900 tracking-tighter">{{ viewingItem.area_m2 }} <span class="text-sm font-bold text-gray-400 ml-1 uppercase">Metros Cuadrados</span></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-l-4 border-emerald-500 pl-3">Ubicación Registrada</p>
                                    <p class="text-xl font-black text-gray-900 tracking-tighter uppercase leading-tight">{{ viewingItem.activo_fijo?.ubicacion?.nombre }}</p>
                                    <p class="text-[10px] font-black text-indigo-600 uppercase mt-1 tracking-widest bg-indigo-50 px-2 py-0.5 rounded-lg inline-block">{{ viewingItem.activo_fijo?.departamento?.nombre }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-l-4 border-emerald-500 pl-3">Código Catastral</p>
                                    <p class="text-xl font-black text-gray-900 tracking-tighter uppercase">{{ viewingItem.catastro }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-l-4 border-emerald-500 pl-3">Estado Legal del Activo</p>
                                    <span class="inline-block mt-1 px-4 py-1.5 bg-emerald-50 text-emerald-700 rounded-xl text-[10px] font-black uppercase border border-emerald-100 shadow-sm">
                                        {{ viewingItem.activo_fijo?.estado_activo?.nombre }}
                                    </span>
                                </div>
                                <div class="col-span-2 space-y-1 p-6 bg-white rounded-3xl border border-gray-100 shadow-sm">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Responsable de Custodia</p>
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-400 font-black">{{ viewingItem.activo_fijo?.responsable?.nombre_completo.charAt(0) }}</div>
                                        <p class="text-lg font-black text-gray-900 tracking-tight uppercase leading-none">{{ viewingItem.activo_fijo?.responsable?.nombre_completo }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6">
                                <div class="bg-gray-100/50 p-6 rounded-3xl border border-gray-200/50">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Historial Adquisitivo</p>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm">
                                            <span class="text-[11px] font-bold text-gray-500 uppercase">Costo Incial</span>
                                            <span class="text-sm font-black text-gray-900">C$ {{ formatCurrency(viewingItem.activo_fijo?.valor_adquisicion).replace('NIO', '').replace('C$', '') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm">
                                            <span class="text-[11px] font-bold text-gray-500 uppercase">Fecha Registro</span>
                                            <span class="text-sm font-black text-gray-900">{{ viewingItem.activo_fijo?.fecha_adquisicion }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-100/50 p-6 rounded-3xl border border-gray-200/50">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Origen y Respaldo</p>
                                    <div class="space-y-4">
                                        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm">
                                            <span class="text-[11px] font-bold text-gray-500 uppercase">Vida Útil</span>
                                            <span class="text-sm font-black text-indigo-600">{{ viewingItem.activo_fijo?.vida_util }} AÑOS</span>
                                        </div>
                                        <div class="flex justify-between items-center bg-white p-4 rounded-2xl shadow-sm">
                                            <span class="text-[11px] font-bold text-gray-500 uppercase">V. Residual</span>
                                            <span class="text-sm font-black text-orange-600">C$ {{ formatCurrency(viewingItem.activo_fijo?.valor_residual).replace('NIO', '').replace('C$', '') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-10 rounded-[40px] bg-white text-center">
                <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-[30px] bg-red-50 text-red-600 shadow-xl shadow-red-50 mb-8 border border-red-100">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight mb-4 tracking-tighter">¿Confirmar Eliminación?</h2>
                <p class="text-gray-500 font-medium leading-relaxed mb-10 max-w-sm mx-auto">Esta acción es irreversible. Se eliminará el expediente del terreno con Folio <span class="text-red-600 font-black">{{ itemToDelete?.folio_real }}</span> y todos los datos asociados al Activo Fijo.</p>
                
                <div class="flex flex-col gap-3">
                    <button @click="deleteItem" class="w-full py-4 bg-red-600 hover:bg-red-700 text-white font-black rounded-2xl shadow-2xl shadow-red-100 transition-all uppercase tracking-widest text-xs">
                        SÍ, ELIMINAR PERMANENTEMENTE
                    </button>
                    <button @click="confirmingDeletion = false" class="w-full py-4 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-2xl transition-all uppercase tracking-widest text-xs">
                        CANCELAR Y VOLVER
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
