<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useGroups } from '@/composables/useGroups'; 

const route = useRoute();
const router = useRouter();
const { fetchGroupById, toggleMembership } = useGroups(); 

const isLoading = ref(true);
const showSnackbar = ref(false);
const snackbarText = ref('');
const activeTab = ref('feed');

// --- Mock Data: Extended Profile Content ---
const club = ref<any>(null);

// New Mock Data: Posts
const posts = ref([
  { 
    id: 1, 
    author: 'Alice Wanjiku', 
    authorRole: 'Chairperson',
    authorAvatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150',
    content: 'Welcome to the new semester! Our first hackathon is scheduled for next month. Start forming your teams of 3-4 people.', 
    timeAgo: '2 hours ago', 
    likes: 45,
    isLiked: false
  },
  { 
    id: 2, 
    author: 'Tech Innovators', 
    authorRole: 'Official',
    authorAvatar: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=150',
    content: 'Reminder: Weekly coding session happens tomorrow at 10 AM. We will be covering Introduction to Vue.js. Please carry your laptops.', 
    timeAgo: '3 days ago', 
    likes: 82,
    isLiked: true
  }
]);

// New Mock Data: Upcoming Events
const events = ref([
  { id: 101, title: 'Intro to Web3 Workshop', date: 'Oct 24, 2025', time: '2:00 PM - 5:00 PM', location: 'IT Lab 1', type: 'Workshop' },
  { id: 102, title: 'Annual Tech Hackathon', date: 'Nov 12, 2025', time: '8:00 AM (48 Hours)', location: 'Main Auditorium', type: 'Competition' }
]);

