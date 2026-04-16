import { 
  getAllGroups, 
  getGroupById, 
  toggleGroupMembership, 
  createGroupPost, 
  deleteGroupPost,
  getGroupMembers, 
  updateMemberRole, 
  removeGroupMember, 
  updateGroupDetails,
  createGroupEvent,
  updateGroupEvent,
  deleteGroupEvent,
  createGroupAchievement,
  deleteGroupAchievement
} from '@/services/groupService';

export function useGroups() {
  
  const fetchAllGroups = async () => {
    const response = await getAllGroups();
    return response.data;
  };

  const fetchGroupById = async (id: number) => {
    const response = await getGroupById(id);
    return response.data;
  };

  const toggleMembership = async (id: number) => {
    const response = await toggleGroupMembership(id);
    return response.data;
  };

  // --- POSTS ---
  const createPost = async (groupId: number, content: string) => {
    const response = await createGroupPost(groupId, content);
    const newPost = response.data.post;
    return {
      post: {
        id: newPost.id,
        content: newPost.content,
        author: 'You', 
        role: 'Admin',
        avatar: 'https://ui-avatars.com/api/?name=You',
        timeAgo: 'Just now',
        likes: 0,
        isLiked: false
      }
    };
  };

  const deletePost = async (groupId: number, postId: number) => {
    await deleteGroupPost(groupId, postId);
  };

  // --- ADMIN & MEMBERS ---
  const fetchMembers = async (groupId: number) => {
    const response = await getGroupMembers(groupId);
    return response.data;
  };

  const changeMemberRole = async (groupId: number, userId: number, role: string, title: string | null) => {
    const response = await updateMemberRole(groupId, userId, { role, title });
    return response.data;
  };

  const kickMember = async (groupId: number, userId: number) => {
    await removeGroupMember(groupId, userId);
  };

  const saveGroupSettings = async (groupId: number, settings: any) => {
    const response = await updateGroupDetails(groupId, settings);
    return response.data;
  };

  // --- MILESTONES (Events & Achievements) ---
  const addEvent = async (groupId: number, eventData: any) => {
    const response = await createGroupEvent(groupId, eventData);
    return response.data;
  };
const editEvent = async (groupId: number, eventId: number, eventData: any) => {
    const response = await updateGroupEvent(groupId, eventId, eventData);
    return response.data;
  };
  const removeEvent = async (groupId: number, eventId: number) => {
    await deleteGroupEvent(groupId, eventId);
  };

  const addAchievement = async (groupId: number, achievementData: any) => {
    const response = await createGroupAchievement(groupId, achievementData);
    return response.data;
  };

  const removeAchievement = async (groupId: number, achievementId: number) => {
    await deleteGroupAchievement(groupId, achievementId);
  };

  // The critical return block! Everything in here is exposed to your Vue components.
  return {
    fetchAllGroups,
    fetchGroupById,
    toggleMembership,
    createPost,
    deletePost,
    fetchMembers,
    changeMemberRole,
    kickMember,
    saveGroupSettings,
    addEvent,   
    editEvent,      
    removeEvent,       
    addAchievement,    
    removeAchievement  
  };
}