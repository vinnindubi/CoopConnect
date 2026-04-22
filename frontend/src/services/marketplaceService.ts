import { apiClient } from "./myapi"; 

export default {
  /**
   * Fetch all marketplace products and adverts
   * Matches Route::get('/marketplace')
   */
  getMarketplaceData() {
    return apiClient.get('/marketplace'); 
  },
  
  /**
   * Fetch a specific seller's profile and products
   * Matches Route::get('/sellers/{id}')
   */
  show(id: number | string) {
    return apiClient.get(`/sellers/${id}`);
  }
};