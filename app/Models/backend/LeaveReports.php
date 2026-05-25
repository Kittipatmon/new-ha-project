<?php

namespace App\Models\backend;

use Illuminate\Database\Eloquent\Model;

class LeaveReports extends Model
{
    protected $table = 'leave_reports';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'division_code',
        'report_month',
        'total_employees',
        'working_days',
        'total_working_days',
        'sick_times',
        'sick_days',
        'personal_times',
        'personal_days',
        'annual_times',
        'annual_days',
        'maternity_times',
        'maternity_days',
        'other_times',
        'other_days',
        'total_leave_times',
        'total_leave_days',
        'compare_summary',
    ];

    

    // - **division_code**: รหัสสายงาน เช่น CEO, CAO, CMO, CIO, CPO
    // - **report_month**: เดือน-ปี ที่รายงาน เช่น 2025-07
    // - **total_employees**: จำนวนพนักงานทั้งหมดในสายงานนั้น
    // - **working_days**: จำนวนวันทำงานในเดือนนั้น
    // - **total_working_days**: จำนวนวันทำงานทั้งหมด (รวมทุกพนักงาน)
    // - **sick_times**: จำนวนครั้งที่ลาป่วย
    // - **sick_days**: จำนวนวันลาป่วย
    // - **personal_times**: จำนวนครั้งลากิจ
    // - **personal_days**: จำนวนวันลากิจ
    // - **annual_times**: จำนวนครั้งลาพักร้อน
    // - **annual_days**: จำนวนวันลาพักร้อน
    // - **maternity_times**: จำนวนครั้งลาคลอด
    // - **maternity_days**: จำนวนวันลาคลอด
    // - **other_times**: จำนวนครั้งลาอื่นๆ
    // - **other_days**: จำนวนวันลาอื่นๆ
    // - **total_leave_times**: จำนวนครั้งการลาทั้งหมด
    // - **total_leave_days**: จำนวนวันลาทั้งหมด
    // - **compare_summary**: ข้อมูลสรุปเปรียบเทียบกับเดือนที่ผ่านมา
}