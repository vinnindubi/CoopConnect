<script setup lang="ts">
import { ref, computed } from 'vue';

// 1. UI State
const searchQuery = ref('');
const selectedCategory = ref('All');
const categories = ref(['All', 'Student Life', 'Housing', 'Academics', 'Experiences', 'Tips & Tricks']);

// 2. Mock Article Data (Removed isFeatured flags)
const rawArticles = ref([
  {
    id: 1,
    title: 'Surviving Your First Year: A Senior’s Ultimate Cheat Sheet',
    excerpt: 'Forget what the brochure told you. Here is the actual truth about making friends, finding the best food, and surviving finals week without losing your mind.',
    category: 'Tips & Tricks',
    authorName: 'Alex Johnson',
    authorRole: '4th Year, Computer Science',
    authorAvatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=150',
    date: 'Oct 12',
    readTime: '5 min read',
    image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=1200'
  },
  {
    id: 2,
    title: 'The Best Off-Campus Housing Spots You Didn’t Know About',
    excerpt: 'Tired of dorm life? We scoured the areas around campus to find the hidden gems that won’t break your HELB loan.',
    category: 'Housing',
    authorName: 'Sarah Kimani',
    authorRole: '3rd Year, Business',
    authorAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
    date: 'Oct 15',
    readTime: '4 min read',
    image: 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&q=80&w=600'
  },
  {
    id: 3,
    title: 'My 48 Hours Awake at the CUK Tech Hackathon',
    excerpt: 'Lots of coffee, zero sleep, and one broken keyboard later. Here is what it’s actually like to compete in the annual tech showdown.',
    category: 'Experiences',
    authorName: 'David Ochieng',
    authorRole: '2nd Year, IT',
    authorAvatar: 'https://images.unsplash.com/photo-1599566150163-29194dcaad36?auto=format&fit=crop&q=80&w=150',
    date: 'Oct 18',
    readTime: '6 min read',
    image: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=600'
  },
  {
    id: 4,
    title: 'Mastering the Art of Meal Prepping on a Student Budget',
    excerpt: 'Tired of eating noodles every night? Here is a realistic, step-by-step guide to cooking an entire week of healthy meals for under KES 1,500.',
    category: 'Student Life',
    authorName: 'Brian Omondi',
    authorRole: '3rd Year, Economics',
    authorAvatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150',
    date: 'Oct 22',
    readTime: '4 min read',
    image: 'https://plus.unsplash.com/premium_photo-1680291971376-ccc54aacb22b?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NXx8Y29va2luZ3xlbnwwfHwwfHx8MA%3D%3D'
  }
]);

// 3. Computed Logic for Filtering (Simplified)
const gridArticles = computed(() => {
  let result = rawArticles.value;

  // Apply Category Filter
  if (selectedCategory.value !== 'All') {
    result = result.filter(a => a.category === selectedCategory.value);
  }
  
  // Apply Search Filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(a => a.title.toLowerCase().includes(query) || a.excerpt.toLowerCase().includes(query));
  }

  return result;
});
</script>

<template>
  <div class="bg-background min-h-screen pb-12">
    <v-container style="max-width: 1200px;">
      
      <v-card class="rounded-xl overflow-hidden mb-10 elevation-3 border-opacity-50" border>
        <v-img 
          src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1200" 
          height="300" 
          cover
        >
          <div class="fill-height d-flex flex-column justify-center align-center text-center pa-6 text-white" 
               style="background: linear-gradient(to right, rgba(0,0,0,0.8), rgba(0,0,0,0.5));">
            
            <v-chip color="primary" variant="flat" class="mb-4 font-weight-bold text-uppercase px-4">
              Campus Unfiltered
            </v-chip>
            
            <h1 class="text-h3 font-weight-black mb-3 text-white">Student Stories</h1>
            <p class="text-h6 font-weight-regular opacity-90 max-width-700 mx-auto">
              The real, unvarnished truth about campus life. Dive into survival guides, late-night thoughts, housing hacks, and experiences written directly by your peers.
            </p>
          </div>
        </v-img>
      </v-card>

      <div class="d-flex flex-column flex-md-row justify-space-between align-md-center mb-8 gap-4">
        
        <v-chip-group v-model="selectedCategory" selected-class="bg-primary text-white border-primary" mandatory>
          <v-chip 
            v-for="cat in categories" 
            :key="cat" 
            :value="cat" 
            variant="outlined" 
            class="font-weight-bold border-opacity-50"
          >
            {{ cat }}
          </v-chip>
        </v-chip-group>

        <div style="width: 100%; max-width: 300px;">
          <v-text-field
            v-model="searchQuery"
            variant="outlined"
            density="compact"
            hide-details
            placeholder="Search stories..."
            prepend-inner-icon="mdi-magnify"
            bg-color="surface"
            class="rounded-lg"
          ></v-text-field>
        </div>
      </div>

      <v-row>
        
        <v-col cols="12" v-if="gridArticles.length === 0">
          <v-sheet class="text-center pa-10 rounded-xl border border-dashed bg-transparent" elevation="0">
            <v-icon icon="mdi-text-box-search-outline" size="48" color="grey-lighten-1" class="mb-2"></v-icon>
            <div class="text-h6 text-medium-emphasis">No stories found</div>
            <div class="text-body-2 text-medium-emphasis mt-1">Try searching for something else or changing the category.</div>
          </v-sheet>
        </v-col>

        <v-col cols="12" sm="6" md="4" v-for="article in gridArticles" :key="article.id">
          <v-card class="rounded-xl elevation-2 border-opacity-50 h-100 d-flex flex-column bg-surface" border hover>
            
            <div class="position-relative">
              <v-img :src="article.image" height="200" cover></v-img>
              <v-chip size="small" color="surface" class="position-absolute font-weight-bold elevation-1" style="top: 12px; left: 12px;">
                {{ article.category }}
              </v-chip>
            </div>

            <div class="pa-5 d-flex flex-column flex-grow-1">
              <div class="d-flex align-center justify-space-between mb-3 text-caption text-medium-emphasis font-weight-medium">
                <span>{{ article.date }}</span>
                <span><v-icon icon="mdi-clock-outline" size="14" class="mr-1"></v-icon> {{ article.readTime }}</span>
              </div>
              
              <h3 class="text-h6 font-weight-bold mb-3 text-high-emphasis">
                {{ article.title }}
              </h3>
              
              <p class="text-body-2 text-medium-emphasis mb-6">
                {{ article.excerpt }}
              </p>

              <div class="mt-auto pt-4 border-top d-flex align-center gap-3">
                <v-avatar size="36">
                  <v-img :src="article.authorAvatar" cover></v-img>
                </v-avatar>
                <div class="overflow-hidden">
                  <div class="text-subtitle-2 font-weight-bold text-truncate text-high-emphasis">{{ article.authorName }}</div>
                  <div class="text-caption text-medium-emphasis text-truncate">{{ article.authorRole }}</div>
                </div>
              </div>
            </div>
            
          </v-card>
        </v-col>

      </v-row>

    </v-container>
  </div>
</template>