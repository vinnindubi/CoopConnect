<script setup lang="ts">
import { ref } from 'vue';

const currentYear = ref(new Date().getFullYear());

const quickLinks = ref([
  { name: 'Dashboard', icon: 'mdi-view-dashboard-outline' },
  { name: 'Campus Store', icon: 'mdi-storefront-outline' },
  { name: 'Upcoming Events', icon: 'mdi-ticket-confirmation-outline' },
  { name: 'Student Stories', icon: 'mdi-fountain-pen-tip' }
]);

const supportLinks = ref([
  { name: 'Help Center' },
  { name: 'Safety & Trust Guidelines' },
  { name: 'Report an Issue' },
  { name: 'Leave Feedback' }
]);

const socialLinks = ref([
  { icon: 'mdi-instagram' },
  { icon: 'mdi-twitter' },
  { icon: 'mdi-linkedin' }
]);
</script>

<template>
  <v-footer class="bg-surface border-t border-opacity-20 mt-12 px-0 py-10" elevation="0">
    <v-container style="max-width: 1200px;">
      
      <v-row class="mb-8">
        
        <v-col cols="12" md="4" class="pr-md-10 mb-6 mb-md-0">
          <div class="d-flex align-center gap-3 mb-4">
            <v-avatar color="primary" size="44" class="rounded-lg">
              <v-icon icon="mdi-handshake-outline" color="white" size="24"></v-icon>
            </v-avatar>
            <span class="text-h5 font-weight-black text-high-emphasis">CoopConnect</span>
          </div>
          
          <p class="text-body-2 text-medium-emphasis mb-6" style="max-width: 350px; line-height: 1.6;">
            The ultimate campus hub built for students, by students. Discover events, buy and sell gear, and share your experiences all in one place.
          </p>
          
          <div class="d-flex gap-3">
            <v-btn 
              v-for="(social, i) in socialLinks" 
              :key="i" 
              :icon="social.icon" 
              variant="tonal" 
              color="primary" 
              size="small" 
              class="rounded-lg"
            ></v-btn>
          </div>
        </v-col>

        <v-col cols="12" sm="4" md="2" class="mb-6 mb-sm-0">
          <h4 class="text-subtitle-2 font-weight-bold text-high-emphasis text-uppercase mb-4 tracking-wide">Explore</h4>
          <div class="d-flex flex-column align-start">
            <v-btn 
              v-for="link in quickLinks" 
              :key="link.name" 
              variant="plain" 
              density="compact"
              class="text-none px-0 mb-2 text-medium-emphasis font-weight-medium"
              :ripple="false"
            >
              <v-icon start :icon="link.icon" size="18" class="opacity-70"></v-icon>
              {{ link.name }}
            </v-btn>
          </div>
        </v-col>

        <v-col cols="12" sm="4" md="3" class="mb-6 mb-sm-0">
          <h4 class="text-subtitle-2 font-weight-bold text-high-emphasis text-uppercase mb-4 tracking-wide">Support</h4>
          <div class="d-flex flex-column align-start">
            <v-btn 
              v-for="link in supportLinks" 
              :key="link.name" 
              variant="plain" 
              density="compact"
              class="text-none px-0 mb-2 text-medium-emphasis font-weight-medium"
              :ripple="false"
            >
              {{ link.name }}
            </v-btn>
          </div>
        </v-col>

        <v-col cols="12" sm="4" md="3">
          <v-card class="bg-surface-variant border border-opacity-50 rounded-xl pa-5 text-center h-100 d-flex flex-column justify-center" elevation="0">
            <v-icon icon="mdi-storefront-outline" color="primary" size="32" class="mb-2 mx-auto"></v-icon>
            <h4 class="text-subtitle-2 font-weight-bold text-high-emphasis mb-1">Become a Seller</h4>
            <p class="text-caption text-medium-emphasis mb-4">
              Turn your old textbooks and electronics into cash today.
            </p>
            <v-btn color="primary" variant="flat" size="small" block class="text-none font-weight-bold rounded-lg mt-auto">
              Apply Now
            </v-btn>
          </v-card>
        </v-col>
        
      </v-row>

      <v-divider class="mb-6 opacity-20"></v-divider>

      <div class="d-flex flex-column flex-md-row justify-space-between align-md-center text-caption text-medium-emphasis font-weight-medium">
        <div class="mb-4 mb-md-0">
          &copy; {{ currentYear }} CoopConnect. All rights reserved.
        </div>
        
        <div class="d-flex flex-wrap gap-4">
          <v-btn variant="plain" density="compact" class="text-none px-0 text-caption font-weight-medium" :ripple="false">Privacy Policy</v-btn>
          <span class="opacity-30 mt-1">•</span>
          <v-btn variant="plain" density="compact" class="text-none px-0 text-caption font-weight-medium" :ripple="false">Terms of Service</v-btn>
          <span class="opacity-30 mt-1">•</span>
          <v-btn variant="plain" density="compact" class="text-none px-0 text-caption font-weight-medium" :ripple="false">Campus Rules</v-btn>
        </div>
      </div>

    </v-container>
  </v-footer>
</template>