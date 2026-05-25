<?php

namespace App\Models\hrrequest;

use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class HrRequests extends Model
{
    protected $table = 'hr_requests';

    protected $primaryKey = 'hr_request_id';

    protected $fillable = [
        'request_code',
        'employee_id',
        'approver_manager_id',
        'category_id',
        'type_id',
        'subtype_id',
        'title',
        'detail',
        'status',
        'submitted_at',
        'approver_manager_at',
        'approver_manager_status',
        'approver_manager_comment',
        'approver_hr_id',
        'approver_hr_status',
        'approver_hr_comment',
        'approver_hr_at',
        'cancel_id',
        'cancel_status',
        'cancel_comment',
        'cancel_date',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    // }

    public function category()
    {
        return $this->belongsTo(RequestCategories::class, 'category_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(RequestType::class, 'type_id', 'id');
    }

    public function subtype()
    {
        return $this->belongsTo(RequestSubTypes::class, 'subtype_id', 'id');
    }

    public function approverManager()
    {
        return $this->belongsTo(User::class, 'approver_manager_id', 'id');
    }

    public function approverhr()
    {
        return $this->belongsTo(User::class, 'approver_hr_id', 'id');
    }

    public function timeEdits()
    {
        return $this->hasMany(Request_Time_Edits::class, 'request_id', 'hr_request_id');
    }

    public function timeEdit()
    {
        return $this->hasOne(Request_Time_Edits::class, 'request_id', 'hr_request_id');
    }

    public function uniform()
    {
        return $this->hasOne(Request_Uniforms::class, 'request_id', 'hr_request_id');
    }

    public function safetyItems()
    {
        return $this->hasMany(Request_Safety_Items::class, 'request_id', 'hr_request_id');
    }

    public function safetyDoc()
    {
        return $this->hasOne(Request_Safety_Docs::class, 'request_id', 'hr_request_id');
    }

    public function certificate()
    {
        return $this->hasOne(RequestCertificates::class, 'request_id', 'hr_request_id');
    }

    public function welfare()
    {
        return $this->hasOne(RequestWelfare::class, 'request_id', 'hr_request_id');
    }
    
    public function welfares()
    {
        return $this->hasOne(RequestWelfare::class, 'request_id', 'hr_request_id');
    }


    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED_MANAGER = 'approved_manager';
    const STATUS_APPROVED_HR = 'approved_hr';
    const STATUS_REJECTED = 'rejected';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_RETURNED = 'returned';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_PENDING => [
                'label' => 'รอตรวจสอบโดยผู้จัดการ',
                'color' => 'badge-warning'
            ],
            // self::STATUS_APPROVED_MANAGER => [
            //     'label' => 'รออนุมัติโดยผู้จัดการ',
            //     'color' => 'badge-warning'
            // ],
            self::STATUS_APPROVED_HR => [
                'label' => 'รอตรวจสอบโดยฝ่ายบุคคล',
                'color' => 'badge-info'
            ],
            self::STATUS_REJECTED => [
                'label' => 'ถูกปฏิเสธ',
                'color' => 'badge-error'
            ],
            self::STATUS_CANCELLED => [
                'label' => 'ยกเลิก',
                'color' => 'badge-warning'
            ],
            self::STATUS_COMPLETED => [
                'label' => 'ดำเนินการเสร็จสิ้น',
                'color' => 'badge-success text-white'
            ],
            self::STATUS_RETURNED => [
                'label' => 'ส่งกลับแก้ไข',
                'color' => 'badge-primary'
            ],

        ];
    }

    public function getStatusLabelAttribute()
    {
        $statusOptions = self::getStatusOptions();
        return $statusOptions[$this->status]['label'] ?? 'ไม่ระบุสถานะ';
    }

    public function getStatusColorAttribute()
    {
        $statusOptions = self::getStatusOptions();
        return $statusOptions[$this->status]['color'] ?? 'badge-secondary';
    }


    const APPROVER_MANAGER_PENDING = '0'; // รอดำเนินการ
    const APPROVER_MANAGER_APPROVED = '1'; // อนุมัติ
    const APPROVER_MANAGER_REJECTED = '2'; // ปฏิเสธ
    const APPROVER_MANAGER_RETURNED = '3'; // ส่งคืน
 
    public static function getApproverManagerStatusOptions()
    {
        return [
            self::APPROVER_MANAGER_PENDING => [
                'label' => 'รอดำเนินการอนุมัติ',
                'color' => 'badge-warning'
            ],
            self::APPROVER_MANAGER_APPROVED => [
                'label' => 'อนุมัติ',
                'color' => 'badge-success text-white'
            ],
            self::APPROVER_MANAGER_REJECTED => [
                'label' => 'ไม่อนุมัติคำขอ',
                'color' => 'badge-error'
            ],
            self::APPROVER_MANAGER_RETURNED => [
                'label' => 'ส่งกลับแก้ไข',
                'color' => 'badge-primary'
            ],
        ];
    }

    public function getApproverManagerStatusLabelAttribute()
    {
        $statusOptions = self::getApproverManagerStatusOptions();
        return $statusOptions[$this->approver_manager_status]['label'] ?? 'ไม่ระบุสถานะ';
    }

    public function getApproverManagerStatusColorAttribute()
    {
        $statusOptions = self::getApproverManagerStatusOptions();
        return $statusOptions[$this->approver_manager_status]['color'] ?? 'badge-secondary';
    }


    const APPROVER_HR_PENDING = '0'; // รอดำเนินการ
    const APPROVER_HR_APPROVED = '1'; // อนุมัติ
    const APPROVER_HR_REJECTED = '2'; // ปฏิเสธ
    const APPROVER_HR_RETURNED = '3'; // ส่งคืน

    public static function getApproverHRStatusOptions()
    {
        return [
            self::APPROVER_HR_PENDING => [
                'label' => 'รอดำเนินการตรวจสอบ',
                'color' => 'badge-warning'
            ],
            self::APPROVER_HR_APPROVED => [
                'label' => 'อนุมัติ',
                'color' => 'badge-success text-white'
            ],
            self::APPROVER_HR_REJECTED => [
                'label' => 'ไม่อนุมัติคำขอ',
                'color' => 'badge-error'
            ],
            self::APPROVER_HR_RETURNED => [
                'label' => 'ส่งกลับแก้ไข',
                'color' => 'badge-primary'
            ],
        ];
    }

    public function getApproverHRStatusLabelAttribute()
    {
        $statusOptions = self::getApproverHRStatusOptions();
        return $statusOptions[$this->approver_hr_status]['label'] ?? 'ไม่ระบุสถานะ';
    }

    public function getApproverHRStatusColorAttribute()
    {
        $statusOptions = self::getApproverHRStatusOptions();
        return $statusOptions[$this->approver_hr_status]['color'] ?? 'badge-secondary';
    }


}
