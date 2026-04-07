<script setup lang="ts">
import { ref, computed } from 'vue';

// --- State ---
// The Master Switch: Separates Clubs and Societies fundamentally
const currentSection = ref('clubs'); 
const searchQuery = ref('');

// --- Mock Data: Clubs (Secular, Interest, Academic, Welfare) ---
const clubs = ref([
  { id: 1, name: 'Journalism Club', category: 'Media', members: 120, meeting: 'Fridays 4 PM', image: 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&q=80&w=400', description: 'Run the campus newsletter, cover events, and learn photography and reporting.', isMember: false },
  { id: 2, name: 'Wildlife & Environment Club', category: 'Environment', members: 85, meeting: 'Wednesdays 5 PM', image: 'https://images.unsplash.com/photo-1564314968303-86c5df2b9a4c?auto=format&fit=crop&q=80&w=400', description: 'Focused on conservation, tree planting drives, and organizing hikes and park visits.', isMember: true },
  { id: 3, name: 'AYLF (Africa Youth Leadership Forum)', category: 'Leadership', members: 210, meeting: 'Thursdays 5 PM', image: 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&q=80&w=400', description: 'Mentorship, leadership training, and networking for the next generation of African leaders.', isMember: false },
  { id: 4, name: 'Google Developer Student Club', category: 'Tech', members: 315, meeting: 'Saturdays 10 AM', image: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400', description: 'Learn coding, build apps, and participate in global hackathons.', isMember: true },
]);

// --- Mock Data: Societies (Religious & Spiritual) ---
const societies = ref([
  { id: 101, name: 'Christian Union (CU)', members: 850, meeting: 'Wednesdays 5 PM & Sundays', image: 'https://images.unsplash.com/photo-1543589077-47d81606c1bf?auto=format&fit=crop&q=80&w=400', description: 'A fellowship of students dedicated to growing in faith, prayer, and community service.', isMember: false },
  { id: 102, name: 'Catholic Students Association (CSA)', members: 620, meeting: 'Sundays 9 AM @ Chapel', image: 'https://images.unsplash.com/photo-1438283173091-5dbf5c5a3206?auto=format&fit=crop&q=80&w=400', description: 'Uniting Catholic students through mass, charity events, and spiritual retreats.', isMember: false },
  { id: 103, name: 'SDA Students Association', members: 430, meeting: 'Saturdays 9 AM @ Main Hall', image: 'https://images.unsplash.com/photo-1504052434569-70ad5836ab65?auto=format&fit=crop&q=80&w=400', description: 'Observing the Sabbath through worship, Bible study, and uplifting music ministries.', isMember: true },
  { id: 104, name: 'Muslim Students Association (MSA)', members: 390, meeting: 'Fridays 1 PM @ Campus Mosque', image: 'https://images.unsplash.com/photo-1564767609342-620cb19b2357?auto=format&fit=crop&q=80&w=400', description: 'Providing a supportive environment for Muslim students, facilitating Jumu\'ah prayers and Iftars.', isMember: false },
]);

// --- Computed ---
// Dynamically grabs the right list based on the Master Switch
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
const toggleMembership = (id: number) => {
  let group = clubs.value.find(c => c.id === id);
  if (!group) {
    group = societies.value.find(s => s.id === id);
  }
  if (group) {
    group.isMember = !group.isMember;
  }
};
</script>

<template>
  <v-container fluid class="bg-background min-vh-100 py-6 py-md-10">
    <v-container style="max-width: 1200px;">
      
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
        :placeholder="currentSection === 'clubs' ? 'Search for Journalism, Wildlife...' : 'Search for CU, SDA, Catholic...'"
        prepend-inner-icon="mdi-magnify"
        variant="solo-filled"
        flat
        density="comfortable"
        hide-details
        class="rounded-xl mb-8 bg-surface elevation-1"
      ></v-text-field>

      <v-row v-if="filteredItems.length > 0">
        <v-col cols="12" sm="6" md="4" v-for="item in filteredItems" :key="item.id">
          <v-card @click="$router.push('/clubsAndSocieties/' + item.id)"  class="rounded-xl border-opacity-25 bg-surface h-100 d-flex flex-column transition-swing" border elevation="1" hover>
            
            <v-img :src="item.image" height="160" cover class="bg-surface-variant position-relative">
              <v-chip size="small" color="white" variant="flat" class="position-absolute font-weight-bold text-grey-darken-4 elevation-2" style="top: 12px; right: 12px;">
                <v-icon start icon="mdi-account-group" size="14"></v-icon> {{ item.members }}
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
                  :color="item.isMember ? 'success' : 'primary'" 
                  :variant="item.isMember ? 'tonal' : 'elevated'"
                  elevation="1"
                  size="small"
                  class="text-none font-weight-bold rounded-pill px-4 shrink-0 transition-swing"
                  @click="toggleMembership(item.id)"
                >
                  <v-icon start size="16" :icon="item.isMember ? 'mdi-check-circle' : 'mdi-plus-circle-outline'"></v-icon>
                  {{ item.isMember ? 'Joined' : 'Join' }}
                </v-btn>
                
              </div>
            </div>
          </v-card>
        </v-col>
      </v-row>

      <v-card v-else class="rounded-xl border border-dashed pa-10 text-center bg-transparent mt-6 mx-auto" elevation="0" style="max-width: 600px;">
        <v-avatar color="surface-variant" size="64" class="mb-4">
          <v-icon icon="mdi-magnify-close" size="32" color="medium-emphasis"></v-icon>
        </v-avatar>
        <h3 class="text-h6 font-weight-bold text-high-emphasis">No {{ currentSection }} found</h3>
        <p class="text-medium-emphasis">Try a different search term.</p>
      </v-card>

    </v-container>
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