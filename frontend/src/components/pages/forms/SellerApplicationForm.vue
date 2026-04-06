<script setup lang="ts">
import { ref } from 'vue';

// --- State ---
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');
const isFormValid = ref(false);

// --- Form Data ---
const applicationForm = ref({
  fullName: '',
  admissionNumber: '',
  phoneNumber: '',
  categories: [] as string[],
  businessDescription: '',
  idProof: null as File[] | null,
  agreedToTerms: false,
});

const categoryOptions = ['Clothing & Thrift', 'Electronics & Tech', 'Academic Material (Books/Notes)', 'Services (Tutoring/Fixing)', 'Dorm Gear', 'Food & Snacks', 'Other'];

// --- Validation Rules ---
const rules = {
  required: (v: any) => !!v || 'This field is required',
  admission: (v: string) => /^[A-Z0-9/]+$/.test(v) || 'Enter a valid admission number format',
  phone: (v: string) => /^(?:254|\+254|0)?(7[0-9]{8}|1[0-9]{8})$/.test(v) || 'Enter a valid phone number',
  idUpload: (v: any) => (v && v.length > 0) || 'You must upload your Student ID',
};

// --- Methods ---
const triggerSnackbar = (message: string) => {
  snackbarText.value = message;
  showSnackbar.value = true;
};

const submitApplication = async () => {
  if (!isFormValid.value || !applicationForm.value.agreedToTerms) {
    triggerSnackbar('Please fill all required fields and accept the terms.');
    return;
  }

  isSubmitting.value = true;
  
  try {
    // TODO: Send to Laravel Backend API
    // e.g., await submitSellerApplication(applicationForm.value);
    
    setTimeout(() => {
      triggerSnackbar('Application submitted! Admins will review it shortly.');
      isSubmitting.value = false;
      // Optional: Clear form or redirect
    }, 1500);

  } catch (error) {
    console.error("Submission failed:", error);
    triggerSnackbar('Failed to submit application. Please try again.');
    isSubmitting.value = false;
  }
};
</script>

<template>
  <v-container fluid class="pa-0 pa-md-6 bg-grey-lighten-4 min-vh-100 d-flex justify-center align-center">
    <v-card class="rounded-xl border-opacity-25 w-100 my-md-4" style="max-width: 800px;" border elevation="3" bg-color="white">
      
      <div class="pa-6 pa-md-8 border-b border-opacity-25 bg-primary text-white text-center">
        <v-avatar color="indigo-lighten-4" size="64" class="mb-4">
          <v-icon icon="mdi-store-check-outline" size="32" color="primary"></v-icon>
        </v-avatar>
        <h1 class="text-h4 font-weight-black mb-2">Become a Verified Seller</h1>
        <p class="text-body-1 text-indigo-lighten-4 mb-0" style="max-width: 500px; margin: 0 auto;">
          Join the CoopConnect marketplace. We verify all sellers to keep our campus community safe from scams.
        </p>
      </div>

      <v-form v-model="isFormValid" @submit.prevent="submitApplication" class="pa-6 pa-md-8">
        
        <h3 class="text-h6 font-weight-black text-primary mb-4 d-flex align-center">
          <v-avatar color="indigo-lighten-5" size="32" class="mr-3 text-primary font-weight-bold">1</v-avatar>
          Identity Verification
        </h3>
        
        <v-row class="mb-4">
          <v-col cols="12" md="6" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Full Legal Name <span class="text-error">*</span></label>
            <v-text-field 
              v-model="applicationForm.fullName" 
              :rules="[rules.required]"
              placeholder="As it appears on your Student ID"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-text-field>
          </v-col>
          
          <v-col cols="12" md="6" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Admission Number <span class="text-error">*</span></label>
            <v-text-field 
              v-model="applicationForm.admissionNumber" 
              :rules="[rules.required, rules.admission]"
              placeholder="e.g., BITC01/1234/2023"
              variant="outlined" 
              class="rounded-lg mb-2"
              required
            ></v-text-field>
          </v-col>

          <v-col cols="12" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Upload Student ID (Front) <span class="text-error">*</span></label>
            <v-file-input
              v-model="applicationForm.idProof"
              :rules="[rules.idUpload]"
              accept="image/png, image/jpeg, application/pdf"
              label="Tap to upload a clear photo of your ID"
              variant="outlined"
              prepend-inner-icon="mdi-card-account-details-outline"
              prepend-icon=""
              class="rounded-lg"
              hint="Admins will strictly use this to verify your student status. It will never be shown publicly."
              persistent-hint
            ></v-file-input>
          </v-col>
        </v-row>

        <v-divider class="my-8 border-opacity-25"></v-divider>

        <h3 class="text-h6 font-weight-black text-primary mb-4 d-flex align-center">
          <v-avatar color="primary" variant="tonal" size="32" class="mr-3 font-weight-bold">2</v-avatar>
          Store Information
        </h3>

        <v-row>
          <v-col cols="12" md="6" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Active Phone / Payment Number <span class="text-error">*</span></label>
            <v-text-field 
              v-model="applicationForm.phoneNumber" 
              :rules="[rules.required, rules.phone]"
              placeholder="07XX XXX XXX"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="6" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">What will you sell? <span class="text-error">*</span></label>
            <v-select 
              v-model="applicationForm.categories" 
              :items="categoryOptions" 
              :rules="[rules.required]"
              multiple
              chips
              closable-chips
              placeholder="Select all that apply"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-select>
          </v-col>

          <v-col cols="12" class="pt-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Tell us about your business <span class="text-error">*</span></label>
            <v-textarea 
              v-model="applicationForm.businessDescription" 
              :rules="[rules.required]"
              placeholder="I am selling old engineering textbooks and offering math tutoring..."
              variant="outlined" 
              rows="3" 
              auto-grow 
              class="rounded-lg"
            ></v-textarea>
          </v-col>
        </v-row>

        <v-card variant="tonal" color="amber-darken-4" class="pa-4 rounded-lg mt-4 mb-8">
          <div class="d-flex align-start">
            <v-icon icon="mdi-shield-alert-outline" class="mr-3 mt-1"></v-icon>
            <div>
              <div class="font-weight-bold mb-1">Marketplace Safety Policy</div>
              <p class="text-caption mb-0">
                By applying, you agree to the CoopConnect anti-fraud guidelines. You commit to accurate product descriptions, fair pricing, and agreeing to meet buyers only in designated safe campus zones. Any reports of fraudulent activity will result in an immediate permanent ban and campus disciplinary action.
              </p>
            </div>
          </div>
        </v-card>

        <v-checkbox 
          v-model="applicationForm.agreedToTerms" 
          color="primary"
          class="font-weight-bold text-grey-darken-3"
          hide-details
        >
          <template v-slot:label>
            I confirm that my identity details are correct and I agree to the safety policy.
          </template>
        </v-checkbox>

        <div class="d-flex justify-end mt-8 pt-6 border-t border-opacity-25">
          <v-btn 
            type="submit" 
            color="primary" 
            variant="flat" 
            class="text-none font-weight-bold px-8 rounded-lg w-100 w-sm-auto"
            size="large"
            :loading="isSubmitting"
            :disabled="!applicationForm.agreedToTerms"
          >
            Submit Application
          </v-btn>
        </div>

      </v-form>
    </v-card>

    <v-snackbar v-model="showSnackbar" :timeout="4000" color="dark" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        {{ snackbarText }}
      </div>
    </v-snackbar>
  </v-container>
</template>