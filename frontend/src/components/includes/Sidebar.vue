<script setup lang="ts">
import { ref } from "vue";

// reactive state for open/close
const drawer = ref(true); // true = visible by default
</script>

<template> 
  <v-navigation-drawer v-model="drawer" app >
    <v-list>
      <v-list-item prepend-icon="mdi-home" to="/">
        <v-list-item-title>Home</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-calendar-week" to="/events">
        <v-list-item-title>Events</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-newspaper" to="/articles">
        <v-list-item-title>Articles</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-account-group" to="/forums">
        <v-list-item-title>Forums</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-cart" to="/marketplace">
        <v-list-item-title>MarketPlace</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-comment-account-outline" to="/feedback">
        <v-list-item-title>Feedback</v-list-item-title>
      </v-list-item>
      <v-list-item prepend-icon="mdi-account" to="/profile">

        <v-list-item-title>myProfile</v-list-item-title>

      </v-list-item>

      <v-list-item prepend-icon="mdi-cog" to="/settings">
        <v-list-item-title>Settings</v-list-item-title>
      </v-list-item>

      <v-list-item prepend-icon="mdi-logout" to="/login">    
        <v-list-item-title>Login</v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
</template>
