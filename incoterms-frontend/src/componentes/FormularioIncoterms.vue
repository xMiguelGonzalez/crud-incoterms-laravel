<template>
    <div class="caja-formulario">
        <h3>{{ incotermEditando ? ' Editar Incoterm' : 'Crear Incoterm' }}</h3>

        <div class="campo">
            <label>Código:</label>
            <input type="text" v-model="formulario.CODE" placeholder="Ej: FOB">
        </div>

        <div class="campo">
            <label>Nombre:</label>
            <input type="text" v-model="formulario.NAME" placeholder="Ej: Free On Board">
        </div>

        <div class="campo">
            <label>Fases Logísticas:</label>
            <div v-for="paso in pasos" :key="paso.ID" class="opcion-paso">

                <input type="checkbox" :value="paso.ID" v-model="formulario.STEPS">
                <span>{{ paso.NAME }}</span>

            </div>
        </div>

        <div class="acciones">
            <button class="btn-guardar" @click="enviarDatos">Guardar</button>
            <button v-if="incotermEditando" class="btn-cancelar" @click="cancelarFormulario">Cancelar</button>
        </div>

    </div>

</template>


<script setup>

import { ref, watch } from 'vue';

const props = defineProps({

    incotermEditando: Object,
    pasos: Array

})


const emit = defineEmits(['guardar', 'cancelar']);


const formulario = ref({
    ID: null,
    CODE: '',
    NAME: '',
    STEPS: []
});


watch(() => props.incotermEditando, (nuevoIncoterm) => {

    if (nuevoIncoterm != null) {

        formulario.value.ID = nuevoIncoterm.ID
        formulario.value.CODE = nuevoIncoterm.CODE
        formulario.value.NAME = nuevoIncoterm.NAME

        let listaDeIds = [];

        for (let i = 0; i < nuevoIncoterm.tracking_steps.length; i++) {

            listaDeIds.push(nuevoIncoterm.tracking_steps[i].ID);

        }

        formulario.value.STEPS = listaDeIds;

    }

    else {
        formulario.value.ID = null;
        formulario.value.CODE = '';
        formulario.value.NAME = '';
        formulario.value.STEPS = [];
    }

});

const enviarDatos = () => {
    emit('guardar', formulario.value);
};


const cancelarFormulario = () => {
    emit('cancelar');
};

</script>
<style scoped>
/* La caja principal del formulario */
.caja-formulario {
    border: 1px solid #e2e8f0;
    padding: 2rem;
    border-radius: 12px;
    background: #ffffff;
    box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    /* Sombrita elegante */
}

.caja-formulario h3 {
    margin-top: 0;
    margin-bottom: 1.5rem;
    color: #1e293b;
    font-size: 1.25rem;
    border-bottom: 2px solid #f1f5f9;
    padding-bottom: 0.5rem;
}

/* Espaciado entre las preguntas */
.campo {
    margin-bottom: 1.5rem;
}

.campo label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #475569;
}

/* Las cajitas de texto */
.campo input[type="text"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 1rem;
    box-sizing: border-box;
    /* Para que no se salga de la caja */
    transition: all 0.3s ease;
}

/* Efecto cuando haces clic para escribir */
.campo input[type="text"]:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Las opciones de los Checkboxes */
.opcion-paso {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    padding: 8px;
    border-radius: 6px;
    background-color: #f8fafc;
    border: 1px solid #f1f5f9;
    transition: background-color 0.2s;
}

.opcion-paso:hover {
    background-color: #e2e8f0;
}

.opcion-paso input[type="checkbox"] {
    width: 1.2rem;
    height: 1.2rem;
    cursor: pointer;
}

.opcion-paso span {
    font-size: 0.95rem;
    color: #334155;
}

/* Botones */
.acciones {
    margin-top: 2rem;
    display: flex;
    gap: 12px;
}

.btn-guardar {
    background: #3b82f6;
    /* Azul moderno */
    color: white;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    flex: 1;
    /* Para que ocupe el espacio disponible */
    transition: background 0.3s;
}

.btn-guardar:hover {
    background: #2563eb;
}

.btn-cancelar {
    background: #cbd5e1;
    /* Gris suave */
    color: #334155;
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s;
}

.btn-cancelar:hover {
    background: #94a3b8;
    color: white;
}
</style>