<script setup lang="ts">
import { ref } from 'vue';
import router from '@/router';

// --- State ---
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');
const imagePreview = ref<string | null>(null);

// --- Form Data ---
// In a real scenario, if editing, you would fetch the product from Laravel and populate this.
const productForm = ref({
  id: null,
  name: '',
  price: '',
  category: null,
  condition: null,
  description: '',
  image: null as File[] | null, // Vuetify v-file-input uses an array of files
});

// --- Options ---
const categoryOptions = ['Clothing', 'Electronics', 'Academic Material', 'Services', 'Dorm Gear', 'Food & Snacks', 'Other'];
const conditionOptions = ['Brand New', 'Like New', 'Good', 'Fair'];

// --- Methods ---
const handleImageUpload = (fileArray: File[] | undefined) => {
  if (fileArray && fileArray.length > 0) {
    const file = fileArray[0];
    // Create a temporary URL to show the user a preview of what they just selected
    imagePreview.value = URL.createObjectURL(file);
  } else {
    imagePreview.value = null;
  }
};

const triggerSnackbar = (message: string) => {
  snackbarText.value = message;
  showSnackbar.value = true;
};

const handleSaveProduct = async () => {
  isSubmitting.value = true;
  
  try {
    // TODO: Send FormData to Laravel backend here
    // Example: await saveProduct(productForm.value);
    
    setTimeout(() => {
      triggerSnackbar('Product saved successfully!');
      isSubmitting.value = false;
      // router.push('/seller/dashboard'); // Redirect after saving
    }, 1000); // Simulating network delay

  } catch (error) {
    console.error("Failed to save product:", error);
    triggerSnackbar('Failed to save product. Please try again.');
    isSubmitting.value = false;
  }
};

const cancelEdit = () => {
  // Clear the form or go back to the previous page
  console.log("Edit cancelled");
  router.push('/profile');
};
</script>

<template>
  <v-container fluid class="pa-0 pa-md-6 bg-grey-lighten-4 min-vh-100 d-flex justify-center">
    <v-card class="rounded-xl border-opacity-25 w-100 mt-md-4 mb-md-8" style="max-width: 800px;" border elevation="3" bg-color="white">
      
      <div class="pa-6 pa-md-8 border-b border-opacity-25 bg-grey-lighten-5">
        <h1 class="text-h4 font-weight-black text-indigo-darken-4 mb-1">
          {{ productForm.id ? 'Edit Product' : 'Add New Product' }}
        </h1>
        <p class="text-body-2 text-medium-emphasis mb-0">List your item for the campus community to see.</p>
      </div>

      <v-form @submit.prevent="handleSaveProduct" class="pa-6 pa-md-8">
        
        <div class="mb-8">
          <label class="text-caption font-weight-black text-uppercase text-medium-emphasis tracking-widest mb-3 d-block">Product Image</label>
          <div class="d-flex flex-column flex-sm-row align-start align-sm-center gap-4">
            
            <v-avatar rounded="lg" size="120" color="grey-lighten-3" border class="elevation-1 border-dashed">
              <v-img v-if="imagePreview" :src="imagePreview" cover></v-img>
              <v-icon v-else icon="mdi-camera-plus-outline" size="40" color="grey-darken-1"></v-icon>
            </v-avatar>
            
            <div class="flex-grow-1 w-100">
              <v-file-input
                v-model="productForm.image"
                accept="image/png, image/jpeg, image/webp"
                label="Upload a clear photo"
                variant="outlined"
                density="compact"
                prepend-icon=""
                prepend-inner-icon="mdi-image"
                hide-details
                class="rounded-lg mb-2"
                @update:model-value="handleImageUpload"
              ></v-file-input>
              <div class="text-caption text-medium-emphasis">Recommended size: 800x800px. Max size: 2MB.</div>
            </div>
          </div>
        </div>

        <v-divider class="mb-8 border-opacity-25"></v-divider>

        <label class="text-caption font-weight-black text-uppercase text-medium-emphasis tracking-widest mb-3 d-block">Product Details</label>
        <v-row>
          <v-col cols="12" md="8" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Item Name <span class="text-error">*</span></label>
            <v-text-field 
              v-model="productForm.name" 
              placeholder="e.g., Introduction to Calculus Textbook"
              variant="outlined" 
              class="rounded-lg mb-4"
              required
            ></v-text-field>
          </v-col>
          
          <v-col cols="12" md="4" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Price <span class="text-error">*</span></label>
            <v-text-field 
              v-model="productForm.price" 
              placeholder="0"
              prefix="KES"
              type="number"
              min="0"
              :rules="[v => v >= 0 || 'Price cannot be negative']"
              @keydown="(e) => { if (e.key === '-' || e.key === 'e') e.preventDefault(); }"
              variant="outlined" 
              class="rounded-lg font-weight-bold mb-4"
              required
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="6" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Category <span class="text-error">*</span></label>
            <v-select 
              v-model="productForm.category" 
              :items="categoryOptions" 
              placeholder="Select a category"
              variant="outlined" 
              class="rounded-lg mb-4"
              required
            ></v-select>
          </v-col>

          <v-col cols="12" md="6" class="py-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Condition</label>
            <v-select 
              v-model="productForm.condition" 
              :items="conditionOptions" 
              placeholder="How used is it?"
              variant="outlined" 
              class="rounded-lg mb-4"
            ></v-select>
          </v-col>

          <v-col cols="12" class="pt-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Description</label>
            <v-textarea 
              v-model="productForm.description" 
              placeholder="Describe the item, any wear and tear, and your availability to meet up..."
              variant="outlined" 
              rows="4" 
              auto-grow 
              class="rounded-lg"
            ></v-textarea>
          </v-col>
        </v-row>
        
        <div class="d-flex flex-column flex-sm-row justify-end gap-3 mt-6 pt-6 border-t border-opacity-25">
          <v-btn 
            variant="text" 
            color="grey-darken-2" 
            class="text-none font-weight-bold rounded-lg px-6" 
            size="large"
            @click="cancelEdit"
          >
            Cancel
          </v-btn>
          <v-btn 
            type="submit" 
            color="primary" 
            variant="flat" 
            class="text-none font-weight-bold px-8 rounded-lg"
            size="large"
            :loading="isSubmitting"
          >
            Save Product
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
/* Optional: Adds a nice gap utility class if your Vuetify version doesn't have it natively */
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
</style>