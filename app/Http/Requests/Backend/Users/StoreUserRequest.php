<?php

namespace App\Http\Requests\Backend\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->canManageUsers();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'employee_code' => [
                'required',
                'max:50',
                Rule::unique('userkml2025.employees', 'emp_code'),
            ],
            'sex'           => 'required|string|max:20',
            'prefix'        => 'required|string|max:50',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'position'      => 'nullable|string|max:255',
            'employee_type' => 'nullable|string|max:255',
            'workplace'     => 'nullable|string|max:255',
            'department_id' => 'nullable|integer|exists:department,department_id',
            'division_id'   => 'nullable|integer|exists:divisions,division_id',
            'section_id'    => 'nullable|integer|exists:sections,section_id',
            'level_user'    => 'required',
            'hr_status'     => 'required',
            'role'          => [
                'nullable',
                'string',
                'in:admin,editor,viewer',
                function ($attribute, $value, $fail) {
                    if ($value === 'admin' && !auth()->user()->canAssignAdminRole()) {
                        $fail('เฉพาะผู้ดูแลระบบสังกัดฝ่าย 16 Information Communication Technology เท่านั้นที่สามารถกำหนดสิทธิ์เป็น ADMIN ได้');
                    }
                },
            ],
            'startwork_date' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'employee_code.required' => 'กรุณาระบุรหัสพนักงาน',
            'employee_code.unique'   => 'รหัสพนักงานนี้มีอยู่ในระบบแล้ว',
            'first_name.required'    => 'กรุณาระบุชื่อจริง',
            'last_name.required'     => 'กรุณาระบุนามสกุล',
            'sex.required'           => 'กรุณาเลือกเพศ',
            'prefix.required'        => 'กรุณาระบุคำนำหน้า',
            'level_user.required'    => 'กรุณาเลือกระดับพนักงาน',
            'hr_status.required'     => 'กรุณาเลือกสถานะ HR',
        ];
    }
}
