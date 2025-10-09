<script setup lang="ts">

</script>
<template>
  <v-container> 
               <v-img
    src="https://media.gettyimages.com/id/1134454088/photo/portrait-of-happy-young-woman-enjoying-sunlight.jpg?s=612x612&w=0&k=20&c=35zyCVhykBrynJ4KW5mWMsw2OJ_MdoyjKfUVVxn2bNM="
    height="300"
    cover
    class="banner"
  >
    <v-row class="fill-height align-center justify-center text-center text-white">
      <v-col>
        <h1 class="text-h4 font-weight-bold">Welcome to CoopConnect</h1>
        <p class="text-subtitle-1">Your campus digital hub</p>
      </v-col>
    </v-row>
  </v-img>      
   
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
    <v-container class="d-flex align-center justify-center ">
    <v-card :width="1000" 
    elevation="5"
    rounded="lg"
    class="p-4 notification-card"
    >
    <v-card-item>
        <v-card-title class="text-xl" >Card</v-card-title>
        <v-divider></v-divider>
    </v-card-item>
    <v-list>
        <v-list-item>
            <template #prepend>
                <v-icon color="red">mdi-alert-circle </v-icon>
            </template>
            <v-list-item-title>
                <strong>Alert:</strong> Suspicious activity reported near hostel c - 10 mins ago
            </v-list-item-title>
            <v-list-item-subtitle>
                <v-btn variant="outlined" class="mr-1" size="small">Verify</v-btn>
                <v-btn variant="outlined" size="small">Comment</v-btn>
            </v-list-item-subtitle>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item>
            <template #prepend>
                <v-icon color="blue">mdi-calendar-clock</v-icon>
            </template>
            <v-list-item-title>
                <strong>Event:</strong> Debate Club meeting today at 4pm in auditorium - 1h ago
            </v-list-item-title>
            <v-list-item-subtitle>
                <v-btn variant="outlined" class="mr-1" size="small">like</v-btn>
                <v-btn variant="outlined" class="mr-1" size="small">Comment</v-btn>
                <v-btn variant="outlined" size="small">add to Calender</v-btn>
            </v-list-item-subtitle>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item>
            <template #prepend>
                <v-icon color="purple">mdi-image</v-icon>
            </template>
            <v-list-item-title>
                <strong>Talent</strong> @Aisha her new photo series - 2h ago
            </v-list-item-title>
            <v-list-item-subtitle>
                <v-btn variant="outlined" class="mr-1" size="small">like</v-btn>
                <v-btn variant="outlined" class="mr-1" size="small">View Gallery</v-btn>
                <v-btn variant="outlined" size="small">Comment</v-btn>
            </v-list-item-subtitle>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item>
            <template #prepend>
                    <v-icon color="red">mdi-hand-clap</v-icon>
                </template>
            <v-list-item-title>
                <strong>Appreciation</strong> Fashion student of the week @David
            </v-list-item-title>
            <v-list-item-subtitle>
                <v-btn variant="outlined" class="mr-1" size="small">Like</v-btn>
                <v-btn variant="outlined" class="mr-1" size="small">Comment</v-btn>
            </v-list-item-subtitle>
        </v-list-item>
        <!-- duplicated  -->
        <v-divider></v-divider>
        <v-list-item>
            <template #prepend>
                <v-icon color="purple">mdi-image</v-icon>
            </template>
            <v-list-item-title>
                <strong>Talent</strong> @Aisha her new photo series - 2h ago
            </v-list-item-title>
            <v-list-item-subtitle>
                <v-btn variant="outlined" class="mr-1" size="small">like</v-btn>
                <v-btn variant="outlined" class="mr-1" size="small">View Gallery</v-btn>
                <v-btn variant="outlined" size="small">Comment</v-btn>
            </v-list-item-subtitle>
        </v-list-item>
        <v-divider></v-divider>
        <v-list-item>
            <template #prepend>
                    <v-icon color="red">mdi-hand-clap</v-icon>
                </template>
            <v-list-item-title>
                <strong>Appreciation</strong> Fashion student of the week @David
            </v-list-item-title>
            <v-list-item-subtitle>
                <v-btn variant="outlined" class="mr-1" size="small">Like</v-btn>
                <v-btn variant="outlined" class="mr-1" size="small">Comment</v-btn>
            </v-list-item-subtitle>
        </v-list-item>
    </v-list>
    </v-card>
    </v-container>
    <v-row>
        <v-col class=" d-flex justify-center">Featured Talent</v-col>
    </v-row>
    <v-default-provider>
        <v-sheet class="overflow-hidden" max-width="1200" rounded="xl">
            <v-carousel direction="horizontal" progress="red"  vertical-delimiters="right"
            height="400"  hide-delimiter-background>
            </v-carousel>
        </v-sheet>
    </v-default-provider>

  </v-container>
</template>
<style scoped>
.notification-card {
    border-radius: 20px;
    transition: all 0.3s ease;
}
.notification-card:hover{
    transform: translateY(-4px);
}
</style>