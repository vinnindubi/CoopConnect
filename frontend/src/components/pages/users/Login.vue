<script setup lang="ts">
import { ref } from 'vue';
import {useRouter} from 'vue-router'
const email = ref('');
const password = ref('');
const rememberMe = ref(false);
const showPassword = ref(false);
const router= useRouter();
const loading = ref(false);
const rules={
  min: (value: string) => value.length >= 8 || 'Min 8 characters',
  email: (value: string) => /.+@.+\..+/.test(value) || 'E-mail must be valid.',
}
const isFormValid = ref(false);
// Logic to handle the login
const handleLogin = async () => {
  if (!isFormValid.value)     return;
  console.log("Logging in...", { email: email.value, password: password.value });
  loading.value= true;
  setTimeout(() => {
    loading.value = false;
    router.push('/')
  }, 1500);
};
</script>

<template>
  <v-app>
    <v-main class="bg-background-light d-flex align-center justify-center position-relative">
      
      <div class="visual-accent-top"></div>
      <div class="visual-accent-bottom"></div>

      <v-container class="d-flex justify-center" style="z-index: 1;">
        <div class="w-100" style="max-width: 450px;">
          
          <div class="text-center mb-8">
            <div class="d-inline-flex align-center justify-center rounded-circle bg-white elevation-1 mb-4" 
                 style="width: 96px; height: 96px; border: 2px solid rgba(236, 127, 19, 0.1);">
              <v-img 
                src="public\cuk_logo.png" 
                alt="University Logo"
                width="96"
                height="96"
                cover
                class="rounded-circle"
              ></v-img>
            </div>
            <h1 class="text-h4 font-weight-bold text-grey-darken-4 tracking-tight">CoopConnect</h1>
            <p class="text-body-2 text-grey-darken-1 mt-1">The Co-operative University of Kenya</p>
          </div>

          <v-card class="rounded-xl elevation-10 border-opacity-50 " >
            <div class="pa-8">
              <div class="mb-6">
                <h2 class="text-h6 font-weight-bold text-grey-darken-3">Welcome Back</h2>
                <p class="text-caption text-grey-darken-1">Please sign in to access your student portal</p>
              </div>

              <v-form  v-model="isFormValid" @submit.prevent="handleLogin">
                <div class="mb-4">
                  <label class="text-caption font-weight-bold text-grey-darken-2 mb-1 d-block">Student Email or Username</label>
                  <v-text-field
                    v-model="email"
                    :rules="[rules.email]"
                    placeholder="e.g., student@cuk.ac.ke"
                    variant="outlined"
                    prepend-inner-icon="mdi-email-outline"
                    density="comfortable"
                    color="primary"
                    hide-details="auto"
                    bg-color="grey-lighten-5"
                  ></v-text-field>
                </div>

                <div class="mb-2">
                  <label class="text-caption font-weight-bold text-grey-darken-2 mb-1 d-block">Password</label>
                  <v-text-field
                    v-model="password"
                    :rules="[rules.min]"
                    placeholder="••••••••"
                    variant="outlined"
                    prepend-inner-icon="mdi-lock-outline"
                    :type="showPassword ? 'text' : 'password'"
                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append-inner="showPassword = !showPassword"
                    density="comfortable"
                    color="primary"
                    hide-details="auto"
                    bg-color="grey-lighten-5"
                  ></v-text-field>
                </div>

                <div class="d-flex align-center justify-space-between mb-5">
                  <v-checkbox
                    v-model="rememberMe"
                    label="Remember me"
                    color="primary"
                    density="compact"
                    hide-details
                    class="ma-0 pa-0"
                  ></v-checkbox>
                  <a href="#" class="text-caption font-weight-medium text-primary text-decoration-none">
                    Forgot password?
                  </a>
                </div>

                <v-btn
                  type="submit"
                  block
                  color="primary"
                  size="large"
                  class="text-uppercase font-weight-bold letter-spacing-1 rounded-lg"
                  elevation="2"
                  :loading= "loading"
                  height="48"
                  :disabled = "!isFormValid"
                >
                  Sign In
                </v-btn>
              </v-form>

              <div class="mt-8 position-relative text-center">
                <v-divider class="position-absolute w-100" style="top: 50%; z-index: 0;"></v-divider>
                <span class="bg-white px-2 text-caption text-grey text-uppercase position-relative" style="z-index: 1;">
                  Need help?
                </span>
              </div>

              <div class="mt-6 text-center text-body-2 text-grey-darken-1">
                Don't have an account? 
                <a href="/register" class="font-weight-bold text-primary text-decoration-none ml-1">Register here</a>
              </div>
            </div>
          </v-card>

        </div>
      </v-container>
    </v-main>
  </v-app>
</template>