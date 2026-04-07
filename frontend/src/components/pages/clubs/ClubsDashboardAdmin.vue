<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';

// --- UI State ---
const activeTab = ref('posts');
const showSnackbar = ref(false);
const snackbarText = ref('');
const isNewPostDialogOpen = ref(false);
const newPostContent = ref('');

// Simulate fetching data for the currently managed club
const isLoading = ref(true);

const triggerAction = (message: string) => {
  snackbarText.value = message;
  showSnackbar.value = true;
};

// --- Mock Data: The Club ---
const club = ref({
  id: 1,
  name: 'Tech Innovators Club',
  category: 'Technology',
  membersCount: 142,
  coverImage: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=1200',
  description: 'Building the future of software and hardware on campus. Join us for hackathons and coding sessions!'
});

// --- Mock Data: Members (Simulating the group_user pivot table) ---
const members = ref([
  { id: 101, name: 'Alice Wanjiku', admission: 'BITC/202/2022', role: 'admin', joined: 'Oct 1, 2025' }, // The Chairperson
  { id: 102, name: 'Brian Kiprono', admission: 'BCS/112/2024', role: 'member', joined: 'Oct 5, 2025' },
  { id: 103, name: 'Catherine Mutua', admission: 'BCOM/305/2023', role: 'member', joined: 'Oct 10, 2025' },
  { id: 104, name: 'David Omondi', admission: 'BITC/189/2022', role: 'admin', joined: 'Oct 12, 2025' }, // Co-admin
]);

// --- Mock Data: Posts ---
const posts = ref([
  { id: 1, author: 'Alice Wanjiku', content: 'Welcome to the new semester! Our first hackathon is scheduled for next month. Start forming your teams!', timeAgo: '2 hours ago', likes: 14 },
  { id: 2, author: 'David Omondi', content: 'Reminder: Weekly coding session happens this Friday at Lab 3. We will be covering Vue.js basics.', timeAgo: '2 days ago', likes: 32 }
]);

// --- Methods ---
const promoteToAdmin = (memberId: number) => {
  const member = members.value.find(m => m.id === memberId);
  if (member) {
    member.role = 'admin';
    triggerAction(`${member.name} promoted to Admin!`);
  }
};

const demoteToMember = (memberId: number) => {
  const member = members.value.find(m => m.id === memberId);
  if (member) {
    member.role = 'member';
    triggerAction(`${member.name} demoted to regular member.`);
  }
};

const removeMember = (memberId: number) => {
  members.value = members.value.filter(m => m.id !== memberId);
  club.value.membersCount--;
  triggerAction(`Member removed from the club.`);
};

const publishPost = () => {
  if (!newPostContent.value) return;
  
  // Create a fake new post and add it to the top of the array
  const newPost = {
    id: Date.now(),
    author: 'Alice Wanjiku', // Simulating logged-in user
    content: newPostContent.value,
    timeAgo: 'Just now',
    likes: 0
  };
  
  posts.value.unshift(newPost);
  newPostContent.value = '';
  isNewPostDialogOpen.value = false;
  triggerAction('Announcement published successfully!');
};

// Simulate API load time
onMounted(() => {
  setTimeout(() => {
    isLoading.value = false;
  }, 600);
});
</script>

