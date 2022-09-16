<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    use HasFactory;

    protected $dates = ['start_date', 'end_date'];

    public function creator() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function marketer() {
        return $this->belongsTo(User::class, 'mkt_user_id');
    }

    public function video() {
        return $this->belongsTo(Video::class, 'video_id');
    }

    public function getTotalUnreadCommentAttribute() {
        return $this->hasMany(TicketComment::class)
            ->whereTicketId($this->id)
            ->where('user_id', '<>', Auth::user()->id)
            ->where('is_read', 0)->count();
    }
}
