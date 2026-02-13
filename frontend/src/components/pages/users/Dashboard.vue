<script setup lang="ts">
import { ref } from 'vue';

// --- Dummy Data 
const marketplaceItems = ref([
  {
    id: 1,
    title: 'Business Law Textbooks',
    price: 'KES 1,200',
    location: 'Karen Campus',
    time: '2h ago',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCR0uJAi7rqUgmWHqehbHTyIbEMvokW5L7NOLJyQu3ijgqUm4pln3rZxF0LRSNOBqcRpJ5Jl6ui7VBtHNVsiP1kOMFJaEispyFCwj7eTrgNnF0ZaOger6rfvtBHS8RfiihevBZod9G5o8UZ4mlQZhCmEVsWMIIoTJ8Mvks3n2-uO6fd7gI1HaLbPxQQxvKhpPcp4dfnhxAuBEwLuA_znBM6DeSvUS-m5CgE3MEANY54ZFOrpIF61WKlF_m5EX0wMFPkmXjwcpK4XB4'
  },
  {
    id: 2,
    title: 'Laptop Cooling Pad',
    price: 'KES 800',
    location: 'Hostel Block A',
    time: '5h ago',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB9If3y41wUUIdryqe5fRWQ2zV0tfjxeBMRC56RyjlkS-IsHmT6GVDw5W6Z0tjgGHITvQAzXv0WsrJDVaxoYrTnDdjCcJoX3HMEjxJrjONsfbOX8Km4vyUSYflwkI3i9Yu3wV2g8u05dyaIHk2U5BGJEBPkEzT8Wjbxe274lX0ZdxaJZLPS28ei4ZZD4UT15kBdTFMi36qc1mG0QArYdsoUHvwC8EH2i2EzPdxD3AuQ5QBLJd4qEV1G3bPWHtITyENm4f3ZpTHoqfk'
  },
  {
    id: 3,
    title: 'Smart Watch Series 4',
    price: 'KES 4,500',
    location: 'Main Campus',
    time: '1d ago',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCrBi6Vx_LXem2LwIA5yHWwLPsjJlJBufTIQYixdmqiw8h0WjWmCGnSH7FKor-DI7DYsLBT5ZmPXVl68WQCOaywydARV1GbNJJJMw90Wenz72vxjY3ASpYdUhE3pdDudzaVPbaZhHGge-F2qM_PBxIW-mVRADqeeK2BEDZVAe_n9tpA-OJ_LbNgXNQZVMpo33Kk3KYy-sGO2_8lcqLKhb_PlsOMOX30HpWS2G4g1PKvK-Ue6rbmrn8QQZMr0V7crlVeWp76mKEqkEA'
  }
]);

