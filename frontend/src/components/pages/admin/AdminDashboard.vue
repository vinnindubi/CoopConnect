<script setup lang="ts">
import { onMounted, ref, computed } from 'vue';
import { getAllUsers, getPendingVerifications } from '@/services/authService';

// ==========================================
// 1. UI STATE
// ==========================================
const activeTab = ref('overview');
const showSnackbar = ref(false);
const snackbarText = ref('');
const snackbarColor = ref('success');

const triggerAction = (message: string, color: string = 'success') => {
  snackbarText.value = message;
  snackbarColor.value = color;
  showSnackbar.value = true;
};

// ==========================================
// 2. MOCK DATA & API ARRAYS
// ==========================================
const users = ref<any[]>([]);
const pendingVerifications = ref<any[]>([]);
const reportedContent = ref([
  { id: 301, type: 'Product', author: 'Mike T.', reason: 'Counterfeit Item', content: 'Selling cheap knock-off AirPods as original.' },
  { id: 302, type: 'Comment', author: 'Anonymous', reason: 'Harassment', content: 'You are so stupid for posting this.' },
]);

// NEW: Pending Group Registrations
const pendingGroupApps = ref([
  {
    id: 'REQ-089',
    groupType: 'Club',
    name: 'Campus Photography Club',
    category: 'Media',
    description: 'A community for students to learn photography, share equipment, and cover major university events.',
    meetingTime: 'Saturdays 2 PM @ Main Amphitheater',
    leaderName: 'Alice Wanjiku',
    leaderAdmission: 'BITC/202/2022',
    leaderPhone: '0712 345 678',
    patronName: 'Dr. Kamau (Media Studies)',
    submittedAt: 'Oct 12, 2025',
    documentName: 'Photography_Club_Constitution.pdf'
  },
  {
    id: 'REQ-090',
    groupType: 'Society',
    name: 'Anglican Students Fellowship',
    category: 'Christian',
    description: 'Providing spiritual nourishment, Sunday services, and charity outreach for Anglican students.',
    meetingTime: 'Sundays 10 AM @ Room 4B',
    leaderName: 'David Omondi',
    leaderAdmission: 'BCOM/405/2023',
    leaderPhone: '0722 987 654',
    patronName: 'Rev. Mutuku',
    submittedAt: 'Oct 14, 2025',
    documentName: 'ASF_Proposal.pdf'
  }
]);

// DYNAMIC STATS: Automatically updates when you clear items!
const stats = computed(() => [
  { title: 'Total Users', value: users.value.length || '1,245', icon: 'mdi-account-group', color: 'indigo' },
  { title: 'Pending Sellers', value: pendingVerifications.value.length, icon: 'mdi-store-clock', color: 'amber-darken-2' },
  { title: 'Pending Groups', value: pendingGroupApps.value.length, icon: 'mdi-account-group-outline', color: 'blue-darken-2' },
  { title: 'Reported Content', value: reportedContent.value.length, icon: 'mdi-flag-triangle', color: 'red-darken-1' },
]);

// ==========================================
// 3. GROUP APPROVAL STATE
// ==========================================
const isReviewDialogOpen = ref(false);
const isRejectDialogOpen = ref(false);
const isProcessing = ref(false);
const rejectReason = ref('');
const activeApp = ref<any>(null);

// ==========================================
// 4. ACTIONS & METHODS
// ==========================================

// --- Existing Actions ---
const toggleUserStatus = (user: any) => {
  user.status = user.status === 'Active' ? 'Suspended' : 'Active';
  triggerAction(`User status updated to ${user.status}`);
};

const approveSeller = (id: number) => {
  pendingVerifications.value = pendingVerifications.value.filter(v => v.id !== id);
  triggerAction(`Seller #${id} Approved!`);
};

const rejectSeller = (id: number) => {
  pendingVerifications.value = pendingVerifications.value.filter(v => v.id !== id);
  triggerAction(`Seller #${id} Rejected.`, 'error');
};

