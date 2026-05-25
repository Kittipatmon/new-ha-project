<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_code' => $this->employee_code,
            'prefix' => $this->prefix,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'fullname' => $this->fullname, // accessor on model
            'sex' => $this->sex,
            'position' => $this->position,
            'employee_type' => $this->employee_type,
            'workplace' => $this->workplace,
            'section_id' => $this->section_id,
            'division_id' => $this->division_id,
            'department_id' => $this->department_id,
            'level_user' => $this->level_user,
            'level_user_label' => $this->level_user_label,
            'level_user_color' => $this->level_user_color,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'status_color' => $this->status_color,
            'hr_status' => $this->hr_status,
            'hr_status_label' => $this->hr_status_label,
            'hr_status_color' => $this->hr_status_color,
            'photo_user' => $this->photo_user,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),

            // Optional minimal relations
            'department' => $this->whenLoaded('department', function(){
                return [
                    'id' => $this->department->department_id,
                    'department_id' => $this->department->department_id,
                    'department_name' => $this->department->department_name,
                    // backward-compat
                    'name' => $this->department->department_name,
                ];
            }),
            'division' => $this->whenLoaded('division', function(){
                return [
                    'id' => $this->division->division_id,
                    'division_id' => $this->division->division_id,
                    'division_name' => $this->division->division_name,
                    // backward-compat
                    'name' => $this->division->division_name,
                ];
            }),
            'section' => $this->whenLoaded('section', function(){
                return [
                    'id' => $this->section->section_id,
                    'section_id' => $this->section->section_id,
                    'section_code' => $this->section->section_code,
                    'section_name' => $this->section->section_name,
                    // backward-compat
                    'code' => $this->section->section_code,
                    'name' => $this->section->section_name,
                ];
            }),
        ];
    }

    /**
     * Add a success flag to the top-level JSON when not paginating.
     */
    public function with(Request $request): array
    {
        return ['success' => true];
    }
}
