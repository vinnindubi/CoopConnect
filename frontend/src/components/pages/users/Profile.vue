<script setup lang="ts">
import { ref, onMounted} from 'vue';
import { getUserProfile } from '@/services/authService';

// ==========================================
// 1. STATE: Personal Information
// ==========================================
const isEditingProfile = ref(false);
const showSnackbar = ref(false);
const snackbarText = ref('');

// const profile2 = ref({
//   name: 'Alex Johnson',
//   email: 'alex.j@students.coop.ac.ke',
//   course: 'BSc. Software Engineering',
//   year: '3rd Year',
//   phone: '+254 712 345 678',
//   bio: 'Passionate about coding, campus events, and selling the best used textbooks. Let me know if you need any past papers!',
//   avatar: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=300',
//   coverPhoto: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=1200',
//   verificationStatus: 'Pending', 
//   storeCategories: ['Electronics', 'Academic Material'] 
// });

const profile = ref({
  name:'',
  admission:'',
  email:'',
  course: '',
  year:'',
  phone: '',
  bio: '',
  avatar : '',
  coverPhoto:'',
  verificationStatus:'',
  storeCategories: []
});
const categoryOptions = ['Clothing', 'Electronics', 'Academic Material', 'Services', 'Dorm Gear', 'Food & Snacks', 'Other'];

const editForm = ref(JSON.parse(JSON.stringify(profile.value)));

const saveProfile = () => {
  profile.value = JSON.parse(JSON.stringify(editForm.value));
  isEditingProfile.value = false;
  triggerSnackbar('Profile updated successfully!');
};

const cancelEdit = () => {
  editForm.value = JSON.parse(JSON.stringify(profile.value));
  isEditingProfile.value = false;
};

// ==========================================
// 2. STATE: Articles
// ==========================================
const articles = ref([
  { id: 1, title: 'Surviving Finals Week', date: 'Oct 12, 2025', excerpt: 'My top 5 tips for not losing your mind during exams.' },
  { id: 2, title: 'Best Cheap Eats Around Campus', date: 'Sep 28, 2025', excerpt: 'Where to get the best chapati and beans for under KES 150.' }
]); 

const triggerArticleAction = (action: string, id?: number) => {
  triggerSnackbar(`${action} Article ${id ? '#' + id : ''}`);
};

