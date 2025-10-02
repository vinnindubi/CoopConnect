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
      <v-list-item prepend-icon="mdi-calendar-week">
        <v-list-item-title>Events</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-newspaper">
        <v-list-item-title>Articles</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-account-group">
        <v-list-item-title>Forums</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-cart">
        <v-list-item-title>MarketPlace</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-comment-account-outline">
        <v-list-item-title>Feedback</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-account">
        <v-list-item-title>myProfile</v-list-item-title>
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
