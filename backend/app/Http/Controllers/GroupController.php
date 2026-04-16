<?php

namespace App\Http\Controllers;

use App\Models\GroupPost;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\User;
use Str;
use Illuminate\Validation\Rule;
class GroupController extends Controller
{
public function index(){
    $user = auth('api')->user();

        $groups = Group::where('is_approved', true)
            ->withCount('members') // Magically counts rows in the pivot table
            ->get()
            ->map(function ($group) use ($user) {
                
                // Check if the current user is a member/admin
                $role = null;
                if ($user) {
                    $membership = $group->members()->where('user_id', $user->id)->first();
                    $role = $membership ? $membership->pivot->role : null;
                }

                return [
                    'id' => $group->id,
                    'type' => $group->type,
                    'category' => $group->category,
                    'name' => $group->name,
                    'membersCount' => $group->members_count,
                    'meeting' => $group->meeting_time,
                    'image' => $group->cover_image,
                    'description' => $group->description,
                    'currentUserRole' => $role,
                ];
            });

        return response()->json($groups);
}   
public function show($id){
    $user = auth('api')->user();

        // Eager load everything we need for the profile page
        $group = Group::with(['events', 'achievements', 'posts.author'])
            ->withCount('members')
            ->findOrFail($id);

        // Determine user's role
        $role = null;
        if ($user) {
            $membership = $group->members()->where('user_id', $user->id)->first();
            $role = $membership ? $membership->pivot->role : null;
        }

        // Format the response for the Vue Profile Dashboard
        return response()->json([
            'id' => $group->id,
            'type' => $group->type,
            'category' => $group->category,
            'name' => $group->name,
            'mission' => $group->mission,
            'description' => $group->description,
            'membersCount' => $group->members_count,
            'meeting' => $group->meeting_time,
            'meetingVenue' => $group->meeting_venue,
            'contactEmail' => $group->contact_email,
            'image' => $group->cover_image,
            'currentUserRole' => $role,
            
            // Map the related data
            'leaders' => $group->leaders()->get()->map(function ($leader) {
                return [
                    'id' => $leader->id,
                    'name' => $leader->fullname ?? 'Unknown User',
                    'role' => $leader->pivot->title,
                    // In a real app, you'd have an avatar column on the User model
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($leader->name)
                ];
            }),
            'posts' => $group->posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'author' => $post->author->fullname,
                    'role' => 'Admin', // Simplified for now
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($post->author->name),
                    'content' => $post->content,
                    'timeAgo' => $post->created_at->diffForHumans(),
                    'likes' => $post->likes_count,
                    'isLiked' => false,
                ];
            }),
            'events' => $group->events,
            'achievements' => $group->achievements
        ]);

} 
public function store(Request $request){
// 1. Validate the incoming Postman data
        $validated = $request->validate([
            'type' => 'required|in:club,society',
            'category' => 'required|string',
            'name' => 'required|string|unique:groups',
            'description' => 'required|string',
            'meeting_time' => 'required|string',
            'patron_name' => 'required|string',
        ]);

        // 2. Add the background fields automatically
        $validated['slug'] = Str::slug($validated['name']); 
        $validated['proposal_document_path'] = 'dummy/path.pdf'; // Bypassing file upload for Postman test
        $validated['is_approved'] = true; // Auto-approve so we can see it immediately

        // 3. Save to the `groups` table
        $group = Group::create($validated);

        // 4. RELATIONSHIP TEST 1: The Pivot Table
        // Automatically make the logged-in user the Admin of this new club
        $group->members()->attach($request->user()->id, [
            'role' => 'admin',
            'title' => 'Chairperson'
        ]);

        return response()->json([
            'message' => 'Group created successfully!', 
            'group' => $group
        ], 201);
}
/**
     * Add a post to a specific group.
     */
    public function storePost(Request $request, $id)
    {
        $validated = $request->validate(['content' => 'required|string']);
        
        $group = Group::findOrFail($id);
        $user = $request->user();

        // 5. RELATIONSHIP TEST 2: The HasMany Relationship
        // This automatically fills in the `group_id` for us!
        $post = $group->posts()->create([
            'user_id' => $user->id, // We manually provide the author's ID
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Post published successfully!', 
            'post' => $post
        ], 201);
    }
public function toggleMembership(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        
        // This will automatically grab the user via Passport's Bearer token
        $user = $request->user(); 

        $isMember = $group->members()->where('user_id', $user->id)->exists();

        if ($isMember) {
            $group->members()->detach($user->id);
            $message = 'You have left the group.';
            $newRole = null;
        } else {
            $group->members()->attach($user->id, ['role' => 'member']);
            $message = 'You have successfully joined the group.';
            $newRole = 'member';
        }

        return response()->json([
            'message' => $message,
            'currentUserRole' => $newRole,
            'newMembersCount' => $group->members()->count()
        ]);
    }


