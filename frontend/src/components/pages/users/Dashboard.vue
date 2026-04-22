<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useDisplay } from 'vuetify';

// Notice we only need 3 service calls now, because getStudentUpcoming handles both sidebars!
import { 
  getActiveAnnouncements, 
  getStudentFeed, 
  getStudentUpcoming 
} from '@/services/studentService';

const { smAndUp } = useDisplay();

// --- Application State ---
const isLoading = ref(true);
const selectedFilter = ref('All Updates');

// --- Dialog State ---
const isPostDialogOpen = ref(false);
const selectedPost = ref<any>(null);

const openPostDialog = (post: any) => {
  selectedPost.value = post;
  isPostDialogOpen.value = true;
};

// --- Data State ---
const activeAnnouncements = ref<any[]>([]);
const feedPosts = ref<any[]>([]);
const upcomingEvents = ref<any[]>([]);
const marketplaceItems = ref<any[]>([]);

// Filter options 
const filterOptions = ref([
  'All Updates', 'Technology', 'Sports', 'Administration',
  'Clubs & Societies', 'Student Life', 'Housing', 
  'Academics', 'Experiences', 'Tips & Tricks'   
]);

// --- Computed Filters ---
const filteredPosts = computed(() => {
  if (selectedFilter.value === "All Updates") {
    return feedPosts.value;
  }
  return feedPosts.value.filter(post => post.category === selectedFilter.value);
});

// --- Master Fetch Function ---
const loadDashboardData = async () => {
  isLoading.value = true;
  
  try {
    // We only need 3 calls! getStudentUpcoming fetches your combined getHomeData payload.
    const [announcementsRes, feedRes, homeDataRes] = await Promise.all([
      getActiveAnnouncements(),
      getStudentFeed(),
      getStudentUpcoming() 
    ]);

    // 1. Handle Announcements
    activeAnnouncements.value = announcementsRes.data?.map((alert: any) => ({
      ...alert,
      isExpanded: true
    })) || [];
    
    // 2. Handle Feed
    feedPosts.value = feedRes.data || [];

    // 3. Handle Sidebar Widgets (Marketplace & Events)
    const homeData = homeDataRes.data;
    
    // Assign the products directly
    marketplaceItems.value = homeData.featured_products || [];

    // Map the events and split the date string ("Oct 24, 2:00 PM") so the calendar UI looks perfect!
    upcomingEvents.value = (homeData.upcoming_activities || []).map((event: any) => {
      const parts = event.date.split(' '); // splits into ["Oct", "24,", "2:00", "PM"]
      return {
        ...event,
        month: parts[0] || 'TBA',
        day: parts[1] ? parts[1].replace(',', '') : '-',
        time: parts[2] ? `${parts[2]} ${parts[3]}` : ''
      };
    });

  } catch (error) {
    console.error("Failed to load dashboard data:", error);
  } finally {
    isLoading.value = false;
  }
};

// --- Lifecycle ---
onMounted(() => {
  loadDashboardData();
});
</script>

