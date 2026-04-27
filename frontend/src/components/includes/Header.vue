<script setup lang="ts">
import { ref ,inject, onMounted } from "vue";
import { useTheme } from "vuetify";
import { logoutUser,getUserProfile } from "@/services/authService";
// get sidebar toggle function from App.vue
const toggleSidebar = inject("toggleSidebar") as () => void;
const darkTheme= ref(true)
const theme = useTheme()
const toggleTheme= ()=>{
  darkTheme.value =!darkTheme.value;
  theme.toggle();
}
const profile = ref({
                    'avatar':''
                  });
const getUser = async () => {
  try{ 
       const response = await getUserProfile();
       const userData = response.data.data;
        profile.value.avatar= userData.avatar;

  }catch(error){
    console.log(error);

  }
}
const handleLogOut= async ()=>{
  try{
    const response = await logoutUser();
    

  }catch(error){

  }finally{

  }
}
onMounted(() => {
  getUser();
  });

</script>

<template>
  <v-app-bar :elevation="1">
    <!-- clicking this will toggle sidebar -->
    <v-app-bar-nav-icon @click="toggleSidebar" />

    <v-app-bar-title class="cursor-pointer" @click="$router.push('/')">CoopConnect</v-app-bar-title>
    <v-spacer/>
    
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
        <v-avatar v-bind="props" color="primary-lighten-4">
          <v-img v-if="profile.avatar" :src="profile.avatar" alt="User"/>
          <v-icon v-else size="large" color="primary">mdi-account</v-icon>
        </v-avatar>
      </template>
      <v-list>
        <v-list-item prepend-icon="mdi-account" to="/profile">Profile</v-list-item>
        <v-list-item prepend-icon="mdi-cog">Settings</v-list-item>
        <v-list-item @click="handleLogOut" prepend-icon="mdi-logout">LogOut</v-list-item>
      </v-list>
    </v-menu>
  </v-app-bar>
</template>
