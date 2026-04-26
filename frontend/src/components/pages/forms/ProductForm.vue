<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
// Import your actual API services
import { createProduct, getProduct, updateProduct } from '@/services/authService';

const route = useRoute();
const router = useRouter();

// Check if we are editing an existing product (e.g., /editProduct/5)
const productId = route.params.id; 

// --- State ---
const isLoading = ref(false);
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');

// --- Form Data ---
const productForm = ref({
  id: null as number | string | null,
  title: '',     // Changed from 'name' to 'title' to match Laravel database!
  price: '',
  category: null as string | null,
  condition: null as string | null,
  description: '',
  image: '',     // Changed to a standard string for the URL
});

// --- Options ---
const categoryOptions = ['Electronics', 
                'Fashion', 
                'Academic', 
                'Services'];
const conditionOptions = ['new','good','fair','poor'];

// --- Methods ---
const triggerSnackbar = (message: string) => {
  snackbarText.value = message;
  showSnackbar.value = true;
};

// 1. Fetch Product (If Editing)
const fetchProductData = async () => {
  if (!productId) return; // If there's no ID in the URL, we are adding a new product, so skip fetching!

  isLoading.value = true;
  try {
    const response = await getProduct(productId);
    const data = response.data.data;
    
    // Populate the form with the database data
    productForm.value = {
      id: data.id,
      title: data.title,
      price: data.price,
      category: data.category,
      condition: data.condition || null, 
      description: data.description,
      image: data.image || '',
    };
  } catch (error) {
    console.error('Failed to load product:', error);
    triggerSnackbar('Could not load product. It may have been deleted.');
  } finally {
    isLoading.value = false;
  }
};

// 2. Save Product (Handles both Create and Update)
const handleSaveProduct = async () => {
  isSubmitting.value = true;
  
  try {
    // The payload exactly matches what Laravel expects
    const payload = {
      title: productForm.value.title,
      price: productForm.value.price,
      category: productForm.value.category,
      description: productForm.value.description,
      image: productForm.value.image,
      // Note: If you want to save 'condition' to the DB, remember to add it to your Laravel migration and $fillable array!
    };

    if (productForm.value.id) {
      // If we have an ID, UPDATE it
      await updateProduct(productForm.value.id, payload);
      triggerSnackbar('Product updated successfully!');
    } else {
      // If we don't have an ID, CREATE it
      await createProduct(payload);
      triggerSnackbar('Product added successfully!');
    }
    
    // Redirect back to profile after a short delay so they see the success message
    setTimeout(() => {
      router.push('/profile');
    }, 1000);

  } catch (error) {
    console.error("Failed to save product:", error);
    triggerSnackbar('Failed to save product. Please try again.');
  } finally {
    isSubmitting.value = false;
  }
};

const cancelEdit = () => {
  router.push('/profile');
};

// Run the fetch check as soon as the page loads
onMounted(() => {
  fetchProductData();
});
</script>

<template>
  <v-container fluid class="pa-0 pa-md-6 bg-grey-lighten-4 min-vh-100 d-flex justify-center">
    
    <v-card v-if="isLoading" class="rounded-xl border-opacity-25 w-100 mt-md-4 mb-md-8 pa-10 text-center" style="max-width: 800px;" border elevation="3">
       <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
    </v-card>

    <v-card v-else class="rounded-xl border-opacity-25 w-100 mt-md-4 mb-md-8" style="max-width: 800px;" border elevation="3" bg-color="white">
      
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
              <v-img v-if="productForm.image" :src="productForm.image" cover></v-img>
              <v-icon v-else icon="mdi-camera-plus-outline" size="40" color="grey-darken-1"></v-icon>
            </v-avatar>
            
            <div class="flex-grow-1 w-100">
              <v-text-field
                v-model="productForm.image"
                placeholder="https://images.unsplash.com/..."
                label="Image URL Link"
                variant="outlined"
                density="compact"
                prepend-inner-icon="mdi-link"
                hide-details
                class="rounded-lg mb-2"
              ></v-text-field>
              <div class="text-caption text-medium-emphasis">Paste a direct link to an image.</div>
            </div>
          </div>
        </div>

        <v-divider class="mb-8 border-opacity-25"></v-divider>

        <label class="text-caption font-weight-black text-uppercase text-medium-emphasis tracking-widest mb-3 d-block">Product Details</label>
        <v-row>
          <v-col cols="12" md="8" class="pb-0">
            <label class="text-caption font-weight-bold mb-1 d-block text-grey-darken-3">Item Name <span class="text-error">*</span></label>
            <v-text-field 
              v-model="productForm.title" 
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
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
</style>