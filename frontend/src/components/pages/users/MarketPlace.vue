<script setup lang="ts">
import { onMounted } from 'vue';
// Make sure this path matches where you saved the file!
import { useMarketPlaceGroup } from '@/composables/useMarketPlaceGroup'; 

// Destructure exactly what your template needs from the logic group
const {
  activeTab,
  searchQuery,
  selectedProductCat,
  productCategories,
  selectedAdvertCat,
  advertCategories,
  filteredProducts,
  filteredAdverts,
  fetchMarketplaceData
} = useMarketPlaceGroup();

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

// Fetch the data from Laravel as soon as the page loads
onMounted(() => {
  fetchMarketplaceData();
});
</script>

<template>
  <v-container fluid class="pa-6" style="max-width: 1200px;">
    
    <div class="d-flex flex-column flex-md-row justify-space-between align-md-end mb-6 gap-4">
      <div>
        <h1 class="text-h5 font-weight-bold text-high-emphasis">Campus Hub</h1>
        
        <v-tabs v-model="activeTab" color="primary" class="mt-2">
          <v-tab value="marketplace" class="text-none font-weight-bold">
            <v-icon start>mdi-shopping-outline</v-icon> Verified Marketplace
          </v-tab>
          <v-tab value="adverts" class="text-none font-weight-bold">
            <v-icon start>mdi-bulletin-board</v-icon> Advertisements
          </v-tab>
          <v-tab value="inquery" class="text-none font-weight-bold">
            <v-icon start>mdi-message-question </v-icon> Inquiry
          </v-tab>
        </v-tabs>
      </div>

      <div class="d-flex gap-3 align-center " style="width: 100%; max-width:250px;">
        <v-text-field
          v-model="searchQuery"
          variant="solo"
          density="compact"
          hide-details
          placeholder="Search..."
          prepend-inner-icon="mdi-magnify"
          bg-color="surface"
          class="rounded-lg mr-2"
        ></v-text-field>
        
      </div>
    </div>

    <div v-if="activeTab === 'marketplace'">
      
      <v-chip-group v-model="selectedProductCat" selected-class="bg-primary text-white border-primary" mandatory class="mb-6">
        <v-chip v-for="cat in productCategories" :key="cat" :value="cat" variant="outlined" class="font-weight-bold border-opacity-50">
          {{ cat }}
        </v-chip>
      </v-chip-group>

      <v-row>
        <v-col cols="6" sm="4" md="3" v-for="item in filteredProducts" :key="item.id">
          <v-card class="rounded-xl elevation-2 border-opacity-50 h-100 d-flex flex-column" border hover>
            <v-img :src="item.image" height="160" cover class="bg-grey-lighten-3"></v-img>
            
            <div class="pa-3 d-flex flex-column flex-grow-1">
              <div class="text-subtitle-1 font-weight-black text-primary line-clamp-1 mb-1">{{ item.price }}</div>
              
              <template v-if="item.category !== 'Services' && item.quantity">
                <v-chip 
                  v-if="Number(item.quantity) > 1" 
                  size="x-small" 
                  color="orange-darken-2" 
                  variant="flat" 
                  class="font-weight-bold mb-2 align-self-start"
                >
                  {{ item.quantity }} in stock
                </v-chip>
                
                <v-chip 
                  v-else-if="Number(item.quantity) === 1" 
                  size="x-small" 
                  color="red-darken-1" 
                  variant="flat" 
                  class="font-weight-bold mb-2 align-self-start"
                >
                  Only 1 left!
                </v-chip>
              </template>
              <div class="text-body-2 font-weight-bold mb-2 line-clamp-2" style="line-height: 1.2;">{{ item.title }}</div>
              
              <div class="mt-auto pt-2 border-top">
                <div class="d-flex align-center gap-1">
                  <v-icon v-if="item.isVerified" icon="mdi-check-decagram" color="blue" size="14"></v-icon>
                  <v-hover v-slot="{ isHovering, props }">
                    <router-link 
                      :to="`/seller/${item.seller_id}`" 
                      class="text-primary"
                      :class="isHovering ? 'text-decoration-underline' : 'text-decoration-none'"
                      v-bind="props"
                    >
                      <span class="text-caption font-weight-medium text-truncate">
                        {{ item.seller }}
                      </span>
                    </router-link>
                  </v-hover>
                </div>
                <div class="text-caption text-medium-emphasis mt-1">{{ item.time }}</div>
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>
    </div>

    <div v-if="activeTab === 'adverts'">
      
      <v-chip-group v-model="selectedAdvertCat" selected-class="bg-primary text-white border-primary" mandatory class="mb-6">
        <v-chip v-for="cat in advertCategories" :key="cat" :value="cat" variant="outlined" class="font-weight-bold border-opacity-50">
          {{ cat }}
        </v-chip>
      </v-chip-group>

      <v-row>
        <v-col cols="12" md="6" v-for="advert in filteredAdverts" :key="advert.id">
          <v-card class="rounded-xl elevation-1 border-opacity-50 pa-5 h-100" border hover>
            <div class="d-flex gap-4">
              
              <div class="d-flex align-center justify-center rounded-lg bg-grey-lighten-4 shrink-0" style="width: 56px; height: 56px;">
                <v-icon 
                  :icon="getAdvertStyling(advert.category).icon" 
                  :color="getAdvertStyling(advert.category).color" 
                  size="28">
                </v-icon>
              </div>

              <div class="flex-grow-1">
                <div class="d-flex justify-space-between align-start mb-1">
                  <v-chip 
                    size="x-small" 
                    variant="tonal" 
                    :color="getAdvertStyling(advert.category).color" 
                    class="font-weight-bold text-uppercase mb-2"
                  >
                    {{ advert.category }}
                  </v-chip>
                  <span class="text-caption text-medium-emphasis">{{ advert.time }}</span>
                </div>
                
                <h3 class="text-subtitle-1 font-weight-bold lh-sm mb-2">{{ advert.title }}</h3>
                <p class="text-body-2 text-medium-emphasis mb-4">{{ advert.description }}</p>
                
                <div class="d-flex align-center justify-space-between">
                  <div class="text-caption font-weight-medium">Posted by: {{ advert.author }}</div>
                  <v-btn variant="tonal" size="small" color="primary" class="text-none font-weight-bold px-4 rounded-lg">
                    Contact
                  </v-btn>
                </div>
              </div>
              
            </div>
          </v-card>
        </v-col>
      </v-row>
    </div>
    
    <div v-if="activeTab === 'inquery'">
        <v-card>
            
        </v-card>
    </div>

  </v-container>
</template>