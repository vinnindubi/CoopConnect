<script setup lang="ts">
    import { ref } from 'vue';
    const email= ref('')
    const password=ref('')
    let showPassword=ref(false)
  
</script>
<template>
    <v-container class="fill-height fluid d-flex align-center justify-center bg-grey-lighten-4">
        <v-card 
        class="pa-8"
      max-width="400"
      elevation="6"
      rounded="xl">
            <v-card-title>  welcome Back  </v-card-title>
            <v-card-text>
                <v-text-field
                label="Email"
                v-model="email"
                variant="outlined"
                type="email"
                prepend-inner-icon="mdi-email-outline"
                class="mb-3"
                density="comfortable"
                />
                <v-text-field
                label="Password"
                v-model="password"
                variant="outlined"
                :type="showPassword? 'text':'password'"
                :append-inner-icon="showPassword? 'mdi-eye-off': 'mdi-eye'"
                @click:append-inner="showPassword = !showPassword"
                />
            </v-card-text>
            <v-card-action>
                <span>Don't have an account? </span>
                <v-btn variant="outlined" >Register</v-btn>
            </v-card-action>

        </v-card>
</v-container>
</template>