<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    // protected $connection = 'userkml';
    protected $connection = 'userkml2025';
    protected $table = 'department';
    

    protected $primaryKey = 'department_id';

    public $timestamps = false;

    protected $fillable = [
        'division_id',
        'section_id',
        'department_name',
        'department_fullname',
        'department_status',
        'department_description',
        'created_at',
        'updated_at',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'department_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'section_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id', 'division_id');
    }

    const STATUS_ACTIVE = '0';
    const STATUS_INACTIVE = '1';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => [
                'label' => 'ใช้งาน',
                'color' => 'success',
                'icon' => '<svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></circle><polyline points="7 13 10 16 17 8" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></polyline></g></svg>',
            ],
            self::STATUS_INACTIVE => [
                'label' => 'ไม่ใช้งาน',
                'color' => 'primary',
                'icon' => '<svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor"><rect x="1.972" y="11" width="20.056" height="2" transform="translate(-4.971 12) rotate(-45)" fill="currentColor" stroke-width="0"></rect><path d="m12,23c-6.065,0-11-4.935-11-11S5.935,1,12,1s11,4.935,11,11-4.935,11-11,11Zm0-20C7.038,3,3,7.037,3,12s4.038,9,9,9,9-4.037,9-9S16.962,3,12,3Z" stroke-width="0" fill="currentColor"></path></g></svg>',
            ],
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->department_status]['label'] ?? '-';
    }

    public function getStatusColorAttribute()
    {
        return self::getStatusOptions()[$this->department_status]['color'] ?? 'default';
    }

    public function getStatusIconAttribute()
    {
        return self::getStatusOptions()[$this->department_status]['icon'] ?? '';
    }

}