const upcomingEvents = ref([
  {
    id: 1,
    month: 'Feb',
    day: '15',
    title: 'Tech Innovators Meetup',
    location: 'Computer Lab 3',
    icon: 'mdi-map-marker-outline',
    highlight: true // Applies the orange styling
  },
  {
    id: 2,
    month: 'Feb',
    day: '18',
    title: 'Entrepreneurship Workshop',
    location: 'Google Meet',
    icon: 'mdi-video-outline',
    highlight: false
  },
  {
    id: 3,
    month: 'Feb',
    day: '21',
    title: 'Debate Club Finals',
    location: 'Lecture Theatre 1',
    icon: 'mdi-map-marker-outline',
    highlight: false
  }
]);
// --- Feed Posts Data ---
const feedPosts = ref([
  {
    id: 1,
    isImagePost: true,
    category: 'Technology',
    categoryColor: 'primary',
    timestamp: 'Just now',
    title: 'Annual CUK Tech Hackathon 2026 Kicks Off!',
    content: 'Join the School of Computing and Mathematics as students battle it out in a 48-hour coding marathon. Watch the live stream or drop by the Innovation Hub.',
    image: "data:image/jpeg;base64,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",
    avatars: [
      'https://lh3.googleusercontent.com/aida-public/AB6AXuD2pHk35mYTDPutwEtWbbW00A1xZyHTIPVUJwgRdHLc6n6XGDOg5cjOJcSavoVmQSIhwnOT7Y2OCSoHrGhLHPCkHm0JWnBICO9zx7Y4imM1dqdyIaXzn5MMCnSW2hM9wH_7ZXx_RZnIibA_a8_WobcYC8feg2O-q78XsKwwYGXGIoCKdI97SAVx-7w1NfnB9T-OTOvRAZowhpLljRh2mpCPqVMqIH-kM_c_PYbXquLy_XsRCE1iZ_txwU6olma9svigPQNEKNuJELY',
      'https://lh3.googleusercontent.com/aida-public/AB6AXuDEZ4FcC3PaNtuGiWBbjeM0tyhyaxXBLrsEGJzJDPyK-gNPnHhnisHYkJaImg1UA_SlbULHdylQTimbXDG4zwlwPSHbEJ5BJoIfImpyoPh3bVLegyK24uNSPrnfdpBHJVg11aQ3gew-btGOlQaJ8Rp3v_UgYengMnrHuuxXwR9Ja9NYnOtYzvJH6j5iduyk5vI_sEOhajlBHmCX_tY_2Nkx4hTe6elGgh7RlFXMcrhyucCwqEy7lai0OW10_tWkl2IOyKY-oSM0CdI'
    ],
    interactionCount: '+124',
    actionText: 'Share'
  },
  {
    id: 2,
    isImagePost: false,
    category: 'Athletics',
    categoryColor: 'blue-darken-2',
    icon: 'mdi-soccer',
    iconBg: 'blue-lighten-5',
    timestamp: '2 hours ago',
    title: 'Inter-Faculty Tournament Registration',
    content: 'Registration for Football, Basketball, and Volleyball is now open at the Sports Office. Form your faculty teams before Friday.',
    actionText: 'Register Team',
    actionColor: 'grey-darken-4'
  },
  {
    id: 3,
    isImagePost: true,
    category: 'Student Life',
    categoryColor: 'purple-darken-2',
    timestamp: '4 hours ago',
    title: 'Mr. & Miss Coop 2026 Auditions',
    content: 'Do you have what it takes to be the next face of The Co-operative University? Auditions are happening this weekend at the Main Hall. Dress to impress!',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuCR0uJAi7rqUgmWHqehbHTyIbEMvokW5L7NOLJyQu3ijgqUm4pln3rZxF0LRSNOBqcRpJ5Jl6ui7VBtHNVsiP1kOMFJaEispyFCwj7eTrgNnF0ZaOger6rfvtBHS8RfiihevBZod9G5o8UZ4mlQZhCmEVsWMIIoTJ8Mvks3n2-uO6fd7gI1HaLbPxQQxvKhpPcp4dfnhxAuBEwLuA_znBM6DeSvUS-m5CgE3MEANY54ZFOrpIF61WKlF_m5EX0wMFPkmXjwcpK4XB4',
    avatars: [
      'https://lh3.googleusercontent.com/aida-public/AB6AXuB9If3y41wUUIdryqe5fRWQ2zV0tfjxeBMRC56RyjlkS-IsHmT6GVDw5W6Z0tjgGHITvQAzXv0WsrJDVaxoYrTnDdjCcJoX3HMEjxJrjONsfbOX8Km4vyUSYflwkI3i9Yu3wV2g8u05dyaIHk2U5BGJEBPkEzT8Wjbxe274lX0ZdxaJZLPS28ei4ZZD4UT15kBdTFMi36qc1mG0QArYdsoUHvwC8EH2i2EzPdxD3AuQ5QBLJd4qEV1G3bPWHtITyENm4f3ZpTHoqfk',
      'https://lh3.googleusercontent.com/aida-public/AB6AXuCrBi6Vx_LXem2LwIA5yHWwLPsjJlJBufTIQYixdmqiw8h0WjWmCGnSH7FKor-DI7DYsLBT5ZmPXVl68WQCOaywydARV1GbNJJJMw90Wenz72vxjY3ASpYdUhE3pdDudzaVPbaZhHGge-F2qM_PBxIW-mVRADqeeK2BEDZVAe_n9tpA-OJ_LbNgXNQZVMpo33Kk3KYy-sGO2_8lcqLKhb_PlsOMOX30HpWS2G4g1PKvK-Ue6rbmrn8QQZMr0V7crlVeWp76mKEqkEA'
    ],
    interactionCount: '+89',
    actionText: 'Share'
  },
  {
    id: 4,
    isImagePost: false,
    category: 'Administration',
    categoryColor: 'red-darken-2',
    icon: 'mdi-cash-register',
    iconBg: 'red-lighten-5',
    timestamp: 'Yesterday at 10:00 AM',
    title: 'Fee Balance Clearance Deadline',
    content: 'A gentle reminder to all students to clear their semester fee balances by the 20th of February to avoid missing out on the upcoming continuous assessments (CATs).',
    actionText: 'Pay via Portal',
    actionColor: 'red-darken-2'
  },
  {
    id: 5,
    isImagePost: true,
    category: 'Clubs & Societies',
    categoryColor: 'green-darken-2',
    timestamp: 'Yesterday at 4:30 PM',
    title: 'Environmental Club Tree Planting Drive',
    content: 'Join us this Saturday as we plant 500 trees around the Karen campus boundary. T-shirts and refreshments will be provided to all student volunteers.',
    image: 'https://lh3.googleusercontent.com/aida-public/AB6AXuB-Ai0qcZuy-DyVFVtQ-lQRF3b0i_5XE8yNHSnAcoMX5LBmFELPK69YOxjbMwi9KqPYGxdo0lDvkmlQoG7R9C3Ho0HplU0eMkA_mB52SeSNwhWGEgFHgR4JQQc1priBVVEjmf_l-qVIT353ltqxdg10yCPkw6F-pOBrsBjNivTO9L2JxmRjAYhpIbzDY0p1nICjfXU94iufOgv4vCNvxqaKUhg58u8z0yHVa2FHmKNzbvMqoJpbRJwzj_MspJu_rjxebc6jlKdXy1U', 
    avatars: [
      'https://lh3.googleusercontent.com/aida-public/AB6AXuD2pHk35mYTDPutwEtWbbW00A1xZyHTIPVUJwgRdHLc6n6XGDOg5cjOJcSavoVmQSIhwnOT7Y2OCSoHrGhLHPCkHm0JWnBICO9zx7Y4imM1dqdyIaXzn5MMCnSW2hM9wH_7ZXx_RZnIibA_a8_WobcYC8feg2O-q78XsKwwYGXGIoCKdI97SAVx-7w1NfnB9T-OTOvRAZowhpLljRh2mpCPqVMqIH-kM_c_PYbXquLy_XsRCE1iZ_txwU6olma9svigPQNEKNuJELY',
      'https://lh3.googleusercontent.com/aida-public/AB6AXuB9If3y41wUUIdryqe5fRWQ2zV0tfjxeBMRC56RyjlkS-IsHmT6GVDw5W6Z0tjgGHITvQAzXv0WsrJDVaxoYrTnDdjCcJoX3HMEjxJrjONsfbOX8Km4vyUSYflwkI3i9Yu3wV2g8u05dyaIHk2U5BGJEBPkEzT8Wjbxe274lX0ZdxaJZLPS28ei4ZZD4UT15kBdTFMi36qc1mG0QArYdsoUHvwC8EH2i2EzPdxD3AuQ5QBLJd4qEV1G3bPWHtITyENm4f3ZpTHoqfk'
    ],
    interactionCount: '+34',
    actionText: 'Share'
  }
]);
</script>

