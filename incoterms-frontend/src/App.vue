<template>
  <div class="contenedor-principal"></div>

  <div class="cabecera">
    <h1>Gestión de Incoterms</h1>
    <p>CRUD</p>
  </div>

  <div class="layout">
    <FormularioIncoterms :pasos="listaPasos" :incotermEditando="incotermAEditar" @guardar="guardarDatosenAPI"
      @cancelar="incotermAEditar = null" />


    <TablaIncoterms :incoterms="listaIncoterms" @editar="editarIncoterm" @borrar="borrarIncoterm" />

  </div>
</template>


<script setup>

import { ref, onMounted } from 'vue';

import FormularioIncoterms from './componentes/FormularioIncoterms.vue';
import TablaIncoterms from './componentes/TablaIncoterms.vue';


const listaIncoterms = ref([]);
const listaPasos = ref([]);
const incotermAEditar = ref(null);

const URL_API = 'http://127.0.0.1:8000/api';

const cargarPasos = async () => {

  const respuesta = await fetch(`${URL_API}/tracking-steps`);

  const json = await respuesta.json();

  listaPasos.value = json.data;


};

const obtenerIncoterms = async () => {

  const respuesta = await fetch(`${URL_API}/incoterms`);

  const json = await respuesta.json();

  listaIncoterms.value = json.data;

};

onMounted(() => {

  obtenerIncoterms();
  cargarPasos();

});


const guardarDatosenAPI = async (datosFormulario) => {

  const esActualizacion = datosFormulario.ID != null;

  // si el formulario tiene un ID Si tiene, es un PUT. Si es null, es un POST .


  const url = esActualizacion ? `${URL_API}/incoterms/${datosFormulario.ID}` : `${URL_API}/incoterms`;

  const metodo = esActualizacion ? 'PUT' : 'POST';


  await fetch(url, {
    method: metodo,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json'
    },
    body: JSON.stringify(datosFormulario)
  });


  incotermAEditar.value = null;
  obtenerIncoterms();



};



const borrarIncoterm = async (id) => {

  await fetch(`${URL_API}/incoterms/${id}`, {
    method: 'DELETE'
  });

  obtenerIncoterms();

};


const editarIncoterm = (incoterm) => {
  incotermAEditar.value = incoterm;
};




</script>


<style>
body {
  background-color: #f1f5f9;
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 20px;
}

.contenedor-principal {
  max-width: 1200px;
  margin: 0 auto;
}

.cabecera {
  margin-bottom: 20px;
}

.cabecera h1 {
  margin: 0;
  color: #0f172a;
}

.cabecera p {
  color: #64748b;
  margin-top: 5px;
}

.layout-dos-columnas {
  display: grid;
  grid-template-columns: 350px 1fr;
  gap: 20px;
  align-items: start;
}
</style>