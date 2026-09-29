@extends('layouts.app')
@section('content')

<div class="w-full">
    <div class="bg-white dark:bg-[#1E2129] rounded-2xl border border-slate-200/90 dark:border-slate-800 shadow-sm p-5 sm:p-7 space-y-6">

        {{-- Breadcrumb & Title Bar Inside Frame --}}
        <div class="pb-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <nav class="flex text-xs font-semibold text-slate-500 mb-1" aria-label="Breadcrumb">
                    <a href="{{ route('welcome') }}" class="hover:text-indigo-600 transition">หน้าหลัก</a>
                    <span class="mx-2 text-slate-400">/</span>
                    <span class="text-slate-500">จัดการข้อมูล</span>
                    <span class="mx-2 text-slate-400">/</span>
                    <span class="text-indigo-600 dark:text-indigo-400 font-bold">จัดการพนักงาน</span>
                </nav>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center text-lg shadow-sm">
                        <i class="fa-solid fa-users-gear"></i>
                    </div>
                    <span>จัดการข้อมูลพนักงานและสิทธิ์ระบบ</span>
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-2">
                    <span>จัดการรายชื่อพนักงานและกำหนดระดับสิทธิ์</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                        <i class="fa-solid fa-database text-[9px]"></i> hrsystem.hr_user_roles
                    </span>
                </p>
            </div>
        </div>

        <div class="space-y-6">

    {{-- Top Alert Messages --}}
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-xs font-medium flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-medium flex items-center gap-3 shadow-xs">
        <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- 1. Search Filter Card (Matches Screenshot Top Card) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs p-5 transition-all">
        <div class="flex items-center justify-between gap-3 mb-3.5">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 flex items-center gap-2">
                <span>Search Filter</span>
            </h3>
            <button type="button" onclick="resetAllFilters()" id="reset-filters-link" class="{{ request()->anyFilled(['role', 'department', 'status', 'keyword', 'fullname']) ? '' : 'hidden' }} text-xs text-rose-500 hover:text-rose-600 dark:text-rose-400 flex items-center gap-1 font-medium transition cursor-pointer">
                <i class="fa-solid fa-rotate-left text-[10px]"></i> รีเซ็ตตัวกรอง
            </button>
        </div>

        <form method="GET" action="{{ route('users.index') }}" id="search-filter-form" class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4" onsubmit="event.preventDefault(); applyLiveFilter(true);">
            {{-- 1. Select Role --}}
            <div class="relative">
                <select id="filter-role" name="role" onchange="applyLiveFilter(true)" class="w-full appearance-none bg-white dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 pr-10 text-xs font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#7367f0] focus:border-[#7367f0] transition shadow-2xs cursor-pointer">
                    <option value="">Select Role (ทุกสิทธิ์)</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบ)</option>
                    <option value="editor" {{ request('role') == 'editor' ? 'selected' : '' }}>Editor (ผู้แก้ไข)</option>
                    <option value="viewer" {{ request('role') == 'viewer' ? 'selected' : '' }}>Viewer (ผู้ดูข้อมูล)</option>
                </select>
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            {{-- 2. Select Plan / Department --}}
            <div class="relative">
                <select id="filter-department" name="department" onchange="applyLiveFilter(true)" class="w-full appearance-none bg-white dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 pr-10 text-xs font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#7367f0] focus:border-[#7367f0] transition shadow-2xs cursor-pointer">
                    <option value="">Select Plan (ทุกแผนก)</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->department_id }}" {{ (string)request('department') === (string)$dept->department_id ? 'selected' : '' }}>
                            {{ $dept->department_name ? $dept->department_name . ' - ' : '' }}{{ $dept->department_fullname }}
                        </option>
                    @endforeach
                </select>
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>

            {{-- 3. Select Status --}}
            <div class="relative">
                <select id="filter-status" name="status" onchange="applyLiveFilter(true)" class="w-full appearance-none bg-white dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 rounded-lg px-3.5 py-2.5 pr-10 text-xs font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-[#7367f0] focus:border-[#7367f0] transition shadow-2xs cursor-pointer">
                    <option value="">Select Status (ทุกสถานะ)</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active (ใช้งาน)</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive (ไม่ได้ใช้งาน / พ้นสภาพ)</option>
                </select>
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
            </div>
        </form>
    </div>

    {{-- 2. Main Table Card (Matches Screenshot Table Card) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200/80 dark:border-gray-700/80 shadow-xs overflow-hidden p-4 sm:p-6 transition-all" id="table-container">

        {{-- Controls Bar: Show entries (Left) | Live Search (Right) --}}
        <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 mb-5">
            {{-- Left: Show Entries --}}
            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 font-medium">
                <span>Show</span>
                <div class="relative inline-block">
                    <select id="per-page-select" onchange="applyLiveFilter(true)" class="appearance-none bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md pl-2.5 pr-7 py-1.5 text-xs font-semibold text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-1 focus:ring-[#7367f0] cursor-pointer shadow-2xs">
                        <option value="10" {{ request('per_page', 50) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 50) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 50) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 50) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    <i class="fa-solid fa-chevron-down text-[8px] text-gray-400 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>
                <span>entries</span>
            </div>

            {{-- Right: Live Search Input with Spinner & Clear button --}}
            <div class="flex flex-wrap items-center justify-end gap-3">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Search:</span>
                    <div class="relative flex-grow sm:flex-grow-0">
                        <input type="text" id="live-search-input" value="{{ request('keyword', request('fullname')) }}" autocomplete="off" placeholder="" class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md pl-3 pr-8 py-1.5 text-xs text-gray-800 dark:text-gray-100 focus:outline-none focus:ring-1 focus:ring-[#7367f0] focus:border-[#7367f0] w-full sm:w-56 transition shadow-2xs">
                        
                        {{-- Loading spinner inside search input --}}
                        <span id="search-spinner" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-[#7367f0] text-xs pointer-events-none">
                            <i class="fa-solid fa-spinner fa-spin"></i>
                        </span>

                        {{-- Clear Search Button --}}
                        <button type="button" id="clear-search-btn" onclick="clearLiveSearch()" class="{{ request('keyword') ? '' : 'hidden' }} absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs cursor-pointer">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table & Pagination Wrapper (Refreshed via AJAX) --}}
        <div id="users-table-wrapper" class="relative transition-opacity duration-150">
            @include('backend.users.partials.table')
        </div>
    </div>

</div>

{{-- =========================================================================
     Change Role Modal (ปรับสิทธิ์ Admin / Editor / Viewer เข้าตาราง hr_user_roles)
     ========================================================================= --}}
<div id="role-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        
        {{-- Modal Header --}}
        <div class="p-5 border-b border-gray-100 dark:border-gray-700/80 flex justify-between items-center bg-gray-50/60 dark:bg-gray-900/40">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#7367f0]/10 text-[#7367f0] flex items-center justify-center text-lg shadow-xs">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-gray-800 dark:text-white">ปรับสิทธิ์การใช้งานระบบ HR</h3>
                    <p class="text-[11px] text-gray-400 dark:text-gray-400">Database: <code class="font-mono text-[10px] text-purple-600 bg-purple-50 px-1 py-0.5 rounded">hrsystem</code> &middot; Table: <code class="font-mono text-[10px] text-purple-600 bg-purple-50 px-1 py-0.5 rounded">hr_user_roles</code></p>
                </div>
            </div>
            <button type="button" onclick="closeRoleModal()" class="w-8 h-8 rounded-lg text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center justify-center transition cursor-pointer">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="role-form" onsubmit="submitRoleForm(event)" class="p-5 space-y-4">
            @csrf
            <input type="hidden" id="modal-user-id" value="">

            {{-- Employee Summary Card --}}
            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-200/70 dark:border-gray-600/60 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 dark:bg-purple-950/60 dark:text-purple-300 flex items-center justify-center font-bold text-sm shrink-0" id="modal-avatar">
                    US
                </div>
                <div class="min-w-0 flex-1">
                    <div class="font-bold text-xs text-gray-800 dark:text-white truncate" id="modal-fullname">-</div>
                    <div class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5">
                        รหัสพนักงาน: <span class="font-mono font-semibold text-gray-700 dark:text-gray-300" id="modal-emp-code">-</span>
                        &middot; แผนก: <span id="modal-dept">-</span>
                    </div>
                </div>
            </div>

            {{-- Role Selection Options --}}
            <div class="space-y-2.5">
                <label class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center justify-between">
                    <span>เลือกระดับสิทธิ์ที่ต้องการกำหนด :</span>
                    <span class="text-[11px] font-normal text-gray-400">คลิกเพื่อเลือกสิทธิ์</span>
                </label>

                {{-- Option 1: ADMIN --}}
                <label class="role-option-card relative flex items-start gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-[#7367f0] dark:hover:border-[#7367f0] cursor-pointer transition-all bg-white dark:bg-gray-800" id="role-card-admin">
                    <input type="radio" name="role" value="admin" class="mt-0.5 text-[#7367f0] focus:ring-[#7367f0]" onchange="highlightRoleCard('admin')">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-purple-600 dark:text-purple-400">
                                <i class="fa-solid fa-shield-halved text-sm"></i> ADMIN
                            </span>
                            <span class="text-[10px] px-2 py-0.2 rounded bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 font-medium">ผู้ดูแลระบบ</span>
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                            เข้าถึงระบบ HR ได้ทั้งหมด จัดการสิทธิ์การใช้งาน ลบข้อมูล และเข้าถึงเมนูตั้งค่าระดับสูง
                        </p>
                        <div id="admin-grant-warning" class="hidden mt-2 p-2 rounded-lg bg-amber-50 dark:bg-amber-950/50 text-[11px] text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> เฉพาะผู้ดูแลระบบฝ่าย 16 Information Communication Technology เท่านั้นที่สามารถเปลี่ยนเป็น ADMIN ได้
                        </div>
                    </div>
                </label>

                {{-- Option 2: EDITOR --}}
                <label class="role-option-card relative flex items-start gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-[#7367f0] dark:hover:border-[#7367f0] cursor-pointer transition-all bg-white dark:bg-gray-800" id="role-card-editor">
                    <input type="radio" name="role" value="editor" class="mt-0.5 text-[#7367f0] focus:ring-[#7367f0]" onchange="highlightRoleCard('editor')">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-cyan-600 dark:text-cyan-400">
                                <i class="fa-solid fa-pen text-xs"></i> EDITOR
                            </span>
                            <span class="text-[10px] px-2 py-0.2 rounded bg-cyan-50 text-cyan-700 dark:bg-cyan-950/60 dark:text-cyan-300 font-medium">ผู้แก้ไข</span>
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                            สามารถดูข้อมูล เพิ่มข้อมูล และแก้ไขข้อมูลพนักงาน/เอกสารต่างๆ ได้ (ห้ามลบข้อมูล และห้ามจัดการสิทธิ์)
                        </p>
                    </div>
                </label>

                {{-- Option 3: VIEWER --}}
                <label class="role-option-card relative flex items-start gap-3 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-[#7367f0] dark:hover:border-[#7367f0] cursor-pointer transition-all bg-white dark:bg-gray-800" id="role-card-viewer">
                    <input type="radio" name="role" value="viewer" class="mt-0.5 text-[#7367f0] focus:ring-[#7367f0]" onchange="highlightRoleCard('viewer')">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                <i class="fa-solid fa-database text-xs"></i> VIEWER
                            </span>
                            <span class="text-[10px] px-2 py-0.2 rounded bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 font-medium">ผู้ดูข้อมูล</span>
                        </div>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                            ดูข้อมูลได้อย่างเดียว ไม่สามารถเพิ่ม แก้ไข หรือลบข้อมูลใดๆ ได้
                        </p>
                    </div>
                </label>
            </div>

            {{-- Modal Actions --}}
            <div class="pt-3 border-t border-gray-100 dark:border-gray-700/80 flex justify-end items-center gap-2.5">
                <button type="button" onclick="closeRoleModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                    ยกเลิก
                </button>
                <button type="submit" id="btn-save-role" class="px-5 py-2 bg-[#7367f0] hover:bg-[#685dd8] text-white rounded-lg text-xs font-semibold shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-check"></i>
                    <span>บันทึกสิทธิ์</span>
                </button>
            </div>
        </form>

    </div>
</div>
        </div>
    </div>
</div>

{{-- JavaScript Helpers --}}
<script>
    const canAssignAdmin = {{ Auth::check() && Auth::user()->canAssignAdminRole() ? 'true' : 'false' }};

    function updateParam(key, val) {
        const url = new URL(window.location.href);
        if (val) {
            url.searchParams.set(key, val);
        } else {
            url.searchParams.delete(key);
        }
        url.searchParams.set('page', '1');
        window.location.href = url.toString();
    }

    function openRoleModal(userId, fullname, empCode, currentRole, dept) {
        document.getElementById('modal-user-id').value = userId;
        document.getElementById('modal-fullname').innerText = fullname;
        document.getElementById('modal-emp-code').innerText = empCode;
        document.getElementById('modal-dept').innerText = dept || '—';
        document.getElementById('modal-avatar').innerText = (fullname.charAt(0) || 'U').toUpperCase();

        const roleRadio = document.querySelector(`input[name="role"][value="${currentRole}"]`);
        if (roleRadio) {
            roleRadio.checked = true;
            highlightRoleCard(currentRole);
        }

        // Restrict admin choice if current user cannot assign admin
        const adminRadio = document.querySelector('input[name="role"][value="admin"]');
        const adminWarning = document.getElementById('admin-grant-warning');
        const adminCard = document.getElementById('role-card-admin');

        if (!canAssignAdmin && currentRole !== 'admin') {
            adminRadio.disabled = true;
            adminCard.classList.add('opacity-50', 'cursor-not-allowed', 'bg-gray-50');
            adminWarning.classList.remove('hidden');
        } else {
            adminRadio.disabled = false;
            adminCard.classList.remove('opacity-50', 'cursor-not-allowed', 'bg-gray-50');
            adminWarning.classList.add('hidden');
        }

        document.getElementById('role-modal').classList.remove('hidden');
    }

    function closeRoleModal() {
        document.getElementById('role-modal').classList.add('hidden');
    }

    function highlightRoleCard(selectedRole) {
        ['admin', 'editor', 'viewer'].forEach(r => {
            const card = document.getElementById(`role-card-${r}`);
            if (r === selectedRole) {
                card.classList.add('border-[#7367f0]', 'ring-1', 'ring-[#7367f0]/40', 'bg-purple-50/20');
            } else {
                card.classList.remove('border-[#7367f0]', 'ring-1', 'ring-[#7367f0]/40', 'bg-purple-50/20');
            }
        });
    }

    async function submitRoleForm(event) {
        event.preventDefault();
        const userId = document.getElementById('modal-user-id').value;
        const selectedRadio = document.querySelector('input[name="role"]:checked');
        if (!selectedRadio) {
            Swal.fire({ icon: 'warning', title: 'กรุณาเลือกสิทธิ์', text: 'กรุณาเลือกระดับสิทธิ์ที่ต้องการกำหนด' });
            return;
        }

        const newRole = selectedRadio.value;
        const btn = document.getElementById('btn-save-role');
        const originalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังบันทึก...';

        try {
            const res = await fetch(`/backend/users/${userId}/change-role`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ role: newRole })
            });

            const data = await res.json();
            if (res.ok && data.success) {
                closeRoleModal();

                // Live update the table cell
                const cell = document.getElementById(`role-cell-${userId}`);
                if (cell && data.role_html) {
                    cell.innerHTML = data.role_html;
                }

                // Show Toast Notification
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message || 'ปรับสิทธิ์เรียบร้อยแล้ว',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                } else {
                    alert(data.message || 'ปรับสิทธิ์เรียบร้อยแล้ว');
                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'ไม่สามารถปรับสิทธิ์ได้',
                    text: data.message || 'เกิดข้อผิดพลาดในการปรับสิทธิ์'
                });
            }
        } catch (err) {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้'
            });
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
        }
    }

    // =========================================================================
    // Live Search & AJAX Table Refreshing (รีเฉพาะตาราง)
    // =========================================================================
    let currentAbortController = null;
    let searchDebounceTimer = null;

    function applyLiveFilter(resetPage = false, targetPage = null) {
        clearTimeout(searchDebounceTimer);

        if (currentAbortController) {
            currentAbortController.abort();
        }
        currentAbortController = new AbortController();

        const tableWrapper = document.getElementById('users-table-wrapper');
        const searchSpinner = document.getElementById('search-spinner');
        const clearBtn = document.getElementById('clear-search-btn');
        const searchInput = document.getElementById('live-search-input');
        const roleSelect = document.getElementById('filter-role');
        const deptSelect = document.getElementById('filter-department');
        const statusSelect = document.getElementById('filter-status');
        const perPageSelect = document.getElementById('per-page-select');

        const keywordVal = searchInput ? searchInput.value.trim() : '';
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', !keywordVal);
        }
        if (searchSpinner) {
            searchSpinner.classList.remove('hidden');
            if (clearBtn && keywordVal) clearBtn.classList.add('hidden');
        }
        if (tableWrapper) {
            tableWrapper.classList.add('opacity-40', 'pointer-events-none');
        }

        const params = new URLSearchParams();
        if (keywordVal) params.set('keyword', keywordVal);
        if (roleSelect && roleSelect.value) params.set('role', roleSelect.value);
        if (deptSelect && deptSelect.value) params.set('department', deptSelect.value);
        if (statusSelect && statusSelect.value !== '') params.set('status', statusSelect.value);
        if (perPageSelect && perPageSelect.value) params.set('per_page', perPageSelect.value);

        if (!resetPage && targetPage) {
            params.set('page', targetPage);
        }

        // Toggle reset filters button visibility
        const resetFilterLink = document.getElementById('reset-filters-link');
        if (resetFilterLink) {
            const hasFilters = Boolean(keywordVal || (roleSelect && roleSelect.value) || (deptSelect && deptSelect.value) || (statusSelect && statusSelect.value !== ''));
            resetFilterLink.classList.toggle('hidden', !hasFilters);
        }

        const queryString = params.toString();
        const fetchUrl = `{{ route('users.index') }}${queryString ? '?' + queryString : ''}`;

        fetch(fetchUrl, {
            signal: currentAbortController.signal,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.text();
        })
        .then(html => {
            if (tableWrapper) {
                tableWrapper.innerHTML = html;
            }
            // Update browser URL query string without reloading page
            window.history.replaceState(null, '', fetchUrl);
        })
        .catch(err => {
            if (err.name !== 'AbortError') {
                console.error('Error fetching users table:', err);
            }
        })
        .finally(() => {
            if (tableWrapper) {
                tableWrapper.classList.remove('opacity-40', 'pointer-events-none');
            }
            if (searchSpinner) {
                searchSpinner.classList.add('hidden');
            }
            if (clearBtn && keywordVal) {
                clearBtn.classList.remove('hidden');
            }
        });
    }

    function clearLiveSearch() {
        const searchInput = document.getElementById('live-search-input');
        if (searchInput) {
            searchInput.value = '';
            searchInput.focus();
        }
        applyLiveFilter(true);
    }

    function resetAllFilters() {
        const searchInput = document.getElementById('live-search-input');
        const roleSelect = document.getElementById('filter-role');
        const deptSelect = document.getElementById('filter-department');
        const statusSelect = document.getElementById('filter-status');
        const perPageSelect = document.getElementById('per-page-select');

        if (searchInput) searchInput.value = '';
        if (roleSelect) roleSelect.value = '';
        if (deptSelect) deptSelect.value = '';
        if (statusSelect) statusSelect.value = '';
        if (perPageSelect) perPageSelect.value = '50';

        applyLiveFilter(true);
    }

    // Attach listeners on page load
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('live-search-input');
        if (searchInput) {
            // Live typing search with debounce
            searchInput.addEventListener('input', () => {
                const clearBtn = document.getElementById('clear-search-btn');
                if (clearBtn) clearBtn.classList.toggle('hidden', !searchInput.value.trim());

                clearTimeout(searchDebounceTimer);
                searchDebounceTimer = setTimeout(() => {
                    applyLiveFilter(true);
                }, 300);
            });

            // Enter key searches immediately
            searchInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    clearTimeout(searchDebounceTimer);
                    applyLiveFilter(true);
                }
            });
        }

        // Intercept pagination clicks within the table wrapper to keep it AJAX
        document.addEventListener('click', (e) => {
            const paginationLink = e.target.closest('#users-table-wrapper .pagination a, #pagination-links-container a, #users-table-wrapper nav a');
            if (paginationLink && paginationLink.href) {
                e.preventDefault();
                try {
                    const url = new URL(paginationLink.href);
                    const page = url.searchParams.get('page');
                    if (page) {
                        applyLiveFilter(false, page);
                    }
                } catch (err) {
                    console.error(err);
                }
            }
        });
    });

    // Close on Escape or click outside
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeRoleModal();
    });
    document.getElementById('role-modal')?.addEventListener('click', (e) => {
        if (e.target === document.getElementById('role-modal')) closeRoleModal();
    });
</script>

@endsection