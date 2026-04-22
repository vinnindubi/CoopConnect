import { apiClient } from "./myapi";

// Fetch active announcements for the public/student side
export const getActiveAnnouncements = () => {
  return apiClient.get('/announcements');
};
// Fetch the "Happening Now" feed
export const getStudentFeed = () => {
  return apiClient.get('/student/feed');
};
export const getStudentUpcoming = () => {
  return apiClient.get('/home/upcoming');
};

// Fetch upcoming events and club activities
export const getUpcomingEvents = () => {
  return apiClient.get('/student/events/upcoming');
};

// Fetch a preview of the latest marketplace items
export const getMarketplacePreview = () => {
  return apiClient.get('/student/marketplace/preview');
};
