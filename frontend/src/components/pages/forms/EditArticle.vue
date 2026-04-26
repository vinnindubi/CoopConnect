<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getArticle, updateArticle } from '@/services/authService';

const route = useRoute();
const router = useRouter();

// The ID from the URL (e.g., /editArticle/2)
const articleId = route.params.id; 
const categories = ['Student Life', 'Academics', 'Tech', 'Housing', 'Attachment', 'Other'];

const isLoading = ref(true);
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');

// The form state
const article = ref({
  title: '',
  category: '',
  image:'',
  excerpt: '',
  content: '' // Assuming your articles have a main body!
});

const rules = {
  required: (v: string) => !!v || 'This field is required',
};

// 1. Fetch the existing article to pre-fill the form
const fetchArticleData = async () => {
  try {
    const response = await getArticle(articleId);
    // Bind the Laravel data to our Vue form
    article.value = response.data.data; 
  } catch (error) {
    console.error('Failed to load article:', error);
    snackbarText.value = 'Could not load article. It may have been deleted.';
    showSnackbar.value = true;
  } finally {
    isLoading.value = false;
  }
};

// 2. Save the changes
const saveChanges = async () => {
  if (!article.value.title || !article.value.excerpt) return; // Basic validation

  isSubmitting.value = true;
  try {
    await updateArticle(articleId, article.value);
    
    // Redirect back to the profile page on success
    router.push('/profile');
  } catch (error) {
    console.error('Failed to update article:', error);
    snackbarText.value = 'Failed to save changes. Please try again.';
    showSnackbar.value = true;
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  fetchArticleData();
});
</script>

<template>
  <v-container class="bg-background min-vh-100 py-8" fluid>
    <v-container style="max-width: 800px;">
      
      <div class="d-flex align-center mb-6">
        <v-btn icon="mdi-arrow-left" variant="text" @click="router.push('/profile')" class="mr-4"></v-btn>
        <h1 class="text-h4 font-weight-black text-primary">Edit Article</h1>
      </div>

      <v-card v-if="isLoading" class="rounded-xl border-opacity-25 pa-10 text-center bg-surface" border elevation="1">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
      </v-card>

      <v-card v-else class="rounded-xl border-opacity-25 pa-6 pa-md-8 bg-surface" border elevation="1">
        <v-form @submit.prevent="saveChanges">
          
          <v-row>
            <v-col cols="12" md="8">
              <div class="text-subtitle-2 font-weight-bold mb-2">Article Title</div>
              <v-text-field
                v-model="article.title"
                :rules="[rules.required]"
                variant="outlined"
                density="comfortable"
                class="mb-4"
              ></v-text-field>
            </v-col>
            
            <v-col cols="12" md="4">
              <div class="text-subtitle-2 font-weight-bold mb-2">Category</div>
              <v-select
                v-model="article.category"
                :items="categories"
                :rules="[rules.required]"
                variant="outlined"
                density="comfortable"
                class="mb-4"
              ></v-select>
            </v-col>
          </v-row>

          <div class="text-subtitle-2 font-weight-bold mb-2">Cover Photo (Image URL)</div>
          <v-text-field
            v-model="article.image"
            variant="outlined"
            density="comfortable"
            placeholder="https://images.unsplash.com/..."
            class="mb-4"
          ></v-text-field>

          <div class="text-subtitle-2 font-weight-bold mb-2">Short Excerpt (Summary)</div>
          <v-textarea
            v-model="article.excerpt"
            :rules="[rules.required]"
            variant="outlined"
            auto-grow
            rows="2"
            class="mb-4"
          ></v-textarea>

          <div class="text-subtitle-2 font-weight-bold mb-2">Main Content</div>
          <v-textarea
            v-model="article.content"
            :rules="[rules.required]"
            variant="outlined"
            auto-grow
            rows="8"
            placeholder="Write your full story here..."
            class="mb-6"
          ></v-textarea>

          <v-divider class="mb-6 opacity-20"></v-divider>

          <div class="d-flex justify-end gap-4">
            <v-btn variant="text" class="text-none font-weight-bold" @click="router.push('/profile')">
              Cancel
            </v-btn>
            <v-btn 
              type="submit" 
              color="primary" 
              variant="flat" 
              class="text-none font-weight-bold px-8 rounded-lg"
              :loading="isSubmitting"
            >
              Save Changes
            </v-btn>
          </div>

        </v-form>
      </v-card>

    </v-container>

    <v-snackbar v-model="showSnackbar" :timeout="3000" color="error" rounded="pill">
      <span class="font-weight-bold">{{ snackbarText }}</span>
    </v-snackbar>
  </v-container>
</template>

<style scoped>
.gap-4 { gap: 16px; }
</style>