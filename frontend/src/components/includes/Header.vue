<script setup lang="ts">
import { ref ,inject } from "vue";
import { useTheme } from "vuetify";
// get sidebar toggle function from App.vue
const toggleSidebar = inject("toggleSidebar") as () => void;
const darkTheme= ref(false)
const theme = useTheme()
const toggleTheme= ()=>{
  darkTheme.value =!darkTheme.value;
  theme.toggle();
}

</script>

<template>
  <v-app-bar :elevation="1">
    <!-- clicking this will toggle sidebar -->
    <v-app-bar-nav-icon @click="toggleSidebar" />

    <v-app-bar-title class="cursor-pointer" @click="$router.push('/')">CoopConnect</v-app-bar-title>
    <v-spacer/>
    <v-text-field
      prepend-inner-icon="mdi-magnify"
      label="Search"
      variant="outlined"
      density="compact"
      hide-details
      class="mx-4"
      style="max-width: 250px;"
    />
    <v-btn icon variant="text">
      <v-icon>mdi-bell-outline</v-icon>
      <v-badge color="red" content="3" floating></v-badge>
    </v-btn>
    <v-btn icon variant="text">
      <v-icon>mdi-message-text-outline</v-icon>
    </v-btn>
    <v-btn icon variant="text">
      <v-icon>mdi-cog-outline</v-icon>
    </v-btn>

    <v-btn icon variant="text" @click="toggleTheme">
      <v-icon>{{ darkTheme ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
    </v-btn>
    <v-menu>
      <template #activator="{props}">
        <v-avatar v-bind="props">
          <v-img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User"/>
        </v-avatar>
      </template>
      <v-list>
        <v-list-item prepend-icon="mdi-account">Profile</v-list-item>
        <v-list-item prepend-icon="mdi-cog">Settings</v-list-item>
        <v-list-item prepend-icon="mdi-logout">LogOut</v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>
