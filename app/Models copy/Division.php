<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'divisions';

    protected $primaryKey = 'division_id';

    public $timestamps = false;

    protected $fillable = [
        'section_id',
        'division_code',
        'division_name',
        'division_fullname',
        'division_status',
        'division_description',
    ];


    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }
}
