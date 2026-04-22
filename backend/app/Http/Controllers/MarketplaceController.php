<?php

namespace App\Http\Controllers;

use App\Models\MarketplaceAdvert;
use App\Models\MarketplaceProduct;
use App\Models\User;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * Display a listing of the marketplace (Products and Adverts).
     */
    public function index()
    {
        $products = MarketplaceProduct::with('user')->latest()->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'price' => 'KES ' . number_format($product->price, 0),
                'category' => $product->category,
                'seller' => $product->user ? $product->user->fullname : 'Unknown',
                'seller_id' => $product->user_id,
                'isVerified' => $product->user && $product->user->verification_status === 'approved',
                'quantity' => (int) $product->quantity,
                'image' => $product->image,
                'time' => $product->created_at->diffForHumans(),
            ];
        });

        $adverts = MarketplaceAdvert::with('user')->latest()->get()->map(function ($advert) {
            return [
                'id' => $advert->id,
                'title' => $advert->title,
                'category' => $advert->category,
                'description' => $advert->description,
                'author' => $advert->user ? $advert->user->fullname : 'Unknown',
                'time' => $advert->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'products' => $products,
            'adverts' => $adverts
        ]);
    }

    /**
     * Display the specified seller and their products.
     */
    /**
     * Display the specified seller, their products, and adverts.
     */
    public function show($id)
    {
        $seller = User::findOrFail($id);

        // 1. Get Seller's Products
        $products = MarketplaceProduct::where('user_id', $id)->latest()->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'price' => 'KES ' . number_format($product->price, 0),
                'category' => $product->category,
                'quantity' => (int) $product->quantity,
                'image' => $product->image,
                'time' => $product->created_at->diffForHumans(),
            ];
        });

        // 2. Get Seller's Adverts
        $adverts = MarketplaceAdvert::where('user_id', $id)->latest()->get()->map(function ($advert) {
            return [
                'id' => $advert->id,
                'title' => $advert->title,
                'category' => $advert->category,
                'description' => $advert->description,
                'time' => $advert->created_at->diffForHumans(),
            ];
        });

        // 3. Return everything in one clean package
        return response()->json([
            'profile' => [
                'id' => $seller->id,
                'fullname' => $seller->fullname,
                'email' => $seller->email,
                'phone' => $seller->phone,
                'avatar' => $seller->avatar,
                'isVerified' => $seller->verification_status === 'approved',
                'joined' => $seller->created_at->format('M Y'),
            ],
            'products' => $products,
            'adverts' => $adverts
        ]);
    }
}