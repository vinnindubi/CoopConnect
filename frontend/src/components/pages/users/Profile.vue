<script lang="ts"></script>
<template>
    <v-card class="pa-4 d-flex align-center">
        <v-avatar size="120">
        <v-img src="https://randomuser.me/api/portraits/men/32.jpg"  class="rounded-lg" cover/>
        </v-avatar>
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
      <v-text-field label="First Name" readonly variant="solo-filled" model-value="Vinnie" />
    </v-col>

    <v-col cols="12" md="6">
      <v-text-field label="Last Name" readonly variant="solo-filled" model-value="Ndubi" />
    </v-col>

    <v-col cols="12" md="6">
      <v-text-field label="Email" readonly variant="solo-filled" model-value="vinnie@example.com" />
    </v-col>

    <v-col cols="12" md="6">
      <v-text-field label="Phone Number" readonly variant="solo-filled" model-value="0712345678" />
    </v-col>
  </v-row>
</v-card>
<v-card class="pa-4 mt-5"> 
    <div class="text-h6 font-weight-bold mb-3">Address</div>
    <v-row>
        <v-text-field label="country" readonly variant="solo-filled" model-value="Kenya"  />
        <v-text-field label= "city" readonly variant="solo-filled" model-value="Nairobi" class="ml-2"></v-text-field>
        <v-text-field label="postalcode" readonly variant="solo-filled" model-value="302" class="ml-2"></v-text-field>
        
    </v-row>
</v-card>
<div class="pa-4" style="position: sticky; bottom: 0; background: white;">
  <v-btn 
    color="primary" 
    block
    size="large"
  >
    Edit Profile
  </v-btn>
</div>

</template>