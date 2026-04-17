<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
// Import the actual API service we built earlier!
import { createGlobalAnnouncement } from '@/services/adminService'; 

const router = useRouter();

// --- UI State ---
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

// --- Form State ---
const alertForm = ref({
  title: '',
  severity: 'error', // Set this to 'error' to match the Enum/Options values
  message: '',
  action_link: '',
  action_text: 'More Info'
});

const severityOptions = [
  { title: 'Critical (Red)', value: 'error' },
  { title: 'Warning (Orange)', value: 'warning' },
  { title: 'Information (Blue)', value: 'info' }
];

// Keep the button color dynamic based on selection
const actionColor = computed(() => {
  return alertForm.value.severity || 'primary';
});

// --- Submission ---
const submitAlert = async () => {
  if (!alertForm.value.title || !alertForm.value.message) {
    snackbarText.value = 'Please provide both a Title and a Message.';
    snackbarColor.value = 'error';
    showSnackbar.value = true;
    return;
  }

  isSubmitting.value = true;
  try {
    // --- REAL BACKEND CONNECTION ---
    await createGlobalAnnouncement(alertForm.value);
    
    snackbarText.value = 'Emergency broadcast sent to all students!';
    snackbarColor.value = 'success';
    showSnackbar.value = true;
    
    // Give the user 1.5 seconds to see the success message before redirecting
   router.push('/admin');
  } catch (error: any) {
    console.error("Failed to save broadcast:", error);
    snackbarText.value = error.response?.data?.message || 'Failed to send broadcast. Check your connection.';
    snackbarColor.value = 'error';
    showSnackbar.value = true;
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <v-container fluid class="pa-4 pa-md-8 bg-background min-vh-100">
    <v-container style="max-width: 800px;">
      
      <div class="d-flex align-center mb-8">
        <v-btn icon="mdi-arrow-left" variant="text" class="mr-4" @click="router.push('/admin')"></v-btn>
        <div>
          <h1 class="text-h4 font-weight-black text-error d-flex align-center">
            <v-icon icon="mdi-broadcast" class="mr-3"></v-icon> Broadcast Alert
          </h1>
          <p class="text-body-1 text-medium-emphasis mt-1">Send an urgent notification directly to all student dashboards.</p>
        </div>
      </div>

      <v-card class="rounded-xl border-opacity-25 bg-surface pa-6 pa-md-8" border elevation="2">
        <h3 class="text-h6 font-weight-bold mb-6 d-flex align-center">
          <v-icon icon="mdi-text-box-edit-outline" color="primary" class="mr-2"></v-icon> Alert Details
        </h3>
        
        <v-row>
          <v-col cols="12" sm="8">
            <div class="text-caption font-weight-bold text-high-emphasis mb-1">Headline / Title *</div>
            <v-text-field v-model="alertForm.title" placeholder="e.g., Campus Closed Due To Weather" variant="outlined" density="comfortable" hide-details></v-text-field>
          </v-col>
          
          <v-col cols="12" sm="4">
            <div class="text-caption font-weight-bold text-high-emphasis mb-1">Severity Level *</div>
            <v-select v-model="alertForm.severity" :items="severityOptions" item-title="title" item-value="value" variant="outlined" density="comfortable" hide-details></v-select>
          </v-col>

          <v-col cols="12">
            <div class="text-caption font-weight-bold text-high-emphasis mb-1">Detailed Message *</div>
            <v-textarea v-model="alertForm.message" placeholder="Provide clear instructions for students..." rows="5" auto-grow variant="outlined" density="comfortable" hide-details></v-textarea>
          </v-col>

          <v-col cols="12" sm="6">
            <div class="text-caption font-weight-bold text-high-emphasis mb-1">Action Button Text (Optional)</div>
            <v-text-field v-model="alertForm.action_text" placeholder="e.g., Read Full Policy" variant="outlined" density="comfortable" hide-details></v-text-field>
          </v-col>

          <v-col cols="12" sm="6">
            <div class="text-caption font-weight-bold text-high-emphasis mb-1">Action Link URL (Optional)</div>
            <v-text-field v-model="alertForm.action_link" placeholder="https://..." variant="outlined" density="comfortable" hide-details prepend-inner-icon="mdi-link"></v-text-field>
          </v-col>
        </v-row>

        <div class="d-flex justify-end mt-8 pt-6 border-t border-opacity-25">
          <v-btn variant="text" color="medium-emphasis" class="text-none font-weight-bold mr-4" @click="router.push('/admin')">Cancel</v-btn>
          <v-btn :color="actionColor" size="large" class="text-none font-weight-black rounded-lg px-8" prepend-icon="mdi-send" :loading="isSubmitting" @click="submitAlert">
            Send Broadcast
          </v-btn>
        </div>
      </v-card>

    </v-container>

    <v-snackbar v-model="showSnackbar" :timeout="4000" :color="snackbarColor" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon :icon="snackbarColor === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle'" class="mr-3"></v-icon>
        {{ snackbarText }}
      </div>
    </v-snackbar>
  </v-container>
</template>