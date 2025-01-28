<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Mention;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index($feedback_id)
    {
        $comments = Comment::where('feedback_id', $feedback_id)->with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($comments);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'feedbackId' => 'required|exists:feedback,id',
            'commentMessage' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->feedback_id = $request->feedbackId;
        $comment->user_id = Auth::id();
        $comment->content = $request->commentMessage;
        $comment->save();

        $comment->load('user');

        preg_match_all('/@(\w+)/', $request->commentMessage, $matches);
        $usernames = $matches[1];

        foreach ($usernames as $username) {
            $mentionedUser = User::where('name', $username)->first();
            if ($mentionedUser) {
                Mention::create([
                    'comment_id' => $comment->id,
                    'mentioned_user_id' => $mentionedUser->id,
                ]);
            }
        }

        return response()->json(['message' => 'Comment added successfully', 'comment' => $comment]);
    }


    
}
