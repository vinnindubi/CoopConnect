<script setup lang="ts">
import { inject } from "vue";

// get sidebar toggle function from App.vue
const toggleSidebar = inject("toggleSidebar") as () => void;
</script>

<template>
  <v-app-bar :elevation="1">
    <!-- clicking this will toggle sidebar -->
    <v-app-bar-nav-icon @click="toggleSidebar" />

    <v-app-bar-title>Header</v-app-bar-title>
  </v-app-bar>
</template>
