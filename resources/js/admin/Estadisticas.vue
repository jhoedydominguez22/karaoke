<template>
  <div>
    <h3 class="text-center mb-4">Generar Código QR para la Mesa</h3>

    <div class="mb-3">
      <input v-model="numeroMesa" type="number" class="form-control" placeholder="Número de mesa" />
    </div>

    <button class="btn btn-primary w-100" @click="generarQR">Generar QR</button>

    <div v-if="qrUrl" class="mt-4 text-center">
      <h5>Código QR de la Mesa {{ numeroMesa }}</h5>
      <div class="qr-card">
        <img :src="qrUrl" alt="Código QR generado" class="qr-img" />
      </div>
      <button class="btn btn-success mt-3" @click="guardarQR">Guardar QR</button>
    </div>
    

    <div v-if="qrCodes.length" class="mt-5">
      <h4 class="text-center">Códigos QR Generados</h4>
      <div class="row">
        <div v-for="(qr, index) in qrCodes" :key="index" class="col-md-4 col-sm-6 mb-4">
          <div class="card text-center shadow-lg">
            <div class="card-body d-flex flex-column align-items-center">
              <h5 class="card-title">Mesa {{ qr.numeroMesa }}</h5>
              <div class="d-flex justify-content-center align-items-center">
                <img :src="qr.qrUrl" alt="QR" class="qr-img" />
                <div class="d-flex flex-column ml-3">
                  <button class="btn btn-dark mb-2" @click="imprimirQR(qr.qrUrl, qr.numeroMesa)">
                    Imprimir QR
                  </button>
                  <button class="btn btn-danger" @click="eliminarQR(qr)">
                    Eliminar QR
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
import QRCode from 'qrcode';
import axios from 'axios'; // Asegúrate de tener Axios instalado
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      numeroMesa: '', // Número de mesa que el admin quiere usar
      qrUrl: null,    // URL del código QR generado
      qrCodes: [],    // Lista de códigos QR existentes
    };
  },
  methods: {
    // Método para generar el código QR
    generarQR() {
      if (this.numeroMesa) {
        const url = `http://localhost:8000/bienvenida?mesa=${this.numeroMesa}`;

        QRCode.toDataURL(url)
          .then((dataUrl) => {
            this.qrUrl = dataUrl; // Asignamos el URL del QR a la variable
          })
          .catch((error) => {
            console.error('Error al generar el código QR:', error);
          });
      } else {
        alert('Por favor ingresa un número de mesa');
      }
    },

    // Método para guardar el QR en MongoDB
    guardarQR() {
      if (this.numeroMesa && this.qrUrl) {
        axios.post('/guardar-qr', {
          numeroMesa: this.numeroMesa,
          qrUrl: this.qrUrl,
        })
          .then((response) => {
            console.log(response.data.message);
            // Resetear los datos para ocultar el QR y el botón de guardar
            this.qrUrl = null;
            this.numeroMesa = '';

            // Actualizar la lista de códigos QR
            this.obtenerQRCodes();
          })
          .catch((error) => {
            console.error('Error al guardar el código QR:', error);
          });
      } else {
        alert('Por favor, asegúrate de generar un QR primero.');
      }
    },

    // Método para obtener la lista de códigos QR
    obtenerQRCodes() {
      axios.get('/listado-qr')
        .then((response) => {
          
          this.qrCodes = response.data; // Guardar los QR obtenidos en el array
        })
        .catch((error) => {
          console.error('Error al obtener los códigos QR:', error);
        });
    },
     // Eliminar un código QR
     eliminarQR(qr) {
  const qrId = qr._id ? qr._id.$oid : null; // Verifica si qr._id existe antes de intentar acceder a su propiedad
  if (!qrId) {
    console.error('No se encontró un ID válido para el QR');
    return; // Salir si no se encontró un ID
  }

  Swal.fire({
    title: '¿Estás seguro?',
    text: '¿Quieres eliminar este código QR?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminarlo'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await axios.delete(`/eliminar-qr/${qrId}`);
        this.qrCodes = this.qrCodes.filter(qrItem => qrItem._id.$oid !== qrId);
        Swal.fire('Eliminado', 'El código QR se ha eliminado exitosamente.', 'success');
      } catch (error) {
        console.error('Error al eliminar el código QR:', error);
        Swal.fire('Error', 'Hubo un problema al eliminar el código QR. Por favor, inténtalo de nuevo.', 'error');
      }
    }
  });
}
,
    // Método para imprimir el QR
    imprimirQR(qrUrl, numeroMesa) {
      const printWindow = window.open('', '', 'width=800,height=800');
      printWindow.document.write(`
      <html>
        <head>
          <title>Imprimir Código QR</title>
          <style>
            body {
              font-family: Arial, sans-serif;
              margin: 0;
              padding: 0;
              display: flex;
              justify-content: center;
              align-items: center;
              height: 100vh;
            }
            .content {
              text-align: center;
              width: 100%;
            }
            .qr-container {
              display: flex;
              justify-content: center;
              align-items: center;
              flex-direction: column;
              height: 50%; /* Ocupa la mitad de la página */
            }
            .qr-container img {
             width: 400px; /* Tamaño fijo en píxeles para hacer el QR más grande */
              height: auto; /* Mantiene la proporción del QR */
            }
            .mesa-text {
              font-size: 36px;
              margin-bottom: 5px;
            }
          </style>
        </head>
        <body>
          <div class="content">
            <div class="mesa-text">
              <h3>Mesa ${numeroMesa}</h3>
            </div>
            <div class="qr-container">
              <img src="${qrUrl}" alt="QR" />
            </div>
          </div>
        </body>
      </html>
    `);
      printWindow.document.close();
      printWindow.print();
    },
  },
  created() {
    this.obtenerQRCodes(); // Cargar los códigos QR al inicio
  },

};
</script>

<style scoped>
/* Estilos opcionales */
img {
  max-width: 200px;
  margin-top: 10px;
}
</style>
