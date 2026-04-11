// src/services/groupService.ts
import { apiClient } from "./myapi";

export const getAllGroups = () => {
  return apiClient.get('/groups');
};

export const getGroupById = (id: number) => {
  return apiClient.get(`/groups/${id}`);
};

export const toggleGroupMembership = (id: number) => {
  return apiClient.post(`/groups/${id}/toggle-membership`);
};
export const createGroupPost = (groupId: number, content: string) => {
  return apiClient.post(`/groups/${groupId}/posts`, { content });
};
export const getGroupMembers = (groupId: number) => {
  return apiClient.get(`/groups/${groupId}/members`);
};

export const updateMemberRole = (groupId: number, userId: number, data: object) => {
  return apiClient.put(`/groups/${groupId}/members/${userId}`, data);
};

export const removeGroupMember = (groupId: number, userId: number) => {
  return apiClient.delete(`/groups/${groupId}/members/${userId}`);
};

export const updateGroupDetails = (groupId: number, data: object) => {
  return apiClient.put(`/groups/${groupId}`, data);
};

export const deleteGroupPost = (groupIapid: number, postId: number) => {
  return apiClient.delete(`/groups/${groupId}/posts/${postId}`);
};
// Back to a standard, clean JSON POST request
export const createGroupAchievement = (groupId: number, data: any) => {
  return apiClient.post(`/groups/${groupId}/achievements`, data);
};

export const updateGroupAchievement = (groupId: number, achievementId: number, data: any) => {
  return apiClient.put(`/groups/${groupId}/achievements/${achievementId}`, data);
};
// --- MILESTONES (Events & Achievements) ---

export const createGroupEvent = (groupId: number, data: any) => {
  return apiClient.post(`/groups/${groupId}/events`, data);
};

export const deleteGroupEvent = (groupId: number, eventId: number) => {
  return apiClient.delete(`/groups/${groupId}/events/${eventId}`);
};
export const deleteGroupAchievement = (groupId: number, achievementId: number) => {
  return apiClient.delete(`/groups/${groupId}/achievements/${achievementId}`);
};