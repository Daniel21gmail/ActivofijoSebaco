<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const showNotifications = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);

const isCatalogRoute = () => {
    return route().current('departamentos.*') || 
           route().current('ubicaciones.*') || 
           route().current('categorias.*') || 
           route().current('proveedores.*') ||
           route().current('marcas.*') ||
           route().current('modelos.*') ||
           route().current('estados-activos.*') || 
           route().current('colores.*') ||
           route().current('fuentes.*') ||
           route().current('responsables.*') ||
           route().current('cargos.*') ||
           route().current('tecnicos.*');
};

const isActivosRoute = () => {
    return route().current('activos-fijos.*') || 
           route().current('vehiculos.*') || 
           route().current('terrenos.*');
};

const catalogDropdownOpen = ref(isCatalogRoute());
const activosFijosDropdownOpen = ref(isActivosRoute());
const reportDropdownOpen = ref(route().current('reportes.*'));
const page = usePage();
const { auth } = page.props;

const showFlash = ref(false);
let flashTimeout = null;

watch(() => page.props.flash, (newFlash) => {
    if (newFlash && (newFlash.message || newFlash.error)) {
        showFlash.value = true;
        
        if (flashTimeout) clearTimeout(flashTimeout);
        
        flashTimeout = setTimeout(() => {
            showFlash.value = false;
        }, 5000);
    }
}, { deep: true, immediate: true });

// Fetch notifications
const fetchNotifications = async () => {
    try {
        const response = await fetch(route('notifications.index'));
        const data = await response.json();
        notifications.value = data;
    } catch (error) {
        console.error('Error fetching notifications:', error);
    }
};

const fetchUnreadCount = async () => {
    try {
        const response = await fetch(route('notifications.unread-count'));
        const data = await response.json();
        unreadCount.value = data.count;
    } catch (error) {
        console.error('Error fetching unread count:', error);
    }
};

