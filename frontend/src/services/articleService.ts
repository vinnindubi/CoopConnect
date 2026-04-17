import { apiClient } from "./myapi";
export default {
  /**
   * Create a new article
   */
  createArticle(articleData: any) {
    // We point this to the Admin route we created earlier. 
    // If students are allowed to post, ensure you adjust the route prefix accordingly in Laravel!
    return apiClient.post('/articles/store', articleData);
  },

  /**
   * Fetch all articles (for the feed)
   */
  getArticles(category = 'All', search = '') {
    return apiClient.get('/student/articles', {
      params: { category, search }
    });
  },

  /**
   * Fetch a single article by ID
   */
  getArticle(id: number | string) {
    return apiClient.get(`/student/articles/${id}`);
  }
};