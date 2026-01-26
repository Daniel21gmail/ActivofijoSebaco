<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    users: Array,
    roles: Array,
});

const confirmingDeletion = ref(false);
const userToDelete = ref(null);
const editingUser = ref(null);
const modalOpen = ref(false);
const registrationSuccess = ref(false);

const form = useForm({
    nombre: '',
    email: '',
    password: '',
    role_id: '',
});

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    modalOpen.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    form.clearErrors();
    form.nombre = user.nombre;
    form.email = user.email;
    form.role_id = user.role_id;
    form.password = ''; // Don't show existing password
    modalOpen.value = true;
};

const submit = () => {
    if (editingUser.value) {
        form.put(route('usuarios.update', editingUser.value.id), {
            onSuccess: () => {
                registrationSuccess.value = true;
            },
        });
    } else {
        form.post(route('usuarios.store'), {
            onSuccess: () => {
                registrationSuccess.value = true;
            },
        });
    }
};

const confirmDeletion = (user) => {
    userToDelete.value = user;
    confirmingDeletion.value = true;
};

const deleteUser = () => {
    form.delete(route('usuarios.destroy', userToDelete.value.id), {
        onSuccess: () => {
            confirmingDeletion.value = false;
            userToDelete.value = null;
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
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Gestión de Usuarios</h1>
                    <p class="mt-1 text-sm text-gray-500">Administración de usuarios y sus accesos al sistema.</p>
                </div>
                <PrimaryButton @click="openCreateModal">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Usuario
                </PrimaryButton>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 text-xs font-semibold text-blue-600 uppercase tracking-wider">Nombre</th>
                                <th class="px-6 py-4 text-xs font-semibold text-blue-600 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-4 text-xs font-semibold text-blue-600 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-4 text-xs font-semibold text-blue-600 uppercase tracking-wider text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ user.nombre }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ user.email }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                                        {{ user.role?.nombre || 'Sin Rol' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button @click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Editar</button>
                                    <button v-if="$page.props.auth.user.id !== user.id" @click="confirmDeletion(user)" class="text-red-600 hover:text-red-900 text-sm font-medium">Eliminar</button>
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
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">¡Completado!</h2>
                    <p class="text-gray-500 mb-8">El usuario se ha guardado correctamente.</p>
                    <PrimaryButton @click="closeModal" class="w-full justify-center py-3">
                        Cerrar ventana
                    </PrimaryButton>
                </div>

                <!-- Form View -->
                <template v-else>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        {{ editingUser ? 'Editar' : 'Agregar' }} Usuario
                    </h2>
                    
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="nombre" value="Nombre" />
                            <TextInput id="nombre" type="text" class="mt-1 block w-full" v-model="form.nombre" required />
                            <InputError :message="form.errors.nombre" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="role_id" value="Rol de Usuario" />
                            <select id="role_id" v-model="form.role_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled>Seleccione un rol</option>
                                <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.nombre }}</option>
                            </select>
                            <p v-if="roles.length === 0" class="mt-1 text-xs text-red-600">
                                No hay roles definidos. Debe registrarlos en el catálogo de Roles primero.
                            </p>
                            <InputError :message="form.errors.role_id" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="password" :value="editingUser ? 'Nueva Contraseña (opcional)' : 'Contraseña'" />
                            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" :required="!editingUser" />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <SecondaryButton @click="closeModal" :disabled="form.processing">Cancelar</SecondaryButton>
                            <PrimaryButton type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                {{ editingUser ? 'Actualizar' : 'Guardar' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </template>
            </div>
        </Modal>

        <!-- Delete Confirmation Modal -->
        <Modal :show="confirmingDeletion" @close="confirmingDeletion = false">
            <div class="p-6">
                <h2 class="text-lg font-bold text-gray-900">¿Eliminar usuario?</h2>
                <p class="mt-2 text-sm text-gray-600">Esta acción no se puede deshacer y el usuario perderá acceso inmediato.</p>
                <div class="mt-6 flex justify-end gap-3">
                    <SecondaryButton @click="confirmingDeletion = false">Cancelar</SecondaryButton>
                    <DangerButton @click="deleteUser" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Eliminar Definitivamente
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
