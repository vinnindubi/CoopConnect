import { apiClient } from "./myapi";

export default {
  /**
   * Create a new article
   * Matches Route::post('/articles/store')
   */
  createArticle(articleData: any) {
    return apiClient.post('/articles/store', articleData);
  },

  /**
   * Fetch all articles (for the feed)
   * Matches Route::get('/articles')
   */
  getArticles(category = 'All', search = '') {
    return apiClient.get('/articles', {
      params: { category, search }
    });
  },

  /**
   * Fetch a single article by ID
   * Matches Route::get('showArticle/{id}')
   */
  getArticle(id: number | string) {
    return apiClient.get(`/showArticle/${id}`);
  },

  /**
   * Fetch all articles for a specific user profile
   * Matches Route::get('/users/{id}/articles')
   */
  getUserArticles(userId: number | string) {
    return apiClient.get(`/users/${userId}`);
  }
};