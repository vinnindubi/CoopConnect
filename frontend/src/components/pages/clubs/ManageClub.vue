<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useGroups } from '@/composables/useGroups';

const route = useRoute();
const router = useRouter();

// Pull in ALL our CRUD functions
const { 
  fetchGroupById, 
  createPost, 
  deletePost,
  fetchMembers, 
  changeMemberRole, 
  kickMember, 
  saveGroupSettings,
  addEvent,
  removeEvent,
  addAchievement,
  removeAchievement
} = useGroups(); 

const isLoading = ref(true);
const showSnackbar = ref(false);
const snackbarText = ref('');
const activeTab = ref('announcements');

// --- Global State ---
const club = ref<any>(null);

// --- Tab 1: Announcements State ---
const announcements = ref<any[]>([]);
const newPostContent = ref('');
const isSubmittingPost = ref(false);

// --- Tab 2: Members State ---
const members = ref<any[]>([]);

// --- Tab 3: Settings State ---
const isSavingSettings = ref(false);

// --- Tab 4: Milestones State ---
const plans = ref<any[]>([]);
const successes = ref<any[]>([]);

const isSubmittingEvent = ref(false);
const newEvent = ref({ 
  title: '', 
  event_type: 'Major Event', 
  event_date: '', 
  description: '' 
});

const isSubmittingAchievement = ref(false);
const newAchievement = ref({ 
  title: '', 
  date_achieved: '', 
  description: '',
  image_path: '' 
});

// --- Utility ---
const showToast = (message: string) => {
  snackbarText.value = message;
  showSnackbar.value = true;
};

// --- Methods: Announcements ---
const submitPost = async () => {
  if (!newPostContent.value.trim() || !club.value) return;
  isSubmittingPost.value = true;
  try {
    const response = await createPost(club.value.id, newPostContent.value);
    announcements.value.unshift(response.post);
    newPostContent.value = ''; 
    showToast('Announcement published successfully!');
  } catch (error) {
    showToast('Failed to publish post.');
  } finally {
    isSubmittingPost.value = false;
  }
};

const handleDeletePost = async (postId: number) => {
  if (!confirm('Are you sure you want to delete this post?')) return;
  try {
    await deletePost(club.value.id, postId);
    announcements.value = announcements.value.filter(p => p.id !== postId);
    showToast('Post deleted.');
  } catch (error) {
    showToast('Failed to delete post.');
  }
};

// --- Methods: Members ---
const handlePromoteToAdmin = async (member: any) => {
  try {
    await changeMemberRole(club.value.id, member.id, 'admin', 'Committee Member');
    member.role = 'admin';
    member.title = 'Committee Member';
    showToast(`${member.name} promoted to Admin.`);
  } catch (error) {
    showToast('Failed to promote user.');
  }
};

const handleDemoteToMember = async (member: any) => {
  try {
    await changeMemberRole(club.value.id, member.id, 'member', null);
    member.role = 'member';
    member.title = null;
    showToast(`${member.name} demoted to regular member.`);
  } catch (error) {
    showToast('Failed to demote user.');
  }
};

const handleKickMember = async (memberId: number, memberName: string) => {
  if (!confirm(`Are you sure you want to remove ${memberName} from the club?`)) return;
  try {
    await kickMember(club.value.id, memberId);
    members.value = members.value.filter(m => m.id !== memberId);
    showToast(`${memberName} has been removed from the club.`);
  } catch (error) {
    showToast('Failed to remove user.');
  }
};

// --- Methods: Settings ---
const handleSaveSettings = async () => {
  isSavingSettings.value = true;
  try {
    await saveGroupSettings(club.value.id, {
      meeting_time: club.value.meeting,
      contact_email: club.value.contactEmail,
      mission: club.value.mission
    });
    showToast('Club settings updated successfully!');
  } catch (error) {
    showToast('Failed to update settings.');
  } finally {
    isSavingSettings.value = false;
  }
};

// --- Methods: Milestones ---
const handleAddEvent = async () => {
  // 1. Make sure ALL required fields are filled out!
  if (!newEvent.value.title || !newEvent.value.event_date || !newEvent.value.description) {
    showToast('Please fill in the title, date, and description.');
    return;
  }
  
  isSubmittingEvent.value = true;
  try {
    const res = await addEvent(club.value.id, newEvent.value);
    plans.value.push(res.event);
    newEvent.value = { title: '', event_type: 'Major Event', event_date: '', description: '' };
    showToast('Plan added successfully.');
  } catch (error: any) { 
    // 2. Log the EXACT error Laravel sends back to your browser console
    console.error("Laravel Error:", error.response?.data || error.message);
    showToast(error.response?.data?.message || 'Failed to add plan.'); 
  } finally { 
    isSubmittingEvent.value = false; 
  }
};

