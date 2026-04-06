<script setup lang="ts">
import { ref } from 'vue';

const showSnackbar = ref(false);
const snackbarText = ref('');

const users = ref([
  { id: 1, name: 'Jacob Muthiani', admission: 'BITC01/9835/2022', status: 'Active', role: 'User' },
  { id: 2, name: 'Sarah Wanjiku', admission: 'BCOM02/1123/2021', status: 'Suspended', role: 'Seller' },
  { id: 3, name: 'David Omondi', admission: 'BENG05/4432/2023', status: 'Active', role: 'User' },
]);

const toggleUserStatus = (user: any) => {
  user.status = user.status === 'Active' ? 'Suspended' : 'Active';
  snackbarText.value = `User status updated to ${user.status}`;
  showSnackbar.value = true;
};
</script>

<template>
  <div>
    <div class="mb-6 d-flex justify-space-between align-center">
      <div>
        <h1 class="text-h4 font-weight-black text-high-emphasis">User Management</h1>
        <p class="text-medium-emphasis">View, suspend, or manage student accounts.</p>
      </div>
      <v-text-field density="compact" variant="outlined" placeholder="Search users..." prepend-inner-icon="mdi-magnify" hide-details style="max-width: 300px;"></v-text-field>
    </div>

    <v-card class="rounded-xl border-opacity-50" border elevation="2">
      <v-table hover>
        <thead>
          <tr>
            <th class="text-left font-weight-bold">Name</th>
            <th class="text-left font-weight-bold">Admission No.</th>
            <th class="text-left font-weight-bold">Role</th>
            <th class="text-left font-weight-bold">Status</th>
            <th class="text-right font-weight-bold">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td class="font-weight-medium">{{ user.name }}</td>
            <td class="text-medium-emphasis">{{ user.admission }}</td>
            <td><v-chip size="small" variant="tonal" color="primary">{{ user.role }}</v-chip></td>
            <td>
              <v-chip size="small" :color="user.status === 'Active' ? 'success' : 'error'">{{ user.status }}</v-chip>
            </td>
            <td class="text-right">
              <v-btn size="small" variant="tonal" :color="user.status === 'Active' ? 'error' : 'success'" @click="toggleUserStatus(user)">
                {{ user.status === 'Active' ? 'Suspend' : 'Reactivate' }}
              </v-btn>
            </td>
          </tr>
        </tbody>
      </v-table>
    </v-card>

    <v-snackbar v-model="showSnackbar" :timeout="3000" color="dark">
      {{ snackbarText }}
    </v-snackbar>
  </div>
</template>