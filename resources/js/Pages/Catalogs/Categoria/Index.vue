<script setup>
import CatalogCrud from '@/Components/Catalogs/CatalogCrud.vue';

defineProps({
    items: Array,
});

const columns = [
    { key: 'codigo', label: 'Código' },
    { key: 'subcategoria', label: 'Categoría' },
    { key: 'clase', label: 'Clase' },
    { key: 'nombre', label: 'Descripción' },
];
</script>

<template>
    <CatalogCrud 
        title="Categorías" 
        :items="items" 
        :columns="columns" 
        routeName="categorias" 
    />
</template>
