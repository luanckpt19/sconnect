<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class File extends Model {
    
    use HasFactory;
    
    public function video() {
        return $this->hasMany(Video::class, 'product_id');
    }
    
    public function getTotalVideosAttribute() {
        return Video::where('product_id', $this->id)->count();
    }
    
    public function getTotalChannelsAttribute() {        
        $total = DB::table('videos')->select('channel_id')->where('product_id', $this->id)->groupBy('channel_id')->get();        
        return empty($total) ? 0 : count($total);
        
    }
    
}
