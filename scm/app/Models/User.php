<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    	'google_id',
    	'given_name',
    	'family_name',
    	'picture',
    	'user_token'
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function parent() {
    	return $this->belongsTo(Department::class, 'department_id');
    }
	
    public function title() {
    	return $this->belongsTo(Title::class, 'position');
    }
    
    /*
     * Lấy danh sách toàn bộ nhân viên của phòng ban cũng như của các phòng ban con.
     * $staff_group = product | qtk | mkt ?
     * */ 
    public static function getAllStaffInner($dept_id, $permission = null) {
        $staff_list = array();
        $dept_ids = Department::getAllChildrenId($dept_id);
        if (!empty($dept_ids) && count($dept_ids) > 0) {
            $staff_list = User::whereIn('department_id', $dept_ids);
            if (!empty($permission)) {
                $staff_list = $staff_list->where('permission', $permission);
            }
            
            $staff_list = $staff_list->get();
        }
        return $staff_list;
    }
}
