<script setup lang="ts">
import { ref, provide } from "vue";

// reactive state for open/close
const drawer = ref(false); // true = visible by default
const toggleSidebar = () => {
  drawer.value = !drawer.value;
};
provide("toggleSidebar", toggleSidebar);
import { useRouter, useRoute } from 'vue-router';

const router = useRouter();
const route = useRoute();

// We define our navigation links in an array so it's easy to add more later!
const menuItems = ref([
  { title: 'Dashboard', icon: 'mdi-view-dashboard', path: '/admin/dashboard' },
  { title: 'User Management', icon: 'mdi-account-group', path: '/admin/users' },
  { title: 'Verifications', icon: 'mdi-shield-check', path: '/admin/verifications' },
  { title: 'Moderation', icon: 'mdi-flag-triangle', path: '/admin/moderation' },
  { title: 'Store Categories', icon: 'mdi-tag-multiple', path: '/admin/categories' },
  { title: 'Settings', icon: 'mdi-cog', path: '/admin/settings' },
]);

// Helper to determine if a menu item is the currently active page
const isActive = (path: string) => {
  return route.path === path;
};

// Function to leave the admin panel
const returnToApp = () => {
  router.push('/'); // Sends them back to the main student feed
};
</script>

<template>
  <v-navigation-drawer 
    app
    class="bg-surface border-r border-opacity-50" 
    elevation="1"
    width="260"
  >
    
    <div class="pa-4 d-flex align-center border-b border-opacity-50">
      <v-avatar color="primary" rounded size="40" class="mr-3 elevation-2">
        <v-icon icon="mdi-storefront" color="white"></v-icon>
      </v-avatar>
      <div>
        <div class="text-subtitle-1 font-weight-black text-primary leading-none">CoopConnect</div>
        <div class="text-caption text-medium-emphasis font-weight-bold text-uppercase mt-1">Admin Panel</div>
      </div>
    </div>

    <v-list class="px-2 mt-2">
      <v-list-item
        v-for="item in menuItems"
        :key="item.title"
        :to="item.path"
        :active="isActive(item.path)"
        :prepend-icon="item.icon"
        class="rounded-lg mb-1"
        active-color="primary"
        color="primary"
      >
        <v-list-item-title class="font-weight-medium">{{ item.title }}</v-list-item-title>
      </v-list-item>
    </v-list>

    <template v-slot:append>
      <div class="pa-4">
        <v-divider class="mb-4"></v-divider>
        <v-btn 
          block 
          color="surface-variant" 
          variant="tonal" 
          prepend-icon="mdi-logout-variant"
          class="text-none font-weight-bold"
          @click="returnToApp"
        >
          Exit Admin
        </v-btn>
      </div>
    </template>

  </v-navigation-drawer>
</template>