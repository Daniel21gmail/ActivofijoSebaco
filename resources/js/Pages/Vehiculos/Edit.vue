<script setup>
import { ref, watch, onMounted } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    vehiculo: Object,
    catalogs: Object,
});

const activeTab = ref('general');
const autoResidual = ref(false);
const imagePreview = ref(props.vehiculo.activo_fijo?.imagen ? '/storage/' + props.vehiculo.activo_fijo.imagen : null);

const form = useForm({
    // Activo Fijo Fields
    codigo: props.vehiculo.activo_fijo?.codigo || '',
    descripcion: props.vehiculo.activo_fijo?.descripcion || '',
    categoria_id: props.vehiculo.activo_fijo?.categoria_id || '',
    departamento_id: props.vehiculo.activo_fijo?.departamento_id || '',
    fecha_adquisicion: props.vehiculo.activo_fijo?.fecha_adquisicion || '',
    valor_adquisicion: props.vehiculo.activo_fijo?.valor_adquisicion || '',
    vida_util: props.vehiculo.activo_fijo?.vida_util || '',
    valor_residual: props.vehiculo.activo_fijo?.valor_residual || '',
    serie: props.vehiculo.activo_fijo?.serie || '',
    marca_id: props.vehiculo.activo_fijo?.marca_id || '',
    modelo_id: props.vehiculo.activo_fijo?.modelo_id || '',
    color_id: props.vehiculo.activo_fijo?.color_id || '',
    fuente_id: props.vehiculo.activo_fijo?.fuente_id || '',
    proveedor_id: props.vehiculo.activo_fijo?.proveedor_id || '',
    responsable_id: props.vehiculo.activo_fijo?.responsable_id || '',
    estado_activo_id: props.vehiculo.activo_fijo?.estado_activo_id || '',
    ubicacion_id: props.vehiculo.activo_fijo?.ubicacion_id || '',
    imagen: null,
    // Vehiculo Specific Fields
    placa: props.vehiculo.placa || '',
    chasis: props.vehiculo.chasis || '',
    motor: props.vehiculo.motor || '',
    numero_circulacion: props.vehiculo.numero_circulacion || '',
    _method: 'PUT',
});

watch(() => form.valor_adquisicion, (newVal) => {
    if (newVal && autoResidual.value) {
        form.valor_residual = (parseFloat(newVal) * 0.01).toFixed(2);
    }
});

watch(autoResidual, (newVal) => {
    if (newVal && form.valor_adquisicion) {
        form.valor_residual = (parseFloat(form.valor_adquisicion) * 0.01).toFixed(2);
    }
});

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.imagen = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};

const submit = () => {
    form.post(route('vehiculos.update', props.vehiculo.id), {
        forceFormData: true,
    });
};