const handleDeleteEvent = async (id: number) => {
  if (!confirm('Delete this plan?')) return;
  try {
    await removeEvent(club.value.id, id);
    plans.value = plans.value.filter(e => e.id !== id);
    showToast('Plan deleted.');
  } catch(e) {
    showToast('Failed to delete plan.');
  }
};

const handleAddAchievement = async () => {
  // 1. Make sure required fields are filled out!
  if (!newAchievement.value.title || !newAchievement.value.date_achieved || !newAchievement.value.description) {
    showToast('Please fill in the title, date, and description.');
    return;
  }

  isSubmittingAchievement.value = true;
  try {
    const res = await addAchievement(club.value.id, newAchievement.value);
    successes.value.unshift(res.achievement);
    newAchievement.value = { title: '', date_achieved: '', description: '', image_path: '' };
    showToast('Achievement added successfully.');
  } catch (error: any) { 
    // 2. Log the EXACT error to the console
    console.error("Laravel Error:", error.response?.data || error.message);
    showToast(error.response?.data?.message || 'Failed to add achievement.'); 
  } finally { 
    isSubmittingAchievement.value = false; 
  }
};

const handleDeleteAchievement = async (id: number) => {
  if (!confirm('Delete this achievement?')) return;
  try {
    await removeAchievement(club.value.id, id);
    successes.value = successes.value.filter(a => a.id !== id);
    showToast('Achievement deleted.');
  } catch(e) {
    showToast('Failed to delete achievement.');
  }
};

// --- Lifecycle ---
onMounted(async () => {
  const clubId = Number(route.params.id);
  try {
    const data = await fetchGroupById(clubId);
    
    // SECURITY CHECK
    if (data.currentUserRole !== 'admin') {
      router.push(`/clubs/${clubId}`);
      return;
    }

    club.value = data;
    announcements.value = data.posts || [];
    plans.value = data.events || [];
    successes.value = data.achievements || [];

    const membersData = await fetchMembers(clubId);
    members.value = membersData;
    
    isLoading.value = false;
  } catch (error) {
    router.push('/clubsAndSocieties'); 
  }
});
</script>

