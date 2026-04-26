<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { EVENT_CATEGORIES } from '@/utils/constants';
import { getGlobalEvents } from '@/services/groupService'; 
import {useRouter} from 'vue-router'

// 1. UI State
const activeTab = ref('discover'); // 'discover' or 'my-schedule'
const searchQuery = ref('');
const selectedCategory = ref('All');
const isLoading = ref(true);
const router= useRouter();

// Dynamically generate the category list from our Single Source of Truth
const categories = computed(() => ['All', ...EVENT_CATEGORIES]);

// 2. LIVE BACKEND DATA
const rawEvents = ref<any[]>([]);

// 3. REUSABLE FORMATTER (Adapts backend data to your UI needs)
const formatEvent = (event: any) => {
  // Use backend event_date (Fallback to date if testing with dummy data)
  const eventDate = new Date(event.event_date || event.date);
  
  // Format Date (e.g., "MAR", "20")
  const month = eventDate.toLocaleString('default', { month: 'short' }).toUpperCase();
  const day = eventDate.getDate().toString();
  
  // Format Time
  const time = eventDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });

  // Map backend fields to UI expectations with safe fallbacks
  const location = event.location || 'Campus (TBA)';
  const locLower = location.toLowerCase();
  const icon = (locLower.includes('meet') || locLower.includes('zoom') || locLower.includes('online')) 
    ? 'mdi-video-outline' 
    : 'mdi-map-marker-outline';

  const displayPrice = event.price ? `KES ${event.price}` : 'Free';

  return { 
    ...event, 
    // Standardize the fields
    category: event.event_type || event.category,
    organizer: event.group ? event.group.name : (event.organizer || 'Campus Connect'),
    image: event.group && event.group.image 
           ? event.group.image 
           : (event.image || 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1200'),
    // UI Helpers
    month, 
    day, 
    time, 
    location,
    icon, 
    displayPrice 
  };
};

// 4. FRONTEND COMPUTED LOGIC
// A. Banner Events (Only show events marked as 'featured' in the database)
const featuredEvents = computed(() => {
  if (rawEvents.value.length === 0) return [];
  // 1. Filter the array to ONLY include events where is_featured is true (or 1 in MySQL)
  const featured = rawEvents.value.filter(event => event.is_featured === true || event.is_featured === 1);
  // 2. Format them and limit to the top 3 so the carousel doesn't get overwhelmingly long
  return featured.slice(0, 3).map(formatEvent);
});

// B. Grid Events
const gridEvents = computed(() => {
  let result = rawEvents.value.map(formatEvent);

  // Filter by Category
  if (selectedCategory.value !== 'All') {
    result = result.filter(e => e.category === selectedCategory.value);
  }
  
  // Filter by Search (checks title and description)
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(e => 
      e.title.toLowerCase().includes(query) || 
      (e.description && e.description.toLowerCase().includes(query))
    );
  }

  return result;
});

// --- Event Dialog State ---
const showEventDialog = ref(false);
const selectedEvent = ref<any>(null);

const openEventDetails = (event: any) => {
  selectedEvent.value = event;
  showEventDialog.value = true;
};
// 5. FETCH DATA ON LOAD
onMounted(async () => {
  try {
    const response = await getGlobalEvents();
    rawEvents.value = response.data;
  } catch (error) {
    console.error("Failed to load events", error);
  } finally {
    isLoading.value = false;
  }
});
</script>

