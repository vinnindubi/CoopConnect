<script  setup lang="ts">
    import { useRouter } from 'vue-router';

const router = useRouter();
function saveProfile() {
  
  router.push('/profile');
}

</script>
<template> 
<v-sheet>
<v-form @submit.prevent="saveProfile">
<h1> Here is the form to be edited !!!!</h1>
<v-card class="d-flex pa-4 align-center">
    <div class="avatar-wrapper position-relative d-inline-block">
    <v-avatar size="120" >
        <v-img src="https://randomuser.me/api/portraits/men/32.jpg" class="rounded-lg" cover></v-img> 
    </v-avatar>
    <v-btn icon size="small" class="camera-btn">
        <v-icon color="white">mdi-camera</v-icon>
    </v-btn>
    </div>
    <div class="ml-6">
            <div class="text-h5 font-weight-bold">Vincent Ndereba Ndubi</div>
            <div class="text-subtitle-2"> software developer • Calkenya</div>
        </div>
</v-card>
    <!-- Personal Information -->
<v-card class="pa-4 mt-5">
  <div class="text-h6 font-weight-bold mb-3">Personal Information</div>

  <v-row>
    <v-col cols="12" md="6">
      <v-text-field label="First Name"  variant="solo-filled" model-value="Vinnie" />
    </v-col>

    <v-col cols="12" md="6">
      <v-text-field label="Last Name"  variant="solo-filled" model-value="Ndubi" />
    </v-col>

    <v-col cols="12" md="6">
      <v-text-field label="Email"  variant="solo-filled" model-value="vinnie@example.com" />
    </v-col>

    <v-col cols="12" md="6">
      <v-text-field label="Phone Number"  variant="solo-filled" model-value="0712345678" />
    </v-col>
  </v-row>
</v-card>
<v-card class="pa-4 mt-5"> 
    <div class="text-h6 font-weight-bold mb-3">Address</div>
    <v-row>
        <v-text-field label="country" variant="solo-filled" model-value="Kenya"  />
        <v-text-field label= "city" variant="solo-filled" model-value="Nairobi" class="ml-2"></v-text-field>
        <v-text-field label="postalcode" variant="solo-filled" model-value="302" class="ml-2"></v-text-field>
        
    </v-row>
</v-card>
<v-card-actions class="d-flex justify-end">
    <v-btn type="submit" color="success" variant='flat'>
  Save
</v-btn>

</v-card-actions>
</v-form>
</v-sheet>
</template>
<style scoped>
    .position-relative {
  position: relative;
}

.camera-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  transform: translate(20%, 20%);
  background: white !important;
  box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}

</style>