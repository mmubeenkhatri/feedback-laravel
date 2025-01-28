<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index()
    {
        $feedback = Feedback::with('user','comments.user')->orderBy('created_at', 'desc')->get();
        return response()->json($feedback);
    }


    public function store(Request $request)
    {
        // dd("in store");
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
        ]);

        $feedback = new Feedback();
        $feedback->title = $request->title;
        $feedback->description = $request->description;
        $feedback->category = $request->category;
        $feedback->user_id = Auth::id();
        $feedback->save();

        $feedback->load('user','comments.user');

        return response()->json(['message' => 'Feedback submitted successfully', 'feedback' => $feedback]);
    }
}
