<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;
    
    public function children() {
        return $this->hasMany(Topic::class, 'parent_id');
    }
    
    public function parent() {
        return $this->belongsTo(Topic::class, 'parent_id');
    }
    
    public function getTotalChannelsAttribute() {
        return $this->hasMany(Channel::class)->whereTopicId($this->id)->count();
    }
}
