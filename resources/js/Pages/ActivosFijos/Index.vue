<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    items: Array,
    catalogs: Object,
});

const confirmingDeletion = ref(false);
const itemToDelete = ref(null);
const editingItem = ref(null);
const viewingItem = ref(null);
const modalOpen = ref(false);
const viewModalOpen = ref(false);
const historyModalOpen = ref(false);
const transferModalOpen = ref(false);
const maintenanceModalOpen = ref(false);
const decommissionModalOpen = ref(false);
const autoResidual = ref(true);
const imagePreview = ref(null);
const activeTab = ref('movimientos'); // For history modal
const searchQuery = ref('');
const filterCategoria = ref('');
const filterDepartamento = ref('');
const filterEstado = ref('');
const activeWizardStep = ref(1);
const isDragging = ref(false);


const transferForm = useForm({
    activo_fijo_id: '',
    fecha_movimiento: new Date().toISOString().split('T')[0],
    ubicacion_destino_id: '',
    departamento_destino_id: '',
    responsable_destino_id: '',
    autorizado_por: '',
});

const maintenanceForm = useForm({
    activo_fijo_id: '',
    fecha_inicio: new Date().toISOString().split('T')[0],
    costo: '',
    proveedor_id: '',
    responsable_id: '',
    tipo: 'Preventivo',
});

const decommissionForm = useForm({
    activo_fijo_id: '',
    fecha_baja: new Date().toISOString().split('T')[0],
    motivo: '',
    autorizado_por: '',
});

const form = useForm({
    descripcion: '',
    serie: '',
    categoria_id: '',
    departamento_id: '',
    fecha_adquisicion: '',
    valor_adquisicion: '',
    vida_util: 5,
    valor_residual: '',
    marca_id: '',
    modelo_id: '',
    color_id: '',
    fuente_id: '',
    proveedor_id: '',
    responsable_id: '',
    estado_activo_id: '',
    ubicacion_id: '',
    imagen: null,
});

watch(() => form.valor_adquisicion, (newVal) => {
    if (newVal && autoResidual.value && !editingItem.value) {
        form.valor_residual = (parseFloat(newVal) * 0.01).toFixed(2);
    }
});

watch(autoResidual, (newVal) => {
    if (newVal && form.valor_adquisicion) {
        form.valor_residual = (parseFloat(form.valor_adquisicion) * 0.01).toFixed(2);
    }
});

// Computed property to check if selected category is a land type
const isLandCategory = computed(() => {
    if (!form.categoria_id || !props.catalogs?.categorias) return false;
    const selectedCategory = props.catalogs.categorias.find(c => c.id === form.categoria_id);
    if (!selectedCategory) return false;
    // Check if category name contains "terreno" (case insensitive)
    return selectedCategory.nombre.toLowerCase().includes('terreno');
});

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.clearErrors();
    form.vida_util = 5;
    autoResidual.value = true;
    imagePreview.value = null;
    activeWizardStep.value = 1;
    modalOpen.value = true;
};

const openEditModal = (item) => {
    // Check if this is a land (terreno) or vehicle (vehiculo)
    // If so, redirect to their specific edit forms using Inertia
    if (item.terreno) {
        router.visit(route('terrenos.edit', item.terreno.id));
        return;
    }
    
    if (item.vehiculo) {
        router.visit(route('vehiculos.edit', item.vehiculo.id));
        return;
    }
    
    // Otherwise, open the generic edit modal
    editingItem.value = item;
    form.clearErrors();
    form.descripcion = item.descripcion;
    form.serie = item.serie;
    form.categoria_id = item.categoria_id;
    form.departamento_id = item.departamento_id;
    form.fecha_adquisicion = item.fecha_adquisicion;
    form.valor_adquisicion = item.valor_adquisicion;
    form.vida_util = item.vida_util;
    form.valor_residual = item.valor_residual;
    form.marca_id = item.marca_id;
    form.modelo_id = item.modelo_id;
    form.color_id = item.color_id;
    form.fuente_id = item.fuente_id;
    form.proveedor_id = item.proveedor_id;
    form.responsable_id = item.responsable_id;
    form.estado_activo_id = item.estado_activo_id;
    form.ubicacion_id = item.ubicacion_id;
    form.imagen = null;
    imagePreview.value = item.imagen ? '/storage/' + item.imagen : null;
    autoResidual.value = false;
    activeWizardStep.value = 1;
    modalOpen.value = true;
};

const goToStep = (step) => {
    if (step > activeWizardStep.value) {
        if (!validateCurrentStep()) return;
    }
    activeWizardStep.value = step;
};

const validateCurrentStep = () => {
    form.clearErrors();
    let isValid = true;

    if (activeWizardStep.value === 1) {
        if (!form.descripcion) {
            form.setError('descripcion', 'La descripción es obligatoria');
            isValid = false;
        }
        if (!form.categoria_id) {
            form.setError('categoria_id', 'La categoría es obligatoria');
            isValid = false;
        }
        if (!form.estado_activo_id) {
            form.setError('estado_activo_id', 'El estado es obligatorio');
            isValid = false;
        }
    } else if (activeWizardStep.value === 2) {
        if (!form.departamento_id) {
            form.setError('departamento_id', 'El departamento es obligatorio');
            isValid = false;
        }
        if (!form.ubicacion_id) {
            form.setError('ubicacion_id', 'La ubicación es obligatoria');
            isValid = false;
        }
        if (!form.responsable_id) {
            form.setError('responsable_id', 'El responsable es obligatorio');
            isValid = false;
        }
    } else if (activeWizardStep.value === 3) {
        // Skip technical data validation for land categories
        if (!isLandCategory.value) {
            if (!form.marca_id) {
                form.setError('marca_id', 'La marca es obligatoria');
                isValid = false;
            }
            if (!form.modelo_id) {
                form.setError('modelo_id', 'El modelo es obligatorio');
                isValid = false;
            }
        }
    }

    return isValid;
};

const nextStep = () => {
    if (validateCurrentStep()) {
        if (activeWizardStep.value < 4) activeWizardStep.value++;
    }
};

const prevStep = () => {
    if (activeWizardStep.value > 1) activeWizardStep.value--;
};