<template>
  <v-container fluid class="pa-6" style="max-width: 1200px;">
    <v-row>
      
      <v-col cols="12" lg="8">
        <div class="d-flex align-center justify-space-between mb-4">
          <div class="d-flex align-center gap-2">
            <div class="pulsing-dot bg-primary"></div>
            <h2 class="text-h6 font-weight-bold text-grey-darken-4">Happening Now</h2>
          </div>
          <v-sheet width="220" color="transparent">
            <v-select
              v-model="selectedFilter"
              :items="filterOptions"
              variant="outlined"
              hide-details
              density="compact"
              prepend-inner-icon="mdi-filter-variant"
              class="font-weight-medium"
              rounded="lg"
            ></v-select>
          </v-sheet>
        </div>

        <div v-if="activeAnnouncements.length > 0" class="mb-6">
          <v-alert
            v-for="alert in activeAnnouncements" 
            :key="alert.id"
            :color="alert.severity"
            variant="flat"
            class="rounded-xl elevation-6 mb-4 border border-opacity-25 pa-0 overflow-hidden"
          >
            <div 
              class="d-flex align-center justify-space-between pa-4 cursor-pointer" 
              @click="alert.isExpanded = !alert.isExpanded"
            >
              <div class="d-flex align-center">
                <v-icon 
                  :icon="alert.severity === 'error' ? 'mdi-alert-octagon' : (alert.severity === 'warning' ? 'mdi-alert' : 'mdi-information')" 
                  size="24" 
                  class="mr-3 text-white"
                ></v-icon>
                <h4 class="text-h6 font-weight-black leading-tight text-white mb-0">{{ alert.title }}</h4>
              </div>
              
              <v-btn icon variant="text" color="white" size="small" @click.stop="alert.isExpanded = !alert.isExpanded">
                <v-icon :icon="alert.isExpanded ? 'mdi-chevron-up' : 'mdi-chevron-down'"></v-icon>
              </v-btn>
            </div>

            <v-expand-transition>
              <div v-show="alert.isExpanded">
                <div class="px-4 pb-5 pt-0">
                  <p class="text-body-1 font-weight-medium text-white mb-0" style="opacity: 0.95;">
                    {{ alert.message }}
                  </p>
                </div>
              </div>
            </v-expand-transition>
          </v-alert>
        </div>

        <div class="d-flex flex-column gap-6">
          
          <template v-if="isLoading">
            <v-card v-for="n in 3" :key="'skeleton-' + n" class="rounded-xl elevation-2 border-opacity-50 pa-4" border>
              <v-skeleton-loader
                type="article, list-item-avatar"
                elevation="0"
                class="bg-transparent"
              ></v-skeleton-loader>
            </v-card>
          </template>

          <template v-else>
            <v-sheet 
              v-if="filteredPosts.length === 0" 
              class="text-center pa-10 rounded-xl border border-dashed" 
              color="surface-variant" 
              elevation="0"
            >
              <v-icon icon="mdi-text-box-search-outline" size="48" color="grey-lighten-1" class="mb-2"></v-icon>
              <div class="text-h6 text-medium-emphasis">No updates found</div>
              <div class="text-body-2 text-medium-emphasis mt-1">There are currently no posts in "{{ selectedFilter }}".</div>
            </v-sheet>

            <template v-for="post in filteredPosts" :key="post.id">
              <v-card 
                class="rounded-xl elevation-2 border-opacity-50 overflow-hidden cursor-pointer" 
                border
                v-ripple
                @click="openPostDialog(post)"
              >
                <div class="d-flex flex-column flex-sm-row h-100" style="height: 100%;">
                  
                  <div v-if="post.isImagePost" class="post-image-container shrink-0 d-flex"
                  :style="{
                        width: smAndUp ? '240px' :'100%',
                        minWidth: smAndUp ? '240px' :'100%',
                        height: smAndUp ? 'auto': '240px'  }">
                    <v-img :src="post.image" :alt="post.title" class="flex-grow-1" rounded="xl" cover></v-img>
                  </div>

                  <div class="d-flex flex-column justify-space-between pa-6 flex-grow-1">
                    <div class="d-flex gap-4">
                      
                      <div v-if="!post.isImagePost" class="d-flex align-center justify-center rounded-lg shrink-0 mt-1" :class="'bg-' + post.iconBg" style="width: 56px; height: 56px;">
                        <v-icon :icon="post.icon" :color="post.categoryColor" size="28"></v-icon>
                      </div>

                      <div class="flex-grow-1">
                        <div class="d-flex align-center mb-2 gap-2">
                          <v-chip :color="post.categoryColor" size="small" variant="tonal" class="text-uppercase font-weight-bold rounded">
                            {{ post.category }}
                          </v-chip>
                          <span class="text-caption text-medium-emphasis">{{ post.timestamp }}</span>
                        </div>
                        <h3 class="text-h6 font-weight-bold mb-1 line-clamp-2">{{ post.title }}</h3>
                        <p class="text-body-2 text-medium-emphasis mb-4 line-clamp-3">{{ post.content }}</p>
                      </div>
                    </div>

                    <div class="mt-auto">
                      <v-divider class="mb-3 opacity-20"></v-divider>
                      <div class="d-flex align-center justify-end">
                        <v-btn variant="text" color="primary" class="text-none font-weight-bold px-2" append-icon="mdi-arrow-right">
                          Read More
                        </v-btn>
                      </div>
                    </div>

                  </div>
                </div>
              </v-card>
            </template>

          </template>
        </div>
      </v-col>

      <v-col cols="12" lg="4">
        <div class="d-flex sticky-sidebar flex-column gap-6">
          
          <v-card class="rounded-xl elevation-2 border-opacity-50" border>
            <div class="d-flex justify-space-between align-center pa-4 bg-surface-variant border-bottom">
              <div class="text-subtitle-2 font-weight-bold d-flex align-center gap-2">
                <v-icon icon="mdi-shopping-outline" color="primary" size="small"></v-icon>
                Marketplace Picks
              </div>
              <router-link to="/marketplace" class="text-caption font-weight-bold text-primary text-uppercase text-decoration-none hover-underline">
                View All
              </router-link>
            </div>

            <div class="pa-4 d-flex flex-column gap-4" style="max-height: 250px; overflow-y: auto;">
              <div v-if="!isLoading && marketplaceItems.length === 0" class="text-caption text-center text-medium-emphasis py-4">
                No items currently listed.
              </div>
              
              <router-link 
                v-for="item in marketplaceItems" 
                :key="item.id" 
                :to="item.seller_id ? `/seller/${item.seller_id}` : '/marketplace'"
                class="d-flex gap-3 cursor-pointer marketplace-item text-decoration-none text-high-emphasis"
              >
                <div>
                  <v-img :src="item.image" :alt="item.title" width="64" height="64" cover class="rounded-lg shrink-0 bg-grey-lighten-4"></v-img>
                </div>
                <div class="d-flex flex-column justify-center">
                  <div class="text-body-2 font-weight-medium item-title transition-colors line-clamp-1">{{ item.title }}</div>
                  <div class="text-caption font-weight-bold text-primary mt-1">{{ item.price }}</div>
                </div>
              </router-link>
            </div>
          </v-card>

          <v-card class="rounded-xl elevation-2 border-opacity-50" border>
            <div class="d-flex justify-space-between align-center pa-4 bg-surface-variant border-bottom">
              <div class="text-subtitle-2 font-weight-bold d-flex align-center gap-2">
                <v-icon icon="mdi-calendar-outline" color="primary" size="small"></v-icon>
                Upcoming Clubs
              </div>
              <span class="text-caption font-weight-bold text-medium-emphasis text-uppercase">
                This Month
              </span>
            </div>

            <div class="pa-4 d-flex flex-column gap-5">
              <div v-if="!isLoading && upcomingEvents.length === 0" class="text-caption text-center text-medium-emphasis py-4">
                No upcoming events scheduled.
              </div>
              
              <div v-for="event in upcomingEvents" :key="event.id" class="d-flex align-start gap-4">
                
                <div 
                  class="d-flex flex-column rounded-lg elevation-2 shrink-0 overflow-hidden text-center border" 
                  style="width: 52px;"
                  :class="event.highlight ? 'border-primary' : 'border-opacity-25'"
                >
                  <div 
                    class="text-uppercase font-weight-bold" 
                    style="font-size: 10px; padding: 4px 0; letter-spacing: 0.5px;"
                    :class="event.highlight ? 'bg-primary text-white' : 'bg-grey-lighten-3 text-medium-emphasis'"
                  >
                    {{ event.month }}
                  </div>
                  <div class="bg-surface d-flex align-center justify-center" style="padding: 6px 0;">
                    <span class="text-h6 font-weight-black leading-none" :class="event.highlight ? 'text-primary' : 'text-high-emphasis'">
                      {{ event.day }}
                    </span>
                  </div>
                </div>

                <div class="flex-grow-1" style="min-width: 0;">
                  
                  <div class="text-body-2 font-weight-bold line-clamp-2 mb-1" style="line-height: 1.2;">
                    {{ event.title }}
                  </div>
                  
                  <div class="text-caption font-weight-bold text-primary text-truncate">
                    {{ event.club }}
                  </div>
                  
                  <div class="text-caption text-medium-emphasis d-flex align-center gap-1 mt-1">
                    <v-icon icon="mdi-clock-outline" size="14" class="shrink-0"></v-icon> 
                    <span class="text-truncate">{{ event.time }}</span>
                    
                    <v-icon icon="mdi-map-marker-outline" size="14" class="ml-2 shrink-0"></v-icon> 
                    <span class="text-truncate">{{ event.location }}</span>
                  </div>
                </div>

              </div>
            </div>
          </v-card>

          <div class="pa-5 rounded-xl text-white elevation-4 gradient-bg">
            <h4 class="text-subtitle-2 font-weight-bold mb-3">Campus Helplines</h4>
            <div class="d-flex flex-column gap-3">
              <div class="d-flex justify-space-between align-center text-caption">
                <span>Emergency Security</span>
                <span class="font-weight-bold" style="font-family: monospace;">+254 7XX XXX XXX</span>
              </div>
              <div class="d-flex justify-space-between align-center text-caption">
                <span>Health Clinic</span>
                <span class="font-weight-bold" style="font-family: monospace;">+254 7XX XXX XXX</span>
              </div>
              <v-btn variant="tonal" block class="mt-2 text-none font-weight-bold bg-white-opacity">
                Call for Assistance
              </v-btn>
            </div>
          </div>

        </div>
      </v-col>

    </v-row>
  </v-container>

  <v-dialog v-model="isPostDialogOpen" max-width="750" transition="dialog-bottom-transition">
      <v-card v-if="selectedPost" class="rounded-xl elevation-24 border-0 d-flex flex-column" max-height="90vh">
        
        <div class="d-flex align-center justify-space-between pa-4 bg-surface" style="border-bottom: 1px solid rgba(0,0,0,0.05);">
          <div class="text-subtitle-1 font-weight-bold text-medium-emphasis px-2">
            Update Details
          </div>
          <v-btn icon="mdi-close" variant="text" color="medium-emphasis" @click="isPostDialogOpen = false"></v-btn>
        </div>

        <v-card-text class="pa-4 pa-md-6 flex-grow-1" style="overflow-y: auto;">
          
          <div class="d-flex flex-column flex-sm-row gap-4 gap-md-6 mb-6">
            
            <div class="shrink-0 d-flex justify-center justify-sm-start">
              <v-img 
                v-if="selectedPost.isImagePost && selectedPost.image" 
                :src="selectedPost.image" 
                width="200" 
                height="200" 
                max-width="200"
                cover
                class="rounded-xl border border-opacity-25 bg-grey-lighten-3 elevation-2"
              ></v-img>
              
              <div 
                v-else
                class="d-flex align-center justify-center rounded-xl elevation-2" 
                :class="'bg-' + selectedPost.iconBg" 
                style="width: 140px; height: 140px; border: 1px solid rgba(0,0,0,0.05);"
              >
                <v-icon :icon="selectedPost.icon" :color="selectedPost.categoryColor" size="64"></v-icon>
              </div>
            </div>

            <div class="d-flex flex-column justify-center text-center text-sm-left">
              <div class="d-flex align-center justify-center justify-sm-start gap-3 mb-3">
                <v-chip :color="selectedPost.categoryColor" size="small" variant="tonal" class="text-uppercase font-weight-bold px-3 rounded-pill">
                  {{ selectedPost.category }}
                </v-chip>
                <div class="text-caption font-weight-medium text-medium-emphasis d-flex align-center">
                  <v-icon icon="mdi-clock-outline" size="14" class="mr-1"></v-icon>
                  {{ selectedPost.timestamp }}
                </div>
              </div>
              
              <h1 class="text-h5 font-weight-black text-high-emphasis" style="line-height: 1.3;">
                {{ selectedPost.title }}
              </h1>
            </div>

          </div>

          <div class="post-content-reader rounded-xl pa-4 pa-md-5 bg-grey-lighten-4 border border-opacity-25" style="border-color: rgba(0,0,0,0.08) !important;">
            <p class="text-body-2 text-grey-darken-3 mb-0" style="white-space: pre-wrap; line-height: 1.7; font-size: 1rem;">              
              {{ selectedPost.fullContent || selectedPost.content }}             
            </p>
          </div>

        </v-card-text>
        
        <v-card-actions class="pa-4 pa-md-6 d-flex justify-end bg-surface border-t border-opacity-25 shrink-0">
          <v-btn 
            variant="text" 
            color="medium-emphasis" 
            class="text-none font-weight-bold mr-3 rounded-lg px-4" 
            @click="isPostDialogOpen = false"
          >
            Close
          </v-btn>
          
          <v-btn 
            v-if="selectedPost.category.includes('Event')" 
            variant="elevated" 
            :color="selectedPost.categoryColor" 
            class="text-none font-weight-black rounded-lg px-8"
            size="large"
            append-icon="mdi-arrow-right"
          >
            RSVP Now
          </v-btn>
        </v-card-actions>

      </v-card>
    </v-dialog>
