<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';

    protected $primaryKey = 'department_id';

    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'department_name',
        'department_fullname',
        'department_status',
        'department_description',
        'created_at',
        'updated_at',
    ];


    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    
}
