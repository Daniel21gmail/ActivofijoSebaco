<script setup>
import CatalogCrud from '@/Components/Catalogs/CatalogCrud.vue';

defineProps({
    items: Array,
    marcas: Array,
});

const columns = [
    { key: 'nombre', label: 'Nombre' },
    { key: 'marca_id', label: 'Marca', type: 'select', optionsKey: 'marcas', relationName: 'marca', relationField: 'nombre' },
];
</script>

<template>
    <CatalogCrud 
        title="Modelos" 
        :items="items" 
        :columns="columns" 
        routeName="modelos" 
        :extraData="{ marcas }"
    />
</template>
