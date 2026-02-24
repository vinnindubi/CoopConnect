<script setup lang="ts">
import { ref, computed } from 'vue';

// 1. UI State
const activeTab = ref('marketplace'); // 'marketplace' or 'adverts'
const searchQuery = ref('');

// 2. Marketplace State (Verified Sellers)
const selectedProductCat = ref('All');
const productCategories = ref(['All', 'Electronics', 'Fashion', 'Academic', 'Services']);

const products = ref([
  {
    id: 1,
    title: 'HP EliteBook 840 G5 (Core i5)',
    price: 'KES 25,000',
    category: 'Electronics',
    seller: 'TechHub Campus',
    isVerified: true, 
    time: '2h ago',
    image: 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&q=80&w=400'
  },
  {
    id: 2,
    title: 'Knotless Braids & Dreadlocks',
    price: 'From KES 800',
    category: 'Services',
    seller: 'Glamour Cuts',
    isVerified: true,
    time: '1d ago',
    image: 'https://images.unsplash.com/photo-1605497788044-5a32c7078486?auto=format&fit=crop&q=80&w=400'
  },
  {
    id: 3,
    title: 'Casio fx-991EX Scientific Calculator',
    price: 'KES 1,500',
    category: 'Academic',
    seller: 'Engineering Seniors',
    isVerified: true,
    time: '3h ago',
    image: 'https://images.unsplash.com/photo-1632571401005-458e9d244591?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8Q2FzaW8lMjBmeC05OTFFWCUyMFNjaWVudGlmaWMlMjBDYWxjdWxhdG9yfGVufDB8fDB8fHww'
  },
  {
    id: 4,
    title: 'Vintage Thrifted Varsity Jackets',
    price: 'KES 1,200',
    category: 'Fashion',
    seller: 'Drip Thrift Store',
    isVerified: true,
    time: '5h ago',
    image: 'https://images.unsplash.com/photo-1584966393803-c8c7257794cd?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MjR8fFZpbnRhZ2UlMjBUaHJpZnRlZCUyMFZhcnNpdHklMjBKYWNrZXRzfGVufDB8fDB8fHww'
  },
  {
    id: 5,
    title: 'Sony Noise Cancelling Headphones',
    price: 'KES 3,500',
    category: 'Electronics',
    seller: 'Gadget Gadgets',
    isVerified: true, // An unverified student seller
    time: 'Just now',
    image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&q=80&w=400'
  },
  {
    id: 6,
    title: 'Laptop Repair & Windows Installation',
    price: 'From KES 500',
    category: 'Services',
    seller: 'Campus IT Guru',
    isVerified: true,
    time: '1d ago',
    image: 'https://images.unsplash.com/photo-1709102884400-b50ca1a12bc3?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fHJlcGFpciUyMGxhcHRvcHN8ZW58MHx8MHx8fDA%3D'
  },
  {
    id: 7,
    title: 'Nike Air Force 1 (Size 40-44)',
    price: 'KES 2,500',
    category: 'Fashion',
    seller: 'Kicks Ke',
    isVerified: true,
    time: '6h ago',
    image: 'https://images.unsplash.com/photo-1712167631738-4dab9e53c853?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTh8fE5pa2UlMjBBaXIlMjBGb3JjZSUyMDF8ZW58MHx8MHx8fDA%3D'
  },
  {
    id: 8,
    title: 'Business Law & Economics Textbooks',
    price: 'KES 1,200',
    category: 'Academic',
    seller: 'Jane Doe',
    isVerified: true,
    time: '2d ago',
    image: 'https://images.unsplash.com/photo-1588580000645-4562a6d2c839?auto=format&fit=crop&q=80&w=400'
  },
  {
    id: 9,
    title: 'Original iPhone 20W Fast Charger',
    price: 'KES 1,800',
    category: 'Electronics',
    seller: 'TechHub Campus',
    isVerified: true,
    time: '4h ago',
    image: 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&q=80&w=400'
  },
  {
    id: 10,
    title: 'Graduation & Birthday Photoshoots',
    price: 'KES 3,000 / session',
    category: 'Services',
    seller: 'Lens Crafters',
    isVerified: true,
    time: '3d ago',
    image: 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&q=80&w=400'
  }
]);