<template>
  <v-container fluid class="bg-background min-vh-100 pa-0 pb-10">
    
    <v-overlay :model-value="isLoading" class="align-center justify-center" persistent>
      <v-progress-circular color="primary" indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <v-img :src="club.coverImage" height="250" cover class="bg-surface-variant position-relative">
      <div class="position-absolute w-100 h-100 bg-black opacity-40"></div>
      
      <v-container style="max-width: 1000px; height: 100%;" class="d-flex align-end pb-6 position-relative z-index-1">
        <div class="text-white w-100">
          <div class="d-flex justify-space-between align-end">
            <div>
              <v-chip color="white" variant="outlined" size="small" class="mb-3 font-weight-bold opacity-90 backdrop-blur">
                {{ club.category }}
              </v-chip>
              <h1 class="text-h3 font-weight-black mb-1 text-shadow">{{ club.name }}</h1>
              <p class="text-body-1 opacity-90 mb-0 d-flex align-center">
                <v-icon icon="mdi-account-group" size="20" class="mr-2"></v-icon>
                {{ club.membersCount }} Members
              </p>
            </div>
            
            <v-chip color="primary" variant="flat" size="large" class="font-weight-black elevation-4">
              <v-icon start icon="mdi-shield-crown"></v-icon> Admin View
            </v-chip>
          </div>
        </div>
      </v-container>
    </v-img>

    <v-container style="max-width: 1000px;" class="mt-n8 position-relative z-index-2">
      <v-card class="rounded-xl border-opacity-25 bg-surface mb-6" border elevation="3">
        
        <v-tabs v-model="activeTab" color="primary" grow class="border-b border-opacity-25">
          <v-tab value="posts" class="font-weight-bold text-none"><v-icon start icon="mdi-bullhorn-outline"></v-icon> Announcements</v-tab>
          <v-tab value="members" class="font-weight-bold text-none"><v-icon start icon="mdi-account-multiple-outline"></v-icon> Manage Members</v-tab>
          <v-tab value="settings" class="font-weight-bold text-none"><v-icon start icon="mdi-cog-outline"></v-icon> Club Settings</v-tab>
        </v-tabs>

        <v-window v-model="activeTab" class="bg-surface">
          
          <v-window-item value="posts" class="pa-6">
            <div class="d-flex justify-space-between align-center mb-6">
              <h2 class="text-h6 font-weight-bold text-high-emphasis">Club Feed</h2>
              <v-btn color="primary" variant="flat" class="text-none font-weight-bold rounded-lg" prepend-icon="mdi-pencil" @click="isNewPostDialogOpen = true">
                New Announcement
              </v-btn>
            </div>

            <div v-if="posts.length > 0" class="d-flex flex-column gap-4">
              <v-card v-for="post in posts" :key="post.id" variant="outlined" class="rounded-xl border-opacity-25 pa-5 bg-surface-variant">
                <div class="d-flex align-center mb-3">
                  <v-avatar color="primary" variant="tonal" class="mr-3 font-weight-bold">{{ post.author.charAt(0) }}</v-avatar>
                  <div>
                    <div class="text-subtitle-2 font-weight-bold text-high-emphasis">{{ post.author }}</div>
                    <div class="text-caption text-medium-emphasis">{{ post.timeAgo }}</div>
                  </div>
                  <v-spacer></v-spacer>
                  <v-btn icon="mdi-dots-horizontal" variant="text" density="comfortable" color="medium-emphasis"></v-btn>
                </div>
                <p class="text-body-1 text-high-emphasis mb-4">{{ post.content }}</p>
                <div class="d-flex align-center text-medium-emphasis">
                  <v-btn variant="text" density="compact" class="text-none mr-2"><v-icon start icon="mdi-heart-outline"></v-icon> {{ post.likes }} Likes</v-btn>
                </div>
              </v-card>
            </div>
          </v-window-item>

          <v-window-item value="members" class="pa-0">
            <div class="pa-6 border-b border-opacity-25 d-flex justify-space-between align-center">
              <div>
                <h2 class="text-h6 font-weight-bold text-high-emphasis">Member Directory</h2>
                <p class="text-caption text-medium-emphasis mb-0">Assign admin roles to help manage the club.</p>
              </div>
              <v-text-field density="compact" variant="solo-filled" flat placeholder="Search members..." prepend-inner-icon="mdi-magnify" hide-details class="rounded-lg" style="max-width: 250px;"></v-text-field>
            </div>

            <v-table hover class="bg-transparent">
              <thead class="bg-surface-variant">
                <tr>
                  <th class="font-weight-bold text-caption text-uppercase text-medium-emphasis">Student</th>
                  <th class="font-weight-bold text-caption text-uppercase text-medium-emphasis">Joined</th>
                  <th class="font-weight-bold text-caption text-uppercase text-medium-emphasis text-center">Club Role</th>
                  <th class="font-weight-bold text-caption text-uppercase text-medium-emphasis text-right">Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="member in members" :key="member.id">
                  <td class="py-3">
                    <div class="font-weight-bold text-high-emphasis">{{ member.name }}</div>
                    <div class="text-caption text-medium-emphasis">{{ member.admission }}</div>
                  </td>
                  <td class="text-body-2 text-medium-emphasis">{{ member.joined }}</td>
                  <td class="text-center">
                    <v-chip size="small" :color="member.role === 'admin' ? 'primary' : 'grey'" :variant="member.role === 'admin' ? 'flat' : 'tonal'" class="font-weight-bold text-uppercase">
                      <v-icon v-if="member.role === 'admin'" start icon="mdi-shield-account" size="14"></v-icon>
                      {{ member.role }}
                    </v-chip>
                  </td>
                  <td class="text-right">
                    <v-menu location="bottom end">
                      <template v-slot:activator="{ props }">
                        <v-btn icon="mdi-dots-vertical" variant="text" density="comfortable" v-bind="props" color="medium-emphasis"></v-btn>
                      </template>
                      <v-list class="bg-surface border border-opacity-25 elevation-3 rounded-lg" min-width="180">
                        
                        <v-list-item v-if="member.role === 'member'" @click="promoteToAdmin(member.id)" prepend-icon="mdi-arrow-up-bold" class="text-primary font-weight-bold">
                          Promote to Admin
                        </v-list-item>
                        
                        <v-list-item v-if="member.role === 'admin'" @click="demoteToMember(member.id)" prepend-icon="mdi-arrow-down-bold" class="text-warning font-weight-bold">
                          Remove Admin
                        </v-list-item>
                        
                        <v-divider class="my-1 border-opacity-25"></v-divider>
                        
                        <v-list-item @click="removeMember(member.id)" prepend-icon="mdi-account-remove" class="text-error font-weight-bold">
                          Kick from Club
                        </v-list-item>

                      </v-list>
                    </v-menu>
                  </td>
                </tr>
              </tbody>
            </v-table>
          </v-window-item>

          <v-window-item value="settings" class="pa-6">
            <h2 class="text-h6 font-weight-bold text-high-emphasis mb-4">Edit Club Profile</h2>
            <v-form>
              <v-text-field label="Club Name" :model-value="club.name" variant="outlined" class="rounded-lg mb-4"></v-text-field>
              <v-textarea label="Description" :model-value="club.description" variant="outlined" rows="3" class="rounded-lg mb-4"></v-textarea>
              <v-btn color="primary" class="font-weight-bold text-none rounded-lg px-6" size="large">Save Changes</v-btn>
            </v-form>
          </v-window-item>

        </v-window>
      </v-card>
    </v-container>

    <v-dialog v-model="isNewPostDialogOpen" max-width="600">
      <v-card class="rounded-xl bg-surface pa-2">
        <v-card-title class="font-weight-black text-high-emphasis border-b border-opacity-25 pb-4 mb-4">
          Create Announcement
        </v-card-title>
        <v-card-text>
          <v-textarea
            v-model="newPostContent"
            variant="outlined"
            placeholder="What's happening with the club? Share updates, meeting links, or news..."
            rows="5"
            auto-grow
            hide-details
            class="rounded-lg"
          ></v-textarea>
        </v-card-text>
        <v-card-actions class="px-6 pb-6 pt-2">
          <v-spacer></v-spacer>
          <v-btn variant="text" color="medium-emphasis" class="text-none font-weight-bold mr-2" @click="isNewPostDialogOpen = false">Cancel</v-btn>
          <v-btn color="primary" variant="flat" class="text-none font-weight-bold rounded-lg px-6" @click="publishPost" :disabled="!newPostContent">Post to Feed</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

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

/* Subtle text shadow to make text readable over images */
.text-shadow {
  text-shadow: 0px 2px 4px rgba(0,0,0,0.7);
}

.backdrop-blur {
  backdrop-filter: blur(4px);
}
</style>