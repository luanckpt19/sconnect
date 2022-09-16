<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;
    
    public function department() {
        return $this->belongsTo(Department::class, 'department_id');
    }
    
    public function platform() {
        return $this->belongsTo(Platform::class, 'platform_id');
    }

    public function topic() {
        return $this->belongsTo(Topic::class, 'topic_id');
    }
    
    public function manager() {
        return $this->belongsTo(User::class, 'staff_manager_id');
    }
    public function channel_type() {
        return $this->belongsTo(ChannelType::class, 'channel_type_id');
    }
    public function getTotalVideosAttribute() {
        return $this->hasMany(Video::class)->whereChannelId($this->id)->count();
    }
}
