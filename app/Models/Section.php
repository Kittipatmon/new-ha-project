<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{

    protected $connection = 'userkml2025';
    protected $table = 'sections';

    protected $primaryKey = 'section_id';

    public $timestamps = true;

    protected $fillable = [
        'section_code',
        'section_name',
        'section_fullname',
        'section_status',
        'section_description',
        'sort_order',
    ];

    const STATUS_ACTIVE = 0;
    const STATUS_INACTIVE = 1;

    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => [
                'label' => 'ใช้งาน',
                'color' => 'success',
            ],
            self::STATUS_INACTIVE => [
                'label' => 'ไม่ใช้งาน',
                'color' => 'error',
            ],
        ];
    }

    public static function getStatusLabel($status)
    {
        $statuses = self::getStatusOptions();
        return $statuses[$status]['label'] ?? 'Unknown';
    }

    public static function getStatusColor($status)
    {
        $statuses = self::getStatusOptions();
        return $statuses[$status]['color'] ?? 'gray';
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'section_id', 'section_id');
    }

}
