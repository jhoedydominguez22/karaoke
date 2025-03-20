<template>
  <div>
    <h1>NO SIRVE DE MOMENTO</h1>
    <div class="table-responsive">
      <table class="table table-bordered table-hover table-sm">
        <thead class="thead-dark">
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
          <tr v-for="(documento, index) in documentos" :key="index">
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
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Editar Documento</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="updateDocumento">
              <div class="form-group">
                <label for="editInventario">No. Inventario</label>
                <input type="text" id="editInventario" v-model="editDocumento.inventario" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="editFotoUnica">Foto Única</label>
                <select id="editFotoUnica" v-model="editDocumento.fotoUnica" class="form-control" required>
                  <option value="Si">Si</option>
                  <option value="No">No</option>
                </select>
              </div>

              <div class="form-group">
                <label for="editMedidas">Medidas</label>
                <input type="text" id="editMedidas" v-model="editDocumento.medidas" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="editCantidad">Cantidad</label>
                <input type="number" id="editCantidad" v-model="editDocumento.cantidad" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="editColor">Color</label>
                <select id="editColor" v-model="editDocumento.color" class="form-control" required>
                  <option value="BN">Blanco y Negro</option>
                  <option value="C">Color</option>
                </select>
              </div>

              <div class="form-group">
                <label for="editDescripcionImagen">Descripción de la Imagen</label>
                <textarea id="editDescripcionImagen" v-model="editDocumento.descripcionImagen" class="form-control" rows="3" required></textarea>
              </div>

              <div class="form-group">
                <label for="editFormato">Formato</label>
                <select id="editFormato" v-model="editDocumento.formato" class="form-control" required>
                  <option value="Si">Gran formato</option>
                  <option value="No">Estandar</option>
                </select>
              </div>

              <div class="form-group">
                <label for="editClasificacionContenido">Tema / Clasificación del Contenido</label>
                <input type="text" id="editClasificacionContenido" v-model="editDocumento.clasificacionContenido" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="editFondo">Fondo</label>
                <input type="text" id="editFondo" v-model="editDocumento.fondo" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="editSerie">Serie</label>
                <input type="text" id="editSerie" v-model="editDocumento.serie" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="editSubSerie">Sub Serie</label>
                <input type="text" id="editSubSerie" v-model="editDocumento.subSerie" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="editSeccion">Sección</label>
                <input type="text" id="editSeccion" v-model="editDocumento.seccion" class="form-control" required>
              </div>

              <div class="form-group">
                <label for="editDescripcion">Descripción</label>
                <textarea id="editDescripcion" v-model="editDocumento.descripcion" class="form-control" rows="3" required></textarea>
              </div>

              <div class="form-group">
                <label>Daño</label>
                <div class="d-flex flex-wrap">
                  <div v-for="(dano, index) in danos" :key="index" class="custom-control custom-checkbox mr-3 mb-2">
                    <input type="checkbox" class="custom-control-input" :id="'editDano_' + index" v-model="editDocumento.dano" :value="dano">
                    <label class="custom-control-label" :for="'editDano_' + index">{{ dano }}</label>
                  </div>
                </div>
              </div>

              <div class="form-group">
                <label for="editArchivos">Recurso digital</label><br>
                <input type="file" id="editArchivos" ref="editArchivos" multiple @change="handleFileUpload">
              </div>

              <div class="form-group">
                <label for="editCapturador">Capturador</label>
                <input type="text" id="editCapturador" v-model="editDocumento.capturador" class="form-control" disabled>
              </div>

              <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
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
      danos: ['Rotura', 'Suciedad', 'Cintas Adhesivas', 'Hongo', 'Comején']
    };
  },
  mounted() {
    this.getAllDocuments();
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
    editarDocumento(documento) {
      this.editDocumento = { ...documento, dano: documento.dano || [] };
      $('#editModal').modal('show');
    },
    handleFileUpload(event) {
      this.files = Array.from(event.target.files);
    },
    updateDocumento() {
      const idDocumento = this.editDocumento._id.$oid || this.editDocumento._id;
      const formData = new FormData();

      // Crear una copia del documento editado sin el campo '_id'
      const documentData = { ...this.editDocumento };
      delete documentData._id;

      // Añadir todos los campos del documento editado como JSON
      formData.append('document_data', JSON.stringify(documentData));

      // Añadir archivos
      this.files.forEach(file => {
        formData.append('files[]', file);
      });

      axios.post(`/documentos/update/${idDocumento}`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      .then(() => {
        Swal.fire({
          icon: 'success',
          title: 'Documento actualizado',
          showConfirmButton: false,
          timer: 1500
        });
        this.getAllDocuments();
        $('#editModal').modal('hide');
      })
      .catch(error => {
        console.error('Error al actualizar documento:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error al actualizar',
          text: 'No se pudo actualizar el documento. Por favor, intenta de nuevo.'
        });
      });
    },
    eliminarDocumento(idDocumento) {
      Swal.fire({
        title: '¿Estás seguro?',
        text: "No podrás revertir esto",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete(`/documentos/${idDocumento}`)
            .then(() => {
              Swal.fire(
                'Eliminado',
                'El documento ha sido eliminado',
                'success'
              );
              this.getAllDocuments();
            })
            .catch(error => {
              console.error('Error al eliminar documento:', error);
              Swal.fire({
                icon: 'error',
                title: 'Error al eliminar',
                text: 'No se pudo eliminar el documento. Por favor, intenta de nuevo.'
              });
            });
        }
      });
    },
    openFancybox(archivos) {
      const items = archivos.map((archivo) => ({
        src: `http://localhost:8000/storage/${encodeURIComponent(archivo)}`,
        opts: {
          caption: archivo
        }
      }));

      Fancybox.show(items);
    }
  }
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
}
.table {
  width: 100%;
  max-width: 100%;
  margin-bottom: 1rem;
  background-color: transparent;
}
.table th, .table td {
  padding: 0.75rem;
  vertical-align: top;
  border-top: 1px solid #dee2e6;
}
.table thead th {
  vertical-align: bottom;
  border-bottom: 2px solid #dee2e6;
}
.table tbody + tbody {
  border-top: 2px solid #dee2e6;
}
.table-sm th, .table-sm td {
  padding: 0.3rem;
}
.table-bordered {
  border: 1px solid #dee2e6;
}
.table-bordered th, .table-bordered td {
  border: 1px solid #dee2e6;
}
.table-bordered thead th, .table-bordered thead td {
  border-bottom-width: 2px;
}
.table-hover tbody tr:hover {
  background-color: rgba(0, 0, 0, 0.075);
}
@media (max-width: 768px) {
  .table thead {
    display: none;
  }
  .table, .table tbody, .table tr, .table td {
    display: block;
    width: 100%;
  }
  .table tr {
    margin-bottom: 1rem;
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
