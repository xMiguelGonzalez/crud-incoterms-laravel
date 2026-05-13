<template>
  <div class="caja-formulario">
    <h3>{{ incotermEditando ?  ' Editar Incoterm' : 'Crear Incoterm' }}</h3>

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
      <button v-if="incotermEditando" class="btn-cancelar" @click="cancelar">Cancelar</button>
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


