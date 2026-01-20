<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Ticket;

class CommentController extends Controller
{
    public function store(CommentRequest $request, $ticketId)
    {
        // Handle both ticket_id (TKT-10001) and database id
        if (str_starts_with($ticketId, 'TKT-')) {
            $ticket = Ticket::where('ticket_id', $ticketId)->firstOrFail();
        } else {
            $ticket = Ticket::findOrFail($ticketId);
        }

        // Authorization check
        if (!auth()->user()->isAdmin() && $ticket->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Only admins can create internal comments
        $isInternal = $request->boolean('is_internal') && auth()->user()->isAdmin();

        $comment = $ticket->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_internal' => $isInternal,
        ]);

        $comment->load('user');

        return response()->json([
            'id' => $comment->id,
            'content' => $comment->content,
            'is_internal' => $comment->is_internal,
            'user' => $comment->user?->email,
            'created_at' => $comment->created_at->format('Y-m-d\TH:i:s\Z'),
        ], 201);
    }
}