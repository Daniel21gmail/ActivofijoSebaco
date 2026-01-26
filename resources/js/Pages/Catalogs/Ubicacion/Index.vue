<script setup>
import CatalogCrud from '@/Components/Catalogs/CatalogCrud.vue';

defineProps({
    items: Array,
});

const columns = [
    { key: 'nombre', label: 'Nombre' },
    { key: 'descripcion', label: 'Descripción' },
];
</script>

<template>
    <CatalogCrud 
        title="Ubicaciones" 
        :items="items" 
        :columns="columns" 
        routeName="ubicaciones" 
    />
</template>
