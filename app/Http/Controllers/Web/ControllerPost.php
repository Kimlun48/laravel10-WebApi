<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//import model
use App\Models\post;
//return type view
use Illuminate\View\View;
//return type redirect response
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ControllerPost extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():View
    {
        //get post
        $posts=post::latest()->paginate(5);
        //return view
        return view('posts.index', compact('posts'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create():View
    {
        return view('posts.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'image' => 'required|image|mimes:png,jpg,jpeg,png,gif|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);
        //upload image
        $image=$request->file('image');
        $image->storeAs('public/posts', $image->hashName());
         //create posts
         Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
        return redirect ()->route('posts.index')->with(['success'=> 'Data Berhasil Di Simpan']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id):View
    {
        //get post by ID
        $post=post::findOrFail($id);
        //render view with post
        return view ('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id):View
    {
        //get post by ID
        $post = post :: FindOrFail($id);
        //render with post
        return view ('posts.edit', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'image' => 'image|mimes:png,jpg,jpeg,png,gif|max:2048',
            'title' => 'required|min:5',
            'content' => 'required|min:10'
        ]);

        //get Post by ID
        $post = post::FindOrFail($id);

        //check image is upload
        if ($request->hasFile('image')){

            //upload new image
            $image = $request->file('image');
            $image -> storeAs('public/posts', $image->hashName());

            //delete old image
            $image->delete('public/posts'.$post->image);

            //update post with new image
            $post -> update([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content
            ]);
        } else {
            //update post without image
            $post -> update ([
            'title' => $request->title,
            'content' => $request->content
            ]);
        }
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Di Update']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        //get post by ID
        $post = post::FindOrFail($id);
        //delete image
        Storage::delete('public/post'.$post->image);
        //delete post
        $post->delete;
        //return to index
        return redirect()->route('posts.index')->with(['success'=>'Data Berhasil di Hapus']);
    }
}