const submit = () => {
    if (editingItem.value) {
        form.transform((data) => ({
            ...data,
            _method: 'put',
        })).post(route('activos-fijos.update', editingItem.value.id), {
            forceFormData: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('activos-fijos.store'), {
            forceFormData: true,
            onSuccess: () => closeModal(),
        });
    }
};

const openViewModal = (item) => {
    viewingItem.value = item;
    viewModalOpen.value = true;
};

const closeViewModal = () => {
    viewModalOpen.value = false;
    viewingItem.value = null;
};

const editFromView = () => {
    const item = viewingItem.value;
    closeViewModal();
    openEditModal(item);
};

const confirmDeletion = (item) => {
    itemToDelete.value = item;
    confirmingDeletion.value = true;
};

const printAsset = (item) => {
    window.open(route('activos-fijos.print', item.id), '_blank');
};

const deleteItem = () => {
    form.delete(route('activos-fijos.destroy', itemToDelete.value.id), {
        onSuccess: () => {
            confirmingDeletion.value = false;
            itemToDelete.value = null;
        },
    });
};

const openHistoryModal = () => {
    historyModalOpen.value = true;
};

const openTransferModal = () => {
    transferForm.activo_fijo_id = viewingItem.value.id;
    transferForm.ubicacion_destino_id = '';
    transferForm.departamento_destino_id = '';
    transferForm.responsable_destino_id = '';
    transferForm.autorizado_por = '';
    transferModalOpen.value = true;
};

const openMaintenanceModal = () => {
    maintenanceForm.activo_fijo_id = viewingItem.value.id;
    maintenanceForm.costo = '';
    maintenanceForm.proveedor_id = '';
    maintenanceForm.responsable_id = '';
    maintenanceModalOpen.value = true;
};

const openDecommissionModal = () => {
    decommissionForm.activo_fijo_id = viewingItem.value.id;
    decommissionForm.motivo = '';
    decommissionForm.autorizado_por = '';
    decommissionModalOpen.value = true;
};

const submitTransfer = () => {
    transferForm.post(route('movimientos.store'), {
        onSuccess: () => {
            transferModalOpen.value = false;
            // Update viewingItem with new data if possible, or just let Inertia reload
            closeViewModal();
        },
    });
};

const submitMaintenance = () => {
    maintenanceForm.post(route('mantenimientos.store'), {
        onSuccess: () => {
            maintenanceModalOpen.value = false;
            closeViewModal();
        },
    });
};

const submitDecommission = () => {
    decommissionForm.post(route('bajas-activos.store'), {
        onSuccess: () => {
            decommissionModalOpen.value = false;
            closeViewModal();
        },
    });
};

const closeModal = () => {
    modalOpen.value = false;
    editingItem.value = null;
    form.reset();
};

const filteredItems = computed(() => {
    if (!props.items) return [];
    return props.items.filter(item => {
        const matchesSearch = !searchQuery.value || 
            item.descripcion.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.codigo.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            (item.serie && item.serie.toLowerCase().includes(searchQuery.value.toLowerCase()));
        
        const matchesCategoria = !filterCategoria.value || item.categoria_id === parseInt(filterCategoria.value);
        const matchesDepartamento = !filterDepartamento.value || item.departamento_id === parseInt(filterDepartamento.value);
        const matchesEstado = !filterEstado.value || item.estado_activo_id === parseInt(filterEstado.value);

        return matchesSearch && matchesCategoria && matchesDepartamento && matchesEstado;
    });
});

const groupedItems = computed(() => {
    return filteredItems.value.reduce((acc, item) => {
        const key = item.responsable?.nombre_completo || 'Sin Responsable Asignado';
        if (!acc[key]) acc[key] = [];
        acc[key].push(item);
        return acc;
    }, {});
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('es-NI', { style: 'currency', currency: 'NIO' }).format(value);
};

const onFileChange = (e) => {
    const file = e.target.files[0];
    handleFile(file);
};

const handleFile = (file) => {
    if (file && file.type.startsWith('image/')) {
        form.imagen = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const onDragOver = () => {
    isDragging.value = true;
};

const onDragLeave = () => {
    isDragging.value = false;
};

const onDrop = (e) => {
    isDragging.value = false;
    const file = e.dataTransfer.files[0];
    handleFile(file);
};

</script>

<template>
    <Head title="Activos Fijos" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Activos Fijos</h1>
                    <p class="mt-1 text-sm text-gray-500">Gestión integral de los activos fijos de la institución.</p>
                </div>
                <PrimaryButton @click="openCreateModal" class="!rounded-xl shadow-lg shadow-indigo-100">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Registrar Nuevo Activo
                </PrimaryButton>
            </div>

            <!-- Advanced Filters Bar -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 space-y-4">
                <div class="flex items-center gap-2 text-indigo-600 font-bold text-xs uppercase tracking-widest mb-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtros de Búsqueda Avanzada
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search Input -->
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <input 
                            v-model="searchQuery" 
                            type="text" 
                            placeholder="Buscar por código, nombre o serie..." 
                            class="block w-full pl-10 pr-3 py-2 border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm bg-gray-50/50 transition-all"
                        />
                    </div>

                    <!-- Category Filter -->
                    <select v-model="filterCategoria" class="block w-full border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm bg-gray-50/50 transition-all">
                        <option value="">Todas las Categorías</option>
                        <option v-for="c in catalogs.categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                    </select>

                    <!-- Department Filter -->
                    <select v-model="filterDepartamento" class="block w-full border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm bg-gray-50/50 transition-all">
                        <option value="">Todos los Departamentos</option>
                        <option v-for="d in catalogs.departamentos" :key="d.id" :value="d.id">{{ d.nombre }}</option>
                    </select>

                    <!-- Status Filter -->
                    <select v-model="filterEstado" class="block w-full border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-sm bg-gray-50/50 transition-all">
                        <option value="">Todos los Estados</option>
                        <option v-for="e in catalogs.estados" :key="e.id" :value="e.id">{{ e.nombre }}</option>
                    </select>
                </div>
                <!-- Active Filters Count & Reset -->
                <div v-if="searchQuery || filterCategoria || filterDepartamento || filterEstado" class="flex items-center justify-between pt-2">
                    <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-tight">
                        Filtros activos: {{ (searchQuery?1:0) + (filterCategoria?1:0) + (filterDepartamento?1:0) + (filterEstado?1:0) }}
                    </span>
                    <button @click="searchQuery=''; filterCategoria=''; filterDepartamento=''; filterEstado='';" class="text-[10px] font-bold text-gray-400 hover:text-red-500 uppercase tracking-tight transition-colors">
                        Limpiar Todos los Filtros
                    </button>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse table-fixed">
                        <thead>
                            <tr class="bg-gray-900 border-b-2 border-gray-900">
                                <th class="w-24 px-4 py-3 text-[10px] font-black text-white uppercase tracking-widest text-center">Código</th>
                                <th class="w-20 px-4 py-3 text-[10px] font-black text-white uppercase tracking-widest text-center">Bien</th>
                                <th class="px-4 py-3 text-[10px] font-black text-white uppercase tracking-widest text-left">Descripción y Especificaciones</th>
                                <th class="w-40 px-4 py-3 text-[10px] font-black text-white uppercase tracking-widest text-left">Categoría / Depto.</th>
                                <th class="w-32 px-4 py-3 text-[10px] font-black text-white uppercase tracking-widest text-center">Valor Neto</th>
                                <th class="w-28 px-4 py-3 text-[10px] font-black text-white uppercase tracking-widest text-center">Estado</th>
                                <th class="w-40 px-4 py-3 text-[10px] font-black text-white uppercase tracking-widest text-right">Controles</th>
                            </tr>
                        </thead>
                        <tbody v-for="(assets, responsable) in groupedItems" :key="responsable" class="divide-y divide-gray-100">
                            <!-- Group Header: Responsable -->
                            <tr class="bg-gray-100 border-b border-gray-900/10">
                                <td colspan="7" class="px-4 py-2 text-[11px] font-black text-gray-900 border-l-[6px] border-indigo-600 uppercase tracking-wider">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="h-6 w-6 bg-indigo-600 rounded flex items-center justify-center text-white">
                                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor font-bold"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            </div>
                                            <span>RESPONSABLE: {{ responsable }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-gray-900 text-white text-[9px] font-black rounded uppercase">
                                                {{ assets.length }} {{ assets.length === 1 ? 'REGISTRO' : 'REGISTROS' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <!-- Asset Rows -->
                            <tr v-for="item in assets" :key="item.id" class="border-b border-gray-100 hover:bg-indigo-50/20 transition-all duration-200 group">
                                <td class="px-4 py-4 text-xs font-black text-indigo-700 text-center font-mono tracking-tighter">{{ item.codigo }}</td>
                                <td class="px-4 py-4 text-center">
                                    <div class="inline-flex h-12 w-12 rounded-lg bg-white border-2 border-gray-900 overflow-hidden shadow-sm group-hover:scale-105 transition-transform duration-300">
                                        <img v-if="item.imagen" :src="'/storage/' + item.imagen" class="h-full w-full object-cover" />
                                        <div v-else class="h-full w-full flex items-center justify-center text-gray-200 bg-gray-50">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-[11px] font-black text-gray-900 uppercase leading-tight mb-1">{{ item.descripcion }}</div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ item.marca?.nombre || 'S/M' }}</span>
                                        <span class="h-1 w-1 bg-gray-300 rounded-full"></span>
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ item.modelo?.nombre || 'S/M' }}</span>
                                        <span v-if="item.serie" class="h-1 w-1 bg-gray-300 rounded-full"></span>
                                        <span v-if="item.serie" class="text-[9px] font-bold text-indigo-400 uppercase tracking-tighter font-mono">{{ item.serie }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="text-[10px] font-black text-gray-900 uppercase tracking-tighter">{{ item.categoria?.nombre || 'GENERAL' }}</div>
                                    <div class="text-[9px] font-bold text-indigo-500 uppercase tracking-[0.05em] mt-0.5">{{ item.departamento?.nombre || 'SIN ASIGNAR' }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <div class="text-[11px] text-gray-900 font-black font-mono">
                                        <span class="text-[8px] text-gray-400 mr-0.5 tracking-tighter">C$</span>{{ formatCurrency(item.valor_libros || 0).replace('NIO', '').replace('C$', '').trim() }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span 
                                        class="px-2 py-0.5 text-[8px] font-black rounded border uppercase tracking-[0.1em]"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 border-emerald-300': item.estado_activo?.nombre?.toLowerCase().includes('bueno'),
                                            'bg-orange-50 text-orange-700 border-orange-300': item.estado_activo?.nombre?.toLowerCase().includes('regular'),
                                            'bg-red-50 text-red-700 border-red-300': item.estado_activo?.nombre?.toLowerCase().includes('malo'),
                                            'bg-gray-50 text-gray-700 border-gray-300': !item.estado_activo?.nombre?.toLowerCase().match(/bueno|regular|malo/)
                                        }"
                                    >
                                        {{ item.estado_activo?.nombre }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right">
                                    <div v-if="item.terreno" class="flex items-center justify-end gap-2">
                                        <span class="text-[9px] font-bold text-gray-400 uppercase tracking-tight italic">Gestionar desde módulo Terrenos</span>
                                    </div>
                                    <div v-else class="flex items-center justify-end gap-1.5 opacity-60 group-hover:opacity-100 transition-opacity duration-200">
                                        <button @click="openViewModal(item)" class="p-1.5 bg-gray-900 text-white rounded hover:bg-indigo-600 transition-all shadow-sm flex items-center justify-center" title="Ficha Técnica">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                        <button @click="printAsset(item)" class="p-1.5 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-all border border-gray-200 flex items-center justify-center" title="Imprimir QR">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v1l-1 1h2l-1-1V4zM4 12h1v1H4v-1zm15 0h1v1h-1v-1zM4 19h1v1H4v-1zm15 0h1v1h-1v-1zM4 4h1v1H4V4zm15 0h1v1h-1V4zM9 9h6v6H9V9z"/><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"/></svg>
                                        </button>
                                        <button @click="openEditModal(item)" class="p-1.5 bg-gray-100 text-orange-600 rounded hover:bg-orange-600 hover:text-white transition-all border border-gray-200 flex items-center justify-center" title="Corregir">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                        <button @click="confirmDeletion(item)" class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-600 hover:text-white transition-all border border-red-100 flex items-center justify-center" title="Baja">
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-if="items.length === 0">
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-gray-400 italic bg-white">
                                    No se encontraron activos fijos registrados.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- View Detail Modal -->
        <Modal :show="viewModalOpen" @close="closeViewModal" max-width="5xl">
            <div class="p-0 overflow-hidden rounded-2xl bg-white">
                <!-- Header with Action Toolbar -->
                <div class="print:hidden p-8 bg-white border-b border-gray-100 font-sans">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <img src="/images/logo_alcaldia.jpg" alt="Logo Alcaldía" class="h-16 w-16 object-contain" />
                            <div>
                                <p class="text-[10px] font-bold text-indigo-600 uppercase tracking-[0.2em] mb-1">ALCALDÍA MUNICIPAL DE SÉBACO</p>
                                <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight">
                                    Detalle del Activo: <span class="text-indigo-600">{{ viewingItem?.descripcion }}</span>
                                </h2>
                            </div>
                        </div>
                        <button @click="closeViewModal" class="self-start p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Action Bar -->
                    <div class="flex flex-wrap items-center gap-2 mt-8">
                        <button @click="editFromView" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            Editar Datos
                        </button>
                        <button @click="openHistoryModal" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Historial
                        </button>
                        <button @click="openTransferModal" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Traslado
                        </button>
                        <button @click="openMaintenanceModal" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            Mantenimiento
                        </button>
                        <button @click="openDecommissionModal" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Dar de Baja
                        </button>
                        <button @click="printAsset(viewingItem)" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Imprimir
                        </button>
                        <button @click="closeViewModal" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-lg transition-all shadow-sm uppercase tracking-wider">
                             Volver
                        </button>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-8 bg-white font-sans">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                        <!-- Image Side -->
                        <div class="lg:col-span-4 space-y-6">
                            <div class="rounded-2xl overflow-hidden bg-white border-4 border-gray-900 shadow-xl p-1 relative group">
                                <div class="aspect-square rounded-xl overflow-hidden bg-gray-50 flex items-center justify-center border border-gray-100 relative">
                                    <img v-if="viewingItem?.imagen" :src="'/storage/' + viewingItem.imagen" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-700" />
                                    <div v-else class="text-center p-8">
                                        <svg class="h-16 w-16 text-gray-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sin Documentación Fotográfica</p>
                                    </div>
                                    <div class="absolute bottom-4 right-4 bg-gray-900 text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg">Vista General</div>
                                </div>
                            </div>
                            
                            <div class="bg-indigo-50 rounded-2xl p-6 border-2 border-indigo-100">
                                <h4 class="text-[10px] font-black text-indigo-900 uppercase tracking-[0.2em] mb-4">Estado del Registro</h4>
                                <div class="space-y-4 text-xs">
                                    <div class="flex justify-between items-center pb-2 border-b border-indigo-100">
                                        <span class="font-bold text-gray-500 uppercase tracking-tighter">Estado Físico</span>
                                        <span class="font-black text-indigo-700 uppercase">{{ viewingItem?.estado_activo?.nombre }}</span>
                                    </div>
                                    <div class="flex justify-between items-center pb-2 border-b border-indigo-100">
                                        <span class="font-bold text-gray-500 uppercase tracking-tighter">Categoría</span>
                                        <span class="font-black text-indigo-700 uppercase">{{ viewingItem?.categoria?.nombre }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-gray-500 uppercase tracking-tighter">Uso Institucional</span>
                                        <span class="px-2 py-0.5 bg-indigo-600 text-white rounded text-[9px] font-black uppercase">Activo</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Side -->
                        <div class="lg:col-span-8 space-y-8">
                            <!-- Section I -->
                            <div class="bg-white border-2 border-gray-900 rounded-xl overflow-hidden shadow-sm">
                                <div class="bg-gray-100 px-4 py-2 border-b-2 border-gray-900 flex items-center justify-between">
                                    <span class="text-xs font-black text-gray-900 uppercase tracking-widest">I. Datos de Identificación y Especificaciones</span>
                                    <span class="text-[9px] font-black bg-gray-900 text-white px-2 py-0.5 rounded italic uppercase">CÓDIGO INVENTARIO: {{ viewingItem?.codigo }}</span>
                                </div>
                                <div class="p-6">
                                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight mb-6 border-l-4 border-indigo-600 pl-4">{{ viewingItem?.descripcion }}</h3>
                                    <div class="grid grid-cols-2 gap-x-8 gap-y-6">
                                        <div>
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Marca del Fabricante</p>
                                            <p class="text-sm font-black text-gray-900 uppercase">{{ viewingItem?.marca?.nombre || 'No Especificada' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Modelo / Referencia</p>
                                            <p class="text-sm font-black text-gray-900 uppercase">{{ viewingItem?.modelo?.nombre || 'No Especificado' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Número de Serie único</p>
                                            <p class="text-sm font-black text-indigo-600 font-mono tracking-tighter">{{ viewingItem?.serie || 'SIN SERIE REGISTRADA' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Color / Acabado</p>
                                            <p class="text-sm font-black text-gray-900 uppercase">{{ viewingItem?.color?.nombre || 'General' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section II -->
                            <div class="bg-white border-2 border-gray-900 rounded-xl overflow-hidden shadow-sm">
                                <div class="bg-gray-100 px-4 py-2 border-b-2 border-gray-900">
                                    <span class="text-xs font-black text-gray-900 uppercase tracking-widest">II. Ubicación y Responsabilidad Administrativa</span>
                                </div>
                                <div class="p-6">
                                    <div class="grid grid-cols-2 gap-8">
                                        <div class="space-y-4">
                                            <div>
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Asignado a Departamento</p>
                                                <p class="text-sm font-black text-gray-900 uppercase">{{ viewingItem?.departamento?.nombre }}</p>
                                            </div>
                                            <div>
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Ubicación Física Específica</p>
                                                <div class="flex items-center gap-2 text-indigo-600 font-black text-sm uppercase">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                    {{ viewingItem?.ubicacion?.nombre }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 flex flex-col justify-center">
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Funcionario Responsable</p>
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-full bg-white border-2 border-indigo-600 flex items-center justify-center text-indigo-600 font-black">
                                                    {{ viewingItem?.responsable?.nombre_completo?.charAt(0) || 'R' }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-black text-gray-900 uppercase leading-tight">{{ viewingItem?.responsable?.nombre_completo }}</p>
                                                    <p class="text-[9px] font-bold text-gray-400 uppercase">Personal de Planta</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section III -->
                            <div class="bg-white border-2 border-gray-900 rounded-xl overflow-hidden shadow-sm">
                                <div class="bg-gray-100 px-4 py-2 border-b-2 border-gray-900">
                                    <span class="text-xs font-black text-gray-900 uppercase tracking-widest">III. Registro Financiero{{ viewingItem?.terreno ? '' : ' y Depreciación' }}</span>
                                </div>
                                <div class="p-6">
                                    <div :class="viewingItem?.terreno ? 'grid-cols-2' : 'grid-cols-2 md:grid-cols-3'" class="grid gap-6">
                                        <div>
                                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1 h-10 flex items-center">Costo Adquisición</p>
                                            <p class="text-sm font-black text-gray-900 font-mono tracking-tighter">C$ {{ formatCurrency(viewingItem?.valor_adquisicion).replace('NIO','').replace('C$','').trim() }}</p>
                                        </div>
                                        <div v-if="!viewingItem?.terreno" class="border-x border-gray-100 px-6">
                                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1 h-10 flex items-center">Valor Residual (1%)</p>
                                            <p class="text-sm font-black text-amber-600 font-mono tracking-tighter">C$ {{ formatCurrency(viewingItem?.valor_residual || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                        </div>
                                        <div :class="viewingItem?.terreno ? '' : 'border-l border-gray-100 pl-6'">
                                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1 h-10 flex items-center">Valor en Libros</p>
                                            <p class="text-sm font-black text-emerald-600 font-mono tracking-tighter">C$ {{ formatCurrency(viewingItem?.valor_libros || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                        </div>
                                    </div>

                                    <div v-if="!viewingItem?.terreno" class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-3 gap-6">
                                        <div>
                                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1 h-10 flex items-center">Depreciación Anual</p>
                                            <p class="text-sm font-black text-gray-900 font-mono uppercase tracking-tighter">C$ {{ formatCurrency(viewingItem?.depreciacion_anual || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                        </div>
                                        <div class="border-x border-gray-100 px-6">
                                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1 h-10 flex items-center">Depreciación Mensual</p>
                                            <p class="text-sm font-black text-gray-900 font-mono uppercase tracking-tighter">C$ {{ formatCurrency(viewingItem?.depreciacion_mensual || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-1 h-10 flex items-center">Depreciación Acumulada</p>
                                            <p class="text-sm font-black text-rose-600 font-mono uppercase tracking-tighter">C$ {{ formatCurrency(viewingItem?.depreciacion_acumulada || 0).replace('NIO','').replace('C$','').trim() }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 border-t border-gray-100 p-4 flex justify-between items-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <div class="flex items-center gap-4">
                                        <span>Fecha Reg: {{ viewingItem?.fecha_adquisicion }}</span>
                                        <span class="h-1 w-1 bg-gray-300 rounded-full"></span>
                                        <span>Útil: {{ viewingItem?.vida_util }} AÑOS</span>
                                        <span class="h-1 w-1 bg-gray-300 rounded-full"></span>
                                        <span class="text-gray-900">Fuente: {{ viewingItem?.fuente?.nombre || 'FONDOS PROPIOS' }}</span>
                                    </div>
                                    <span class="text-indigo-600">Garantía Activa</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Create/Edit Modal (Wizard Style) -->
        <Modal :show="modalOpen" @close="closeModal" max-width="5xl">
            <div class="flex flex-col md:flex-row h-auto md:h-[750px] overflow-hidden rounded-3xl bg-white">

                <!-- Sidebar Navigation -->
                <div class="w-full md:w-[320px] bg-white border-b md:border-b-0 md:border-r border-gray-100 p-6 md:p-10 flex flex-col justify-between shrink-0">

                    <div>
                        <div class="flex items-center gap-4 mb-6 md:mb-12">
                            <div class="h-10 w-10 md:h-12 md:w-12 bg-indigo-600 rounded-xl md:rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200">
                                <svg class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4-8-4m16 0v10l-8 4-8-4V7" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-black text-gray-900 leading-tight">{{ editingItem ? 'Editar Activo' : 'Nuevo Activo' }}</h3>
                                <p class="text-[9px] md:text-[10px] font-bold text-gray-600 uppercase tracking-widest leading-none">Gestión de Inventario</p>
                            </div>
                        </div>

                        <nav class="flex md:flex-col gap-2 md:space-y-4 mb-6 md:mb-0 overflow-x-auto md:overflow-visible pb-2 md:pb-0">

                            <button @click="goToStep(1)" :class="activeWizardStep === 1 ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700' : 'text-gray-500 hover:text-gray-800'" class="whitespace-nowrap md:whitespace-normal text-left px-4 py-3 rounded-lg flex items-center gap-4 transition-all">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span class="text-[10px] md:text-xs font-black uppercase tracking-wider">Información General</span>
                            </button>
                            <button @click="goToStep(2)" :class="activeWizardStep === 2 ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700' : 'text-gray-500 hover:text-gray-800'" class="whitespace-nowrap md:whitespace-normal text-left px-4 py-3 rounded-lg flex items-center gap-4 transition-all">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[10px] md:text-xs font-black uppercase tracking-wider">Ubicación</span>
                            </button>
                            <button v-if="!isLandCategory" @click="goToStep(3)" :class="activeWizardStep === 3 ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700' : 'text-gray-500 hover:text-gray-800'" class="whitespace-nowrap md:whitespace-normal text-left px-4 py-3 rounded-lg flex items-center gap-4 transition-all">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="text-[10px] md:text-xs font-black uppercase tracking-wider">Datos Técnicos</span>
                            </button>
                            <button @click="goToStep(4)" :class="activeWizardStep === 4 ? 'bg-indigo-50 border-l-4 border-indigo-600 text-indigo-700' : 'text-gray-500 hover:text-gray-800'" class="whitespace-nowrap md:whitespace-normal text-left px-4 py-3 rounded-lg flex items-center gap-4 transition-all">
                                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="text-[10px] md:text-xs font-black uppercase tracking-wider">Datos Financieros</span>
                            </button>
                        </nav>
                    </div>

                    <button @click="closeModal" class="hidden md:block w-full py-4 border-2 border-gray-100 rounded-xl text-[10px] font-black uppercase tracking-widest text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-all">
                        Cerrar Panel
                    </button>
                </div>


                <!-- Main Content Panel -->
                <div class="flex-1 flex flex-col min-w-0">
                    <div class="flex-1 p-12 overflow-y-auto">
                        <form @submit.prevent="submit" id="wizardForm">
                            <Transition
                                enter-active-class="transition ease-out duration-300 transform"
                                enter-from-class="opacity-0 translate-x-8"
                                enter-to-class="opacity-100 translate-x-0"
                                leave-active-class="transition ease-in duration-200 transform absolute w-full"
                                leave-from-class="opacity-100 translate-x-0"
                                leave-to-class="opacity-0 -translate-x-8"
                                mode="out-in"
                            ><div v-if="activeWizardStep === 1" :key="1" class="space-y-10">
                                <div>
                                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Información General</h2>
                                    <p class="text-sm text-gray-600 font-bold mt-1 uppercase tracking-tight">Identificación y aspectos visuales del activo.</p>
                                </div>


                                <div class="space-y-8">
                                    <div>
                                        <InputLabel value="Nombre del Activo" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                        <TextInput v-model="form.descripcion" class="w-full !rounded-xl !bg-gray-50/50 !border-gray-100 !p-4 focus:!bg-white focus:!ring-indigo-600 transition-all font-bold text-gray-900" placeholder="Ej: Laptop Dell Latitude 5400" required />
                                        <InputError :message="form.errors.descripcion" />
                                    </div>

                                    <div class="grid grid-cols-2 gap-8">
                                            <InputLabel value="Categoría" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <select v-model="form.categoria_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 focus:bg-white focus:ring-indigo-600 transition-all font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="c in catalogs.categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.categoria_id" />
                                        </div>
                                        <div>
                                            <InputLabel value="Estado Físico" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <select v-model="form.estado_activo_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 focus:bg-white focus:ring-indigo-600 transition-all font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="e in catalogs.estados" :key="e.id" :value="e.id">{{ e.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.estado_activo_id" />
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel value="Multimedia del Activo" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                        <div 
                                            class="relative group"
                                            @dragover.prevent="onDragOver"
                                            @dragleave.prevent="onDragLeave"
                                            @drop.prevent="onDrop"
                                        >
                                            <input type="file" @change="onFileChange" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" />
                                            <div 
                                                class="border-2 border-dashed rounded-3xl p-12 text-center transition-all duration-300"
                                                :class="isDragging ? 'border-indigo-500 bg-indigo-50 ring-4 ring-indigo-100 ring-opacity-50 scale-105' : 'border-gray-100 group-hover:border-indigo-200 group-hover:bg-indigo-50/30'"
                                            >
                                                <div v-if="imagePreview" class="relative inline-block">
                                                    <img :src="imagePreview" class="h-32 w-32 object-cover rounded-2xl shadow-xl shadow-gray-200" />
                                                    <div class="absolute -top-2 -right-2 bg-indigo-600 text-white rounded-full p-1 border-4 border-white">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </div>
                                                </div>
                                                <div v-else class="space-y-4">
                                                    <div 
                                                        class="h-16 w-16 rounded-2xl flex items-center justify-center mx-auto transition-all duration-300"
                                                        :class="isDragging ? 'bg-indigo-600 text-white scale-110' : 'bg-gray-50 text-gray-300 group-hover:scale-110'"
                                                    >
                                                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-black text-gray-900" v-if="!isDragging">Click para subir o arrastrar imagen</p>
                                                        <p class="text-sm font-black text-indigo-600" v-else>Suelta la imagen aquí</p>
                                                        <p class="text-[10px] font-bold text-gray-600 uppercase tracking-widest mt-1">JPG, PNG o SVG (MAX 5MB)</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                            </div>
                            <div v-else-if="activeWizardStep === 2" :key="2" class="space-y-10">
                                <div>
                                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Localización y Estado</h2>
                                    <p class="text-sm text-gray-600 font-bold mt-1 uppercase">Ubicación física y responsabilidad administrativa.</p>
                                </div>


                                <div class="space-y-8">
                                    <div class="grid grid-cols-2 gap-8">
                                        <div>
                                            <InputLabel value="Departamento / Área" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <select v-model="form.departamento_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="d in catalogs.departamentos" :key="d.id" :value="d.id">{{ d.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.departamento_id" />
                                        </div>
                                        <div>
                                            <InputLabel value="Ubicación Física" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <select v-model="form.ubicacion_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="u in catalogs.ubicaciones" :key="u.id" :value="u.id">{{ u.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.ubicacion_id" />
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel value="Personal Responsable" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3" />
                                        <select v-model="form.responsable_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 font-bold text-gray-900" required>
                                            <option value="" disabled>Seleccionar responsable...</option>
                                            <option v-for="r in catalogs.responsables" :key="r.id" :value="r.id">{{ r.nombre_completo }}</option>
                                        </select>
                                        <InputError :message="form.errors.responsable_id" />
                                    </div>
                                </div>
                            </div><div v-else-if="activeWizardStep === 3 && !isLandCategory" :key="3" class="space-y-10">
                                <div>
                                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Especificaciones Técnicas</h2>
                                    <p class="text-sm text-gray-600 font-bold mt-1 uppercase">Detalles de serie, marca y modelo.</p>
                                </div>


                                <div class="space-y-8">
                                    <div class="grid grid-cols-2 gap-8">
                                        <div>
                                            <InputLabel value="Marca" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <select v-model="form.marca_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="m in catalogs.marcas" :key="m.id" :value="m.id">{{ m.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.marca_id" />
                                        </div>
                                        <div>
                                            <InputLabel value="Modelo" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <select v-model="form.modelo_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="m in catalogs.modelos" :key="m.id" :value="m.id">{{ m.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.modelo_id" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-8">
                                        <div>
                                            <InputLabel value="Color / Acabado" class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3" />
                                            <select v-model="form.color_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="c in catalogs.colores" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.color_id" />
                                        </div>
                                        <div>
                                            <InputLabel value="Número de Serie" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <TextInput v-model="form.serie" class="w-full !rounded-xl !bg-gray-50/50 !border-gray-100 !p-4 font-bold text-gray-900 uppercase" placeholder="SIN SERIE" />
                                            <InputError :message="form.errors.serie" />
                                        </div>
                                    </div>
                                </div>
                            </div><div v-else-if="activeWizardStep === 4" :key="4" class="space-y-10">
                                <div>
                                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Datos Financieros</h2>
                                    <p class="text-sm text-gray-600 font-bold mt-1 uppercase">Valor de adquisición y parámetros de depreciación.</p>
                                </div>


                                <div class="space-y-8">
                                    <div class="grid grid-cols-2 gap-8">
                                        <div>
                                            <InputLabel value="Proveedor" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <select v-model="form.proveedor_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar proveedor...</option>
                                                <option v-for="p in catalogs.proveedores" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.proveedor_id" />
                                        </div>
                                        <div>
                                            <InputLabel value="Fuente de Financiamiento" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <select v-model="form.fuente_id" class="w-full rounded-xl bg-gray-50/50 border-gray-100 p-4 font-bold text-gray-900" required>
                                                <option value="" disabled>Seleccionar...</option>
                                                <option v-for="f in catalogs.fuentes" :key="f.id" :value="f.id">{{ f.nombre }}</option>
                                            </select>
                                            <InputError :message="form.errors.fuente_id" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-8">
                                        <div>
                                            <InputLabel value="Fecha de Adquisición" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <TextInput type="date" v-model="form.fecha_adquisicion" class="w-full !rounded-xl !bg-gray-50/50 !border-gray-100 !p-4 font-bold text-gray-900" required />
                                            <InputError :message="form.errors.fecha_adquisicion" />
                                        </div>
                                        <div>
                                            <InputLabel value="Valor de Adquisición (C$)" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <TextInput type="number" step="0.01" v-model="form.valor_adquisicion" class="w-full !rounded-xl !bg-white !p-4 font-black text-emerald-600 text-lg border-emerald-100" placeholder="0.00" required />
                                            <InputError :message="form.errors.valor_adquisicion" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-8">
                                        <div>
                                            <InputLabel value="Valor Residual (Estimado)" class="text-[10px] font-black text-gray-700 uppercase tracking-widest mb-3" />
                                            <div class="flex items-center gap-4">
                                                <TextInput type="number" step="0.01" v-model="form.valor_residual" class="flex-1 !rounded-xl !bg-white !border-gray-100 !p-4 font-black text-gray-900" :disabled="autoResidual" required />
                                                <label class="flex items-center gap-2 cursor-pointer">
                                                    <input type="checkbox" v-model="autoResidual" class="rounded-lg text-indigo-600 focus:ring-indigo-500 h-5 w-5 border-gray-200" />
                                                    <span class="text-[10px] font-black text-gray-700 uppercase tracking-tighter">Auto</span>
                                                </label>
                                            </div>
                                            <InputError :message="form.errors.valor_residual" />
                                        </div>
                                    </div>

                                    <div class="p-6 bg-emerald-50 rounded-3xl border border-emerald-100 flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 bg-white rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm border border-emerald-100">
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                            </div>
                                            <div>
                                                <p class="text-[10px] font-black text-emerald-800 uppercase tracking-widest">Vida Útil Estimada</p>
                                                <p class="text-xl font-black text-emerald-600 leading-none mt-1">{{ form.vida_util }} Años <span class="text-[10px] font-bold text-emerald-400 uppercase ml-2">Deprec: {{ (100 / (form.vida_util || 5)).toFixed(0) }}%/Año</span></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div></Transition>
                        <InputError :message="form.errors.total" />
                    </form>
                </div>


                    <!-- Wizard Footer Toolbar -->
                    <div class="h-24 px-12 border-t border-gray-100 flex items-center justify-between bg-gray-50/30">
                        <div class="flex items-center gap-2">
                             <div v-for="n in 4" :key="n" :class="activeWizardStep === n ? 'w-8 bg-indigo-600' : 'w-2 bg-gray-200'" class="h-2 rounded-full transition-all duration-500"></div>
                        </div>

                        <div class="flex items-center gap-4">
                            <button @click="prevStep" v-show="activeWizardStep > 1" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all">Regresar</button>
                            <button @click="nextStep" v-show="activeWizardStep < 4" class="px-8 py-3 bg-white border border-gray-200 text-xs font-black uppercase tracking-widest text-gray-900 rounded-xl hover:bg-gray-50 transition-all shadow-sm">Siguiente</button>
                            
                            <div class="flex items-center gap-3">
                                <button v-show="activeWizardStep === 4 || editingItem" @click="closeModal" class="px-6 py-2.5 text-xs font-black uppercase tracking-widest text-gray-600 hover:text-gray-900 transition-colors">Cancelar</button>
                                <button @click="submit" form="wizardForm" :class="(activeWizardStep === 4 || editingItem) ? 'bg-indigo-900 text-white shadow-indigo-200' : 'hidden'" class="px-8 py-4 !rounded-2xl text-xs font-black uppercase tracking-widest shadow-xl transition-all">
                                    {{ editingItem ? 'Actualizar Cambios' : 'Registrar Activo' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-8">
                <div class="flex items-center gap-4 text-red-600 mb-4">
                    <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">¿Confirmar eliminación?</h2>
                </div>
                <p class="text-gray-600">¿Estás seguro de que deseas eliminar este activo? Esta acción es permanente y no se puede deshacer.</p>
                
                <div class="mt-8 flex justify-end gap-3">
                    <SecondaryButton @click="confirmingDeletion = false" class="!rounded-xl">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteItem" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="!rounded-xl shadow-lg shadow-red-200">
                        Sí, Eliminar Activo
                    </DangerButton>
                </div>
            </div>
        </Modal>
        <Modal :show="historyModalOpen" @close="historyModalOpen = false" max-width="4xl">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 font-black">
                            H
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Expediente de Vida del Activo</h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Código Individual: {{ viewingItem?.codigo }}</p>
                        </div>
                    </div>
                    <button @click="historyModalOpen = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="flex gap-1 mb-8 bg-gray-100 p-1 rounded-xl">
                    <button @click="activeTab = 'movimientos'" :class="{'bg-white shadow-sm text-indigo-600': activeTab === 'movimientos', 'text-gray-500 hover:bg-gray-50': activeTab !== 'movimientos'}" class="flex-1 py-2 text-[11px] font-black uppercase tracking-widest rounded-lg transition-all">Movimientos / Traslados</button>
                    <button @click="activeTab = 'mantenimientos'" :class="{'bg-white shadow-sm text-indigo-600': activeTab === 'mantenimientos', 'text-gray-500 hover:bg-gray-50': activeTab !== 'mantenimientos'}" class="flex-1 py-2 text-[11px] font-black uppercase tracking-widest rounded-lg transition-all">Historial Técnico (Mantenimiento)</button>
                </div>

                <div v-if="activeTab === 'movimientos'" class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                    <div v-if="viewingItem?.movimientos?.length === 0" class="text-center py-12 text-gray-400 italic bg-gray-50 rounded-2xl border border-dashed border-gray-200">No hay traslados registrados para este folio.</div>
                    <div v-for="m in viewingItem?.movimientos" :key="m.id" class="relative pl-8 pb-8 border-l-2 border-indigo-100 last:pb-0">
                        <div class="absolute -left-[9px] top-0 h-4 w-4 rounded-full bg-indigo-600 border-4 border-white"></div>
                        <div class="bg-white border-2 border-gray-900 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-gray-100 px-4 py-2 border-b-2 border-gray-900 flex justify-between items-center">
                                <span class="text-[10px] font-black text-gray-900 uppercase">TRASLADO #{{ m.id }}</span>
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">{{ m.fecha_movimiento }}</span>
                            </div>
                            <div class="p-4 grid grid-cols-2 gap-8 text-[11px]">
                                <div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mb-1">PUNTO DE ORIGEN</p>
                                    <p class="font-black text-gray-900 uppercase">{{ m.ubicacion_origen?.nombre }}</p>
                                    <p class="text-indigo-500 font-bold mt-0.5">{{ m.responsable_origen?.nombre_completo }}</p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter mb-1">PUNTO DE DESTINO</p>
                                    <p class="font-black text-emerald-600 uppercase">{{ m.ubicacion_destino?.nombre }}</p>
                                    <p class="text-indigo-500 font-bold mt-0.5">{{ m.responsable_destino?.nombre_completo }}</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-4 py-1 border-t border-gray-100 text-[9px] text-gray-400 font-bold uppercase text-right">
                                AUTORIZADO POR: {{ m.autorizado_por?.nombre }}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="activeTab === 'mantenimientos'" class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                    <div v-if="viewingItem?.mantenimientos?.length === 0" class="text-center py-12 text-gray-400 italic bg-gray-50 rounded-2xl border border-dashed border-gray-200">No hay registros técnicos disponibles.</div>
                    <div v-for="m in viewingItem?.mantenimientos" :key="m.id" class="p-4 bg-white border-2 border-gray-900 rounded-xl shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 h-10 w-1 bg-emerald-500 group-hover:h-full transition-all"></div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-[9px] font-black bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full uppercase tracking-widest">{{ m.tipo }}</span>
                                <p class="text-[10px] font-black text-gray-900 uppercase mt-2 tracking-tight">{{ m.fecha_inicio }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2">
                                <p class="text-sm font-black text-emerald-600 font-mono tracking-tighter">C$ {{ formatCurrency(m.costo).replace('NIO','').replace('C$','').trim() }}</p>
                                <a :href="route('mantenimientos.print', m.id)" 
                                   target="_blank"
                                   class="p-1.5 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg transition-colors flex items-center gap-1.5 px-2"
                                   title="Imprimir Comprobante">
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9v4a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span class="text-[9px] font-black uppercase tracking-tighter">Imprimir</span>
                                </a>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-[10px]">
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">ESTABLECIMIENTO / PROVEEDOR</p>
                                <p class="font-bold text-gray-900 uppercase">{{ m.proveedor?.nombre }}</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase mb-0.5">RESPONSABLE TÉCNICO</p>
                                <p class="font-bold text-indigo-600 uppercase">{{ m.responsable?.nombre_completo }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>

        <Modal :show="transferModalOpen" @close="transferModalOpen = false" max-width="2xl">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 font-black">
                            I
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Registro de Traslado Oficial</h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Movimiento de Bienes e Inmuebles</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitTransfer" class="space-y-6">
                    <div class="bg-white border-2 border-gray-900 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-gray-100 px-4 py-2 border-b-2 border-gray-900 flex items-center gap-2">
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">I. Datos del Traslado</span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="t_fecha" value="Fecha Efectiva" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                    <TextInput id="t_fecha" type="date" v-model="transferForm.fecha_movimiento" class="mt-0 block w-full bg-gray-50/50" required />
                                </div>
                                <div>
                                    <InputLabel for="t_autorizado" value="Autorizado por" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                     <select id="t_autorizado" v-model="transferForm.autorizado_por" class="block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccionar...</option>
                                        <option v-for="u in catalogs.usuarios" :key="u.id" :value="u.id">{{ u.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="space-y-4 pt-2">
                                <div>
                                    <InputLabel for="t_depto" value="Departamento Destino" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                    <select id="t_depto" v-model="transferForm.departamento_destino_id" class="block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccionar...</option>
                                        <option v-for="d in catalogs.departamentos" :key="d.id" :value="d.id">{{ d.nombre }}</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <InputLabel for="t_ubic" value="Ubicación Física" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                        <select id="t_ubic" v-model="transferForm.ubicacion_destino_id" class="block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50" required>
                                            <option value="" disabled>Seleccionar...</option>
                                            <option v-for="u in catalogs.ubicaciones" :key="u.id" :value="u.id">{{ u.nombre }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel for="t_resp" value="Nuevo Responsable" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                        <select id="t_resp" v-model="transferForm.responsable_destino_id" class="block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50" required>
                                            <option value="" disabled>Seleccionar...</option>
                                            <option v-for="r in catalogs.responsables" :key="r.id" :value="r.id">{{ r.nombre_completo }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <SecondaryButton @click="transferModalOpen = false" class="!rounded-xl">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit" :disabled="transferForm.processing" class="!rounded-xl shadow-lg shadow-indigo-100">
                            Confirmar y Registrar Traslado
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Maintenance Modal -->
        <Modal :show="maintenanceModalOpen" @close="maintenanceModalOpen = false" max-width="2xl">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 font-black">
                            I
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Registro de Mantenimiento</h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Control Técnico de Bienes</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitMaintenance" class="space-y-6">
                    <div class="bg-white border-2 border-gray-900 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-gray-100 px-4 py-2 border-b-2 border-gray-900 flex items-center gap-2">
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">I. Datos Técnicos y Costos</span>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="m_fecha" value="Fecha de Inicio" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                    <TextInput id="m_fecha" type="date" v-model="maintenanceForm.fecha_inicio" class="mt-0 block w-full bg-gray-50/50" required />
                                </div>
                                <div>
                                    <InputLabel for="m_tipo" value="Tipo de Servicio" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                    <select id="m_tipo" v-model="maintenanceForm.tipo" class="block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50" required>
                                        <option value="Preventivo">Preventivo</option>
                                        <option value="Correctivo">Correctivo</option>
                                        <option value="Predictivo">Predictivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="m_costo" value="Monto del Servicio (C$)" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                    <TextInput id="m_costo" type="number" step="0.01" v-model="maintenanceForm.costo" class="mt-0 block w-full bg-gray-50/50 font-black" required />
                                </div>
                                <div>
                                    <InputLabel for="m_prov" value="Taller / Casa Comercial" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                    <select id="m_prov" v-model="maintenanceForm.proveedor_id" class="block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccionar...</option>
                                        <option v-for="p in catalogs.proveedores" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="m_resp" value="Responsable Técnico Municipal" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                <select id="m_resp" v-model="maintenanceForm.responsable_id" class="block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50" required>
                                    <option value="" disabled>Seleccionar...</option>
                                    <option v-for="r in catalogs.responsables" :key="r.id" :value="r.id">{{ r.nombre_completo }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <SecondaryButton @click="maintenanceModalOpen = false" class="!rounded-xl">Cancelar</SecondaryButton>
                        <PrimaryButton type="submit" :disabled="maintenanceForm.processing" class="!rounded-xl shadow-lg shadow-emerald-100">
                            Registrar Mantenimiento
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>

        <Modal :show="decommissionModalOpen" @close="decommissionModalOpen = false" max-width="2xl">
            <div class="p-8">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center text-red-600 font-black">
                            X
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-gray-900 uppercase tracking-tight">Acta de Baja Técnica</h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Retiro Oficial del Inventario</p>
                        </div>
                    </div>
                </div>

                <!-- Warning Banner -->
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl shadow-sm">
                    <div class="flex items-start">
                        <svg class="h-4 w-4 text-red-600 mr-3 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor font-bold"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="text-[11px] font-black text-red-800 uppercase tracking-wide">ADVERTENCIA LEGAL</p>
                            <p class="text-[10px] text-red-700 mt-1 font-medium leading-relaxed">Este proceso es IRREVERSIBLE. El activo dejará de ser parte del patrimonio activo de la institución. Requiere autorización superior.</p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submitDecommission" class="space-y-6">
                    <div class="bg-white border-2 border-gray-900 rounded-xl overflow-hidden shadow-sm font-sans">
                        <div class="bg-gray-100 px-4 py-2 border-b-2 border-gray-900 flex items-center gap-2">
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">I. Justificación de la Baja</span>
                        </div>
                        <div class="p-6 space-y-4 text-xs">
                             <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="b_fecha" value="Fecha de Baja" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                    <TextInput id="b_fecha" type="date" v-model="decommissionForm.fecha_baja" class="mt-0 block w-full bg-gray-50/50" required />
                                </div>
                                <div>
                                    <InputLabel for="b_autorizado" value="Autorizado por" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                    <select id="b_autorizado" v-model="decommissionForm.autorizado_por" class="block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccionar Usuario...</option>
                                        <option v-for="u in catalogs.usuarios" :key="u.id" :value="u.id">{{ u.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <InputLabel for="b_motivo" value="Descripción del Motivo / Estado de Desecho" class="text-[10px] font-bold text-gray-500 uppercase mb-1" />
                                <textarea id="b_motivo" v-model="decommissionForm.motivo" class="mt-0 block w-full border-gray-200 rounded-lg text-sm bg-gray-50/50 focus:bg-white transition-all" rows="4" required placeholder="EJ: EQUIPO OBSOLETO, DAÑO IRREPARABLE EN TARJETA MADRE..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <SecondaryButton @click="decommissionModalOpen = false" class="!rounded-xl">Cancelar</SecondaryButton>
                        <DangerButton type="submit" :disabled="decommissionForm.processing" class="!rounded-xl shadow-lg shadow-red-100 uppercase text-[10px] font-black tracking-widest px-6">
                            Confirmar Baja Definitiva
                        </DangerButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>

<Teleport to="body">
            <div v-if="viewingItem" id="printable-area" class="hidden print:block bg-white p-6 md:p-12 max-w-4xl mx-auto">
                <!-- Header -->
                <div class="flex items-start justify-between border-b-2 border-black pb-4 mb-6">
                    <div class="flex items-center gap-4">
                        <img src="/logo_sebaco.jpg" alt="Logo Alcaldía" class="h-24 w-auto object-contain" />
                        <div>
                            <h1 class="text-xl font-bold uppercase tracking-wide text-black">Alcaldía Municipal de Sébaco</h1>
                            <h2 class="text-sm font-bold uppercase text-gray-600">Departamento de Activos Fijos</h2>
                            <h3 class="text-lg font-black uppercase underline mt-2">Ficha Técnica de Activo Fijo</h3>
                        </div>
                    </div>
                    <div class="flex flex-col items-center justify-center border border-black p-2 rounded w-32 h-32">
                        <!-- Placeholder QR - In a real app, use a QR library -->
                         <svg class="h-20 w-20 text-black mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zm-6 0H6.414a1 1 0 00-.707.293L4.293 16.707a1 1 0 00.293.707H6a1 1 0 001-1v-1.414zM22 6v4m-6 0h6m-6-6h6m-16 6h2m-2-2h2m0 4H6m4-4h2m-2-2h2m0 4h-2m-2 4h4m4-14h-4m4 4h-4m-4-4H4m4 4H4M6 6h2m-2 2h2m10 8h2m-2 2h2m-2-2h-2m2 4h-2m4-6h-2m2 2h-2m-6-6h-2m2 2h-2m2 6h2m-2 2h2m-2-2h-2m2 4h-2"/></svg>
                        <span class="text-[8px] font-bold uppercase text-center">Escanear Detalle</span>
                    </div>
                </div>

                <!-- Section I: Identification -->
                <div class="mb-6">
                    <div class="bg-gray-100 border border-black px-2 py-1 mb-0 border-b-0">
                        <h4 class="text-xs font-bold uppercase text-center">I. Datos de Identificación</h4>
                    </div>
                    <table class="w-full border-collapse border border-black text-xs">
                        <tr>
                            <td class="border border-black p-2 font-bold bg-white w-1/6">Código Inventario:</td>
                            <td class="border border-black p-2 w-1/3 text-lg font-mono font-bold">{{ viewingItem?.codigo }}</td>
                            <td class="border border-black p-2 font-bold bg-white w-1/6">Categoría:</td>
                            <td class="border border-black p-2 w-1/3 uppercase">{{ viewingItem?.categoria?.nombre }}</td>
                            <td rowspan="2" class="border border-black p-0 w-32 text-center align-middle">
                                <div class="w-full h-full min-h-[80px] flex items-center justify-center bg-gray-50">
                                   <img v-if="viewingItem?.imagen" :src="'/storage/' + viewingItem.imagen" class="max-h-24 max-w-full object-contain" />
                                   <span v-else class="text-[9px] text-gray-400 italic">Sin Foto</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-black p-2 font-bold bg-white">Nombre del Bien:</td>
                            <td class="border border-black p-2 uppercase font-bold">{{ viewingItem?.descripcion }}</td>
                            <td class="border border-black p-2 font-bold bg-white">Estado Actual:</td>
                            <td class="border border-black p-2 uppercase">{{ viewingItem?.estado_activo?.nombre }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Section II: Specs -->
                <div class="mb-6">
                    <div class="bg-gray-100 border border-black border-b-0 px-2 py-1">
                        <h4 class="text-xs font-bold uppercase text-center">II. Especificaciones Técnicas</h4>
                    </div>
                    <table class="w-full border-collapse border border-black text-xs">
                         <tr>
                            <td class="border border-black p-2 font-bold bg-white w-1/4">Marca:</td>
                            <td class="border border-black p-2 w-1/4 uppercase">{{ viewingItem?.marca?.nombre }}</td>
                            <td class="border border-black p-2 font-bold bg-white w-1/4">Modelo:</td>
                            <td class="border border-black p-2 w-1/4 uppercase">{{ viewingItem?.modelo?.nombre }}</td>
                        </tr>
                         <tr>
                            <td class="border border-black p-2 font-bold bg-white w-1/4">Color:</td>
                            <td class="border border-black p-2 w-1/4 uppercase">{{ viewingItem?.color?.nombre }}</td>
                            <td class="border border-black p-2 font-bold bg-white w-1/4">Serie:</td>
                            <td class="border border-black p-2 w-1/4 uppercase">{{ viewingItem?.serie || 'N/A' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Section III: Location -->
                <div class="mb-6">
                     <div class="bg-gray-100 border border-black border-b-0 px-2 py-1">
                        <h4 class="text-xs font-bold uppercase text-center">III. Ubicación y Responsabilidad</h4>
                    </div>
                    <table class="w-full border-collapse border border-black text-xs">
                        <tr>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Departamento / Área:</td>
                             <td class="border border-black p-2 uppercase" colspan="3">{{ viewingItem?.departamento?.nombre }}</td>
                        </tr>
                        <tr>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Ubicación Física:</td>
                             <td class="border border-black p-2 uppercase" colspan="3">{{ viewingItem?.ubicacion?.nombre }}</td>
                        </tr>
                         <tr>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Responsable Asignado:</td>
                             <td class="border border-black p-2 uppercase font-bold" colspan="3">{{ viewingItem?.responsable?.nombre_completo }}</td>
                        </tr>
                    </table>
                </div>

                 <!-- Section IV: Financial -->
                <div class="mb-8">
                     <div class="bg-gray-100 border border-black border-b-0 px-2 py-1">
                        <h4 class="text-xs font-bold uppercase text-center">IV. Información Financiera</h4>
                    </div>
                    <table class="w-full border-collapse border border-black text-xs">
                        <tr>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Fecha Adquisición:</td>
                             <td class="border border-black p-2 uppercase">{{ new Date(viewingItem?.fecha_adquisicion).toLocaleDateString() }}</td>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Vida Útil:</td>
                             <td class="border border-black p-2 uppercase">{{ viewingItem?.vida_util }} Años</td>
                        </tr>
                        <tr>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Valor Adquisición:</td>
                             <td class="border border-black p-2 uppercase font-mono">C$ {{ formatCurrency(viewingItem?.valor_adquisicion).replace('NIO','').replace('C$','') }}</td>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Valor Residual:</td>
                             <td class="border border-black p-2 uppercase font-mono">C$ {{ formatCurrency(viewingItem?.valor_residual).replace('NIO','').replace('C$','') }}</td>
                        </tr>
                         <tr>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Fuente Financ.:</td>
                             <td class="border border-black p-2 uppercase">{{ viewingItem?.fuente?.nombre }}</td>
                             <td class="border border-black p-2 font-bold bg-white w-1/4">Valor Actual (Libros):</td>
                             <td class="border border-black p-2 uppercase font-mono font-bold">C$ {{ formatCurrency(viewingItem?.valor_libros).replace('NIO','').replace('C$','') }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Signatures -->
                <div class="grid grid-cols-2 gap-20 mt-16 text-xs">
                     <div class="text-center">
                        <div class="border-t border-black pt-2 mx-8"></div>
                        <p class="font-bold uppercase">{{ viewingItem?.responsable?.nombre_completo }}</p>
                        <p class="text-gray-600">Responsable del Bien</p>
                     </div>
                     <div class="text-center">
                        <div class="border-t border-black pt-2 mx-8"></div>
                        <p class="font-bold uppercase">Responsable de Activos Fijos</p>
                        <p class="text-gray-600">Autorizado Por</p>
                     </div>
                </div>

                 <!-- Footer -->
                <div class="mt-8 pt-4 border-t-2 border-gray-100 flex justify-between text-[10px] text-gray-400">
                    <p>Impreso el: {{ new Date().toLocaleString() }}</p>
                    <p>Sistema Integrado de Activos Fijos (SIAFSEB)</p>
                </div>
            </div>
</Teleport>
</template>

<style>
/* Estilos de impresión para la ficha técnica */
@media print {
    body > *:not(#printable-area) {
        display: none !important;
    }
    
    #printable-area {
        display: block !important;
        background: white;
        width: 100%;
        height: auto;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 10000;
    }

    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>
