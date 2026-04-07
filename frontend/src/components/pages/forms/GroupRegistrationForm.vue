<script setup lang="ts">
import { ref } from 'vue';

// --- State ---
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');
const isFormValid = ref(false);

// --- Form Data ---
const regForm = ref({
  groupType: 'Club', // Default selection
  name: '',
  category: null,
  description: '',
  meetingTime: '',
  leaderName: '',
  leaderAdmission: '',
  leaderPhone: '',
  patronName: '',
  coverImage: null as File[] | null,
  officialDocument: null as File[] | null,
  agreedToTerms: false,
});

// --- Options ---
const groupTypes = ['Club', 'Society'];
const clubCategories = ['Tech', 'Media', 'Environment', 'Leadership', 'Arts', 'Sports'];
const societyCategories = ['Christian', 'Catholic', 'Muslim', 'SDA', 'Other Faiths'];

// --- Rules ---
const rules = {
  required: (v: any) => !!v || 'This field is required',
  admission: (v: string) => /^[A-Z0-9/]+$/.test(v) || 'Enter a valid admission number',
  phone: (v: string) => /^(?:254|\+254|0)?(7[0-9]{8}|1[0-9]{8})$/.test(v) || 'Enter a valid phone number',
  fileRequired: (v: any) => {
    if (!v) return 'An official document is required'; // Catches null or undefined
    if (Array.isArray(v) && v.length === 0) return 'An official document is required'; // Catches empty arrays
    return true; // If it passes both, it's a valid file!
  }
};

// --- Methods ---
const submitApplication = () => {
  if (!isFormValid.value || !regForm.value.agreedToTerms) return;

  isSubmitting.value = true;
  
  setTimeout(() => {
    snackbarText.value = 'Application submitted! The Admin will review it shortly.';
    showSnackbar.value = true;
    isSubmitting.value = false;
    // router.push('/clubs')
  }, 1500);
};
</script>

