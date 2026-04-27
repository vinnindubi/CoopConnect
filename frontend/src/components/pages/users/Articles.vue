<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
// Remember to use the 'import * as' syntax we fixed earlier!
import articleService from '@/services/articleService';


// 1. UI & Loading State
const isLoading = ref(true);
const fetchError = ref(false);
const searchQuery = ref('');
const selectedCategory = ref('All');
const categories = ref(['All']); 

// 2. Main Feed State
const rawArticles = ref<any[]>([]);

// 3. Reader Dialog State (For the popup!)
const isReadingDialog = ref(false);
const isLoadingArticle = ref(false);
const activeArticle = ref<any>(null);

// --- Methods ---

// Fetch all articles for the grid
const fetchArticlesData = async () => {
  isLoading.value = true;
  fetchError.value = false;
  
  try {
    const response = await articleService.getArticles();
    const payload = response.data || response;
    
    categories.value = payload.categories || ['All'];
    rawArticles.value = payload.articles || payload.data || payload || [];
  } catch (error) {
    console.error("Failed to load articles feed:", error);
    fetchError.value = true;
  } finally {
    isLoading.value = false;
  }
};

// Fetch full details and open the popup
const readArticle = async (id: number) => {
  isReadingDialog.value = true; 
  isLoadingArticle.value = true; 
  activeArticle.value = null; 

  try {
    const response = await articleService.getArticle(id);
    activeArticle.value = response.data?.data || response.data || response;
  } catch (error) {
    console.error("Failed to load full article details:", error);
  } finally {
    isLoadingArticle.value = false;
  }
};
// --- Comments State ---
const showComments = ref(false);
const comments = ref<any[]>([]);
const newComment = ref('');
const isLoadingComments = ref(false);
const isSubmittingComment = ref(false);

// Function to toggle and fetch comments
// Toggle and fetch comments
const toggleComments = async () => {
  showComments.value = !showComments.value;
  
  if (showComments.value && comments.value.length === 0 && activeArticle.value?.id) {
    isLoadingComments.value = true;
    try {
      const res = await articleService.getComments(activeArticle.value.id);
      
      // 1. Log exactly what Laravel sends back
      //console.log("1. RAW BACKEND RESPONSE:", res); 

      // 2. Safely extract the array no matter how it is wrapped
      let fetchedComments = [];
      if (res.data && Array.isArray(res.data.data)) {
        fetchedComments = res.data.data; // Laravel JSON wrapper
      } else if (res.data && Array.isArray(res.data)) {
        fetchedComments = res.data; // Standard Axios
      } else if (Array.isArray(res)) {
        fetchedComments = res; // Raw array
      } else {
        console.warn("Could not find an array of comments in the response!");
      }

      comments.value = fetchedComments;
      
      // 3. Confirm what Vue is actually trying to render
      //console.log("2. COMMENTS VUE IS RENDERING:", comments.value);

    } catch (err) {
      console.error("Could not load comments:", err);
    } finally {
      isLoadingComments.value = false;
    }
  }
};

// Function to post a new comment
const submitComment = async () => {
  // Prevent empty submissions
  if (!newComment.value.trim() || !activeArticle.value?.id) return;

  isSubmittingComment.value = true;
  try {
    const res = await articleService.postComment(activeArticle.value.id, { 
      content: newComment.value // This perfectly matches our Laravel validation!
    });
    
    // The backend sends back the new comment WITH the user data attached.
    // We 'unshift' it to push it to the very top of the list instantly.
    comments.value.unshift(res.data?.data || res.data);
    newComment.value = ''; // Clear the input field
    
  } catch (err) {
    console.error("Comment submission failed:", err);
  } finally {
    isSubmittingComment.value = false;
  }
};

// Update closeDialog to clean up
const closeDialog = () => {
  isReadingDialog.value = false;
  setTimeout(() => {
    activeArticle.value = null;
    showComments.value = false;
    comments.value = [];
  }, 300);
};

onMounted(() => {
  fetchArticlesData();
});

