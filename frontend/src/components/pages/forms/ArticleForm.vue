<script setup lang="ts">
import { ref } from 'vue';

// --- State ---
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');
const imagePreview = ref<string | null>(null);

// --- Form Data ---
const articleForm = ref({
  id: null,
  title: '',
  excerpt: '', // The short teaser text
  content: '', // The main article body
  tags: [] as string[],
  coverImage: null as File[] | null,
});

// --- Options ---
const tagOptions = ['Campus Life', 'Study Tips', 'Housing', 'Career & Internships', 'Events', 'Food & Dining'];

// --- Methods ---
const handleImageUpload = (fileArray: File[] | undefined) => {
  if (fileArray && fileArray.length > 0) {
    const file = fileArray[0];
    imagePreview.value = URL.createObjectURL(file);
  } else {
    imagePreview.value = null;
  }
};

const triggerSnackbar = (message: string) => {
  snackbarText.value = message;
  showSnackbar.value = true;
};

const handleSaveArticle = async () => {
  isSubmitting.value = true;
  
  try {
    // TODO: Send FormData to Laravel backend
    
    setTimeout(() => {
      triggerSnackbar('Article published successfully!');
      isSubmitting.value = false;
    }, 1000);

  } catch (error) {
    console.error("Failed to publish article:", error);
    triggerSnackbar('Failed to publish. Please try again.');
    isSubmitting.value = false;
  }
};

const cancelEdit = () => {
  console.log("Article edit cancelled");
};
</script>

<template>
  <v-container fluid class="pa-0 pa-md-6 bg-grey-lighten-4 min-vh-100 d-flex justify-center">
    <v-card class="rounded-xl border-opacity-25 w-100 mt-md-4 mb-md-8" style="max-width: 900px;" border elevation="3" bg-color="white">
      
      <div class="pa-6 pa-md-8 border-b border-opacity-25 bg-grey-lighten-5">
        <h1 class="text-h4 font-weight-black text-indigo-darken-4 mb-1">
          {{ articleForm.id ? 'Edit Article' : 'Draft a New Article' }}
        </h1>
        <p class="text-body-2 text-medium-emphasis mb-0">Share your campus experiences, tips, and stories.</p>
      </div>

      <v-form @submit.prevent="handleSaveArticle" class="pa-6 pa-md-8">
        
        <div class="mb-8">
          <label class="text-caption font-weight-black text-uppercase text-medium-emphasis tracking-widest mb-3 d-block">Cover Photo</label>
          
          <v-card 
            variant="outlined" 
            class="rounded-xl border-dashed border-opacity-50 bg-grey-lighten-5 overflow-hidden mb-3 d-flex align-center justify-center position-relative" 
            height="250"
          >
            <v-img v-if="imagePreview" :src="imagePreview" cover class="w-100 h-100"></v-img>
            
            <div v-else class="text-center pa-6">
              <v-avatar color="indigo-lighten-5" size="64" class="mb-3">
                <v-icon icon="mdi-image-plus" size="32" color="indigo-darken-2"></v-icon>
              </v-avatar>
              <div class="text-subtitle-1 font-weight-bold text-grey-darken-3">Upload a Cover Photo</div>
              <div class="text-caption text-medium-emphasis">16:9 ratio recommended (e.g., 1200x675px)</div>
            </div>

            <v-file-input
              v-model="articleForm.coverImage"
              accept="image/png, image/jpeg, image/webp"
              class="position-absolute w-100 h-100 opacity-0 cursor-pointer"
              style="top: 0; left: 0; z-index: 10;"
              prepend-icon=""
              hide-details
              @update:model-value="handleImageUpload"
            ></v-file-input>
          </v-card>
        </div>

        <v-divider class="mb-8 border-opacity-25"></v-divider>

        <v-row>
          <v-col cols="12" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Article Title <span class="text-error">*</span></label>
            <v-text-field 
              v-model="articleForm.title" 
              placeholder="Catchy, interesting title..."
              variant="outlined" 
              class="rounded-lg font-weight-bold text-h6 mb-2"
              required
            ></v-text-field>
          </v-col>
          
          <v-col cols="12" md="12" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Excerpt (Short Summary) <span class="text-error">*</span></label>
            <v-textarea 
              v-model="articleForm.excerpt" 
              placeholder="Give readers a 1-2 sentence summary of what this article is about."
              variant="outlined" 
              rows="2" 
              auto-grow 
              counter="150"
              class="rounded-lg mb-2"
              required
            ></v-textarea>
          </v-col>

          <v-col cols="12" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Tags</label>
            <v-combobox 
              v-model="articleForm.tags" 
              :items="tagOptions" 
              placeholder="Select or type your own tags..."
              multiple 
              chips 
              closable-chips
              variant="outlined" 
              class="rounded-lg mb-4"
            ></v-combobox>
          </v-col>
        </v-row>

        <v-divider class="mb-6 mt-2 border-opacity-25"></v-divider>

        <label class="text-caption font-weight-black text-uppercase text-medium-emphasis tracking-widest mb-3 d-block">Article Body</label>
        
        <v-textarea 
          v-model="articleForm.content" 
          placeholder="Start writing your story here..."
          variant="outlined" 
          rows="15" 
          auto-grow
          bg-color="grey-lighten-5"
          class="rounded-lg mb-4 text-body-1"
          required
        ></v-textarea>
        
        <div class="d-flex flex-column flex-sm-row justify-end gap-3 mt-8 pt-6 border-t border-opacity-25">
          <v-btn 
            variant="text" 
            color="grey-darken-2" 
            class="text-none font-weight-bold rounded-lg px-6" 
            size="large"
            @click="cancelEdit"
          >
            Save as Draft
          </v-btn>
          <v-btn 
            type="submit" 
            color="indigo-darken-4" 
            variant="flat" 
            class="text-none font-weight-bold px-8 rounded-lg"
            size="large"
            :loading="isSubmitting"
          >
            Publish Article
          </v-btn>
        </div>
      </v-form>
    </v-card>

    <v-snackbar v-model="showSnackbar" :timeout="3000" color="indigo-darken-4" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon icon="mdi-check-circle" color="success" class="mr-3"></v-icon>
        {{ snackbarText }}
      </div>
    </v-snackbar>
  </v-container>
</template>

<style scoped>
/* Allows the gap utility class for spacing out the bottom buttons */
.gap-3 { gap: 12px; }

/* Makes the text input look a bit larger to feel like a real article title */
:deep(.v-field__input) {
  line-height: 1.5;
}
</style>