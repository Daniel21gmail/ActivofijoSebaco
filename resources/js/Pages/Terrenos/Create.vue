<script setup>
import { ref, watch } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    catalogs: Object,
});

const activeTab = ref('property');
const imagePreview = ref(null);
const hasErrors = ref(false);

const form = useForm({
    // Activo Fijo Fields
    codigo: '',
    descripcion: '',
    serie: '',
    categoria_id: '',
    departamento_id: '',
    fecha_adquisicion: '',
    valor_adquisicion: '',
    vida_util: '',
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
    // Terreno Specific Fields
    area_m2: '',
    folio_real: '',
    catastro: '',
    uso: '',
});

watch(() => form.categoria_id, async (newVal) => {
    if (newVal) {
        try {
            const response = await axios.get(route('activos-fijos.next-code'), {
                params: { categoria_id: newVal }
            });
            form.codigo = response.data.code;
        } catch (error) {
            console.error('Error al obtener el siguiente código:', error);
            // Fallback to catalog code if API fails
            const category = props.catalogs.categorias.find(c => c.id === newVal);
            if (category) {
                form.codigo = category.codigo;
            }
        }
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
    hasErrors.value = false;
    const fieldTabs = {
        'folio_real': 'property',
        'catastro': 'property',
        'area_m2': 'property',
        'uso': 'property',
        'descripcion': 'general',
        'codigo': 'general',
        'departamento_id': 'general',
        'ubicacion_id': 'general',
        'responsable_id': 'general',
        'categoria_id': 'general',
        'estado_activo_id': 'general',
        'imagen': 'general',
        'fuente_id': 'financial',
        'proveedor_id': 'financial',
        'fecha_adquisicion': 'financial',
        'valor_adquisicion': 'financial',

    };

    form.post(route('terrenos.store'), {
        forceFormData: true,
        onError: (errors) => {
            hasErrors.value = true;
            const firstErrorField = Object.keys(errors)[0];
            if (firstErrorField && fieldTabs[firstErrorField]) {
                activeTab.value = fieldTabs[firstErrorField];
            }
        }
    });
};

const tabs = [
    { id: 'property', name: 'Datos de Propiedad', icon: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z' },
    { id: 'general', name: 'Información del Activo', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { id: 'financial', name: 'Datos Financieros', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
];
</script>

<template>
    <Head title="Registrar Terreno" />

    <AuthenticatedLayout>
        <div class="max-w-7xl mx-auto py-6">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">REGISTRAR NUEVO TERRENO</h1>
                    <p class="text-gray-500 mt-1">Gestión profesional de activos territoriales y bienes inmuebles.</p>
                </div>
                <Link :href="route('terrenos.index')" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl font-bold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Volver al Listado
                </Link>
            </div>

            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
                <div class="flex border-b border-gray-100 bg-gray-50/50">
                    <button v-for="tab in tabs" :key="tab.id" @click="activeTab = tab.id"
                        :class="[
                            'flex-1 flex items-center justify-center py-5 px-4 text-sm font-bold uppercase tracking-wider transition-all border-b-2',
                            activeTab === tab.id ? 'border-emerald-600 text-emerald-600 bg-white' : 'border-transparent text-gray-400 hover:text-gray-600 hover:bg-gray-100/50'
                        ]">
                        <svg class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon"/></svg>
                        {{ tab.name }}
                    </button>
                </div>

                <div v-if="hasErrors" class="mx-10 mt-6 p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-2xl border border-red-200 flex items-center gap-3 animate-pulse">
                     <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                     <span class="font-bold">Por favor, revise el formulario. Hay campos obligatorios pendientes o con errores en la pestaña actual.</span>
                </div>

                <form @submit.prevent="submit" class="p-10" novalidate>
                    <!-- Tab: Property Data -->
                    <div v-show="activeTab === 'property'" class="space-y-12 animate-in fade-in duration-500">
                        <div class="bg-emerald-50/30 rounded-[40px] p-10 border border-emerald-100">
                            <div class="flex items-center gap-4 mb-10">
                                <div class="h-14 w-14 bg-emerald-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Datos Técnicos y Legales</h3>
                                    <p class="text-xs text-emerald-600 font-bold uppercase tracking-widest mt-1">Información catastral y registral del bien</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8">
                                <div>
                                    <InputLabel for="folio_real" value="Folio Real (Registro Público)" />
                                    <TextInput id="folio_real" v-model="form.folio_real" class="mt-2 block w-full bg-white font-black text-emerald-700 uppercase" placeholder="Ej: 123-A" required />
                                    <InputError :message="form.errors.folio_real" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="catastro" value="Código de Catastro" />
                                    <TextInput id="catastro" v-model="form.catastro" class="mt-2 block w-full bg-white uppercase" placeholder="CAT-000-000" required />
                                    <InputError :message="form.errors.catastro" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="area_m2" value="Área Total (en Metros Cuadrados)" />
                                    <div class="relative">
                                        <TextInput id="area_m2" type="number" step="0.01" v-model="form.area_m2" class="mt-2 block w-full bg-white pr-12 font-black text-lg" placeholder="0.00" required />
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pt-1 pointer-events-none text-gray-400 font-bold text-xs uppercase">m²</div>
                                    </div>
                                    <InputError :message="form.errors.area_m2" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="uso" value="Uso de Suelo / Destino" />
                                    <TextInput id="uso" v-model="form.uso" class="mt-2 block w-full bg-white" placeholder="Ej: Oficinas Centrales" required />
                                    <InputError :message="form.errors.uso" class="mt-1" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab: General Info -->
                    <div v-show="activeTab === 'general'" class="space-y-8 animate-in slide-in-from-right-10 duration-500">
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                            <div class="md:col-span-1">
                                <div class="bg-gray-50 rounded-3xl p-8 border-2 border-dashed border-gray-200 text-center">
                                    <InputLabel value="Planos o Fotografía" class="mb-4 text-center text-xs font-black uppercase tracking-widest" />
                                    <div class="aspect-square rounded-2xl bg-white border border-gray-100 shadow-sm flex items-center justify-center overflow-hidden mb-6 group relative">
                                        <img v-if="imagePreview" :src="imagePreview" class="w-full h-full object-cover" />
                                        <div v-else class="text-gray-300">
                                            <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    </div>
                                    <label class="w-full inline-flex items-center justify-center px-6 py-3 bg-emerald-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-emerald-700 cursor-pointer transition-all shadow-lg shadow-emerald-200">
                                        Cargar Imagen
                                        <input type="file" @change="onFileChange" class="hidden" accept="image/*" />
                                    </label>
                                </div>
                            </div>

                            <div class="md:col-span-2 grid grid-cols-2 gap-x-8 gap-y-6">
                                <div class="col-span-2">
                                    <InputLabel for="descripcion" value="Descripción del Inmueble" />
                                    <TextInput id="descripcion" v-model="form.descripcion" class="mt-1 block w-full bg-gray-50/50" placeholder="Ej: Terreno Central km 10" required />
                                    <InputError :message="form.errors.descripcion" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="codigo" value="Código Interno" />
                                    <TextInput id="codigo" v-model="form.codigo" class="mt-1 block w-full bg-gray-50/50 font-black text-emerald-700" placeholder="TER-001" required />
                                    <InputError :message="form.errors.codigo" class="mt-1" />
                                </div>
                                <div>
                                    <InputLabel for="departamento_id" value="Departamento" />
                                    <select id="departamento_id" v-model="form.departamento_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccione...</option>
                                        <option v-for="d in catalogs.departamentos" :key="d.id" :value="d.id">{{ d.nombre }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="ubicacion_id" value="Ubicación Detallada" />
                                    <select id="ubicacion_id" v-model="form.ubicacion_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccione...</option>
                                        <option v-for="u in catalogs.ubicaciones" :key="u.id" :value="u.id">{{ u.nombre }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="responsable_id" value="Custodio / Responsable" />
                                    <select id="responsable_id" v-model="form.responsable_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccione...</option>
                                        <option v-for="r in catalogs.responsables" :key="r.id" :value="r.id">{{ r.nombre_completo }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="categoria_id" value="Categoría" />
                                    <select id="categoria_id" v-model="form.categoria_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccione...</option>
                                        <option v-for="c in catalogs.categorias" :key="c.id" :value="c.id">{{ c.nombre }}</option>
                                    </select>
                                </div>
                                <div>
                                    <InputLabel for="estado_activo_id" value="Estado Legal" />
                                    <select id="estado_activo_id" v-model="form.estado_activo_id" class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm bg-gray-50/50" required>
                                        <option value="" disabled>Seleccione...</option>
                                        <option v-for="e in catalogs.estados" :key="e.id" :value="e.id">{{ e.nombre }}</option>
                                    </select>
                                </div>
                            </div>
                         </div>
                    </div>

                    <!-- Tab: Financial Data -->
                    <div v-show="activeTab === 'financial'" class="space-y-10 animate-in slide-in-from-right-10 duration-500">
                        <div class="p-10 bg-emerald-50/30 rounded-[40px] border border-emerald-100">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-y-10 gap-x-12">
                                <div class="col-span-1 border-r border-emerald-100 pr-12">
                                    <InputLabel for="fuente_id" value="Fuente Financiamiento" />
                                    <select id="fuente_id" v-model="form.fuente_id" class="mt-2 block w-full border-gray-300 rounded-2xl bg-white shadow-sm font-bold text-gray-700" required>
                                        <option value="" disabled>Seleccionar...</option>
                                        <option v-for="f in catalogs.fuentes" :key="f.id" :value="f.id">{{ f.nombre }}</option>
                                    </select>
                                </div>

                                <div class="col-span-2 grid grid-cols-2 gap-8">
                                    <div class="col-span-2">
                                        <InputLabel for="proveedor_id" value="Cédente / Vendedor" />
                                        <select id="proveedor_id" v-model="form.proveedor_id" class="mt-2 block w-full border-gray-300 rounded-2xl bg-white shadow-sm font-bold text-gray-700" required>
                                            <option value="" disabled>Seleccionar...</option>
                                            <option v-for="p in catalogs.proveedores" :key="p.id" :value="p.id">{{ p.nombre }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <InputLabel for="fecha_adquisicion" value="Fecha Adquisición" />
                                        <TextInput id="fecha_adquisicion" type="date" v-model="form.fecha_adquisicion" class="mt-2 block w-full bg-white text-center font-bold" required />
                                    </div>
                                    <div>
                                        <InputLabel for="valor_adquisicion" value="Valor Contable (C$)" />
                                        <TextInput id="valor_adquisicion" type="number" step="0.01" v-model="form.valor_adquisicion" class="mt-2 block w-full bg-white font-black text-emerald-600 text-lg" required />
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 flex items-center justify-between pt-10 border-t border-gray-100">
                        <div class="flex items-center text-gray-400 text-xs font-bold uppercase tracking-widest italic">
                            SIAFSEB - Terrenos e Inmuebles
                        </div>
                        <div class="flex gap-4">
                            <SecondaryButton @click="$inertia.visit(route('terrenos.index'))" :disabled="form.processing" class="!rounded-2xl px-10 !h-14 font-black">
                                CANCELAR
                            </SecondaryButton>
                            
                            <PrimaryButton @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="!rounded-2xl px-12 !h-14 !bg-emerald-600 hover:!bg-emerald-700 shadow-xl shadow-emerald-200 font-black">
                                <svg v-if="!form.processing" class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span v-if="form.processing">PROCESANDO...</span>
                                <span v-else>GUARDAR TERRENO</span>
                            </PrimaryButton>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