// 3. Adverts State (Peer-to-Peer Requests & Opportunities)
const selectedAdvertCat = ref('All');
const advertCategories = ref(['All', 'Housing', 'Collaborations', 'Jobs', 'General']);

const adverts = ref([
  {
    id: 1,
    title: 'Looking for a Female Roommate',
    category: 'Housing',
    description: 'Seeking a neat roommate to share a 1-bedroom apartment in Karen Ndogo. Rent is 6k per month. WiFi included.',
    author: 'Sarah M.',
    time: '5h ago',
    icon: 'mdi-home-account',
    iconColor: 'orange-darken-2'
  },
  {
    id: 2,
    title: 'Frontend Developer Needed for Startup',
    category: 'Collaborations',
    description: 'We are building a fintech app for students and need a Vue.js developer to join our team. Equity compensation.',
    author: 'CoopInnovate Team',
    time: '1d ago',
    icon: 'mdi-code-braces',
    iconColor: 'blue-darken-2'
  },
  {
    id: 3,
    title: 'Part-time Library Assistant',
    category: 'Jobs',
    description: 'The main library is looking for 3 students to work evening shifts organizing shelves. Apply at the admin block.',
    author: 'Library Admin',
    time: '2d ago',
    icon: 'mdi-briefcase-outline',
    iconColor: 'green-darken-2'
  }
]);

// 4. Computed Filters
const filteredProducts = computed(() => {
  let result = products.value;
  if (selectedProductCat.value !== 'All') result = result.filter(p => p.category === selectedProductCat.value);
  if (searchQuery.value) result = result.filter(p => p.title.toLowerCase().includes(searchQuery.value.toLowerCase()));
  return result;
});

const filteredAdverts = computed(() => {
  let result = adverts.value;
  if (selectedAdvertCat.value !== 'All') result = result.filter(a => a.category === selectedAdvertCat.value);
  if (searchQuery.value) result = result.filter(a => a.title.toLowerCase().includes(searchQuery.value.toLowerCase()));
  return result;
});
</script>
<template>
  <v-container fluid class="pa-6" style="max-width: 1200px;">
    
    <div class="d-flex flex-column flex-md-row justify-space-between align-md-end mb-6 gap-4">
      <div>
        <h1 class="text-h5 font-weight-bold text-grey-darken-4">Campus Hub</h1>
        
        <v-tabs v-model="activeTab" color="primary" class="mt-2">
          <v-tab value="marketplace" class="text-none font-weight-bold">
            <v-icon start>mdi-shopping-outline</v-icon> Verified Marketplace
          </v-tab>
          <v-tab value="adverts" class="text-none font-weight-bold">
            <v-icon start>mdi-bulletin-board</v-icon> Advertisements
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
              <div class="text-subtitle-1 font-weight-black text-primary line-clamp-1">{{ item.price }}</div>
              <div class="text-body-2 font-weight-bold mb-2 line-clamp-2" style="line-height: 1.2;">{{ item.title }}</div>
              
              <div class="mt-auto pt-2 border-top">
                <div class="d-flex align-center gap-1">
                  <v-icon v-if="item.isVerified" icon="mdi-check-decagram" color="blue" size="14"></v-icon>
                  <span class="text-caption font-weight-medium text-truncate">{{ item.seller }}</span>
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
                <v-icon :icon="advert.icon" :color="advert.iconColor" size="28"></v-icon>
              </div>

              <div class="flex-grow-1">
                <div class="d-flex justify-space-between align-start mb-1">
                  <v-chip size="x-small" variant="tonal" :color="advert.iconColor" class="font-weight-bold text-uppercase mb-2">
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

  </v-container>
</template>