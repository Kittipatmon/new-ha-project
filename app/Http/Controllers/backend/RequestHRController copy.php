<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\hrrequest\RequestCategories;
use App\Models\hrrequest\RequestType;
use App\Models\hrrequest\RequestSubtypes;
use App\Models\hrrequest\HrRequests;
use App\Models\hrrequest\Request_Safety_Docs;
use App\Models\hrrequest\Request_Safety_Items;
use App\Models\hrrequest\Request_Time_Edits;
use App\Models\hrrequest\Request_Uniforms;
use App\Models\hrrequest\RequestWelfare;
use App\Models\hrrequest\RequestCertificates;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Department;
use App\Models\Division;
use App\Models\Section;

class RequestHRController extends Controller
{
    public function welcomeRequest()
    {
        $breadcrumbs = [
            // ['label' => 'หน้าหลัก', 'url' => route('dashboard')],
            ['label' => 'งานบริการ', 'url' => route('welcome.system')],
            ['label' => 'Request HR', 'url' => null],
        ];

        $hrrequestsCount = HrRequests::where('employee_id', Auth::id())
        ->count();
        $hrrequests = HrRequests::where('employee_id', Auth::id())
            ->whereIn('status', [
                HrRequests::STATUS_PENDING, 
                HrRequests::STATUS_APPROVED_MANAGER, 
                HrRequests::STATUS_APPROVED_HR, 
                HrRequests::STATUS_RETURNED
            ])
            ->count();

        return view('requesthr.welcomerequest', compact('breadcrumbs', 'hrrequestsCount' , 'hrrequests'));
    }

    public function requestHR()
    {
        $Requestcategories = RequestCategories::all();
        $Requesttypes = RequestType::all();
        $Requestsubtypes = RequestSubtypes::all();

        return view('requesthr.requesthr', compact('Requestcategories', 'Requesttypes', 'Requestsubtypes'));
    }


    public function requestStore(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'type_id' => 'required',
            'subtype_id' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            // Generate Request Code
            $date = now();
            $prefix = 'HR-' . $date->format('ymd') . '-';
            $lastRequest = HrRequests::where('request_code', 'like', $prefix . '%')
                ->orderBy('request_code', 'desc')
                ->first();
            
            $nextNumber = 1;
            if ($lastRequest) {
                $lastNumber = (int) substr($lastRequest->request_code, -4);
                $nextNumber = $lastNumber + 1;
            }
            $requestCode = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Create Main Request
            $hrRequest = new HrRequests();
            $hrRequest->request_code = $requestCode;
            $hrRequest->employee_id = Auth::user()->id; 
            $hrRequest->category_id = $request->category_id;
            $hrRequest->type_id = $request->type_id;
            $hrRequest->subtype_id = $request->subtype_id;
            $hrRequest->status = HrRequests::STATUS_PENDING;
            $hrRequest->submitted_at = now();
            
            // Determine Title based on type
            $type = RequestType::find($request->type_id);
            $hrRequest->title = $type ? $type->name_th : 'คำร้องทั่วไป';
            
            $hrRequest->save();

            // 1. Time Edit
            if ($request->has('edit_reason') && $request->filled('edit_reason')) {
                $timeEdit = new Request_Time_Edits();
                $timeEdit->request_id = $hrRequest->hr_request_id;
                $timeEdit->edit_reason = $request->edit_reason;
                $timeEdit->edit_start_date = $request->edit_start_date;
                $timeEdit->edit_end_date = $request->edit_end_date;
                $timeEdit->edit_start_time = $request->edit_start_time;
                $timeEdit->edit_end_time = $request->edit_end_time;
                
                if ($request->hasFile('timefile')) {
                    $path = $request->file('timefile')->store('public/hr_requests/time_edits');
                    $timeEdit->timefile = str_replace('public/', '', $path);
                }
                
                $timeEdit->created_at = now();
                $timeEdit->save();
            }

            // 2. Uniform
            if ($request->has('uniform_gender') && $request->filled('uniform_gender')) {
                $uniform = new Request_Uniforms();
                $uniform->request_id = $hrRequest->hr_request_id;
                $uniform->uniform_gender = $request->uniform_gender;
                $uniform->uniform_size = $request->uniform_size;
                $uniform->uniform_reason = $request->uniform_reason;
                $uniform->created_at = now();
                $uniform->save();
            }

            // 3. Safety Items
            if ($request->has('safety_item_name')) {
                $names = $request->safety_item_name;
                $quantities = $request->safety_item_quantity;
                
                foreach ($names as $index => $name) {
                    if (!empty($name)) {
                        $safetyItem = new Request_Safety_Items();
                        $safetyItem->request_id = $hrRequest->hr_request_id;
                        $safetyItem->item_name = $name;
                        $safetyItem->quantity = $quantities[$index] ?? 1;
                        $safetyItem->created_at = now();
                        $safetyItem->save();
                    }
                }
            }

            // 4. Safety Docs
            if ($request->has('safety_reason') && $request->filled('safety_reason')) {
                 $safetyDoc = new Request_Safety_Docs();
                 $safetyDoc->request_id = $hrRequest->hr_request_id;
                 $safetyDoc->safety_reason = $request->safety_reason;
                 $safetyDoc->created_at = now();
                 $safetyDoc->save();
            }

            // 5. Certificate
            if ($request->has('certificate_reason') && $request->filled('certificate_reason')) {
                $cert = new RequestCertificates();
                $cert->request_id = $hrRequest->hr_request_id;
                $cert->certificate_reason = $request->certificate_reason;
                $cert->created_at = now();
                $cert->save();
            }

            // 6. Welfare
            if ($request->has('welfare_reason') && $request->filled('welfare_reason')) {
                $welfare = new RequestWelfare();
                $welfare->request_id = $hrRequest->hr_request_id;
                $welfare->welfare_reason = $request->welfare_reason;
                $welfare->created_at = now();
                $welfare->save();
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()], 500);
        }
    }




    public function requesthrList()
    {
        $user = Auth::user();
        $hrrequests = collect();
        if ($user?->id) {
            $hrrequests = HrRequests::where('employee_id', $user->id)
                ->whereIn('status', [
                    HrRequests::STATUS_PENDING, 
                    HrRequests::STATUS_APPROVED_MANAGER, 
                    HrRequests::STATUS_APPROVED_HR, 
                    HrRequests::STATUS_RETURNED
                ])
                ->get();
        }
        return view('requesthr.requesthrlist', compact('hrrequests'));
    }

    public function requesthrlistall()
    {
        $user = Auth::user();
        $hrrequests = HrRequests::where('employee_id', $user->id)->get();
        return view('requesthr.requesthrlistall', compact('hrrequests'));
    }

    

    public function detailUser($id)
    {
        $hrrequest = HrRequests::findOrFail($id);

        return view('requesthr.detail.detailuser', compact('hrrequest'));
    }
}
