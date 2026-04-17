<script setup lang="ts">
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import { EVENT_CATEGORIES } from '@/utils/constants';
import { createGlobalEvent } from '@/services/adminService';

const router = useRouter();

// --- UI State ---
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

// --- Event Form State ---
const eventForm = ref({
  title: '',
  event_type: EVENT_CATEGORIES[0],
  event_date: '',
  location: '',
  price: null as number | null,
  image: '',
  description: '',
  organizer: 'Campus Connect' // Default for global events
});

// --- Smart Live Preview Computed Properties ---
const previewMonth = computed(() => {
  if (!eventForm.value.event_date) return 'TBA';
  return new Date(eventForm.value.event_date).toLocaleString('default', { month: 'short' }).toUpperCase();
});

const previewDay = computed(() => {
  if (!eventForm.value.event_date) return '-';
  return new Date(eventForm.value.event_date).getDate().toString();
});

const previewTime = computed(() => {
  if (!eventForm.value.event_date) return 'Time TBA';
  return new Date(eventForm.value.event_date).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
});

const previewIcon = computed(() => {
  const loc = eventForm.value.location.toLowerCase();
  return (loc.includes('meet') || loc.includes('zoom') || loc.includes('online')) ? 'mdi-video-outline' : 'mdi-map-marker-outline';
});