<template>
  <v-container fluid class="pa-6" style="max-width: 1200px;">
    <v-row>
      <v-col cols="12" lg="8">
        <div class="d-flex align-center justify-space-between mb-4">
          <div class="d-flex align-center gap-2">
            <div class="pulsing-dot bg-primary"></div>
            <h2 class="text-h6 font-weight-bold text-grey-darken-4">Happening Now</h2>
          </div>
          <v-btn variant="text" color="primary" size="small" class="text-none font-weight-bold">
            View All News
          </v-btn>
        </div>

        <v-alert
          variant="tonal"
          color="error"
          icon="mdi-alert-outline"
          class="mb-6 rounded-xl border-error-subtle"
          border="start"
        >
          <div class="font-weight-bold text-error">Security Notice: Ongoing Construction</div>
          <div class="text-body-2 mt-1">
            students are urged to avoid the student centre since there is ongoing construction to avoid accidents
          </div>
          <div class="text-caption font-weight-bold text-uppercase mt-2">
            Urgent Alert • 10 mins ago
          </div>
        </v-alert>

        <div v-for = "post in feedPosts" :key="post.id" class="d-flex flex-column gap-6">
          <v-card v-if="post.isImagePost" class="rounded-xl elevation-2 border-opacity-50 mb-6" border>
                  <v-img :src="post.image" :alt="post.title" height="250" cover></v-img>
            
            <div class="pa-6">
              <div class="d-flex align-center mb-3 gap-2">
                <v-chip :color="post.categoryColor" size="small" variant="tonal" class="text-uppercase font-weight-bold rounded">
                  {{ post.category }}
                </v-chip>
                <span class="text-caption text-grey-darken-1">{{ post.timestamp }}</span>
              </div>
              
              <h3 class="text-h5 font-weight-bold mb-2">{{ post.title }}</h3>
              <p class="text-body-2 text-grey-darken-2 mb-4">{{ post.content }}</p>
              
              <v-divider class="my-4"></v-divider>
              
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center stacked-avatars">
                  <v-avatar v-for="(avatar, index) in post.avatars" :key="index" size="32" class="border-white" :image="avatar"></v-avatar>
                  <v-avatar size="32" class="border-white bg-grey-lighten-3 text-caption font-weight-bold text-grey-darken-2">
                    {{ post.interactionCount }}
                  </v-avatar>
                </div>
                <v-btn variant="text" color="grey-darken-1" prepend-icon="mdi-share-variant-outline" class="text-none">
                  {{ post.actionText }}
                </v-btn>
              </div>
            </div>
    </v-card>

    <v-card v-else class="rounded-xl elevation-2 pa-6 border-opacity-50 mb-6" border>
      <div class="d-flex gap-4">
        <div class="d-flex align-center justify-center rounded-lg shrink-0" :class="'bg-' + post.iconBg" style="width: 64px; height: 64px;">
          <v-icon :icon="post.icon" :color="post.categoryColor" size="32"></v-icon>
        </div>
        
        <div class="flex-grow-1">
          <div class="d-flex align-center mb-1 gap-2">
            <v-chip :color="post.categoryColor" size="small" variant="tonal" class="text-uppercase font-weight-bold rounded">
              {{ post.category }}
            </v-chip>
            <span class="text-caption text-grey-darken-1">{{ post.timestamp }}</span>
          </div>
          
          <h3 class="text-h6 font-weight-bold">{{ post.title }}</h3>
          <p class="text-body-2 text-grey-darken-1 mt-1">{{ post.content }}</p>
          
          <v-btn :color="post.actionColor" size="small" class="text-none font-weight-bold mt-4 rounded-lg px-4">
            {{ post.actionText }}
          </v-btn>
        </div>
      </div>
    </v-card>          
         
          <!-- <v-card class="rounded-xl elevation-2 border-opacity-50" border>
            <v-img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuAlNtJdaBpQVgZ7bSlVvvIsYSBNwZO3vLSmQKibmMWQskkf4sx7pZ0XV7VxkcJnf2s86YOWchhNfpl9w8zTTFaneddpgBbgh7mJwnXoB6BBUYiqclMwY4aOxw2ziysQ1-W4JBCd-tyPPsjp1gktQuC1uISbfeDQcZTVIpHdPCKvnlYJSNtPMQeWJlG3PEaicWB2wAUHZhbNz0Lx_1QMjSPOAjciDNrB8w240XDvMPDtDxofx60tpcyrLFox4Lbwo2abLIxyb4OUUFk"
              alt="Library Update"
              height="250"
              cover
            ></v-img>
            
            <div class="pa-6">
              <div class="d-flex align-center mb-3 gap-2">
                <v-chip color="primary" size="small" variant="tonal" class="text-uppercase font-weight-bold rounded">
                  Academic
                </v-chip>
                <span class="text-caption text-grey-darken-1">Today at 9:00 AM</span>
              </div>
              
              <h3 class="text-h5 font-weight-bold mb-2">Library Hours Extended for Exams Week</h3>
              <p class="text-body-2 text-grey-darken-2 mb-4">
                To support your revision efforts, the University Library will remain open 24/7 starting this Monday until the end of the semester examination period.
              </p>
              
              <v-divider class="my-4"></v-divider>
              
              <div class="d-flex align-center justify-space-between">
                <div class="d-flex align-center stacked-avatars">
                  <v-avatar size="32" class="border-white" image="https://lh3.googleusercontent.com/aida-public/AB6AXuD2pHk35mYTDPutwEtWbbW00A1xZyHTIPVUJwgRdHLc6n6XGDOg5cjOJcSavoVmQSIhwnOT7Y2OCSoHrGhLHPCkHm0JWnBICO9zx7Y4imM1dqdyIaXzn5MMCnSW2hM9wH_7ZXx_RZnIibA_a8_WobcYC8feg2O-q78XsKwwYGXGIoCKdI97SAVx-7w1NfnB9T-OTOvRAZowhpLljRh2mpCPqVMqIH-kM_c_PYbXquLy_XsRCE1iZ_txwU6olma9svigPQNEKNuJELY"></v-avatar>
                  <v-avatar size="32" class="border-white" image="https://lh3.googleusercontent.com/aida-public/AB6AXuDEZ4FcC3PaNtuGiWBbjeM0tyhyaxXBLrsEGJzJDPyK-gNPnHhnisHYkJaImg1UA_SlbULHdylQTimbXDG4zwlwPSHbEJ5BJoIfImpyoPh3bVLegyK24uNSPrnfdpBHJVg11aQ3gew-btGOlQaJ8Rp3v_UgYengMnrHuuxXwR9Ja9NYnOtYzvJH6j5iduyk5vI_sEOhajlBHmCX_tY_2Nkx4hTe6elGgh7RlFXMcrhyucCwqEy7lai0OW10_tWkl2IOyKY-oSM0CdI"></v-avatar>
                  <v-avatar size="32" class="border-white bg-grey-lighten-3 text-caption font-weight-bold text-grey-darken-2">
                    +42
                  </v-avatar>
                </div>
                
                <v-btn variant="text" color="grey-darken-1" prepend-icon="mdi-share-variant-outline" class="text-none">
                  Share
                </v-btn>
              </div>
            </div>
          </v-card>
          <v-card class="rounded-xl elevation-2 pa-6 border-opacity-50" border>
            <div class="d-flex gap-4">
              <div class="d-flex align-center justify-center rounded-lg bg-blue-lighten-5 shrink-0" style="width: 64px; height: 64px;">
                <v-icon icon="mdi-soccer" color="blue-darken-2" size="32"></v-icon>
              </div>
              
              <div class="flex-grow-1">
                <div class="d-flex align-center mb-1 gap-2">
                  <v-chip color="blue-darken-2" size="small" variant="tonal" class="text-uppercase font-weight-bold rounded">
                    Athletics
                  </v-chip>
                  <span class="text-caption text-grey-darken-1">Yesterday</span>
                </div>
                
                <h3 class="text-h6 font-weight-bold">Inter-Faculty Tournament Registration</h3>
                <p class="text-body-2 text-grey-darken-1 mt-1">
                  Registration for Football, Basketball, and Volleyball is now open at the Sports Office.
                </p>
                
                <v-btn color="grey-darken-4" size="small" class="text-none font-weight-bold mt-4 rounded-lg px-4">
                  Register Team
                </v-btn>
              </div>
            </div>
          </v-card> -->

        </div>
      </v-col>

      <v-col cols="12" lg="4">
        <div class="d-flex sticky-sidebar flex-column gap-6">

          <v-card class="rounded-xl elevation-2 border-opacity-50" border>
            <div class="d-flex justify-space-between align-center pa-4 bg-grey-lighten-4 border-bottom">
              <div class="text-subtitle-2 font-weight-bold d-flex align-center gap-2">
                <v-icon icon="mdi-shopping-outline" color="primary" size="small"></v-icon>
                Marketplace
              </div>
              <span class="text-caption font-weight-bold text-primary text-uppercase cursor-pointer hover-underline">
                Post Listing
              </span>
            </div>

            <div class="pa-4 d-flex flex-column gap-4">
              <div v-for="item in marketplaceItems" :key="item.id" class="d-flex gap-3 cursor-pointer marketplace-item">
                <v-img :src="item.image" :alt="item.title" width="64" height="64" cover class="rounded-lg shrink-0"></v-img>
                <div>
                  <div class="text-body-2 font-weight-medium item-title transition-colors">{{ item.title }}</div>
                  <div class="text-caption font-weight-bold text-primary mt-1">{{ item.price }}</div>
                  <div class="text-caption text-grey mt-1" style="font-size: 10px !important;">
                    {{ item.location }} • {{ item.time }}
                  </div>
                </div>
              </div>
            </div>

            <v-divider></v-divider>
            <v-btn variant="text" block color="grey-darken-1" class="text-none text-caption font-weight-bold py-3 rounded-0 bg-grey-lighten-5">
              View All Listings
            </v-btn>
          </v-card>

          <v-card class="rounded-xl elevation-2 border-opacity-50" border>
             <div class="d-flex justify-space-between align-center pa-4 bg-grey-lighten-4 border-bottom">
              <div class="text-subtitle-2 font-weight-bold d-flex align-center gap-2">
                <v-icon icon="mdi-calendar-outline" color="primary" size="small"></v-icon>
                Upcoming Clubs
              </div>
              <span class="text-caption font-weight-bold text-grey-darken-1 text-uppercase">
                Feb 2026
              </span>
            </div>

            <div class="pa-4 d-flex flex-column gap-5">
              <div v-for="event in upcomingEvents" :key="event.id" class="d-flex gap-4">
                
                <div 
                  class="d-flex flex-column align-center justify-center rounded-xl border shrink-0" 
                  style="width: 48px; height: 56px;"
                  :class="event.highlight ? 'bg-orange-lighten-5 border-orange-lighten-3' : 'bg-grey-lighten-4 border-grey-lighten-2'"
                >
                  <span class="text-uppercase font-weight-bold" style="font-size: 10px;" :class="event.highlight ? 'text-primary' : 'text-grey-darken-1'">
                    {{ event.month }}
                  </span>
                  <span class="text-h6 font-weight-bold leading-none mt-1" :class="event.highlight ? 'text-primary' : 'text-grey-darken-2'">
                    {{ event.day }}
                  </span>
                </div>

                <div class="flex-grow-1">
                  <div class="text-body-2 font-weight-bold text-truncate">{{ event.title }}</div>
                  <div class="text-caption text-grey-darken-1 d-flex align-center gap-1 mt-1">
                    <v-icon :icon="event.icon" size="14"></v-icon>
                    {{ event.location }}
                  </div>
                  <v-btn v-if="event.highlight" color="primary" size="x-small" rounded="pill" class="mt-2 text-none font-weight-bold">
                    Remind Me
                  </v-btn>
                </div>
              </div>
            </div>
          </v-card>

          <div class="pa-5 rounded-xl text-white elevation-4 gradient-bg">
            <h4 class="text-subtitle-2 font-weight-bold mb-3">Campus Helplines</h4>
            <div class="d-flex flex-column gap-3">
              <div class="d-flex justify-space-between align-center text-caption">
                <span>Emergency Security</span>
                <span class="font-weight-bold" style="font-family: monospace;">+254 7XX XXX XXX</span>
              </div>
              <div class="d-flex justify-space-between align-center text-caption">
                <span>Health Clinic</span>
                <span class="font-weight-bold" style="font-family: monospace;">+254 7XX XXX XXX</span>
              </div>
              <v-btn variant="tonal" block class="mt-2 text-none font-weight-bold bg-white-opacity">
                Call for Assistance
              </v-btn>
            </div>
          </div>

        </div>
      </v-col>

    </v-row>
  </v-container>
