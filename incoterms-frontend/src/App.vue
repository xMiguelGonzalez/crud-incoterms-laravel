<template>
  <div class="dashboard-container">
    
    <transition-group name="toast">
      <div v-if="notificacion.visible" :class="['toast', notificacion.tipo]" :key="1">
        {{ notificacion.mensaje }}
      </div>
    </transition-group>

    <header class="app-header">
      <div class="logo-container">
        <span class="logo">🚢</span>
      </div>
      <div class="title-area">
        <h1>Crud de INCOTERMS</h1>
        <p>Control de Distribución y Transferencia de Riesgos</p>
      </div>
    </header>

    <div class="layout-dos-columnas">
      
      <aside class="tarjeta-formulario" :class="{ 'form-editing': modoEdicion }">
        <div class="form-header">
          <h2>{{ modoEdicion ? '✏️ Editar Registro' : '✨ Nuevo Registro' }}</h2>
          <div v-if="modoEdicion" class="edit-indicator">Editando ID: #{{ formulario.ID }}</div>
        </div>
        
        <div class="grupo-input">
          <label>Incoterm</label>
          <select v-model="formulario.INCOTERM_TYPE_ID" :disabled="cargando">
            <option value="" disabled>Selecciona el tipo...</option>
            <option v-for="tipo in tiposList" :key="tipo.ID" :value="tipo.ID">
              {{ tipo.CODE }} - {{ tipo.NAME }}
            </option>
          </select>
        </div>

        <div class="grupo-input">
          <label>Punto de Entrega (Hito Logístico)</label>
          <select v-model="formulario.TRACKING_STEP_ID" :disabled="cargando">
            <option value="" disabled>Selecciona el momento...</option>
            <option v-for="paso in pasosList" :key="paso.ID" :value="paso.ID">
              [{{ paso.ORDER_NUM }}] {{ paso.NAME }}
            </option>
          </select>
        </div>

        <div class="acciones-formulario">
          <button class="boton-guardar" @click="guardarIncoterm" :disabled="cargando || !formulario.INCOTERM_TYPE_ID || !formulario.TRACKING_STEP_ID">
            <span v-if="!cargando">{{ modoEdicion ? 'Actualizar Registro' : 'Crear Incoterm' }}</span>
            <span v-else class="spinner"></span>
          </button>
          
          <button v-if="modoEdicion" class="boton-cancelar" @click="cancelarEdicion" :disabled="cargando">
            Cancelar
          </button>
        </div>
      </aside>

      <main class="tarjeta-tabla">
        <div class="cabecera-tabla">
          <h2>📦 Registros en SQL Server</h2>
          <div class="stats">
            <span class="contador">{{ incotermsList.length }} Items</span>
            <button class="btn-refresh" @click="obtenerIncoterms" :class="{ 'spinning': cargandoTabla }">🔄</button>
          </div>
        </div>

        <div class="contenedor-tabla">
          <table v-if="!cargandoTabla">
            <thead>
              <tr>
                <th>Código</th>
                <th>Descripción del Riesgo</th>
                <th>Momento de Entrega</th>
                <th class="col-acciones"></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="incoterm in incotermsList" :key="incoterm.ID" :class="{ 'row-editing': formulario.ID === incoterm.ID }">
                <td><span class="badge-code">{{ incoterm.incotermType?.CODE || incoterm.incoterm_type?.CODE || '???' }}</span></td>
                <td><div class="txt-main">{{ incoterm.incotermType?.NAME || incoterm.incoterm_type?.NAME || 'Sin nombre' }}</div></td>
                <td><div class="txt-step">{{ incoterm.trackingStep?.NAME || incoterm.tracking_step?.NAME || 'No asignado' }}</div></td>
                <td class="celda-acciones">
                  <button class="btn-action edit" @click="cargarDatosParaEditar(incoterm)">✏️</button>
                  <button class="btn-action delete" @click="borrarIncoterm(incoterm.ID)">🗑️</button>
                </td>
              </tr>
            </tbody>
          </table>
          
          <div v-else class="loading-state">
            <div class="spinner-large"></div>
            <p>Sincronizando con el servidor...</p>
          </div>

          <div v-if="incotermsList.length === 0 && !cargandoTabla" class="mensaje-vacio">
            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" />
            <p>No hay datos. Empieza creando un Incoterm a la izquierda.</p>
          </div>
        </div>
      </main>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

