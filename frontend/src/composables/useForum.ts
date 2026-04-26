import { ref, computed, watch, onMounted } from 'vue';
import { forumService } from '../services/forumService';
import { getUserProfile } from '../services/authService';

export function useForum() {
  // --- Global State ---
  const currentUser = ref<any>(null);
  const threads = ref<any[]>([]);
  const trendingTags = ref<any[]>([]);
  const isLoading = ref(true);
  

  // --- Filters ---
  const searchQuery = ref('');
  const selectedCategory = ref('All');
  const categories = ['All', 'Campus Life', 'Academics', 'Housing', 'Attachment & Careers', 'Marketplace', 'Rants'];

  // ==========================================
  // READ (Fetch Data via Service)
  // ==========================================
  const fetchThreads = async () => {
    isLoading.value = true;
    try {
      threads.value = await forumService.getThreads(selectedCategory.value, searchQuery.value);
    } catch (error) {
      console.error('Error fetching threads:', error);
    } finally {
      isLoading.value = false;
    }
  };

  const fetchTrending = async () => {
    try {
      trendingTags.value = await forumService.getTrending();
    } catch (error) {
      console.error('Error fetching trending tags:', error);
    }
  };

  // Reactively fetch new data when filters change (with a 300ms debounce)
  let debounceTimer: any;
  watch([searchQuery, selectedCategory], () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => fetchThreads(), 300); 
  });

  // ==========================================
  // CREATE (Submit via Service)
  // ==========================================
  const showNewPostDialog = ref(false);
  const isSubmitting = ref(false);
  const formRef = ref();
  const newTopic = ref({ title: '', category: '', excerpt: '',isAnonymous: false });
  
  const postableCategories = computed(() => categories.filter(c => c !== 'All'));

  const rules = {
    required: (v: string) => !!v || 'This field is required',
    minLength: (v: string) => (v && v.length >= 10) || 'Must be at least 10 characters'
  };

  const submitTopic = async () => {
    const { valid } = await formRef.value.validate();
    if (!valid) return;

    isSubmitting.value = true;
    try {
      // Map it to snake_case for the Laravel backend
      const payload = {
        title: newTopic.value.title,
        category: newTopic.value.category,
        excerpt: newTopic.value.excerpt,
        is_anonymous: newTopic.value.isAnonymous
      };

      await forumService.createThread(payload);
      await fetchThreads(); 
      
      showNewPostDialog.value = false;
      // Reset everything, including the toggle!
      newTopic.value = { title: '', category: '', excerpt: '', isAnonymous: false };
      formRef.value.reset();
    } catch (error) {
      console.error('Error creating topic:', error);
    } finally {
      isSubmitting.value = false;
    }
  };


  // ==========================================
  // UPDATE & DELETE (via Service)
  // ==========================================
  const upvoteThread = async (id: number) => {
    const thread = threads.value.find(t => t.id === id);
    if (thread) thread.upvotes++; // Optimistic UI update for instant feedback

    try {
      await forumService.upvoteThread(id);
    } catch (error) {
      console.error('Error upvoting:', error);
      if (thread) thread.upvotes--; // Revert if the API call fails
    }
  };

  const deleteThread = async (id: number) => {
    if (!confirm('Are you sure you want to delete this discussion?')) return;

    try {
      await forumService.deleteThread(id);
      threads.value = threads.value.filter(t => t.id !== id); // Instantly remove from UI
    } catch (error) {
      console.error('Error deleting topic:', error);
    }
  };
  const fetchCurrentUser = async () => {
    try {
      const response = await getUserProfile();
      // Since you use apiClient (Axios), the user data is inside response.data
      currentUser.value = response.data; 
    } catch (error) {
      console.error('Failed to fetch user profile:', error);
      // Optional: Handle redirect to login if they aren't authenticated
    }
  };

  // --- Initialization ---
  onMounted(() => {
    fetchCurrentUser();
    fetchThreads();
    fetchTrending();
  });

  // Expose everything the Vue template needs to render and interact
  return {
    currentUser,
    threads, trendingTags, isLoading, searchQuery, selectedCategory,
    categories, postableCategories, showNewPostDialog, isSubmitting,
    formRef, newTopic, rules, submitTopic, upvoteThread, deleteThread
  };
}