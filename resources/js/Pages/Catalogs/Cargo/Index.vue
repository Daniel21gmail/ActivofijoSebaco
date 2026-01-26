<script setup>
import CatalogCrud from '@/Components/Catalogs/CatalogCrud.vue';

defineProps({
    items: Array,
});

const columns = [
    { key: 'nombre', label: 'Nombre' },
];
</script>

<template>
    <CatalogCrud 
        title="Cargos" 
        :items="items" 
        :columns="columns" 
        routeName="cargos" 
    />
</template>
