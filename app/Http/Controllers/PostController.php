<?php

namespace App\Http\Controllers;

use App\Http\Requests\likeRequest;
use App\Models\Post;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Like;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostResource::collection(Post::withCount('likes')->where('user_id',auth()->id())->get());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function showAll()
    {
        return PostResource::collection(Post::where('is_public',true)->get());
       
    }

    public function searchPost(Request $request)
    {
        $search = $request->search??'';
        // dd($search);
        return PostResource::collection(
            Post::withCount('likes')
            ->where('user_id',auth()->id())
            ->when($search, function ($q) use($search) {
                $q->where(function($q) use($search){
                    $q->where('title', 'like',$search."%")->orWhere('body', 'like',$search."%");
                });
            })
            ->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        
        $post = Post::create($request->validated());
        $post->loadCount('likes');
        return new PostResource($post);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->loadCount('likes');
        return new PostResource($post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $post->loadCount('likes');
       
     
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
      $post->update($request->validated());

      return new PostResource($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
       $post->delete();
      return response()->noContent();
    }
    public function likes(likeRequest $request){
        

        
        if(auth()->id() >0 ){

        
        $post = Post::findOrFail($request->post_id);
    // dd($post);
    $like = Like::where(
        ['user_id' => $request->user_id, 'post_id' => $post->id]
    )->get();

    if($like->count()>0){

        $message = 'un liked post';
    Like::where(
        ['user_id' => $request->user_id, 'post_id' => $post->id]
    )->delete();
    }
    else{
        $message = 'Liked post';
        Like::create(
            ['user_id' => $request->user_id, 'post_id' => $post->id]
        );
    }
    $post->loadCount('likes');
       
}else{
    $message = 'Logged in first to like or unlike post';
}
        return response()->json(['error'=>true,'message'=>$message]);


    }
}