const deleteContent = (id: number) => {
  reportedContent.value = reportedContent.value.filter(c => c.id !== id);
  triggerAction('Content deleted successfully.');
};

// --- NEW: Group Actions ---
const openGroupReview = (app: any) => {
  activeApp.value = app;
  isReviewDialogOpen.value = true;
};

const handleApproveGroup = () => {
  isProcessing.value = true;
  setTimeout(() => {
    pendingGroupApps.value = pendingGroupApps.value.filter(app => app.id !== activeApp.value.id);
    isProcessing.value = false;
    isReviewDialogOpen.value = false;
    triggerAction(`${activeApp.value.name} has been officially approved!`, 'success');
  }, 800);
};

const handleRejectGroup = () => {
  if (!rejectReason.value) return;
  isProcessing.value = true;
  setTimeout(() => {
    pendingGroupApps.value = pendingGroupApps.value.filter(app => app.id !== activeApp.value.id);
    isProcessing.value = false;
    isRejectDialogOpen.value = false;
    isReviewDialogOpen.value = false;
    rejectReason.value = ''; 
    triggerAction(`Group rejected. Email sent to ${activeApp.value.leaderName}.`, 'error');
  }, 800);
};

// --- API Calls ---
const getClient = async () => {
  try {
    const response = await getAllUsers();
    const rawUsers = response.data.data;     
    users.value = rawUsers.map((user: any) => ({
      id: user.id,
      name: user.fullname, 
      admission: user.admission,
      status: user.verification_status || 'Pending', 
    }));
  } catch (error) {
    console.error("Error fetching users:", error);
    // Don't trigger error if it's just a missing backend during dev
  }
}

const fetchPendingSellers = async () => {
  try {
    const response = await getPendingVerifications();
    const rawPending = response.data.data;
    pendingVerifications.value = rawPending.map((req: any) => ({
      id: req.id,
      name: req.fullname,
      storeCategories: req.store_categories || [], 
      submittedAt: new Date(req.updated_at).toLocaleDateString()
    }));
  } catch (error) {
    console.error("Error fetching pending verifications:", error);
  }
}

onMounted(() => {
  getClient();
  fetchPendingSellers();
});
</script>

