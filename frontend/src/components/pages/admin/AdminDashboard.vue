<script setup lang="ts">
import { onMounted, ref,reactive } from 'vue';
import { getAllUsers,getPendingVerifications } from '@/services/authService';

// --- UI State ---
const activeTab = ref('overview');
const showSnackbar = ref(false);
const snackbarText = ref('');

const triggerAction = (message: string) => {
  snackbarText.value = message;
  showSnackbar.value = true;
};

// --- Mock Data ---
const stats = ref([
  { title: 'Total Users', value: '1,245', icon: 'mdi-account-group', color: 'indigo' },
  { title: 'Pending Sellers', value: '12', icon: 'mdi-store-clock', color: 'amber-darken-2' },
  { title: 'Reported Content', value: '8', icon: 'mdi-flag-triangle', color: 'red-darken-1' },
]);

//  Now Vue knows this will hold a list of multiple users
const users = ref<any[]>([]);

const pendingVerifications = ref<any[]>([]);

const reportedContent = ref([
  { id: 301, type: 'Product', author: 'Mike T.', reason: 'Counterfeit Item', content: 'Selling cheap knock-off AirPods as original.' },
  { id: 302, type: 'Comment', author: 'Anonymous', reason: 'Harassment', content: 'You are so stupid for posting this.' },
]);

// --- Actions ---
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
  triggerAction(`Seller #${id} Rejected.`);
};

const deleteContent = (id: number) => {
  reportedContent.value = reportedContent.value.filter(c => c.id !== id);
  triggerAction('Content deleted successfully.');
};
const getClient = async () => {
  try {
    const response = await getAllUsers();
    const rawUsers = response.data.data;     
    // We loop through Laravel's data and map it to match your Vue template
    users.value = rawUsers.map((user: any) => ({
      id: user.id,
      name: user.fullname, 
      admission: user.admission,
      status: user.verification_status || 'Pending', // Fallback just in case
    }));

  } catch (error) {
    console.error("Error fetching users:", error);
    triggerAction("Failed to load users from database.");
  }
}
const fetchPendingSellers = async () => {
  try {
    const response = await getPendingVerifications();
    const rawPending = response.data.data;
    
    // Map the database data to your frontend array
    pendingVerifications.value = rawPending.map((req: any) => ({
  id: req.id,
  name: req.fullname,
  storeCategories: req.store_categories || [], // <-- Mapping it here!
  submittedAt: new Date(req.updated_at).toLocaleDateString()
}));

  } catch (error) {
    console.error("Error fetching pending verifications:", error);
  }
}
 onMounted(()=>{
 getClient()
 fetchPendingSellers()
 })
</script>

