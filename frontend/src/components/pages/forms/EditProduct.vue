<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { getProduct, updateProduct } from '@/services/authService';

const route = useRoute();
const router = useRouter();
const productId = route.params.id;

const isLoading = ref(true);
const isSubmitting = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');

const product = ref({
  title: '',
  price: '',
  category: '',
  description: '',
  image: '' 
});

const categories = ['Clothing', 'Electronics', 'Academic Material', 'Services', 'Dorm Gear', 'Food & Snacks', 'Other'];
const rules = { required: (v: string) => !!v || 'This field is required' };

const fetchProductData = async () => {
  try {
    const response = await getProduct(productId);
    product.value = response.data.data;
  } catch (error) {
    console.error('Failed to load product:', error);
    snackbarText.value = 'Could not load product. It may have been deleted.';
    showSnackbar.value = true;
  } finally {
    isLoading.value = false;
  }
};

const saveChanges = async () => {
  if (!product.value.title || !product.value.price) return;

  isSubmitting.value = true;
  try {
    await updateProduct(productId, product.value);
    router.push('/profile');
  } catch (error) {
    console.error('Failed to update product:', error);
    snackbarText.value = 'Failed to save changes. Please try again.';
    showSnackbar.value = true;
  } finally {
    isSubmitting.value = false;
  }
};

onMounted(() => {
  fetchProductData();
});
</script>

<template>
  <v-container class="bg-background min-vh-100 py-8" fluid>
    <v-container style="max-width: 800px;">
      
      <div class="d-flex align-center mb-6">
        <v-btn icon="mdi-arrow-left" variant="text" @click="router.push('/profile')" class="mr-4"></v-btn>
        <h1 class="text-h4 font-weight-black text-primary">Edit Product</h1>
      </div>

      <v-card v-if="isLoading" class="rounded-xl border-opacity-25 pa-10 text-center bg-surface" border elevation="1">
        <v-progress-circular indeterminate color="primary" size="64"></v-progress-circular>
      </v-card>

      <v-card v-else class="rounded-xl border-opacity-25 pa-6 pa-md-8 bg-surface" border elevation="1">
        <v-form @submit.prevent="saveChanges">
          
          <v-row>
            <v-col cols="12" md="8">
              <div class="text-subtitle-2 font-weight-bold mb-2">Item Name</div>
              <v-text-field
                v-model="product.title"
                :rules="[rules.required]"
                variant="outlined"
                density="comfortable"
                class="mb-4"
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <div class="text-subtitle-2 font-weight-bold mb-2">Price (KES)</div>
              <v-text-field
                v-model="product.price"
                :rules="[rules.required]"
                variant="outlined"
                density="comfortable"
                type="number"
                prefix="KES"
                class="mb-4"
              ></v-text-field>
            </v-col>
          </v-row>

          <div class="text-subtitle-2 font-weight-bold mb-2">Category</div>
          <v-select
            v-model="product.category"
            :items="categories"
            :rules="[rules.required]"
            variant="outlined"
            density="comfortable"
            class="mb-4"
          ></v-select>

          <div class="text-subtitle-2 font-weight-bold mb-2">Description</div>
          <v-textarea
            v-model="product.description"
            :rules="[rules.required]"
            variant="outlined"
            auto-grow
            rows="4"
            placeholder="Describe the condition, features, etc."
            class="mb-4"
          ></v-textarea>

          <div class="text-subtitle-2 font-weight-bold mb-2">Image URL</div>
          <v-text-field
            v-model="product.image"
            variant="outlined"
            density="comfortable"
            placeholder="https://..."
            class="mb-6"
          ></v-text-field>

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
              Save Product
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