<template>
  <v-container fluid class="pa-0 pa-md-6 bg-background min-vh-100">
    <v-container style="max-width: 1200px;">
      
      <div class="mb-8">
        <h1 class="text-h3 font-weight-black text-primary mb-2">Command Center</h1>
        <p class="text-body-1 text-medium-emphasis">Monitor activity, verify sellers, and keep the platform safe.</p>
      </div>

      <v-card class="rounded-xl border-opacity-25 bg-surface" border elevation="3">
        
        <v-tabs 
          v-model="activeTab" 
          color="primary" 
          align-tabs="center"
          grow
          height="64"
          class="bg-surface"
        >
          <v-tab value="overview" class="font-weight-bold text-none"><v-icon start icon="mdi-view-dashboard-outline"></v-icon> Overview</v-tab>
          <v-tab value="users" class="font-weight-bold text-none"><v-icon start icon="mdi-account-group-outline"></v-icon> Users</v-tab>
          
          <v-tab value="verifications" class="font-weight-bold text-none">
            <v-badge v-if="pendingVerifications.length" :content="pendingVerifications.length" color="amber-darken-2" class="mr-3"></v-badge>
            <v-icon start icon="mdi-shield-check-outline"></v-icon> Sellers
          </v-tab>
          
          <v-tab value="groups" class="font-weight-bold text-none">
            <v-badge v-if="pendingGroupApps.length" :content="pendingGroupApps.length" color="blue-darken-2" class="mr-3"></v-badge>
            <v-icon start icon="mdi-account-multiple-plus-outline"></v-icon> Groups
          </v-tab>
          
          <v-tab value="moderation" class="font-weight-bold text-none"><v-icon start icon="mdi-alert-octagon-outline"></v-icon> Moderation</v-tab>
        </v-tabs>

        <v-divider></v-divider>

        <v-window v-model="activeTab" class="bg-surface">
          
          <v-window-item value="overview" class="pa-4 pa-md-8">
            <v-row>
              <v-col v-for="stat in stats" :key="stat.title" cols="12" sm="6" md="3">
                <v-card class="rounded-xl border-opacity-50 h-100 d-flex flex-column transition-swing " hover border elevation="0">
                  <v-card-text class="d-flex justify-space-between align-center pa-6">
                    <div>
                      <div class="text-caption font-weight-black text-medium-emphasis mb-1 text-uppercase">{{ stat.title }}</div>
                      <div class="text-h3 font-weight-black text-high-emphasis">{{ stat.value }}</div>
                    </div>
                    <v-avatar :color="stat.color" variant="tonal" size="64" class="rounded-lg">
                      <v-icon :icon="stat.icon" size="32"></v-icon>
                    </v-avatar>
                  </v-card-text>
                  <div :class="`bg-${stat.color}`" style="height: 4px; width: 100%; margin-top: auto;"></div>
                </v-card>
              </v-col>
            </v-row>
          </v-window-item>

          <v-window-item value="users" class="pa-4 pa-md-8">
            <div class="d-flex justify-space-between align-end mb-6">
              <h2 class="text-h5 font-weight-black text-primary">Registered Students</h2>
              <v-text-field density="compact" variant="solo-filled" flat placeholder="Search students..." prepend-inner-icon="mdi-magnify" hide-details style="max-width: 300px;" class="rounded-lg"></v-text-field>
            </div>
            
            <v-card variant="outlined" class="border-opacity-25 rounded-lg overflow-hidden bg-surface">
              <v-table hover class="bg-transparent">
                <thead class="">
                  <tr>
                    <th class="text-left font-weight-bold text-uppercase text-caption text-medium-emphasis py-4">Student</th>
                    <th class="text-left font-weight-bold text-uppercase text-caption text-medium-emphasis py-4">Admission</th>
                    <th class="text-center font-weight-bold text-uppercase text-caption text-medium-emphasis py-4">Status</th>
                    <th class="text-right font-weight-bold text-uppercase text-caption text-medium-emphasis py-4">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in users" :key="user.id">
                    <td class="font-weight-bold py-3 text-high-emphasis">
                      <v-avatar size="32" color="indigo" variant="tonal" class="mr-3 text-caption font-weight-bold">
                        {{ user.name.charAt(0) }}
                      </v-avatar>
                      {{ user.name }}
                    </td>
                    <td class="text-medium-emphasis">{{ user.admission }}</td>
                    <td class="text-center">
                      <v-chip size="small" :color="user.status === 'Active' ? 'success' : 'error'" variant="flat" class="font-weight-bold px-4">
                        {{ user.status }}
                      </v-chip>
                    </td>
                    <td class="text-right">
                      <v-btn size="small" variant="text" :color="user.status === 'Active' ? 'error' : 'success'" class="font-weight-bold text-none rounded-lg" @click="toggleUserStatus(user)">
                        {{ user.status === 'Active' ? 'Suspend' : 'Reactivate' }}
                      </v-btn>
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </v-card>
          </v-window-item>

          <v-window-item value="verifications" class="pa-4 pa-md-8">
            <h2 class="text-h5 font-weight-black text-primary mb-6">Pending Sellers</h2>
            <v-row v-if="pendingVerifications.length > 0">
              <v-col cols="12" md="6" v-for="req in pendingVerifications" :key="req.id">
                <v-card variant="outlined" class="rounded-xl pa-5 border-opacity-25 ">
                  <div class="d-flex">
                    <v-avatar rounded="lg" size="80" class="mr-5 elevation-1">
                      <v-img :src="req.idProof || 'https://via.placeholder.com/150'" cover></v-img>
                    </v-avatar>
                    
                    <div class="flex-grow-1">
                      <div class="d-flex justify-space-between align-start mb-2">
                        <div class="text-h6 font-weight-bold text-high-emphasis">{{ req.name }}</div>
                        <v-chip size="x-small" color="medium-emphasis" variant="tonal" class="font-weight-bold">{{ req.submittedAt }}</v-chip>
                      </div>
                      
                      <div class="mb-4">
                        <div class="text-caption text-medium-emphasis font-weight-bold mb-2 text-uppercase">Categories</div>
                        <div v-if="req.storeCategories && req.storeCategories.length > 0" class="d-flex flex-wrap gap-1">
                          <v-chip v-for="cat in req.storeCategories" :key="cat" size="small" color="primary" variant="outlined" class="font-weight-bold border-opacity-50">
                            {{ cat }}
                          </v-chip>
                        </div>
                        <div v-else class="text-caption text-medium-emphasis font-italic">No categories selected.</div>
                      </div>
                      
                      <div class="d-flex gap-3 mt-4">
                        <v-btn flex-grow-1 color="success" variant="flat" class="text-none font-weight-bold rounded-lg" @click="approveSeller(req.id)">Approve</v-btn>
                        <v-btn flex-grow-1 color="error" variant="tonal" class="text-none font-weight-bold rounded-lg" @click="rejectSeller(req.id)">Deny</v-btn>
                      </div>
                    </div>
                  </div>
                </v-card>
              </v-col>
            </v-row>
            <div v-else class="text-center pa-12 text-medium-emphasis bg-surface-variant rounded-xl border border-dashed">
              <v-icon icon="mdi-check-decagram-outline" size="48" color="success" class="mb-3"></v-icon>
              <div class="text-h6 font-weight-bold text-high-emphasis">All Caught Up</div>
              <p>There are no pending seller applications.</p>
            </div>
          </v-window-item>

          <v-window-item value="groups" class="pa-4 pa-md-8">
            <h2 class="text-h5 font-weight-black text-primary mb-6">Group & Society Requests</h2>
            <div v-if="pendingGroupApps.length > 0">
              <v-list lines="two" class="bg-transparent py-0">
                <template v-for="(app, index) in pendingGroupApps" :key="app.id">
                  <v-list-item class="py-4 px-4 px-sm-6 hover-bg-variant transition-swing border rounded-lg mb-3 ">
                    <template v-slot:prepend>
                      <v-avatar :color="app.groupType === 'Club' ? 'blue-lighten-4' : 'purple-lighten-4'" size="48" class="mr-4">
                        <v-icon :icon="app.groupType === 'Club' ? 'mdi-palette-swatch' : 'mdi-hands-pray'" :color="app.groupType === 'Club' ? 'blue-darken-3' : 'purple-darken-3'"></v-icon>
                      </v-avatar>
                    </template>

                    <div class="d-flex flex-column flex-sm-row justify-space-between align-sm-center w-100">
                      <div class="mb-2 mb-sm-0 pr-sm-4">
                        <div class="d-flex align-center mb-1">
                          <v-chip size="x-small" :color="app.groupType === 'Club' ? 'blue' : 'purple'" variant="tonal" class="font-weight-bold text-uppercase mr-2">
                            {{ app.groupType }}
                          </v-chip>
                          <span class="text-caption text-medium-emphasis">{{ app.submittedAt }}</span>
                        </div>
                        <v-list-item-title class="text-h6 font-weight-bold text-high-emphasis leading-tight">{{ app.name }}</v-list-item-title>
                        <v-list-item-subtitle class="text-body-2 text-medium-emphasis">By: {{ app.leaderName }} ({{ app.leaderAdmission }})</v-list-item-subtitle>
                      </div>
                      
                      <v-btn color="primary" variant="tonal" class="text-none font-weight-bold rounded-lg shrink-0" @click="openGroupReview(app)">
                        Review Document
                      </v-btn>
                    </div>
                  </v-list-item>
                </template>
              </v-list>
            </div>
            
            <div v-else class="text-center pa-12 text-medium-emphasis bg-surface-variant rounded-xl border border-dashed">
              <v-icon icon="mdi-check-decagram-outline" size="48" color="success" class="mb-3"></v-icon>
              <div class="text-h6 font-weight-bold text-high-emphasis">All Caught Up</div>
              <p>There are no pending club registrations.</p>
            </div>
          </v-window-item>

          <v-window-item value="moderation" class="pa-4 pa-md-8">
            <h2 class="text-h5 font-weight-black text-primary mb-6">Flagged Content</h2>
            <v-row v-if="reportedContent.length > 0">
              <v-col cols="12" v-for="report in reportedContent" :key="report.id">
                <v-card variant="outlined" class="rounded-xl pa-5 border-opacity-50 border-error" color="surface-variant">
                  <div class="d-flex flex-column flex-md-row justify-space-between align-md-center mb-3">
                    <div class="d-flex align-center">
                      <v-chip size="small" color="error" variant="flat" class="font-weight-bold mr-3">
                        <v-icon start icon="mdi-flag" size="14"></v-icon> {{ report.reason }}
                      </v-chip>
                      <span class="text-caption font-weight-bold text-medium-emphasis text-uppercase">Reported {{ report.type }}</span>
                    </div>
                    <div class="text-caption text-medium-emphasis mt-2 mt-md-0">Flagged by: <strong class="text-high-emphasis">{{ report.author }}</strong></div>
                  </div>
                  
                  <v-card class="pa-4 bg-surface rounded-lg elevation-0 border mb-4">
                    <p class="text-body-1 font-italic text-high-emphasis mb-0">"{{ report.content }}"</p>
                  </v-card>

                  <div class="d-flex justify-end gap-3">
                    <v-btn variant="text" color="medium-emphasis" class="text-none font-weight-bold rounded-lg">Ignore</v-btn>
                    <v-btn color="error" variant="flat" prepend-icon="mdi-delete" class="text-none font-weight-bold rounded-lg" @click="deleteContent(report.id)">Remove</v-btn>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-window-item>

        </v-window>
      </v-card>
    </v-container>

    <v-dialog v-model="isReviewDialogOpen" max-width="700" persistent>
      <v-card class="rounded-xl bg-surface" v-if="activeApp">
        <div class="pa-5 border-b border-opacity-25 d-flex justify-space-between align-center bg-surface-variant">
          <h2 class="text-h6 font-weight-black text-high-emphasis d-flex align-center">
            <v-icon icon="mdi-text-box-search-outline" class="mr-2 text-primary"></v-icon>
            Application: {{ activeApp.id }}
          </h2>
          <v-btn icon="mdi-close" variant="text" density="comfortable" @click="isReviewDialogOpen = false" :disabled="isProcessing"></v-btn>
        </div>

        <v-card-text class="pa-6">
          <v-row>
            <v-col cols="12" md="6">
              <div class="text-overline font-weight-bold text-primary mb-2">Group Details</div>
              <div class="mb-4">
                <div class="text-caption text-medium-emphasis">Proposed Name</div>
                <div class="text-body-1 font-weight-bold text-high-emphasis">{{ activeApp.name }}</div>
              </div>
              <div class="mb-4">
                <div class="text-caption text-medium-emphasis">Type & Category</div>
                <div class="text-body-2 text-high-emphasis">{{ activeApp.groupType }} • {{ activeApp.category }}</div>
              </div>
              <div class="mb-4">
                <div class="text-caption text-medium-emphasis">Mission / Description</div>
                <div class="text-body-2 text-high-emphasis">{{ activeApp.description }}</div>
              </div>
              <div>
                <div class="text-caption text-medium-emphasis">Meeting Logistics</div>
                <div class="text-body-2 text-high-emphasis"><v-icon icon="mdi-calendar-clock" size="14" class="mr-1"></v-icon> {{ activeApp.meetingTime }}</div>
              </div>
            </v-col>

            <v-col cols="12" md="6">
              <div class="text-overline font-weight-bold text-primary mb-2">Accountability</div>
              <v-card variant="tonal" color="grey" class="pa-3 rounded-lg mb-4">
                <div class="text-caption text-high-emphasis font-weight-bold mb-1">Proposed Chairperson</div>
                <div class="text-body-2 text-high-emphasis"><v-icon icon="mdi-account" size="14" class="mr-1"></v-icon> {{ activeApp.leaderName }}</div>
                <div class="text-body-2 text-high-emphasis"><v-icon icon="mdi-card-account-details" size="14" class="mr-1"></v-icon> {{ activeApp.leaderAdmission }}</div>
                <div class="text-body-2 text-high-emphasis"><v-icon icon="mdi-phone" size="14" class="mr-1"></v-icon> {{ activeApp.leaderPhone }}</div>
              </v-card>
              <div class="mb-4">
                <div class="text-caption text-medium-emphasis">Staff Patron</div>
                <div class="text-body-2 text-high-emphasis">{{ activeApp.patronName }}</div>
              </div>
              <div>
                <div class="text-caption text-medium-emphasis mb-1">Official Document</div>
                <v-btn variant="outlined" color="primary" size="small" class="text-none font-weight-bold rounded-lg w-100" prepend-icon="mdi-file-pdf-box">
                  View {{ activeApp.documentName }}
                </v-btn>
              </div>
            </v-col>
          </v-row>
        </v-card-text>

        <div class="pa-5 border-t border-opacity-25 d-flex justify-end gap-3 bg-surface-variant">
          <v-btn color="error" variant="text" class="text-none font-weight-bold rounded-lg px-5" @click="isRejectDialogOpen = true" :disabled="isProcessing">Reject</v-btn>
          <v-btn color="success" variant="flat" class="text-none font-weight-bold rounded-lg px-6" @click="handleApproveGroup" :loading="isProcessing">Approve & Create</v-btn>
        </div>
      </v-card>
    </v-dialog>

    <v-dialog v-model="isRejectDialogOpen" max-width="400">
      <v-card class="rounded-xl bg-surface pa-2">
        <v-card-title class="font-weight-black text-error d-flex align-center">
          <v-icon icon="mdi-alert-circle" class="mr-2"></v-icon> Reject Application
        </v-card-title>
        <v-card-text class="pt-2">
          <p class="text-body-2 text-medium-emphasis mb-4">Provide a reason for rejecting this application. This will be emailed to the student.</p>
          <v-textarea v-model="rejectReason" variant="outlined" placeholder="e.g., The proposed meeting time clashes..." rows="3" auto-grow hide-details class="rounded-lg"></v-textarea>
        </v-card-text>
        <v-card-actions class="px-4 pb-4">
          <v-spacer></v-spacer>
          <v-btn variant="text" color="medium-emphasis" class="text-none font-weight-bold" @click="isRejectDialogOpen = false" :disabled="isProcessing">Cancel</v-btn>
          <v-btn color="error" variant="flat" class="text-none font-weight-bold rounded-lg px-4" @click="handleRejectGroup" :loading="isProcessing" :disabled="!rejectReason">Confirm Rejection</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-snackbar v-model="showSnackbar" :timeout="3000" :color="snackbarColor" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon :icon="snackbarColor === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle'" class="mr-3"></v-icon>
        {{ snackbarText }}
      </div>
    </v-snackbar>

  </v-container>
</template>

<style scoped>
.gap-1 { gap: 4px; }
.gap-3 { gap: 12px; }

.hover-bg-variant:hover {
  background-color: rgba(var(--v-theme-on-surface), 0.03);
}

.leading-tight { line-height: 1.2 !important; }
</style>