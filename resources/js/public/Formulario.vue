<template>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card p-4 shadow-lg text-center">
            <h2 class="mb-3">Solicitud de Canciones</h2>
            <p v-if="esInvitado" class="text-danger">Estás ingresando como invitado.</p>
            <p v-else class="text-success">Bienvenido, {{ usuario.nombre }}!</p>

            <form @submit.prevent="enviarSolicitud">
                <div class="mb-3">
                    <label class="form-label">¿Cual es tu nombre?</label>
                    <input type="text" class="form-control" v-model="usuario.nombre" placeholder="Tu nombre" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Buscar Canción o Artista</label>
                    <input type="text" class="form-control" v-model="busquedaCancion" @input="buscarCanciones"
                        placeholder="Escribe el nombre de la canción o artista" />
                    <div v-if="buscando" class="spinner-border text-primary mt-2" role="status">
                        <span class="visually-hidden">Buscando...</span>
                    </div>
                    <div v-if="resultadosBusqueda.length > 0" class="mt-2">
                        <ul class="list-group">
                            <li v-for="(cancion, index) in resultadosBusqueda" :key="index"
                                class="list-group-item d-flex justify-content-between align-items-center song-item">
                                <img v-if="cancion.albumCover" :src="cancion.albumCover" alt="Carátula" width="50"
                                    height="50" class="me-2" />
                                <span>{{ cancion.name }} - {{ cancion.artist }}</span>
                                <button type="button" class="btn btn-link" @click="seleccionarCancion(cancion)">
                                    Seleccionar
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div v-else-if="busquedaCancion.length >= 3 && resultadosBusqueda.length === 0"
                        class="mt-2 text-muted">
                        No se encontraron resultados.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nombre de la canción</label>
                    <input type="text" class="form-control" v-model="cancion" disabled />
                </div>

                <div class="mb-3">
                    <label class="form-label">Artista</label>
                    <input type="text" class="form-control" v-model="artista" disabled />
                </div>

                <!-- Nuevo campo para dedicatoria -->
                <div class="mb-3">
                    <label class="form-label">Dedicatoria (opcional)</label>
                    <textarea class="form-control" v-model="dedicatoria"
                        placeholder="Escribe una dedicatoria para la canción..." rows="3"></textarea>
                </div>

                <!-- Número de mesa oculto -->
      <input type="hidden" :value="mesa" />

                <button type="submit" class="btn btn-primary w-100 submit-btn">Enviar Solicitud</button>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            nombre: "",
            cancion: "",
            artista: "",
            busquedaCancion: "",
            resultadosBusqueda: [],
            usuario: { nombre: "" },
            esInvitado: false,
            accessToken: "",
            buscando: false,
            dedicatoria: "",
            mesa: null,

        };
    },
    created() {
        this.esInvitado = localStorage.getItem("esInvitado") === "true";
        if (this.esInvitado) {
            this.nombre = "Invitado";
        } else {
            this.fetchCurrentUser();
        }
        this.obtenerAccessToken();

         // Obtener el número de mesa de la URL
    const urlParams = new URLSearchParams(window.location.search);
    this.mesa = urlParams.get('mesa');
    },
    methods: {
        async obtenerAccessToken() {
            const clientId = '26eae5713cbc43abbd742d30db2d2130';
            const clientSecret = '96a4df45215745ac93490de026be2071';
            const response = await fetch('https://accounts.spotify.com/api/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'Authorization': 'Basic ' + btoa(clientId + ':' + clientSecret),
                },
                body: new URLSearchParams({
                    grant_type: 'client_credentials',
                }),
            });

            const data = await response.json();
            this.accessToken = data.access_token;
        },
        fetchCurrentUser() {
            axios.get('/currentuser')
                .then(response => {
                    const currentUser = response.data;
                    this.usuario.nombre = `${currentUser.nombre} ${currentUser.apellido_paterno} ${currentUser.apellido_materno}`;
                })
                .catch(error => {
                    console.error('Error al obtener el usuario actual:', error);
                });
        },
        async buscarCanciones() {
            if (this.busquedaCancion.length < 3) {
                this.resultadosBusqueda = [];
                return;
            }

            this.buscando = true;
            this.resultadosBusqueda = [];

            try {
                const response = await fetch(
                    `https://api.spotify.com/v1/search?q=${this.busquedaCancion}&type=track,artist&limit=5`,
                    {
                        headers: {
                            Authorization: `Bearer ${this.accessToken}`,
                        },
                    }
                );

                const data = await response.json();

                if (data.tracks && data.tracks.items) {
                    this.resultadosBusqueda = data.tracks.items.map(item => ({
                        name: item.name,
                        artist: item.artists && item.artists.length > 0 ? item.artists[0].name : 'Artista desconocido',
                        albumCover: item.album.images && item.album.images[0] ? item.album.images[0].url : '',
                    }));
                } else if (data.artists && data.artists.items) {
                    this.resultadosBusqueda = data.artists.items.map(item => ({
                        name: item.name,
                        artist: item.name,
                        albumCover: item.images && item.images[0] ? item.images[0].url : '',
                    }));
                }
            } catch (error) {
                console.error("Error al buscar canciones:", error);
            } finally {
                this.buscando = false;
            }
        },
        seleccionarCancion(cancion) {
    this.cancion = cancion.name;
    this.artista = cancion.artist;
    this.albumCover = cancion.albumCover;  // Guardar la carátula seleccionada

    // Verificar qué contiene resultadosBusqueda después de seleccionar
    console.log("Resultados de búsqueda después de seleccionar:", this.resultadosBusqueda);
    console.log("Canción seleccionada:", this.cancion);

    // Limpiar los resultados de búsqueda solo después de seleccionar la canción
    this.resultadosBusqueda = [];
},



