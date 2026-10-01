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

    protected $hidden = [
        'password',
        'remember_token',
        'signature',
    ];

    protected $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'resign_date' => 'datetime',
        'password' => 'hashed',
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
        'role_label',
        'role_badge',
        'hr_role',
        'hr_role_label',
        'hr_role_badge',
    ];
    
    /**
     * Relationship to HR System specific role in Database: hrsystem
     */
    public function hrRole()
    {
        return $this->hasOne(HrUserRole::class, 'employee_code', 'emp_code');
    }

    /**
     * Relationship to Microsoft 365 OAuth Token
     */
    public function microsoftToken()
    {
        return $this->hasOne(\App\Models\UserMicrosoftToken::class, 'user_id');
    }

    /**
     * Check if user has active Microsoft 365 connection
     */
    public function hasMicrosoftConnected(): bool
    {
        return $this->microsoftToken()->whereNotNull('access_token')->exists();
    }

    // Accessors/Mutators to dynamically map missing/changed columns
    public function getStartworkDateAttribute()
    {
        $kml = $this->getUserKmlRecord();
        $date = ($kml && !empty($kml->startwork_date)) ? $kml->startwork_date : $this->created_at;
        return $date ? \Carbon\Carbon::parse($date) : null;
    }

    public function setStartworkDateAttribute($value)
    {
        $this->attributes['created_at'] = $value;
    }

    public function getEndworkDateAttribute()
    {
        return $this->resign_date ? \Carbon\Carbon::parse($this->resign_date) : null;
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

    public function getPrefixAttribute()
    {
        if (!empty($this->attributes['prefix'])) {
            return $this->attributes['prefix'];
        }
        $kml = $this->getUserKmlRecord();
        return $kml?->prefix ?? '';
    }

    public function setPrefixAttribute($value)
    {
        $this->attributes['prefix'] = $value;
    }

    public function getFirstNameAttribute()
    {
        return $this->attributes['first_name'] ?? ($this->attributes['firstname'] ?? null);
    }

    public function getLastNameAttribute()
    {
        return $this->attributes['last_name'] ?? ($this->attributes['lastname'] ?? null);
    }

    protected function getUserKmlRecord()
    {
        static $userKmlCache = [];
        $code = $this->emp_code;
        if (!$code) return null;
        if (!array_key_exists($code, $userKmlCache)) {
            try {
                $userKmlCache[$code] = \Illuminate\Support\Facades\DB::table('userkmlsystem.userskml')
                    ->where('employee_code', $code)
                    ->first();
            } catch (\Throwable $e) {
                $userKmlCache[$code] = null;
            }
        }
        return $userKmlCache[$code];
    }

    public function getPositionAttribute()
    {
        if (!empty($this->attributes['position'])) {
            return $this->attributes['position'];
        }
        $kml = $this->getUserKmlRecord();
        return $kml?->position ?? null;
    }

    public function setPositionAttribute($value)
    {
        $this->attributes['position'] = $value;
    }

    public function getSexAttribute()
    {
        if (!empty($this->attributes['sex'])) {
            return $this->attributes['sex'];
        }
        $kml = $this->getUserKmlRecord();
        return $kml?->sex ?? 'ชาย';
    }

    public function setSexAttribute($value)
    {
        $this->attributes['sex'] = $value;
    }

    public function getWorkplaceAttribute()
    {
        if (!empty($this->attributes['workplace'])) {
            return $this->attributes['workplace'];
        }
        $kml = $this->getUserKmlRecord();
        return $kml?->workplace ?? 'สนง.ใหญ่';
    }

    public function setWorkplaceAttribute($value)
    {
        $this->attributes['workplace'] = $value;
    }

    public function getEmployeeTypeAttribute()
    {
        if (!empty($this->attributes['employee_type'])) {
            return $this->attributes['employee_type'];
        }
        $kml = $this->getUserKmlRecord();
        return $kml?->employee_type ?? 'รายเดือน';
    }

    public function getLevelUserAttribute()
    {
        $kml = $this->getUserKmlRecord();
        if ($kml && isset($kml->level_user)) {
            return (string)$kml->level_user;
        }
        return $this->role === 'admin' ? self::LEVEL_USER_SYSTEM_ADMIN : self::LEVEL_USER_OPERATION_STAFF;
    }

    public function setLevelUserAttribute($value)
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
        if (isset($this->attributes['hr_status'])) {
            return (string)$this->attributes['hr_status'];
        }
        $kml = $this->getUserKmlRecord();
        if ($kml && isset($kml->hr_status)) {
            return (string)$kml->hr_status;
        }
        return ($this->dept_id == 15) ? self::HR_STATUS_ACTIVE : self::HR_STATUS_INACTIVE;
    }

    public function setHrStatusAttribute($value)
    {
        // No-op to prevent database error
    }

    public function isHrOrAdmin()
    {
        return $this->canAccessBackend();
    }

    /**
     * Central database (appkum_user) Role:
     * - admin
     * - staff
     *
     * HR System database (hrsystem) Rules:
     * - admin (เข้าถึงระบบ HR ได้ทั้งหมด)
     * - editor (ดู/เพิ่ม/แก้ไข - ห้ามลบ/ห้ามจัดการสิทธิ์)
     * - viewer (ดูข้อมูลได้อย่างเดียว)
     */
    const ROLE_ADMIN = 'admin';
    const ROLE_EDITOR = 'editor';
    const ROLE_VIEWER = 'viewer';
    const ROLE_STAFF = 'staff';

    /**
     * Get the HR System specific role (admin / editor / viewer)
     * Loaded from hrsystem.hr_user_roles, with fallback to central role if admin.
     */
    public function getHrRoleAttribute(): string
    {
        // Safely check relations array or query relationship directly
        $roleRecord = array_key_exists('hrRole', $this->relations) 
            ? $this->relations['hrRole'] 
            : $this->hrRole()->first();

        if ($roleRecord && !empty($roleRecord->role)) {
            return strtolower(trim($roleRecord->role));
        }

        // Fallback: If central user is admin or level_user == 0, grant HR admin
        $centralRole = strtolower(trim((string)($this->role ?? '')));
        if (in_array($centralRole, ['admin', 'superadmin', 'administrator']) || (int)$this->level_user === 0) {
            return self::ROLE_ADMIN;
        }

        return self::ROLE_VIEWER;
    }

    public function getRoleNormalizedAttribute(): string
    {
        return $this->hr_role;
    }

    public function isAdmin(): bool
    {
        return $this->hr_role === self::ROLE_ADMIN;
    }

    public function isEditor(): bool
    {
        return in_array($this->hr_role, [self::ROLE_ADMIN, self::ROLE_EDITOR]);
    }

    public function isViewer(): bool
    {
        return $this->hr_role === self::ROLE_VIEWER;
    }

    /**
     * Check if user can access backend (Only ADMIN and EDITOR)
     */
    public function canAccessBackend(): bool
    {
        return in_array($this->hr_role, [self::ROLE_ADMIN, self::ROLE_EDITOR]);
    }

    /**
     * Check if user belongs to Department 16: Information Communication Technology (ICT)
     */
    public function isIctDepartment(): bool
    {
        if ((int)$this->dept_id === 16 || (int)$this->department_id === 16) {
            return true;
        }

        if ($this->department) {
            if ((int)$this->department->department_id === 16) {
                return true;
            }
            $fullname = strtolower($this->department->department_fullname ?? '');
            $name = strtolower($this->department->department_name ?? '');
            if (str_contains($fullname, 'information communication technology') || str_contains($name, 'ict')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user is an ICT personnel or authorized IT developer
     */
    public function isIct(): bool
    {
        // 1. Department 16 (ICT)
        if ($this->isIctDepartment()) {
            return true;
        }

        // 2. Division check
        if ($this->division) {
            $divName = strtolower($this->division->division_name ?? '');
            $divFull = strtolower($this->division->division_fullname ?? '');
            if (str_contains($divName, 'ict') || str_contains($divFull, 'information communication technology')) {
                return true;
            }
        }

        // 3. Known ICT admin/developer email whitelist
        $allowedEmails = [
            'it@kumwell.com',
            'ict@kumwell.com',
            'admin@company.com',
            'boonsurm.kr@kumwell.com',
            'dunupong.pa@kumwell.com',
            'napapan.so@kumwell.com',
            'kriangsak.duk@kumwell.com',
            'kittipat.ma@kumwell.com',
        ];
        $email = strtolower(trim((string)$this->email));
        if (in_array($email, $allowedEmails, true)) {
            return true;
        }

        // 4. Position title
        $pos = strtolower($this->position ?? '');
        if (
            str_contains($pos, 'ict') ||
            str_contains($pos, 'developer') ||
            str_contains($pos, 'programmer') ||
            str_contains($pos, 'software') ||
            str_contains($pos, 'network') ||
            str_contains($pos, 'system admin') ||
            str_contains($pos, 'it specialist') ||
            str_contains($pos, 'it support')
        ) {
            return true;
        }

        return false;
    }

    /**
     * Check if user is permitted to view & manage Database Backups (ICT only)
     */
    public function canAccessDatabaseBackups(): bool
    {
        // Must be an admin or CEO, AND belong to ICT
        return ($this->isAdmin() || $this->isCeo() || (string)$this->level_user === '9') && $this->isIct();
    }

    /**
     * Check if user can assign the ADMIN role:
     * ADMIN can grant EDITOR, VIEWER.
     * Only Department 16 (Information Communication Technology) can grant ADMIN.
     */
    public function canAssignAdminRole(): bool
    {
        return $this->isAdmin() && $this->isIctDepartment();
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin();
    }

    public function canDelete(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Check if user is the CEO / Chief Executive Officer
     */
    public function isCeo(): bool
    {
        $pos = strtolower($this->position ?? '');
        if (str_contains($pos, 'ceo') || str_contains($pos, 'ประธานเจ้าหน้าที่บริหาร') || str_contains($pos, 'chief executive')) {
            return true;
        }
        if ((string)$this->level_user === '9' || (string)$this->level_user === self::LEVEL_USER_C_LEVEL) {
            if (empty($pos) || str_contains($pos, 'บริหาร')) {
                return true;
            }
        }
        return false;
    }

    public function canEdit(): bool
    {
        return $this->isAdmin() || $this->hr_role === self::ROLE_EDITOR;
    }

    public function canCreate(): bool
    {
        return $this->isAdmin() || $this->hr_role === self::ROLE_EDITOR;
    }

    public static function getAvailableRoles(): array
    {
        return HrUserRole::getAvailableRoles();
    }

    public function getHrRoleLabelAttribute(): string
    {
        return strtoupper($this->hr_role);
    }

    public function getHrRoleBadgeAttribute(): string
    {
        $roles = self::getAvailableRoles();
        return $roles[$this->hr_role]['badge_class'] ?? 'bg-gray-100 text-gray-700';
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->hr_role_label;
    }

    public function getRoleBadgeAttribute(): string
    {
        return $this->hr_role_badge;
    }

    public function hasRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) return true;
            }
            return false;
        }
        if ($this->isAdmin()) return true;
        return $this->hr_role === strtolower($roles) || $this->role === $roles;
    }

    public function getPhotoUserAttribute()
    {
        if (!empty($this->profile_pic) && file_exists(public_path($this->profile_pic))) {
            return $this->profile_pic;
        }

        // Check if there is an image in images/profiles for this employee code
        $code = $this->emp_code;
        if ($code) {
            $files = glob(public_path('images/profiles/*' . $code . '*'));
            if (!empty($files)) {
                usort($files, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $relPath = 'images/profiles/' . basename($files[0]);
                return $relPath;
            }
        }

        $kml = $this->getUserKmlRecord();
        if ($kml && !empty($kml->photo_user) && file_exists(public_path($kml->photo_user))) {
            return $kml->photo_user;
        }

        return null;
    }

    public function setPhotoUserAttribute($value)
    {
        $this->attributes['profile_pic'] = $value;
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'dept_id', 'department_id');
    }

    public function orgDepartment()
    {
        return $this->belongsTo(OrgDepartment::class, 'dept_id', 'id');
    }

    /**
     * Get department IDs where this user is the department manager (manager_id in departments table).
     */
    public function getManagedDepartmentIds(): array
    {
        return OrgDepartment::where('manager_id', $this->id)->pluck('id')->toArray();
    }

    /**
     * Check if this user is a department manager in departments table.
     */
    public function isDepartmentManager(): bool
    {
        return !empty($this->getManagedDepartmentIds());
    }

    /**
     * Check if user is in Central HR / Recruitment department (dept_id = 15 or role = admin)
     */
    public function isCentralHr(): bool
    {
        return $this->role === 'admin' || (int)$this->dept_id === 15;
    }

    /**
     * Check if user is allowed to see 'Candidates forwarded by HA'
     * (Only Central HR or designated department managers with manager_id)
     */
    public function canViewDeptCandidates(): bool
    {
        return $this->isCentralHr() || $this->isDepartmentManager();
    }

    public function division()
    {
        return $this->hasOneThrough(
            Division::class,
            Department::class,
            'department_id', // Foreign key on Department table
            'division_id',   // Foreign key on Division table
            'dept_id',       // Local key on User table
            'division_id'    // Local key on Department table
        );
    }

    public function section()
    {
        return $this->hasOneThrough(
            Section::class,
            Department::class,
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
        return $this->hasMany(TrainingApply::class, 'employee_code', 'emp_code');
    }

}
