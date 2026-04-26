// src/services/forumService.ts
import { apiClient } from "./myapi"; // Adjust the path to where your myapi file is located

export const forumService = {
  // READ: Get all threads (with optional filters)
  async getThreads(category?: string, search?: string) {
    const params = new URLSearchParams();
    if (category && category !== 'All') params.append('category', category);
    if (search) params.append('search', search);

    // apiClient handles the base URL (e.g., '/api') and headers!
    const response = await apiClient.get(`/forum?${params.toString()}`);
    
    // Axios puts the response body in '.data'. 
    // Laravel's API Resource also wraps it in a 'data' object.
    return response.data.data; 
  },

  // READ: Get trending topics
  async getTrending() {
    const response = await apiClient.get('/forum/trending');
    return response.data;
  },

  // CREATE: Post a new thread
  async createThread(payload: { title: string; category: string; excerpt: string }) {
    const response = await apiClient.post('/forum', payload);
    return response.data;
  },

  // UPDATE: Upvote a thread
  async upvoteThread(id: number) {
    const response = await apiClient.post(`/forum/${id}/upvote`);
    return response.data;
  },

  // DELETE: Remove a thread
  async deleteThread(id: number) {
    const response = await apiClient.delete(`/forum/${id}`);
    return response.data;
  }
};