<template>
    <div class="container mt-5">

       <!--  <div>
            <h1>Generador de UUID</h1>
            <button @click="generarUUID">Generar UUID</button>
            <p v-if="uuid">UUID Generado: {{ uuid }}</p>
        </div> -->

        <!--Formulario-->
        <h1 class="text-center">Capturar nuevo registro</h1>
        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="">
                    <div class="form-group">
                        <label for="inventario">No. Inventario</label>
                        <input type="text" id="inventario" v-model="form.inventario" class="form-control" nullable>
                    </div>


                    <div class="form-group">
                        <label for="fotoUnica">Foto Única</label>
                        <select id="fotoUnica" v-model="form.fotoUnica" class="form-control" nullable>
                            <option value="Si">Si</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="medidas">Medidas</label>
                        <input type="text" id="medidas" v-model="form.medidas" class="form-control" nullable>
                    </div>

                    <div class="form-group">
                        <label for="cantidad">Cantidad</label>
                        <input type="number" id="cantidad" v-model="form.cantidad" class="form-control" nullable>
                    </div>

                    <div class="form-group">
                        <label for="color">Color</label>
                        <select id="color" v-model="form.color" class="form-control" nullable>
                            <option value="BN">Blanco y Negro</option>
                            <option value="C">Color</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="descripcionImagen">Descripción de la Imagen</label>
                        <textarea id="descripcionImagen" v-model="form.descripcionImagen" class="form-control" rows="3"
                            nullable></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="formato">Formato</label>
                    <select id="formato" v-model="form.formato" class="form-control" nullable>
                        <option value="Gran formato">Gran formato</option>
                        <option value="Estandar">Estandar</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="clasificacionContenido">Tema / Clasificación del Contenido</label>
                    <input type="text" id="clasificacionContenido" v-model="form.clasificacionContenido"
                        class="form-control" nullable>
                </div>

                <div class="form-group">
                    <label for="fondo">Fondo</label>
                    <input type="text" id="fondo" v-model="form.fondo" class="form-control" nullable>
                </div>

                <div class="form-group">
                    <label for="serie">Serie</label>
                    <input type="text" id="serie" v-model="form.serie" class="form-control" nullable>
                </div>

                <div class="form-group">
                    <label for="subSerie">Sub Serie</label>
                    <input type="text" id="subSerie" v-model="form.subSerie" class="form-control" nullable>
                </div>

                <div class="form-group">
                    <label for="seccion">Sección</label>
                    <input type="text" id="seccion" v-model="form.seccion" class="form-control" nullable>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" v-model="form.descripcion" class="form-control" rows="3"
                        nullable></textarea>
                </div>


                <div class="form-group">
                    <label>Daño</label>
                    <div class="d-flex flex-wrap">
                        <div v-for="(dano, index) in danos" :key="index"
                            class="custom-control custom-checkbox mr-3 mb-2">
                            <input type="checkbox" class="custom-control-input" :id="'dano_' + index"
                                v-model="form.dano" :value="dano">
                            <label class="custom-control-label" :for="'dano_' + index">{{ dano }}</label>
                        </div>
                    </div>
                    <br>
                    <div class="form-group">
                        <label for="descripcion">Recurso digital</label><br>
                        <input type="file" id="archivos" ref="archivos" multiple @change="handleFileUpload">
                    </div>

                    <div class="form-group">
                        <label for="capturador">Capturador</label>
                        <input type="text" id="capturador" v-model="form.capturador" class="form-control" disabled>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';
import { v4 as uuidv4 } from 'uuid';

