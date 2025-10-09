<script setup lang="ts">
import { ref, provide } from "vue";
import Header from "./components/includes/Header.vue";
import Sidebar from "./components/includes/Sidebar.vue";
import Dashboard from "./components/pages/Dashboard.vue";
import Login from "./components/pages/Login.vue";

const drawer = ref(true); // state shared between header + sidebar

// provide toggle function to children (like Header)
const toggleSidebar = () => {
  drawer.value = !drawer.value;
};
provide("toggleSidebar", toggleSidebar);
</script>

<template>
  <v-app class="bg-green">
    <Header />
    <Sidebar v-model="drawer" />

    <v-main>
      <router-view/>
    </v-main>
  </v-app>
</template>
<style scoped>

</style>