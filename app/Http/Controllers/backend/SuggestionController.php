<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suggestion;
use App\Models\User;

class SuggestionController extends Controller
{
    public function index()
    {
        $users = User::where('status', '0')->get();
        $mySuggestions = collect();
        if (auth()->check()) {
            $mySuggestions = Suggestion::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return view('suggestion.index', compact('users', 'mySuggestions'));
    }

    public function dashboard()
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $total = Suggestion::count();
        $pending = Suggestion::whereIn('status', ['รอรับเรื่องคำร้อง', 'รับเรื่อง', 'รับเรื่องคำร้อง', 'รับเรื่องคำร้องแล้ว'])->count();
        $inProgress = Suggestion::whereIn('status', ['ตรวจสอบ', 'ดำเนินการ'])->count();
        $completed = Suggestion::whereIn('status', ['เสร็จสิ้น', 'ปิดเรื่อง'])->count();

        // 1. All Statuses pre-filled
        $allStatuses = [
            'รอรับเรื่องคำร้อง' => 0,
            'รับเรื่องคำร้องแล้ว' => 0,
            'ตรวจสอบ' => 0,
            'ดำเนินการ' => 0,
            'เสร็จสิ้น' => 0,
            'ปิดเรื่อง' => 0
        ];

        // Group those states: "รับเรื่อง", "รับเรื่องคำร้อง", "รับเรื่องคำร้องแล้ว" into one.
        $rawStatuses = Suggestion::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');
        foreach ($rawStatuses as $status => $count) {
            $statusStr = trim($status);
            if (in_array($statusStr, ['รับเรื่อง', 'รับเรื่องคำร้อง', 'รับเรื่องคำร้องแล้ว'])) {
                $allStatuses['รับเรื่องคำร้องแล้ว'] += $count;
            } elseif (array_key_exists($statusStr, $allStatuses)) {
                $allStatuses[$statusStr] += $count;
            } else {
                // If there's an unknown status, dynamically add it
                $allStatuses[$statusStr] = $count;
            }
        }
        $statusLabels = array_keys($allStatuses);
        $statusCounts = array_values($allStatuses);


        // 2. All Complaint Types pre-filled
        $allTypes = [
            'ร้องเรียนด้วยตนเอง' => 0,
            'ร้องเรียนแทนผู้อื่น' => 0,
            'ร้องเรียนทางโทรศัพท์' => 0
        ];

        $rawTypes = Suggestion::selectRaw('complaint_type, count(*) as count')->groupBy('complaint_type')->pluck('count', 'complaint_type');
        foreach ($rawTypes as $type => $count) {
            if ($type == 'self')
                $allTypes['ร้องเรียนด้วยตนเอง'] += $count;
            elseif ($type == 'other')
                $allTypes['ร้องเรียนแทนผู้อื่น'] += $count;
            elseif ($type == 'phone')
                $allTypes['ร้องเรียนทางโทรศัพท์'] += $count;
            else
                $allTypes[$type] = $count;
        }
        $typeLabels = array_keys($allTypes);
        $typeCounts = array_values($allTypes);

        // Count per month for trend
        $monthlyData = Suggestion::selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, count(*) as count')
            ->groupBy('month')
            ->orderByRaw('MIN(created_at)')
            ->pluck('count', 'month');
        $monthlyLabels = $monthlyData->keys()->toArray();
        $monthlyCounts = $monthlyData->values()->toArray();


        return view('suggestion.dashboard', compact(
            'total',
            'pending',
            'inProgress',
            'completed',
            'statusLabels',
            'statusCounts',
            'typeLabels',
            'typeCounts',
            'monthlyLabels',
            'monthlyCounts'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'complaint_type' => 'required|string',
            'topic' => 'required|string|max:255',
            'to_person' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'age' => 'nullable|integer',
            'phone' => 'required|string|max:50',
            'address_no' => 'nullable|string|max:255',
            'moo' => 'nullable|string|max:255',
            'soi' => 'nullable|string|max:255',
            'road' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'details' => 'required|string',
            'demands' => 'required|string',
            'docs' => 'nullable|array',
            'other_docs_detail' => 'nullable|string',
            'history' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $suggestion = new Suggestion($validated);
        $suggestion->user_id = auth()->id();
        $suggestion->status = 'รอรับเรื่องคำร้อง';

        if ($request->hasFile('attachments')) {
            $attachmentPaths = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('suggestions', 'public');
                $attachmentPaths[] = $path;
            }
            $suggestion->attachments = $attachmentPaths;
        }

        $suggestion->save();

        return redirect()->route('suggestion.index')->with('success', 'ส่งข้อเสนอแนะสำเร็จระบบรับเรื่องเรียบร้อยแล้ว');
    }

    public function apiSuggestions(Request $request)
    {
        $perPage = (int) $request->input('per_page', 50);
        $perPage = max(1, min($perPage, 100));

        $suggestions = $this->filteredSuggestions($request)
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json($suggestions);
    }

    public function list(Request $request)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $suggestions = $this->filteredSuggestions($request)->paginate(50)->withQueryString();
        return view('backend.suggestion.list', compact('suggestions'));
    }

