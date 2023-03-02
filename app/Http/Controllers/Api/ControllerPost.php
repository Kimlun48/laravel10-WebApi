<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\post;
use  App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ControllerPost extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get posts
        $posts = post::latest()->paginate(5);

        //return collection of posts as a resource
        return new PostResource(true, 'List Data Posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(),[
            'image' => 'required|image|mimes:png,jpg,jpeg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);


        //check validation
        if ($validator->fails()) {
            return response()->json($validator->errors(),422);
        }

        //upload image
        $image = $request->file('image');
        $image -> storeAs('public/posts', $image->hashName());
        //create post
        $post = post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
            return new PostResource(true, 'Data Post Berhasil Di Tambah',$post);

        }

    /**
     * Display the specified resource.
     */
    public function show(post $post)
    {
        return new PostResource(true, 'Data Post Di Temukan', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, post $post)
    {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'content' => 'required',
        ]);
        //check validation
        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        //check image is not empty
        if ($request->hasFile('image')) {
            //upload image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            //delete old image
            Storage::delete('public/posts'.$post->image);

            //update post with new image
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);

        }else{
            //update post without image
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }
        return new PostResource(true, 'Data Berhasil Di Update', $post);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(post $post)
    {
        //delete image
        Storage::delete('public/post'. $post->image);
        //delete post
        $post->delete();
        //return response
        return new PostResource(true, 'Data Berhasil DI hapus',null);
    }
}
