<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, usePage } from '@inertiajs/vue3';

const page = usePage();

const props = defineProps({
    title: String,
    items: Array,
    columns: Array, // [{ key: 'nombre', label: 'Nombre', type: 'text', options?: [] }]
    routeName: String,
    extraData: Object, // e.g., { cargos: [], marcas: [] }
});

const confirmingDeletion = ref(false);
const itemToDelete = ref(null);
const editingItem = ref(null);
const modalOpen = ref(false);
const registrationSuccess = ref(false);

const form = useForm(
    props.columns.reduce((acc, col) => {
        acc[col.key] = '';
        return acc;
    }, {})
);

const openCreateModal = () => {
    editingItem.value = null;
    form.reset();
    form.clearErrors();
    modalOpen.value = true;
};

const openEditModal = (item) => {
    editingItem.value = item;
    form.clearErrors();
    props.columns.forEach(col => {
        form[col.key] = item[col.key];
    });
    modalOpen.value = true;
};

const submit = () => {
    if (editingItem.value) {
        form.put(route(`${props.routeName}.update`, editingItem.value.id), {
            onSuccess: () => {
                registrationSuccess.value = true;
                setTimeout(() => {
                    closeModal();
                }, 2000); // Auto-close success modal after 2 seconds
            },
        });
    } else {
        form.post(route(`${props.routeName}.store`), {
            onSuccess: () => {
                registrationSuccess.value = true;
                setTimeout(() => {
                    closeModal();
                }, 2000); // Auto-close success modal after 2 seconds
            },
        });
    }
};

const confirmDeletion = (item) => {
    itemToDelete.value = item;
    confirmingDeletion.value = true;
};

const deleteItem = () => {
    form.delete(route(`${props.routeName}.destroy`, itemToDelete.value.id), {
        onSuccess: () => {
            confirmingDeletion.value = false;
            itemToDelete.value = null;
        },
    });
};

const closeModal = () => {
    modalOpen.value = false;
    setTimeout(() => {
        registrationSuccess.value = false;
        form.reset();
    }, 200);
};
</script>

<template>
    <Head :title="title" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ title }}</h1>
                    <p class="mt-1 text-sm text-gray-500">Administración del catálogo de {{ title.toLowerCase() }}.</p>
                </div>
                <PrimaryButton @click="openCreateModal">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Agregar Nuevo
                </PrimaryButton>
            </div>

            <!-- Redundant local flash messages removed (now in AuthenticatedLayout) -->

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th v-for="col in columns" :key="col.key" class="px-6 py-4 text-xs font-semibold text-blue-600 uppercase tracking-wider">
                                    {{ col.label }}
                                </th>
                                <th class="px-6 py-4 text-xs font-semibold text-blue-600 uppercase tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="item in items" :key="item.id" class="hover:bg-gray-50/50 transition-colors group">
                                <td v-for="col in columns" :key="col.key" class="px-6 py-4 text-sm text-gray-600">
                                    <template v-if="col.relationName">
                                        {{ item[col.relationName]?.[col.relationField] || 'N/A' }}
                                    </template>
                                    <template v-else>
                                        {{ item[col.key] }}
                                    </template>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="openEditModal(item)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Editar</button>
                                    <button @click="confirmDeletion(item)" class="text-red-600 hover:text-red-900 text-sm font-medium">Eliminar</button>
                                </td>
                            </tr>
                            <tr v-if="items.length === 0">
                                <td :colspan="columns.length + 1" class="px-6 py-10 text-center text-gray-400 italic">
                                    No se encontraron registros.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <Modal :show="modalOpen" @close="closeModal">
            <div class="p-6">
                <!-- Success View -->
                <div v-if="registrationSuccess" class="py-8 text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Excelente!</h2>
                    <p class="text-gray-500 mb-8">El registro se ha guardado satisfactoriamente.</p>
                    <PrimaryButton @click="closeModal" class="w-full justify-center py-3">
                        Volver al Catálogo
                    </PrimaryButton>
                </div>

                <!-- Form View -->
                <template v-else>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        {{ editingItem ? 'Editar' : 'Agregar' }} {{ title }}
                    </h2>
                    
                    <form @submit.prevent="submit" class="space-y-4">
                        <div v-for="col in columns" :key="col.key">
                            <InputLabel :for="col.key" :value="col.label" />
                            
                            <select v-if="col.type === 'select'"
                                :id="col.key"
                                v-model="form[col.key]"
                                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="" disabled>Seleccione una opción</option>
                                <option v-for="opt in extraData[col.optionsKey]" :key="opt.id" :value="opt.id">
                                    {{ opt.nombre || opt.nombre_completo }}
                                </option>
                            </select>

                            <TextInput 
                                v-else
                                :id="col.key"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form[col.key]"
                                :placeholder="`Ingrese ${col.label.toLowerCase()}...`"
                                :required="!col.optional"
                            />

                            <InputError :message="form.errors[col.key]" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <SecondaryButton @click="closeModal" :disabled="form.processing">Cancelar</SecondaryButton>
                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ editingItem ? 'Actualizar' : 'Guardar' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </template>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900">¿Estás seguro de eliminar este registro?</h2>
                <p class="mt-2 text-sm text-gray-600">Esta acción no se puede deshacer y puede afectar a registros relacionados.</p>
                
                <!-- Error Message -->
                <div v-if="page.props.flash.error" class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
                        <p class="text-sm font-medium">{{ page.props.flash.error }}</p>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="confirmingDeletion = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteItem" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Eliminar Registro
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
