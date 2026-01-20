<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['creator', 'assignee']);

        // Role-based filtering
        if (!auth()->user()->isAdmin()) {
            $query->where('created_by', auth()->id());
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->latest()->get();

        return response()->json($tickets->map(function ($ticket) {
            return $this->formatTicketResponse($ticket);
        }));
    }

    public function store(CreateTicketRequest $request)
    {
        $ticket = Ticket::create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        $ticket->load(['creator', 'assignee']);

        return response()->json($this->formatTicketResponse($ticket), 201);
    }

    public function show($id)
    {
        // Handle both ticket_id (TKT-10001) and database id
        if (str_starts_with($id, 'TKT-')) {
            $ticket = Ticket::where('ticket_id', $id)->with(['creator', 'assignee', 'comments.user'])->firstOrFail();
        } else {
            $ticket = Ticket::with(['creator', 'assignee', 'comments.user'])->findOrFail($id);
        }

        // Authorization: Users can only view their own tickets
        if (!auth()->user()->isAdmin() && $ticket->created_by !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Filter internal comments for non-admin users
        if (!auth()->user()->isAdmin()) {
            $ticket->setRelation('comments', $ticket->comments->where('is_internal', false)->values());
        }

        return response()->json($this->formatTicketResponse($ticket, true));
    }

    public function update(UpdateTicketRequest $request, $id)
    {
        // Handle both ticket_id (TKT-10001) and database id
        if (str_starts_with($id, 'TKT-')) {
            $ticket = Ticket::where('ticket_id', $id)->firstOrFail();
        } else {
            $ticket = Ticket::findOrFail($id);
        }

        $data = $request->validated();

        // Handle assigned_to email conversion
        if (isset($data['assigned_to'])) {
            $assignee = \App\Models\User::where('email', $data['assigned_to'])->first();
            $data['assigned_to'] = $assignee?->id;
        }

        $ticket->update($data);

        // Add internal note if provided
        if ($request->has('internal_note')) {
            $ticket->comments()->create([
                'user_id' => auth()->id(),
                'content' => $request->internal_note,
                'is_internal' => true,
            ]);
        }

        $ticket->load(['creator', 'assignee']);

        return response()->json($this->formatTicketResponse($ticket));
    }

    /**
     * Format ticket response according to API specification
     */
    private function formatTicketResponse($ticket, $includeComments = false)
    {
        $response = [
            'id' => $ticket->ticket_id,
            'subject' => $ticket->subject,
            'status' => $ticket->status,
            'priority' => $ticket->priority,
            'category' => $ticket->category,
            'created_by' => $ticket->creator?->email ?? null,
            'assigned_to' => $ticket->assignee?->email ?? null,
            'created_at' => $ticket->created_at->format('Y-m-d\TH:i:s\Z'),
        ];

        // Add description if viewing single ticket
        if ($includeComments || request()->route()->getName() === null) {
            $response = array_merge([
                'id' => $ticket->ticket_id,
                'subject' => $ticket->subject,
                'description' => $ticket->description,
                'status' => $ticket->status,
                'priority' => $ticket->priority,
                'category' => $ticket->category,
                'created_by' => $ticket->creator?->email ?? null,
                'assigned_to' => $ticket->assignee?->email ?? null,
                'created_at' => $ticket->created_at->format('Y-m-d\TH:i:s\Z'),
            ], []);
        }

        // Add comments if requested
        if ($includeComments && $ticket->relationLoaded('comments')) {
            $response['comments'] = $ticket->comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'is_internal' => $comment->is_internal,
                    'user' => $comment->user?->email ?? null,
                    'created_at' => $comment->created_at->format('Y-m-d\TH:i:s\Z'),
                ];
            });
        }

        return $response;
    }
}