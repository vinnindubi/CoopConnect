<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;
class ArticleController extends Controller
{
    public function index(){
       dd('this is index results');
    }
    public function store(Request $request){
    $validated= $request->validate([
        'title'=>'required',
        'description'=> 'required',
        'author'=> 'required'
        ]);
        if($validated){
            $data=Article::create([
                'title'=> $validated['title'],
                'description'=> $validated['description'],
                'author'=> $validated['author']
            ]);
        }
        return response()->json([
            'status'=> 'success',
            'data'=> $data
        ]);
    }
    public function destroy(Request $request){}
    public function show(Request $request){}
}
