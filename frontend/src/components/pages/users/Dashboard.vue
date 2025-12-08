<script setup lang="ts">
const highlights = [
  {
    title: "Student of the Week",
    description: "🏅 David Kamau, Fashion & Design",
    icon: "mdi-account-star",
    color: "purple",
    action: "View Profile"
  },
  {
    title: "Upcoming Event",
    description: "📅 Tech Expo 2025 - Oct 12th",
    icon: "mdi-calendar-clock",
    color: "blue",
    action: "Learn More"
  },
  {
    title: "Top Talent",
    description: "🎨 Aisha’s Art Gallery Featured",
    icon: "mdi-palette",
    color: "orange",
    action: "View Gallery"
  },
  {
    title: "New Announcement",
    description: "🔔 Campus Wi-Fi upgrade ongoing",
    icon: "mdi-bullhorn",
    color: "green",
    action: "Read More"
  }
];
</script>
<template>
  <v-container fluid>
    <!--  Banner Section -->
    <v-sheet class="banner-section overflow-hidden" height="250" rounded="xl" >
      <v-row class="fill-height align-center justify-center text-white text-h4 font-weight-bold">
        Welcome to CoopConnect Campus Platform
      </v-row>
    </v-sheet>

    <!-- Quick Highlights Section -->
    <v-row>
      <v-col cols="12" class="text-center mb-4">
        <h2 class="text-h5 font-weight-bold">Quick Highlights</h2>
      </v-col>
    </v-row>

    <v-row>
      <v-col
        v-for="(item, index) in highlights"
        :key="index"
        cols="12"
        sm="6"
        md="3"
      >
        <v-card
          class="pa-4 d-flex flex-column align-center justify-space-between hover-scale"
          :color="item.color + '-lighten-5'"
          elevation="3"
          rounded="xl"
          height="220"
        >
          <v-icon
            :color="item.color"
            size="48"
            class="mb-3"
          >
            {{ item.icon }}
          </v-icon>

          <div class="text-center">
            <div class="text-subtitle-1 font-weight-medium mb-1">
              {{ item.title }}
            </div>
            <div class="text-body-2 text-medium-emphasis">
              {{ item.description }}
            </div>
          </div>

          <v-btn
            :color="item.color"
            variant="tonal"
            size="small"
            class="mt-3"
          >
            {{ item.action }}
          </v-btn>
        </v-card>
      </v-col>
    </v-row>

    <!-- Recent Activities Section -->
    <v-card class="mt-8 pa-4" elevation="5" rounded="xl">
      <v-card-title class="text-h6 mb-3">Recent Activities Feed</v-card-title>
      <v-divider></v-divider>
      <v-list>
        <v-list-item>
          <template #prepend><v-icon color="red">mdi-alert-circle</v-icon></template>
          <v-list-item-title>
            <strong>Alert:</strong> Suspicious activity reported near Hostel C – 10 min ago
          </v-list-item-title>
          <v-list-item-subtitle>
            <v-btn size="small" variant="outlined">Verify</v-btn>
            <v-btn size="small" variant="outlined" class="ml-2">Comment</v-btn>
          </v-list-item-subtitle>
        </v-list-item>

        <v-divider></v-divider>

        <v-list-item>
          <template #prepend><v-icon color="blue">mdi-calendar-week</v-icon></template>
          <v-list-item-title>
            <strong>Event:</strong> Debate Club meeting today at 4pm – 1h ago
          </v-list-item-title>
          <v-list-item-subtitle>
            <v-btn size="small" variant="outlined">Like</v-btn>
            <v-btn size="small" variant="outlined" class="ml-2">Comment</v-btn>
            <v-btn size="small" variant="outlined" class="ml-2">Add to Calendar</v-btn>
          </v-list-item-subtitle>
        </v-list-item>
      </v-list>
    </v-card>

    <!-- Featured Talent Carousel -->
    <v-sheet class="mt-8 overflow-hidden" rounded="xl" elevation="3" >
      <v-carousel height="300" progress="green"  >
        <v-carousel-item
          v-for="n in 3"
          :key="n">
          <v-img
          :src="`https://picsum.photos/800/300?random=${n}`"
          
          cover></v-img>
        </v-carousel-item>
      </v-carousel>
    </v-sheet>

    <!-- Footer -->
    <v-sheet class="mt-8 pa-4 text-center" color="grey-lighten-4" rounded="lg">
      <h4>Stay updated with upcoming campus activities!</h4>
      <p>Join clubs, explore the marketplace, and connect with fellow students.</p>
    </v-sheet>
  </v-container>
</template>

<style scoped>
.banner-section {
  background: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b')
    center/cover no-repeat;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}
.hover-scale {
  transition: all 0.3s ease;
}
.hover-scale:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}
</style>

<!-- <style scoped>
.notification-card {
    border-radius: 20px;
    transition: all 0.3s ease;
}
.notification-card:hover{
    transform: translateY(-4px);
}
</style> -->