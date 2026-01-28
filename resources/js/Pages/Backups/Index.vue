<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Database, Download, Trash2, HardDrive, FileText, Clock, RefreshCcw } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    backups: Array,
});

const processing = ref(false);

const createBackup = () => {
    if (confirm('¿Estás seguro de crear un nuevo respaldo de la base de datos?')) {
        processing.value = true;
        router.post(route('backups.store'), {}, {
            preserveScroll: true,
            onFinish: () => processing.value = false,
        });
    }
};

const deleteBackup = (name) => {
    if (confirm('¿Estás seguro de eliminar este respaldo? Esta acción no se puede deshacer.')) {
        router.delete(route('backups.destroy', name), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Respaldos de Seguridad" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Header Section -->
                <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 uppercase tracking-tight flex items-center gap-3">
                            <Database class="h-8 w-8 text-indigo-600" />
                            Respaldos de Seguridad
                        </h2>
                        <p class="text-sm text-gray-500 mt-1 ml-11">Gestiona las copias de seguridad de la base de datos y archivos.</p>
                    </div>
                    <div>
                        <button @click="createBackup" 
                                :disabled="processing"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-lg shadow-indigo-200">
                            <RefreshCcw class="w-4 h-4 mr-2" :class="{'animate-spin': processing}" />
                            {{ processing ? 'Generando...' : 'Crear Nuevo Respaldo' }}
                        </button>
                    </div>
                </div>

                <!-- Backup List -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                    <div class="p-6">
                        <div v-if="backups.length === 0" class="text-center py-12">
                            <div class="bg-gray-50 h-20 w-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                <HardDrive class="h-10 w-10 text-gray-400" />
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">No hay respaldos disponibles</h3>
                            <p class="text-gray-500 text-sm mt-1">Genera tu primer respaldo usando el botón superior.</p>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Archivo</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tamaño</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha de Creación</th>
                                        <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="backup in backups" :key="backup.name" class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                                    <FileText class="h-5 w-5" />
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900">{{ backup.name }}</div>
                                                    <div class="text-xs text-gray-500">Backup SQL Completo</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-blue-50 text-blue-700 uppercase tracking-wider">
                                                {{ backup.size }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center text-sm text-gray-500">
                                                <Clock class="h-4 w-4 mr-1.5 text-gray-400" />
                                                {{ backup.date }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                                            <a :href="route('backups.download', backup.name)" 
                                               class="inline-flex items-center text-emerald-600 hover:text-emerald-900 font-bold text-xs uppercase tracking-wider transition-colors bg-emerald-50 px-3 py-1.5 rounded-lg hover:bg-emerald-100">
                                                <Download class="h-4 w-4 mr-1" /> Download
                                            </a>
                                            <button @click="deleteBackup(backup.name)" 
                                                    class="inline-flex items-center text-rose-600 hover:text-rose-900 font-bold text-xs uppercase tracking-wider transition-colors bg-rose-50 px-3 py-1.5 rounded-lg hover:bg-rose-100">
                                                <Trash2 class="h-4 w-4 mr-1" /> Eliminar
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