</template>

<style scoped>
/* 1. Spacing */
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
.gap-5 { gap: 20px; }
.gap-6 { gap: 24px; }

/* 2. Custom Border Color for Alert */
.border-error-subtle {
  border-color: rgba(var(--v-theme-error), 0.3) !important;
}

/* 3. Pulsing Dot Animation */
.pulsing-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}

/* 4. Stacked Avatars */
.stacked-avatars .v-avatar {
  border: 2px solid white;
  margin-left: -8px;
}
.stacked-avatars .v-avatar:first-child {
  margin-left: 0;
}

/* 5. Widget Specific Styles */
.border-bottom {
  border-bottom: 1px solid rgba(0,0,0,0.05);
}
.cursor-pointer {
  cursor: pointer;
}
.hover-underline:hover {
  text-decoration: underline;
}

/* 6. Marketplace Hover Effect */
.marketplace-item:hover .item-title {
  color: rgb(var(--v-theme-primary));
}
.transition-colors {
  transition: color 0.2s ease-in-out;
}

/* 7. Text Truncation */
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

/* 8. Gradient Background for Helplines */
.gradient-bg {
  background: linear-gradient(135deg, #a08b7d 0%, #d84315 100%);
}
.bg-white-opacity {
  background-color: rgba(255, 255, 255, 0.2) !important;
}

/* 9. Sticky Sidebar Settings */
.sticky-sidebar {
  position: sticky;
  top: 88px; 
  max-height: 250vh; 
  overflow-y: auto; 
}

.sticky-sidebar::-webkit-scrollbar {
  display: none; 
}
.sticky-sidebar {
  -ms-overflow-style: none; 
  scrollbar-width: none; 
}
</style>