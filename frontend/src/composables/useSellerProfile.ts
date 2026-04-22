import { ref } from 'vue';
import marketplaceService from '@/services/marketplaceService';

export function useSellerProfile() {
  const isLoading = ref(true);
  const errorMessage = ref('');

  // 1. DATA STATE
  const profile = ref({
    id: null,
    fullname: '',
    email: '',
    phone: '',
    admission: '',
    course: '',
    year: '',
    bio: '',
    avatar: '',
    coverPhoto: '',
    verificationStatus: '',
    store_categories: [] as string[],
    joined: ''
  });

  const products = ref<any[]>([]);
  const adverts = ref<any[]>([]); 

  // 2. FETCH ACTION
  const fetchSellerData = async (sellerId: string | number) => {
    isLoading.value = true;
    errorMessage.value = '';

    try {
      // One request to rule them all!
      const response = await marketplaceService.show(sellerId);
      
      const data = response.data;
      console.log(data);
      const publicData = data.profile || {};

      // Map the seller profile details
      profile.value.id = publicData.id;
      profile.value.fullname = publicData.fullname;
      profile.value.email = publicData.email;
      profile.value.phone = publicData.phone;
      profile.value.admission = publicData.admission;
      profile.value.course = publicData.course;
      profile.value.year = publicData.year;
      profile.value.bio = publicData.bio;
      profile.value.avatar = publicData.avatar;
      profile.value.coverPhoto = publicData.cover_photo;
      profile.value.joined = publicData.joined;
      profile.value.verificationStatus = publicData.isVerified ? 'approved' : 'pending';
      
      // Handle the categories array safety
      let rawCategories = publicData.store_categories || [];
      if (typeof rawCategories === 'string') {
        try { rawCategories = JSON.parse(rawCategories); } catch (e) { rawCategories = []; }
      }
      profile.value.store_categories = rawCategories;

      // Map the marketplace content
      products.value = data.products || [];
      adverts.value = data.adverts || [];

    } catch (error) {
      console.error('Error fetching seller data:', error);
      errorMessage.value = 'Failed to load the seller profile. They may have been removed.';
    } finally {
      isLoading.value = false;
    }
  };

  return {
    isLoading,
    errorMessage,
    profile,
    products,
    adverts,
    fetchSellerData
  };
}