<template>
  <v-container fluid class="bg-background min-vh-100 py-6 py-md-10 d-flex justify-center">
    <v-card class="rounded-xl border-opacity-25 w-100 bg-surface my-md-4" style="max-width: 800px;" border elevation="2">
      
      <div class="pa-6 pa-md-8 border-b border-opacity-25 text-center" style="background: linear-gradient(135deg, rgb(var(--v-theme-primary)), rgb(var(--v-theme-secondary)));">
        <v-avatar color="surface" size="64" class="mb-4 elevation-2">
          <v-icon icon="mdi-file-certificate-outline" size="32" color="primary"></v-icon>
        </v-avatar>
        <h1 class="text-h4 font-weight-black text-white mb-2">Register a Group</h1>
        <p class="text-body-1 text-white opacity-90 mb-0" style="max-width: 500px; margin: 0 auto;">
          Submit your proposal to the Admin to get your club or society officially listed on coopConnect.
        </p>
      </div>

      <v-form v-model="isFormValid" @submit.prevent="submitApplication" class="pa-6 pa-md-8">
        
        <h3 class="text-h6 font-weight-black text-primary mb-4 d-flex align-center">
          <v-avatar color="primary" variant="tonal" size="32" class="mr-3 font-weight-bold">1</v-avatar>
          Group Details
        </h3>
        
        <v-row class="mb-4">
          <v-col cols="12" sm="6" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Group Type <span class="text-error">*</span></label>
            <v-select 
              v-model="regForm.groupType" 
              :items="groupTypes" 
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-select>
          </v-col>
          
          <v-col cols="12" sm="6" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Category <span class="text-error">*</span></label>
            <v-select 
              v-model="regForm.category" 
              :items="regForm.groupType === 'Club' ? clubCategories : societyCategories" 
              :rules="[rules.required]"
              placeholder="Select category"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-select>
          </v-col>

          <v-col cols="12" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Official Name <span class="text-error">*</span></label>
            <v-text-field 
              v-model="regForm.name" 
              :rules="[rules.required]"
              placeholder="e.g., Tech Innovators Club"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-text-field>
          </v-col>

          <v-col cols="12" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Mission / Description <span class="text-error">*</span></label>
            <v-textarea 
              v-model="regForm.description" 
              :rules="[rules.required]"
              placeholder="What is the main goal of your group?"
              variant="outlined" 
              rows="3" 
              auto-grow 
              class="rounded-lg"
            ></v-textarea>
          </v-col>
        </v-row>

        <v-divider class="my-6 border-opacity-25"></v-divider>

        <h3 class="text-h6 font-weight-black text-primary mb-4 d-flex align-center">
          <v-avatar color="primary" variant="tonal" size="32" class="mr-3 font-weight-bold">2</v-avatar>
          Leadership & Contacts
        </h3>

        <v-row class="mb-4">
          <v-col cols="12" sm="6" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Chairperson Name <span class="text-error">*</span></label>
            <v-text-field 
              v-model="regForm.leaderName" 
              :rules="[rules.required]"
              placeholder="Full Legal Name"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="6" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Admission Number <span class="text-error">*</span></label>
            <v-text-field 
              v-model="regForm.leaderAdmission" 
              :rules="[rules.required, rules.admission]"
              placeholder="e.g., BITC/123/2023"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="6" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Chairperson Phone <span class="text-error">*</span></label>
            <v-text-field 
              v-model="regForm.leaderPhone" 
              :rules="[rules.required, rules.phone]"
              placeholder="07XX XXX XXX"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="6" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Staff Patron / Advisor <span class="text-error">*</span></label>
            <v-text-field 
              v-model="regForm.patronName" 
              :rules="[rules.required]"
              placeholder="Name of sponsoring lecturer"
              variant="outlined" 
              class="rounded-lg mb-2"
            ></v-text-field>
          </v-col>
        </v-row>

        <v-divider class="my-6 border-opacity-25"></v-divider>

        <h3 class="text-h6 font-weight-black text-primary mb-4 d-flex align-center">
          <v-avatar color="primary" variant="tonal" size="32" class="mr-3 font-weight-bold">3</v-avatar>
          Logistics & Documents
        </h3>

        <v-row>
          <v-col cols="12" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Proposed Meeting Time & Venue <span class="text-error">*</span></label>
            <v-text-field 
              v-model="regForm.meetingTime" 
              :rules="[rules.required]"
              placeholder="e.g., Fridays 4 PM @ Block C"
              variant="outlined" 
              prepend-inner-icon="mdi-calendar-clock"
              class="rounded-lg mb-2"
            ></v-text-field>
          </v-col>

          <v-col cols="12" sm="6" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Upload Cover Image</label>
            <v-file-input
              v-model="regForm.coverImage"
              accept="image/*"
              label="For the app listing"
              variant="outlined"
              prepend-inner-icon="mdi-image"
              prepend-icon=""
              class="rounded-lg"
            ></v-file-input>
          </v-col>

          <v-col cols="12" sm="6" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-high-emphasis">Constitution / Proposal PDF <span class="text-error">*</span></label>
            <v-file-input
              v-model="regForm.officialDocument"
              :rules="[rules.fileRequired]"
              accept="application/pdf"
              label="Attach official documents"
              variant="outlined"
              prepend-inner-icon="mdi-file-pdf-box"
              prepend-icon=""
              class="rounded-lg"
            ></v-file-input>
          </v-col>
        </v-row>

        <v-card color="amber-darken-4" variant="tonal" class="pa-4 rounded-lg mt-4 mb-8">
          <div class="d-flex align-start">
            <v-icon icon="mdi-gavel" color="secondary" class="mr-3 mt-1"></v-icon>
            <div>
              <div class="font-weight-bold text-high-emphasis mb-1">University Compliance Policy</div>
              <p class="text-caption text-medium-emphasis mb-0">
                By submitting this form, I confirm that this group will abide by all university regulations. We will not engage in activities that promote hate speech, illegal acts, or academic disruption. I understand that falsifying information will lead to immediate rejection and disciplinary action.
              </p>
            </div>
          </div>
        </v-card>

        <v-checkbox 
          v-model="regForm.agreedToTerms" 
          color="primary"
          class="font-weight-bold text-high-emphasis"
          hide-details
        >
          <template v-slot:label>
            I accept the University Compliance Policy as the acting Chairperson.
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
            :disabled="!regForm.agreedToTerms"
          >
            Submit Proposal
          </v-btn>
        </div>

      </v-form>
    </v-card>

    <v-snackbar v-model="showSnackbar" :timeout="4000" color="success" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon icon="mdi-check-circle" class="mr-2"></v-icon> {{ snackbarText }}
      </div>
    </v-snackbar>
  </v-container>
</template>