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
