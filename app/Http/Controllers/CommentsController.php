<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Comment;
use Auth;
use App\Events\NewComment;

class CommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Post $post)
    {
      return response()->json(
          $post->comments()->with('user')->latest()->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Post $post)
    {
      $comment = $post->comments()->create([
        'body' => $request->body,
        'user_id' => Auth::user()->id,
        'post_id' => $post->id
      ]);

      $comment = Comment::where('id', $comment->id)->with('user')->first();

      broadcast(new NewComment($comment))->toOthers();

      return $comment->toJson();
    }
}
