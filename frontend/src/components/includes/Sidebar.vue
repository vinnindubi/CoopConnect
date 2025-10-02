<script setup lang="ts">
import { ref } from "vue";

// reactive state for open/close
const drawer = ref(true); // true = visible by default
</script>

<template>
  <v-navigation-drawer v-model="drawer" app>
    <v-list>
      <v-list-item prepend-icon="mdi-home">
        <v-list-item-title>Home</v-list-item-title>
      </v-list-item>

      <v-list-item prepend-icon="mdi-chart-line">
        <v-list-item-title>Dashboard</v-list-item-title>
      </v-list-item>

      <v-list-item prepend-icon="mdi-cog">
        <v-list-item-title>Settings</v-list-item-title>
      </v-list-item>

      <v-list-item prepend-icon="mdi-logout">
        <v-list-item-title>Logout</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>
