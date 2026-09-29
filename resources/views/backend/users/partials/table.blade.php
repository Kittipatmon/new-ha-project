{{-- Table Container --}}
<div class="overflow-x-auto w-full border-t border-gray-100 dark:border-gray-700/60 min-h-[220px] pb-16">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider bg-gray-50/40 dark:bg-gray-900/20">
                <th class="py-3.5 px-4 font-bold">USER</th>
                <th class="py-3.5 px-4 font-bold">EMAIL</th>
                <th class="py-3.5 px-4 font-bold">ROLE</th>
                <th class="py-3.5 px-4 font-bold">PLAN</th>
                <th class="py-3.5 px-4 font-bold">STATUS</th>
                <th class="py-3.5 px-4 font-bold text-center">ACTIONS</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60 text-xs">
            @php
                $pastelColors = [
                    'bg-purple-100 text-purple-600 dark:bg-purple-950/60 dark:text-purple-300',
                    'bg-sky-100 text-sky-600 dark:bg-sky-950/60 dark:text-sky-300',
                    'bg-pink-100 text-pink-600 dark:bg-pink-950/60 dark:text-pink-300',
                    'bg-indigo-100 text-indigo-600 dark:bg-indigo-950/60 dark:text-indigo-300',
                    'bg-blue-100 text-blue-600 dark:bg-blue-950/60 dark:text-blue-300',
                    'bg-teal-100 text-teal-600 dark:bg-teal-950/60 dark:text-teal-300',
                    'bg-amber-100 text-amber-600 dark:bg-amber-950/60 dark:text-amber-300',
                ];
            @endphp

            @forelse($users as $index => $user)
                @php
                    $firstLetter = mb_substr($user->firstname ?? '', 0, 1);
                    $secondLetter = mb_substr($user->lastname ?? '', 0, 1);
                    $initials = ($firstLetter || $secondLetter) ? mb_strtoupper($firstLetter . $secondLetter) : 'US';
                    $colorClass = $pastelColors[$index % count($pastelColors)];
                    $email = $user->email ?? (strtolower($user->firstname) ? strtolower($user->firstname) . '.' . strtolower(substr($user->lastname ?? '', 0, 2)) . '@kumwell.com' : '—');
                    $deptName = $user->department?->department_name ?: ($user->department?->department_fullname ?: '—');
                    $userRole = strtolower($user->hr_role ?? 'viewer');
                @endphp
                <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition-colors" id="user-row-{{ $user->id }}">
                    {{-- 1. USER --}}
                    <td class="py-3.5 px-4">
                        <div class="flex items-center gap-3">
                            @if($user->photo_user && file_exists(public_path($user->photo_user)))
                                <img src="{{ asset($user->photo_user) }}" class="w-9 h-9 rounded-full object-cover shrink-0 ring-1 ring-gray-200 dark:ring-gray-700" alt="{{ $user->fullname }}">
                            @else
                                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs shrink-0 {{ $colorClass }}">
                                    {{ $initials }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <a href="{{ route('users.show', $user->id) }}" class="font-bold text-gray-800 dark:text-gray-100 text-xs hover:text-[#7367f0] transition-colors block truncate">
                                    {{ $user->fullname }}
                                </a>
                                <div class="text-[11px] text-gray-400 font-normal">
                                    {{ '@' . ($user->username ?: $user->emp_code) }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- 2. EMAIL --}}
                    <td class="py-3.5 px-4">
                        <span class="font-normal text-gray-500 dark:text-gray-400 text-xs">
                            {{ $email }}
                        </span>
                    </td>

                    {{-- 3. ROLE (Icon + Label matching Screenshot) --}}
                    <td class="py-3.5 px-4" id="role-cell-{{ $user->id }}">
                        @if($userRole === 'admin')
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-purple-600 dark:text-purple-400">
                                <i class="fa-solid fa-shield-halved text-purple-600 text-sm"></i>
                                <span>Admin</span>
                            </span>
                        @elseif($userRole === 'editor')
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-cyan-600 dark:text-cyan-400">
                                <i class="fa-solid fa-pen text-cyan-500 text-xs"></i>
                                <span>Editor</span>
                            </span>
                        @else
                            <span class="inline-flex items-center gap-2 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                                <i class="fa-solid fa-database text-emerald-500 text-xs"></i>
                                <span>Viewer</span>
                            </span>
                        @endif
                    </td>

                    {{-- 4. PLAN (Department) --}}
                    <td class="py-3.5 px-4">
                        <span class="text-xs text-gray-600 dark:text-gray-300 font-medium">
                            {{ $deptName }}
                        </span>
                    </td>

                    {{-- 5. STATUS --}}
                    <td class="py-3.5 px-4">
                        @if((string)$user->status === '1' || strtolower($user->status_label) === 'ใช้งาน' || strtolower($user->status_label) === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/40">
                                active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                inactive
                            </span>
                        @endif
                    </td>

                    {{-- 6. ACTIONS (⋮ Dropdown) --}}
                    <td class="py-3.5 px-4 text-center">
                        <div class="dropdown dropdown-end inline-block">
                            <button type="button" tabindex="0" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                            </button>
                            <ul tabindex="0" class="dropdown-content z-30 menu p-1.5 shadow-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl w-48 text-xs space-y-0.5 mt-1">
                                {{-- 1. ดูข้อมูล --}}
                                <li>
                                    <a href="{{ route('users.show', $user->id) }}" class="flex items-center gap-2 text-gray-700 dark:text-gray-200 hover:text-[#7367f0] py-2">
                                        <i class="fa-solid fa-eye text-sky-500 w-4"></i>
                                        <span>ดูข้อมูลพนักงาน</span>
                                    </a>
                                </li>

                                {{-- 2. ปรับสิทธิ์ (Role: Admin / Editor / Viewer) --}}
                                @if(Auth::check() && Auth::user()->canManageUsers())
                                <li>
                                    <button type="button" onclick="openRoleModal({{ $user->id }}, '{{ addslashes($user->fullname) }}', '{{ $user->employee_code }}', '{{ $userRole }}', '{{ addslashes($deptName) }}')" class="flex items-center gap-2 text-[#7367f0] dark:text-purple-400 hover:bg-purple-50 dark:hover:bg-purple-950/40 py-2 w-full text-left font-semibold">
                                        <i class="fa-solid fa-shield-halved text-[#7367f0] w-4"></i>
                                        <span>ปรับสิทธิ์ระบบ (Role)</span>
                                    </button>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-10 text-center text-gray-400 dark:text-gray-500">
                        <div class="flex flex-col items-center justify-center space-y-2">
                            <i class="fa-solid fa-users text-4xl opacity-30"></i>
                            <p class="text-sm font-medium">ไม่พบข้อมูลพนักงานที่ตรงกับเงื่อนไขการค้นหา</p>
                            <button type="button" onclick="resetAllFilters()" class="text-xs text-[#7367f0] hover:underline cursor-pointer">รีเซ็ตตัวกรองทั้งหมด</button>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination & Total Count Footer (Matching Screenshot Bottom) --}}
<div class="mt-5 flex flex-col sm:flex-row justify-between items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/60 text-xs text-gray-500 dark:text-gray-400 font-medium">
    <div>
        {{ $users->total() }} total (แสดง {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} จาก {{ $users->total() }})
    </div>
    <div id="pagination-links-container">
        {{ $users->links() }}
    </div>
</div>
