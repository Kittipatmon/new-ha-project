<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\datacenter\HeroBackground;
use Illuminate\Support\Facades\File;

class HeroBackgroundController extends Controller
{
    /**
     * Display a listing of hero background images.
     */
    public function index()
    {
        $backgrounds = HeroBackground::orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return view('backend.hero-backgrounds.index', compact('backgrounds'));
    }

    /**
     * Store a newly created hero background.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid('hero_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/hero-backgrounds'), $filename);
            $imagePath = 'images/hero-backgrounds/' . $filename;
        }

        try {
            $background = HeroBackground::create([
                'title' => $request->title,
                'image_path' => $imagePath,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'เพิ่มภาพพื้นหลังสำเร็จ',
                'background' => $background
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการบันทึก: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Return hero background data for editing via Ajax.
     */
    public function edit(HeroBackground $hero_background)
    {
        return response()->json([
            'id' => $hero_background->id,
            'title' => $hero_background->title,
            'image_path' => $hero_background->image_path,
            'sort_order' => $hero_background->sort_order,
            'is_active' => $hero_background->is_active,
        ]);
    }

    /**
     * Update the specified hero background.
     */
    public function update(Request $request, HeroBackground $hero_background)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'sort_order' => 'nullable|integer',
        ]);

        $imagePath = $hero_background->image_path;
        if ($request->hasFile('image')) {
            // Remove old image
            if ($imagePath && File::exists(public_path($imagePath))) {
                File::delete(public_path($imagePath));
            }
            $file = $request->file('image');
            $filename = time() . '_' . uniqid('hero_') . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/hero-backgrounds'), $filename);
            $imagePath = 'images/hero-backgrounds/' . $filename;
        }

        try {
            $hero_background->update([
                'title' => $request->title,
                'image_path' => $imagePath,
                'sort_order' => $request->sort_order ?? 0,
                'is_active' => $request->has('is_active'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'แก้ไขภาพพื้นหลังสำเร็จ',
                'background' => $hero_background
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการแก้ไข: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified hero background.
     */
    public function destroy(HeroBackground $hero_background)
    {
        if ($hero_background->image_path && File::exists(public_path($hero_background->image_path))) {
            File::delete(public_path($hero_background->image_path));
        }

        $hero_background->delete();
        return response()->json(['success' => 'ลบภาพพื้นหลังเรียบร้อยแล้ว']);
    }

    /**
     * Toggle active status via Ajax.
     */
    public function toggleActive(HeroBackground $hero_background)
    {
        $hero_background->update(['is_active' => !$hero_background->is_active]);

        return response()->json([
            'success' => true,
            'is_active' => $hero_background->is_active,
            'message' => $hero_background->is_active ? 'เปิดใช้งานแล้ว' : 'ปิดใช้งานแล้ว'
        ]);
    }
}