export default {
    props: {

    },
    data() {
        return {
            uuid: '',
            form: {
                inventario: '',
                formato: '',
                fotoUnica: '',
                medidas: '',
                cantidad: '',
                color: '',
                descripcionImagen: '',
                dano: [], // Array para almacenar los daños seleccionados
                clasificacionContenido: '',
                fondo: '',
                serie: '',
                subSerie: '',
                seccion: '',
                descripcion: '',
                capturador: '' // Nombre de usuario se asignará automáticamente

            },
            danos: ['Rotura', 'Suciedad', 'Cintas Adhesivas', 'Hongo', 'Comején']
        };
    },

    methods: {
        generarUUID() {
            this.uuid = uuidv4();
        },
        submitForm() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres guardar este documento?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {

                    let formData = new FormData();
                    // Agregar campos de texto
                    formData.append('inventario', this.form.inventario);
                    formData.append('formato', this.form.formato);
                    formData.append('fotoUnica', this.form.fotoUnica);
                    formData.append('medidas', this.form.medidas);
                    formData.append('cantidad', this.form.cantidad);
                    formData.append('color', this.form.color);
                    formData.append('descripcionImagen', this.form.descripcionImagen);
                    formData.append('clasificacionContenido', this.form.clasificacionContenido);
                    formData.append('fondo', this.form.fondo);
                    formData.append('serie', this.form.serie);
                    formData.append('subSerie', this.form.subSerie);
                    formData.append('seccion', this.form.seccion);
                    formData.append('descripcion', this.form.descripcion);
                    formData.append('capturador', this.form.capturador);
                    // Agregar campos de array (checkboxes)
                    this.form.dano.forEach((dano, index) => {
                        formData.append('dano[]', dano);
                    });
                    // Agregar archivos
                    const files = this.$refs.archivos.files;
                    for (let i = 0; i < files.length; i++) {
                        formData.append('archivos[]', files[i]);
                    }

                    // Enviar datos al servidor usando Axios
                    axios.post('/documentos', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(response => {
                            // Manejar la respuesta del servidor
                            // Mostrar mensaje de éxito
                            Swal.fire({
                                title: '¡Documento creado!',
                                text: 'El documento se ha guardado con éxito.',
                                icon: 'success'
                            });
                            // Resetear formulario
                            this.resetForm();
                        })
                        .catch(error => {
                            // Manejar errores
                            console.error('Error al guardar el documento:', error);
                            Swal.fire({
                                title: 'Error',
                                text: 'Hubo un error al guardar el documento.',
                                icon: 'error'
                            });
                        });
                }
            });
        },
        handleFileUpload(event) {
            // Manejar la subida de archivos
            const files = event.target.files;
            // Limite de 10 archivos
            if (files.length > 10) {
                alert('Solo se pueden cargar hasta 10 archivos.');
                return;
            }
           

        },

        resetForm() {
            // Aquí puedes reiniciar los campos del formulario después de enviarlo
            this.form.inventario = '';
            this.form.formato = '';
            this.form.fotoUnica = '';
            this.form.medidas = '';
            this.form.cantidad = '';
            this.form.color = '';
            this.form.descripcionImagen = '';
            this.form.dano = [];
            this.form.clasificacionContenido = '';
            this.form.fondo = '';
            this.form.serie = '';
            this.form.subSerie = '';
            this.form.seccion = '';
            this.form.descripcion = '';
            capturador: this.capturador;
            // Puedes añadir más campos según sea necesario
            // También puedes limpiar los campos del archivo si es necesario
            this.$refs.archivos.value = null;
        },
        fetchCurrentUser() {
            axios.get('/currentuser')
                .then(response => {
                    // Aquí asumes que response.data contiene los datos del usuario
                    const currentUser = response.data;
                    // Construye el nombre completo del usuario
                    this.form.capturador = `${currentUser.nombre} ${currentUser.apellido_paterno} ${currentUser.apellido_materno}`;
                })
                .catch(error => {
                    console.error('Error al obtener el usuario actual:', error);
                });
        }
    },
    mounted() {
        // Llama al método para obtener el usuario actual al montar el componente
        this.fetchCurrentUser();
    }
};
</script>

<style scoped>
.container {
    max-width: 1000px;
    margin: 0 auto;
}

.row {
    display: flex;
    flex-wrap: wrap;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.btn {
    margin-top: 20px;
}

.custom-control-label {
    margin-right: 10px;
}
</style>