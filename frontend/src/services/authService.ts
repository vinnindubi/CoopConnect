import { apiClient } from "./myapi";

export const register= (formData: object) => {
    return apiClient.post('/register', formData);
};

export const loginUser = (formData: object) => {
  return apiClient.post('/login', formData);
};

export const getUserProfile = () => {
  return apiClient.get('/user');
};

export const logoutUser = () => {
  return apiClient.post('/logout');
};
export const  updateUser = (profileData: object)=>{
  return apiClient.post('/userEdit',profileData);
}
export const getAllUsers = ()=>{
  return apiClient.get('/allUsers');
}
// src/services/authService.ts

export const getPendingVerifications = () => {
  return apiClient.get('/admin/pending-sellers');
}
// Add these exports to your service file
export const getMyArticles = () => {
  return apiClient.get('/user/articles');
};

export const getMyProducts = () => {
  return apiClient.get('/user/products');
};

export const deleteMyArticle = (id: number) => {
  return apiClient.delete(`/user/articles/${id}`);
};

export const deleteMyProduct = (id: number) => {
  return apiClient.delete(`/user/products/${id}`);
};
// --- Articles ---
export const getArticle = (id: string | string[]) => {
  return apiClient.get(`/user/articles/${id}`);
};

export const updateArticle = (id: string | string[], data: object) => {
  // Using PUT or PATCH for updates is REST standard
  return apiClient.put(`/user/articles/${id}`, data); 
};

// --- Products (Marketplace) ---
export const getProduct = (id: string | string[]) => {
  return apiClient.get(`/user/products/${id}`);
};

export const updateProduct = (id: string | string[], data: object) => {
  return apiClient.put(`/user/products/${id}`, data);
};
// --- Create New Items ---
export const createArticle = (data: object) => {
  return apiClient.post('/user/articles', data);
};

export const createProduct = (data: object) => {
  return apiClient.post('/user/products', data);
};
export const toggleClubMembership = (groupId: number) => {
  return apiClient.post(`/groups/${groupId}/toggle-membership`);
};