<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\datacenter\Poster;
use Illuminate\Support\Facades\File;

class PosterController extends Controller
{
    /**
     * Display a listing of posters.
     */
    public function index()
    {
        $posters = Poster::orderBy('position')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('backend.posters.index', compact('posters'));
    }

    /**
     * Store a newly created poster.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|in:hero_banner,hero_top,main_carousel,side_top,side_bottom,recruitment',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'target_type' => 'required|in:link,file,none',
            'link_url' => 'nullable|url|max:500',
            'attachment_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp,zip,rar|max:20480',
            'sort_order' => 'nullable|integer',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid('poster_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/posters'), $filename);
            $imagePath = 'images/posters/' . $filename;
        }

        $filePath = null;
        if ($request->hasFile('attachment_file')) {
            $file = $request->file('attachment_file');
            $filename = time() . '_' . uniqid('file_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('files/posters'), $filename);
            $filePath = 'files/posters/' . $filename;
        }

        try {
            $poster = Poster::create([
                'title' => $request->title,
                'position' => $request->position,
                'image_path' => $imagePath,
                'target_type' => $request->target_type,
                'link_url' => $request->target_type === 'link' ? $request->link_url : null,
                'file_path' => $request->target_type === 'file' ? $filePath : null,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active'),
                'start_at' => $request->start_at ? \Carbon\Carbon::parse($request->start_at) : null,
                'end_at' => $request->end_at ? \Carbon\Carbon::parse($request->end_at) : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มโปสเตอร์สำเร็จ',
                'poster' => $poster
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Return poster data for editing via Ajax.
     */
    public function edit(Poster $poster)
    {
        return response()->json([
            'id' => $poster->id,
            'title' => $poster->title,
            'position' => $poster->position,
            'image_path' => $poster->image_path,
            'target_type' => $poster->target_type,
            'link_url' => $poster->link_url,
            'file_path' => $poster->file_path,
            'sort_order' => $poster->sort_order,
            'is_active' => $poster->is_active,
            'start_at' => $poster->start_at ? $poster->start_at->format('Y-m-d\TH:i') : null,
            'end_at' => $poster->end_at ? $poster->end_at->format('Y-m-d\TH:i') : null,
        ]);
    }

    /**
     * Update the specified poster.
     */
    public function update(Request $request, Poster $poster)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|in:hero_banner,hero_top,main_carousel,side_top,side_bottom,recruitment',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            'target_type' => 'required|in:link,file,none',
            'link_url' => 'nullable|url|max:500',
            'attachment_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp,zip,rar|max:20480',
            'sort_order' => 'nullable|integer',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        $imagePath = $poster->image_path;
        if ($request->hasFile('image')) {
            // Remove old image
            if ($imagePath && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }
            $file = $request->file('image');
            $filename = time() . '_' . uniqid('poster_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/posters'), $filename);
            $imagePath = 'images/posters/' . $filename;
        }

        $filePath = $poster->file_path;
        if ($request->hasFile('attachment_file')) {
            // Remove old file
            if ($filePath && File::exists(public_path($filePath))) {
                File::delete(public_path($filePath));
            }
            $file = $request->file('attachment_file');
            $filename = time() . '_' . uniqid('file_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('files/posters'), $filename);
            $filePath = 'files/posters/' . $filename;
        }

        try {
            $poster->update([
                'title' => $request->title,
                'position' => $request->position,
                'image_path' => $imagePath,
                'target_type' => $request->target_type,
                'link_url' => $request->target_type === 'link' ? $request->link_url : null,
                'file_path' => $request->target_type === 'file' ? $filePath : null,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active'),
                'start_at' => $request->start_at ? \Carbon\Carbon::parse($request->start_at) : null,
                'end_at' => $request->end_at ? \Carbon\Carbon::parse($request->end_at) : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'แก้ไขโปสเตอร์สำเร็จ',
                'poster' => $poster
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการแก้ไข: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified poster.
     */
    public function destroy(Poster $poster)
    {
        if ($poster->image_path && File::exists(public_path($poster->image_path))) {
            File::delete(public_path($poster->image_path));
        }

        if ($poster->file_path && File::exists(public_path($poster->file_path))) {
            File::delete(public_path($poster->file_path));
        }

        $poster->delete();
        return response()->json(['success' => 'ลบโปสเตอร์เรียบร้อยแล้ว']);
    }

    /**
     * Track impressions (views) when posters are shown on frontend.
     */
    public function trackViews(Request $request)
    {
        $ids = $request->input('poster_ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false]);
        }

        $userId = auth()->id();
        $ip = $request->ip();
        $userAgent = $request->header('User-Agent');
        $today = now()->toDateString();

        foreach ($ids as $id) {
            $poster = Poster::find($id);
            if ($poster) {
                // Increment total views
                $poster->increment('views');

                // Record detailed log
                \App\Models\datacenter\PosterView::create([
                    'poster_id' => $poster->id,
                    'event_type' => 'view',
                    'user_id' => $userId,
                    'ip_address' => $ip,
                    'user_agent' => $userAgent,
                    'view_date' => $today,
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Track poster click and redirect to target URL/File.
     */
    public function handleClick(Request $request, Poster $poster)
    {
        // Increment total clicks
        $poster->increment('clicks');

        // Record detailed log
        \App\Models\datacenter\PosterView::create([
            'poster_id' => $poster->id,
            'event_type' => 'click',
            'user_id' => auth()->id(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'view_date' => now()->toDateString(),
        ]);

        $actionUrl = $poster->action_url;
        if ($actionUrl) {
            return redirect()->away($actionUrl);
        }

        return redirect()->route('welcome');
    }

    /**
     * Return daily analytics stats for modal/charts.
     */
    public function analyticsData(Request $request)
    {
        $posterId = $request->query('poster_id');
        $days = (int) $request->query('days', 7);

        $query = \App\Models\datacenter\PosterView::query();
        if ($posterId) {
            $query->where('poster_id', $posterId);
        }

        $startDate = now()->subDays($days - 1)->toDateString();
        $logs = (clone $query)->where('view_date', '>=', $startDate)
            ->selectRaw('view_date, event_type, count(*) as count')
            ->groupBy('view_date', 'event_type')
            ->orderBy('view_date', 'asc')
            ->get();

        // Hourly statistics for peak hours
        $hourlyLogs = (clone $query)->where('view_date', '>=', $startDate)
            ->selectRaw('HOUR(created_at) as hour, event_type, count(*) as count')
            ->groupBy('hour', 'event_type')
            ->orderBy('hour', 'asc')
            ->get();

        $postersList = Poster::select('id', 'views', 'clicks', 'is_active', 'start_at', 'end_at')->get();

        return response()->json([
            'logs' => $logs,
            'hourly_logs' => $hourlyLogs,
            'total_views' => Poster::sum('views'),
            'total_clicks' => Poster::sum('clicks'),
            'posters' => $postersList,
        ]);
    }
}
