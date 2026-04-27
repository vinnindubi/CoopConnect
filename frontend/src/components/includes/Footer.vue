<script setup lang="ts">
// No imports needed! This component is now completely static and lightweight.

const currentYear = new Date().getFullYear();

// Standard JavaScript arrays. No reactive overhead.
const quickLinks = [
  { name: 'Dashboard', icon: 'mdi-view-dashboard-outline', route: '/' },
  { name: 'Campus Store', icon: 'mdi-storefront-outline', route: '/marketplace' },
  { name: 'Upcoming Events', icon: 'mdi-calendar-week', route: '/events' },
  { name: 'Student Stories', icon: 'mdi-fountain-pen-tip', route: '/articles' },
  { name: 'Register Club or Society', icon: 'mdi-account-group-outline', route: '/registerClubOrSociety' }
];

const supportLinks = [
  { name: 'Report an Issue', route:'/feedback' },
  { name: 'Leave Feedback', route: '/feedback' }
];

const socialLinks = [
  { icon: 'mdi-instagram', url:'https://instagram.com'},
  { icon: 'mdi-twitter' ,url:'https://x.com'},
  { icon: 'mdi-linkedin',url:'https://linkedin.com' }
];
</script>

<template>
  <v-footer class="bg-blue-darken-4 pt-12 pb-6 px-0" elevation="0">
    <v-container style="max-width: 1200px;">
      
      <v-row class="mb-8">
        
        <v-col cols="12" md="4" class="pr-md-10 mb-8 mb-md-0">
          <div class="d-flex align-center gap-3 mb-5">
            <v-avatar color="white" size="48" class="rounded-lg elevation-2">
              <v-icon icon="mdi-handshake-outline" color="blue-darken-4" size="28"></v-icon>
            </v-avatar>
            <span class="text-h5 font-weight-black text-white ml-1">CoopConnect</span>
          </div>
          
          <p class="text-body-1 text-blue-lighten-5 font-weight-medium mb-8" style="max-width: 350px; line-height: 1.6;">
            The ultimate campus hub built for students, by students. Discover events, buy and sell products, and share your experiences all in one place.
          </p>
          
          <div class="d-flex gap-3">
            <v-btn 
              v-for="(social, i) in socialLinks" 
              :key="i" 
              :icon="social.icon" 
              :href="social.url"
              target="_blank"
              rel="noopener noreferrer"
              variant="tonal" 
              color="white" 
              size="small" 
              class="rounded-lg mr-1"
            ></v-btn>
          </div>
        </v-col>

        <v-col cols="12" sm="4" md="2" class="mb-8 mb-sm-0">
          <h4 class="text-subtitle-1 font-weight-black text-white text-uppercase mb-5" style="letter-spacing: 1px;">Explore</h4>
          <div class="d-flex flex-column align-start">
            <v-btn 
              v-for="link in quickLinks" 
              :key="link.name" 
              :to="link.route"
              variant="plain" 
              density="compact"
              class="text-none px-0 mb-3 text-blue-lighten-4 font-weight-bold opacity-90"
              :ripple="false"
            >
              <v-icon start :icon="link.icon" size="20" class="mr-2"></v-icon>
              {{ link.name }}
            </v-btn>
          </div>
        </v-col>

        <v-col cols="12" sm="4" md="2" class="mb-8 mb-sm-0">
          <h4 class="text-subtitle-1 font-weight-black text-white text-uppercase mb-5" style="letter-spacing: 1px;">Support</h4>
          <div class="d-flex flex-column align-start">
            <v-btn 
              v-for="link in supportLinks" 
              :key="link.name" 
              :to="link.route"
              variant="plain" 
              density="compact"
              class="text-none px-0 mb-3 text-blue-lighten-4 font-weight-bold opacity-90 mr-1"
              :ripple="false"
            >
              {{ link.name }}
            </v-btn>
          </div>
        </v-col>

        <v-col cols="12" sm="12" md="4" class="d-flex align-center">
          <v-row>
            
            <v-col cols="12" sm="6">
              <v-card class="bg-surface rounded-xl pa-5 text-center d-flex flex-column justify-center elevation-4 mx-auto" border hover style="border-top: 4px solid rgb(var(--v-theme-primary)) !important; max-width: 250px;">
                <v-avatar color="primary" variant="tonal" size="48" class="mb-3 mx-auto rounded-circle">
                  <v-icon icon="mdi-storefront-outline" size="24"></v-icon>
                </v-avatar>
                <h4 class="text-subtitle-1 font-weight-black text-high-emphasis mb-1 leading-tight">Become a Seller</h4>
                <p class="text-caption text-medium-emphasis font-weight-medium mb-4" style="line-height: 1.4;">
                  Turn old gear into cash.
                </p>
                <v-btn to="/applySeller" color="primary" variant="flat" block class="text-none font-weight-black rounded-lg elevation-2 mt-auto">
                  Apply Now
                </v-btn>
              </v-card>
            </v-col>

            <v-col cols="12" sm="6">
              <v-card class="bg-surface rounded-xl pa-5 text-center d-flex flex-column justify-center elevation-4 mx-auto" border hover style="border-top: 4px solid #4CAF50 !important; max-width: 250px;">
                <v-avatar color="success" variant="tonal" size="48" class="mb-3 mx-auto rounded-circle">
                  <v-icon icon="mdi-hand-heart-outline" size="24"></v-icon>
                </v-avatar>
                <h4 class="text-subtitle-1 font-weight-black text-high-emphasis mb-1 leading-tight">Feed a Comrade</h4>
                <p class="text-caption text-medium-emphasis font-weight-medium mb-4" style="line-height: 1.4;">
                  Support fellow students.
                </p>
                <v-btn to="/donate" color="success" variant="flat" block class="text-none font-weight-black rounded-lg elevation-2 mt-auto">
                  <v-icon start icon="mdi-cellphone"></v-icon> M-Pesa
                </v-btn>
              </v-card>
            </v-col>

          </v-row>
        </v-col>
        
      </v-row>

      <v-divider class="mb-6 border-opacity-25" color="white"></v-divider>

      <div class="d-flex flex-column flex-md-row justify-space-between align-md-center text-body-2 text-blue-lighten-5 font-weight-medium opacity-80">
        <div class="mb-4 mb-md-0">
          &copy; {{ currentYear }} CoopConnect. All rights reserved.
        </div>
        
        <div class="d-flex flex-wrap gap-4">
          <v-btn variant="plain" density="compact" class="text-none px-0 text-body-2 font-weight-medium" :ripple="false">Privacy Policy</v-btn>
          <span class="mt-1 opacity-50">•</span>
          <v-btn variant="plain" density="compact" class="text-none px-0 text-body-2 font-weight-medium" :ripple="false">Terms of Service</v-btn>
          <span class="mt-1 opacity-50">•</span>
          <v-btn variant="plain" density="compact" class="text-none px-0 text-body-2 font-weight-medium" :ripple="false">Campus Rules</v-btn>
        </div>
      </div>

    </v-container>
  </v-footer>
</template>

<style scoped>
/* Ensure titles are clean and don't push heights on small screens */
.leading-tight { 
  line-height: 1.2 !important; 
}
</style>