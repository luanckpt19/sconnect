<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model {
    use HasFactory;
    
    protected $table="departments";
    
    protected $fillable = [
    		'name',
    		'parent_id',
    		'manager_id',
    		'alias',
    		'address'
    ];
    
    public function children() {
    	return $this->hasMany(Department::class, 'parent_id');
    }
    
    public function parent() {
    	return $this->belongsTo(Department::class, 'parent_id');
    }
    
    public function staffs() {
    	return $this->hasMany(User::class, 'department_id');
    }
    
    public function manager() {
    	return $this->belongsTo(User::class, 'manager_id');
    }
    
    public function prefixes() {
    	return $this->belongsTo(DepartmentPrefix::class, 'prefix');
    }
	
    public function getTotalStaffsAttribute() {
    	return $this->hasMany(User::class)->whereDepartmentId($this->id)->count();
    }
    
    public static function getAllChildrenId($dept_id) {
        $dept_ids = array();
        $dept = Department::find($dept_id);
        if (!empty($dept)) $dept_ids[] = $dept->id;
        
        $children = $dept->children;
        if (!empty($children) && count($children) > 0) {
            foreach ($children as $child) {
                $dept_ids[] = Department::getAllChildrenId($child->id);
            }
        }
        return $dept_ids;
    }

    public static function getDeptTreeParent($dept_id) {
        $depts = array();
        $dept = Department::find($dept_id);
        if (!empty($dept)) {
            $depts[] = $dept;
            $parent = $dept->parent;
            if (!empty($parent)) {
                $depts = array_merge(Department::getDeptTreeParent($parent->id), $depts);
            }
        }
        return $depts;
    }
}
