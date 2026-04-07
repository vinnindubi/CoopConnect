<script setup lang="ts">
import { ref, provide } from "vue";

// reactive state for open/close
const drawer = ref(false); // true = visible by default
const toggleSidebar = () => {
  drawer.value = !drawer.value;
};
provide("toggleSidebar", toggleSidebar);
</script>

<template> 
  <v-navigation-drawer v-model="drawer" app >
    <v-list>
      <v-list-item color="primary" prepend-icon="mdi-home" to="/home">
        <v-list-item-title>Home</v-list-item-title>
      </v-list-item>
      <v-list-item color="primary" prepend-icon="mdi-calendar-week" to="/events">
        <v-list-item-title>Events</v-list-item-title>
      </v-list-item>
      <v-list-item color="primary" prepend-icon="mdi-newspaper" to="/articles">
        <v-list-item-title>Articles</v-list-item-title>
      </v-list-item>
      <v-list-item color="primary" prepend-icon="mdi-account-group" to="/forums">
        <v-list-item-title>Forums</v-list-item-title>
      </v-list-item>
      <v-list-item color="primary" prepend-icon="mdi-cart" to="/marketplace">
        <v-list-item-title>MarketPlace</v-list-item-title>
      </v-list-item>
      <v-list-item color="primary" prepend-icon="mdi-comment-account-outline" to="/feedback">
        <v-list-item-title>Feedback</v-list-item-title>
      </v-list-item>
      <v-list-item color="primary" prepend-icon="mdi-account" to="/profile">
        <v-list-item-title >Profile</v-list-item-title>
      </v-list-item>

      <v-list-item color="primary" prepend-icon="mdi-account-group-outline" to="/clubsAndSocieties">
        <v-list-item-title>Clubs & Societies</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>
