<script setup>
import CatalogCrud from '@/Components/Catalogs/CatalogCrud.vue';

defineProps({
    items: Array,
});

const columns = [
    { key: 'nombre', label: 'Nombre' },
    { key: 'nit', label: 'Número RUC' },
    { key: 'telefono', label: 'Teléfono' },
    { key: 'direccion', label: 'Dirección' },
];
</script>

<template>
    <CatalogCrud 
        title="Proveedores" 
        :items="items" 
        :columns="columns" 
        routeName="proveedores" 
    />
</template>
