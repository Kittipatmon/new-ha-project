<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\JobPost;
use App\Models\Recruitment\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationReceived;
use App\Mail\NewApplicationHaNotification;
use App\Services\RecruitmentMailService;
use App\Models\Recruitment\RecruitmentRequest;
use App\Models\User;
use App\Models\datacenter\Poster;
use App\Models\Recruitment\JobPostView;
use Illuminate\Support\Facades\Auth;

class RecruitmentController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::with(['department', 'jobPosition'])
            ->where('publish_status', 'published')
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });

        // Filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('position_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('type')) {
            $query->where('employment_type', $request->type);
        }

        // Sorting
        $sort = $request->get('sort', 'relevant');
        switch ($sort) {
            case 'latest':
                $query->orderBy('published_at', 'desc')->orderBy('id', 'desc');
                break;
            case 'company':
                $query->orderBy('position_name', 'asc');
                break;
            case 'salary_max':
                $query->orderByRaw('COALESCE(salary_max, salary_min, 0) DESC');
                break;
            case 'salary_min':
                $query->orderByRaw('COALESCE(salary_min, salary_max, 99999999) ASC');
                break;
            case 'relevant':
            default:
                $query->orderBy('published_at', 'desc')->orderBy('id', 'desc');
                break;
        }

        $posts = $query->paginate(12)->withQueryString();

        $activeDepartmentIds = JobPost::where('publish_status', 'published')
            ->distinct()
            ->pluck('department_id');

        $departments = Department::whereIn('department_id', $activeDepartmentIds)->get();

        // Top horizontal carousel positions / featured jobs
        $featuredPosts = JobPost::with('department')
            ->where('publish_status', 'published')
            ->orderBy('published_at', 'desc')
            ->take(8)
            ->get();

        // Popular job categories (departments with real counts)
        $popularCategories = Department::whereIn('department_id', $activeDepartmentIds)
            ->withCount(['jobPosts' => function ($q) {
                $q->where('publish_status', 'published');
            }])
            ->having('job_posts_count', '>', 0)
            ->orderBy('job_posts_count', 'desc')
            ->take(5)
            ->get();

        // If fewer than 5 active departments with jobs, list other departments with real 0 count
        if ($popularCategories->count() < 5) {
            $extraDepts = Department::whereNotIn('department_id', $popularCategories->pluck('department_id'))
                ->take(5 - $popularCategories->count())
                ->get()
                ->map(function ($d) {
                    $d->job_posts_count = 0;
                    return $d;
                });
            $popularCategories = $popularCategories->concat($extraDepts);
        }

        // Popular search keywords strictly based on Job Positions (ตำแหน่งงาน)
        $publishedPositions = JobPost::where('publish_status', 'published')
            ->whereNotNull('position_name')
            ->where('position_name', '!=', '')
            ->distinct()
            ->pluck('position_name')
            ->toArray();

        $allDbPositions = JobPost::whereNotNull('position_name')
            ->where('position_name', '!=', '')
            ->distinct()
            ->pluck('position_name')
            ->toArray();

        $requestPositions = \Illuminate\Support\Facades\DB::table('recruitment_requests')
            ->whereNotNull('position_name')
            ->where('position_name', '!=', '')
            ->distinct()
            ->pluck('position_name')
            ->toArray();

        // Standard / Common Job Positions in company & industry
        $commonJobPositions = [
            'วิศวกรไฟฟ้า',
            'วิศวกรเครื่องกล',
            'ช่างเทคนิค',
            'ช่างซ่อมคอม',
            'โปรแกรมเมอร์',
            'Developer',
            'เจ้าหน้าที่การตลาด',
            'เจ้าหน้าที่บุคคล (HR)',
            'เจ้าหน้าที่บัญชีและการเงิน',
            'เจ้าหน้าที่จัดซื้อ',
            'เจ้าหน้าที่ความปลอดภัย (จป.)',
            'เจ้าหน้าที่ธุรการ/ประสานงาน',
        ];

        // Put published positions first so clicking them always finds jobs, followed by other real positions
        $popularKeywords = array_values(array_unique(array_filter(array_merge($publishedPositions, $allDbPositions, $requestPositions, $commonJobPositions))));

        // Get Promotional Posters strictly for Recruitment page (with scheduling check)
        $promoPosters = Poster::published()
            ->where('position', 'recruitment')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('frontend.recruitment.index', compact('posts', 'departments', 'promoPosters', 'featuredPosts', 'popularCategories', 'sort', 'popularKeywords'));
    }

    public function show($slug)
    {
        $post = JobPost::with(['department', 'jobPosition'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Related / other job posts by company
        $otherPosts = JobPost::with('department')
            ->where('publish_status', 'published')
            ->where('id', '!=', $post->id)
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        // Increment view count strictly when clicking on a job card (request('from_card'))
        // Excludes page reloads / direct refreshes entirely
        if (request()->has('from_card')) {
            try {
                if (\Illuminate\Support\Facades\Schema::hasColumn('recruitment_job_posts', 'views')) {
                    $post->increment('views');
                }

                // Log view event for analytics
                if (\Illuminate\Support\Facades\Schema::hasTable('recruitment_job_post_views')) {
                    JobPostView::create([
                        'job_post_id' => $post->id,
                        'event_type' => 'view',
                        'user_id' => auth()->id(),
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'view_date' => now()->toDateString(),
                    ]);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Job post view tracking failed: ' . $e->getMessage());
            }
        }

        $applicationCount = $post->applications()->count();

        return view('frontend.recruitment.show', compact('post', 'otherPosts', 'applicationCount'));
    }

    public function apply($slug)
    {
        $post = JobPost::where('slug', $slug)->firstOrFail();

        $isExpired = $post->end_date && $post->end_date->endOfDay()->isPast();
        if ($post->publish_status === 'closed' || $isExpired) {
            return redirect()->route('recruitment.show', $slug)
                ->with('closed_alert', 'ขออภัย ตำแหน่งงานนี้ถูกยกเลิกหรือปิดรับสมัครแล้ว ไม่สามารถส่งใบสมัครได้');
        }

        // Track click event (applying = intent to apply = click) safely
        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('recruitment_job_posts', 'clicks')) {
                $post->increment('clicks');
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('recruitment_job_post_views')) {
                JobPostView::create([
                    'job_post_id' => $post->id,
                    'event_type' => 'click',
                    'user_id' => auth()->id(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'view_date' => now()->toDateString(),
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Job post click tracking failed: ' . $e->getMessage());
        }

        return view('frontend.recruitment.apply', compact('post'));
    }

    public function submitApplication(Request $request, $slug)
    {
        $post = JobPost::where('slug', $slug)->firstOrFail();

        $isExpired = $post->end_date && $post->end_date->endOfDay()->isPast();
        if ($post->publish_status === 'closed' || $isExpired) {
            return redirect()->route('recruitment.show', $slug)
                ->with('closed_alert', 'ขออภัย ตำแหน่งงานนี้ถูกยกเลิกหรือปิดรับสมัครแล้ว ไม่สามารถส่งใบสมัครได้');
        }

        $validated = $request->validate([
            // Page 1: Personal Info
            'prefix' => 'nullable|string|max:20',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'nickname' => 'nullable|string|max:50',
            'position_applied' => 'nullable|string|max:255',
            'gender' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'age' => 'nullable|numeric',
            'place_of_birth' => 'nullable|string|max:100',
            'race' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'religion' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:30',
            'id_card_issued_by' => 'nullable|string|max:100',
            'id_card_issued_province' => 'nullable|string|max:100',
            'id_card_issued_date' => 'nullable|date',
            'id_card_expiry_date' => 'nullable|date',
            'height_cm' => 'nullable|numeric',
            'weight_kg' => 'nullable|numeric',
            'military_status' => 'nullable|string|max:50',
            'marital_status' => 'nullable|string|max:50',

            // Contact & Address
            'house_no' => 'nullable|string|max:50',
            'moo' => 'nullable|string|max:50',
            'road' => 'nullable|string|max:100',
            'subdistrict' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'phone' => 'required|string|max:30',
            'tel' => 'nullable|string|max:30',
            'line_id' => 'nullable|string|max:50',
            'facebook' => 'nullable|string|max:100',
            'email' => 'required|email|max:100',
            'housing_type' => 'nullable|string|max:50',
            'address' => 'nullable|string',

            // Page 2: Family & Emergency Contact
            'family_info' => 'nullable|array',
            'emergency_contact' => 'nullable|array',

            // Page 3: Education & Experience
            'education' => 'nullable|array',
            'education.*.level' => 'nullable|string',
            'education.*.institution_name' => 'nullable|string',
            'education.*.faculty' => 'nullable|string',
            'education.*.major' => 'nullable|string',
            'education.*.start_year' => 'nullable|string',
            'education.*.end_year' => 'nullable|string',
            'education.*.gpa' => 'nullable|numeric|between:0,4.00',

            'experience' => 'nullable|array',
            'experience.*.company_name' => 'nullable|string',
            'experience.*.position' => 'nullable|string',
            'experience.*.start_date' => 'nullable|date',
            'experience.*.end_date' => 'nullable|date',
            'experience.*.job_detail' => 'nullable|string',
            'experience.*.salary' => 'nullable|numeric',
            'experience.*.reason_for_leaving' => 'nullable|string',

            'experience_summary' => 'nullable|string',
            'language_skills' => 'nullable|array',

            // Page 4: Skills, Special Abilities & General Questions
            'computer_skills' => 'nullable|string',
            'special_abilities' => 'nullable|array',
            'application_questions' => 'nullable|array',
            'expected_salary' => 'nullable|numeric',

            // Page 5: References & Questionnaire & Docs Check
            'references_info' => 'nullable|array',
            'health_criminal_questionnaire' => 'nullable|array',
            'attached_documents_check' => 'nullable|array',
            'applicant_signature' => 'nullable|string|max:255',
            'm_applicant_signature' => 'nullable|string|max:255',

            // Files & PDPA
            'resume' => 'nullable',
            'resume.*' => 'file|mimes:pdf,doc,docx,jpeg,jpg,png,webp|max:10240',
            'm_resume' => 'nullable',
            'm_resume.*' => 'file|mimes:pdf,doc,docx,jpeg,jpg,png,webp|max:10240',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,webp|max:5120',
            'portfolio' => 'nullable',
            'portfolio.*' => 'file|mimes:pdf,doc,docx,jpeg,jpg,png,webp|max:10240',
            'pdpa_consent' => 'accepted',
        ], [
            'required' => 'กรุณากรอกข้อมูล :attribute',
            'email' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'numeric' => ':attribute ต้องเป็นตัวเลขเท่านั้น',
            'between' => ':attribute ต้องอยู่ระหว่าง :min ถึง :max',
            'max' => ':attribute ต้องไม่เกิน :max ตัวอักษร/KB',
            'mimes' => ':attribute ต้องเป็นไฟล์ประเภท :values เท่านั้น',
            'accepted' => 'กรุณายอมรับเงื่อนไขการคุ้มครองข้อมูลส่วนบุคคล (PDPA)',
        ], [
            'first_name' => 'ชื่อ',
            'last_name' => 'นามสกุล',
            'email' => 'อีเมล',
            'phone' => 'เบอร์โทรศัพท์มือถือ',
            'resume' => 'ไฟล์ Resume',
        ]);

        // 1. Create or Update Applicant
        $applicant = null;
        if (!empty($validated['national_id'])) {
            $applicant = \App\Models\Recruitment\Applicant::where('national_id', $validated['national_id'])->first();
        }

        if (!$applicant) {
            // Check for application limit (max 3 per day per email)
            $todayApplicationsCount = \App\Models\Recruitment\Application::whereHas('applicant', function ($q) use ($validated) {
                $q->where('email', $validated['email']);
            })->whereDate('applied_at', now()->toDateString())->count();

            if ($todayApplicationsCount >= 3) {
                return back()->withInput()->withErrors(['email' => 'คุณส่งใบสมัครเกินโควตา 3 ครั้งต่อวันสำหรับอีเมลนี้แล้ว กรุณาลองใหม่ในวันพรุ่งนี้ (Email limit exceeded: max 3/day)']);
            }

            $applicant = \App\Models\Recruitment\Applicant::where('email', $validated['email'])
                ->first();
        }

        // Build composite address if separate fields provided
        $fullAddress = $validated['address'] ?? '';
        if (empty($fullAddress)) {
            $addrParts = [];
            if (!empty($validated['house_no'])) $addrParts[] = 'เลขที่ ' . $validated['house_no'];
            if (!empty($validated['moo'])) $addrParts[] = 'หมู่ ' . $validated['moo'];
            if (!empty($validated['road'])) $addrParts[] = 'ถ.' . $validated['road'];
            if (!empty($validated['subdistrict'])) $addrParts[] = 'ต./แขวง ' . $validated['subdistrict'];
            if (!empty($validated['district'])) $addrParts[] = 'อ./เขต ' . $validated['district'];
            if (!empty($validated['province'])) $addrParts[] = 'จ.' . $validated['province'];
            if (!empty($validated['postcode'])) $addrParts[] = $validated['postcode'];
            $fullAddress = implode(' ', $addrParts);
        }

        $familyInfo = $validated['family_info'] ?? null;
        if (is_array($familyInfo)) {
            // หากเลือก "โสด" ให้ตัดข้อมูลคู่สมรสออก (ไม่ต้องจัดเก็บ)
            if (($validated['marital_status'] ?? '') === 'โสด') {
                unset($familyInfo['spouse']);
            }
            // ข้อมูลบุตร: หากไม่มีบุตรหรือเว้นว่าง ให้กำหนดเป็น 0 หรือตามจริง
            if (!isset($familyInfo['children_count']) || $familyInfo['children_count'] === '' || $familyInfo['children_count'] === null) {
                $familyInfo['children_count'] = 0;
            }
        }

        $applicantData = [
            'email' => $validated['email'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'nickname' => $validated['nickname'] ?? null,
            'prefix' => $validated['prefix'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'age' => $validated['age'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'race' => $validated['race'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'national_id' => $validated['national_id'] ?? null,
            'id_card_issued_by' => $validated['id_card_issued_by'] ?? null,
            'id_card_issued_province' => $validated['id_card_issued_province'] ?? null,
            'id_card_issued_date' => $validated['id_card_issued_date'] ?? null,
            'id_card_expiry_date' => $validated['id_card_expiry_date'] ?? null,
            'height_cm' => $validated['height_cm'] ?? null,
            'weight_kg' => $validated['weight_kg'] ?? null,
            'military_status' => $validated['military_status'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'phone' => $validated['phone'],
            'tel' => $validated['tel'] ?? null,
            'line_id' => $validated['line_id'] ?? null,
            'facebook' => $validated['facebook'] ?? null,
            'housing_type' => $validated['housing_type'] ?? null,
            'address' => $fullAddress,
            'house_no' => $validated['house_no'] ?? null,
            'moo' => $validated['moo'] ?? null,
            'road' => $validated['road'] ?? null,
            'subdistrict' => $validated['subdistrict'] ?? null,
            'district' => $validated['district'] ?? null,
            'province' => $validated['province'] ?? null,
            'postcode' => $validated['postcode'] ?? null,
            'family_info' => $familyInfo,
            'emergency_contact' => $validated['emergency_contact'] ?? null,
            'language_skills' => (function() use ($request, $validated) {
                $raw = $validated['language_skills'] ?? $request->input('language_skills', []);
                if (!is_array($raw)) {
                    $raw = [];
                }
                $levels = ['Good', 'Fair', 'Poor'];
                $result = [];
                $keys = ['thai', 'english', 'other1', 'other2'];
                
                foreach ($keys as $k) {
                    $row = $raw[$k] ?? [];
                    if (!is_array($row)) {
                        $row = [];
                    }
                    
                    $hasData = !empty($row['speaking']) || !empty($row['writing']) || !empty($row['reading']) || !empty($row['name']);
                    
                    if ($k === 'thai' || $k === 'english' || $hasData) {
                        $speaking = in_array($row['speaking'] ?? '', $levels) ? $row['speaking'] : ($k === 'thai' ? 'Good' : 'Fair');
                        $writing = in_array($row['writing'] ?? '', $levels) ? $row['writing'] : $speaking;
                        $reading = in_array($row['reading'] ?? '', $levels) ? $row['reading'] : $speaking;
                        
                        $item = [
                            'speaking' => $speaking,
                            'writing' => $writing,
                            'reading' => $reading,
                        ];
                        if (!empty($row['name'])) {
                            $item['name'] = trim((string)$row['name']);
                        }
                        $result[$k] = $item;
                    }
                }
                return !empty($result) ? $result : null;
            })(),
            'experience_summary' => $validated['experience_summary'] ?? null,
            'computer_skills' => $validated['computer_skills'] ?? null,
            'special_abilities' => $validated['special_abilities'] ?? null,
            'application_questions' => $validated['application_questions'] ?? null,
            'references_info' => (function() use ($validated, $request) {
                $ref = $validated['references_info'] ?? [];
                $sig = $request->input('applicant_signature') ?: $request->input('m_applicant_signature');
                if ($sig) {
                    $ref['applicant_signature'] = $sig;
                }
                return $ref;
            })(),
            'health_criminal_questionnaire' => $validated['health_criminal_questionnaire'] ?? null,
            'attached_documents_check' => $validated['attached_documents_check'] ?? null,
            'expected_salary' => $validated['expected_salary'] ?? null,
            'status' => 'active',
        ];

        // Sync main applicant table with latest education/experience if available
        if (!empty($validated['education']) && is_array($validated['education'])) {
            foreach ($validated['education'] as $edu) {
                if (!empty($edu['level']) || !empty($edu['institution_name'])) {
                    $applicantData['education_level'] = $edu['level'] ?? null;
                    $applicantData['university_name'] = $edu['institution_name'] ?? null;
                    $applicantData['faculty'] = $edu['faculty'] ?? null;
                    $applicantData['major'] = $edu['major'] ?? null;
                    $applicantData['gpa'] = $edu['gpa'] ?? null;
                    break;
                }
            }
        }

        if (!empty($validated['experience']) && is_array($validated['experience'])) {
            foreach ($validated['experience'] as $exp) {
                if (!empty($exp['company_name']) || !empty($exp['position'])) {
                    $applicantData['current_company'] = $exp['company_name'] ?? null;
                    $applicantData['current_position'] = $exp['position'] ?? null;
                    if (empty($applicantData['expected_salary']) && !empty($exp['salary'])) {
                        $applicantData['expected_salary'] = $exp['salary'];
                    }
                    break;
                }
            }
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            if ($applicant) {
                $applicant->update($applicantData);
            } else {
                $applicant = \App\Models\Recruitment\Applicant::create($applicantData);
            }

            // 3. Create Application
            $application = \App\Models\Recruitment\Application::create([
                'job_post_id' => $post->id,
                'applicant_id' => $applicant->id,
                'application_no' => 'APP-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'status' => 'new',
                'applied_at' => now(),
                'remarks' => 'PDPA Consent Accepted on ' . now()->format('Y-m-d H:i:s') . ' [IP: ' . $request->ip() . ']',
            ]);

            // 4. Handle Education History (skip completely empty rows)
            if (!empty($validated['education']) && is_array($validated['education'])) {
                foreach ($validated['education'] as $edu) {
                    if (!is_array($edu)) continue;
                    $level = trim($edu['level'] ?? '');
                    $institution = trim($edu['institution_name'] ?? '');
                    if ($level === '' && $institution === '') {
                        continue;
                    }
                    if ($level === '') $level = 'อื่นๆ';
                    if ($institution === '') $institution = '-';

                    $edu['level'] = $level;
                    $edu['institution_name'] = $institution;

                    \App\Models\Recruitment\ApplicationEducation::create(array_merge($edu, [
                        'applicant_id' => $applicant->id,
                        'application_id' => $application->id
                    ]));
                }
            }

            // 5. Handle Experience History (skip completely empty rows)
            if (!empty($validated['experience']) && is_array($validated['experience'])) {
                foreach ($validated['experience'] as $exp) {
                    if (!is_array($exp)) continue;
                    $company = trim($exp['company_name'] ?? '');
                    $pos = trim($exp['position'] ?? '');
                    if ($company === '' && $pos === '') {
                        continue;
                    }
                    if ($company === '') $company = '-';
                    if ($pos === '') $pos = '-';

                    $exp['company_name'] = $company;
                    $exp['position'] = $pos;

                    \App\Models\Recruitment\ApplicationExperience::create(array_merge($exp, [
                        'applicant_id' => $applicant->id,
                        'application_id' => $application->id
                    ]));
                }
            }

            // 6. Handle File Uploads (Secure Private Storage)
            $fileTypes = [
                'resume' => 'resume',
                'm_resume' => 'resume',
                'photo' => 'photo',
                'portfolio' => 'portfolio',
            ];
            $targetDir = storage_path('app/recruitment_documents');

            // Ensure directory exists
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0775, true);
            }

            foreach ($fileTypes as $field => $docType) {
                if ($request->hasFile($field)) {
                    $uploaded = $request->file($field);
                    $files = is_array($uploaded) ? $uploaded : [$uploaded];

                    foreach ($files as $idx => $file) {
                        if (!$file || !$file->isValid()) continue;

                        $originalName = $file->getClientOriginalName();
                        $fileSize = $file->getSize();
                        $filename = time() . '_' . $idx . '_' . $docType . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);

                        // Move file to public directory
                        $file->move($targetDir, $filename);
                        $path = $filename; // Store just the filename

                        \App\Models\Recruitment\ApplicantDocument::create([
                            'application_id' => $application->id,
                            'document_type' => $docType,
                            'file_name' => $originalName,
                            'file_path' => $path,
                            'file_size' => $fileSize,
                        ]);

                        // Update applicant model for legacy/shortcut access (first file only)
                        if ($docType === 'resume' && empty($applicant->resume_file)) {
                            $applicant->update(['resume_file' => $path]);
                        }
                        if ($docType === 'photo' && empty($applicant->photo_file)) {
                            $applicant->update(['photo_file' => $path]);
                        }
                        if ($docType === 'portfolio' && empty($applicant->portfolio_file)) {
                            $applicant->update(['portfolio_file' => $path]);
                        }
                    }
                }
            }

            // 7. Log Status
            \App\Models\Recruitment\StatusLog::create([
                'application_id' => $application->id,
                'old_status' => null,
                'new_status' => 'new',
                'changed_by' => 0,
                'remark' => 'ส่งใบสมัครเรียบร้อยแล้ว (Multi-step Form)',
            ]);

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Application submission failed: ' . $e->getMessage());
            return back()->withInput()->withErrors(['email' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()]);
        }

        // ส่งอีเมลยืนยันการรับสมัคร (ใช้ Queue/Background dispatchAfterResponse เพื่อให้หน้าจอไม่ค้างและตอบสนองทันที)
        if ($applicant->email) {
            try {
                $mailable = new ApplicationReceived($application);
                \App\Services\RecruitmentMailService::queueMailable(
                    $mailable,
                    $applicant->email,
                    trim(($applicant->prefix ?? '') . ' ' . $applicant->first_name . ' ' . $applicant->last_name),
                    'application_received',
                    [
                        'application_id' => $application->id,
                        'position_name' => $post->position_name ?? ($post->jobPosition?->position_name ?? $post->title),
                    ]
                );
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to queue application confirmation email: ' . $e->getMessage());
            }
        }

        // ส่งอีเมลแจ้งเตือนฝ่ายทรัพยากรบุคคล (HA) เมื่อมีผู้สมัครใหม่เข้ามา
        try {
            $haEmail = config('recruitment.ha_notification_email', 'Kittipat.Ma@kumwell.com');
            if (!empty($haEmail)) {
                $haMailable = new NewApplicationHaNotification($application);
                RecruitmentMailService::queueMailable(
                    $haMailable,
                    $haEmail,
                    'ฝ่ายทรัพยากรบุคคล (HA)',
                    'new_application_ha_notification',
                    [
                        'application_id' => $application->id,
                        'application_no' => $application->application_no,
                        'position_name' => $post->position_name ?? ($post->jobPosition?->position_name ?? $post->title),
                        'applicant_name' => trim(($applicant->prefix ?? '') . ' ' . $applicant->first_name . ' ' . $applicant->last_name),
                        'recipient_role' => 'HA',
                    ]
                );
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to queue HA notification email for new application: ' . $e->getMessage());
        }

        return redirect()->route('recruitment.success', $post->slug)
            ->with('success', 'ส่งใบสมัครงานเรียบร้อยแล้ว!')
            ->with('application_no', $application->application_no);
    }

    public function success($slug)
    {
        $post = JobPost::where('slug', $slug)->firstOrFail();
        return view('frontend.recruitment.success', compact('post'));
    }

    public function requestReport(Request $request)
    {
        $user = Auth::user();
        
        // -------------------------------------------------------------
        // 1. ผู้สมัครที่ HA ส่งมา (Candidates forwarded by HA)
        // แสดงเฉพาะผู้สมัครที่ทีม HA คัดกรองและส่งต่อมาพิจารณาแล้วเท่านั้น
        // ไม่รวมผู้สมัครที่เพิ่งส่งใบสมัครเข้ามา (new, pending) หรือไม่ผ่านคุณสมบัติตั้งแต่ HA (screening_failed)
        // -------------------------------------------------------------
        $candidateQuery = \App\Models\Recruitment\Application::with([
            'applicant.education',
            'applicant.experience',
            'education',
            'experience',
            'jobPost.department',
            'jobPost.jobPosition',
            'screener',
            'deptReviewer',
            'documents',
            'interviews.interviewers',
            'interviews.scores',
            'statusLogs.user',
        ])
        ->whereNotIn('status', ['new', 'pending', 'screening_failed'])
        ->orderBy('created_at', 'desc');

        // ตรวจสอบสิทธิ์: ผู้ที่จะเข้าหน้านี้ได้ต้องเป็นฝ่าย HA หรือ หัวหน้าแผนก (manager_id ในตาราง departments) เท่านั้น
        // คนทั่วไปในแผนกจะไม่สามารถเข้าดูได้
        if (!$user || !$user->canViewDeptCandidates()) {
            abort(403, 'เฉพาะหัวหน้าแผนก (Manager) หรือฝ่าย HA เท่านั้นที่สามารถดูข้อมูลผู้สมัครที่ HA ส่งมาได้');
        }

        // Non-HR users (หัวหน้าแผนก): เห็นเฉพาะผู้สมัครในตำแหน่งของแผนกที่ตนเองเป็น manager_id
        if (!$user->isCentralHr()) {
            $managedDeptIds = $user->getManagedDepartmentIds();
            $candidateQuery->whereHas('jobPost', function($jq) use ($managedDeptIds) {
                $jq->whereIn('department_id', $managedDeptIds);
            });
        }

        // Status counts for candidate filter badges
        $baseCountQuery = clone $candidateQuery;
        $candidateStats = [
            'total' => (clone $baseCountQuery)->count(),
            'dept_review' => (clone $baseCountQuery)->where('status', 'dept_review')->count(),
            'interview' => (clone $baseCountQuery)->whereIn('status', ['interview', 'interview_scheduled', 'interview_completed'])->count(),
            'passed' => (clone $baseCountQuery)->whereIn('status', ['passed_selection', 'selection_approved', 'offered', 'hired'])->count(),
            'rejected' => (clone $baseCountQuery)->whereIn('status', ['dept_rejected', 'interview_failed', 'rejected'])->count(),
        ];

        // Search Filter
        if ($request->filled('search')) {
            $search = $request->search;
            $candidateQuery->where(function($q) use ($search) {
                $q->where('application_no', 'like', "%{$search}%")
                  ->orWhereHas('applicant', function($aq) use ($search) {
                      $aq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('jobPost', function($jq) use ($search) {
                      $jq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        // Job Post filter
        if ($request->filled('job_post_id')) {
            $candidateQuery->where('job_post_id', $request->job_post_id);
        }

        // Status filter: ค่าเริ่มต้นคือ 'all' (หรือตามที่ระบุใน request)
        $candidateStatus = $request->get('status', 'all');
        if ($candidateStatus !== 'all' && $request->filled('status')) {
            if ($candidateStatus === 'dept_review') {
                $candidateQuery->where('status', 'dept_review');
            } elseif ($candidateStatus === 'interview') {
                $candidateQuery->whereIn('status', ['interview', 'interview_scheduled', 'interview_completed']);
            } elseif ($candidateStatus === 'passed') {
                $candidateQuery->whereIn('status', ['passed_selection', 'selection_approved', 'offered', 'hired']);
            } elseif ($candidateStatus === 'rejected') {
                $candidateQuery->whereIn('status', ['dept_rejected', 'interview_failed', 'rejected']);
            } else {
                $candidateQuery->where('status', $candidateStatus);
            }
        }

        $candidates = $candidateQuery->get();

        // Job positions dropdown for filter
        $availableJobPosts = \App\Models\Recruitment\JobPost::when($user && !$user->isCentralHr(), function($q) use ($user) {
            $managedDeptIds = $user->getManagedDepartmentIds();
            $q->whereIn('department_id', $managedDeptIds);
        })->orderBy('title')->get();

        $availableDepartments = $availableJobPosts->map(function($jp) {
            $dept = $jp->department ?: $jp->recruitmentRequest?->department;
            return $dept ? ($dept->department_name ?: ($dept->department_fullname ?: $dept->name)) : null;
        })->filter()->unique()->values();

        // -------------------------------------------------------------
        // 2. รายงานคำขอเปิดรับสมัครพนักงาน (Manpower Requests - เฉพาะ HA)
        // -------------------------------------------------------------
        $isHa = $user && $user->isCentralHr();
        $activeTab = ($isHa && $request->get('tab') === 'requests') ? 'requests' : 'candidates';

        if ($isHa) {
            $query = \App\Models\ManpowerRequest::with(['user'])
                ->orderBy('id', 'desc');

            $totalCount = (clone $query)->count();
            $pendingCount = (clone $query)->whereNotIn('status', ['approved', 'rejected', 'draft'])->count();
            $requests = $query->get();
            $requestDepartments = \App\Models\Recruitment\Department::orderBy('department_fullname')->get();
        } else {
            $totalCount = 0;
            $pendingCount = 0;
            $requests = collect([]);
            $requestDepartments = collect([]);
        }

        return view('frontend.recruitment.report', compact(
            'candidates',
            'candidateStats',
            'candidateStatus',
            'availableJobPosts',
            'availableDepartments',
            'requests',
            'totalCount',
            'pendingCount',
            'activeTab',
            'isHa',
            'requestDepartments'
        ));
    }

    public function requestShow($id)
    {
        $user = Auth::user();
        $recruitmentRequest = RecruitmentRequest::with(['department', 'jobPosition', 'requester', 'managerApprover', 'executiveApprover', 'targetManagerApprover', 'targetExecutiveApprover'])->findOrFail($id);

        // Authorization check
        if (!$user->isHrOrAdmin() && 
            $user->id != $recruitmentRequest->requested_by &&
            $user->id != $recruitmentRequest->approver_manager_id && 
            $user->id != $recruitmentRequest->approver_executive_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('frontend.recruitment.request_show', compact('recruitmentRequest'));
    }

    public function searchAddress(Request $request)
    {
        $query = trim($request->get('q', ''));
        $type = trim($request->get('type', '')); // 'p', 'd', 's', 'z' or empty for all
        $filterProvince = trim($request->get('province', ''));
        $filterDistrict = trim($request->get('district', ''));

        $jsonPath = public_path('js/thai_address_compact.json');
        if (!file_exists($jsonPath)) {
            return response()->json([]);
        }

        $data = json_decode(file_get_contents($jsonPath), true);
        if (!is_array($data)) {
            return response()->json([]);
        }

        // Mode 1: Provinces list (when clicking or searching in Province field)
        if ($type === 'p') {
            $provinces = [];
            $queryLower = mb_strtolower($query);
            foreach ($data as $item) {
                if ($query === '' || mb_stripos($item['p'], $queryLower) !== false) {
                    if (!in_array($item['p'], $provinces)) {
                        $provinces[] = $item['p'];
                    }
                }
            }
            sort($provinces);
            return response()->json(['type' => 'p', 'data' => $provinces]);
        }

        // Mode 2: Districts list (when in District field)
        if ($type === 'd') {
            $districts = [];
            $queryLower = mb_strtolower($query);
            foreach ($data as $item) {
                if ($filterProvince !== '' && $item['p'] !== $filterProvince) {
                    continue;
                }
                if ($query === '' || mb_stripos($item['d'], $queryLower) !== false) {
                    $key = $item['d'] . '|' . $item['p'];
                    if (!isset($districts[$key])) {
                        $districts[$key] = [
                            'd' => $item['d'],
                            'p' => $item['p']
                        ];
                    }
                }
            }
            $limit = ($filterProvince !== '') ? 150 : 50;
            return response()->json(['type' => 'd', 'data' => array_values(array_slice($districts, 0, $limit))]);
        }

        // Mode 3: Subdistricts list (when in Subdistrict field)
        if ($type === 's') {
            $subdistricts = [];
            $queryLower = mb_strtolower($query);
            foreach ($data as $item) {
                if ($filterProvince !== '' && $item['p'] !== $filterProvince) {
                    continue;
                }
                if ($filterDistrict !== '' && $item['d'] !== $filterDistrict) {
                    continue;
                }
                if ($query === '' || mb_stripos($item['s'], $queryLower) !== false) {
                    $key = $item['s'] . '|' . $item['d'] . '|' . $item['p'];
                    if (!isset($subdistricts[$key])) {
                        $subdistricts[$key] = $item;
                    }
                }
            }
            $limit = ($filterDistrict !== '') ? 150 : 50;
            return response()->json(['type' => 's', 'data' => array_values(array_slice($subdistricts, 0, $limit))]);
        }

        // Mode 4: General search or Zipcode search
        if (mb_strlen($query) === 0) {
            $provinces = [];
            foreach ($data as $item) {
                if (!in_array($item['p'], $provinces)) {
                    $provinces[] = $item['p'];
                }
            }
            sort($provinces);
            return response()->json(['provinces' => $provinces]);
        }

        $results = [];
        $queryLower = mb_strtolower($query);

        foreach ($data as $item) {
            // Apply province/district filter if already filled
            if ($filterProvince !== '' && $item['p'] !== $filterProvince) {
                continue;
            }
            if ($filterDistrict !== '' && $item['d'] !== $filterDistrict) {
                continue;
            }

            if (
                mb_stripos($item['s'], $queryLower) !== false ||
                mb_stripos($item['d'], $queryLower) !== false ||
                mb_stripos($item['p'], $queryLower) !== false ||
                mb_stripos($item['z'], $queryLower) !== false
            ) {
                $results[] = $item;
                if (count($results) >= 25) {
                    break;
                }
            }
        }

        return response()->json($results);
    }

    /**
     * Show applicant tracking search form (supports auto-lookup via ?app_no=...)
     */
    public function trackForm(Request $request)
    {
        $applicationNo = trim((string)$request->query('app_no', ''));
        $application = null;
        $applications = collect([]);
        $searched = false;

        if (!empty($applicationNo)) {
            $application = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.jobPosition', 'jobPost.department'])
                ->where('application_no', strtoupper($applicationNo))
                ->first();

            if ($application) {
                $searched = true;
                if ($application->applicant_id) {
                    $applications = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.jobPosition', 'jobPost.department'])
                        ->where('applicant_id', $application->applicant_id)
                        ->orderBy('id', 'desc')
                        ->get();
                } else {
                    $applications = collect([$application]);
                }
            }
        }

        return view('frontend.recruitment.track', [
            'applicationNo' => $applicationNo,
            'application' => $application,
            'applications' => $applications,
            'searched' => $searched,
        ]);
    }

    /**
     * Process tracking request and display current status (allows either application_no OR phone/email)
     */
    public function trackStatus(Request $request)
    {
        $appNoInput = trim((string)$request->input('application_no', ''));
        $identifierInput = trim((string)$request->input('identifier', ''));

        // Check if at least one input is provided
        if (empty($appNoInput) && empty($identifierInput)) {
            return back()->withInput()->withErrors([
                'search_error' => 'กรุณากรอกข้อมูลอย่างใดอย่างหนึ่ง: เลขที่ใบสมัคร หรือ เบอร์โทรศัพท์ หรือ อีเมล'
            ]);
        }

        $selectedApp = null;
        $allApplications = collect([]);

        // Case 1: Application No is provided
        if (!empty($appNoInput)) {
            $cleanAppNo = strtoupper($appNoInput);

            // Look up by application_no
            $app = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.jobPosition', 'jobPost.department'])
                ->where('application_no', $cleanAppNo)
                ->first();

            if ($app) {
                // If identifier is also provided, verify match
                if (!empty($identifierInput)) {
                    $applicant = $app->applicant;
                    $appEmail = strtolower(trim($applicant->email ?? ''));
                    $appPhone = preg_replace('/[^0-9]/', '', $applicant->phone ?? '');
                    $inputClean = preg_replace('/[^0-9]/', '', $identifierInput);
                    $identLower = strtolower($identifierInput);

                    $isEmailMatch = ($identLower === $appEmail);
                    $isPhoneMatch = (!empty($appPhone) && !empty($inputClean) && (str_ends_with($appPhone, $inputClean) || $appPhone === $inputClean));

                    if (!$isEmailMatch && !$isPhoneMatch) {
                        return back()->withInput()->withErrors([
                            'identifier' => 'ข้อมูลเบอร์โทรศัพท์/อีเมลไม่ตรงกับใบสมัครนี้'
                        ]);
                    }
                }

                $selectedApp = $app;
                // Also load all other applications of this applicant if available
                if ($app->applicant_id) {
                    $allApplications = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.jobPosition', 'jobPost.department'])
                        ->where('applicant_id', $app->applicant_id)
                        ->orderBy('id', 'desc')
                        ->get();
                } else {
                    $allApplications = collect([$app]);
                }
            } else {
                // If not found as application_no, check if user entered phone or email in this box
                if (empty($identifierInput)) {
                    $identifierInput = $appNoInput;
                    $appNoInput = '';
                } else {
                    return back()->withInput()->withErrors([
                        'application_no' => 'ไม่พบข้อมูลใบสมัครเลขที่นี้ กรุณาตรวจสอบความถูกต้อง'
                    ]);
                }
            }
        }

        // Case 2: Identifier (Phone or Email) is provided (or fallback from appNoInput)
        if (!$selectedApp && !empty($identifierInput)) {
            $identLower = strtolower($identifierInput);
            $cleanDigits = preg_replace('/[^0-9]/', '', $identifierInput);

            // First check if identifierInput was actually an application_no (e.g. user pasted APP-... into phone/email box)
            $byAppNo = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.jobPosition', 'jobPost.department'])
                ->where('application_no', strtoupper($identifierInput))
                ->first();

            if ($byAppNo) {
                $selectedApp = $byAppNo;
                if ($byAppNo->applicant_id) {
                    $allApplications = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.jobPosition', 'jobPost.department'])
                        ->where('applicant_id', $byAppNo->applicant_id)
                        ->orderBy('id', 'desc')
                        ->get();
                } else {
                    $allApplications = collect([$byAppNo]);
                }
            } else {
                // Search by email or phone
                $allApplications = \App\Models\Recruitment\Application::with(['applicant', 'jobPost.jobPosition', 'jobPost.department'])
                    ->whereHas('applicant', function($q) use ($identLower, $cleanDigits) {
                        $q->where(function($subQ) use ($identLower, $cleanDigits) {
                            if (str_contains($identLower, '@')) {
                                $subQ->where('email', $identLower);
                            } else {
                                if (!empty($cleanDigits) && strlen($cleanDigits) >= 4) {
                                    $subQ->whereRaw("REPLACE(REPLACE(REPLACE(phone, '-', ''), ' ', ''), '+66', '0') LIKE ?", ["%{$cleanDigits}%"]);
                                }
                                $subQ->orWhere('email', $identLower);
                            }
                        });
                    })
                    ->orderBy('id', 'desc')
                    ->get();

                if ($allApplications->isEmpty()) {
                    return back()->withInput()->withErrors([
                        'identifier' => 'ไม่พบข้อมูลการสมัครงานที่ตรงกับเลขที่ใบสมัคร หรือเบอร์โทรศัพท์/อีเมลนี้'
                    ]);
                }

                // If user selected a specific application via select_id query or input
                $selectId = $request->input('select_id');
                if ($selectId) {
                    $selectedApp = $allApplications->firstWhere('id', $selectId) ?? $allApplications->first();
                } else {
                    $selectedApp = $allApplications->first();
                }
            }
        }

        if (!$selectedApp) {
            return back()->withInput()->withErrors([
                'search_error' => 'ไม่พบข้อมูลใบสมัคร กรุณาตรวจสอบเลขที่ใบสมัคร หรือเบอร์โทรศัพท์/อีเมล'
            ]);
        }

        return view('frontend.recruitment.track', [
            'applicationNo' => $selectedApp->application_no,
            'application' => $selectedApp,
            'applications' => $allApplications,
            'searched' => true,
        ]);
    }
}
