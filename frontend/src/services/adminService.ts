import { apiClient } from "./myapi";


// --- Admin Event Management ---

export const createGlobalEvent = (eventData: any) => {
  return apiClient.post('/admin/events', eventData);
};
export const getAllEvents = () => {
  // Reusing the same endpoint the student calendar uses!
  return apiClient.get('/events'); 
};

export const deleteAdminEvent = (eventId: number) => {
  return apiClient.delete(`/admin/events/${eventId}`);
};
// Admin can promote an event to be featured.
export const toggleEventFeature = (eventId: number) => {
  return apiClient.patch(`/admin/events/${eventId}/feature`);
};

// --- Announcements API ---
export const createGlobalAnnouncement = (data: any) => {
  return apiClient.post('/admin/announcements', data);
};

export const fetchActiveAnnouncements = () => {
  return apiClient.get('/announcements');
};

export const deactivateAnnouncement = (id: number) => {
  return apiClient.patch(`/admin/announcements/${id}/deactivate`);
};
// You can add other admin calls here later (e.g., verifying sellers, approving groups)