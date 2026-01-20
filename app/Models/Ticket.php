<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_id',
        'created_by',
        'assigned_to',
        'subject',
        'description',
        'status',
        'priority',
        'category'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_id)) {
                // Get the last ticket to determine next number
                $lastTicket = static::orderBy('id', 'desc')->first();
                $nextNumber = $lastTicket ? ($lastTicket->id + 1) : 10001;

                // Generate ticket ID in format TKT-10001
                $ticket->ticket_id = 'TKT-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}