enviarSolicitud() {
    const nombreUsuario = this.usuario.nombre || "Invitado";  // Si es invitado, usa "Invitado"

    // Verificar si la carátula está correctamente asignada
    const albumCover = this.albumCover;  // Usar la carátula almacenada
    console.log("Album Cover: ", albumCover);  // Verificar el valor de albumCover

    if (!albumCover) {
        alert("No se encontró la carátula de la canción. Por favor, selecciona una canción válida.");
        return; // Evitar enviar la solicitud si no hay carátula
    }

    const solicitud = {
        nombreUsuario,
        cancion: this.cancion,
        artista: this.artista,
        albumCover,  // Incluir la carátula
        dedicatoria: this.dedicatoria,
        mesa: this.mesa,  // Incluir el número de mesa en la solicitud

    };

    // Enviar la solicitud al backend utilizando axios
    axios.post('/solicitud', solicitud)
        .then(response => {
            // Si la solicitud fue exitosa, muestra un mensaje
            alert(`Solicitud enviada con éxito: \nCanción: ${this.cancion} \nArtista: ${this.artista} \nPor: ${nombreUsuario} \nDedicatoria: ${this.dedicatoria}`);
        })
        .catch(error => {
            // Si hay un error, muestra un mensaje
            console.error("Error al enviar la solicitud:", error);
            alert("Hubo un error al enviar la solicitud.");
        });
},

    },
};
</script>

<style scoped>
.card {
    max-width: 400px;
    border-radius: 15px;
    padding: 30px;
    background-color: #f9f9f9;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

h2 {
    font-family: 'Roboto', sans-serif;
    color: #333;
}

input.form-control, textarea.form-control {
    border-radius: 10px;
    box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.1);
}

button.submit-btn {
    background-color: #007bff;
    border: none;
    border-radius: 10px;
    padding: 10px;
    font-size: 1rem;
}

button.submit-btn:hover {
    background-color: #0056b3;
}

.song-item {
    transition: background-color 0.3s ease;
}

.song-item:hover {
    background-color: #f1f1f1;
}

.spinner-border {
    display: inline-block;
}
</style>
