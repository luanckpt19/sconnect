<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    use HasFactory;
    
    public function getTotalChannelsAttribute() {
        return $this->hasMany(Channel::class)->wherePlatformId($this->id)->count();
    }
}