    private function filteredSuggestions(Request $request)
    {
        $query = Suggestion::query();

        // 1. Text Search Filter (topic, fullname, to_person, phone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'LIKE', "%{$search}%")
                    ->orWhere('fullname', 'LIKE', "%{$search}%")
                    ->orWhere('to_person', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        // 2. Status Filter
        if ($request->filled('status')) {
            // handle exact match
            if ($request->status == 'รับเรื่องคำร้องแล้ว') {
                $query->whereIn('status', ['รับเรื่อง', 'รับเรื่องคำร้อง', 'รับเรื่องคำร้องแล้ว']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // 3. Complaint Type Filter
        if ($request->filled('complaint_type')) {
            $query->where('complaint_type', $request->complaint_type);
        }

        // 4. Date Range Filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            // ensure end_date covers the whole day
            $endDate = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $endDate]);
        } elseif ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date . ' 00:00:00');
        } elseif ($request->filled('end_date')) {
            $endDate = date('Y-m-d 23:59:59', strtotime($request->end_date));
            $query->where('created_at', '<=', $endDate);
        }

        // Define exact ordering of status so urgent ones are consistently at top
        $query->orderByRaw("CASE 
            WHEN status = 'รอรับเรื่องคำร้อง' THEN 1
            WHEN status IN ('รับเรื่อง', 'รับเรื่องคำร้อง', 'รับเรื่องคำร้องแล้ว') THEN 2
            WHEN status = 'ตรวจสอบ' THEN 3
            WHEN status = 'ดำเนินการ' THEN 4
            WHEN status = 'เสร็จสิ้น' THEN 5
            WHEN status = 'ปิดเรื่อง' THEN 6
            ELSE 7 
        END ASC");

        $query->orderBy('created_at', 'desc');

        return $query;
    }

    public function edit($id)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $suggestion = Suggestion::findOrFail($id);
        $users = User::where('status', '0')->get();
        return view('backend.suggestion.edit', compact('suggestion', 'users'));
    }

    public function show($id)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $suggestion = Suggestion::findOrFail($id);
        return view('backend.suggestion.show', compact('suggestion'));
    }

    public function userShow($id)
    {
        $suggestion = Suggestion::findOrFail($id);

        // Ensure user can only view their own suggestions
        if ($suggestion->user_id && $suggestion->user_id != auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('suggestion.user_show', compact('suggestion'));
    }

    public function update(Request $request, $id)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $validated = $request->validate([
            'complaint_type' => 'required|string',
            'topic' => 'required|string|max:255',
            'to_person' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'age' => 'nullable|integer',
            'phone' => 'required|string|max:50',
            'address_no' => 'nullable|string|max:255',
            'moo' => 'nullable|string|max:255',
            'soi' => 'nullable|string|max:255',
            'road' => 'nullable|string|max:255',
            'subdistrict' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'details' => 'required|string',
            'demands' => 'required|string',
            'history' => 'required|string',
            'status' => 'required|string',
            'progress_notes' => 'nullable|string',
        ]);

        $suggestion = Suggestion::findOrFail($id);
        $suggestion->update($validated);

        return redirect()->route('suggestion.list')->with('success', 'ปรับปรุงข้อมูลสำเร็จแล้ว');
    }

    public function destroy($id)
    {
        if (!(auth()->check() && (auth()->user()->hr_status == 0 || auth()->user()->employee_code == '11648'))) {
            abort(403);
        }
        $suggestion = Suggestion::findOrFail($id);
        $suggestion->delete();

        return redirect()->route('suggestion.list')->with('success', 'ลบข้อมูลสำเร็จแล้ว');
    }
}