<template>
  <v-container fluid class="bg-background min-vh-100 pa-4 pa-md-8">
    
    <v-overlay :model-value="isLoading" class="align-center justify-center" persistent>
      <v-progress-circular color="primary" indeterminate size="64"></v-progress-circular>
    </v-overlay>

    <div v-if="!isLoading && club" style="max-width: 1000px; margin: 0 auto;">
      
      <div class="d-flex align-center justify-space-between mb-8">
        <div>
          <v-btn variant="text" prepend-icon="mdi-arrow-left" class="mb-2 px-0 text-medium-emphasis" @click="router.push(`/clubsAndSocieties/${club.id}`)">
            Back to Public Profile
          </v-btn>
          <h1 class="text-h4 font-weight-black text-high-emphasis">Manage: {{ club.name }}</h1>
        </div>
        <v-avatar size="64" class="elevation-2">
          <v-img :src="club.image" cover></v-img>
        </v-avatar>
      </div>

      <v-card class="rounded-xl border-opacity-25 bg-surface mb-6" border elevation="1">
        
        <v-tabs v-model="activeTab" color="primary" align-tabs="center" class="border-b border-opacity-25">
          <v-tab value="announcements" class="text-none font-weight-bold"><v-icon start icon="mdi-bullhorn"></v-icon> Announcements</v-tab>
          <v-tab value="members" class="text-none font-weight-bold"><v-icon start icon="mdi-account-group"></v-icon> Members</v-tab>
          <v-tab value="milestones" class="text-none font-weight-bold"><v-icon start icon="mdi-flag-checkered"></v-icon> Milestones</v-tab>
          <v-tab value="settings" class="text-none font-weight-bold"><v-icon start icon="mdi-cog"></v-icon> Settings</v-tab>
        </v-tabs>

        <v-card-text class="pa-0">
          <v-window v-model="activeTab">
            
            <v-window-item value="announcements" class="pa-6">
              <h3 class="text-h6 font-weight-bold mb-4">Post a new announcement</h3>
              <v-textarea v-model="newPostContent" placeholder="What do you want to share with the club?" variant="outlined" rows="3" auto-grow class="mb-2" hide-details></v-textarea>
              <div class="d-flex justify-end mb-8">
                <v-btn color="primary" class="text-none font-weight-bold rounded-lg px-6" :loading="isSubmittingPost" :disabled="!newPostContent.trim()" @click="submitPost">
                  <v-icon start icon="mdi-send"></v-icon> Publish Post
                </v-btn>
              </div>

              <v-divider class="mb-6"></v-divider>
              <h3 class="text-h6 font-weight-bold mb-4">Past Announcements</h3>
              
              <v-card v-for="post in announcements" :key="post.id" class="mb-4 rounded-lg bg-background border border-opacity-25" elevation="0">
                <div class="pa-4 d-flex align-start justify-space-between">
                  <div>
                    <div class="text-caption text-medium-emphasis mb-1">{{ post.timeAgo || 'Just now' }}</div>
                    <p class="text-body-1 mb-0">{{ post.content }}</p>
                  </div>
                  <v-btn icon="mdi-delete-outline" variant="text" color="error" size="small" @click="handleDeletePost(post.id)"></v-btn>
                </div>
              </v-card>
              
              <div v-if="announcements.length === 0" class="text-medium-emphasis text-center py-4">
                No announcements posted yet.
              </div>
            </v-window-item>

            <v-window-item value="members" class="pa-0">
              <v-table class="bg-transparent">
                <thead>
                  <tr>
                    <th class="text-left font-weight-bold">Member</th>
                    <th class="text-left font-weight-bold">Role</th>
                    <th class="text-right font-weight-bold">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="member in members" :key="member.id">
                    <td class="py-3">
                      <div class="d-flex align-center">
                        <v-avatar size="40" class="mr-3"><v-img :src="member.avatar"></v-img></v-avatar>
                        <div>
                          <div class="font-weight-bold text-high-emphasis">{{ member.name }}</div>
                          <div class="text-caption text-medium-emphasis">{{ member.email }}</div>
                        </div>
                      </div>
                    </td>
                    <td>
                      <v-chip size="small" :color="member.role === 'admin' ? 'primary' : 'default'" class="font-weight-bold text-uppercase">
                        {{ member.role }}
                      </v-chip>
                      <div v-if="member.title" class="text-caption text-medium-emphasis mt-1">{{ member.title }}</div>
                    </td>
                    <td class="text-right">
                      <v-menu offset-y>
                        <template v-slot:activator="{ props }">
                          <v-btn icon="mdi-dots-vertical" variant="text" size="small" v-bind="props"></v-btn>
                        </template>
                        <v-list class="bg-surface elevation-3 rounded-lg">
                          <v-list-item v-if="member.role !== 'admin'" @click="handlePromoteToAdmin(member)">
                            <v-icon start color="success" icon="mdi-shield-arrow-up-outline"></v-icon> Promote to Admin
                          </v-list-item>
                          <v-list-item v-if="member.role === 'admin'" @click="handleDemoteToMember(member)">
                            <v-icon start color="warning" icon="mdi-shield-arrow-down-outline"></v-icon> Demote to Member
                          </v-list-item>
                          <v-divider></v-divider>
                          <v-list-item @click="handleKickMember(member.id, member.name)">
                            <v-icon start color="error" icon="mdi-account-remove-outline"></v-icon> Remove from Club
                          </v-list-item>
                        </v-list>
                      </v-menu>
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </v-window-item>

            <v-window-item value="milestones" class="pa-6 bg-surface">
              
              <div class="d-flex align-center justify-space-between mb-4">
                <h3 class="text-h6 font-weight-bold text-primary"><v-icon start icon="mdi-rocket-launch-outline"></v-icon> Future Plans & Roadmap</h3>
              </div>
              
              <v-card class="pa-4 mb-6 border border-opacity-25 bg-background" elevation="0" rounded="lg">
                <v-row>
                  <v-col cols="12" sm="6"><v-text-field v-model="newEvent.title" label="Event Title" variant="outlined" density="comfortable" hide-details></v-text-field></v-col>
                  <v-col cols="12" sm="6"><v-select v-model="newEvent.event_type" :items="['Major Event', 'Community Plan', 'Workshop', 'Competition']" label="Type" variant="outlined" density="comfortable" hide-details></v-select></v-col>
                  <v-col cols="12" sm="4"><v-text-field v-model="newEvent.event_date" type="date" label="Date" variant="outlined" density="comfortable" hide-details></v-text-field></v-col>
                  <v-col cols="12" sm="8"><v-text-field v-model="newEvent.description" label="Short Description" variant="outlined" density="comfortable" hide-details></v-text-field></v-col>
                </v-row>
                <div class="d-flex justify-end mt-4">
                  <v-btn color="primary" class="text-none font-weight-bold" :loading="isSubmittingEvent" @click="handleAddEvent">Add Plan</v-btn>
                </div>
              </v-card>

              <v-list class="bg-transparent mb-8">
                <v-list-item v-for="plan in plans" :key="plan.id" class="border border-opacity-25 rounded-lg mb-2 bg-surface">
                  <template v-slot:prepend><v-icon icon="mdi-calendar-star" color="primary"></v-icon></template>
                  <v-list-item-title class="font-weight-bold">{{ plan.title }} <span class="text-caption text-medium-emphasis ml-2">({{ plan.event_date || plan.date }})</span></v-list-item-title>
                  <v-list-item-subtitle>{{ plan.description || plan.desc }}</v-list-item-subtitle>
                  <template v-slot:append>
                    <v-btn icon="mdi-delete-outline" variant="text" color="error" size="small" @click="handleDeleteEvent(plan.id)"></v-btn>
                  </template>
                </v-list-item>
              </v-list>

              <v-divider class="mb-8"></v-divider>

              <div class="d-flex align-center justify-space-between mb-4">
                <h3 class="text-h6 font-weight-bold text-warning"><v-icon start icon="mdi-trophy-award"></v-icon> Track Record & Successes</h3>
              </div>

              <v-card class="pa-4 mb-6 border border-opacity-25 bg-background" elevation="0" rounded="lg">
                <v-row>
                  <v-col cols="12" sm="8"><v-text-field v-model="newAchievement.title" label="Achievement Title" variant="outlined" density="comfortable" hide-details></v-text-field></v-col>
                  <v-col cols="12" sm="4"><v-text-field v-model="newAchievement.date_achieved" label="Date (e.g., Aug 2025)" variant="outlined" density="comfortable" hide-details></v-text-field></v-col>
                  <v-col cols="12">
                    <v-text-field v-model="newAchievement.image_path" label="Image Link / URL (Optional)" placeholder="https://images.unsplash.com/..." variant="outlined" density="comfortable" hide-details prepend-inner-icon="mdi-link"></v-text-field>
                  </v-col>
                  <v-col cols="12"><v-textarea v-model="newAchievement.description" label="Detailed Description" rows="2" auto-grow variant="outlined" density="comfortable" hide-details></v-textarea></v-col>
                </v-row>
                <div class="d-flex justify-end mt-4">
                  <v-btn color="warning" class="text-none font-weight-bold text-grey-darken-4" :loading="isSubmittingAchievement" @click="handleAddAchievement">
                    <v-icon start icon="mdi-plus"></v-icon> Add Success
                  </v-btn>
                </div>
              </v-card>

              <v-list class="bg-transparent">
                <v-list-item v-for="success in successes" :key="success.id" class="border border-opacity-25 rounded-lg mb-2 bg-surface">
                  <template v-slot:prepend><v-icon icon="mdi-medal-outline" color="warning"></v-icon></template>
                  <v-list-item-title class="font-weight-bold">{{ success.title }} <span class="text-caption text-medium-emphasis ml-2">({{ success.date_achieved || success.date }})</span></v-list-item-title>
                  <v-list-item-subtitle class="line-clamp-2">{{ success.description || success.desc }}</v-list-item-subtitle>
                  <template v-slot:append>
                    <v-btn icon="mdi-delete-outline" variant="text" color="error" size="small" @click="handleDeleteAchievement(success.id)"></v-btn>
                  </template>
                </v-list-item>
              </v-list>

            </v-window-item>

            <v-window-item value="settings" class="pa-6">
              <h3 class="text-h6 font-weight-bold mb-4">Edit Club Details</h3>
              <v-text-field label="Meeting Time" v-model="club.meeting" variant="outlined" class="mb-4"></v-text-field>
              <v-text-field label="Contact Email" v-model="club.contactEmail" variant="outlined" class="mb-4"></v-text-field>
              <v-textarea label="Club Mission" v-model="club.mission" variant="outlined" rows="3"></v-textarea>
              <div class="d-flex justify-end mt-4">
                <v-btn color="primary" class="px-8 font-weight-bold rounded-lg text-none" :loading="isSavingSettings" @click="handleSaveSettings">Save Changes</v-btn>
              </div>
            </v-window-item>

          </v-window>
        </v-card-text>
      </v-card>
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
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>