const tabs = [
    { id: 'general', name: 'Información General', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { id: 'technical', name: 'Especificaciones Técnicas', icon: 'M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z' },
    { id: 'financial', name: 'Datos Financieros', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
];
</script>

<template>
    <Head title="Editar Vehículo" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">EDITAR VEHÍCULO</h1>
                    <p class="text-gray-500 mt-1">Modifique la información necesaria para el vehículo con placa <span class="font-black text-indigo-600">{{ props.vehiculo.placa }}</span>.</p>
                </div>
                <Link :href="route('vehiculos.index')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver al Listado
                </Link>
            </div>

            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="flex border-b border-gray-100 bg-gray-50/50">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                        :class="[
                            'flex-1 flex items-center justify-center py-5 px-4 text-sm font-bold uppercase tracking-wider transition-all border-b-2',
                            activeTab === tab.id ? 'border-indigo-600 text-indigo-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100/50'
                        ]">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon"/></svg>
                        {{ tab.name }}
                    </button>
                </div>

                <form @submit.prevent="submit" class="p-10">
                    <!-- Tab: General Info -->
                    <div v-show="activeTab === 'general'" class="space-y-8 animate-in fade-in duration-500">
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                            <div class="md:col-span-1">
                                <div class="bg-gray-50 rounded-3xl p-8 border-2 border-dashed border-gray-200 text-center">
                                    <InputLabel value="Imagen del Vehículo" class="mb-4 text-center text-xs font-black uppercase tracking-widest text-gray-700" />
                                    <div class="aspect-square rounded-2xl bg-white border border-gray-100 shadow-sm flex items-center justify-center overflow-hidden mb-6 group relative">
                                        <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                        <div v-else class="text-gray-300">
                                            <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                    <label class="w-full inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-indigo-700 cursor-pointer transition-all shadow-lg shadow-indigo-200">
                                        Actualizar Foto
                                        <input type="file" @change="onFileChange" class="hidden" accept="image/*" />
                                    </label>
                                    <p class="text-[10px] text-gray-600 mt-4 leading-relaxed uppercase font-bold">Formatos aceptados: JPG, PNG. Máx. 2MB</p>
                                </div>
                            </div>

                            <div class="md:col-span-2 grid grid-cols-2 gap-x-8 gap-y-6">
                                <div class="col-span-2">
                                    <InputLabel for="descripcion" value="Descripción Detallada del Vehículo" class="text-gray-700 font-bold" />
                                    <TextInput id="descripcion" v-model="form.descripcion" class="mt-1 block w-full bg-gray-50/50" placeholder="Ej: Camioneta Toyota Hilux 4x4, Blanca" required />
                                    <InputError :message="form.errors.descripcion" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="codigo" value="Código de Inventario (Solo Lectura)" class="text-gray-700 font-bold" />
                                    <TextInput id="codigo" v-model="form.codigo" class="mt-1 block w-full bg-gray-100 font-black text-gray-500 cursor-not-allowed" placeholder="VEH-001" readonly />
                                    <InputError :message="form.errors.codigo" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="departamento_id" value="Departamento Asignado" class="text-gray-700 font-bold" />
                                    <select id="departamento_id" v-model="form.departamento_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50 focus:ring-indigo-600" required>
                                        <option value="" disabled>Seleccione un departamento...</option>
                                        <option v-for="d in catalogs.departamentos" :key="d.id" :value="d.id">{{ d.nombre }}</option>
                                    </select>
                                    <InputError :message="form.errors.departamento_id" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="ubicacion_id" value="Ubicación Física" class="text-gray-700 font-bold" />
                                    <select id="ubicacion_id" v-model="form.ubicacion_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50 focus:ring-indigo-600" required>
                                        <option value="" disabled>Seleccione ubicación...</option>
                                        <option v-for="u in catalogs.ubicaciones" :key="u.id" :value="u.id">{{ u.nombre }}</option>
                                    </select>
                                    <InputError :message="form.errors.ubicacion_id" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="responsable_id" value="Funcionario Responsable" class="text-gray-700 font-bold" />
                                    <select id="responsable_id" v-model="form.responsable_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50 focus:ring-indigo-600" required>
                                        <option value="" disabled>Seleccione responsable...</option>
                                        <option v-for="r in catalogs.responsables" :key="r.id" :value="r.id">{{ r.nombre_completo }}</option>
                                    </select>
                                    <InputError :message="form.errors.responsable_id" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="categoria_id" value="Categoría de Activo" class="text-gray-700 font-bold" />
                                    <select id="categoria_id" v-model="form.categoria_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50 focus:ring-indigo-600" required>
                                        <option value="" disabled>Seleccione categoría...</option>
                                        <option v-for="c in catalogs.categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                                    </select>
                                    <InputError :message="form.errors.categoria_id" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="estado_activo_id" value="Estado de Conservación" class="text-gray-700 font-bold" />
                                    <select id="estado_activo_id" v-model="form.estado_activo_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50 focus:ring-indigo-600" required>
                                        <option value="" disabled>Seleccione estado...</option>
                                        <option v-for="e in catalogs.estados" :key="e.id" :value="e.id">{{ e.nombre }}</option>
                                    </select>
                                    <InputError :message="form.errors.estado_activo_id" class="mt-1" />
                                </div>
                            </div>
                         </div>
                    </div>

                    <!-- Tab: Technical Specs -->
                    <div v-show="activeTab === 'technical'" class="space-y-12 animate-in slide-in-from-right-10 duration-500">
                        <section>
                            <h3 class="flex items-center text-xs font-black text-indigo-600 uppercase tracking-widest mb-6 pb-2 border-b border-indigo-50">
                                <span class="bg-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-lg mr-3 shadow-lg shadow-indigo-100 italic">1</span>
                                Datos Legales y de Identificación
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <InputLabel for="placa" value="Número de Placa" class="text-gray-700 font-bold" />
                                    <TextInput id="placa" v-model="form.placa" class="mt-1 block w-full uppercase font-black" placeholder="Ej: M 123456" required />
                                    <InputError :message="form.errors.placa" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="serie" value="Número de Serie (General)" class="text-gray-700 font-bold" />
                                    <TextInput id="serie" v-model="form.serie" class="mt-1 block w-full uppercase" placeholder="Serie AF..." />
                                    <InputError :message="form.errors.serie" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="chasis" value="Número de Chasis" class="text-gray-700 font-bold" />
                                    <TextInput id="chasis" v-model="form.chasis" class="mt-1 block w-full uppercase" placeholder="VIN..." required />
                                    <InputError :message="form.errors.chasis" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="motor" value="Número de Motor" class="text-gray-700 font-bold" />
                                    <TextInput id="motor" v-model="form.motor" class="mt-1 block w-full uppercase" placeholder="MOTOR-..." required />
                                    <InputError :message="form.errors.motor" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="numero_circulacion" value="Número de Circulación" class="text-gray-700 font-bold" />
                                    <TextInput id="numero_circulacion" v-model="form.numero_circulacion" class="mt-1 block w-full uppercase font-bold" placeholder="Ej: 12345678" required />
                                    <InputError :message="form.errors.numero_circulacion" class="mt-1" />
                                </div>
                            </div>
                        </section>

                        <section>
                            <h3 class="flex items-center text-xs font-black text-indigo-600 uppercase tracking-widest mb-6 pb-2 border-b border-indigo-50">
                                <span class="bg-indigo-600 text-white w-6 h-6 flex items-center justify-center rounded-lg mr-3 shadow-lg shadow-indigo-100 italic">2</span>
                                Fabricación y Estética
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <div>
                                    <InputLabel for="marca_id" value="Marca" class="text-gray-700 font-bold" />
                                    <select id="marca_id" v-model="form.marca_id" class="mt-1 block w-full border-gray-300 rounded-xl bg-gray-50/50" required>
                                        <option value="" disabled>Seleccionar...</option>
                                        <option v-for="m in catalogs.marcas" :key="m.id" :value="m.id">{{ m.nombre }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="modelo_id" value="Modelo" class="text-gray-700 font-bold" />
                                    <select id="modelo_id" v-model="form.modelo_id" class="mt-1 block w-full border-gray-300 rounded-xl bg-gray-50/50" required>
                                        <option value="" disabled>Seleccionar...</option>
                                        <option v-for="m in catalogs.modelos" :key="m.id" :value="m.id">{{ m.nombre }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="color_id" value="Color Exterior" class="text-gray-700 font-bold" />
                                    <select id="color_id" v-model="form.color_id" class="mt-1 block w-full border-gray-300 rounded-xl bg-gray-50/50" required>
                                        <option value="" disabled>Seleccionar...</option>
                                        <option v-for="c in catalogs.colores" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                        </section>
                    </div>

                    <!-- Tab: Financial Data -->
                    <div v-show="activeTab === 'financial'" class="space-y-10 animate-in slide-in-from-right-10 duration-500">
                        <div class="p-10 bg-indigo-50/50 rounded-[40px] border border-indigo-100">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-y-10 gap-x-12">
                                <div class="col-span-1 border-r border-indigo-100 pr-12">
                                    <InputLabel for="fuente_id" value="Fuente de Financiamiento" class="text-gray-700 font-bold" />
                                    <select id="fuente_id" v-model="form.fuente_id" class="mt-2 block w-full border-gray-300 rounded-2xl bg-white shadow-sm font-bold text-gray-700" required>
                                        <option value="" disabled>Seleccione una fuente...</option>
                                        <option v-for="f in catalogs.fuentes" :key="f.id" :value="f.id">{{ f.nombre }}</option>
                                    </select>
                                    <p class="text-[10px] text-gray-600 mt-3 leading-relaxed uppercase font-black">Determine el origen de los fondos para la adquisición.</p>
                                </div>

                                <div class="col-span-2 grid grid-cols-2 gap-8">
                                    <div class="col-span-2">
                                        <InputLabel for="proveedor_id" value="Proveedor o Concesionario" class="text-gray-700 font-bold" />
                                        <select id="proveedor_id" v-model="form.proveedor_id" class="mt-2 block w-full border-gray-300 rounded-2xl bg-white shadow-sm font-bold text-gray-700" required>
                                            <option value="" disabled>Seleccione un proveedor...</option>
                                            <option v-for="p in catalogs.proveedores" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel for="fecha_adquisicion" value="Fecha de Compra / Registro" class="text-gray-700 font-bold" />
                                        <TextInput id="fecha_adquisicion" type="date" v-model="form.fecha_adquisicion" class="mt-2 block w-full bg-white text-center font-bold" required />
                                    </div>
                                    <div>
                                        <InputLabel for="valor_adquisicion" value="Valor de Adquisición (C$)" class="text-gray-700 font-bold" />
                                        <TextInput id="valor_adquisicion" type="number" step="0.01" v-model="form.valor_adquisicion" class="mt-2 block w-full bg-white font-black text-emerald-600 text-lg" placeholder="0.00" required />
                                    </div>
                                    <div>
                                        <InputLabel for="vida_util" value="Vida Útil Estima (Años)" class="text-gray-700 font-bold" />
                                        <TextInput id="vida_util" type="number" v-model="form.vida_util" class="mt-2 block w-full bg-white text-center font-bold" placeholder="5" required />
                                    </div>
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <InputLabel for="valor_residual" value="Valor Residual" class="text-gray-700 font-bold" />
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input type="checkbox" v-model="autoResidual" class="rounded-lg h-4 w-4 text-indigo-600 focus:ring-0" />
                                                <span class="text-[10px] font-black text-gray-700 uppercase">AUTO (1%)</span>
                                            </label>
                                        </div>
                                        <TextInput id="valor_residual" type="number" step="0.01" v-model="form.valor_residual" class="mt-1 block w-full !bg-white !text-gray-900 border-gray-100 font-black" :disabled="autoResidual" required />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex items-center justify-between pt-10 border-t border-gray-100">
                        <div class="flex items-center text-gray-400 text-xs font-bold uppercase tracking-widest">
                            <span class="mr-2 text-gray-600">SIAFSEB v1.0</span>
                            <span class="px-2 py-1 bg-gray-200 rounded-lg text-[10px] text-gray-900">{{ activeTab.toUpperCase() }} SECTION</span>
                        </div>
                        <div class="flex gap-4">
                            <SecondaryButton @click="$inertia.visit(route('vehiculos.index'))" :disabled="form.processing" class="!rounded-2xl px-10 !h-14 font-black">
                                CANCELAR
                            </SecondaryButton>
                            
                            <PrimaryButton @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="!rounded-2xl px-12 !h-14 !bg-indigo-600 hover:!bg-indigo-700 shadow-xl shadow-indigo-200 font-black">
                                <svg v-if="!form.processing" class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span v-if="form.processing">PROCESANDO...</span>
                                <span v-else>ACTUALIZAR VEHÍCULO</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
