<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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
    form.delete(route('vehiculos.destroy', itemToDelete.value.id), {
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
    <Head title="Gestión de Vehículos" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Vehículos</h1>
                    <p class="mt-1 text-sm text-gray-500">Gestión de la flota vehicular vinculada a Activos Fijos.</p>
                </div>
                <Link :href="route('vehiculos.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Registrar Vehículo
                </Link>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-[11px] font-bold text-blue-600 uppercase tracking-widest text-center">Placa</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-blue-600 uppercase tracking-widest text-center">Imagen</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-blue-600 uppercase tracking-widest">Vehículo / Descripción</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-blue-600 uppercase tracking-widest text-center">Motor / Chasis</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-blue-600 uppercase tracking-widest">Ubicación / Depto.</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-blue-600 uppercase tracking-widest text-center">Valor Neto</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-blue-600 uppercase tracking-widest text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4 text-sm font-bold text-indigo-800 text-center">{{ item.placa }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="inline-flex h-14 w-14 rounded-xl bg-gray-100 border border-gray-100 overflow-hidden shadow-sm">
                                        <img v-if="item.activo_fijo?.imagen" :src="'/storage/' + item.activo_fijo.imagen" class="h-full w-full object-cover" />
                                        <div v-else class="h-full w-full flex items-center justify-center text-gray-300">
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-800 uppercase">{{ item.activo_fijo?.descripcion }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ item.activo_fijo?.marca?.nombre }} | {{ item.activo_fijo?.modelo?.nombre }}</div>
                                    <div class="text-[10px] text-indigo-500 font-bold mt-0.5">Circulación: {{ item.numero_circulacion }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-[10px] text-gray-400 uppercase tracking-tighter">Motor: {{ item.motor }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase tracking-tighter mt-1">Chasis: {{ item.chasis }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-semibold text-gray-700">{{ item.activo_fijo?.ubicacion?.nombre }}</div>
                                    <div class="text-[10px] text-gray-400 uppercase tracking-tighter mt-0.5">{{ item.activo_fijo?.departamento?.nombre }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="text-[10px] text-gray-400 mb-0.5 uppercase tracking-tighter">C$</div>
                                    <div class="text-sm text-emerald-600 font-extrabold leading-none">{{ formatCurrency(item.activo_fijo?.valor_libros).replace('NIO', '').replace('C$', '') }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openViewModal(item)" class="p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-all" title="Ver Detalles">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <Link :href="route('vehiculos.edit', item.id)" class="p-1.5 text-orange-600 hover:text-orange-800 hover:bg-orange-50 rounded-lg transition-all" title="Editar">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </Link>
                                        <button @click="confirmDeletion(item)" class="p-1.5 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all" title="Eliminar">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="items.length === 0">
                                <td colspan="7" class="px-6 py-10 text-center text-gray-400 italic bg-white">
                                    No se encontraron vehículos registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View Detail Modal -->
        <Modal :show="viewModalOpen" @close="closeViewModal" max-width="5xl">
            <div class="p-0 overflow-hidden rounded-2xl bg-white" v-if="viewingItem">
                <div class="p-8 bg-white border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Detalle de Vehículo</p>
                            <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">
                                {{ viewingItem.activo_fijo?.descripcion }} - <span class="text-indigo-600">{{ viewingItem.placa }}</span>
                            </h2>
                        </div>
                        <button @click="closeViewModal" class="self-start p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 mt-8">
                        <Link :href="route('vehiculos.edit', viewingItem.id)" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Editar Datos
                        </Link>
                        <button @click="closeViewModal" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                             Volver
                        </button>
                    </div>
                </div>

                <div class="p-8 bg-gray-50/50">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        <div class="lg:col-span-4 rounded-3xl overflow-hidden bg-white shadow-xl shadow-gray-200/50 border border-gray-100 p-2 text-center">
                            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50 flex items-center justify-center border border-gray-50 mb-4">
                                <img v-if="viewingItem.activo_fijo?.imagen" :src="'/storage/' + viewingItem.activo_fijo.imagen" class="w-full h-full object-cover" />
                                <svg v-else class="h-24 w-24 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="py-2 inline-block px-4 bg-indigo-600 text-white rounded-xl font-bold text-lg mb-4">{{ viewingItem.placa }}</div>
                        </div>

                        <div class="lg:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-10 py-4 font-sans">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Placa No.</p>
                                <p class="text-xl font-black text-indigo-700 tracking-tight">{{ viewingItem.placa }}</p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Número de Circulación</p>
                                <p class="text-xl font-black text-gray-900 tracking-tight">{{ viewingItem.numero_circulacion }}</p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Motor</p>
                                <p class="text-lg font-bold text-gray-900 uppercase tracking-tight">{{ viewingItem.motor }}</p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Chasis</p>
                                <p class="text-lg font-bold text-gray-900 uppercase tracking-tight">{{ viewingItem.chasis }}</p>
                            </div>

                            <div v-if="viewingItem.activo_fijo?.serie" class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">No. Serie</p>
                                <p class="text-lg font-bold text-gray-900 uppercase tracking-tight">{{ viewingItem.activo_fijo.serie }}</p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Código AF</p>
                                <p class="text-xl font-black text-gray-900">{{ viewingItem.activo_fijo?.codigo }}</p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ubicación</p>
                                <p class="text-lg font-bold text-gray-900 uppercase leading-none tracking-tight">{{ viewingItem.activo_fijo?.ubicacion?.nombre }}</p>
                                <p class="text-[10px] font-bold text-indigo-500 uppercase mt-1 tracking-tight">{{ viewingItem.activo_fijo?.departamento?.nombre }}</p>
                            </div>

                            <div class="space-y-1">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Marca / Modelo</p>
                                <p class="text-lg font-bold text-gray-900 uppercase tracking-tight">
                                    {{ viewingItem.activo_fijo?.marca?.nombre }} / {{ viewingItem.activo_fijo?.modelo?.nombre }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Estado</p>
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 rounded-lg text-xs font-bold uppercase border border-emerald-100">
                                    {{ viewingItem.activo_fijo?.estado_activo?.nombre }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-12 border-t border-gray-200/60 font-sans">
                         <div class="bg-gray-900 rounded-3xl overflow-hidden shadow-2xl">
                             <div class="bg-white/10 px-6 py-3 border-b border-white/10 backdrop-blur-sm">
                                 <span class="text-xs font-black text-white uppercase tracking-widest">Información Financiera y Depreciación</span>
                             </div>
                             <div class="p-8">
                                 <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
                                     <div>
                                         <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Costo Adquisición</p>
                                         <p class="text-xl font-black text-white font-mono tracking-tighter">C$ {{ formatCurrency(viewingItem.activo_fijo?.valor_adquisicion).replace('NIO','').replace('C$','').trim() }}</p>
                                     </div>
                                     <div class="border-x border-white/10 px-8">
                                         <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Valor Residual (1%)</p>
                                         <p class="text-xl font-black text-amber-400 font-mono tracking-tighter">C$ {{ formatCurrency(viewingItem.activo_fijo?.valor_residual || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                     </div>
                                     <div>
                                         <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Valor Neto Actual</p>
                                         <p class="text-xl font-black text-emerald-400 font-mono tracking-tighter">C$ {{ formatCurrency(viewingItem.activo_fijo?.valor_libros || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                     </div>
                                 </div>

                                 <div class="mt-8 pt-8 border-t border-white/10 grid grid-cols-1 md:grid-cols-3 gap-8">
                                     <div>
                                         <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Depreciación Anual</p>
                                         <p class="text-sm font-black text-white font-mono uppercase tracking-tighter">C$ {{ formatCurrency(viewingItem.activo_fijo?.depreciacion_anual || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                     </div>
                                     <div class="border-x border-white/10 px-8">
                                         <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Depreciación Mensual</p>
                                         <p class="text-sm font-black text-white font-mono uppercase tracking-tighter">C$ {{ formatCurrency(viewingItem.activo_fijo?.depreciacion_mensual || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                     </div>
                                     <div>
                                         <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Depreciación Acumulada</p>
                                         <p class="text-sm font-black text-rose-400 font-mono uppercase tracking-tighter">C$ {{ formatCurrency(viewingItem.activo_fijo?.depreciacion_acumulada || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                     </div>
                                 </div>
                             </div>
                             <div class="bg-white/5 p-4 flex justify-between items-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                 <div class="flex items-center gap-4">
                                     <span>Adquirido: {{ viewingItem.activo_fijo?.fecha_adquisicion }}</span>
                                     <span class="h-1 w-1 bg-gray-600 rounded-full"></span>
                                     <span>Vida Útil: {{ viewingItem.activo_fijo?.vida_util }} Años</span>
                                 </div>
                                 <span class="text-indigo-400 uppercase">Cálculo en Línea Recta</span>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Delete Verification Modal -->
        <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-8">
                <div class="flex items-center gap-4 text-red-600 mb-4">
                    <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">¿Confirmar eliminación?</h2>
                </div>
                <p class="text-gray-600">¿Estás seguro de que deseas eliminar este vehículo ({{ itemToDelete?.placa }})? Se eliminará también el registro de Activo Fijo asociado.</p>
                
                <div class="mt-8 flex justify-end gap-3">
                    <SecondaryButton @click="confirmingDeletion = false" class="!rounded-xl">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteItem" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="!rounded-xl shadow-lg shadow-red-200">
                        Sí, Eliminar Vehículo
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
