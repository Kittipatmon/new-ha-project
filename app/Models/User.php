<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'userkml2025';
    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'emp_code',
        'firstname',
        'lastname',
        'email',
        'username',
        'password',
        'dept_id',
        'status',
        'role',
        'profile_pic',
        'signature',
        'resign_date',
    ];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'resign_date' => 'datetime',
    ];

    protected $appends = [
        'fullname',
        'employee_code',
        'level_user_label',
        'level_user_color',
        'hr_status_label',
        'hr_status_color',
        'status_label',
        'status_color',
    ];

    // Accessors/Mutators to dynamically map missing/changed columns
    public function getStartworkDateAttribute()
    {
        return $this->created_at;
    }

    public function setStartworkDateAttribute($value)
    {
        $this->attributes['created_at'] = $value;
    }

    public function getEndworkDateAttribute()
    {
        return $this->resign_date;
    }

    public function setEndworkDateAttribute($value)
    {
        $this->attributes['resign_date'] = $value;
    }

    public function getDepartmentIdAttribute()
    {
        return $this->dept_id;
    }

    public function setDepartmentIdAttribute($value)
    {
        $this->attributes['dept_id'] = $value;
    }

    public function getDivisionIdAttribute()
    {
        return $this->department ? $this->department->division_id : null;
    }

    public function setDivisionIdAttribute($value)
    {
        // No-op to prevent database errors when saving
    }

    public function getSectionIdAttribute()
    {
        return $this->department ? $this->department->section_id : null;
    }

    public function setSectionIdAttribute($value)
    {
        // No-op to prevent database errors when saving
    }

    public function getLevelUserAttribute()
    {
        return $this->role === 'admin' ? self::LEVEL_USER_SYSTEM_ADMIN : self::LEVEL_USER_OPERATION_STAFF;
    }

    public function setLevelUserAttribute($value)
    {
        // No-op
    }

    public function getSexAttribute()
    {
        return 'ชาย'; // default
    }

    public function setSexAttribute($value)
    {
        // No-op
    }

    public function getWorkplaceAttribute()
    {
        return 'HQ'; // default
    }

    public function setWorkplaceAttribute($value)
    {
        // No-op
    }

    public function usertype()
    {
        return $this->belongsTo(UserType::class, 'level_user', 'id');
    }
    

    // Accessor for `$user->fullname`
    public function getFullnameAttribute(): string
    {
        return trim("{$this->firstname} {$this->lastname}");
    }

    public function getEmployeeCodeAttribute()
    {
        return $this->emp_code;
    }

    public function setEmployeeCodeAttribute($value)
    {
        $this->attributes['emp_code'] = $value;
    }

    public function getHrStatusAttribute()
    {
        return '1'; // Default inactive since column is gone
    }

    public function setHrStatusAttribute($value)
    {
        // No-op to prevent database error
    }

    public function isHrOrAdmin()
    {
        return $this->dept_id == 15 || $this->role === 'admin';
    }

    public function getPhotoUserAttribute()
    {
        return $this->profile_pic;
    }

    public function setPhotoUserAttribute($value)
    {
        $this->attributes['profile_pic'] = $value;
    }

    public function department()
    {
        $db = config('database.connections.mysql.database', 'hrsystem');
        $instance = new Department();
        $instance->setTable("{$db}.department");
        return $this->newBelongsTo(
            $instance->newQuery(),
            $this,
            'dept_id',
            'department_id',
            'department'
        );
    }

    public function division()
    {
        $db = config('database.connections.mysql.database', 'hrsystem');
        
        $related = new Division();
        $related->setTable("{$db}.divisions");
        
        $through = new Department();
        $through->setTable("{$db}.department");

        return $this->newHasOneThrough(
            $related->newQuery(),
            $this,
            $through,
            'department_id', // Foreign key on Department table
            'division_id',   // Foreign key on Division table
            'dept_id',       // Local key on User table
            'division_id'    // Local key on Department table
        );
    }

    public function section()
    {
        $db = config('database.connections.mysql.database', 'hrsystem');
        
        $related = new Section();
        $related->setTable("{$db}.sections");
        
        $through = new Department();
        $through->setTable("{$db}.department");

        return $this->newHasOneThrough(
            $related->newQuery(),
            $this,
            $through,
            'department_id', // Foreign key on Department table
            'section_id',    // Foreign key on Section table
            'dept_id',       // Local key on User table
            'section_id'     // Local key on Department table
        );
    }

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'resign';

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
                'color' => 'error',
                'icon' => '<svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor"><rect x="1.972" y="11" width="20.056" height="2" transform="translate(-4.971 12) rotate(-45)" fill="currentColor" stroke-width="0"></rect><path d="m12,23c-6.065,0-11-4.935-11-11S5.935,1,12,1s11,4.935,11,11-4.935,11-11,11Zm0-20C7.038,3,3,7.037,3,12s4.038,9,9,9,9-4.037,9-9S16.962,3,12,3Z" stroke-width="0" fill="currentColor"></path></g></svg>',
            ],
        ];
    }
    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->status]['label'] ?? '-';
    }
    public function getStatusColorAttribute()
    {
        return self::getStatusOptions()[$this->status]['color'] ?? 'default';
    }
    public function getStatusIconAttribute()
    {
        return self::getStatusOptions()[$this->status]['icon'] ?? '';
    }

    // Scope to filter active users only when such a column exists
    public function scopeActive($query)
    {
        $table = $this->getTable();
        $schema = $this->getConnection()->getSchemaBuilder();

        if ($schema->hasColumn($table, 'status')) {
            return $query->where($table . '.status', self::STATUS_ACTIVE);
        }
        if ($schema->hasColumn($table, 'user_status')) {
            return $query->where($table . '.user_status', self::STATUS_ACTIVE);
        }
        if ($schema->hasColumn($table, 'active')) {
            return $query->where($table . '.active', 1);
        }
        return $query; // no-op when no known column exists
    }

    // ระดับผู้ใช้งาน
    const LEVEL_USER_SYSTEM_ADMIN = '0'; // System Administrator
    const LEVEL_USER_OPERATION_STAFF = '1'; // พนักงานปฏิบัติการ
    const LEVEL_USER_SUPERVISOR = '2'; // หัวหน้า , Supervisor
    const LEVEL_USER_OFFICER = '3'; // เจ้าหน้าที่ , Officer
    const LEVEL_USER_EXECUTIVE = '4'; // เจ้าหน้าที่อาวุโส , Executive
    const LEVEL_USER_HEAD_SECTION = '5'; // หัวหน้างาน , Head Section
    const LEVEL_USER_ASST_DEPT_MGR = '6'; // ผู้ช่วยผู้จัดการแผนก , Asst Department Mgr.
    const LEVEL_USER_DEPT_MGR = '7'; // ผู้จัดการแผนก , Department Mgr.
    const LEVEL_USER_DIVISION_MGR = '8'; // ผู้จัดการฝ่าย , Division Mgr.
    const LEVEL_USER_C_LEVEL = '9'; // C-Level

    public static function getLevelUserOptions()
    {
        return [
            self::LEVEL_USER_SYSTEM_ADMIN => [
                'label' => 'System Administrator',
                'color' => 'error', // daisyUI: btn-primary, badge-primary
                'icon' => 'mdi mdi-shield-account', // Example: Material Design Icons
            ],
            self::LEVEL_USER_OPERATION_STAFF => [
                'label' => '1',
                'color' => 'info', // daisyUI: btn-secondary, badge-secondary
                'icon' => 'mdi mdi-account', // Example: Material Design Icons
            ],
            self::LEVEL_USER_SUPERVISOR => [
                'label' => '2',
                'color' => 'primary', // daisyUI: btn-secondary, badge-secondary
                'icon' => 'mdi mdi-account-tie', // Example: Material Design Icons
            ],
            self::LEVEL_USER_OFFICER => [
                'label' => '3',
                'color' => 'success', // daisyUI: btn-success, badge-success
                'icon' => 'mdi mdi-account-cog', // Example: Material Design Icons
            ],
            self::LEVEL_USER_EXECUTIVE => [
                'label' => '4',
                'color' => 'warning', // daisyUI: btn-warning, badge-warning
                'icon' => 'mdi mdi-account-star', // Example: Material Design Icons
            ],
            self::LEVEL_USER_HEAD_SECTION => [
                'label' => '5',
                'color' => 'accent', // daisyUI: btn-accent, badge-accent
                'icon' => 'mdi mdi-account-supervisor', // Example: Material Design Icons
            ],
            self::LEVEL_USER_ASST_DEPT_MGR => [
                'label' => '6',
                'color' => 'secondary', // daisyUI: btn-secondary, badge-secondary
                'icon' => 'mdi mdi-account-tie', // Example: Material Design Icons
            ],
            self::LEVEL_USER_DEPT_MGR => [
                'label' => '7',
                'color' => 'neutral', // daisyUI: btn-neutral, badge-neutral
                'icon' => 'mdi mdi-account-tie', // Example: Material Design Icons
            ],
            self::LEVEL_USER_DIVISION_MGR => [
                'label' => '8',
                'color' => 'base-100', // daisyUI: btn-base-100, badge-base-100
                'icon' => 'mdi mdi-account-tie', // Example: Material Design Icons
            ],
            self::LEVEL_USER_C_LEVEL => [
                'label' => '9',
                'color' => 'base-200', // daisyUI: btn-base-200, badge-base-200
                'icon' => 'mdi mdi-account-tie', // Example: Material Design Icons
            ],
        ];
    }

    public function getLevelUserLabelAttribute()
    {
        return self::getLevelUserOptions()[$this->level_user]['label'] ?? '-';
    }

    public function getLevelUserColorAttribute()
    {
        return self::getLevelUserOptions()[$this->level_user]['color'] ?? 'default';
    }

    public function getLevelUserIconAttribute()
    {
        return self::getLevelUserOptions()[$this->level_user]['icon'] ?? '';
    }


    const HR_STATUS_ACTIVE = '0';
    const HR_STATUS_INACTIVE = '1';
    // 0 = เป็น hr   1 = ไม่ได้เป้น HR 

    public static function getHrStatusOptions()
    {
        return [
            self::HR_STATUS_ACTIVE => [
                'label' => 'เป็น',
                'color' => 'warning',
                'icon' => '<svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt"><circle cx="12" cy="12" r="10" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></circle><polyline points="7 13 10 16 17 8" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></polyline></g></svg>',
            ],
            self::HR_STATUS_INACTIVE => [
                'label' => 'ไม่เป็น',
                'color' => 'secondary',
                'icon' => '<svg class="size-[1em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><g fill="currentColor"><rect x="1.972" y="11" width="20.056" height="2" transform="translate(-4.971 12) rotate(-45)" fill="currentColor" stroke-width="0"></rect><path d="m12,23c-6.065,0-11-4.935-11-11S5.935,1,12,1s11,4.935,11,11-4.935,11-11,11Zm0-20C7.038,3,3,7.037,3,12s4.038,9,9,9,9-4.037,9-9S16.962,3,12,3Z" stroke-width="0" fill="currentColor"></path></g></svg>',
            ],
        ];
    }

    public function getHrStatusLabelAttribute()
    {
        return self::getHrStatusOptions()[$this->hr_status]['label'] ?? '-';
    }
    public function getHrStatusColorAttribute()
    {
        return self::getHrStatusOptions()[$this->hr_status]['color'] ?? 'default';
    }
    public function getHrStatusIconAttribute()
    {
        return self::getHrStatusOptions()[$this->hr_status]['icon'] ?? '';
    }

    // เพศ
    const SEX_MALE = 'ชาย';
    const SEX_FEMALE = 'หญิง';
    const SEX_OTHER = 'อื่นๆ';

    public static function getSexOptions()
    {
        return [
            self::SEX_MALE => [
                'label' => 'ชาย',
                'color' => 'primary',
            ],
            self::SEX_FEMALE => [
                'label' => 'หญิง',
                'color' => 'secondary',
            ],
            self::SEX_OTHER => [
                'label' => 'อื่นๆ',
                'color' => 'accent',
            ],
        ];
    }

    public function getSexLabelAttribute()
    {
        return self::getSexOptions()[$this->sex]['label'] ?? ($this->sex ?: '-');
    }

    public function getSexColorAttribute()
    {
        return self::getSexOptions()[$this->sex]['color'] ?? 'default';
    }

    public function trainingApplies()
    {
        return $this->hasMany(TrainingApply::class, 'employee_code', 'employee_code');
    }

}
