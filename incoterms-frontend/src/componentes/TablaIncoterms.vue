<template>

    <div class="tabla">

        <h3> Incoterms </h3>

        <table width="100%">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Fases Logísticas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>

                <tr v-for="incoterm in incoterms" :key="incoterm.ID">

                    <td><strong>{{ incoterm.CODE }}</strong></td>

                    <td>{{ incoterm.NAME }}</td>

                    <td>
                        <span v-if="incoterm.tracking_steps && incoterm.tracking_steps.length > 0">

                            <span v-for="paso in incoterm.tracking_steps" :key="paso.ID" class="etiqueta-paso">

                                {{ paso.NAME }}

                            </span>
                        </span>

                        <span v-else class="sin-fases">Sin fases</span>
                    </td>
                    <td>
                        <button class="btn-editar" @click="$emit('editar', incoterm)">Editar</button>
                        <button class="btn-borrar" @click="$emit('borrar', incoterm.ID)">Borrar</button>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</template>


<script setup>

import { ref } from 'vue';

defineProps({

    incoterms: Array

})

defineEmits(['editar', 'borrar']);


</script>


<style scoped>
.tabla {
    border: 1px solid #ddd;
    padding: 1.5rem;
    border-radius: 10px;
    background: white;
}

table {
    border-collapse: collapse;
    text-align: left;
    margin-top: 10px;
}

th,
td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

th {
    background-color: #f8fafc;
    color: #334155;
}

.etiqueta-paso {
    background: #e0e7ff;
    color: #4338ca;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.8rem;
    margin-right: 5px;
    display: inline-block;
    margin-bottom: 2px;
}

.sin-fases {
    color: #94a3b8;
    font-style: italic;
}

.btn-editar {
    background: #fef3c7;
    border: 1px solid #fde68a;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    margin-right: 5px;
    color: #92400e;
    font-weight: bold;
}

.btn-borrar {
    background: #fee2e2;
    border: 1px solid #fecaca;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    color: #991b1b;
    font-weight: bold;
}
</style>
