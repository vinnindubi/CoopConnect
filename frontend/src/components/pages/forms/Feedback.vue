<script setup lang="ts">
import { ref } from 'vue';

// 1. Form State
const feedbackCategory = ref('Feature Suggestion');
const rating = ref(0);
const feedbackDetails = ref('');
const attachment = ref<File | null>(null);
const isAnonymous = ref(false);
const allowContact = ref(true);

// 2. UI State
const isSubmitting = ref(false);
const isSuccess = ref(false);

// 3. Form Options
const categories = ref([
  { label: 'Feature Suggestion', icon: 'mdi-lightbulb-on-outline', color: 'amber-darken-2' },
  { label: 'Bug Report', icon: 'mdi-bug-outline', color: 'error' },
  { label: 'General Review', icon: 'mdi-star-outline', color: 'primary' },
  { label: 'Need Help', icon: 'mdi-help-circle-outline', color: 'info' }
]);

// 4. Submission Handler
const submitFeedback = () => {
  if (!feedbackDetails.value) return; 
  
  isSubmitting.value = true;
  
  setTimeout(() => {
    isSubmitting.value = false;
    isSuccess.value = true;
  }, 1500);
};

const submitAnother = () => {
  isSuccess.value = false;
  feedbackDetails.value = '';
  rating.value = 0;
  attachment.value = null;
};
</script>

<template>
  <div class="bg-background min-h-screen py-8">
    <v-container style="max-width: 800px;">
      
      <div class="text-center mb-10">
        <v-avatar color="primary" variant="tonal" size="64" class="mb-4">
          <v-icon icon="mdi-message-draw" size="32" color="primary"></v-icon>
        </v-avatar>
        
        <h1 class="text-h4 font-weight-black text-high-emphasis mb-4">Help us build CoopConnect</h1>
        
        <p class="text-body-1 text-medium-emphasis px-md-10 mx-auto" style="max-width: 650px; line-height: 1.6;">
          This app is built for students, by students. Whether you found a glitch, have a brilliant idea for a new feature, or just want to tell us how we are doing—we read every single submission. 
          <br><br>
          <strong>Your feedback directly determines what we build next</strong> and helps keep our campus platform safe, fast, and bug-free.
        </p>
      </div>

      <v-card class="rounded-xl elevation-3 border-opacity-50 bg-surface" border>
        
        <div v-if="isSuccess" class="pa-10 d-flex flex-column align-center justify-center text-center py-16">
          <v-avatar color="success" variant="tonal" size="80" class="mb-6">
            <v-icon icon="mdi-check-all" size="40"></v-icon>
          </v-avatar>
          <h2 class="text-h5 font-weight-bold mb-2">Feedback Received!</h2>
          <p class="text-body-1 text-medium-emphasis mb-8 max-w-[400px]">
            Thank you for taking the time to share your thoughts. Your input directly shapes the future of the app.
          </p>
          <v-btn color="primary" variant="outlined" class="text-none font-weight-bold rounded-lg px-6" @click="submitAnother">
            Submit Another Response
          </v-btn>
        </div>

        <div v-else class="pa-6 pa-md-10">
          <v-form @submit.prevent="submitFeedback">
            
            <div class="mb-8">
              <label class="text-subtitle-1 font-weight-bold d-block mb-3">1. What is this regarding?</label>
              <v-chip-group v-model="feedbackCategory" selected-class="bg-primary text-white" mandatory column>
                <v-chip 
                  v-for="cat in categories" 
                  :key="cat.label" 
                  :value="cat.label" 
                  variant="outlined" 
                  class="font-weight-bold border-opacity-50 px-5 py-5 mb-2 mr-2"
                >
                  <v-icon start :icon="cat.icon" :color="feedbackCategory === cat.label ? 'white' : cat.color"></v-icon>
                  {{ cat.label }}
                </v-chip>
              </v-chip-group>
            </div>

            <v-expand-transition>
              <div class="mb-8" v-if="feedbackCategory === 'General Review'">
                <label class="text-subtitle-1 font-weight-bold d-block mb-2">2. How would you rate your experience?</label>
                <v-rating v-model="rating" color="amber-darken-2" active-color="amber-darken-2" hover size="x-large"></v-rating>
              </div>
            </v-expand-transition>

            <div class="mb-8">
              <label class="text-subtitle-1 font-weight-bold d-block mb-2">
                {{ feedbackCategory === 'General Review' ? '3.' : '2.' }} The Details <span class="text-error">*</span>
              </label>
              <v-textarea
                v-model="feedbackDetails"
                variant="outlined"
                placeholder="Tell us what's on your mind... Please be as specific as possible!"
                rows="5"
                auto-grow
                hide-details
                bg-color="surface"
                class="rounded-lg"
                :rules="[v => !!v || 'Details are required']"
              ></v-textarea>
            </div>

            <div class="mb-8">
              <label class="text-subtitle-1 font-weight-bold d-block mb-2 text-medium-emphasis">
                {{ feedbackCategory === 'General Review' ? '4.' : '3.' }} Attach a Screenshot (Optional)
              </label>
              <v-file-input
                v-model="attachment"
                variant="outlined"
                density="compact"
                prepend-inner-icon="mdi-camera-outline"
                prepend-icon=""
                placeholder="Drop an image here or click to browse"
                accept="image/*"
                hide-details
                class="rounded-lg"
              ></v-file-input>
            </div>

            <div class="mb-8">
              <v-sheet class="rounded-xl pa-6 border border-opacity-50" color="transparent">
                <h4 class="text-subtitle-2 font-weight-bold mb-4 text-high-emphasis">Privacy Preferences</h4>
                
                <v-switch
                  v-model="isAnonymous"
                  color="primary"
                  hide-details
                  density="compact"
                  class="mb-3"
                >
                  <template v-slot:label>
                    <span class="text-body-2 font-weight-medium text-high-emphasis">Submit Anonymously</span>
                  </template>
                </v-switch>
                
                <v-expand-transition>
                  <div v-if="!isAnonymous">
                    <v-divider class="mb-4 mt-2 opacity-20"></v-divider>
                    <v-switch
                      v-model="allowContact"
                      color="primary"
                      hide-details
                      density="compact"
                    >
                      <template v-slot:label>
                        <span class="text-body-2 text-medium-emphasis">It is okay to contact me regarding this feedback.</span>
                      </template>
                    </v-switch>
                  </div>
                </v-expand-transition>
              </v-sheet>
            </div>

            <v-btn 
              type="submit" 
              color="primary" 
              size="x-large" 
              block
              class="text-none font-weight-bold rounded-lg" 
              :loading="isSubmitting"
              :disabled="!feedbackDetails"
            >
              Send Feedback
              <v-icon end icon="mdi-send" class="ml-2"></v-icon>
            </v-btn>

          </v-form>
        </div>
      </v-card>

    </v-container>
  </div>
</template>