// ESTADOS
const incotermsList = ref([]); 
const tiposList = ref([]);     
const pasosList = ref([]);     
const formulario = ref({ ID: null, INCOTERM_TYPE_ID: '', TRACKING_STEP_ID: '' });
const modoEdicion = ref(false);
const cargando = ref(false);
const cargandoTabla = ref(false);

// SISTEMA DE NOTIFICACIONES (Toast)
const notificacion = ref({ visible: false, mensaje: '', tipo: 'success' });
const mostrarToast = (msj, tipo = 'success') => {
  notificacion.value = { visible: true, mensaje: msj, tipo: tipo };
  setTimeout(() => { notificacion.value.visible = false; }, 3000);
};

const API_BASE = 'http://127.0.0.1:8000/api';

// CARGAS INICIALES
const cargarDesplegables = async () => {
  try {
    const [resT, resP] = await Promise.all([
      fetch(`${API_BASE}/incoterm-types`),
      fetch(`${API_BASE}/tracking-steps`)
    ]);
    const [jsonT, jsonP] = await Promise.all([resT.json(), resP.json()]);
    tiposList.value = jsonT.data;
    pasosList.value = jsonP.data;
  } catch (e) { mostrarToast("Error al cargar diccionarios", "error"); }
};

const obtenerIncoterms = async () => {
  cargandoTabla.value = true;
  try {
    const res = await fetch(`${API_BASE}/incoterms`);
    const json = await res.json();
    if (json.success) incotermsList.value = json.data;
  } catch (e) { mostrarToast("Error de conexión con la API", "error"); }
  finally { cargandoTabla.value = false; }
};