// New Mock Data: Leadership
const leaders = ref([
  { id: 1, name: 'Alice Wanjiku', role: 'Chairperson', avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150' },
  { id: 2, name: 'David Omondi', role: 'Secretary', avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150' },
  { id: 3, name: 'Catherine Mutua', role: 'Treasurer', avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&q=80&w=150' }
]);

// New Mock Data: Gallery
const gallery = ref([
  'https://images.unsplash.com/photo-1531482615713-2afd69097998?auto=format&fit=crop&q=80&w=400',
  'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=400',
  'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&q=80&w=400',
  'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=400'
]);

// --- Methods ---
const handleToggleMembership = async () => {
  if (!club.value) return;
  const updatedGroup = await toggleMembership(club.value.id);
  club.value = updatedGroup;
  
  const actionText = club.value.currentUserRole === 'member' ? 'joined' : 'left';
  snackbarText.value = `You have successfully ${actionText} ${club.value.name}.`;
  showSnackbar.value = true;
};

const toggleLike = (post: any) => {
  post.isLiked = !post.isLiked;
  post.isLiked ? post.likes++ : post.likes--;
};

// --- Lifecycle ---
onMounted(async () => {
  const clubId = Number(route.params.id);
  try {
    const data = await fetchGroupById(clubId);
    club.value = data;
    setTimeout(() => { isLoading.value = false; }, 400);
  } catch (error) {
    router.push('/clubsAndSocieties'); 
  }
});
</script>

<template>
  <v-container fluid class="bg-background min-vh-100 pa-0 pb-12">
    
    <v-overlay :model-value="isLoading" class="align-center justify-center" persistent>
      <v-progress-circular color="primary" indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <div v-if="!isLoading && club">
      
      <v-img :src="club.image" height="320" cover class="bg-surface-variant position-relative">
        <div class="position-absolute w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.85) 100%);"></div>
        
        <v-container style="max-width: 1200px; height: 100%;" class="d-flex align-end pb-8 position-relative z-index-1">
          <div class="d-flex flex-column flex-md-row align-md-end w-100 gap-6">
            
            <v-avatar size="140" class="elevation-6 border-white bg-surface" style="border: 4px solid white; margin-bottom: -40px;">
              <v-img :src="club.image" cover></v-img>
            </v-avatar>

            <div class="text-white flex-grow-1 pb-md-2">
              <v-chip color="primary" variant="flat" size="small" class="mb-2 font-weight-bold text-uppercase">
                {{ club.category }}
              </v-chip>
              <h1 class="text-h3 font-weight-black mb-2 text-shadow leading-tight">{{ club.name }}</h1>
              <div class="d-flex align-center flex-wrap gap-4 opacity-90">
                <span class="d-flex align-center text-body-2 font-weight-medium">
                  <v-icon icon="mdi-account-group" size="18" class="mr-2"></v-icon> {{ club.membersCount }} Members
                </span>
                <span class="d-flex align-center text-body-2 font-weight-medium">
                  <v-icon icon="mdi-calendar-clock" size="18" class="mr-2"></v-icon> {{ club.meeting }}
                </span>
              </div>
            </div>

            <div class="pb-md-2">
              <v-btn 
                :color="club.currentUserRole ? 'success' : 'primary'" 
                :variant="club.currentUserRole ? 'flat' : 'elevated'"
                size="x-large"
                class="text-none font-weight-black rounded-pill px-8 elevation-4"
                @click="handleToggleMembership"
              >
                <v-icon start :icon="club.currentUserRole ? 'mdi-check-circle' : 'mdi-plus-circle-outline'"></v-icon>
                {{ club.currentUserRole ? 'Joined' : 'Join Group' }}
              </v-btn>
            </div>

          </div>
        </v-container>
      </v-img>

      <v-container style="max-width: 1200px;" class="mt-12">
        <v-row>
          
          <v-col cols="12" md="4" lg="3">
            <div class="position-sticky" style="top: 24px;">
              
              <v-card class="rounded-xl border-opacity-25 bg-surface mb-6" border elevation="1">
                <div class="pa-4 border-b border-opacity-25 font-weight-black text-uppercase text-caption text-high-emphasis tracking-widest d-flex align-center">
                  <v-icon icon="mdi-information-outline" size="16" class="mr-2 text-primary"></v-icon> About
                </div>
                <div class="pa-5">
                  <p class="text-body-2 text-medium-emphasis mb-0 leading-relaxed">
                    {{ club.description }}
                  </p>
                </div>
              </v-card>

              <v-card class="rounded-xl border-opacity-25 bg-surface mb-6" border elevation="1">
                <div class="pa-4 border-b border-opacity-25 font-weight-black text-uppercase text-caption text-high-emphasis tracking-widest d-flex align-center">
                  <v-icon icon="mdi-map-marker-outline" size="16" class="mr-2 text-primary"></v-icon> Logistics
                </div>
                <v-list lines="two" class="py-0 bg-transparent">
                  <v-list-item class="px-5 py-3">
                    <template v-slot:prepend>
                      <v-avatar color="primary" variant="tonal" size="36" class="mr-4"><v-icon icon="mdi-clock-outline" size="18"></v-icon></v-avatar>
                    </template>
                    <v-list-item-title class="font-weight-bold text-high-emphasis">{{ club.meeting }}</v-list-item-title>
                    <v-list-item-subtitle class="text-medium-emphasis text-caption">Routine Schedule</v-list-item-subtitle>
                  </v-list-item>
                </v-list>
              </v-card>

            </div>
          </v-col>

          <v-col cols="12" md="8" lg="9">
            
            <v-card class="rounded-xl border-opacity-25 bg-surface overflow-hidden" border elevation="1">
              
              <v-tabs 
                v-model="activeTab" 
                color="primary" 
                class="border-b border-opacity-25 bg-surface-variant px-2"
                height="60"
              >
                <v-tab value="feed" class="font-weight-bold text-none"><v-icon start icon="mdi-bullhorn-outline"></v-icon> Announcements</v-tab>
                <v-tab value="events" class="font-weight-bold text-none"><v-icon start icon="mdi-calendar-star"></v-icon> Events</v-tab>
                <v-tab value="team" class="font-weight-bold text-none"><v-icon start icon="mdi-account-tie-outline"></v-icon> Leadership</v-tab>
                <v-tab value="gallery" class="font-weight-bold text-none"><v-icon start icon="mdi-image-multiple-outline"></v-icon> Gallery</v-tab>
              </v-tabs>

              <v-window v-model="activeTab" class="bg-surface pa-6">
                
                <v-window-item value="feed">
                  <div class="d-flex flex-column gap-6">
                    <v-card v-for="post in posts" :key="post.id" class="rounded-xl border-opacity-25 bg-surface-variant transition-swing" variant="outlined">
                      <div class="pa-5">
                        <div class="d-flex align-center justify-space-between mb-4">
                          <div class="d-flex align-center">
                            <v-avatar size="48" class="mr-3 elevation-1">
                              <v-img :src="post.authorAvatar" cover></v-img>
                            </v-avatar>
                            <div>
                              <div class="d-flex align-center">
                                <span class="font-weight-bold text-high-emphasis mr-2">{{ post.author }}</span>
                                <v-chip size="x-small" color="success" variant="flat" class="font-weight-bold">{{ post.authorRole }}</v-chip>
                              </div>
                              <div class="text-caption text-medium-emphasis">{{ post.timeAgo }}</div>
                            </div>
                          </div>
                        </div>

                        <p class="text-body-1 text-high-emphasis mb-5 leading-relaxed">{{ post.content }}</p>

                        <div class="d-flex align-center border-t border-opacity-25 pt-3">
                          <v-btn 
                            :variant="post.isLiked ? 'tonal' : 'text'" 
                            :color="post.isLiked ? 'primary' : 'medium-emphasis'" 
                            class="text-none font-weight-bold rounded-pill px-4"
                            @click="toggleLike(post)"
                          >
                            <v-icon start :icon="post.isLiked ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'" size="20"></v-icon> 
                            {{ post.likes }} Likes
                          </v-btn>
                        </div>
                      </div>
                    </v-card>
                  </div>
                </v-window-item>

                <v-window-item value="events">
                  <v-row>
                    <v-col cols="12" sm="6" v-for="event in events" :key="event.id">
                      <v-card variant="outlined" class="rounded-xl border-opacity-25 h-100 bg-surface-variant pa-5 d-flex flex-column">
                        <v-chip size="x-small" color="primary" variant="tonal" class="font-weight-bold text-uppercase mb-3 align-self-start">{{ event.type }}</v-chip>
                        <h3 class="text-h6 font-weight-bold text-high-emphasis mb-4 leading-tight">{{ event.title }}</h3>
                        
                        <div class="mt-auto d-flex flex-column gap-2">
                          <div class="d-flex align-center text-body-2 text-medium-emphasis">
                            <v-icon icon="mdi-calendar" size="16" class="mr-2 text-primary"></v-icon> {{ event.date }}
                          </div>
                          <div class="d-flex align-center text-body-2 text-medium-emphasis">
                            <v-icon icon="mdi-clock-outline" size="16" class="mr-2 text-primary"></v-icon> {{ event.time }}
                          </div>
                          <div class="d-flex align-center text-body-2 text-medium-emphasis">
                            <v-icon icon="mdi-map-marker-outline" size="16" class="mr-2 text-primary"></v-icon> {{ event.location }}
                          </div>
                        </div>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-window-item>

                <v-window-item value="team">
                  <v-row>
                    <v-col cols="12" sm="4" v-for="leader in leaders" :key="leader.id">
                      <v-card variant="outlined" class="rounded-xl border-opacity-25 text-center pa-6 bg-surface-variant">
                        <v-avatar size="80" class="mb-4 elevation-2">
                          <v-img :src="leader.avatar" cover></v-img>
                        </v-avatar>
                        <h3 class="text-subtitle-1 font-weight-bold text-high-emphasis">{{ leader.name }}</h3>
                        <div class="text-caption font-weight-bold text-primary text-uppercase">{{ leader.role }}</div>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-window-item>

                <v-window-item value="gallery">
                  <v-row>
                    <v-col cols="6" md="4" v-for="(img, index) in gallery" :key="index">
                      <v-card class="rounded-xl overflow-hidden elevation-1" hover>
                        <v-img :src="img" height="150" cover class="bg-grey-lighten-2 transition-swing"></v-img>
                      </v-card>
                    </v-col>
                  </v-row>
                </v-window-item>

              </v-window>
            </v-card>
            
          </v-col>
        </v-row>
      </v-container>
    </div>

    <v-snackbar v-model="showSnackbar" :timeout="3000" color="success" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon icon="mdi-check-circle" class="mr-3"></v-icon>
        {{ snackbarText }}
      </div>
    </v-snackbar>
  </v-container>
</template>

<style scoped>
.gap-4 { gap: 16px; }
.gap-6 { gap: 24px; }
.gap-2 { gap: 8px; }

.text-shadow { text-shadow: 0px 2px 6px rgba(0,0,0,0.8); }
.leading-tight { line-height: 1.2 !important; }
.leading-relaxed { line-height: 1.6 !important; }

/* Gives the overlapping avatar a clean look over the dark image */
.border-white { border-color: rgb(var(--v-theme-surface)) !important; }
</style>