// public function getPosts($id){
//     $group = Group::findOrFail($id);
//     $allPosts = $group->posts()->with('author')->latest()->get();
//     return response()->json([
//         'message' => 'got all posts for the specific group',
//         'data'=> $allPosts
//     ]);
// } it might be repetitive , i have built this in the show function.
/**
     * Helper function to check if the logged-in user is an Admin of the group.
     */
    private function isAdmin(Group $group, $user)
    {
        $membership = $group->members()->where('user_id', $user->id)->first();
        return $membership && $membership->pivot->role === 'admin';
    }

    /**
     * Fetch all members (both admins and regular members) for the dashboard.
     */
    public function getMembers($id)
    {
        $group = Group::findOrFail($id);

        $members = $group->members()->get()->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->name ?? 'Unknown User',
                'email' => $member->email,
                'role' => $member->pivot->role,
                'title' => $member->pivot->title,
                'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($member->name ?? 'User')
            ];
        });

        return response()->json($members);
    }

    /**
     * Update club settings (Mission, Email, Meeting Time).
     */
    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        // Security check
        if (!$this->isAdmin($group, $request->user())) {
            return response()->json(['message' => 'Unauthorized. Admins only.'], 403);
        }

        $validated = $request->validate([
            'meeting_time' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'mission' => 'nullable|string',
        ]);

        $group->update($validated);

        return response()->json(['message' => 'Group updated successfully!', 'group' => $group]);
    }

    /**
     * Promote or demote a user (Update Pivot Table).
     */
    public function updateMemberRole(Request $request, $id, $userId)
    {
        $group = Group::findOrFail($id);

        if (!$this->isAdmin($group, $request->user())) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'role' => 'required|in:admin,member',
            'title' => 'nullable|string'
        ]);

        // Laravel magic to update extra columns on a specific pivot relationship!
        $group->members()->updateExistingPivot($userId, [
            'role' => $validated['role'],
            'title' => $validated['title']
        ]);

        return response()->json(['message' => 'Member role updated.']);
    }

    /**
     * Kick a user from the club (Detach from Pivot Table).
     */
    public function removeMember(Request $request, $id, $userId)
    {
        $group = Group::findOrFail($id);

        if (!$this->isAdmin($group, $request->user())) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Prevent the admin from accidentally kicking themselves
        if ($request->user()->id == $userId) {
            return response()->json(['message' => 'You cannot kick yourself.'], 400);
        }

        // Remove the relationship
        $group->members()->detach($userId);

        return response()->json(['message' => 'Member removed successfully.']);
    }

    /**
     * Delete an announcement.
     */
    public function destroyPost(Request $request, $id, $postId)
    {
        $group = Group::findOrFail($id);

        if (!$this->isAdmin($group, $request->user())) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Find the specific post and ensure it actually belongs to this group
        $post = $group->posts()->where('id', $postId)->firstOrFail();
        $post->delete();

        return response()->json(['message' => 'Post deleted successfully.']);
    }
    /**
     * Track Record: Create a new Achievement (Simple Image Path version)
     */
    public function storeAchievement(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        if (!$this->isAdmin($group, $request->user())) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'date_achieved' => 'required|string',
            'description' => 'required|string',
            'image_path' => 'nullable|string', // Just accept a regular string URL!
        ]);

        $achievement = $group->achievements()->create($validated);
        return response()->json(['message' => 'Achievement added successfully!', 'achievement' => $achievement]);
    }

    /**
     * Update an existing Achievement
     */
    public function updateAchievement(Request $request, $id, $achievementId)
    {
        $group = Group::findOrFail($id);
        if (!$this->isAdmin($group, $request->user())) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $achievement = $group->achievements()->findOrFail($achievementId);

        $validated = $request->validate([
            'title' => 'required|string',
            'date_achieved' => 'required|string',
            'description' => 'required|string',
            'image_path' => 'nullable|string', // Same here
        ]);

        $achievement->update($validated);
        return response()->json(['message' => 'Achievement updated!', 'achievement' => $achievement]);
    }
   /**
     * Future Plans: Create a new Event
     */
    public function storeEvent(Request $request, $id)
    {
        $group = Group::findOrFail($id);
        
        // Security check to ensure only admins can add events
        if (!$this->isAdmin($group, $request->user())) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Your standardized categories
        $allowedCategories = [
            'Academic', 'Social', 'Sports', 'Career'
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'nullable|string',
            'event_type' => ['required', 'string', Rule::in($allowedCategories)],
            
            // --- NEW FIELDS ADDED HERE ---
            'location' => 'nullable|string|max:255',
            // Ensure price is a number and not negative
            'price' => 'nullable|numeric|min:0', 
            // Accepts a string (URL or path)
            'image' => 'nullable|string', 
        ]);

        // Create the event linked directly to this group
        $event = $group->events()->create($validated);
        
        // Return the new event so Vue can instantly push it to the UI list
        return response()->json([
            'message' => 'Plan added successfully!', 
            'event' => $event
        ], 201);
    }

    /**
     * Future Plans: Update an existing Event
     */
    public function updateEvent(Request $request, $id, $eventId)
    {
        $group = Group::findOrFail($id);
        
        // Security check: Only admins can edit events
        if (!$this->isAdmin($group, $request->user())) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Keep validation identical to storeEvent to prevent bad data
        $allowedCategories = [
            'Academic', 'Social', 'Sports', 'Career'
        ];

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_date' => 'required|date',
            'description' => 'required|string',
            'event_type' => ['required', 'string', Rule::in($allowedCategories)],
            
            // --- NEW FIELDS ADDED HERE ---
            'location' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|string',
        ]);

        // Find the specific event and update it securely
        $event = $group->events()->where('id', $eventId)->firstOrFail();
        $event->update($validated);
        
        return response()->json([
            'message' => 'Plan updated successfully!', 
            'event' => $event
        ]);
    }
}

