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

// --- STATE: Start with empty data ---
const club = ref<any>(null);
const leaders = ref<any[]>([]);
const announcements = ref<any[]>([]);
const plans = ref<any[]>([]);
const successes = ref<any[]>([]);

// --- Methods ---
const handleToggleMembership = async () => {
  if (!club.value) return;
  
  try {
    const response = await toggleMembership(club.value.id);
    
    // Update the UI instantly with the response from Laravel
    club.value.currentUserRole = response.currentUserRole;
    club.value.membersCount = response.newMembersCount;
    
    const actionText = club.value.currentUserRole === 'member' ? 'joined' : 'left';
    snackbarText.value = `You have successfully ${actionText} ${club.value.name}.`;
    showSnackbar.value = true;
  } catch (error) {
    snackbarText.value = 'Failed to update membership. Please try again.';
    showSnackbar.value = true;
  }
};

const toggleLike = (post: any) => {
  post.isLiked = !post.isLiked;
  post.isLiked ? post.likes++ : post.likes--;
  // Note: In a real app, you would also make an axios.post() call here to save the like to the DB!
};

// --- Lifecycle ---
onMounted(async () => {
  const clubId = Number(route.params.id);
  try {
    // 1. Fetch the data from Laravel
    const data = await fetchGroupById(clubId);
    
    // 2. Assign the main club details
    club.value = data;
    
    // 3. Populate the arrays directly from the backend relationships
    leaders.value = data.leaders || [];
    announcements.value = data.posts || [];
    
    // 4. Map the Events table data to match your UI layout expectations
    plans.value = (data.events || []).map((e: any) => ({
      id: e.id,
      title: e.title,
      date: new Date(e.event_date).toLocaleDateString('en-US', { month: 'short', year: 'numeric' }),
      desc: e.description,
      type: e.event_type,
      icon: 'mdi-calendar-star' 
    }));

    // 5. Map the Achievements table data to match your UI layout expectations
    successes.value = (data.achievements || []).map((a: any) => ({
      id: a.id,
      title: a.title,
      date: a.date_achieved,
      desc: a.description,
      image: a.image_path || 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&q=80&w=600'
    }));

    isLoading.value = false;
    
  } catch (error) {
    console.error("Failed to load club profile");
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
                  <v-icon icon="mdi-account-group" size="18" class="mr-2"></v-icon> {{ club.membersCount }} Active Members
                </span>
              </div>
            </div>
          </div>
        </v-container>
      </v-img>

      <v-container style="max-width: 1200px;" class="mt-12">
        <v-row>
          
          <v-col cols="12" md="8" lg="8">
            
            <v-card v-if="club.mission" class="rounded-xl border-opacity-25 bg-surface mb-10 pa-6 pa-md-8 text-center" border elevation="1" style="border-top: 4px solid rgb(var(--v-theme-primary)) !important;">
              <div class="text-overline font-weight-black text-primary mb-2 tracking-widest">Our Mission</div>
              <p class="text-h5 font-weight-medium text-high-emphasis leading-relaxed font-italic mb-0">
                "{{ club.mission }}"
              </p>
            </v-card>

            <div class="mb-10">
              <h2 class="text-h5 font-weight-black text-high-emphasis d-flex align-center mb-6">
                <v-icon icon="mdi-bullhorn-outline" class="mr-3 text-primary"></v-icon> Latest Announcements
              </h2>
              <div v-if="announcements.length > 0" class="d-flex flex-column gap-4">
                <v-card v-for="post in announcements" :key="post.id" class="rounded-xl border-opacity-25 bg-surface transition-swing" border elevation="1">
                  <div class="pa-5">
                    <div class="d-flex align-center justify-space-between mb-4">
                      <div class="d-flex align-center">
                        <v-avatar size="48" class="mr-3 elevation-1"><v-img :src="post.avatar" cover></v-img></v-avatar>
                        <div>
                          <div class="d-flex align-center">
                            <span class="font-weight-bold text-high-emphasis mr-2">{{ post.author }}</span>
                            <v-chip size="x-small" color="success" variant="flat" class="font-weight-bold">{{ post.role }}</v-chip>
                          </div>
                          <div class="text-caption text-medium-emphasis">{{ post.timeAgo }}</div>
                        </div>
                      </div>
                    </div>
                    <p class="text-body-1 text-high-emphasis mb-4 leading-relaxed">{{ post.content }}</p>
                    <div class="d-flex align-center border-t border-opacity-25 pt-3">
                      <v-btn :variant="post.isLiked ? 'tonal' : 'text'" :color="post.isLiked ? 'primary' : 'medium-emphasis'" class="text-none font-weight-bold rounded-pill px-4" @click="toggleLike(post)">
                        <v-icon start :icon="post.isLiked ? 'mdi-thumb-up' : 'mdi-thumb-up-outline'" size="20"></v-icon> {{ post.likes }} Likes
                      </v-btn>
                    </div>
                  </div>
                </v-card>
              </div>
              <div v-else class="text-body-1 text-medium-emphasis text-center py-6 border border-dashed rounded-xl border-opacity-25">
                No recent announcements.
              </div>
            </div>

            <div class="mb-10">
              <h2 class="text-h5 font-weight-black text-high-emphasis d-flex align-center mb-6">
                <v-icon icon="mdi-rocket-launch-outline" class="mr-3 text-primary"></v-icon> Future Plans & Roadmap
              </h2>
              <v-row v-if="plans.length > 0">
                <v-col cols="12" sm="6" v-for="plan in plans" :key="plan.id">
                  <v-card class="rounded-xl border-opacity-25 bg-surface h-100 d-flex flex-column pa-5" border elevation="1">
                    <v-avatar color="primary" variant="tonal" size="48" class="mb-4">
                      <v-icon :icon="plan.icon" size="24"></v-icon>
                    </v-avatar>
                    <v-chip size="x-small" color="medium-emphasis" variant="tonal" class="font-weight-bold text-uppercase mb-2 align-self-start">{{ plan.type }} • {{ plan.date }}</v-chip>
                    <h3 class="text-h6 font-weight-bold text-high-emphasis mb-2 leading-tight">{{ plan.title }}</h3>
                    <p class="text-body-2 text-medium-emphasis mb-0 mt-auto">{{ plan.desc }}</p>
                  </v-card>
                </v-col>
              </v-row>
              <div v-else class="text-body-1 text-medium-emphasis text-center py-6 border border-dashed rounded-xl border-opacity-25">
                No upcoming events listed yet.
              </div>
            </div>

            <div class="mb-10">
              <h2 class="text-h5 font-weight-black text-high-emphasis d-flex align-center mb-6">
                <v-icon icon="mdi-trophy-award" class="mr-3 text-warning"></v-icon> Track Record & Successes
              </h2>
              <div v-if="successes.length > 0" class="d-flex flex-column gap-6">
                <v-card v-for="item in successes" :key="item.id" class="rounded-xl border-opacity-25 bg-surface overflow-hidden d-flex flex-column flex-sm-row" border elevation="1">
                  <v-img :src="item.image" cover width="100%" class="bg-surface-variant flex-shrink-0" style="max-width: 300px; min-height: 200px;"></v-img>
                  <div class="pa-5 pa-md-6 d-flex flex-column justify-center">
                    <div class="text-caption font-weight-black text-primary text-uppercase tracking-widest mb-1">{{ item.date }}</div>
                    <h3 class="text-h5 font-weight-bold text-high-emphasis mb-3 leading-tight">{{ item.title }}</h3>
                    <p class="text-body-1 text-medium-emphasis mb-0 leading-relaxed">{{ item.desc }}</p>
                  </div>
                </v-card>
              </div>
              <div v-else class="text-body-1 text-medium-emphasis text-center py-6 border border-dashed rounded-xl border-opacity-25">
                Check back soon for updates on our achievements!
              </div>
            </div>

          </v-col>

          <v-col cols="12" md="4" lg="4">
            <div class="position-sticky" style="top: 24px;">
              
              <v-card v-if="club.currentUserRole === 'admin'" class="rounded-xl border-opacity-25 bg-surface mb-6 pa-6 text-center" border elevation="2" style="border-color: rgb(var(--v-theme-primary)) !important;">
                <v-avatar color="primary" variant="tonal" size="56" class="mb-4">
                  <v-icon icon="mdi-shield-crown-outline" size="32"></v-icon>
                </v-avatar>
                <h3 class="text-h6 font-weight-bold text-primary mb-2">You Manage This Page</h3>
                <p class="text-body-2 text-medium-emphasis mb-6">Post announcements, schedule events, and update club details.</p>
                <v-btn 
                  block
                  color="primary" 
                  size="x-large"
                  class="text-none font-weight-black rounded-lg"
                  @click="router.push(`/clubs/${club.id}/manage`)"
                >
                  <v-icon start icon="mdi-pencil-ruler"></v-icon>
                  Manage Club
                </v-btn>
              </v-card>

              <v-card v-else class="rounded-xl border-opacity-25 bg-surface mb-6 pa-6 text-center" border elevation="2">
                <h3 class="text-h6 font-weight-bold text-high-emphasis mb-2">Want to be part of this?</h3>
                <p class="text-body-2 text-medium-emphasis mb-4">Join {{ club.membersCount }} other students currently shaping the future.</p>
                <v-btn 
                  block
                  :color="club.currentUserRole ? 'success' : 'primary'" 
                  :variant="club.currentUserRole ? 'tonal' : 'flat'"
                  size="x-large"
                  class="text-none font-weight-black rounded-lg"
                  @click="handleToggleMembership"
                >
                  <v-icon start :icon="club.currentUserRole ? 'mdi-check-circle' : 'mdi-plus-circle-outline'"></v-icon>
                  {{ club.currentUserRole ? 'You are a Member' : 'Join Now' }}
                </v-btn>
              </v-card>

              <v-card v-if="leaders.length > 0" class="rounded-xl border-opacity-25 bg-surface mb-6" border elevation="1">
                <div class="pa-4 border-b border-opacity-25 font-weight-black text-uppercase text-caption text-high-emphasis tracking-widest d-flex align-center">
                  <v-icon icon="mdi-account-tie-outline" size="16" class="mr-2 text-primary"></v-icon> Leadership Team
                </div>
                <v-list lines="two" class="py-2 bg-transparent">
                  <v-list-item v-for="leader in leaders" :key="leader.id" class="px-5 py-3">
                    <template v-slot:prepend>
                      <v-avatar size="48" class="mr-4 elevation-1">
                        <v-img :src="leader.avatar" cover></v-img>
                      </v-avatar>
                    </template>
                    <v-list-item-title class="font-weight-bold text-high-emphasis text-body-1">{{ leader.name }}</v-list-item-title>
                    <v-list-item-subtitle class="font-weight-bold text-primary mt-1">{{ leader.role }}</v-list-item-subtitle>
                  </v-list-item>
                </v-list>
              </v-card>

              <v-card class="rounded-xl border-opacity-25 bg-surface" border elevation="1">
                <div class="pa-4 border-b border-opacity-25 font-weight-black text-uppercase text-caption text-high-emphasis tracking-widest d-flex align-center">
                  <v-icon icon="mdi-map-marker-outline" size="16" class="mr-2 text-primary"></v-icon> Where to find us
                </div>
                <div class="pa-5">
                  <div class="d-flex align-start mb-4">
                    <v-avatar color="primary" variant="tonal" size="40" class="mr-4"><v-icon icon="mdi-clock-outline"></v-icon></v-avatar>
                    <div>
                      <div class="font-weight-bold text-high-emphasis mb-1">Standard Meeting</div>
                      <div class="text-body-2 text-medium-emphasis">{{ club.meeting }}</div>
                    </div>
                  </div>
                  <div class="d-flex align-start">
                    <v-avatar color="primary" variant="tonal" size="40" class="mr-4"><v-icon icon="mdi-email-outline"></v-icon></v-avatar>
                    <div>
                      <div class="font-weight-bold text-high-emphasis mb-1">Contact Email</div>
                      <div class="text-body-2 text-medium-emphasis">{{ club.contactEmail || 'Not Provided' }}</div>
                    </div>
                  </div>
                </div>
              </v-card>

            </div>
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

.text-shadow { text-shadow: 0px 2px 6px rgba(0,0,0,0.8); }
.leading-tight { line-height: 1.2 !important; }
.leading-relaxed { line-height: 1.6 !important; }
.tracking-widest { letter-spacing: 0.1em !important; }

.border-white { border-color: rgb(var(--v-theme-surface)) !important; }

@media (max-width: 599px) {
  .v-card.d-flex.flex-sm-row {
    flex-direction: column !important;
  }
  .v-card .v-img {
    max-width: 100% !important;
  }
}
</style>