<script setup lang="ts">
import { onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useSellerProfile } from '@/composables/useSellerProfile';

const route = useRoute();
const router = useRouter();
const sellerId = route.params.id as string;

// Pull everything from our streamlined composable
const {
  isLoading,
  errorMessage,
  profile,
  products,
  adverts,
  fetchSellerData
} = useSellerProfile();

// Helper function to format advert icons correctly (since we removed them from the backend)
const getAdvertStyling = (category: string) => {
  switch (category) {
    case 'Housing':
      return { icon: 'mdi-home-account', color: 'orange-darken-2' };
    case 'Collaborations':
      return { icon: 'mdi-code-braces', color: 'blue-darken-2' };
    case 'Jobs':
      return { icon: 'mdi-briefcase-outline', color: 'green-darken-2' };
    default:
      return { icon: 'mdi-bulletin-board', color: 'grey-darken-2' };
  }
};

onMounted(() => {
  if (sellerId) {
    fetchSellerData(sellerId);
  }
});
</script>

<template>
  <v-container fluid class="pa-6" style="max-width: 1200px;">
    
    <v-btn variant="text" prepend-icon="mdi-arrow-left" @click="router.back()" class="mb-4 text-none font-weight-bold text-medium-emphasis">
      Back to Marketplace
    </v-btn>

    <div v-if="isLoading" class="d-flex justify-center py-12">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </div>

    <v-alert v-else-if="errorMessage" type="error" variant="tonal" class="mb-6">
      {{ errorMessage }}
    </v-alert>

    <div v-else-if="profile.fullname">
      
      <v-card class="rounded-xl pa-6 mb-8 border-opacity-50" border elevation="1" color="surface">
        <div class="d-flex flex-column flex-sm-row align-center align-sm-start gap-6">
          
          <v-avatar color="primary-lighten-4" size="90" class="border">
            <v-img v-if="profile.avatar" :src="profile.avatar" cover></v-img>
            <span v-else class="text-h3 font-weight-black text-primary">
              {{ profile.fullname.charAt(0).toUpperCase() }}
            </span>
          </v-avatar>

          <div class="text-center text-sm-left flex-grow-1">
            <div class="d-flex align-center justify-center justify-sm-start gap-2 mb-2">
              <h1 class="text-h4 font-weight-black">{{ profile.fullname }}</h1>
              <v-icon v-if="profile.isVerified" icon="mdi-check-decagram" color="blue" size="28"></v-icon>
            </div>
            
            <div class="d-flex flex-wrap justify-center justify-sm-start gap-4 mb-4">
              <v-chip prepend-icon="mdi-phone" variant="tonal" color="success" class="font-weight-medium">
                {{ profile.phone || 'No phone provided' }}
              </v-chip>
              <v-chip prepend-icon="mdi-email" variant="tonal" color="primary" class="font-weight-medium">
                {{ profile.email }}
              </v-chip>
            </div>

            <div class="text-caption text-medium-emphasis font-weight-medium">
              <v-icon size="16" class="mr-1">mdi-calendar</v-icon> 
              Joined {{ profile.joined }}
            </div>
          </div>
        </div>
      </v-card>

      <v-row>
        <v-col cols="12" md="7" lg="8">
          <div class="d-flex align-center justify-space-between mb-4">
            <h2 class="text-h6 font-weight-bold d-flex align-center gap-2">
              <v-icon>mdi-storefront-outline</v-icon> Products ({{ products.length }})
            </h2>
          </div>

          <v-alert v-if="products.length === 0" color="info" variant="tonal" class="rounded-xl">
            This seller has no active products.
          </v-alert>

          <v-row v-else>
            <v-col cols="6" sm="6" md="4" v-for="item in products" :key="item.id">
              <v-card class="rounded-xl elevation-2 border-opacity-50 h-100 d-flex flex-column" border hover>
                <v-img :src="item.image" height="140" cover class="bg-grey-lighten-3"></v-img>
                
                <div class="pa-3 d-flex flex-column flex-grow-1">
                  <div class="text-subtitle-1 font-weight-black text-primary line-clamp-1 mb-1">{{ item.price }}</div>
                  
                  <template v-if="item.category !== 'Services' && item.quantity">
                    <v-chip v-if="Number(item.quantity) > 1" size="x-small" color="orange-darken-2" variant="flat" class="font-weight-bold mb-2 align-self-start">
                      {{ item.quantity }} in stock
                    </v-chip>
                    <v-chip v-else-if="Number(item.quantity) === 1" size="x-small" color="red-darken-1" variant="flat" class="font-weight-bold mb-2 align-self-start">
                      Only 1 left!
                    </v-chip>
                  </template>

                  <div class="text-body-2 font-weight-bold mb-2 line-clamp-2" style="line-height: 1.2;">{{ item.title }}</div>
                  
                  <div class="mt-auto pt-2 border-top">
                    <div class="text-caption text-medium-emphasis mt-1">Posted {{ item.time }}</div>
                  </div>
                </div>
              </v-card>
            </v-col>
          </v-row>
        </v-col>

        <v-col cols="12" md="5" lg="4">
          <div class="d-flex align-center justify-space-between mb-4">
            <h2 class="text-h6 font-weight-bold d-flex align-center gap-2">
              <v-icon>mdi-bulletin-board</v-icon> Adverts ({{ adverts.length }})
            </h2>
          </div>

          <v-alert v-if="adverts.length === 0" color="info" variant="tonal" class="rounded-xl">
            This seller has no active adverts.
          </v-alert>

          <div v-else class="d-flex flex-column gap-3">
            <v-card 
              v-for="advert in adverts" 
              :key="advert.id"
              class="rounded-xl border-opacity-50" 
              border 
              elevation="1" 
              hover
            >
              <div class="d-flex pa-4 gap-4 align-start">
                <div class="d-flex align-center justify-center rounded-lg bg-grey-lighten-4 shrink-0" style="width: 48px; height: 48px;">
                  <v-icon 
                    :icon="getAdvertStyling(advert.category).icon" 
                    :color="getAdvertStyling(advert.category).color" 
                    size="24">
                  </v-icon>
                </div>
                
                <div class="flex-grow-1">
                  <div class="d-flex justify-space-between align-start mb-1">
                    <v-chip size="x-small" variant="tonal" :color="getAdvertStyling(advert.category).color" class="font-weight-bold text-uppercase">
                      {{ advert.category }}
                    </v-chip>
                    <span class="text-caption text-medium-emphasis">{{ advert.time }}</span>
                  </div>
                  <h3 class="text-subtitle-2 font-weight-bold mb-1" style="line-height: 1.2;">{{ advert.title }}</h3>
                  <p class="text-caption text-medium-emphasis line-clamp-2">{{ advert.description }}</p>
                </div>
              </div>
            </v-card>
          </div>
        </v-col>
      </v-row>

    </div>
  </v-container>
</template>

