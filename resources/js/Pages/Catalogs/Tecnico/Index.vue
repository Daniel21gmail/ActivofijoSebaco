<script setup>
import CatalogCrud from '@/Components/Catalogs/CatalogCrud.vue';

defineProps({
    items: Array,
});

const columns = [
    { key: 'nombre_completo', label: 'Nombre Completo' },
    { key: 'telefono', label: 'Teléfono' },
];
</script>

<template>
    <CatalogCrud 
        title="Técnicos" 
        :items="items" 
        :columns="columns" 
        routeName="tecnicos" 
    />
</template>