</template>

<style scoped>
/* 1. Spacing Helpers */
.gap-1 { gap: 4px; }
.gap-2 { gap: 8px; }
.gap-3 { gap: 12px; }
.gap-4 { gap: 16px; }
.gap-5 { gap: 20px; }
.gap-6 { gap: 24px; }

/* 2. Custom Border Color for Alert */
.border-error-subtle {
  border-color: rgba(var(--v-theme-error), 0.3) !important;
}

/* 3. Pulsing Dot Animation */
.pulsing-dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: .5; }
}

/* 4. Stacked Avatars */
.stacked-avatars .v-avatar {
  border: 2px solid white;
  margin-left: -8px;
}
.stacked-avatars .v-avatar:first-child {
  margin-left: 0;
}

/* 5. Widget Specific Styles */
.border-bottom {
  border-bottom: 1px solid rgba(0,0,0,0.05);
}
.cursor-pointer {
  cursor: pointer;
}
.hover-underline:hover {
  text-decoration: underline;
}

/* 6. Marketplace Hover Effect (Vue equivalent to group-hover) */
.marketplace-item:hover .item-title {
  color: rgb(var(--v-theme-primary));
}
.transition-colors {
  transition: color 0.2s ease-in-out;
}

/* 7. Gradient Background for Helplines */
.gradient-bg {
  background: linear-gradient(135deg, #ec7f13 0%, #d84315 100%);
}
.bg-white-opacity {
  background-color: rgba(255, 255, 255, 0.2) !important;
}
.sticky-sidebar {
  /* 1. Make it stick to the screen */
  position: sticky;
  
  /* 2. Stop 88px from the top (leaves room for a top app-bar) */
  top: 88px; 
  
  /* 3. Give it a maximum height so it doesn't run off the bottom */
  max-height: 250vh ; /*update this later !!!!*/
  
  /* 4. Allow the sidebar to scroll internally IF the widgets are too tall */
  overflow-y: auto; 
}

/* Optional: Hide the ugly scrollbar on the sidebar for a cleaner, modern look */
.sticky-sidebar::-webkit-scrollbar {
  display: none; /* Chrome, Safari and Opera */
}
.sticky-sidebar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>