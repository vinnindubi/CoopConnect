<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MarketplaceProduct;
use App\Models\Article;
use Hash;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function login( Request $request ){
        $validated = $request->validate([
        "email"=> 'required|email',
        "password" =>'required|string'
        ]);
        if (Auth::attempt(['email'=>$validated['email'],'password' => $validated['password']])) {
        $user= Auth::user();
        $token = $user->createToken('testing')->accessToken;
        return response()->json([
            'message'=>"Login Successful",
            "token" => $token
        ],200);
        }
        else{
            return response()->json([
            'message'=>"Login failed"
        ],401);
        }
    }
    public function register(Request $request) {
            $validated = $request->validate([
                "fullname"  => "required|string|max:255",
                "email"     => "required|email|unique:users,email",
                "password"  => "required|string|min:8",
                'admission' => [
                    'required',
                    'string',
                    'regex:/^[a-zA-Z0-9]+\/[a-zA-Z0-9]+\/\d{4}$/'
                ],
            ],
            [
            'admission.regex' => 'The admission number must be in the format COURSE/NUMBER/YEAR (e.g., BITC01/1065/2026).'
            ]);

            $user = User::create([
                'fullname'  => $validated['fullname'],
                'admission' => $validated['admission'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
            ]);

            // 3. Generate token
            $token = $user->createToken('testing')->accessToken;

            // 4. Return response
            return response()->json([
                "user"  => $user,
                "token" => $token
            ], 201); 
    }
 public function logout(Request $request){
    $request->user()->token()->revoke();

    return response()->json([
        'message' => 'Successfully logged out'
    ], 200);
 }
 public function getUser(){
    $response =  Auth::user();
    return response()->json([
    "message"=> "Got User successfully!!",
    "data" => $response
    ],200);
 }
public function updateUserData(Request $request) {
    $user = Auth::user();

    // 1. Map the keys EXACTLY to what Vue is sending
    $user->update([
        'fullname'         => $request->fullname,             // Vue sends 'name'
        'email'            => $request->email,
        'course'           => $request->course,
        'year'             =>$request ->year,
        'bio'              => $request->bio,
        'phone'            => $request->phone,
        'store_categories' => $request->store_categories,  // Vue sends 'storeCategories'
    ]);

    // 2. Fetch the fresh, newly updated user from the database
    $updatedUser = $user->fresh();

    // 3. Return the actual user object, not a boolean
    return response()->json([
        "message" => "User updated successfully!!",
        "data"    => $updatedUser
    ]);
}
public function getAllUsers(){
    $users = User::all();
    return response()->json([
        "message" => "users returned successfully",
        "data" => $users
    ]);
}
// app/Http/Controllers/AuthController.php

public function getPendingSellers() {
    $pendingUsers = User::where('verification_status', 'pending')->get();

    // Map through the users and format the data before sending it to Vue
    $formattedUsers = $pendingUsers->map(function ($user) {
        
        // 1. Grab the raw category data
        $rawCategories = $user->store_categories;
        $parsedCategories = [];

        // 2. Safely convert it to a real array based on how it's stored
        if (is_string($rawCategories)) {
            // Try to decode it if it's a JSON string (e.g., '["Clothing"]')
            $decoded = json_decode($rawCategories, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $parsedCategories = $decoded;
            } else {
                // If it's just comma-separated (e.g., 'Clothing,Tech'), split it
                $parsedCategories = array_map('trim', explode(',', $rawCategories));
            }
        } elseif (is_array($rawCategories)) {
            $parsedCategories = $rawCategories;
        }

        // 3. Return exactly what Vue needs
        return [
            'id' => $user->id,
            'fullname' => $user->fullname,
            'store_categories' => $parsedCategories, // Now guaranteed to be an array!
            'id_proof' => $user->id_proof,
            'updated_at' => $user->updated_at,
        ];
    });
    return response()->json([
        'message' => 'Pending sellers fetched successfully',
        'data' => $formattedUsers // Send the mapped data instead of the raw database output
    ], 200);
}
public function myArticles()
{
    // Assuming a user 'hasMany' posts
    $articles = auth()->user()->articles()->latest()->get();
    return response()->json(['data' => $articles]);
}

public function myProducts()
{
    // Assuming a user 'hasMany' marketplaceItems
    $products = auth()->user()->marketplaceProducts()->latest()->get();
    return response()->json(['data' => $products]);
}
public function destroyArticle(Article $post)
{
    // Extra security: Ensure the user actually owns this post!
    if ($post->user_id != auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    $post->delete();
    return response()->json(['message' => 'Deleted']);
}

public function destroyProduct(MarketplaceProduct $item)
{
    if ($item->user_id != auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    
    $item->delete();
    return response()->json(['message' => 'Deleted']);
}
// ==========================================
    // ARTICLES
    // ==========================================
    public function showArticle(Article $post)
    {
        // Security: Ensure the user owns this post
        if ($post->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json(['data' => $post]);
    }

    public function updateArticle(Request $request, Article $post)
    {
        if ($post->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the exact fields coming from Vue
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',       // <--- Added validation
            'image' => 'nullable|url',             // <--- Added validation (nullable in case they leave it blank)
            'excerpt' => 'required|string',
            'content' => 'required|string',
        ]);

        // This line does the direct edit!
        $post->update($validated);

        return response()->json(['message' => 'Article updated successfully', 'data' => $post]);
    }

    // ==========================================
    // PRODUCTS (MARKETPLACE)
    // ==========================================
    public function showProduct(MarketplaceProduct $item)
    {
        if ($item->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json(['data' => $item]);
    }

    public function updateProduct(Request $request, MarketplaceProduct $item)
    {
        if ($item->user_id != auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|url',
        ]);

        $item->update($validated);

        return response()->json(['message' => 'Product updated successfully', 'data' => $item]);
    }
    public function storeProduct(Request $request)
    {
        // 1. Validate the incoming data from your Vue form
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string',
            'condition' => 'nullable|string', // Captures the 'Brand New', 'Like New', etc.
            'description' => 'nullable|string',
            'image' => 'nullable|url', // Ensures the user actually pasted a valid link
        ]);

        // 2. Attach the currently logged-in user's ID to the data
        $validated['user_id'] = auth()->id();

        // 3. Create the product in the database
        // Note: Change 'MarketplaceProduct' if your model is named something else!
        $product = MarketplaceProduct::create($validated);

        // 4. Return a successful JSON response back to Vue
        return response()->json([
            'message' => 'Product added successfully',
            'data' => $product
        ], 201); // 201 is the standard HTTP status code for "Created"
    }
    public function storeFeedback(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'details' => 'required|string',
            'attachment_path' => 'nullable|url', // Validate that it is a proper link!
            'is_anonymous' => 'required|boolean',
            'allow_contact' => 'required|boolean',
        ]);

        $userId = $validated['is_anonymous'] ? null : auth()->id();

        // No file storage logic needed. Just dump the validated data straight into the DB!
        $feedback = \App\Models\Feedback::create([
            'user_id' => $userId,
            'category' => $validated['category'],
            'rating' => $validated['rating'],
            'details' => $validated['details'],
            'attachment_path' => $validated['attachment_path'], // Save the link directly
            'is_anonymous' => $validated['is_anonymous'],
            'allow_contact' => $validated['allow_contact'],
        ]);

        return response()->json([
            'message' => 'Feedback submitted successfully!',
            'data' => $feedback
        ], 201);
    }
 }
