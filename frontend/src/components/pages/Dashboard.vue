<script setup lang="ts">
</script>
<template>
  <v-container> 
    <v-row >
        <v-col col>
            <v-card class="mr-3 d-flex  justify-center">Student of the Week</v-card>
        </v-col>
        <v-col>
            <v-card class="mr-3 d-flex  justify-center">Item two</v-card>
         </v-col>
         <v-col>
            <v-card class="mr-3 d-flex  justify-center">Item three</v-card>
        </v-col>
    </v-row>
    <v-row>
        <v-col class="d-flex justify-center">Recent Activities Feed</v-col>
    </v-row>
    <v-container class="d-flex align-center justify-center">
    <v-card :width="1000" :height="300"
    elevation="5"
    rounded="lg">
    <v-card-item>
        <v-card-title class="text-xl" >Card</v-card-title>
        <v-divider></v-divider>
    </v-card-item>
    </v-card>
</v-container>

  </v-container>
</template>