<template>
  <v-container fluid class="pa-0 pa-md-6 bg-grey-lighten-4 min-vh-100">
    <v-container style="max-width: 1200px;">
      
      <div class="mb-8">
        <h1 class="text-h3 font-weight-black text-indigo-darken-4 mb-2">Command Center</h1>
        <p class="text-body-1 text-medium-emphasis">Monitor activity, verify sellers, and keep the platform safe.</p>
      </div>

      <v-card class="rounded-xl border-opacity-25" border elevation="3" bg-color="white">
        
        <v-tabs 
          v-model="activeTab" 
          bg-color="white" 
          color="indigo-darken-2" 
          slider-color="indigo-darken-2"
          align-tabs="center"
          grow
          height="64"
        >
          <v-tab value="overview" class="font-weight-bold text-none"><v-icon start icon="mdi-view-dashboard-outline"></v-icon> Overview</v-tab>
          <v-tab value="users" class="font-weight-bold text-none"><v-icon start icon="mdi-account-group-outline"></v-icon> Users</v-tab>
          <v-tab value="verifications" class="font-weight-bold text-none">
            <v-badge v-if="pendingVerifications.length" :content="pendingVerifications.length" color="amber-darken-2" class="mr-3"></v-badge>
            <v-icon start icon="mdi-shield-check-outline"></v-icon> Verifications
          </v-tab>
          <v-tab value="moderation" class="font-weight-bold text-none"><v-icon start icon="mdi-alert-octagon-outline"></v-icon> Moderation</v-tab>
        </v-tabs>

        <v-divider></v-divider>

        <v-window v-model="activeTab" class="bg-white">
          
          <v-window-item value="overview" class="pa-4 pa-md-8">
            <v-row>
              <v-col v-for="stat in stats" :key="stat.title" cols="12" md="4">
                <v-card class="rounded-xl border-opacity-50 h-100 d-flex flex-column transition-swing" hover border elevation="0" bg-color="grey-lighten-5">
                  <v-card-text class="d-flex justify-space-between align-center pa-6">
                    <div>
                      <div class="text-overline font-weight-black text-medium-emphasis mb-1">{{ stat.title }}</div>
                      <div class="text-h3 font-weight-black text-grey-darken-4">{{ stat.value }}</div>
                    </div>
                    <v-avatar :color="stat.color" variant="tonal" size="72" class="rounded-lg">
                      <v-icon :icon="stat.icon" size="40"></v-icon>
                    </v-avatar>
                  </v-card-text>
                  <div :class="`bg-${stat.color}`" style="height: 4px; width: 100%; margin-top: auto;"></div>
                </v-card>
              </v-col>
            </v-row>
          </v-window-item>

          <v-window-item value="users" class="pa-4 pa-md-8">
            <div class="d-flex justify-space-between align-end mb-6">
              <h2 class="text-h5 font-weight-black text-indigo-darken-4">Registered Students</h2>
              <v-text-field density="compact" variant="solo-filled" flat placeholder="Search students..." prepend-inner-icon="mdi-magnify" hide-details style="max-width: 300px;" class="rounded-lg"></v-text-field>
            </div>
            
            <v-card variant="outlined" class="border-opacity-25 rounded-lg overflow-hidden">
              <v-table hover>
                <thead class="bg-grey-lighten-4">
                  <tr>
                    <th class="text-left font-weight-bold text-uppercase text-caption text-medium-emphasis py-4">Student</th>
                    <th class="text-left font-weight-bold text-uppercase text-caption text-medium-emphasis py-4">Admission</th>
                    <th class="text-center font-weight-bold text-uppercase text-caption text-medium-emphasis py-4">Status</th>
                    <th class="text-right font-weight-bold text-uppercase text-caption text-medium-emphasis py-4">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="user in users" :key="user.id">
                    <td class="font-weight-bold py-3 text-grey-darken-4">
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
                      <v-btn size="small" variant="text" :color="user.status === 'Active' ? 'red-darken-1' : 'success'" class="font-weight-bold text-none rounded-lg" @click="toggleUserStatus(user)">
                        {{ user.status === 'Active' ? 'Suspend Account' : 'Reactivate' }}
                      </v-btn>
                    </td>
                  </tr>
                </tbody>
              </v-table>
            </v-card>
          </v-window-item>

          <v-window-item value="verifications" class="pa-4 pa-md-8">
            <h2 class="text-h5 font-weight-black text-indigo-darken-4 mb-6">Pending Applications</h2>
            <v-row v-if="pendingVerifications.length > 0">
              <v-col cols="12" md="6" v-for="req in pendingVerifications" :key="req.id">
                <v-card variant="outlined" class="rounded-xl pa-5 border-opacity-25 bg-grey-lighten-5">
                  <div class="d-flex">
                    <v-avatar rounded="lg" size="80" class="mr-5 elevation-1">
                      <v-img :src="req.idProof" cover></v-img>
                    </v-avatar>
                    
                    <div class="flex-grow-1">
                      <div class="d-flex justify-space-between align-start mb-2">
                        <div class="text-h6 font-weight-bold text-grey-darken-4">{{ req.name }}</div>
                        <v-chip size="x-small" color="medium-emphasis" variant="tonal" class="font-weight-bold">{{ req.submittedAt }}</v-chip>
                      </div>
                      
                      <div class="mb-4">
                        <div class="text-caption text-medium-emphasis font-weight-bold mb-2 text-uppercase">Store Categories</div>
                        
                        <div v-if="req.storeCategories && req.storeCategories.length > 0" class="d-flex flex-wrap">
                          <v-chip 
                            v-for="cat in req.storeCategories" 
                            :key="cat" 
                            size="small" 
                            color="indigo-darken-2"
                            variant="outlined" 
                            class="font-weight-bold border-opacity-50 mr-2 mb-1"
                          >
                            {{ cat }}
                          </v-chip>
                        </div>
                        <div v-else class="text-caption text-medium-emphasis font-italic">No categories selected.</div>
                      </div>
                      
                      <div class="d-flex gap-3">
                        <v-btn flex-grow-1 color="success" variant="flat" class="text-none font-weight-bold rounded-lg" @click="approveSeller(req.id)">Approve</v-btn>
                        <v-btn flex-grow-1 color="error" variant="tonal" class="text-none font-weight-bold rounded-lg ml-2" @click="rejectSeller(req.id)">Deny</v-btn>
                      </div>
                    </div>
                  </div>
                </v-card>
              </v-col>
            </v-row>
            <div v-else class="text-center pa-12 text-medium-emphasis bg-grey-lighten-4 rounded-xl border border-dashed">
              <v-icon icon="mdi-check-decagram-outline" size="48" color="success" class="mb-3"></v-icon>
              <div class="text-h6 font-weight-bold text-grey-darken-3">All Caught Up</div>
              <p>There are no pending seller applications.</p>
            </div>
          </v-window-item>

          <v-window-item value="moderation" class="pa-4 pa-md-8">
            <h2 class="text-h5 font-weight-black text-indigo-darken-4 mb-6">Flagged Content</h2>
            <v-row v-if="reportedContent.length > 0">
              <v-col cols="12" v-for="report in reportedContent" :key="report.id">
                <v-card variant="outlined" class="rounded-xl pa-5 border-opacity-50 border-error bg-red-lighten-5">
                  <div class="d-flex flex-column flex-md-row justify-space-between align-md-center mb-3">
                    <div class="d-flex align-center">
                      <v-chip size="small" color="error" variant="flat" class="font-weight-bold mr-3">
                        <v-icon start icon="mdi-flag" size="14"></v-icon> {{ report.reason }}
                      </v-chip>
                      <span class="text-caption font-weight-bold text-medium-emphasis text-uppercase">Reported {{ report.type }}</span>
                    </div>
                    <div class="text-caption text-medium-emphasis mt-2 mt-md-0">Flagged by: <strong>{{ report.author }}</strong></div>
                  </div>
                  
                  <v-card class="pa-4 bg-white rounded-lg elevation-0 border mb-4">
                    <p class="text-body-1 font-italic text-grey-darken-3 mb-0">"{{ report.content }}"</p>
                  </v-card>

                  <div class="d-flex justify-end">
                    <v-btn variant="text" color="grey-darken-2" class="text-none font-weight-bold mr-3 rounded-lg">Ignore Report</v-btn>
                    <v-btn color="error" variant="flat" prepend-icon="mdi-delete" class="text-none font-weight-bold rounded-lg" @click="deleteContent(report.id)">Remove Content</v-btn>
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-window-item>

        </v-window>
      </v-card>
    </v-container>

    <v-snackbar v-model="showSnackbar" :timeout="3000" color="primary" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon icon="mdi-check-circle" color="success" class="mr-3"></v-icon>
        {{ snackbarText }}
      </div>
    </v-snackbar>
  </v-container>
</template>