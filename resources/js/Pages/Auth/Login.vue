<script setup>
import Checkbox from '@/Components/Checkbox.vue';

import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Left Side: Branding -->
        <div class="w-full md:w-1/2 bg-gradient-to-br from-indigo-600 to-purple-500 flex flex-col justify-center items-center text-white p-8 relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-10 rounded-full mix-blend-overlay filter blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 bg-purple-800 opacity-20 rounded-full mix-blend-overlay filter blur-3xl translate-x-1/2 translate-y-1/2"></div>
            
            <div class="z-10 text-center">
                <div class="mb-6 flex justify-center">
                   <!-- Logo -->
                   <img src="/images/logo_alcaldia.jpg" alt="Logo Alcaldía" class="w-32 h-32 object-contain bg-white p-2 rounded-2xl shadow-lg" />
                </div>
                <h1 class="text-4xl font-bold tracking-wide mb-2 uppercase">Sebaco</h1>
                <p class="text-lg opacity-90 max-w-sm uppercase font-bold">Alcaldía Municipal</p>
                <p class="text-sm opacity-80 max-w-sm mt-2">Sistema Integral de Activos Fijos</p>
                
                <div class="mt-8 flex justify-center space-x-2">
                    <span class="w-2 h-2 bg-white rounded-full opacity-100"></span>
                    <span class="w-2 h-2 bg-white rounded-full opacity-50"></span>
                    <span class="w-2 h-2 bg-white rounded-full opacity-50"></span>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-1/2 bg-white flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">¡Bienvenido!</h2>
                    <p class="text-gray-500">Ingresa tus credenciales para continuar</p>
                </div>

                <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <InputLabel for="email" value="Correo Electrónico" class="text-gray-700" />
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                </svg>
                            </div>
                            <TextInput
                                id="email"
                                type="email"
                                class="pl-10 block w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="tu@email.com"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                             <InputLabel for="password" value="Contraseña" class="text-gray-700" />
                             <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm text-indigo-600 hover:text-indigo-500 font-medium"
                            >
                                ¿Olvidaste tu contraseña?
                            </Link>
                        </div>
                        <div class="mt-1 relative rounded-md shadow-sm">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <TextInput
                                id="password"
                                type="password"
                                class="pl-10 block w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                        </div>
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" class="text-indigo-600 focus:ring-indigo-500 rounded border-gray-300" />
                            <span class="ms-2 text-sm text-gray-600">Mantener sesión iniciada</span>
                        </label>
                    </div>

                    <div>
                        <PrimaryButton
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            :class="{ 'opacity-75': form.processing }"
                            :disabled="form.processing"
                        >
                            Iniciar Sesión
                        </PrimaryButton>
                    </div>
                </form>
                
                
                <div class="mt-10 text-center text-xs text-gray-400">
                    &copy; 2026 SIAFSEB. Todos los derechos reservados.
                </div>
            </div>
        </div>
    </div>
</template>
