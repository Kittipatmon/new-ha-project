<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\datacenter\News;
use Illuminate\Support\Facades\File; // เรียกใช้ Facade File เพื่อใช้ลบไฟล์

class NewsController extends Controller
{
    public function index()
    {
        $newsItems = News::orderBy('published_date', 'desc')->get();
        return view('backend.news.index', compact('newsItems'));
    }

    public function newsAll()
    {
        $newsItems = News::where('is_active', true)
            ->orderBy('published_date', 'desc')
            ->get();

        return view('backend.news.newsall', compact('newsItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'link_news' => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'file_news' => 'nullable|array',
            'file_news.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:20480', // 20MB
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filename = time() . '_' . uniqid('img_') . '_' . $cleanName;
                $file->move(public_path('images/news'), $filename);
                $imagePaths[] = 'images/news/' . $filename;
            }
        }

        $fileNewsPaths = [];
        if ($request->hasFile('file_news')) {
            foreach ($request->file('file_news') as $file) {
                $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filename = time() . '_' . uniqid('file_') . '_' . $cleanName;
                $file->move(public_path('files/news'), $filename);
                $fileNewsPaths[] = 'files/news/' . $filename;
            }
        }

        try {
            $news = News::create([
                'title' => $request->title,
                'content' => $request->input('content'),
                'published_date' => $request->published_date,
                'is_active' => $request->has('is_active'),
                'image_path' => $imagePaths,
                'file_news' => $fileNewsPaths,
                'link_news' => $request->link_news,
            ]);

            return response()->json($news);
        } catch (\Exception $e) {
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage()], 500);
        }
    }

    public function edit(News $news)
    {
        return response()->json($news);
    }

    public function update(Request $request, News $news)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'link_news' => 'nullable|string|max:255',
            'published_date' => 'nullable|date',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'file_news' => 'nullable|array',
            'file_news.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:20480', // 20MB
        ]);

        // Normalize existing paths to arrays
        $existingImages = is_array($news->image_path) ? $news->image_path : ($news->image_path ? [$news->image_path] : []);
        $existingFiles = is_array($news->file_news) ? $news->file_news : ($news->file_news ? [$news->file_news] : []);

        // Process deletions of existing images
        if ($request->has('deleted_images')) {
            foreach ($request->deleted_images as $path) {
                if (($key = array_search($path, $existingImages)) !== false) {
                    if (File::exists(public_path($path))) {
                        File::delete(public_path($path));
                    }
                    unset($existingImages[$key]);
                }
            }
            $existingImages = array_values($existingImages); // Re-index
        }

        // Process deletions of existing files
        if ($request->has('deleted_files')) {
            foreach ($request->deleted_files as $path) {
                if (($key = array_search($path, $existingFiles)) !== false) {
                    if (File::exists(public_path($path))) {
                        File::delete(public_path($path));
                    }
                    unset($existingFiles[$key]);
                }
            }
            $existingFiles = array_values($existingFiles); // Re-index
        }

        $newImagePaths = $existingImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filename = time() . '_' . uniqid('img_') . '_' . $cleanName;
                $file->move(public_path('images/news'), $filename);
                $newImagePaths[] = 'images/news/' . $filename;
            }
        }

        $newFilePaths = $existingFiles;
        if ($request->hasFile('file_news')) {
            foreach ($request->file('file_news') as $file) {
                $cleanName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file->getClientOriginalName());
                $filename = time() . '_' . uniqid('file_') . '_' . $cleanName;
                $file->move(public_path('files/news'), $filename);
                $newFilePaths[] = 'files/news/' . $filename;
            }
        }

        try {
            $news->update([
                'title' => $request->title,
                'content' => $request->input('content'),
                'published_date' => $request->published_date,
                'is_active' => $request->has('is_active'),
                'image_path' => $newImagePaths,
                'file_news' => $newFilePaths,
                'link_news' => $request->link_news,
            ]);

            return response()->json($news);
        } catch (\Exception $e) {
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(News $news)
    {
        // ลบรูป (รองรับหลายไฟล์)
        $imagePaths = is_array($news->image_path) ? $news->image_path : ($news->image_path ? [$news->image_path] : []);
        foreach ($imagePaths as $img) {
            if ($img && File::exists(public_path($img))) {
                File::delete(public_path($img));
            }
        }

        // ลบไฟล์แนบ (รองรับหลายไฟล์)
        $filePaths = is_array($news->file_news) ? $news->file_news : ($news->file_news ? [$news->file_news] : []);
        foreach ($filePaths as $fp) {
            if ($fp && File::exists(public_path($fp))) {
                File::delete(public_path($fp));
            }
        }

        $news->delete();
        return response()->json(['success' => 'News deleted successfully.']);
    }

    public function detail(Request $request, $id)
    {
        $news = News::findOrFail($id);

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('news', 'views')) {
                $news->increment('views');
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('news', 'clicks')) {
                $news->increment('clicks'); // count opening detail as click
            }

            // Record log into news_views
            if (\Illuminate\Support\Facades\Schema::hasTable('news_views')) {
                \App\Models\datacenter\NewsView::create([
                    'news_id' => $news->news_id,
                    'event_type' => 'view',
                    'user_id' => auth()->id(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('User-Agent'),
                    'view_date' => now()->toDateString(),
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('News detail view/click tracking failed: ' . $e->getMessage());
        }

        return view('backend.news.detail', compact('news'));
    }

    /**
     * Return analytics stats for news modal/charts.
     */
    public function analyticsData(Request $request)
    {
        $newsId = $request->query('news_id');
        $days = (int) $request->query('days', 7);

        $query = \App\Models\datacenter\NewsView::query();
        if ($newsId) {
            $query->where('news_id', $newsId);
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

        $cols = ['news_id', 'is_active', 'title'];
        if (\Illuminate\Support\Facades\Schema::hasColumn('news', 'views')) {
            $cols[] = 'views';
        }
        if (\Illuminate\Support\Facades\Schema::hasColumn('news', 'clicks')) {
            $cols[] = 'clicks';
        }
        $newsList = News::select($cols)->get();

        $totalViews = \Illuminate\Support\Facades\Schema::hasColumn('news', 'views') ? (int) News::sum('views') : 0;
        $totalClicks = \Illuminate\Support\Facades\Schema::hasColumn('news', 'clicks') ? (int) News::sum('clicks') : 0;

        return response()->json([
            'logs' => $logs,
            'hourly_logs' => $hourlyLogs,
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'news_items' => $newsList,
        ]);
    }
}