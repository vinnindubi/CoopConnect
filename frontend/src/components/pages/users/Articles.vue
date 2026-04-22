<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import articleService from '@/services/articleService'; // Adjust path if necessary

// 1. UI & Loading State
const isLoading = ref(true);
const fetchError = ref(false);
const searchQuery = ref('');
const selectedCategory = ref('All');
const categories = ref(['All']); // Will be populated dynamically by the backend

// 2. Main Feed State (No more mock data!)
const rawArticles = ref<any[]>([]);

// 3. Reader Dialog State (For viewing a single article)
const isReadingDialog = ref(false);
const isLoadingArticle = ref(false);
const activeArticle = ref<any>(null);

// --- Methods ---

// Fetch all articles and categories for the grid
const fetchArticlesData = async () => {
  isLoading.value = true;
  fetchError.value = false;
  
  try {
    const response = await articleService.getArticles();
    
    // Safely extract the data
    const payload = response.data || response;
    
    categories.value = payload.categories || ['All'];
    rawArticles.value = payload.articles || [];
    
  } catch (error) {
    console.error("Failed to load articles feed:", error);
    fetchError.value = true;
  } finally {
    isLoading.value = false;
  }
};

// Fetch full details when a student clicks a specific card
const readArticle = async (id: number) => {
  isReadingDialog.value = true; // Open the popup instantly
  isLoadingArticle.value = true; // Show a spinner
  activeArticle.value = null; // Clear out the previous article

  try {
    const response = await articleService.getArticle(id);
    activeArticle.value = response.data || response;
  } catch (error) {
    console.error("Failed to load full article details:", error);
  } finally {
    isLoadingArticle.value = false;
  }
};

// Fire the fetch function the second the page loads!
onMounted(() => {
  fetchArticlesData();
});

// --- Computed Properties ---

// Filter logic (Now fully null-safe)
const gridArticles = computed(() => {
  let result = rawArticles.value;

  // Apply Category Filter
  if (selectedCategory.value !== 'All') {
    result = result.filter(a => a.category === selectedCategory.value);
  }
  
  // Apply Search Filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(a => 
      // Added '?.' (optional chaining) so it won't crash if a title or excerpt is missing
      a.title?.toLowerCase().includes(query) || 
      a.excerpt?.toLowerCase().includes(query)
    );
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