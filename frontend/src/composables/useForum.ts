import { ref, computed, watch, onMounted } from 'vue';
import { forumService } from '../services/forumService';
import { getUserProfile } from '../services/authService';
import { getPostReplies, createPostReply } from '@/services/authService';

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

  const fetchCurrentUser = async () => {
    try {
      const response = await getUserProfile();
      currentUser.value = response.data; 
    } catch (error) {
      console.error('Failed to fetch user profile:', error);
    }
  };

  // ==========================================
  // CREATE TOPICS
  // ==========================================
  const showNewPostDialog = ref(false);
  const isSubmitting = ref(false);
  const formRef = ref();
  const newTopic = ref({ title: '', category: '', excerpt: '', isAnonymous: false });
  
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
      const payload = {
        title: newTopic.value.title,
        category: newTopic.value.category,
        excerpt: newTopic.value.excerpt,
        is_anonymous: newTopic.value.isAnonymous
      };

      await forumService.createThread(payload);
      await fetchThreads(); 
      
      showNewPostDialog.value = false;
      newTopic.value = { title: '', category: '', excerpt: '', isAnonymous: false };
      formRef.value.reset();
    } catch (error) {
      console.error('Error creating topic:', error);
    } finally {
      isSubmitting.value = false;
    }
  };

  // ==========================================
  // REPLIES LOGIC (Updated for Accordion)
  // ==========================================

  // 1. Fetch existing replies for a specific thread
  const loadReplies = async (postId: number) => {
    try {
      const res = await getPostReplies(postId);
      const fetchedReplies = res.data?.data || res.data;
      
      // Find the thread in our local array and attach the replies to it
      const thread = threads.value.find(t => t.id === postId);
      if (thread) {
        thread.replies = fetchedReplies;
      }
    } catch (err) {
      console.error("Failed to load replies:", err);
    }
  };

  // 2. Submit a new reply to a specific thread
  const submitReply = async (postId: number, content: string) => {
    try {
      const res = await createPostReply(postId, { content });
      const savedReply = res.data?.data || res.data;
      
      // Instantly push it into the local thread so the UI updates without refreshing
      const thread = threads.value.find(t => t.id === postId);
      if (thread) {
        if (!thread.replies) thread.replies = [];
        thread.replies.push(savedReply);
        
        // Increase the comment count visually
        thread.comments = (thread.comments || 0) + 1;
      }
      return savedReply;
    } catch (err) {
      console.error("Reply submission failed:", err);
      throw err; // Throw it so the component can stop its loading spinner
    }
  };


  // ==========================================
  // UPDATE & DELETE
  // ==========================================
  const upvoteThread = async (id: number) => {
    const thread = threads.value.find(t => t.id === id);
    if (thread) thread.upvotes++; 

    try {
      await forumService.upvoteThread(id);
    } catch (error) {
      console.error('Error upvoting:', error);
      if (thread) thread.upvotes--; 
    }
  };

  const deleteThread = async (id: number) => {
    if (!confirm('Are you sure you want to delete this discussion?')) return;

    try {
      await forumService.deleteThread(id);
      threads.value = threads.value.filter(t => t.id !== id); 
    } catch (error) {
      console.error('Error deleting topic:', error);
    }
  };

  // --- Initialization ---
  onMounted(() => {
    fetchCurrentUser();
    fetchThreads();
    fetchTrending();
  });

  // Expose everything the Vue template needs
  return {
    currentUser,
    threads, trendingTags, isLoading, searchQuery, selectedCategory,
    categories, postableCategories, showNewPostDialog, isSubmitting,
    formRef, newTopic, rules, submitTopic, upvoteThread, deleteThread, 
    submitReply, loadReplies 
  };
}