const markAsRead = async (id) => {
    try {
        await fetch(route('notifications.read', id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        await fetchNotifications();
        await fetchUnreadCount();
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
};

const markAllAsRead = async () => {
    try {
        await fetch(route('notifications.read-all'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        await fetchNotifications();
        await fetchUnreadCount();
    } catch (error) {
        console.error('Error marking all as read:', error);
    }
};

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
    if (showNotifications.value) {
        fetchNotifications();
    }
};

// Load initial data
fetchUnreadCount();
</script>

<template>
    <div class="flex h-screen bg-[#F3F4F9]">
        <!-- Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-[#232333] text-white transition-all duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-white/5"
               :class="{'translate-x-0': showingNavigationDropdown, '-translate-x-full': !showingNavigationDropdown}">
            <div class="flex h-20 items-center px-6">
                <Link :href="route('dashboard')" class="flex items-center gap-3">
                    <ApplicationLogo class="h-8 w-8 fill-current" />
                    <span class="text-xl font-bold tracking-tight text-white">SIAFSEB</span>
                </Link>
            </div>

            <div class="flex flex-col h-[calc(100vh-5rem)]">
                <nav class="flex-1 mt-2 px-4 space-y-2 overflow-y-auto custom-scrollbar">
                <!-- GENERAL -->
                <div>
                    <h3 class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">GENERAL</h3>
                    <div class="space-y-1">
                        <Link :href="route('dashboard')" 
                              class="sidebar-link"
                              :class="{ 'active': route().current('dashboard') }">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                            <span>Dashboard</span>
                        </Link>
                    </div>
                </div>

                <!-- OPERATIVO / GESTIÓN -->
                <div v-if="auth.user.role?.nombre !== 'Consulta'">
                    <h3 class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 mt-4">GESTIÓN</h3>
                    
                    <!-- Catálogos Dropdown -->
                    <div class="space-y-1 mb-1">
                        <div @click="catalogDropdownOpen = !catalogDropdownOpen" 
                             class="sidebar-link justify-between cursor-pointer group"
                             :class="{ 'active': isCatalogRoute() }">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>Catálogos</span>
                            </div>
                            <svg class="h-4 w-4 text-gray-400 group-hover:text-white transition-transform duration-200" 
                                 :class="{'rotate-180': catalogDropdownOpen}"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        
                        <div v-show="catalogDropdownOpen" class="mt-1 ml-4 pl-4 border-l border-gray-700/50 space-y-1">
                            <Link :href="route('departamentos.index')" class="sidebar-sublink" :class="{'text-white': route().current('departamentos.*')}">Departamento</Link>
                            <Link :href="route('ubicaciones.index')" class="sidebar-sublink" :class="{'text-white': route().current('ubicaciones.*')}">Ubicación</Link>
                            <Link :href="route('categorias.index')" class="sidebar-sublink" :class="{'text-white': route().current('categorias.*')}">Categoría</Link>
                            <Link :href="route('proveedores.index')" class="sidebar-sublink" :class="{'text-white': route().current('proveedores.*')}">Proveedores</Link>
                            <Link :href="route('marcas.index')" class="sidebar-sublink" :class="{'text-white': route().current('marcas.*')}">Marcas</Link>
                            <Link :href="route('modelos.index')" class="sidebar-sublink" :class="{'text-white': route().current('modelos.*')}">Modelo</Link>
                            <Link :href="route('estados-activos.index')" class="sidebar-sublink" :class="{'text-white': route().current('estados-activos.*')}">Estados</Link>
                            <Link :href="route('colores.index')" class="sidebar-sublink" :class="{'text-white': route().current('colores.*')}">Colores</Link>
                            <Link :href="route('fuentes.index')" class="sidebar-sublink" :class="{'text-white': route().current('fuentes.*')}">Fuentes</Link>
                            <Link :href="route('responsables.index')" class="sidebar-sublink" :class="{'text-white': route().current('responsables.*')}">Responsable</Link>
                            <Link :href="route('cargos.index')" class="sidebar-sublink" :class="{'text-white': route().current('cargos.*')}">Cargos</Link>
                            <Link :href="route('tecnicos.index')" class="sidebar-sublink" :class="{'text-white': route().current('tecnicos.*')}">Técnicos</Link>
                        </div>
                    </div>

                    <!-- Activos Fijos Dropdown -->
                    <div class="space-y-1">
                        <div @click="activosFijosDropdownOpen = !activosFijosDropdownOpen" 
                             class="sidebar-link justify-between cursor-pointer group"
                             :class="{ 'active': isActivosRoute() }">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                <span>Activos Fijos</span>
                            </div>
                            <svg class="h-4 w-4 text-gray-400 group-hover:text-white transition-transform duration-200" 
                                 :class="{'rotate-180': activosFijosDropdownOpen}"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        
                        <div v-show="activosFijosDropdownOpen" class="mt-1 ml-4 pl-4 border-l border-gray-700/50 space-y-1">
                            <Link :href="route('activos-fijos.index')" class="sidebar-sublink" :class="{'text-white': route().current('activos-fijos.*')}">Lista de Activos</Link>
                            <Link :href="route('vehiculos.index')" class="sidebar-sublink" :class="{'text-white': route().current('vehiculos.*')}">Vehículos</Link>
                            <Link :href="route('terrenos.index')" class="sidebar-sublink" :class="{'text-white': route().current('terrenos.*')}">Terrenos</Link>
                        </div>
                    </div>

                    <!-- Reportes -->
                    <div class="space-y-1">
                        <div @click="reportDropdownOpen = !reportDropdownOpen" 
                             class="sidebar-link justify-between cursor-pointer group"
                             :class="{ 'active': route().current('reportes.*') }">
                            <div class="flex items-center gap-3">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>Reportes</span>
                            </div>
                            <svg class="h-4 w-4 text-gray-400 group-hover:text-white transition-transform duration-200" 
                                 :class="{'rotate-180': reportDropdownOpen}"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        
                        <div v-show="reportDropdownOpen" class="mt-1 ml-4 pl-4 border-l border-gray-700/50 space-y-1">
                            <Link :href="route('reportes.index', { report_type: 'inventario' })" class="sidebar-sublink" :class="{'text-white': route().current('reportes.index')}">General</Link>
                            <Link :href="route('reportes.index', { report_type: 'mantenimientos' })" class="sidebar-sublink" :class="{'text-white': route().current('reportes.index') && $page.props.filters?.report_type === 'mantenimientos'}">Mantenimientos</Link>
                        </div>
                    </div>
                </div>

                <!-- SEGURIDAD -->
                <div v-if="auth.user.role?.nombre !== 'Consulta'" class="pb-4">
                    <h3 class="px-2 text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2 mt-4">SEGURIDAD</h3>
                    <div class="space-y-1">
                        <Link :href="route('usuarios.index')" 
                              class="sidebar-link"
                              :class="{ 'active': route().current('usuarios.*') }">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            <span>Usuarios</span>
                        </Link>
                        <Link :href="route('roles.index')" 
                              class="sidebar-link"
                              :class="{ 'active': route().current('roles.*') }">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            <span>Roles</span>
                        </Link>
                    </div>
                </div>
            </nav>

            <!-- User Profile (Bottom) -->
            <div class="p-4 border-t border-white/5">
                <div class="flex items-center gap-3 px-2">
                    <div class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white text-lg font-bold shadow-md ring-2 ring-indigo-600/20">
                        {{ auth.user.nombre.charAt(0).toUpperCase() }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-white truncate w-32">{{ auth.user.nombre }}</span>
                        <Link :href="route('profile.edit')" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">Ver opciones</Link>
                    </div>
                </div>
            </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="flex h-16 items-center justify-between bg-white px-8 shadow-sm">
                <!-- Mobile Menu Button -->
                <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="lg:hidden p-2 rounded-md text-gray-400 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Search Bar -->
                <div class="flex-1 max-w-md">
                    <div class="relative group">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400 group-focus-within:text-[#2D2E83] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" 
                               class="block w-full rounded-full border-gray-200 bg-gray-50 pl-10 focus:border-[#2D2E83] focus:ring-[#2D2E83] sm:text-sm" 
                               placeholder="Buscar activo, vehículo, terreno...">
                    </div>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-4">
                    <!-- Notifications Dropdown -->
                <div class="relative">
                    <button @click="toggleNotifications" class="relative p-2 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span v-if="unreadCount > 0" class="absolute top-1 right-1 flex h-4 w-4">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-[10px] text-white items-center justify-center font-bold">{{ unreadCount }}</span>
                        </span>
                    </button>

                    <!-- Notifications Dropdown -->
                    <transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div v-show="showNotifications" class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                            <!-- Header -->
                            <div class="flex items-center justify-between px-4 py-3 border-b">
                                <h3 class="text-sm font-semibold text-gray-900">Notificaciones</h3>
                                <button v-if="unreadCount > 0" @click="markAllAsRead" class="text-xs text-[#2D2E83] hover:text-[#1a1b4d] font-medium">
                                    Marcar todas como leídas
                                </button>
                            </div>

                            <!-- Notifications List -->
                            <div class="max-h-96 overflow-y-auto">
                                <div v-if="notifications.length === 0" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    No tienes notificaciones
                                </div>
                                <div v-else>
                                    <div v-for="notification in notifications" :key="notification.id" 
                                         @click="markAsRead(notification.id)"
                                         class="px-4 py-3 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 transition-colors"
                                         :class="{'bg-blue-50': !notification.read}">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-1">
                                                <div class="h-2 w-2 rounded-full" 
                                                     :class="{
                                                         'bg-blue-500': notification.type === 'info',
                                                         'bg-green-500': notification.type === 'success',
                                                         'bg-yellow-500': notification.type === 'warning',
                                                         'bg-red-500': notification.type === 'error'
                                                     }"></div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                                                <p class="text-xs text-gray-600 mt-1">{{ notification.message }}</p>
                                                <p class="text-xs text-gray-400 mt-1">{{ new Date(notification.created_at).toLocaleString('es-ES') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>

                    <!-- User Profile Dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button type="button" class="flex items-center gap-3 focus:outline-none">
                                <div class="h-8 w-8 rounded-full bg-[#2D2E83] flex items-center justify-center text-white text-sm font-bold shadow-md">
                                    {{ $page.props.auth.user.nombre.charAt(0).toUpperCase() }}
                                </div>
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                {{ $page.props.auth.user.nombre }}
                            </div>
                            <DropdownLink :href="route('profile.edit')">Perfil</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">Cerrar Sesión</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-x-hidden overflow-y-auto p-8 relative">
                <!-- Global Notifications (Toast style) -->
                <div class="fixed top-20 right-8 z-[100] w-96 pointer-events-none">
                    <transition
                        enter-active-class="transform ease-out duration-300 transition"
                        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                        leave-active-class="transition ease-in duration-100"
                        leave-from-class="opacity-100"
                        leave-to-class="opacity-0"
                    >
                        <div v-if="showFlash && (page.props.flash.message || page.props.flash.error)" 
                             class="pointer-events-auto shadow-2xl rounded-xl overflow-hidden border border-white/20 backdrop-blur-lg">
                            
                            <!-- Success Message -->
                            <div v-if="page.props.flash.message" class="bg-emerald-500/90 p-4 text-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <p class="font-medium">{{ page.props.flash.message }}</p>
                                </div>
                                <button @click="showFlash = false" class="hover:bg-white/10 p-1 rounded-full transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <!-- Error Message -->
                            <div v-if="page.props.flash.error" class="bg-rose-500/90 p-4 text-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    </div>
                                    <p class="font-medium">{{ page.props.flash.error }}</p>
                                </div>
                                <button @click="showFlash = false" class="hover:bg-white/10 p-1 rounded-full transition-colors">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                        </div>
                    </transition>
                </div>

                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
.sidebar-link {
    @apply flex items-center gap-3 px-4 py-3 text-sm font-medium text-gray-400 transition-all duration-200 border-b-2 border-transparent hover:text-white hover:bg-white/5 hover:border-indigo-500 hover:shadow-[0_4px_20px_-2px_rgba(99,102,241,0.2)];
}

.sidebar-link.active {
    @apply text-indigo-400 bg-white/5;
}

.sidebar-sublink {
    @apply block px-3 py-1.5 text-sm text-gray-400 hover:text-white transition-colors rounded-md hover:bg-white/5 font-medium;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}
</style>
