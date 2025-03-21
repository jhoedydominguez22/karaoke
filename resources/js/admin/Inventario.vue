<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Solicitudes Pendientes</h2>

    <!-- Tabla de solicitudes -->
    <table class="table table-striped table-bordered table-hover">
      <thead class="thead-dark">
        <tr>
          <th>#</th>
          <th>Nombre del Usuario</th>
          <th>Nombre de la Canción</th>
          <th>Artista</th>
          <th>Carátula</th>
          <th>Dedicatoria</th>
          <th>Número de Mesa</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(solicitud, index) in solicitudesPendientes" :key="solicitud._id.$oid">
          <td>{{ index + 1 }}</td>
          <td>{{ solicitud.nombreUsuario }}</td>
          <td>{{ solicitud.cancion }}</td>
          <td>{{ solicitud.artista }}</td>
          <td>
            <img v-if="solicitud.albumCover" :src="solicitud.albumCover" alt="Carátula" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
            <span v-else>No disponible</span>
          </td>
          <td>{{ solicitud.dedicatoria || 'N/A' }}</td>
          <td>{{ solicitud.mesa || 'No especificada' }}</td>
          <td>
            <button class="btn btn-success btn-sm" @click="marcarComoAtendida(solicitud._id.$oid)">
              Marcar como Atendida
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      solicitudes: [], // Todas las solicitudes del backend
    };
  },
  created() {
    this.fetchSolicitudes();
  },
  computed: {
    // Filtrar solicitudes para mostrar solo las que no han sido atendidas
    solicitudesPendientes() {
      return this.solicitudes.filter(solicitud => !solicitud.atendida);
    }
  },
  methods: {
    fetchSolicitudes() {
      axios.get('/documentos')
        .then(response => {
          this.solicitudes = response.data.sort((a, b) => new Date(a.fecha_solicitud) - new Date(b.fecha_solicitud));
        })
        .catch(error => {
          console.error('Hubo un error al obtener las solicitudes:', error);
        });
    },
    marcarComoAtendida(id) {
      if (!id) {
        console.error('ID no válido:', id);
        return;
      }

      axios.put(`/solicitudes/${id}/atendida`)
        .then(() => {
          // Actualizar el estado de la solicitud en el frontend
          this.solicitudes = this.solicitudes.filter(s => s._id.$oid !== id);
        })
        .catch(error => {
          console.error('Error al marcar como atendida:', error);
        });
    },
  },
};
</script>


<style scoped>
/* Estilos para la tabla */
.table {
  margin-top: 20px;
  border-radius: 8px;
  overflow: hidden;
}

.table th, .table td {
  vertical-align: middle;
  text-align: center;
  padding: 10px;
}

.table-hover tbody tr:hover {
  background-color: #f1f1f1;
}

.table-bordered {
  border: 2px solid #ddd;
}

.thead-dark th {
  background-color: #343a40;
  color: white;
}

/* Estilos para las carátulas */
.img-thumbnail {
  border: 1px solid #ddd;
  border-radius: 8px;
}

/* Estilos para los botones */
.btn-sm {
  padding: 6px 12px;
  font-size: 14px;
  border-radius: 20px;
}

.btn-success {
  background-color: #28a745;
  border-color: #28a745;
}

.btn-success:hover {
  background-color: #218838;
  border-color: #1e7e34;
}

/* Estilos para los badges */
.badge {
  font-size: 14px;
  padding: 8px 12px;
  border-radius: 12px;
}

/* Estilo del encabezado */
h2 {
  color: #343a40;
  font-size: 2rem;
}
</style>