// --- Computed Properties ---
const gridArticles = computed(() => {
  let result = rawArticles.value;

  if (selectedCategory.value !== 'All') {
    result = result.filter(a => a.category === selectedCategory.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(a => 
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
        <v-img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&q=80&w=1200" height="300" cover>
          <div class="fill-height d-flex flex-column justify-center align-center text-center pa-6 text-white" style="background: linear-gradient(to right, rgba(0,0,0,0.8), rgba(0,0,0,0.5));">
            <v-chip color="primary" variant="flat" class="mb-4 font-weight-bold text-uppercase px-4">Campus Unfiltered</v-chip>
            <h1 class="text-h3 font-weight-black mb-3 text-white">Student Stories</h1>
            <p class="text-h6 font-weight-regular opacity-90 max-width-700 mx-auto">
              The real, unvarnished truth about campus life. Dive into survival guides, late-night thoughts, housing hacks, and experiences written directly by your peers.
            </p>
          </div>
        </v-img>
      </v-card>

      <div class="d-flex flex-column flex-md-row justify-space-between align-md-center mb-8 gap-4">
        <v-chip-group v-model="selectedCategory" selected-class="bg-primary text-white border-primary" mandatory>
          <v-chip v-for="cat in categories" :key="cat" :value="cat" variant="outlined" class="font-weight-bold border-opacity-50">{{ cat }}</v-chip>
        </v-chip-group>

        <div style="width: 100%; max-width: 300px;">
          <v-text-field v-model="searchQuery" variant="outlined" density="compact" hide-details placeholder="Search stories..." prepend-inner-icon="mdi-magnify" bg-color="surface" class="rounded-lg"></v-text-field>
        </div>
      </div>

      <v-row>
        
        <v-col cols="12" v-if="isLoading">
          <v-sheet class="text-center pa-10 bg-transparent">
            <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
          </v-sheet>
        </v-col>

        <v-col cols="12" v-else-if="gridArticles.length === 0">
          <v-sheet class="text-center pa-10 rounded-xl border border-dashed bg-transparent" elevation="0">
            <v-icon icon="mdi-text-box-search-outline" size="48" color="grey-lighten-1" class="mb-2"></v-icon>
            <div class="text-h6 text-medium-emphasis">No stories found</div>
          </v-sheet>
        </v-col>

        <v-col cols="12" sm="6" md="4" v-for="article in gridArticles" :key="article.id">
          <v-card class="rounded-xl elevation-2 border-opacity-50 h-100 d-flex flex-column bg-surface" border hover @click="readArticle(article.id)" style="cursor: pointer;">
            
            <div class="position-relative">
              <v-img :src="article.image" height="200" cover></v-img>
              <v-chip size="small" color="surface" class="position-absolute font-weight-bold elevation-1" style="top: 12px; left: 12px;">
                {{ article.category }}
              </v-chip>
            </div>

            <div class="pa-5 d-flex flex-column flex-grow-1">
              <div class="d-flex align-center justify-space-between mb-3 text-caption text-medium-emphasis font-weight-medium">
                <span>{{ article.created_at ? new Date(article.created_at).toLocaleDateString() : article.date }}</span>
                <span><v-icon icon="mdi-clock-outline" size="14" class="mr-1"></v-icon> {{ article.read_time || article.readTime || '5 min read' }}</span>
              </div>
              
              <h3 class="text-h6 font-weight-bold mb-3 text-high-emphasis" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ article.title }}
              </h3>
              
              <p class="text-body-2 text-medium-emphasis mb-6" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                {{ article.excerpt }}
              </p>

              <div class="mt-auto pt-4 border-top d-flex align-center gap-3">
                <v-avatar size="36">
                  <v-img :src="article.user?.avatar || article.authorAvatar" cover></v-img>
                </v-avatar>
                <div class="overflow-hidden">
                  <div class="text-subtitle-2 font-weight-bold text-truncate text-high-emphasis">{{ article.user?.fullname || article.authorName }}</div>
                  <div class="text-caption text-medium-emphasis text-truncate">{{ article.user?.course || article.authorRole }}</div>
                </div>
              </div>
            </div>
            
          </v-card>
        </v-col>
      </v-row>
    </v-container>

    <v-dialog v-model="isReadingDialog" max-width="800" scrollable transition="dialog-bottom-transition" @update:model-value="(val) => !val && closeDialog()">
  <v-card class="rounded-xl overflow-hidden bg-surface" elevation="4">
    
    <v-toolbar color="surface" elevation="0" class="border-b border-opacity-25 px-2">
      <v-chip size="small" color="primary" variant="tonal" class="font-weight-bold ml-2">{{ activeArticle?.category || 'Article' }}</v-chip>
      <v-spacer></v-spacer>
      <v-btn icon="mdi-close" variant="text" @click="closeDialog"></v-btn>
    </v-toolbar>

    <v-card-text v-if="isLoadingArticle" class="pa-10 text-center d-flex align-center justify-center">
      <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </v-card-text>

    <v-card-text class="pa-0" v-else-if="activeArticle">
      
      <v-container class="pt-6">
        <v-img v-if="activeArticle.image" :src="activeArticle.image" height="220" class="rounded-xl mx-auto border" style="max-width: 500px;" cover></v-img>
      </v-container>

      <div class="pa-6 pa-md-10">
        <h1 class="text-h4 font-weight-black text-high-emphasis mb-4" style="line-height: 1.3;">{{ activeArticle.title }}</h1>
        
        <div class="d-flex align-center mb-8 pb-6 border-b border-opacity-25">
           <v-avatar size="44" color="grey-lighten-2" class="mr-4">
             <v-img v-if="activeArticle.user?.avatar || activeArticle.authorAvatar" :src="activeArticle.user?.avatar || activeArticle.authorAvatar" cover></v-img>
             <v-icon v-else icon="mdi-account"></v-icon>
           </v-avatar>
           <div>
             <div class="font-weight-bold text-subtitle-1">{{ activeArticle.user?.fullname || activeArticle.authorName || 'Student Writer' }}</div>
             <div class="text-caption text-medium-emphasis">
               {{ activeArticle.created_at ? new Date(activeArticle.created_at).toLocaleDateString() : (activeArticle.date || '') }} 
               • {{ activeArticle.read_time || activeArticle.readTime || '5 min read' }}
             </div>
           </div>
        </div>

        <div class="text-body-1 text-high-emphasis" style="white-space: pre-wrap; font-size: 1.15rem !important; line-height: 1.8;">
          {{ activeArticle.content }}
        </div>

        <v-expand-transition>
          <div v-show="showComments" class="mt-10 pt-6 border-t">
            <h3 class="text-h6 font-weight-bold mb-6">Comments ({{ comments.length }})</h3>
            
            <div class="d-flex gap-3 mb-8">
              <v-avatar size="40" color="primary" variant="tonal">
                <v-icon icon="mdi-account"></v-icon>
              </v-avatar>
              
              <v-text-field 
                v-model="newComment" 
                placeholder="Write a comment..." 
                variant="outlined" 
                density="compact" 
                hide-details 
                class="rounded-lg"
                @keyup.enter="submitComment" 
                :disabled="isSubmittingComment"
              >
                <template v-slot:append-inner>
                  <v-btn 
                    icon="mdi-send" 
                    variant="text" 
                    size="small" 
                    color="primary"
                    :loading="isSubmittingComment" 
                    :disabled="!newComment.trim()"
                    @click="submitComment"
                  ></v-btn>
                </template>
              </v-text-field>
            </div>
            <div v-if="isLoadingComments" class="text-center py-4">
              <v-progress-circular indeterminate color="primary" size="32"></v-progress-circular>
            </div>
            
            <div v-for="comment in comments" :key="comment.id" class="d-flex gap-3 mb-6">
              <v-avatar size="36" class="border">
                <v-img :src="comment.user?.avatar"></v-img>
              </v-avatar>
              <div class="bg-grey-lighten-4 pa-4 rounded-xl rounded-ts-0 flex-grow-1">
                <div class="d-flex justify-space-between mb-1">
                  <span class="text-subtitle-2 font-weight-bold">{{ comment.user?.fullname || 'Student' }}</span>
                  <span class="text-caption">{{ new Date(comment.created_at).toLocaleDateString() }}</span>
                </div>
                <p class="text-body-2 mb-0" style="white-space: pre-wrap;">{{ comment.content }}</p>
              </div>
            </div>
          </div>
        </v-expand-transition>

      </div>
    </v-card-text>

    <v-card-actions class="pa-4 border-t bg-white">
      <v-btn 
        variant="tonal" 
        color="primary" 
        class="text-none font-weight-bold px-6" 
        rounded="lg"
        @click="toggleComments"
      >
        <v-icon start :icon="showComments ? 'mdi-chevron-up' : 'mdi-comment-outline'"></v-icon>
        {{ showComments ? 'Hide Discussion' : 'See Comments' }}
      </v-btn>
      <v-spacer></v-spacer>
      <v-btn variant="text" @click="closeDialog" class="font-weight-bold">Close</v-btn>
    </v-card-actions>

  </v-card>
</v-dialog>

  </div>
</template>

<style scoped>
.gap-4 { gap: 16px; }
.gap-3 { gap: 12px; }
</style>