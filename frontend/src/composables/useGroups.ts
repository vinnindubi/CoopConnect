import { ref } from 'vue';

// 1. THE FAKE DATABASE
// We use a single array to hold everything, just like the 'groups' table we discussed.
const groupsDb = ref([
  { 
    id: 4, 
    type: 'club', 
    category: 'Tech',
    name: 'Google Developer Student Club', 
    membersCount: 315, 
    meeting: 'Saturdays 10 AM', 
    image: 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&q=80&w=400', 
    description: 'Learn coding, build apps, and participate in global hackathons.',
    // Simulating the pivot table: Is the currently logged-in user a member?
    currentUserRole: 'admin' // 'admin', 'member', or null
  },
  { 
    id: 1, 
    type: 'club', 
    category: 'Media',
    name: 'Journalism Club', 
    membersCount: 120, 
    meeting: 'Fridays 4 PM', 
    image: 'https://images.unsplash.com/photo-1585829365295-ab7cd400c167?auto=format&fit=crop&q=80&w=400', 
    description: 'Run the campus newsletter, cover events, and learn photography and reporting.',
    currentUserRole: null
  },
  { 
    id: 101, 
    type: 'society', 
    category: 'Christian',
    name: 'Christian Union (CU)', 
    membersCount: 850, 
    meeting: 'Wednesdays 5 PM', 
    image: 'https://images.unsplash.com/photo-1543589077-47d81606c1bf?auto=format&fit=crop&q=80&w=400', 
    description: 'A fellowship of students dedicated to growing in faith, prayer, and community service.',
    currentUserRole: 'member'
  },
  { 
    id: 103, 
    type: 'society', 
    category: 'SDA',
    name: 'SDA Students Association', 
    membersCount: 430, 
    meeting: 'Saturdays 9 AM', 
    image: 'https://images.unsplash.com/photo-1504052434569-70ad5836ab65?auto=format&fit=crop&q=80&w=400', 
    description: 'Observing the Sabbath through worship, Bible study, and uplifting music ministries.',
    currentUserRole: null
  }
]);

// 2. THE FAKE API FUNCTIONS
export function useGroups() {
  
  // Fetch all groups (Simulates a GET request)
  const fetchAllGroups = async () => {
    // We use a Promise with setTimeout to fake the 500ms it takes a server to respond
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve(groupsDb.value);
      }, 500);
    });
  };

  // Fetch a single group by its ID
  const fetchGroupById = async (id: number) => {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        const group = groupsDb.value.find(g => g.id === id);
        if (group) {
          resolve(group);
        } else {
          reject('Group not found');
        }
      }, 300);
    });
  };

  // Toggle membership (Simulates a POST request to join/leave)
  const toggleMembership = async (id: number) => {
    return new Promise((resolve) => {
      setTimeout(() => {
        const group = groupsDb.value.find(g => g.id === id);
        if (group) {
          if (group.currentUserRole) {
            group.currentUserRole = null; // Leave group
            group.membersCount--;
          } else {
            group.currentUserRole = 'member'; // Join group
            group.membersCount++;
          }
          resolve(group);
        }
      }, 400);
    });
  };

  return {
    fetchAllGroups,
    fetchGroupById,
    toggleMembership
  };
}
