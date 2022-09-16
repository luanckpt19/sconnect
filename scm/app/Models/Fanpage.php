<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fanpage extends Model
{
    use HasFactory;

    protected $table = 'fanpage';

    protected $fillable = [
        'link',
        'page_name',
        'theme',
    ];
}
