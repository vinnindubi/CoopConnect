<script setup lang="ts">
// Adjust this import path depending on where you saved the composable
import { useForum } from '@/composables/useForum'; 

// Destructure exactly what the template needs from our logic file
const {
  currentUser,
  threads,
  trendingTags,
  isLoading,
  searchQuery,
  selectedCategory,
  categories,
  postableCategories,
  showNewPostDialog,
  isSubmitting,
  formRef,
  newTopic,
  rules,
  submitTopic,
  submitReply,
  upvoteThread,
  deleteThread
} = useForum();
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

          <div v-if="isLoading" class="d-flex justify-center pa-10">
            <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
          </div>

          <div v-else-if="threads.length > 0" class="d-flex flex-column gap-4">
            <v-card 
              v-for="thread in threads" 
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

                  <v-btn 
                    v-if="currentUser && thread.author.id === currentUser.id"
                    variant="text" 
                    color="error" 
                    size="small" 
                    class="rounded-pill font-weight-bold px-4 text-none"
                    @click.stop="deleteThread(thread.id)"
                  >
                    <v-icon start icon="mdi-delete-outline" size="18"></v-icon> Delete
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
                <v-list-item link class="px-4" v-for="tag in trendingTags" :key="tag.tag">
                  <div class="d-flex align-center justify-space-between w-100">
                    <span class="font-weight-bold text-body-2 text-high-emphasis">{{ tag.tag }}</span>
                    <span class="text-caption text-medium-emphasis">{{ tag.count }}</span>
                  </div>
                </v-list-item>

                <div v-if="trendingTags.length === 0" class="pa-4 text-caption text-center text-medium-emphasis">
                  No trending topics yet.
                </div>
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

            <div class="d-flex align-center justify-space-between mb-2">
               <div class="text-subtitle-2 font-weight-bold">Details</div>
               <div class="text-caption text-primary font-weight-medium">Include #hashtags to trend!</div>
            </div>
            <v-textarea
              v-model="newTopic.excerpt"
              :rules="[rules.required, rules.minLength]"
              placeholder="Share details, ask a question... (e.g., The WiFi in Block C is down #CampusLife)"
              variant="outlined"
              auto-grow
              rows="4"
              counter
              class="mb-2"
            ></v-textarea>

            <v-card class="bg-background mt-2 pa-3 rounded-lg border-opacity-25" border elevation="0">
              <v-switch
                v-model="newTopic.isAnonymous"
                color="primary"
                hide-details
                density="compact"
                class="mt-0"
              >
                <template v-slot:label>
                  <span class="text-body-2 font-weight-medium text-high-emphasis ml-2">Post Anonymously</span>
                </template>
              </v-switch>
              <p class="text-caption text-medium-emphasis ml-12 mt-n1">Your name and profile picture will be hidden from other students.</p>
            </v-card>

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
/* Utility classes */
.gap-4 { gap: 16px; }
.gap-2 { gap: 8px; }

/* Truncates long text gracefully */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

/* Beautiful hover state that adapts to your theme colors automatically */
.hover-text-primary:hover {
  color: rgb(var(--v-theme-primary)) !important; 
  text-decoration: underline;
}
</style>