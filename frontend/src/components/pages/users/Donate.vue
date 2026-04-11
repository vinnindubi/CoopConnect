<script setup lang="ts">
import { ref, computed, onUnmounted } from 'vue';
import { initiateMpesaDonation, checkMpesaStatus } from '@/services/mpesaService';

// --- State ---
const viewState = ref<'waiting' | 'success'>('waiting'); // Controls what the pop-up shows
const showDialog = ref(false); // Controls if the pop-up is open

const amount = ref<number | null>(null);
const customAmount = ref<string>('');
const phoneNumber = ref('');
const isProcessing = ref(false);
const showSnackbar = ref(false);
const snackbarMessage = ref('');
const snackbarColor = ref('success');

// Polling variables
let pollingInterval: any = null;
const currentCheckoutId = ref('');
const receiptNumber = ref('');

// --- Data ---
const presetAmounts = [
  { value: 50, label: 'KES 50' },
  { value: 100, label: 'KES 100' },
  { value: 500, label: 'KES 500' },
  { value: 1000, label: 'KES 1,000' },
];

const impacts = [
  { icon: 'mdi-food-apple-outline', amount: '50', description: 'Provides a quick campus breakfast.' },
  { icon: 'mdi-food-hot-dog', amount: '100', description: 'Secures a hot, nutritious lunch.' },
  { icon: 'mdi-basket-outline', amount: '500', description: 'Buys groceries to last a student a few days.' }
];

// --- Computed & Methods ---
const totalToPay = computed(() => {
  if (amount.value) return amount.value;
  if (customAmount.value) return parseInt(customAmount.value) || 0;
  return 0;
});

const selectPreset = (val: number) => {
  amount.value = val;
  customAmount.value = ''; 
};

const handleCustomAmountInput = () => {
  amount.value = null; 
};

// 1. Start the STK Push
const processDonation = async () => {
  if (totalToPay.value < 1) return showToast('Minimum donation is KES 10', 'error');
  if (phoneNumber.value.length < 9) return showToast('Please enter a valid Safaricom number', 'error');

  isProcessing.value = true;

  try {
    const response = await initiateMpesaDonation(phoneNumber.value, totalToPay.value);
    
    // Open the Dialog and set it to Waiting
    currentCheckoutId.value = response.data.checkout_request_id;
    viewState.value = 'waiting'; 
    showDialog.value = true;
    
    // Start polling Safaricom
    startPolling();

  } catch (error: any) {
    showToast(error.response?.data?.message || 'Failed to initiate payment.', 'error');
  } finally {
    isProcessing.value = false;
  }
};

// 2. The Polling Logic
const startPolling = () => {
  let attempts = 0;
  const maxAttempts = 20; // Poll for 60 seconds

  pollingInterval = setInterval(async () => {
    attempts++;
    
    try {
      const res = await checkMpesaStatus(currentCheckoutId.value);
      
      if (res.data.status === 'completed') {
        clearInterval(pollingInterval);
        receiptNumber.value = res.data.receipt;
        viewState.value = 'success'; // Switch pop-up to success view!
      } 
      else if (res.data.status === 'failed') {
        clearInterval(pollingInterval);
        showDialog.value = false; // Close pop-up
        showToast('Transaction failed or was cancelled.', 'error');
      }

    } catch (e) {
      console.error('Polling error', e);
    }

    if (attempts >= maxAttempts) {
      clearInterval(pollingInterval);
      showDialog.value = false; // Close pop-up
      showToast('Transaction timed out. Please try again.', 'error');
    }
  }, 3000); 
};

const resetForm = () => {
  showDialog.value = false; // Close the pop-up
  amount.value = null;
  customAmount.value = '';
  phoneNumber.value = '';
};

const showToast = (message: string, color: string) => {
  snackbarMessage.value = message;
  snackbarColor.value = color;
  showSnackbar.value = true;
};

// Clean up if user leaves the page early
onUnmounted(() => {
  if (pollingInterval) clearInterval(pollingInterval);
});
</script>

