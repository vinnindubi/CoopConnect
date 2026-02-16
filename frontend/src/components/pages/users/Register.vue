<script setup lang="ts">
import { ref, reactive } from 'vue';
import {useRouter} from 'vue-router'

// -- Form State --
const formData= reactive({
  fullname:'',
  admNumber:'',
  email:'',
  password:'',
  confirmPassword:''
});
const agreeToTerms = ref(false);
const showPassword = ref(false);
const router= useRouter();

// -- Validation Rules --
// These help Vuetify show red error text if the user types something wrong
const rules = {
  required: (value: string) => !!value || 'Required.',
  email: (value: string) => /.+@.+\..+/.test(value) || 'E-mail must be valid.',
  min: (value: string) => value.length >= 8 || 'Min 8 characters',
  match: (value: string) => value === formData.password || 'Passwords must match',
};

// -- Submit Logic --
const loading = ref(false);
const isFormValid = ref(false)

const handleRegister = async () => {
  if (!isFormValid.value ||!agreeToTerms.value)   return;
  
  loading.value = true;
  // Simulate an API call
  setTimeout(() => {
    console.log("Registering:", { 
      name: formData.fullname, 
      adm: formData.admNumber, 
      email: formData.email 
    });
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
        <div class="w-100" style="max-width: 500px;">
          
          <div class="text-center mb-6">
             <v-avatar size="80" class="elevation-1 mb-3 bg-white" style="border: 2px solid rgba(236, 127, 19, 0.1);">
              <v-img 
                src="/cuk_logo.png" 
                alt="University Logo" 
                cover
              ></v-img>
            </v-avatar>
            <h1 class="text-h4 font-weight-bold text-grey-darken-4">Create Account</h1>
            <p class="text-body-2 text-grey-darken-1">Join the CoopConnect Student Community</p>
          </div>

          <v-card class="rounded-xl elevation-10 border-opacity-50" border>
            <div class="pa-8">
              
              <v-form v-model="isFormValid" @submit.prevent="handleRegister">
                
                <div class="mb-3">
                  <label class="text-caption font-weight-bold text-grey-darken-2 mb-1 d-block">Full Name</label>
                  <v-text-field
                    v-model="formData.fullname"
                    :rules="[rules.required]"
                    placeholder="e.g., John Doe"
                    variant="outlined"
                    prepend-inner-icon="mdi-account-outline"
                    density="comfortable"
                    color="primary"
                    bg-color="grey-lighten-5"
                  ></v-text-field>
                </div>

                <v-row dense>
                  <v-col cols="12" sm="6">
                    <div class="mb-3">
                      <label class="text-caption font-weight-bold text-grey-darken-2 mb-1 d-block">Admission No.</label>
                      <v-text-field
                        v-model="formData.admNumber"
                        :rules="[rules.required]"
                        placeholder="e.g., BITC01/..."
                        variant="outlined"
                        prepend-inner-icon="mdi-card-account-details-outline"
                        density="comfortable"
                        color="primary"
                        bg-color="grey-lighten-5"
                      ></v-text-field>
                    </div>
                  </v-col>
                  <v-col cols="12" sm="6">
                     <div class="mb-3">
                      <label class="text-caption font-weight-bold text-grey-darken-2 mb-1 d-block">Student Email</label>
                      <v-text-field
                        v-model="formData.email"
                        :rules="[rules.required, rules.email]"
                        placeholder="student@cuk.ac.ke"
                        variant="outlined"
                        prepend-inner-icon="mdi-email-outline"
                        density="comfortable"
                        color="primary"
                        bg-color="grey-lighten-5"
                      ></v-text-field>
                    </div>
                  </v-col>
                </v-row>

                <div class="mb-3">
                  <label class="text-caption font-weight-bold text-grey-darken-2 mb-1 d-block">Password</label>
                  <v-text-field
                    v-model="formData.password"
                    :rules="[rules.required, rules.min]"
                    placeholder="Min 8 characters"
                    variant="outlined"
                    prepend-inner-icon="mdi-lock-outline"
                    :type="showPassword ? 'text' : 'password'"
                    :append-inner-icon="showPassword ? 'mdi-eye-off' : 'mdi-eye'"
                    @click:append-inner="showPassword = !showPassword"
                    density="comfortable"
                    color="primary"
                    bg-color="grey-lighten-5"
                  ></v-text-field>
                </div>

                <div class="mb-1">
                  <label class="text-caption font-weight-bold text-grey-darken-2 mb-1 d-block">Confirm Password</label>
                  <v-text-field
                    v-model="formData.confirmPassword"
                    :rules="[rules.required, rules.match]"
                    placeholder="Re-enter password"
                    variant="outlined"
                    prepend-inner-icon="mdi-lock-check-outline"
                    type="password"
                    density="comfortable"
                    color="primary"
                    bg-color="grey-lighten-5"
                  ></v-text-field>
                </div>

                <v-checkbox
                  v-model="agreeToTerms"
                  color="primary"
                  hide-details
                  class="mt-0 mb-4"
                >
                  <template v-slot:label>
                    <span class="text-caption text-grey-darken-1">
                      I agree to the <a href="#" class="text-primary font-weight-bold text-decoration-none">Terms</a> and <a href="#" class="text-primary font-weight-bold text-decoration-none">Privacy Policy</a>
                    </span>
                  </template>
                </v-checkbox>

                <v-btn
                  type="submit"
                  block
                  color="primary"
                  size="large"
                  class="text-uppercase font-weight-bold rounded-lg"
                  elevation="2"
                  height="48"
                  :loading="loading"
                  :disabled="!isFormValid || !agreeToTerms"
                >
                  Create Account
                </v-btn>

              </v-form>

              <div class="mt-6 text-center text-body-2 text-grey-darken-1">
                Already have an account? 
                <a href="/login" class="font-weight-bold text-primary text-decoration-none ml-1">Sign in instead</a>
              </div>

            </div>
          </v-card>
        </div>
      </v-container>
    </v-main>
  </v-app>
</template>