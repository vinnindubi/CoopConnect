<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useGroups } from '@/composables/useGroups'; // 1. Import the composable

// 2. Destructure the API functions
const { fetchAllGroups, toggleMembership: apiToggleMembership } = useGroups();

// --- State ---
const currentSection = ref('clubs'); 
const searchQuery = ref('');
const isLoading = ref(true); // Add a loading state
const showSnackbar = ref(false);
const snackbarText = ref('');

// 3. Start with empty arrays!
const clubs = ref<any[]>([]);
const societies = ref<any[]>([]);

// --- Computed ---
const activeList = computed(() => {
  return currentSection.value === 'clubs' ? clubs.value : societies.value;
});

const filteredItems = computed(() => {
  if (!searchQuery.value) return activeList.value;
  
  const query = searchQuery.value.toLowerCase();
  return activeList.value.filter(item => 
    item.name.toLowerCase().includes(query) || 
    item.description.toLowerCase().includes(query)
  );
});

// --- Methods ---
const handleToggleMembership = async (item: any) => {
  try {
    const response = await apiToggleMembership(item.id);
    
    // DEBUG: This will print exactly what Axios is returning
    console.log("Raw API Response:", response);

    // THE FIX: Safely grab the data whether Axios unpacks it or not!
    const payload = response.data ? response.data : response;

    // Update the UI
    item.currentUserRole = payload.is_member ? 'Member' : null;
    snackbarText.value = payload.message;
    showSnackbar.value = true;

  } catch (error) {
    // THE SMOKING GUN: This will print the exact JavaScript error breaking your code
    console.error("EXACT JAVASCRIPT ERROR:", error);
    
    snackbarText.value = "Failed to update membership. Please try again.";
    showSnackbar.value = true;
  }
};

// 4. Fetch the real data when the page loads!
onMounted(async () => {
  try {
    const allGroups = await fetchAllGroups();
    
    // Split the data into clubs and societies based on the database column 'type'
    clubs.value = allGroups.filter((g: any) => g.type === 'club');
    societies.value = allGroups.filter((g: any) => g.type === 'society');
    
  } catch (error) {
    snackbarText.value = 'Failed to load groups. Check your connection.';
    showSnackbar.value = true;
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <v-container fluid class="bg-background min-vh-100 py-6 py-md-10">
    <v-container style="max-width: 1200px;">
      
      <v-overlay :model-value="isLoading" class="align-center justify-center" persistent>
        <v-progress-circular color="primary" indeterminate size="64"></v-progress-circular>
      </v-overlay>

      <div class="mb-8 text-center">
        <h1 class="text-h3 font-weight-black text-primary mb-2">Campus Life</h1>
        <p class="text-body-1 text-medium-emphasis">Discover your community and explore your passions.</p>
      </div>

      <div class="d-flex justify-center mb-8">
        <v-btn-toggle
          v-model="currentSection"
          color="primary"
          class="rounded-pill border border-opacity-25 bg-surface elevation-2"
          mandatory
        >
          <v-btn value="clubs" class="px-6 px-sm-8 font-weight-bold text-none" size="large">
            <v-icon start icon="mdi-palette-swatch-outline"></v-icon> Campus Clubs
          </v-btn>
          <v-btn value="societies" class="px-6 px-sm-8 font-weight-bold text-none" size="large">
            <v-icon start icon="mdi-hands-pray"></v-icon> Religious Societies
          </v-btn>
        </v-btn-toggle>
      </div>

      <v-text-field
        v-model="searchQuery"
        :placeholder="currentSection === 'clubs' ? 'Search for clubs...' : 'Search for societies...'"
        prepend-inner-icon="mdi-magnify"
        variant="solo-filled"
        flat
        density="comfortable"
        hide-details
        class="rounded-xl mb-8 bg-surface elevation-1"
      ></v-text-field>

      <v-row v-if="filteredItems.length > 0 && !isLoading">
        <v-col cols="12" sm="6" md="4" v-for="item in filteredItems" :key="item.id">
          <v-card @click="$router.push('/clubsAndSocieties/' + item.id)" class="rounded-xl border-opacity-25 bg-surface h-100 d-flex flex-column transition-swing" border elevation="1" hover>
            
            <v-img :src="item.image" height="160" cover class="bg-surface-variant position-relative">
              <v-chip size="small" color="white" variant="flat" class="position-absolute font-weight-bold text-grey-darken-4 elevation-2" style="top: 12px; right: 12px;">
                <v-icon start icon="mdi-account-group" size="14"></v-icon> 
                {{ item.membersCount }}
              </v-chip>
            </v-img>

            <div class="pa-5 d-flex flex-column flex-grow-1">
              <div v-if="item.category" class="mb-2">
                <v-chip size="x-small" color="primary" variant="tonal" class="font-weight-bold text-uppercase">
                  {{ item.category }}
                </v-chip>
              </div>

              <h3 class="text-h6 font-weight-bold text-high-emphasis mb-2 leading-tight">
                {{ item.name }}
              </h3>
              <p class="text-body-2 text-medium-emphasis mb-4 line-clamp-3">
                {{ item.description }}
              </p>

              <v-spacer></v-spacer>

              <div class="mt-auto pt-4 border-t border-opacity-25 d-flex align-center justify-space-between">
                
                <div class="d-flex align-center text-caption text-medium-emphasis font-weight-medium" title="Meeting Time">
                  <v-avatar color="primary" variant="tonal" size="28" class="mr-2">
                    <v-icon icon="mdi-calendar-clock" size="14"></v-icon>
                  </v-avatar>
                  <span class="text-truncate" style="max-width: 130px;">{{ item.meeting }}</span>
                </div>

                <v-btn 
                  :color="item.currentUserRole ? 'success' : 'primary'" 
                  :variant="item.currentUserRole ? 'tonal' : 'elevated'"
                  elevation="1"
                  size="small"
                  class="text-none font-weight-bold rounded-pill px-4 shrink-0 transition-swing"
                  @click.stop="handleToggleMembership(item)" 
                >
                  <v-icon start size="16" :icon="item.currentUserRole ? 'mdi-check-circle' : 'mdi-plus-circle-outline'"></v-icon>
                  {{ item.currentUserRole ? 'Joined' : 'Join' }}
                </v-btn>
                
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <v-card v-else-if="!isLoading" class="rounded-xl border border-dashed pa-10 text-center bg-transparent mt-6 mx-auto" elevation="0" style="max-width: 600px;">
        <v-avatar color="surface-variant" size="64" class="mb-4">
          <v-icon icon="mdi-magnify-close" size="32" color="medium-emphasis"></v-icon>
        </v-avatar>
        <h3 class="text-h6 font-weight-bold text-high-emphasis">No {{ currentSection }} found</h3>
        <p class="text-medium-emphasis">Try a different search term or check your database.</p>
      </v-card>

    </v-container>
    

    <v-snackbar v-model="showSnackbar" :timeout="3000" color="success" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon icon="mdi-information-outline" class="mr-3"></v-icon>
        {{ snackbarText }}
      </div>
    </v-snackbar>
  </v-container>
</template>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.leading-tight {
  line-height: 1.2 !important;
}
</style>