// ==========================================
// 3. STATE: Seller Catalogue
// ==========================================
const catalogue = ref([
  { id: 1, name: 'Advanced Calculus Textbook', price: 'KES 1,200', image: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=300' },
  { id: 2, name: 'MacBook Pro Charger', price: 'KES 3,500', image: 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?auto=format&fit=crop&q=80&w=300' },
  { id: 3, name: 'Drawing Tablet', price: 'KES 5,000', image: 'https://images.unsplash.com/photo-1584905066893-7d5c142ba4e1?auto=format&fit=crop&q=80&w=300' }
]);

const triggerProductAction = (action: string, id?: number) => {
  triggerSnackbar(`${action} Product ${id ? '#' + id : ''}`);
};

// ==========================================
// 4. STATE: Reviews & Ratings
// ==========================================
const reviewsData = ref({
  averageRating: 4.8,
  totalReviews: 24,
  distribution: [
    { stars: 5, percentage: 85 },
    { stars: 4, percentage: 10 },
    { stars: 3, percentage: 5 },
    { stars: 2, percentage: 0 },
    { stars: 1, percentage: 0 },
  ]
});

const reviews = ref([
  { 
    id: 1, 
    reviewerName: 'Samuel Kamau', 
    avatar: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&q=80&w=150', 
    rating: 5, 
    date: 'Oct 20, 2025', 
    text: 'Great seller! The textbook was in perfect condition and he met me at the student center right on time.' 
  },
  { 
    id: 2, 
    reviewerName: 'Faith Mutuku', 
    avatar: 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=150', 
    rating: 4, 
    date: 'Sep 28, 2025', 
    text: 'Really helpful with the coding assignment tutoring. We had to reschedule once, but otherwise excellent.' 
  },
  { 
    id: 3, 
    reviewerName: 'Brian Omondi', 
    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150', 
    rating: 5, 
    date: 'Sep 15, 2025', 
    text: 'Bought the MacBook charger. Works perfectly. Highly recommend this seller!' 
  }
]);

// ==========================================
// Helper
// ==========================================
const triggerSnackbar = (text: string) => {
  snackbarText.value = text;
  showSnackbar.value = true;
};
const getUser = async () => {
  try{
       const response = await getUserProfile();
       const userData = response.data.data;
       profile.value.name = userData.fullname;
       profile.value.email = userData.email;
       profile.value.admission = userData.admission
  }catch(error){
    console.error('Error fetching profile:', error);
  }
  
}
onMounted(() => {
  getUser();
  });
</script>

<template>
  <div class="bg-background min-h-screen py-8">
    <v-container style="max-width: 900px;">
      
      <v-card class="rounded-xl elevation-2 border-opacity-50 bg-surface mb-10 overflow-hidden" border>
        
        <v-img :src="profile.coverPhoto" height="180" cover class="bg-grey-lighten-2"></v-img>

        <div class="px-6 pb-8 px-md-10">
          
          <div class="d-flex justify-space-between align-end mb-6" style="margin-top: -50px;">
            <div class="pa-1 bg-surface rounded-circle elevation-2">
              <v-avatar size="100" class="rounded-circle">
                <v-img :src="profile.avatar" cover></v-img>
              </v-avatar>
            </div>
            
            <v-btn 
              v-if="!isEditingProfile"
              color="primary" 
              variant="flat" 
              class="text-none font-weight-bold rounded-lg px-6"
              @click="isEditingProfile = true"
            >
              <v-icon start icon="mdi-pencil"></v-icon> Edit Profile
            </v-btn>
          </div>

          <div v-if="!isEditingProfile">
            
            <h1 class="text-h4 font-weight-black text-high-emphasis mb-2 d-flex align-center flex-wrap">
              <span class="mr-3">{{ profile.name }}</span>
              <v-chip 
                :color="profile.verificationStatus === 'Verified' ? 'success' : 'warning'" 
                variant="tonal"
                size="small" 
                class="font-weight-bold text-uppercase mt-1"
              >
                <v-icon start :icon="profile.verificationStatus === 'Verified' ? 'mdi-check-decagram' : 'mdi-clock-outline'" size="16"></v-icon>
                {{ profile.verificationStatus }}
              </v-chip>
            </h1>
            
            <div class="text-body-1 text-primary font-weight-bold mb-4">
              {{ profile.course }} <span class="text-medium-emphasis mx-1">•</span> {{ profile.year }}
            </div>
            
            <p class="text-body-1 text-high-emphasis mb-6" style="max-width: 700px;">
              {{ profile.bio }}
            </p>

            <v-divider class="mb-5 opacity-20"></v-divider>

            <v-row class="text-body-2">
              <v-col cols="12" sm="6" md="4" class="mb-2">
                <div class="text-caption text-medium-emphasis font-weight-bold mb-2 text-uppercase">Contact Info</div>
                <div class="d-flex align-center mb-2">
                  <v-icon icon="mdi-email-outline" size="16" class="mr-2"></v-icon> {{ profile.email }}
                </div>
                <div class="d-flex align-center">
                  <v-icon icon="mdi-phone-outline" size="16" class="mr-2"></v-icon> {{ profile.phone }}
                </div>
              </v-col>
              
              <v-col cols="12" sm="6" md="8" class="mb-2">
                <div class="text-caption text-medium-emphasis font-weight-bold mb-2 text-uppercase">Store Categories</div>
                <div v-if="profile.storeCategories.length > 0" class="d-flex flex-wrap">
                  <v-chip v-for="cat in profile.storeCategories" :key="cat" size="small" variant="outlined" class="font-weight-bold border-opacity-50 mr-2 mb-2">
                    {{ cat }}
                  </v-chip>
                </div>
                <div v-else class="text-medium-emphasis font-italic">No categories selected.</div>
              </v-col>
            </v-row>
          </div>

          <v-form v-else @submit.prevent="saveProfile" class="mt-4">
            <v-row>
              <v-col cols="12" sm="6">
                <label class="text-caption font-weight-bold mb-1 d-block">Full Name</label>
                <v-text-field v-model="profile.name" variant="outlined" density="compact" class="rounded-lg mb-2"></v-text-field>
                
                <label class="text-caption font-weight-bold mb-1 d-block">Course/Major</label>
                <v-text-field v-model="profile.course" variant="outlined" density="compact" class="rounded-lg mb-2"></v-text-field>

                <label class="text-caption font-weight-bold mb-1 d-block">Year of Study</label>
                <v-text-field v-model="profile.year" variant="outlined" density="compact" class="rounded-lg mb-2"></v-text-field>
              </v-col>
              
              <v-col cols="12" sm="6">
                <label class="text-caption font-weight-bold mb-1 d-block">Email Address</label>
                <v-text-field v-model="profile.email" variant="outlined" density="compact" class="rounded-lg mb-2"></v-text-field>
                
                <label class="text-caption font-weight-bold mb-1 d-block">Phone Number</label>
                <v-text-field v-model="profile.phone" variant="outlined" density="compact" class="rounded-lg mb-2"></v-text-field>
                
                <label class="text-caption font-weight-bold mb-1 d-block">Store Categories (Select all that apply)</label>
                <v-select 
                  v-model="profile.storeCategories" 
                  :items="categoryOptions" 
                  multiple 
                  chips 
                  closable-chips
                  variant="outlined" 
                  density="compact" 
                  class="rounded-lg mb-2"
                  placeholder="What do you sell?"
                ></v-select>
              </v-col>

              <v-col cols="12" class="pt-0">
                <label class="text-caption font-weight-bold mb-1 d-block">About Me / Bio</label>
                <v-textarea v-model="profile.bio" variant="outlined" rows="3" auto-grow hide-details class="rounded-lg"></v-textarea>
              </v-col>
            </v-row>
            
            <div class="d-flex justify-end mt-6">
              <v-btn variant="text" color="medium-emphasis" class="text-none font-weight-bold mr-3" @click="cancelEdit">Cancel</v-btn>
              <v-btn type="submit" color="primary" variant="flat" class="text-none font-weight-bold px-6 rounded-lg">Save Profile</v-btn>
            </div>
          </v-form>

        </div>
      </v-card>
      <div class="mb-10">
        <div class="d-flex justify-space-between align-end mb-4 px-2">
          <div>
            <h2 class="text-h5 font-weight-black text-high-emphasis">My Articles</h2>
            <p class="text-body-2 text-medium-emphasis mt-1">Manage your stories, tips, and experiences.</p>
          </div>
          <v-btn v-if="articles.length > 0" color="primary" variant="flat" size="small" class="text-none font-weight-bold rounded-lg mb-2" @click="triggerArticleAction('Drafting New')">
            <v-icon start icon="mdi-plus"></v-icon> Write Article
          </v-btn>
        </div>

        <div v-if="articles.length > 0">
          <v-card v-for="article in articles" :key="article.id" class="rounded-xl elevation-2 border-opacity-50 pa-5 bg-surface mb-4" border hover>
            
            <div class="d-flex flex-column flex-sm-row justify-space-between align-sm-start">
              <div class="mb-4 mb-sm-0 pr-sm-4 flex-grow-1">
                <h3 class="text-h6 font-weight-bold text-high-emphasis mb-1">{{ article.title }}</h3>
                <div class="text-caption text-medium-emphasis mb-2">Published: {{ article.date }}</div>
                <p class="text-body-2 text-medium-emphasis mb-0">{{ article.excerpt }}</p>
              </div>
              
              <div class="d-flex shrink-0">
                <v-btn color="primary" variant="tonal" size="small" class="text-none font-weight-bold rounded-lg mr-2" @click="triggerArticleAction('Editing', article.id)">
                  Edit
                </v-btn>
                <v-btn color="error" variant="tonal" size="small" class="text-none font-weight-bold rounded-lg" @click="triggerArticleAction('Deleting', article.id)">
                  Delete
                </v-btn>
              </div>
            </div>

          </v-card>
        </div>

        <v-card v-else class="rounded-xl border border-dashed border-purple bg-surface-variant bg-opacity-30 pa-8 text-center" elevation="0">
          <v-avatar color="blue" variant="tonal" size="64" class="mb-4">
            <v-icon icon="mdi-fountain-pen-tip" size="32"></v-icon>
          </v-avatar>
          <h3 class="text-h5 font-weight-bold text-high-emphasis mb-2">Share your campus story</h3>
          <p class="text-body-1 text-medium-emphasis mb-6 mx-auto" style="max-width: 450px;">
            Whether it's a guide to surviving finals or a review of local housing, your peers want to hear from you.
          </p>
          <v-btn color="blue" variant="flat" size="large" class="text-none font-weight-bold rounded-lg px-8" @click="triggerArticleAction('Drafting New')">
            Write Your First Article
          </v-btn>
        </v-card>
      </div>
      <div class="mb-10">
        <div class="d-flex justify-space-between align-end mb-4 px-2">
          <div>
            <h2 class="text-h5 font-weight-black text-high-emphasis">My Store</h2>
            <p class="text-body-2 text-medium-emphasis mt-1">Manage the items you are selling on campus.</p>
          </div>
          <v-btn v-if="catalogue.length > 0" color="primary" variant="flat" size="small" class="text-none font-weight-bold rounded-lg mb-2" @click="triggerProductAction('Adding New')">
            <v-icon start icon="mdi-plus"></v-icon> Add Product
          </v-btn>
        </div>

        <v-row v-if="catalogue.length > 0">
          <v-col cols="12" sm="6" md="4" v-for="product in catalogue" :key="product.id">
            <v-card class="rounded-xl elevation-2 border-opacity-50 h-100 d-flex flex-column bg-surface" border hover>
              <v-img :src="product.image" height="150" cover></v-img>
              <div class="pa-4 d-flex flex-column flex-grow-1">
                <div class="text-subtitle-1 font-weight-bold text-high-emphasis mb-1">{{ product.name }}</div>
                <div class="text-body-1 font-weight-black text-primary mb-4">{{ product.price }}</div>
                
                <div class="mt-auto d-flex border-t border-opacity-20 pt-3">
                  <v-btn flex-grow-1 color="primary" variant="tonal" size="small" class="text-none font-weight-bold rounded-lg mr-2" @click="triggerProductAction('Editing', product.id)">
                    <v-icon start icon="mdi-pencil" size="16"></v-icon> Edit
                  </v-btn>
                  <v-btn icon="mdi-trash-can-outline" variant="tonal" color="error" size="small" class="rounded-lg shrink-0" @click="triggerProductAction('Deleting', product.id)"></v-btn>
                </div>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <v-card v-else class="rounded-xl border border-dashed border-success bg-surface-variant bg-opacity-30 pa-8 text-center" elevation="0">
          <v-avatar color="primary" variant="tonal" size="64" class="mb-4">
            <v-icon icon="mdi-storefront-outline" size="32"></v-icon>
          </v-avatar>
          <h3 class="text-h5 font-weight-bold text-high-emphasis mb-2">Open your campus store</h3>
          <p class="text-body-1 text-medium-emphasis mb-6 mx-auto" style="max-width: 400px;">
            Got old textbooks, electronics, or dorm gear? List your first item and start making money today.
          </p>
          <v-btn color="primary" variant="flat" size="large" class="text-none font-weight-bold rounded-lg px-8" @click="triggerProductAction('Adding New')">
            Add Your First Product
          </v-btn>
        </v-card>
      </div>

      <div class="mb-10">
        <div class="d-flex justify-space-between align-end mb-4 px-2">
          <div>
            <h2 class="text-h5 font-weight-black text-high-emphasis">Customer Reviews</h2>
            <p class="text-body-2 text-medium-emphasis mt-1">See what the campus community is saying.</p>
          </div>
        </div>

        <v-row v-if="reviews.length > 0">
          <v-col cols="12" sm="4">
            <div style="position: sticky; ">
            <v-card class="rounded-xl elevation-2 border-opacity-50 bg-surface h-100 d-flex flex-column pa-6" border>
  
              <div class="text-center mb-5 mt-2">
                <h1 class="text-h2 font-weight-black text-high-emphasis leading-none">{{ reviewsData.averageRating }}</h1>
                <v-rating 
                  :model-value="reviewsData.averageRating" 
                  color="amber-darken-2" 
                  density="compact" 
                  half-increments 
                  readonly 
                  class="mt-2 mb-1"
                ></v-rating>
                <div class="text-caption font-weight-bold text-medium-emphasis">
                  Based on {{ reviewsData.totalReviews }} reviews
                </div>
              </div>

              <v-divider class="mb-5 opacity-20"></v-divider>

              <div class="d-flex flex-column gap-2 flex-grow-1 justify-center">
                <div v-for="item in reviewsData.distribution" :key="item.stars" class="d-flex align-center">
                  
                  <span class="text-caption font-weight-bold text-high-emphasis" style="width: 10px;">
                    {{ item.stars }}
                  </span>
                  <v-icon icon="mdi-star" color="amber-darken-2" size="14" class="mx-2"></v-icon>
                  
                  <v-progress-linear
                    :model-value="item.percentage"
                    color="amber-darken-2"
                    height="8"
                    rounded
                    bg-color="surface-variant"
                    class="flex-grow-1 opacity-90"
                  ></v-progress-linear>
                  
                  <span class="text-caption text-medium-emphasis text-right ml-3" style="width: 32px;">
                    {{ item.percentage }}%
                  </span>
                </div>
              </div>
            </v-card>
            </div>
          </v-col>

          <v-col cols="12" sm="8">
            <v-card v-for="review in reviews" :key="review.id" class="rounded-xl elevation-0 border-opacity-50 bg-surface pa-5 mb-4" border>
              <div class="d-flex justify-space-between align-start mb-2">
                <div class="d-flex align-center gap-3">
                  <v-avatar size="40" class="rounded-circle">
                    <v-img :src="review.avatar" cover></v-img>
                  </v-avatar>
                  <div>
                    <div class="text-subtitle-2 font-weight-bold text-high-emphasis">{{ review.reviewerName }}</div>
                    <v-rating :model-value="review.rating" color="amber-darken-2" density="compact" size="x-small" readonly></v-rating>
                  </div>
                </div>
                <div class="text-caption text-medium-emphasis">{{ review.date }}</div>
              </div>
              <p class="text-body-2 text-high-emphasis mt-2 mb-0">
                "{{ review.text }}"
              </p>
            </v-card>
          </v-col>
        </v-row>
      </div>

    </v-container>

    <v-snackbar v-model="showSnackbar" :timeout="2000" color="surface-variant" rounded="pill">
      <span class="font-weight-bold text-high-emphasis">{{ snackbarText }}</span>
    </v-snackbar>

  </div>
</template>