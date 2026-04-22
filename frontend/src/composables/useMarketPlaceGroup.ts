import { ref, computed } from 'vue';
// Adjust the path to wherever you saved marketplaceService.ts
import marketplaceService from '@/services/marketplaceService'; 

export function useMarketPlaceGroup() {
  // ==========================================
  // 1. UI STATE
  // ==========================================
  const activeTab = ref('marketplace'); 
  const searchQuery = ref('');
  const isLoading = ref(true);
  const errorMessage = ref(''); 

  // ==========================================
  // 2. DATA STATE
  // ==========================================
  const products = ref<any[]>([]);
  const adverts = ref<any[]>([]);

  const selectedProductCat = ref('All');
  const productCategories = ['All', 'Electronics', 'Fashion', 'Academic', 'Services'];

  const selectedAdvertCat = ref('All');
  const advertCategories = ['All', 'Housing', 'Collaborations', 'Jobs', 'General'];

  // ==========================================
  // 3. API ACTIONS
  // ==========================================
  const fetchMarketplaceData = async () => {
    isLoading.value = true;
    errorMessage.value = '';

    try {
      // Because the service uses apiClient directly, we await the full response
      const response = await marketplaceService.getMarketplaceData();
      
      // Safely extract the products and adverts from response.data
      products.value = response.data?.products || [];
      adverts.value = response.data?.adverts || [];
      
    } catch (error) {
      console.error("Error fetching marketplace data:", error);
      errorMessage.value = "Failed to load the marketplace. Please try again later.";
      
      // Reset to empty arrays on failure so the UI doesn't crash
      products.value = [];
      adverts.value = [];
    } finally {
      isLoading.value = false;
    }
  };

  // ==========================================
  // 4. COMPUTED FILTERS
  // ==========================================
  const filteredProducts = computed(() => {
    return products.value.filter(p => {
      // Check if category matches
      const matchesCategory = selectedProductCat.value === 'All' || p.category === selectedProductCat.value;
      // Check if title includes search query (case-insensitive)
      const matchesSearch = p.title?.toLowerCase().includes(searchQuery.value.toLowerCase());
      
      return matchesCategory && matchesSearch;
    });
  });

  const filteredAdverts = computed(() => {
    return adverts.value.filter(a => {
      // Check if category matches
      const matchesCategory = selectedAdvertCat.value === 'All' || a.category === selectedAdvertCat.value;
      // Check if title includes search query (case-insensitive)
      const matchesSearch = a.title?.toLowerCase().includes(searchQuery.value.toLowerCase());
      
      return matchesCategory && matchesSearch;
    });
  });

  // ==========================================
  // 5. EXPOSE TO VUE TEMPLATE
  // ==========================================
  return {
    // State variables
    activeTab,
    searchQuery,
    isLoading,
    errorMessage,
    
    // Categories
    selectedProductCat,
    productCategories,
    selectedAdvertCat,
    advertCategories,
    
    // Filtered Data Arrays
    filteredProducts,
    filteredAdverts,
    
    // Actions
    fetchMarketplaceData
  };
}