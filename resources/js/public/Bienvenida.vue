<template>
  <div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card p-4 shadow-lg text-center">
      <div class="mb-8">
          <img src="/assets/logokaraoke.png" alt="Your Logo" width="340" height="250">
      </div>
      <h2 class="mb-4">Bienvenid@ al Karaoke</h2>
      <p>Elige cómo quieres ingresar:</p>
      
      <button class="btn btn-secondary btn-lg w-100 mb-2" @click="entrarComoInvitado">
        Entrar como Invitado
      </button>
      
      <button class="btn btn-success btn-lg w-100 mb-2" @click="logIn">
        Iniciar sesión
      </button>

      <span>¿No tienes cuenta? <a href="#" @click="registrarse">Regístrate</a></span>
    </div>
  </div>
</template>

<script>
export default {
methods: {
  // Función para redirigir al formulario como invitado
  entrarComoInvitado() {
    // Obtiene el número de mesa de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const mesa = urlParams.get("mesa");

    // Guarda en el localStorage que es invitado y el número de mesa
    localStorage.setItem("esInvitado", "true");
    localStorage.setItem("mesa", mesa);

    // Redirige al formulario de solicitud de canciones, manteniendo el número de mesa
    window.location.href = `/formulario?mesa=${mesa}`;
  },

  // Función para redirigir al login
  logIn() {
    // Obtiene el número de mesa de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const mesa = urlParams.get("mesa");

    // Guarda en el localStorage que NO es invitado
    localStorage.setItem("esInvitado", "false");
    localStorage.setItem("mesa", mesa);

    // Redirige a la página de inicio de sesión
    window.location.href = "/login";
  },

  registrarse() {
    // Redirige a la página de registro
    window.location.href = "/registro";
  }
}
};
</script>

<style scoped>
.card {
max-width: 400px;
border-radius: 15px;
}
</style>
