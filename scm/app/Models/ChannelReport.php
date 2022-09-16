<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChannelReport extends Model
{
    use HasFactory;
    
    
    public function channel() {
        return $this->belongsTo(Channel::class, 'channel_id');
    }
}
