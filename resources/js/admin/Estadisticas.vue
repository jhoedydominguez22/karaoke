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
            <div class="card-body">
              <h5 class="card-title">Mesa {{ qr.numeroMesa }}</h5>
              <img :src="qr.qrUrl" alt="QR" class="qr-img" />
              <button class="btn btn-dark mt-3" @click="imprimirQR(qr.qrUrl, qr.numeroMesa)">
                Imprimir QR
              </button>
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
          this.obtenerQRCodes(); // Actualizar la lista de códigos QR
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
