<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Article;
use App\Models\User;
class ArticleController extends Controller
{
    public function index(){
       dd('this is index results');
    }
    public function store(Request $request){
        $user= User::find($request->user_id);

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
 public function update(Request $request, $id){
    $validated= $request->validate([
        'title'=> 'required',
        'description'=> 'required',
        'author'=>'required'
    ]);
    $article=Article::findOrFail($id);
    if($article){
    $article->update([
        'title'=> $validated['title'],
        'description'=> $validated['description'],
        'author'=> $validated['author']
    ]);
    
    return response()->json([
        'status'=> 'Updated successfully',
        'data'=> $article
    ]);
    } else{
        return response()->json([
        'status'=> 'error',
        'message'=> 'failed to update article',
        'data'=> $article
    ]);
    }

 }
    public function show($id){
  $article = Article::find($id);
  if ($article) {
    return response()->json([
    'status'=> 'success',
    'data'=> $article
    ]);
  }else {
    return response()->json([
            'status' => 'error',
            'message' => 'Article not found'
        ], 404);
  }
    }
      public function destroy(Request $request, $id)
{
    $article = Article::find($id);

    if ($article) {
        $article::destroy($id);

        return response()->json([
            'status' => 'successfully deleted'
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Article not found'
        ], 404);
    }
}

}
