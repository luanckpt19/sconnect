<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model
{
    use HasFactory;

    public function ticket() {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}
