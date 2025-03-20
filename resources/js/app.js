import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import Chart from 'chart.js/auto';
import QRCode from 'qrcode';
 
import { createApp } from 'vue';



const app = createApp({});

import Dashboard from './admin/Dashboard.vue';
app.component('dashboard-vue', Dashboard);

import Capturar from './admin/Capturar.vue';
app.component('capturar-vue', Capturar);

import Createuser from './admin/Createuser.vue';
app.component('createuser-vue', Createuser);

import Listuser from './admin/Listuser.vue';
app.component('listuser-vue', Listuser);

import Consultar from './admin/Consultar.vue';
app.component('consultar-vue', Consultar);

import Desechados from './admin/Desechados.vue';
app.component('desechados-vue', Desechados);

import Estadisticas from './admin/Estadisticas.vue';
app.component('estadisticas-vue', Estadisticas);

import Buscar from './admin/Buscar.vue';
app.component('buscar-vue', Buscar);

import Buscardesechados from './admin/Buscardesechados.vue';
app.component('buscardesechos-vue', Buscardesechados);

import Inventario from './admin/Inventario.vue';
app.component('inventario-vue', Inventario);




import Home from './public/Home.vue';
app.component('home-vue', Home);

import Bienvenida from './public/Bienvenida.vue';
app.component('bienvenida-vue', Bienvenida);

import Formulario from './public/Formulario.vue';
app.component('formulario-vue', Formulario);


app.mount('#app');
app.config.globalProperties.$Chart = Chart;

