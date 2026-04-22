<script setup lang="ts">
import { ref, computed } from 'vue';

// --- State ---
const searchQuery = ref('');
const selectedCategory = ref('All');
const showNewPostDialog = ref(false);

// --- Options ---
const categories = ['All', 'Campus Life', 'Academics', 'Housing', 'Attachment & Careers', 'Marketplace', 'Rants'];

// --- Mock Data ---
const threads = ref([
  {
    id: 1,
    title: "Is the new 24/7 library schedule actually helping anyone?",
    excerpt: "I've been going to the library at 2 AM and it's mostly empty, but the guards seem exhausted. Are we actually using this or should the school revert the budget to something else?",
    author: { name: 'David O.', avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150', isVerified: true },
    category: 'Campus Life',
    upvotes: 142,
    comments: 56,
    timeAgo: '2 hours ago',
    hot: true
  },
  {
    id: 2,
    title: "Looking for a roommate (Male) - Ruiru Area",
    excerpt: "Hey guys, my current roommate is graduating. I have a 2-bedroom place near the main gate. Rent is KES 8,000 per person. Looking for someone clean and preferably in 3rd/4th year.",
    author: { name: 'Brian Kamau', avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150', isVerified: false },
    category: 'Housing',
    upvotes: 12,
    comments: 8,
    timeAgo: '5 hours ago',
    hot: false
  },
  {
    id: 3,
    title: "Best tech companies for Software Engineering attachments?",
    excerpt: "I'm starting my attachment search for next semester. Has anyone here interned at Safaricom or Microsoft ADC? What was the interview process like?",
    author: { name: 'Sarah W.', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150', isVerified: true },
    category: 'Attachment & Careers',
    upvotes: 89,
    comments: 34,
    timeAgo: '1 day ago',
    hot: true
  },
  {
    id: 4,
    title: "The WiFi in Block C has been down for 3 days 😡",
    excerpt: "How are we supposed to submit assignments when the school WiFi keeps dropping? I've used up all my mobile data. Anyone else facing this issue?",
    author: { name: 'Anonymous', avatar: '', isVerified: false },
    category: 'Rants',
    upvotes: 215,
    comments: 89,
    timeAgo: '2 days ago',
    hot: false
  }
]);

// --- Computed ---
// Filters the threads based on the search bar and the selected category chip
const filteredThreads = computed(() => {
  let filtered = threads.value;
  
  if (selectedCategory.value !== 'All') {
    filtered = filtered.filter(t => t.category === selectedCategory.value);
  }
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(t => 
      t.title.toLowerCase().includes(query) || 
      t.excerpt.toLowerCase().includes(query)
    );
  }
  
  return filtered;
});

// --- Methods ---
const upvoteThread = (id: number) => {
  const thread = threads.value.find(t => t.id === id);
  if (thread) thread.upvotes++;
  // TODO: Send upvote to Laravel backend
};
// --- Form State for New Topic ---
const formRef = ref();
const isSubmitting = ref(false);
const newTopic = ref({
  title: '',
  category: '',
  excerpt: ''
});

// We don't want users to select "All" as a category when posting
const postableCategories = computed(() => categories.filter(c => c !== 'All'));

// --- Form Validation Rules ---
const rules = {
  required: (v: string) => !!v || 'This field is required',
  minLength: (v: string) => (v && v.length >= 10) || 'Must be at least 10 characters'
};

const submitTopic = async () => {
  const { valid } = await formRef.value.validate();
  
  if (valid) {
    isSubmitting.value = true;
    
    // TODO: Replace this timeout with your actual Laravel API call!
    // Example: await apiClient.post('/threads', newTopic.value);
    setTimeout(() => {
      // Mocking a successful post by adding it to the top of our local array
      threads.value.unshift({
        id: Date.now(),
        title: newTopic.value.title,
        excerpt: newTopic.value.excerpt,
        author: { name: 'You', avatar: '', isVerified: false }, // Replace with logged-in user
        category: newTopic.value.category,
        upvotes: 0,
        comments: 0,
        timeAgo: 'Just now',
        hot: false
      });

      // Reset and close
      isSubmitting.value = false;
      showNewPostDialog.value = false;
      formRef.value.reset();
    }, 1000);
  }
};
</script>

<template>
  <v-container fluid class="bg-background min-vh-100 py-6 py-md-10">
    <v-container style="max-width: 1100px;">
      
      <div class="mb-8 text-center text-md-left">
        <h1 class="text-h3 font-weight-black text-primary mb-2">Community Forum</h1>
        <p class="text-body-1 text-medium-emphasis">Ask questions, debate ideas, and connect with your campus.</p>
      </div>

      <v-row>
        <v-col cols="12" md="8">
          
          <v-card class="rounded-xl border-opacity-25 mb-6 pa-4 bg-surface" border elevation="1">
            <v-text-field
              v-model="searchQuery"
              placeholder="Search discussions..."
              prepend-inner-icon="mdi-magnify"
              variant="solo-filled"
              flat
              density="compact"
              hide-details
              class="rounded-lg mb-4"
            ></v-text-field>

            <v-chip-group v-model="selectedCategory" color="primary" mandatory>
              <v-chip 
                v-for="category in categories" 
                :key="category" 
                :value="category"
                variant="outlined" 
                class="font-weight-bold"
              >
                {{ category }}
              </v-chip>
            </v-chip-group>
          </v-card>

          <div v-if="filteredThreads.length > 0" class="d-flex flex-column gap-4">
            <v-card 
              v-for="thread in filteredThreads" 
              :key="thread.id" 
              class="rounded-xl border-opacity-25 transition-swing bg-surface" 
              border 
              elevation="1" 
              hover
              :ripple="false"
            >
              <div class="pa-5">
                <div class="d-flex align-center justify-space-between mb-3">
                  <div class="d-flex align-center">
                    <v-avatar size="32" color="primary" variant="tonal" class="mr-3">
                      <v-img v-if="thread.author.avatar" :src="thread.author.avatar" cover></v-img>
                      <v-icon v-else icon="mdi-incognito" color="primary" size="20"></v-icon>
                    </v-avatar>
                    <div>
                      <div class="text-subtitle-2 font-weight-bold d-flex align-center text-high-emphasis">
                        {{ thread.author.name }}
                        <v-icon v-if="thread.author.isVerified" icon="mdi-check-decagram" color="success" size="14" class="ml-1" title="Verified Student"></v-icon>
                      </div>
                      <div class="text-caption text-medium-emphasis">{{ thread.timeAgo }}</div>
                    </div>
                  </div>
                  
                  <v-chip size="small" variant="tonal" color="primary" class="font-weight-bold">
                    {{ thread.category }}
                  </v-chip>
                </div>

                <h2 class="text-h6 font-weight-bold text-high-emphasis mb-2 leading-tight cursor-pointer hover-text-primary">
                  {{ thread.title }}
                </h2>
                <p class="text-body-2 text-medium-emphasis mb-4 line-clamp-2">
                  {{ thread.excerpt }}
                </p>

                <div class="d-flex align-center gap-4">
                  <v-btn 
                    variant="tonal" 
                    color="primary" 
                    size="small" 
                    class="rounded-pill font-weight-bold px-4"
                    @click.stop="upvoteThread(thread.id)"
                  >
                    <v-icon start icon="mdi-arrow-up-bold-outline" size="18"></v-icon> {{ thread.upvotes }}
                  </v-btn>
                  
                  <v-btn 
                    variant="text" 
                    color="medium-emphasis" 
                    size="small" 
                    class="rounded-pill font-weight-bold px-4 text-none"
                  >
                    <v-icon start icon="mdi-comment-text-outline" size="18"></v-icon> {{ thread.comments }} Replies
                  </v-btn>

                  <v-spacer></v-spacer>

                  <v-chip v-if="thread.hot" size="small" color="error" variant="flat" class="font-weight-bold px-3">
                    <v-icon start icon="mdi-fire" size="14"></v-icon> Hot
                  </v-chip>
                </div>
              </div>
            </v-card>
          </div>

          <v-card v-else class="rounded-xl border border-dashed pa-10 text-center bg-transparent" elevation="0">
            <v-avatar color="grey" size="64" class="mb-4">
              <v-icon icon="mdi-forum-outline" size="32" color="primary"></v-icon>
            </v-avatar>
            <h3 class="text-h6 font-weight-bold text-high-emphasis">No discussions found</h3>
            <p class="text-medium-emphasis">Try adjusting your filters or be the first to start this topic!</p>
          </v-card>

        </v-col>

        <v-col cols="12" md="4">
          <div class="position-sticky" style="top: 24px;">
            
            <v-card class="rounded-xl border-opacity-25 mb-6 pa-5 text-center" color="primary" elevation="3" border>
              <h3 class="text-h6 font-weight-bold mb-2">Have something to say?</h3>
              <p class="text-body-2 opacity-80 mb-4">Start a discussion, ask a question, or share an opportunity with the campus.</p>
              <v-btn 
                @click="showNewPostDialog = true" 
                color="surface" 
                variant="flat" 
                block 
                size="large" 
                class="text-primary font-weight-black text-none rounded-lg"
                prepend-icon="mdi-plus"
              >
                Start New Topic
              </v-btn>
            </v-card>

            <v-card class="rounded-xl border-opacity-25 mb-6 bg-surface" border elevation="1">
              <div class="pa-4 border-b border-opacity-25 font-weight-black text-uppercase text-caption text-medium-emphasis tracking-widest">
                Trending Right Now
              </div>
              <v-list lines="one" class="py-2 bg-transparent">
                <v-list-item link class="px-4">
                  <div class="d-flex align-center justify-space-between w-100">
                    <span class="font-weight-bold text-body-2 text-high-emphasis">#Graduation2026</span>
                    <span class="text-caption text-medium-emphasis">142 posts</span>
                  </div>
                </v-list-item>
                <v-list-item link class="px-4">
                  <div class="d-flex align-center justify-space-between w-100">
                    <span class="font-weight-bold text-body-2 text-high-emphasis">#HELF_Loans</span>
                    <span class="text-caption text-medium-emphasis">89 posts</span>
                  </div>
                </v-list-item>
                <v-list-item link class="px-4">
                  <div class="d-flex align-center justify-space-between w-100">
                    <span class="font-weight-bold text-body-2 text-high-emphasis">#LostAndFound</span>
                    <span class="text-caption text-medium-emphasis">34 posts</span>
                  </div>
                </v-list-item>
              </v-list>
            </v-card>

           <v-card class="rounded-xl border-opacity-25 bg-surface" border elevation="1">
                <div class="pa-4 border-b border-opacity-25 font-weight-black text-uppercase text-caption text-medium-emphasis tracking-widest d-flex align-center">
                    <v-icon icon="mdi-shield-check-outline" size="16" class="mr-2 text-primary"></v-icon> Forum Rules
                </div>
                <div class="pa-4 text-caption text-medium-emphasis">
                    <ol class="pl-4 d-flex flex-column gap-2">
                    <li>Be respectful to fellow students.</li>
                    <li>No spam or self-promotion outside the Marketplace tab.</li>
                    <li>Do not post personal identifiable information (doxxing).</li>
                    <li>Keep debates constructive.</li>
                    </ol>
                </div>
            </v-card>
          </div>
        </v-col>
      </v-row>

    </v-container>
  </v-container>
  <v-dialog v-model="showNewPostDialog" max-width="600" persistent>
      <v-card class="rounded-xl border-opacity-25 bg-surface">
        <v-card-title class="d-flex align-center justify-space-between pa-6 border-b border-opacity-25">
          <span class="text-h6 font-weight-black">Start a New Discussion</span>
          <v-btn icon="mdi-close" variant="text" density="comfortable" @click="showNewPostDialog = false"></v-btn>
        </v-card-title>

        <v-card-text class="pa-6">
          <v-form ref="formRef" @submit.prevent="submitTopic">
            
            <div class="text-subtitle-2 font-weight-bold mb-2">Topic Title</div>
            <v-text-field
              v-model="newTopic.title"
              :rules="[rules.required]"
              placeholder="e.g., Anyone taking COMP 302 next semester?"
              variant="outlined"
              density="comfortable"
              class="mb-4"
            ></v-text-field>

            <div class="text-subtitle-2 font-weight-bold mb-2">Category</div>
            <v-select
              v-model="newTopic.category"
              :items="postableCategories"
              :rules="[rules.required]"
              placeholder="Select a category"
              variant="outlined"
              density="comfortable"
              class="mb-4"
            ></v-select>

            <div class="text-subtitle-2 font-weight-bold mb-2">Details</div>
            <v-textarea
              v-model="newTopic.excerpt"
              :rules="[rules.required, rules.minLength]"
              placeholder="Share more details, ask your question, or explain your thoughts..."
              variant="outlined"
              auto-grow
              rows="4"
              counter
              class="mb-2"
            ></v-textarea>
          </v-form>
        </v-card-text>

        <v-card-actions class="pa-6 pt-0 d-flex justify-end gap-3">
          <v-btn 
            variant="text" 
            class="text-none font-weight-bold rounded-lg" 
            @click="showNewPostDialog = false"
            :disabled="isSubmitting"
          >
            Cancel
          </v-btn>
          <v-btn 
            color="primary" 
            variant="flat" 
            class="text-none font-weight-bold px-6 rounded-lg" 
            @click="submitTopic"
            :loading="isSubmitting"
          >
            Post Topic
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>

<style scoped>
/* Utility class to keep spacing consistent */
.gap-4 { gap: 16px; }
.gap-2 { gap: 8px; }

/* Truncates text with an ellipsis after 2 lines so long posts don't break the UI */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

/* Nice hover effect for thread titles. 
  By using the CSS variable --v-theme-primary, this naturally works in dark mode! 
*/
.hover-text-primary:hover {
  color: rgb(var(--v-theme-primary)) !important; 
  text-decoration: underline;
}
</style>