// ACCIONES
const cargarDatosParaEditar = (incoterm) => {
  modoEdicion.value = true;
  formulario.value = {
    ID: incoterm.ID,
    INCOTERM_TYPE_ID: incoterm.INCOTERM_TYPE_ID,
    TRACKING_STEP_ID: incoterm.TRACKING_STEP_ID
  };
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelarEdicion = () => {
  modoEdicion.value = false;
  formulario.value = { ID: null, INCOTERM_TYPE_ID: '', TRACKING_STEP_ID: '' };
};

const guardarIncoterm = async () => {
  cargando.value = true;
  const url = modoEdicion.value ? `${API_BASE}/incoterms/${formulario.value.ID}` : `${API_BASE}/incoterms`;
  const metodo = modoEdicion.value ? 'PUT' : 'POST';

  try {
    const res = await fetch(url, {
      method: metodo,
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(formulario.value)
    });
    const json = await res.json();

    if (json.success) {
      mostrarToast(modoEdicion.value ? "Registro actualizado" : "Incoterm creado con éxito");
      cancelarEdicion();
      obtenerIncoterms();
    } else {
      mostrarToast(json.message || "Error en los datos", "error");
    }
  } catch (e) {
    mostrarToast("Fallo en el servidor", "error");
  } finally { cargando.value = false; }
};

const borrarIncoterm = async (id) => {
  if (!confirm('¿Confirmas la eliminación definitiva de este registro?')) return;
  
  try {
    const res = await fetch(`${API_BASE}/incoterms/${id}`, { 
        method: 'DELETE',
        headers: { 'Accept': 'application/json' } 
    });
    const json = await res.json();
    
    if (json.success) {
      mostrarToast("Registro eliminado");
      obtenerIncoterms();
    } else {
      mostrarToast(json.message, "error");
    }
  } catch (e) { mostrarToast("Error al borrar", "error"); }
};

onMounted(() => {
  cargarDesplegables();
  obtenerIncoterms();
});
</script>

<style scoped>
/* FUENTE Y BASE */
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');

.dashboard-container {
  max-width: 1300px;
  margin: 0 auto;
  padding: 40px 20px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  background-color: #f8fafc;
  min-height: 100vh;
}

/* TOAST ANIMATION */
.toast {
  position: fixed;
  top: 20px;
  right: 20px;
  padding: 15px 25px;
  border-radius: 12px;
  color: white;
  font-weight: 600;
  z-index: 9999;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2);
}
.toast.success { background: #10b981; }
.toast.error { background: #ef4444; }

.toast-enter-active, .toast-leave-active { transition: all 0.4s ease; }
.toast-enter-from { opacity: 0; transform: translateX(50px); }
.toast-leave-to { opacity: 0; transform: scale(0.9); }

/* HEADER */
.app-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 40px;
}
.logo-container {
  background: white;
  width: 60px;
  height: 60px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 18px;
  font-size: 2rem;
  box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
}
.title-area h1 { font-size: 2.2rem; margin: 0; color: #0f172a; }
.badge-v1 { font-size: 0.8rem; background: #e2e8f0; padding: 4px 8px; border-radius: 6px; vertical-align: middle; }
.title-area p { margin: 5px 0 0; color: #64748b; }

/* GRID */
.layout-dos-columnas {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 30px;
}

/* CARDS */
.tarjeta-formulario, .tarjeta-tabla {
  background: white;
  border-radius: 24px;
  padding: 30px;
  border: 1px solid #f1f5f9;
  box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}

.form-editing { border: 2px solid #3b82f6; background: #f0f7ff; }

/* INPUTS */
.grupo-input { margin-bottom: 25px; }
label { display: block; font-weight: 600; color: #334155; margin-bottom: 10px; font-size: 0.9rem; }
select {
  width: 100%;
  padding: 14px;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.2s;
  background: white;
}
select:focus { border-color: #3b82f6; outline: none; }

/* BOTONES */
.boton-guardar {
  width: 100%;
  background: #0f172a;
  color: white;
  padding: 16px;
  border-radius: 12px;
  display: flex;
  justify-content: center;
  align-items: center;
}
.boton-guardar:hover:not(:disabled) { background: #334155; transform: translateY(-2px); }
.boton-guardar:disabled { opacity: 0.5; cursor: not-allowed; }

.boton-cancelar { width: 100%; background: transparent; color: #64748b; margin-top: 10px; border: 1px solid #e2e8f0; }

/* TABLA */
.cabecera-tabla { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
.stats { display: flex; align-items: center; gap: 15px; }
.contador { background: #f1f5f9; padding: 6px 12px; border-radius: 10px; font-size: 0.9rem; font-weight: 600; }

table { width: 100%; border-spacing: 0 10px; border-collapse: separate; }
th { text-align: left; padding: 10px 20px; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; }
td { background: white; padding: 20px; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
td:first-child { border-left: 1px solid #f1f5f9; border-radius: 15px 0 0 15px; }
td:last-child { border-right: 1px solid #f1f5f9; border-radius: 0 15px 15px 0; }

tr:hover td { background: #f8fafc; }
.row-editing td { background: #eff6ff !important; border-color: #bfdbfe; }

.badge-code { background: #0f172a; color: white; padding: 5px 10px; border-radius: 8px; font-weight: 800; font-size: 0.85rem; }
.txt-main { font-weight: 600; color: #1e293b; }
.txt-step { color: #64748b; font-size: 0.95rem; }

/* ACTIONS */
.btn-action { background: #f1f5f9; border: none; padding: 10px; border-radius: 10px; cursor: pointer; transition: 0.2s; }
.btn-action.edit:hover { background: #fef3c7; }
.btn-action.delete:hover { background: #fee2e2; }

/* SPINNER */
.spinner {
  width: 20px;
  height: 20px;
  border: 3px solid rgba(255,255,255,0.3);
  border-top-color: white;
  border-radius: 50%;
  animation: spin 1s infinite linear;
}
@keyframes spin { to { transform: rotate(360deg); } }

.loading-state { text-align: center; padding: 50px; color: #94a3b8; }
.spinner-large { width: 40px; height: 40px; border: 4px solid #f1f5f9; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s infinite linear; margin: 0 auto 15px; }

.btn-refresh.spinning { animation: spin 1s infinite linear; }
</style>