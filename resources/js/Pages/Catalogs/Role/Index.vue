<script setup>
import CatalogCrud from '@/Components/Catalogs/CatalogCrud.vue';

defineProps({
    items: Array,
});

const columns = [
    { key: 'nombre', label: 'Nombre del Rol' },
    { key: 'descripcion', label: 'Descripción' },
];
</script>

<template>
    <CatalogCrud 
        title="Roles" 
        :items="items" 
        :columns="columns" 
        routeName="roles" 
    />
</template>