<template>
  <v-container fluid class="pa-0 min-vh-100 bg-background">
    <div class="hero-gradient position-relative py-16 py-md-24 px-4 px-md-8 d-flex align-center">
      <div class="position-absolute w-100 h-100 bg-pattern" style="top:0; left:0; opacity: 0.1;"></div>
      
      <v-container style="max-width: 1200px; position: relative; z-index: 1;">
        <v-row align="center" justify="space-between">
          
          <v-col cols="12" md="6" class="text-white mb-10 mb-md-0 pr-md-10">
            <v-chip color="white" variant="outlined" size="small" class="font-weight-bold text-uppercase tracking-widest mb-6">
              CoopConnect Support Fund
            </v-chip>
            
            <h1 class="text-h3 text-md-h2 font-weight-black mb-6 leading-tight text-shadow">
              Feed a Comrade. <br>
              <span class="text-green-accent-3">Change a Day.</span>
            </h1>
            
            <p class="text-h6 font-weight-regular opacity-90 mb-10" style="line-height: 1.6; max-width: 500px;">
              University is hard enough without having to worry about your next meal. 100% of your donation goes directly to students struggling with food insecurity on campus.
            </p>

            <div class="d-flex flex-column gap-6">
              <div v-for="(impact, i) in impacts" :key="i" class="d-flex align-center bg-white bg-opacity-10 pa-4 rounded-xl border border-white border-opacity-25 backdrop-blur">
                <v-avatar color="green-accent-3" size="56" class="mr-4 elevation-4">
                  <v-icon :icon="impact.icon" color="blue-darken-4" size="28"></v-icon>
                </v-avatar>
                <div>
                  <div class="font-weight-black text-h6 text-green-accent-3 mb-1">KES {{ impact.amount }}</div>
                  <div class="text-body-2 font-weight-medium opacity-90">{{ impact.description }}</div>
                </div>
              </div>
            </div>
          </v-col>

          <v-col cols="12" md="5">
            <v-card class="rounded-xl pa-6 pa-md-8 elevation-10 bg-surface">
              
              <div class="text-center mb-8">
                <v-avatar color="success-lighten-4" size="64" class="mb-4 rounded-circle">
                  <v-icon icon="mdi-hand-heart" color="success" size="32"></v-icon>
                </v-avatar>
                <h2 class="text-h5 font-weight-black text-high-emphasis">Make a Donation</h2>
                <p class="text-body-2 text-medium-emphasis mt-2">Select an amount to give securely via M-Pesa</p>
              </div>

              <v-row class="mb-4" dense>
                <v-col cols="6" v-for="preset in presetAmounts" :key="preset.value">
                  <v-btn block size="large" :color="amount === preset.value ? 'success' : 'grey-lighten-3'" :class="amount === preset.value ? 'text-white font-weight-black' : 'text-high-emphasis font-weight-bold'" elevation="0" class="rounded-lg text-none" @click="selectPreset(preset.value)">
                    {{ preset.label }}
                  </v-btn>
                </v-col>
              </v-row>

              <div class="d-flex align-center mb-6">
                <v-divider></v-divider>
                <span class="px-4 text-caption font-weight-bold text-medium-emphasis text-uppercase">OR</span>
                <v-divider></v-divider>
              </div>

              <v-text-field v-model="customAmount" label="Custom Amount" prefix="KES" type="number" variant="outlined" density="comfortable" class="mb-2 font-weight-bold" hide-details @input="handleCustomAmountInput"></v-text-field>

              <div class="mt-6">
                <div class="text-caption font-weight-bold text-high-emphasis mb-2 ml-1">M-Pesa Phone Number</div>
                <v-text-field v-model="phoneNumber" placeholder="07XX XXX XXX" prefix="+254" variant="outlined" density="comfortable" hide-details class="font-weight-bold">
                  <template v-slot:prepend-inner><v-icon icon="mdi-cellphone-wireless" color="success" class="mr-2"></v-icon></template>
                </v-text-field>
              </div>

              <v-btn block color="success" size="x-large" class="mt-8 text-none font-weight-black rounded-lg elevation-4" :loading="isProcessing" :disabled="totalToPay < 1" @click="processDonation">
                Donate KES {{ totalToPay || '0' }}
              </v-btn>

            </v-card>
          </v-col>
          </v-row>
      </v-container>
    </div>

    <v-dialog v-model="showDialog" persistent max-width="450">
      <v-card class="rounded-xl pa-8 text-center">
        
        <div v-if="viewState === 'waiting'">
          <v-progress-circular indeterminate color="success" size="80" width="6" class="mb-6"></v-progress-circular>
          <h2 class="text-h5 font-weight-black text-high-emphasis mb-2">Check your phone!</h2>
          <p class="text-body-1 text-medium-emphasis mb-6">
            We've sent an M-Pesa prompt to <strong>{{ phoneNumber }}</strong>.<br>
            Please enter your PIN to complete the donation.
          </p>
          <v-chip color="warning" variant="flat" size="small" class="font-weight-bold text-uppercase px-4">Waiting for Safaricom...</v-chip>
        </div>

        <div v-else-if="viewState === 'success'">
          <v-avatar color="success" size="96" class="mb-6 elevation-4">
            <v-icon icon="mdi-check-bold" color="white" size="48"></v-icon>
          </v-avatar>
          <h2 class="text-h4 font-weight-black text-high-emphasis mb-2">Thank You!</h2>
          <p class="text-body-1 text-medium-emphasis mb-6">Your donation of KES {{ totalToPay }} was received successfully.</p>
          
          <v-card color="success-lighten-5" class="pa-4 mb-8 rounded-lg" border="success border-opacity-25" elevation="0">
            <div class="text-caption text-success font-weight-bold text-uppercase mb-1">Receipt Number</div>
            <div class="text-h6 font-weight-black text-high-emphasis tracking-widest">{{ receiptNumber }}</div>
          </v-card>

          <v-btn color="success" size="large" block class="text-none font-weight-bold rounded-lg" @click="resetForm">
            Done
          </v-btn>
        </div>

      </v-card>
    </v-dialog>

    <v-snackbar v-model="showSnackbar" :timeout="4000" :color="snackbarColor" elevation="4" rounded="pill">
      <div class="d-flex align-center font-weight-bold">
        <v-icon :icon="snackbarColor === 'success' ? 'mdi-check-circle' : 'mdi-alert-circle'" class="mr-3"></v-icon>
        {{ snackbarMessage }}
      </div>
    </v-snackbar>

  </v-container>
</template>

<style scoped>
.hero-gradient {
  background: linear-gradient(135deg, rgb(13, 71, 161) 0%, rgb(25, 118, 210) 100%);
  min-height: 100vh;
}

.bg-pattern {
  background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0);
  background-size: 32px 32px;
}

.text-shadow {
  text-shadow: 0px 4px 12px rgba(0,0,0,0.3);
}

.leading-tight { 
  line-height: 1.1 !important; 
}

.tracking-widest { 
  letter-spacing: 0.15em !important; 
}

.backdrop-blur {
  backdrop-filter: blur(10px);
}
</style>


<!-- <template v-slot:prepend-inner>
                    <v-img src="https://upload.wikimedia.org/wikipedia/commons/1/15/M-PESA_LOGO-01.svg" width="32" class="mr-2"></v-img>
                  </template> -->