<script setup lang="ts">
import { ref, computed } from 'vue';

// 1. UI State
const activeTab = ref('discover'); // 'discover' or 'my-schedule'
const searchQuery = ref('');
const selectedCategory = ref('All');
const categories = ref(['All', 'Academic', 'Social', 'Sports', 'Career']);

// 2. RAW BACKEND DATA (Split into Featured and Grid)
const rawFeaturedEvents = ref([
  {
    id: 1,
    title: 'CUK Tech Hackathon 2026',
    category: 'Academic',
    location: 'Main Hall',
    date: '2026-03-20T08:00:00Z',
    price: 0, 
    organizer: 'School of Computing',
    image: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1200'
  }
]);

const rawGridEvents = ref([
  {
    id: 2,
    title: 'Mr. & Miss Coop Auditions',
    category: 'Social',
    location: 'Student Centre',
    date: '2026-03-25T14:00:00Z',
    price: 500,
    organizer: 'Student Council',
    image: 'https://images.unsplash.com/photo-1514525253161-7a46d19cd819?auto=format&fit=crop&q=80&w=600'
  },
  {
    id: 3,
    title: 'Resume Building Masterclass',
    category: 'Career',
    location: 'Google Meet',
    date: '2026-03-28T16:00:00Z',
    price: 0,
    organizer: 'Career Services',
    image: 'https://images.unsplash.com/photo-1586281380349-632531db7ed4?auto=format&fit=crop&q=80&w=600'
  },
  {
    id: 4,
    title: 'Inter-Faculty Basketball Finals',
    category: 'Sports',
    location: 'Main Court',
    date: '2026-04-02T15:30:00Z',
    price: 0,
    organizer: 'Sports Department',
    image: 'https://images.unsplash.com/photo-1546519638-68e109498ffc?auto=format&fit=crop&q=80&w=600'
  }
]);

// 3. REUSABLE FORMATTER (Processes dates, times, and icons)
const formatEvent = (event: any) => {
  const eventDate = new Date(event.date);
  
  // Format Date
  const month = eventDate.toLocaleString('default', { month: 'short' }).toUpperCase();
  const day = eventDate.getDate().toString();
  
  // Format Time
  const time = eventDate.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });

  // Determine Icon based on location
  const locLower = event.location.toLowerCase();
  const icon = (locLower.includes('meet') || locLower.includes('zoom') || locLower.includes('online')) 
    ? 'mdi-video-outline' 
    : 'mdi-map-marker-outline';

  // Format Price
  const displayPrice = event.price === 0 ? 'Free' : `KES ${event.price}`;

  return { ...event, month, day, time, icon, displayPrice };
};

// 4. FRONTEND COMPUTED LOGIC
// A. Banner Events
const featuredEvents = computed(() => {
  return rawFeaturedEvents.value.map(formatEvent);
});

// B. Grid Events
const gridEvents = computed(() => {
  let result = rawGridEvents.value.map(formatEvent);

  // Filter by Category
  if (selectedCategory.value !== 'All') {
    result = result.filter(e => e.category === selectedCategory.value);
  }
  
  // Filter by Search
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(e => e.title.toLowerCase().includes(query));
  }

  return result;
});
</script>

<template>
  <v-container fluid class="pa-6" style="max-width: 1200px;">
    
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
          
          <v-chip color="primary" variant="flat" class="mb-3 align-self-start font-weight-bold text-uppercase">
            Featured
          </v-chip>
          
          <h2 class="text-h4 text-md-h3 font-weight-black mb-3 line-clamp-2" style="line-height: 1.1;">
            {{ event.title }}
          </h2>
          
          <div class="d-flex flex-wrap align-center gap-4 text-body-2 text-md-body-1 font-weight-medium mb-5 opacity-90">
            <div class="d-flex align-center gap-1"><v-icon icon="mdi-calendar" size="20"></v-icon> {{ event.month }} {{ event.day }}</div>
            <div class="d-flex align-center gap-1"><v-icon icon="mdi-clock-outline" size="20"></v-icon> {{ event.time }}</div>
            <div class="d-flex align-center gap-1"><v-icon :icon="event.icon" size="20"></v-icon> {{ event.location }}</div>
          </div>
          
          <v-btn color="primary" size="large" class="text-none font-weight-bold align-self-start rounded-lg px-8">
            RSVP Now
          </v-btn>
        </div>
      </v-carousel-item>
    </v-carousel>

    <div class="d-flex flex-column flex-md-row justify-space-between align-md-center mb-6 gap-4">
      
      <v-tabs v-model="activeTab" color="primary">
        <v-tab value="discover" class="text-none font-weight-bold">Discover</v-tab>
        <v-tab value="my-schedule" class="text-none font-weight-bold">My Schedule</v-tab>
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
        <v-sheet class="text-center pa-10 rounded-xl border border-dashed" color="surface-variant" elevation="0">
          <v-icon icon="mdi-calendar-blank-outline" size="48" color="grey-lighten-1" class="mb-2"></v-icon>
          <div class="text-h6 text-medium-emphasis">No events found</div>
          <div class="text-body-2 text-medium-emphasis mt-1">Check back later or try a different category.</div>
        </v-sheet>
      </v-col>

      <v-col cols="12" sm="6" md="4" lg="3" v-for="event in gridEvents" :key="event.id">
        <v-card class="rounded-xl elevation-2 border-opacity-50 h-100 d-flex flex-column" border hover>
          
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

            <div class="mt-auto d-flex align-center justify-space-between pt-3 border-top">
              <div class="text-caption text-medium-emphasis text-truncate pe-2">
                By {{ event.organizer }}
              </div>
              <v-btn color="primary" variant="flat" size="small" class="text-none font-weight-bold px-4 rounded-lg shrink-0">
                RSVP
              </v-btn>
            </div>
            
          </div>
        </v-card>
      </v-col>
    </v-row>
    
    <v-row v-if="activeTab === 'my-schedule'">
      <v-col cols="12">
        <v-sheet class="text-center pa-10 rounded-xl border border-dashed" color="surface-variant" elevation="0">
          <v-icon icon="mdi-ticket-confirmation-outline" size="48" color="grey-lighten-1" class="mb-2"></v-icon>
          <div class="text-h6 text-medium-emphasis">Your schedule is empty</div>
          <div class="text-body-2 text-medium-emphasis mt-1">RSVP to events to see them listed here.</div>
          <v-btn color="primary" variant="tonal" class="mt-4 text-none font-weight-bold" @click="activeTab = 'discover'">
            Discover Events
          </v-btn>
        </v-sheet>
      </v-col>
    </v-row>

  </v-container>
</template>