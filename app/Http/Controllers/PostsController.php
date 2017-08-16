<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Post;

class PostsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', [
            'except' => [
                'index',
                'show',
            ]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//        $posts = Post::all();
//        $posts = Post::orderBy('title', 'asc')->get();

        $posts = Post::orderBy('created_at', 'asc')->paginate(1);

        return view('model.post.index')->with('posts', $posts);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('model.post.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
        ]);

        // Create the post
        $post = new Post();
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->user_id = auth()->user()->id;
        $post->save();

        // Redirect to the post index
        // TODO: Dynamically link to /posts
        // TODO: Use dynamic language here
        return redirect()->route('posts.index')->with('success', 'Post created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::find($id);

        return view('model.post.show')->with('post', $post);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);

        // Check for correct user
        if(auth()->user()->id != $post->user_id)
            return redirect()->route('posts.index')->with('error', 'Unauthorized page');

        return view('model.post.edit')->with('post', $post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate
        $this->validate($request, [
            'title' => 'required',
            'body' => 'required',
        ]);

        // Find the post
        $post = Post::find($id);

        // Check for correct user
        if(auth()->user()->id != $post->user_id)
            return redirect()->route('posts.index')->with('error', 'Unauthorized page');

        // Update the properties
        $post->title = $request->input('title');
        $post->body = $request->input('body');
        $post->save();

        // Redirect to the post index
        // TODO: Dynamically link to /posts
        // TODO: Use dynamic language here
        return redirect()->route('posts.index')->with('success', 'Post updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::find($id);

        $post->delete();

        // Redirect to the post index
        // TODO: Dynamically link to /posts
        // TODO: Use dynamic language here
        return redirect()->route('posts.index')->with('success', 'Post removed');
    }
}
