<template>
  <div>
    <h1>Consultar Documentos</h1>

    <div class="table-responsive">
      <!-- Formulario de Búsqueda -->
      <form class="d-flex mb-4" @submit.prevent="buscar">
        <input v-model="palabraClave" class="form-control me-2" type="search" placeholder="Buscar" aria-label="Buscar">
      </form>

      <table class="table table-minimal">
        <thead>
          <tr>
            <th style="min-width: 100px;">Inventario</th>
            <th style="min-width: 100px;">Formato</th>
            <th style="min-width: 100px;">Foto Única</th>
            <th style="min-width: 100px;">Medidas</th>
            <th style="min-width: 100px;">Cantidad</th>
            <th style="min-width: 100px;">Color</th>
            <th style="min-width: 150px;">Descripción Imagen</th>
            <th style="min-width: 200px;">Clasificación Contenido</th>
            <th style="min-width: 100px;">Fondo</th>
            <th style="min-width: 100px;">Serie</th>
            <th style="min-width: 100px;">Sub Serie</th>
            <th style="min-width: 100px;">Sección</th> 
            <th style="min-width: 200px;">Descripción</th>
            <th style="min-width: 100px;">Capturador</th>
            <th style="min-width: 150px;">Daño</th>
            <th style="min-width: 100px;">Archivos</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="documentosPaginados.length === 0">
            <td colspan="16" class="text-center">No se obtuvieron datos</td>
          </tr>
          
          <tr v-for="(documento, index) in documentosPaginados" :key="index">
            <td>{{ documento.inventario }}</td>
            <td>{{ documento.formato }}</td>
            <td>{{ documento.fotoUnica }}</td>
            <td>{{ documento.medidas }}</td>
            <td>{{ documento.cantidad }}</td>
            <td>{{ documento.color }}</td>
            <td>{{ documento.descripcionImagen }}</td>
            <td>{{ documento.clasificacionContenido }}</td>
            <td>{{ documento.fondo }}</td>
            <td>{{ documento.serie }}</td>
            <td>{{ documento.subSerie }}</td>
            <td>{{ documento.seccion }}</td>
            <td>{{ documento.descripcion }}</td>
            <td>{{ documento.capturador }}</td>
            <td>{{ documento.dano.join(', ') }}</td>
            <td>
              <a v-if="documento.archivos" @click.prevent="openFancybox(documento.archivos)" href="#">
                <i class="fas fa-external-link-alt"></i>
              </a>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Paginador -->
      <nav aria-label="Page navigation">
        <ul class="pagination">
          <li class="page-item" :class="{ disabled: paginaActual === 1 }">
            <a class="page-link" href="#" @click.prevent="cambiarPagina(paginaActual - 1)">Anterior</a>
          </li>
          <li class="page-item" v-for="pagina in totalPaginas" :key="pagina" :class="{ active: pagina === paginaActual }">
            <a class="page-link" href="#" @click.prevent="cambiarPagina(pagina)">{{ pagina }}</a>
          </li>
          <li class="page-item" :class="{ disabled: paginaActual === totalPaginas }">
            <a class="page-link" href="#" @click.prevent="cambiarPagina(paginaActual + 1)">Siguiente</a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Fancybox } from "@fancyapps/ui";
import '@fancyapps/ui/dist/fancybox/fancybox.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

export default {
  data() {
    return {
      documentos: [],
      editDocumento: {
        dano: []
      },
      files: [],
      palabraClave: '',
      danos: ['Rotura', 'Suciedad', 'Cintas Adhesivas', 'Hongo', 'Comején'],
      paginaActual: 1,
      documentosPorPagina: 14
    };
  },
  computed: {
    documentosFiltrados() { 
      if (!this.palabraClave) {
        return this.documentos;
      }
      const palabraClaveLower = this.palabraClave.toLowerCase();
      return this.documentos.filter(documento =>
        Object.values(documento).some(value =>
          value.toString().toLowerCase().includes(palabraClaveLower)
        )
      );
    },
    documentosPaginados() {
      const inicio = (this.paginaActual - 1) * this.documentosPorPagina;
      const fin = inicio + this.documentosPorPagina;
      return this.documentosFiltrados.slice(inicio, fin);
    },
    totalPaginas() {
      return Math.ceil(this.documentosFiltrados.length / this.documentosPorPagina);
    }
  },
  mounted() {
    this.getAllDocuments();
    this.getCurrentUser();
    Fancybox.bind("[data-fancybox]", {
      // Opciones adicionales de FancyBox si es necesario
    });
  },
  methods: {
    getAllDocuments() {
      axios.get('/documentosDesechados')
        .then(response => {
          this.documentos = response.data;
        })
        .catch(error => {
          console.error('Error al obtener documentos:', error);
        });
    },
    async getCurrentUser() {
      try {
        const response = await axios.get('/currentuser');
        this.currentUser = response.data;
      } catch (error) {
        console.error('Error al obtener usuario actual:', error);
      }
    },
    handleFileUpload(event) {
      this.files = Array.from(event.target.files);
    },
    openFancybox(archivos) {
      const baseURL = `${window.location.protocol}//${window.location.hostname}:8000`;

      const items = archivos.map((archivo) => ({
        src: `${baseURL}/storage/${encodeURIComponent(archivo)}`,
        opts: {
          caption: archivo
        }
      }));

      Fancybox.show(items);
    },
    cambiarPagina(pagina) {
      if (pagina >= 1 && pagina <= this.totalPaginas) {
        this.paginaActual = pagina;
      }
    }
  }
};
</script>

<style scoped>



.modal-dialog {
  max-width: 60%;
}

.table {
  table-layout: auto;
  word-wrap: break-word;
}

.table th,
.table td {
  padding: 12px;
  vertical-align: middle;
  white-space: nowrap;
}




.table-responsive {
  margin: 30px 0;
 
  -webkit-overflow-scrolling: touch;
  overflow-x: auto;
}




.table-hover tbody tr:hover {
  background-color: #f5f5f5;
}

.table-sm th,
.table-sm td {
  padding: 8px;
}

.pagination {
  display: flex;
  justify-content: center;
  margin-top: 20px;
}

@media (max-width: 768px) {
  .table thead {
    display: none;
  }

  .table tbody,
  .table tr,
  .table td {
    display: block;
    width: 100%;
  }

  .table tr {
    margin-bottom: 15px;
  }

  .table td {
    text-align: right;
    padding-left: 50%;
    position: relative;
  }

  .table td::before {
    content: attr(data-label);
    position: absolute;
    left: 0;
    width: 50%;
    padding-left: 15px;
    font-weight: bold;
    text-align: left;
  }
}
</style>