// --- Methods ---
const submitEvent = async () => {
  if (!eventForm.value.title || !eventForm.value.event_date || !eventForm.value.description) {
    snackbarText.value = 'Please fill in all required fields (Title, Date, Description).';
    snackbarColor.value = 'error';
    showSnackbar.value = true;
    return;
  }

  isSubmitting.value = true;
  try {
    // await createGlobalEvent(eventForm.value);
    
    // Simulate API call for now
    await createGlobalEvent(eventForm.value);
    
    snackbarText.value = 'Global event published successfully!';
    snackbarColor.value = 'success';
    showSnackbar.value = true;
    
    // Send them back to the dash after a short delay
    setTimeout(() => {
      router.push('/admin');
    }, 1500);

  } catch (error) {
    snackbarText.value = 'Failed to publish event. Please try again.';
    snackbarColor.value = 'error';
    showSnackbar.value = true;
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<template>
  <v-container fluid class="pa-4 pa-md-8 bg-background min-vh-100">
    <v-container style="max-width: 1200px;">
      
      <div class="d-flex align-center mb-8">
        <v-btn icon="mdi-arrow-left" variant="text" class="mr-4" @click="router.push('/admin')"></v-btn>
        <div>
          <h1 class="text-h4 font-weight-black text-high-emphasis">Create Global Event</h1>
          <p class="text-body-1 text-medium-emphasis mt-1">This event will be visible to all students on the campus calendar.</p>
        </div>
      </div>

      <v-row>
        <v-col cols="12" md="7" lg="8">
          <v-card class="rounded-xl border-opacity-25 bg-surface pa-6 pa-md-8" border elevation="1">
            <h3 class="text-h6 font-weight-bold mb-6 d-flex align-center">
              <v-icon icon="mdi-text-box-edit-outline" color="primary" class="mr-2"></v-icon> Event Details
            </h3>
            
            <v-row>
              <v-col cols="12">
                <div class="text-caption font-weight-bold text-high-emphasis mb-1">Event Title *</div>
                <v-text-field v-model="eventForm.title" placeholder="e.g., End of Year Campus Bash" variant="outlined" density="comfortable" hide-details></v-text-field>
              </v-col>
              
              <v-col cols="12" sm="6">
                <div class="text-caption font-weight-bold text-high-emphasis mb-1">Category *</div>
                <v-select v-model="eventForm.event_type" :items="EVENT_CATEGORIES" variant="outlined" density="comfortable" hide-details></v-select>
              </v-col>
              
              <v-col cols="12" sm="6">
                <div class="text-caption font-weight-bold text-high-emphasis mb-1">Date & Time *</div>
                <v-text-field v-model="eventForm.event_date" type="datetime-local" variant="outlined" density="comfortable" hide-details></v-text-field>
              </v-col>

              <v-col cols="12" sm="6">
                <div class="text-caption font-weight-bold text-high-emphasis mb-1">Location</div>
                <v-text-field v-model="eventForm.location" placeholder="e.g., Main Amphitheater" variant="outlined" density="comfortable" hide-details prepend-inner-icon="mdi-map-marker-outline"></v-text-field>
              </v-col>

              <v-col cols="12" sm="6">
                <div class="text-caption font-weight-bold text-high-emphasis mb-1">Price (KES)</div>
                <v-text-field v-model="eventForm.price" type="number" placeholder="Leave empty for Free" variant="outlined" density="comfortable" hide-details prefix="KES"></v-text-field>
              </v-col>

              <v-col cols="12">
                <div class="text-caption font-weight-bold text-high-emphasis mb-1">Organizer Name (Optional)</div>
                <v-text-field v-model="eventForm.organizer" placeholder="Campus Connect" variant="outlined" density="comfortable" hide-details hint="Who is hosting this? Defaults to Campus Connect."></v-text-field>
              </v-col>

              <v-col cols="12">
                <div class="text-caption font-weight-bold text-high-emphasis mb-1">Banner Image URL</div>
                <v-text-field v-model="eventForm.image" placeholder="https://images.unsplash.com/..." variant="outlined" density="comfortable" hide-details prepend-inner-icon="mdi-image-outline"></v-text-field>
              </v-col>

              <v-col cols="12">
                <div class="text-caption font-weight-bold text-high-emphasis mb-1">Description *</div>
                <v-textarea v-model="eventForm.description" placeholder="What should students expect?" rows="4" auto-grow variant="outlined" density="comfortable" hide-details></v-textarea>
              </v-col>
            </v-row>

            <div class="d-flex justify-end mt-8 pt-6 border-t border-opacity-25">
              <v-btn variant="text" color="medium-emphasis" class="text-none font-weight-bold mr-4" @click="router.push('/admin')">Cancel</v-btn>
              <v-btn color="primary" size="large" class="text-none font-weight-black rounded-lg px-8" :loading="isSubmitting" @click="submitEvent">
                Publish Event
              </v-btn>
            </div>
          </v-card>
        </v-col>

        <v-col cols="12" md="5" lg="4">
          <div class="position-sticky" style="top: 24px;">
            <div class="text-caption font-weight-black text-primary text-uppercase tracking-widest mb-4 d-flex align-center">
              <v-icon icon="mdi-eye-outline" size="16" class="mr-2"></v-icon> Live Preview
            </div>
            
            <v-card class="rounded-xl elevation-4 border-opacity-50 d-flex flex-column bg-surface" border>
              <div class="position-relative">
                <v-img :src="eventForm.image || 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=600'" height="200" cover class="bg-grey-lighten-3"></v-img>
                
                <div class="position-absolute bg-surface rounded-lg elevation-2 d-flex flex-column align-center justify-center" style="top: 12px; left: 12px; width: 50px; height: 56px;">
                  <span class="text-caption font-weight-bold text-primary" style="font-size: 10px !important;">{{ previewMonth }}</span>
                  <span class="text-h6 font-weight-black leading-none mt-n1 text-high-emphasis">{{ previewDay }}</span>
                </div>

                <v-chip size="small" color="surface" class="position-absolute font-weight-bold elevation-1" style="top: 12px; right: 12px;">
                  {{ eventForm.price ? `KES ${eventForm.price}` : 'Free' }}
                </v-chip>
              </div>

              <div class="pa-5 d-flex flex-column flex-grow-1">
                <v-chip size="x-small" variant="tonal" color="primary" class="text-uppercase font-weight-bold mb-3 align-self-start">
                  {{ eventForm.event_type }}
                </v-chip>
                
                <h3 class="text-h5 font-weight-bold mb-3 line-clamp-2 leading-tight">
                  {{ eventForm.title || 'Your Event Title' }}
                </h3>
                
                <div class="d-flex flex-column gap-2 text-body-2 text-medium-emphasis mb-4">
                  <div class="d-flex align-start gap-2">
                    <v-icon icon="mdi-clock-outline" size="18" class="mt-1 shrink-0"></v-icon>
                    <span>{{ previewTime }}</span>
                  </div>
                  <div class="d-flex align-start gap-2">
                    <v-icon :icon="previewIcon" size="18" class="mt-1 shrink-0"></v-icon>
                    <span class="line-clamp-1">{{ eventForm.location || 'Campus (TBA)' }}</span>
                  </div>
                </div>

                <p class="text-body-2 text-medium-emphasis mb-6 line-clamp-3">
                  {{ eventForm.description || 'Event description will appear here...' }}
                </p>

                <div class="mt-auto d-flex align-center pt-4 border-t border-opacity-25">
                  <v-avatar size="32" color="primary" variant="tonal" class="mr-3">
                    <v-icon icon="mdi-shield-crown-outline" size="16"></v-icon>
                  </v-avatar>
                  <div>
                    <div class="text-caption text-medium-emphasis font-weight-medium">Hosted by</div>
                    <div class="text-body-2 font-weight-bold text-primary">{{ eventForm.organizer || 'Campus Connect' }}</div>
                  </div>
                </div>
              </div>
            </v-card>
          </div>
        </v-col>
      </v-row>

    </v-container>

    <v-snackbar v-model="showSnackbar" :timeout="4000" :color="snackbarColor" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon :icon="snackbarColor === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle'" class="mr-3"></v-icon>
        {{ snackbarText }}
      </div>
    </v-snackbar>
  </v-container>
</template>

<style scoped>
.gap-2 { gap: 8px; }
.tracking-widest { letter-spacing: 0.1em !important; }
.leading-tight { line-height: 1.2 !important; }

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>