// import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
// Vuetify imports
import "vuetify/styles"; // Global CSS
import { createVuetify } from "vuetify";
import * as components from "vuetify/components";
import * as directives from "vuetify/directives";
//Import Material Design Icons
import "@mdi/font/css/materialdesignicons.css";
const vuetify = createVuetify({
  components,
  directives,
});

createApp(App).use(vuetify).mount('#app')
