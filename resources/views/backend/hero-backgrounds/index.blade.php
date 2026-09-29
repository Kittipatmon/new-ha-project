@extends('layouts.app')

@section('content')
<div class="w-full">
    <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-5 sm:p-7 space-y-6">
        
        <!-- Header Bar Inside Frame -->
        <div class="pb-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex text-xs font-semibold text-slate-500 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('welcome') }}" class="hover:text-indigo-600 transition">หน้าหลัก</a>
                    <span class="mx-2 text-slate-400">/</span>
                    <span class="text-slate-800 dark:text-slate-200">จัดการภาพพื้นหลัง (Hero Background)</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                    <i class="fa-solid fa-panorama text-indigo-600"></i> จัดการภาพพื้นหลัง (Hero Background)
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    กำหนดรูปภาพพื้นหลังแบนเนอร์หัวเว็บ (Hero Banner) ที่จะแสดงในหน้าแรก สามารถเพิ่มได้หลายภาพเพื่อสไลด์สลับกัน
                </p>
            </div>
            <button type="button" onclick="showAddModal()"
                class="btn bg-indigo-600 hover:bg-indigo-700 text-white shadow-md flex items-center gap-2 px-5 py-2.5 rounded-xl font-medium text-sm transition-all cursor-pointer">
                <i class="fa-solid fa-plus"></i> เพิ่มภาพพื้นหลังใหม่
            </button>
        </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-images"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400">ภาพพื้นหลังทั้งหมด</span>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $backgrounds->count() }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400">เปิดใช้งานอยู่</span>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $backgrounds->where('is_active', true)->count() }}</h3>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl">
                <i class="fa-solid fa-circle-pause"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 dark:text-slate-400">ปิดใช้งาน</span>
                <h3 class="text-2xl font-black text-slate-800 dark:text-white">{{ $backgrounds->where('is_active', false)->count() }}</h3>
            </div>
        </div>
    </div>

    <!-- Grid of Background Cards -->
    @if($backgrounds->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="backgrounds-grid">
        @foreach ($backgrounds as $bg)
        <div id="bg-card-{{ $bg->id }}" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden group hover:shadow-lg transition-all duration-300">
            <!-- Image Preview -->
            <div class="relative aspect-[16/7] overflow-hidden bg-slate-100 dark:bg-slate-900">
                <img src="{{ asset($bg->image_path) }}" alt="{{ $bg->title }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">

                <!-- Overlay controls -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                <!-- Status badge -->
                <div class="absolute top-3 left-3">
                    @if($bg->is_active)
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/90 text-white backdrop-blur-sm shadow-sm">
                            <i class="fa-solid fa-circle-check mr-1"></i> เปิดใช้งาน
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-500/90 text-white backdrop-blur-sm shadow-sm">
                            <i class="fa-solid fa-circle-pause mr-1"></i> ปิดใช้งาน
                        </span>
                    @endif
                </div>

                <!-- Sort order badge -->
                <div class="absolute top-3 right-3">
                    <span class="px-2 py-1 rounded-lg text-[11px] font-bold bg-black/50 text-white backdrop-blur-sm">
                        ลำดับ: {{ $bg->sort_order }}
                    </span>
                </div>

                <!-- Action buttons on hover -->
                <div class="absolute bottom-3 right-3 flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <button onclick="openFullPreview('{{ asset($bg->image_path) }}', '{{ $bg->title }}')"
                        class="w-8 h-8 rounded-lg bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm text-slate-700 dark:text-slate-200 flex items-center justify-center hover:bg-white transition-colors shadow-sm"
                        title="ดูภาพเต็ม">
                        <i class="fa-solid fa-expand text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- Card Info -->
            <div class="p-4">
                <h3 class="font-semibold text-sm text-slate-800 dark:text-white truncate mb-1">{{ $bg->title }}</h3>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 mb-3">
                    สร้างเมื่อ {{ $bg->created_at->format('d/m/Y H:i') }}
                </p>

                <div class="flex items-center gap-2">
                    <!-- Toggle Active -->
                    <button onclick="toggleActive({{ $bg->id }})"
                        class="flex-1 px-3 py-2 rounded-lg text-xs font-medium transition-all {{ $bg->is_active ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40' : 'bg-slate-50 dark:bg-slate-700/50 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}"
                        id="toggle-btn-{{ $bg->id }}">
                        <i class="fa-solid {{ $bg->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }} mr-1"></i>
                        {{ $bg->is_active ? 'เปิดอยู่' : 'ปิดอยู่' }}
                    </button>

                    <!-- Edit -->
                    <button onclick="showEditModal({{ $bg->id }})"
                        class="px-3 py-2 rounded-lg text-xs font-medium bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-all">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </button>

                    <!-- Delete -->
                    @if(Auth::check() && Auth::user()->canDelete())
                    <button onclick="confirmDelete({{ $bg->id }}, '{{ addslashes($bg->title) }}')"
                        class="px-3 py-2 rounded-lg text-xs font-medium bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/40 transition-all">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-12 text-center">
        <div class="w-20 h-20 mx-auto rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-400 flex items-center justify-center text-3xl mb-4">
            <i class="fa-solid fa-panorama"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-200 mb-2">ยังไม่มีภาพพื้นหลัง</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">เพิ่มภาพพื้นหลังสำหรับ Hero Banner ที่จะแสดงบนหน้าแรก</p>
        <button onclick="showAddModal()"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium transition-all shadow-sm cursor-pointer">
            <i class="fa-solid fa-plus"></i> เพิ่มภาพพื้นหลังใหม่
        </button>
    </div>
    @endif
    </div>
</div>

<!-- ==================== ADD/EDIT MODAL ==================== -->
<div id="bgModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i class="fa-solid fa-panorama text-indigo-600"></i> เพิ่มภาพพื้นหลัง
            </h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <form id="bgForm" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            <input type="hidden" id="bg_id" name="id">

            <!-- Title -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    <i class="fa-solid fa-heading text-indigo-500 mr-1"></i> ชื่อภาพพื้นหลัง <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="bg_title"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition-all"
                    placeholder="เช่น ภาพโรงงาน Kumwell">
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    <i class="fa-solid fa-image text-indigo-500 mr-1"></i> รูปภาพ <span class="text-red-500" id="imageRequired">*</span>
                </label>
                <div id="imageDropZone"
                    class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 text-center hover:border-indigo-500 dark:hover:border-indigo-400 transition-colors cursor-pointer bg-slate-50 dark:bg-gray-700/50">
                    <input type="file" name="image" id="bg_image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(this)">
                    <div id="uploadPlaceholder">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400 dark:text-slate-500 mb-2"></i>
                        <p class="text-sm text-slate-500 dark:text-slate-400">คลิกหรือลากไฟล์มาที่นี่</p>
                        <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-1">รองรับ JPG, PNG, GIF, SVG, WebP (สูงสุด 10MB)</p>
                        <p class="text-[11px] text-indigo-500 font-medium mt-1">แนะนำขนาด 1920×600 px หรืออัตราส่วน 16:5</p>
                    </div>
                    <div id="imagePreview" class="hidden">
                        <img id="previewImg" src="" alt="Preview" class="max-h-40 mx-auto rounded-lg shadow-sm">
                        <p class="text-xs text-slate-500 mt-2" id="previewFilename"></p>
                    </div>
                </div>
                <!-- Existing image (edit mode) -->
                <div id="currentImageWrap" class="hidden mt-3">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-1">ภาพปัจจุบัน:</p>
                    <img id="currentImage" src="" alt="Current" class="max-h-32 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                </div>
            </div>

            <!-- Sort Order -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1.5">
                    <i class="fa-solid fa-sort text-indigo-500 mr-1"></i> ลำดับการแสดงผล
                </label>
                <input type="number" name="sort_order" id="bg_sort_order" value="0" min="0"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-slate-800 dark:text-white text-sm focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500 transition-all"
                    placeholder="0">
                <p class="text-[11px] text-slate-400 mt-1">ยิ่งตัวเลขน้อย ยิ่งแสดงก่อน</p>
            </div>

            <!-- Active Toggle -->
            <div class="flex items-center gap-3 bg-slate-50 dark:bg-gray-700/50 rounded-xl px-4 py-3">
                <input type="checkbox" name="is_active" id="bg_is_active" class="toggle toggle-success" checked>
                <label for="bg_is_active" class="text-sm font-medium text-slate-700 dark:text-slate-200 cursor-pointer">
                    เปิดใช้งาน (แสดงบนหน้าแรก)
                </label>
            </div>

            <!-- Submit -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeModal()"
                    class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
                    ยกเลิก
                </button>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> <span id="submitText">บันทึก</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== FULL PREVIEW MODAL ==================== -->
<div id="previewModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4 cursor-pointer" onclick="closePreview()">
    <div class="relative max-w-5xl w-full">
        <button onclick="closePreview()" class="absolute -top-10 right-0 w-8 h-8 rounded-lg bg-white/20 text-white hover:bg-white/30 flex items-center justify-center transition-colors">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <img id="fullPreviewImg" src="" alt="" class="w-full rounded-xl shadow-2xl">
        <p id="fullPreviewTitle" class="text-center text-white text-sm mt-3 font-medium"></p>
    </div>
</div>

<!-- ==================== DELETE CONFIRM MODAL ==================== -->
<div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center border border-gray-200 dark:border-gray-700">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-red-50 dark:bg-red-900/30 text-red-500 flex items-center justify-center text-2xl mb-4">
            <i class="fa-solid fa-trash-can"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-2">ยืนยันการลบ</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-1">คุณต้องการลบภาพพื้นหลัง</p>
        <p class="text-sm font-bold text-red-600 dark:text-red-400 mb-5" id="deleteItemTitle"></p>
        <div class="flex items-center justify-center gap-3">
            <button onclick="closeDeleteModal()"
                class="px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition-all">
                ยกเลิก
            </button>
            <button onclick="executeDelete()" id="confirmDeleteBtn"
                class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 shadow-md transition-all flex items-center gap-2">
                <i class="fa-solid fa-trash-can"></i> ลบ
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed top-6 right-6 z-[70] hidden">
    <div id="toastContent" class="flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-medium border backdrop-blur-sm max-w-sm">
        <i id="toastIcon" class="text-base"></i>
        <span id="toastMessage"></span>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const BASE_URL = "{{ url('/') }}";
    let editingId = null;
    let deleteId = null;

    // ===== Show Add Modal =====
    function showAddModal() {
        editingId = null;
        document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-panorama text-indigo-600"></i> เพิ่มภาพพื้นหลังใหม่';
        document.getElementById('submitText').textContent = 'บันทึก';
        document.getElementById('imageRequired').classList.remove('hidden');
        document.getElementById('bgForm').reset();
        document.getElementById('bg_id').value = '';
        document.getElementById('bg_is_active').checked = true;
        document.getElementById('currentImageWrap').classList.add('hidden');
        resetImagePreview();
        openModal('bgModal');
    }

    // ===== Show Edit Modal =====
    function showEditModal(id) {
        editingId = id;
        document.getElementById('modalTitle').innerHTML = '<i class="fa-solid fa-pen-to-square text-indigo-600"></i> แก้ไขภาพพื้นหลัง';
        document.getElementById('submitText').textContent = 'อัปเดต';
        document.getElementById('imageRequired').classList.add('hidden');
        resetImagePreview();

        fetch(`${BASE_URL}/hero-backgrounds/${id}/edit`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('bg_id').value = data.id;
                document.getElementById('bg_title').value = data.title;
                document.getElementById('bg_sort_order').value = data.sort_order;
                document.getElementById('bg_is_active').checked = data.is_active;

                if (data.image_path) {
                    document.getElementById('currentImage').src = `${BASE_URL}/${data.image_path}`;
                    document.getElementById('currentImageWrap').classList.remove('hidden');
                } else {
                    document.getElementById('currentImageWrap').classList.add('hidden');
                }

                openModal('bgModal');
            })
            .catch(() => showToast('ไม่สามารถโหลดข้อมูลได้', 'error'));
    }

    // ===== Submit Form =====
    document.getElementById('bgForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังบันทึก...';

        let url, method;
        if (editingId) {
            url = `${BASE_URL}/hero-backgrounds/${editingId}`;
            formData.append('_method', 'PUT');
        } else {
            url = `${BASE_URL}/hero-backgrounds`;
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                closeModal();
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message || 'เกิดข้อผิดพลาด', 'error');
            }
        })
        .catch(err => {
            showToast('เกิดข้อผิดพลาดในการบันทึก', 'error');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-check"></i> <span id="submitText">' + (editingId ? 'อัปเดต' : 'บันทึก') + '</span>';
        });
    });

    // ===== Toggle Active =====
    function toggleActive(id) {
        fetch(`${BASE_URL}/hero-backgrounds/${id}/toggle-active`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 600);
            }
        })
        .catch(() => showToast('เกิดข้อผิดพลาด', 'error'));
    }

    // ===== Delete =====
    function confirmDelete(id, title) {
        deleteId = id;
        document.getElementById('deleteItemTitle').textContent = `"${title}"`;
        openModal('deleteModal');
    }

    function executeDelete() {
        if (!deleteId) return;
        const btn = document.getElementById('confirmDeleteBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังลบ...';

        fetch(`${BASE_URL}/hero-backgrounds/${deleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            showToast('ลบภาพพื้นหลังสำเร็จ', 'success');
            closeDeleteModal();
            const card = document.getElementById(`bg-card-${deleteId}`);
            if (card) {
                card.style.transition = 'opacity 0.3s, transform 0.3s';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                    // If no cards left, reload to show empty state
                    if (!document.querySelector('[id^="bg-card-"]')) location.reload();
                }, 300);
            }
        })
        .catch(() => showToast('เกิดข้อผิดพลาดในการลบ', 'error'))
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-trash-can"></i> ลบ';
        });
    }

    // ===== Image Preview =====
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImg').src = e.target.result;
                document.getElementById('previewFilename').textContent = input.files[0].name;
                document.getElementById('uploadPlaceholder').classList.add('hidden');
                document.getElementById('imagePreview').classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function resetImagePreview() {
        document.getElementById('uploadPlaceholder').classList.remove('hidden');
        document.getElementById('imagePreview').classList.add('hidden');
        document.getElementById('previewImg').src = '';
        document.getElementById('bg_image').value = '';
    }

    // ===== Full Preview =====
    function openFullPreview(src, title) {
        document.getElementById('fullPreviewImg').src = src;
        document.getElementById('fullPreviewTitle').textContent = title;
        openModal('previewModal');
    }
    function closePreview() {
        closeModalById('previewModal');
    }

    // ===== Modal Helpers =====
    function openModal(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        closeModalById('bgModal');
    }
    function closeDeleteModal() {
        closeModalById('deleteModal');
        deleteId = null;
    }
    function closeModalById(id) {
        const modal = document.getElementById(id);
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        // Only restore scroll if no other modals are visible
        if (!document.querySelector('.fixed.z-50.flex, .fixed.z-\\[60\\].flex')) {
            document.body.style.overflow = '';
        }
    }

    // ===== Toast =====
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const content = document.getElementById('toastContent');
        const icon = document.getElementById('toastIcon');
        const msg = document.getElementById('toastMessage');

        msg.textContent = message;

        if (type === 'success') {
            content.className = 'flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-medium border backdrop-blur-sm max-w-sm bg-emerald-50 dark:bg-emerald-900/80 text-emerald-800 dark:text-emerald-200 border-emerald-200 dark:border-emerald-700';
            icon.className = 'fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400 text-base';
        } else {
            content.className = 'flex items-center gap-3 px-5 py-3 rounded-xl shadow-lg text-sm font-medium border backdrop-blur-sm max-w-sm bg-red-50 dark:bg-red-900/80 text-red-800 dark:text-red-200 border-red-200 dark:border-red-700';
            icon.className = 'fa-solid fa-circle-exclamation text-red-600 dark:text-red-400 text-base';
        }

        toast.classList.remove('hidden');
        toast.style.animation = 'slideIn 0.3s ease-out';

        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => toast.classList.add('hidden'), 280);
        }, 3000);
    }

    // Close modals on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            closeDeleteModal();
            closePreview();
        }
    });
</script>

<style>
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(20px); }
    }
</style>
@endpush
