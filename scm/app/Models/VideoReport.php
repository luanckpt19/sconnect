<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class VideoReport extends BaseModel
{
    use HasFactory;
    
    protected $fillable = ['video_id', 'date', 'view_count', 'like_count', 'share_count'];
}
