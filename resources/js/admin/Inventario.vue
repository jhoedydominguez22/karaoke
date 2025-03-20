<template>
  <div class="container mt-5">
    <h2 class="text-center mb-4">Solicitudes Enviadas a los DJ</h2>

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
          <th>Número de Mesa</th> <!-- Nueva columna para el número de mesa -->
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(solicitud, index) in solicitudes" :key="solicitud._id.$oid">
          <td>{{ index + 1 }}</td>
          <td>{{ solicitud.nombreUsuario }}</td> <!-- Nombre del Usuario -->
          <td>{{ solicitud.cancion }}</td> <!-- Nombre de la Canción -->
          <td>{{ solicitud.artista }}</td> <!-- Nombre del Artista -->
          <td>
            <!-- Mostrar la carátula si existe -->
            <img v-if="solicitud.albumCover" :src="solicitud.albumCover" alt="Carátula" class="img-thumbnail" style="max-width: 80px; max-height: 80px; object-fit: cover;">
            <span v-else>No disponible</span>
          </td>
          <td>{{ solicitud.dedicatoria || 'N/A' }}</td> <!-- Dedicatoria -->
          <td>{{ solicitud.mesa || 'No especificada' }}</td> <!-- Mostrar el número de mesa -->
          <td>
            <span v-if="solicitud.atendida" class="badge bg-success">Atendida</span>
            <span v-else class="badge bg-warning">Pendiente</span>
          </td>
          <td>
            <button v-if="!solicitud.atendida" class="btn btn-success btn-sm" @click="marcarComoAtendida(solicitud._id.$oid)">
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
      solicitudes: [], // Aquí guardaremos las solicitudes obtenidas del backend
    };
  },
  created() {
    this.fetchSolicitudes();
  },
  methods: {
    // Método para obtener las solicitudes de los DJ desde el backend
    fetchSolicitudes() {
      axios.get('/documentos')
        .then(response => {
          this.solicitudes = response.data.sort((a, b) => new Date(a.fecha_solicitud) - new Date(b.fecha_solicitud));
        })
        .catch(error => {
          console.error('Hubo un error al obtener las solicitudes:', error);
        });
    },

    // Método para formatear la fecha
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('es-MX', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
      });
    },

    // Método para marcar una solicitud como atendida
    marcarComoAtendida(id) {
      // Verifica que el id esté en formato correcto antes de enviarlo
      const validId = id || id.$oid;

      if (!validId) {
        console.error('ID no válido:', id);
        return;
      }

      axios.put(`/solicitudes/${validId}/atendida`)
        .then(response => {
          // Actualizar el estado de la solicitud en el frontend
          const solicitud = this.solicitudes.find(s => s._id.$oid === validId);
          if (solicitud) {
            solicitud.atendida = true;
          }
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