<template>
  <v-container fluid class="pa-6" style="max-width: 1200px;">
    
    <v-overlay :model-value="isLoading" class="align-center justify-center" persistent>
      <v-progress-circular color="primary" indeterminate size="64" width="6"></v-progress-circular>
    </v-overlay>

    <div v-if="!isLoading">
      <div class="mb-6">
        <h1 class="text-h4 font-weight-black text-high-emphasis">Campus Events</h1>
        <p class="text-body-1 text-medium-emphasis mt-1">Discover what's happening around you.</p>
      </div>

      <v-carousel 
        v-if="featuredEvents.length > 0 "
        cycle 
        height="400" 
        hide-delimiter-background 
        show-arrows="hover" 
        class="rounded-xl mb-6 elevation-4 overflow-hidden"
      >
        <v-carousel-item v-for="event in featuredEvents" :key="'featured-' + event.id" :src="event.image" cover>
          <div class="fill-height d-flex flex-column justify-end pa-6 pa-md-10 text-white" 
               style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 100%);">          
            <h2 class="text-h4 text-md-h3 font-weight-black mb-3 line-clamp-2" style="line-height: 1.1;">
              {{ event.title }}
            </h2>
            
            <div class="d-flex flex-wrap align-center gap-4 text-body-2 text-md-body-1 font-weight-medium mb-5 opacity-90">
              <div class="d-flex align-center gap-1 mr-1"><v-icon icon="mdi-calendar" size="20"></v-icon> {{ event.month }} {{ event.day }}</div>
              <div class="d-flex align-center gap-1 mr-1"><v-icon icon="mdi-clock-outline" size="20"></v-icon> {{ event.time }}</div>
              <div class="d-flex align-center gap-1 mr-1"><v-icon :icon="event.icon" size="20"></v-icon> {{ event.location }}</div>
            </div>
            
            <v-btn color="primary" size="large" class="text-none font-weight-bold align-self-start rounded-lg px-8" @click="openEventDetails(event)">
              View Details
            </v-btn>
          </div>
        </v-carousel-item>
      </v-carousel>

      <div class="d-flex flex-column flex-md-row justify-space-between align-md-center mb-6 gap-4">
        
        <v-tabs v-model="activeTab" color="primary">
          <v-tab value="discover" class="text-none font-weight-bold">Discover</v-tab>
          <!-- <v-tab value="my-schedule" class="text-none font-weight-bold">My Schedule</v-tab> -->
        </v-tabs>

        <div style="width: 100%; max-width: 350px;">
          <v-text-field
            v-model="searchQuery"
            variant="outlined"
            density="compact"
            hide-details
            placeholder="Search events..."
            prepend-inner-icon="mdi-magnify"
            bg-color="surface"
            class="rounded-lg"
          ></v-text-field>
        </div>
      </div>

      <v-chip-group 
        v-if="activeTab === 'discover'" 
        v-model="selectedCategory" 
        selected-class="bg-primary text-white border-primary" 
        mandatory 
        class="mb-6"
      >
        <v-chip v-for="cat in categories" :key="cat" :value="cat" variant="outlined" class="font-weight-bold border-opacity-50">
          {{ cat }}
        </v-chip>
      </v-chip-group>

      <v-row v-if="activeTab === 'discover'">
        
        <v-col cols="12" v-if="gridEvents.length === 0">
          <v-sheet class="text-center pa-10 rounded-xl border border-dashed bg-transparent" color="surface-variant" elevation="0">
            <v-icon icon="mdi-calendar-blank-outline" size="48" color="medium-emphasis" class="mb-2"></v-icon>
            <div class="text-h6 font-weight-bold text-high-emphasis">No events found</div>
            <div class="text-body-2 text-medium-emphasis mt-1">Check back later or try a different category.</div>
          </v-sheet>
        </v-col>

        <v-col cols="12" sm="6" md="4" lg="3" v-for="event in gridEvents" :key="event.id">
          
          <v-card @click="openEventDetails(event)" class="rounded-xl elevation-2 border-opacity-50 h-100 d-flex flex-column cursor-pointer" border hover>
            
            <div class="position-relative">
              <v-img :src="event.image" height="180" cover class="bg-grey-lighten-3"></v-img>
              
              <div class="position-absolute bg-surface rounded-lg elevation-2 d-flex flex-column align-center justify-center" 
                   style="top: 12px; left: 12px; width: 50px; height: 56px;">
                <span class="text-caption font-weight-bold text-primary" style="font-size: 10px !important;">{{ event.month }}</span>
                <span class="text-h6 font-weight-black leading-none mt-n1 text-high-emphasis">{{ event.day }}</span>
              </div>

              <v-chip size="small" color="surface" class="position-absolute font-weight-bold elevation-1" style="top: 12px; right: 12px;">
                {{ event.displayPrice }}
              </v-chip>
            </div>

            <div class="pa-4 d-flex flex-column flex-grow-1">
              <v-chip size="x-small" variant="tonal" color="primary" class="text-uppercase font-weight-bold mb-2 align-self-start">
                {{ event.category }}
              </v-chip>
              
              <h3 class="text-h6 font-weight-bold mb-3 line-clamp-2" style="line-height: 1.2;">{{ event.title }}</h3>
              
              <div class="d-flex flex-column gap-2 text-body-2 text-medium-emphasis mb-4">
                <div class="d-flex align-start gap-2">
                  <v-icon icon="mdi-clock-outline" size="18" class="mt-1 shrink-0"></v-icon>
                  <span>{{ event.time }}</span>
                </div>
                <div class="d-flex align-start gap-2">
                  <v-icon :icon="event.icon" size="18" class="mt-1 shrink-0"></v-icon>
                  <span class="line-clamp-1">{{ event.location }}</span>
                </div>
              </div>

              <div class="mt-auto d-flex align-center justify-space-between pt-3 border-t border-opacity-25">
                <div class="text-caption font-weight-medium text-medium-emphasis text-truncate pe-2">
                  By {{ event.organizer }}
                </div>
                
                <v-btn color="primary" variant="flat" size="small" class="text-none font-weight-bold px-4 rounded-lg shrink-0" @click="openEventDetails(event)">
                  View
                </v-btn>
              </div>
              
            </div>
          </v-card>
        </v-col>
      </v-row>
      
      <v-row v-if="activeTab === 'my-schedule'">
        <v-col cols="12">
          <v-sheet class="text-center pa-10 rounded-xl border border-dashed bg-transparent" color="surface-variant" elevation="0">
            <v-icon icon="mdi-ticket-confirmation-outline" size="48" color="medium-emphasis" class="mb-2"></v-icon>
            <div class="text-h6 font-weight-bold text-high-emphasis">Your schedule is empty</div>
            <div class="text-body-2 text-medium-emphasis mt-1">RSVP to events to see them listed here.</div>
            <v-btn color="primary" variant="tonal" class="mt-4 text-none font-weight-bold rounded-lg" @click="activeTab = 'discover'">
              Discover Events
            </v-btn>
          </v-sheet>
        </v-col>
      </v-row>
    </div>

  </v-container>
  <v-dialog v-model="showEventDialog" max-width="700" scrollable>
      <v-card v-if="selectedEvent" class="rounded-xl elevation-24 bg-surface overflow-hidden">
        
        <div class="pa-4 pa-md-6 border-b border-opacity-25 bg-surface d-flex justify-space-between align-start gap-4">
          <div>
            <v-chip size="small" color="primary" variant="tonal" class="font-weight-bold text-uppercase mb-2">{{ selectedEvent.category }}</v-chip>
            <h2 class="text-h5 font-weight-black leading-tight">{{ selectedEvent.title }}</h2>
          </div>
          <v-btn icon="mdi-close" variant="tonal" color="medium-emphasis" size="small" class="shrink-0" @click="showEventDialog = false"></v-btn>
        </div>

        <v-card-text class="pa-6 pa-md-8 text-body-1 text-high-emphasis bg-background">
          
          <v-img :src="selectedEvent.image" height="180" cover class="rounded-xl mb-6 elevation-2 bg-surface-variant border border-opacity-25"></v-img>

          <div class="d-flex flex-wrap align-center justify-space-between mb-8 gap-4">
            <div class="d-flex align-center gap-4 flex-wrap">
              <div class="d-flex align-center gap-2">
                <v-icon icon="mdi-calendar-blank" color="primary"></v-icon>
                <span class="font-weight-bold">{{ selectedEvent.month }} {{ selectedEvent.day }}</span>
              </div>
              <v-divider vertical class="mx-2 hidden-sm-and-down" style="height: 20px;"></v-divider>
              <div class="d-flex align-center gap-2">
                <v-icon icon="mdi-clock-outline" color="primary"></v-icon>
                <span class="font-weight-bold">{{ selectedEvent.time }}</span>
              </div>
              <v-divider vertical class="mx-2 hidden-sm-and-down" style="height: 20px;"></v-divider>
              <div class="d-flex align-center gap-2">
                <v-icon :icon="selectedEvent.icon" color="primary"></v-icon>
                <span class="font-weight-bold">{{ selectedEvent.location }}</span>
              </div>
            </div>
            
            <v-chip size="large" color="primary" class="font-weight-black elevation-1">
              {{ selectedEvent.displayPrice }}
            </v-chip>
          </div>

          <v-card class="pa-6 rounded-xl border border-opacity-25 bg-surface elevation-0 mb-8">
            <div class="text-overline font-weight-black text-primary mb-3 tracking-widest">About This Event</div>
            <div style="white-space: pre-wrap; line-height: 1.8;" class="text-body-1">
              {{ selectedEvent.description }}
            </div>
          </v-card>

          <v-card v-if="selectedEvent.group_id" class="rounded-xl border border-opacity-25 bg-surface overflow-hidden" elevation="0">
            <div class="pa-4 bg-surface-variant border-b border-opacity-25">
              <div class="text-caption font-weight-black text-primary text-uppercase tracking-widest">Hosted By</div>
            </div>
            <div class="pa-5 d-flex flex-column flex-sm-row gap-4 align-sm-center">
              <v-avatar size="72" class="elevation-2 border" color="white">
                <v-img :src="selectedEvent.group?.image || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=600'" cover></v-img>
              </v-avatar>
              <div class="flex-grow-1">
                <h3 class="text-h6 font-weight-bold leading-tight mb-1">{{ selectedEvent.organizer }}</h3>
                <p class="text-body-2 text-medium-emphasis mb-3 line-clamp-2">
                  {{ selectedEvent.group?.mission || 'A student-led community committed to organizing amazing experiences on campus.' }}
                </p>
                <v-btn variant="outlined" color="primary" size="small" class="text-none font-weight-bold rounded-lg" @click="router.push(`/clubsAndSocieties/${selectedEvent.group_id}`)">
                  View Full Club Profile
                </v-btn>
              </div>
            </div>
          </v-card>
          
          <div v-else class="d-flex align-center pa-5 bg-surface rounded-xl border border-opacity-25">
            <v-avatar size="56" color="primary" variant="tonal" class="mr-4">
              <v-icon icon="mdi-shield-crown-outline" size="24"></v-icon>
            </v-avatar>
            <div>
              <div class="text-caption text-medium-emphasis font-weight-bold text-uppercase tracking-widest mb-1">Hosted by</div>
              <div class="text-h6 font-weight-bold text-high-emphasis leading-none">{{ selectedEvent.organizer }}</div>
            </div>
          </div>

        </v-card-text>

        <v-card-actions class="pa-4 pa-sm-6 border-t border-opacity-25 bg-surface d-flex justify-space-between align-center">
          <div class="text-caption text-medium-emphasis font-weight-medium">Don't miss out!</div>
          <div class="d-flex gap-3">
            <v-btn variant="text" color="medium-emphasis" class="text-none font-weight-bold px-4" @click="showEventDialog = false">Cancel</v-btn>
            <!-- <v-btn color="primary" variant="flat" size="large" class="text-none font-weight-black px-8 rounded-lg">Confirm RSVP</v-btn> -->
          </div>
        </v-card-actions>
      </v-card>
    </v-dialog>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-4 { gap: 16px; }
</style>