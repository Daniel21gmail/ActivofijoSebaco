<script setup>
import CatalogCrud from '@/Components/Catalogs/CatalogCrud.vue';

defineProps({
    items: Array,
    cargos: Array,
});

const columns = [
    { key: 'nombre_completo', label: 'Nombre Completo' },
    { key: 'cargo_id', label: 'Cargo', type: 'select', optionsKey: 'cargos', relationName: 'cargo', relationField: 'nombre' },
    { key: 'telefono', label: 'Teléfono' },
];
</script>

<template>
    <CatalogCrud 
        title="Responsables" 
        :items="items" 
        :columns="columns" 
        routeName="responsables" 
        :extraData="{ cargos }"
    />
</template>
