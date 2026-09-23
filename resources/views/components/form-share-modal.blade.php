<!-- Form Share Modal Component -->
<div id="formShareModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity" onclick="closeShareModal()"></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all w-full max-w-xl border border-gray-100 dark:border-gray-700">
            
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-sky-500 to-indigo-600 px-6 py-4 text-white flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold leading-tight" id="shareModalTitle">แชร์และกำหนดสิทธิ์การดูเอกสาร</h3>
                        <p class="text-xs text-sky-100" id="shareModalSubtitle">กำหนดบุคคลหรือแผนกที่มีสิทธิ์เข้าถึงเอกสารนี้</p>
                    </div>
                </div>
                <button type="button" onclick="closeShareModal()" class="text-white/80 hover:text-white transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Tab Buttons -->
            <div class="flex border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 px-6 pt-3 gap-2">
                <button type="button" onclick="switchShareTab('share')" id="tabBtnShare" class="pb-3 px-2 text-xs sm:text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-share-nodes text-xs"></i>
                    <span>กำหนดการแชร์</span>
                </button>
                <button type="button" onclick="switchShareTab('history')" id="tabBtnHistory" class="pb-3 px-2 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                    <span>ประวัติ & สิทธิ์การเข้าถึง (<span id="shareCountBadge">0</span>)</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                
                <!-- TAB 1: Share Form -->
                <div id="tabContentShare" class="space-y-4">
                    
                    <!-- Section: แชร์ให้ใคร (Share target mode switch) -->
                    <div class="bg-gray-50/80 dark:bg-gray-750/50 p-3.5 rounded-2xl border border-gray-100 dark:border-gray-700/80 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-gray-800 dark:text-gray-200 flex items-center gap-1.5">
                                <i class="fa-solid fa-users text-indigo-500"></i>
                                <span>แชร์ให้ใคร (เลือกรูปแบบผู้รับสิทธิ์) <span class="text-red-500">*</span></span>
                            </label>
                            <span class="text-[11px] text-gray-400" id="shareModeBadge">เลือกได้หลายรายการ</span>
                        </div>

                        <!-- Segmented Switch: รายบุคคล / ทั้งแผนก / ทุกคนในระบบ -->
                        <div class="grid grid-cols-3 gap-1.5 bg-gray-200/80 dark:bg-gray-700 p-1 rounded-xl">
                            <button type="button" onclick="switchShareTargetMode('user')" id="btnTargetModeUser"
                                class="py-2 px-2 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-1.5 bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-xs">
                                <i class="fa-solid fa-user text-[11px]"></i>
                                <span>ระบุรายบุคคล</span>
                            </button>
                            <button type="button" onclick="switchShareTargetMode('dept')" id="btnTargetModeDept"
                                class="py-2 px-2 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-1.5 text-gray-600 dark:text-gray-300 hover:text-gray-900">
                                <i class="fa-solid fa-building text-[11px]"></i>
                                <span>ระบุทั้งแผนก</span>
                            </button>
                            <button type="button" onclick="switchShareTargetMode('all')" id="btnTargetModeAll"
                                class="py-2 px-2 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-1.5 text-gray-600 dark:text-gray-300 hover:text-gray-900">
                                <i class="fa-solid fa-globe text-[11px]"></i>
                                <span>ทุกคนในระบบ</span>
                            </button>
                        </div>

                        <!-- Sub-view 1: รายบุคคล (Search Autocomplete) -->
                        <div id="targetModeSectionUser" class="space-y-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </div>
                                <input type="text" id="shareTargetSearch" autocomplete="off"
                                    class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 block w-full pl-9 pr-3 py-2.5 transition"
                                    placeholder="พิมพ์ชื่อ, นามสกุล หรือ รหัสพนักงาน...">
                                
                                <!-- Search Dropdown Results -->
                                <div id="searchResultsDropdown" class="absolute left-0 right-0 top-full mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl max-h-60 overflow-y-auto hidden z-30 divide-y divide-gray-100 dark:divide-gray-700">
                                    <!-- Results dynamically populated -->
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-400 flex items-center gap-1">
                                <i class="fa-regular fa-lightbulb text-[10px] text-amber-500"></i>
                                <span>แสดงเฉพาะพนักงานในแผนกเดียวกับเอกสาร พิมพ์เพื่อค้นหา</span>
                            </p>
                        </div>

                        <!-- Sub-view 2: ทั้งแผนก (Department Select) -->
                        <div id="targetModeSectionDept" class="space-y-2 hidden">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-sky-500">
                                    <i class="fa-regular fa-building text-xs"></i>
                                </div>
                                <select id="deptQuickSelect" onchange="onSelectDepartment(this)"
                                    class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white text-xs sm:text-sm rounded-xl focus:ring-2 focus:ring-sky-500 block w-full pl-9 pr-8 py-2.5 transition">
                                    <option value="">-- คลิกเลือกแผนกที่ต้องการให้สิทธิ์เข้าถึง --</option>
                                </select>
                            </div>
                            <p class="text-[11px] text-gray-400 flex items-center gap-1" id="deptShareHintText">
                                <i class="fa-solid fa-circle-info text-[10px] text-sky-500"></i>
                                <span>พนักงานทุกคนที่สังกัดในแผนกที่เลือก จะสามารถเปิดดูเอกสารนี้ได้</span>
                            </p>
                        </div>

                        <!-- Sub-view 3: ทุกคนในระบบ (Public in organization) -->
                        <div id="targetModeSectionAll" class="p-3 bg-indigo-50/70 dark:bg-indigo-950/30 rounded-xl border border-indigo-100 dark:border-indigo-800/50 space-y-2 hidden">
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center shrink-0 mt-0.5">
                                    <i class="fa-solid fa-globe text-xs"></i>
                                </div>
                                <div class="text-xs">
                                    <div class="font-bold text-indigo-900 dark:text-indigo-200">เปิดสิทธิ์ให้พนักงานทุกคนในระบบ</div>
                                    <div class="text-gray-600 dark:text-gray-400 text-[11.5px] mt-0.5">
                                        พนักงานทุกคนที่มีบัญชีในระบบและล็อกอินอยู่ จะสามารถเปิดดูเอกสารนี้ผ่านลิงก์ได้ทันที
                                    </div>
                                </div>
                            </div>
                            <button type="button" onclick="selectShareTarget(0, 'ทุกคนในระบบ (พนักงานทั้งหมด)', '', 'all')"
                                class="w-full py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-xs transition flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-check"></i>
                                <span>ยืนยันเลือก: แชร์ให้ทุกคนในระบบ</span>
                            </button>
                        </div>

                    </div>

                    <!-- Selected Target Tags Container -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300">
                                รายชื่อ/แผนกที่เลือกรับสิทธิ์ (<span id="selectedTargetsCount">0</span>):
                            </label>
                            <button type="button" onclick="clearAllSelectedTargets()" id="btnClearAllTargets" class="text-[11px] text-red-500 hover:underline hidden">
                                ล้างทั้งหมด
                            </button>
                        </div>
                        <div id="selectedTargetsContainer" class="flex flex-wrap gap-1.5 min-h-[42px] p-2.5 bg-gray-50 dark:bg-gray-750 rounded-xl border border-dashed border-gray-200 dark:border-gray-600">
                            <span class="text-xs text-gray-400 self-center" id="noTargetPlaceholder">
                                <i class="fa-solid fa-arrow-up text-[10px] mr-1"></i>ยังไม่ได้เลือกผู้รับสิทธิ์ (เลือกจากด้านบน)
                            </span>
                        </div>
                    </div>

                    <!-- Share Channel & Security Notice -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">ช่องทางการแชร์</label>
                            <select id="shareChannelSelect" class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-indigo-500 block w-full p-2.5">
                                <option value="link">คัดลอกลิงก์ (Direct Link)</option>
                                <option value="email">อีเมลภายใน (Email)</option>
                                <option value="chat">แชทไลน์/ระบบ (Chat)</option>
                                <option value="internal">งานภายใน (Internal)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">การรักษาความปลอดภัย</label>
                            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/30 p-2.5 rounded-xl border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-shield-halved shrink-0"></i>
                                <span class="leading-tight">เฉพาะผู้ได้รับสิทธิ์เท่านั้นที่เปิดดูได้ (403 Forbidden)</span>
                            </div>
                        </div>
                    </div>

                    <!-- Note / Remarks -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            หมายเหตุหรือเหตุผลการแชร์ (ถ้ามี)
                        </label>
                        <textarea id="shareNoteInput" rows="2" class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white text-xs rounded-xl focus:ring-2 focus:ring-indigo-500 block w-full p-2.5 resize-none" placeholder="เช่น ส่งต่อให้ตรวจสอบข้อมูลเพิ่มเติม, แจ้งเพื่อทราบ..."></textarea>
                    </div>

                    <!-- Share Link Copy Bar -->
                    <div class="pt-1">
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            ลิงก์ของเอกสารนี้:
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="shareDirectLinkInput" readonly class="bg-gray-100 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 text-xs rounded-xl block flex-1 p-2 font-mono">
                            <button type="button" onclick="copyShareDirectLink()" class="px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-xl transition flex items-center gap-1 shrink-0">
                                <i class="fa-regular fa-copy"></i>
                                <span>คัดลอก</span>
                            </button>
                        </div>
                        <p class="text-[10.5px] text-amber-600 dark:text-amber-400 mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-triangle-exclamation text-[10px] shrink-0"></i>
                            <span>หากบุคคลอื่นที่ไม่มีสิทธิ์เข้าถึงลิงก์นี้ ระบบจะไม่อนุญาตให้เปิดดูเอกสาร (403 Forbidden)</span>
                        </p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" onclick="closeShareModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-xl transition">
                            ยกเลิก
                        </button>
                        <button type="button" onclick="submitShareForm()" id="btnSubmitShare" class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-600 hover:to-indigo-700 text-white text-xs font-bold rounded-xl shadow-md hover:shadow-lg transition">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span>บันทึกการแชร์</span>
                        </button>
                    </div>

                </div>

                <!-- TAB 2: Share History -->
                <div id="tabContentHistory" class="space-y-3 hidden">
                    <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center justify-between mb-2">
                        <span>รายการบันทึกและสิทธิ์การเข้าถึงเอกสารนี้</span>
                        <button type="button" onclick="loadShareHistory()" class="text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 font-medium">
                            <i class="fa-solid fa-rotate-right text-[10px]"></i> รีเฟรช
                        </button>
                    </div>

                    <div id="shareHistoryList" class="max-h-72 overflow-y-auto space-y-2 pr-1">
                        <!-- History rows injected here -->
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<script>
    let currentShareFormType = '';
    let currentShareFormId = 0;
    let currentTargetMode = 'user'; // 'user' | 'dept' | 'all'
    let selectedShareUsers = new Map(); // id -> { id, name, code, type }
    let cachedDepartmentsList = [];

    function openShareModal(formType, formId, title = '') {
        currentShareFormType = formType;
        currentShareFormId = formId;
        selectedShareUsers.clear();
        updateSelectedTargetsUI();

        if (title) {
            document.getElementById('shareModalTitle').innerText = 'แชร์เอกสาร: ' + title;
        }

        // Set document direct link
        let directUrl = '';
        if (formType === 'manpower_request') {
            directUrl = window.location.origin + '/manpower-request/show/' + formId;
        } else if (formType === 'probation_evaluation') {
            directUrl = window.location.origin + '/probation-evaluation/show/' + formId;
        } else if (formType === 'interview_evaluation') {
            directUrl = window.location.origin + '/interview-evaluation/show/' + formId;
        } else {
            directUrl = window.location.origin + '/manpower-request/show/' + formId;
        }
        document.getElementById('shareDirectLinkInput').value = directUrl;

        // Reset search & fields
        document.getElementById('shareTargetSearch').value = '';
        document.getElementById('searchResultsDropdown').classList.add('hidden');
        document.getElementById('shareNoteInput').value = '';

        // Switch to share tab & default mode 'user'
        switchShareTab('share');
        switchShareTargetMode('user');

        // Preload departments for select
        loadDepartmentsList();

        // Preload share history count
        loadShareHistory(false);

        // Show modal
        document.getElementById('formShareModal').classList.remove('hidden');
    }

    function closeShareModal() {
        document.getElementById('formShareModal').classList.add('hidden');
    }

    function switchShareTab(tab) {
        const tabShare = document.getElementById('tabContentShare');
        const tabHist = document.getElementById('tabContentHistory');
        const btnShare = document.getElementById('tabBtnShare');
        const btnHist = document.getElementById('tabBtnHistory');

        if (tab === 'share') {
            tabShare.classList.remove('hidden');
            tabHist.classList.add('hidden');
            btnShare.className = "pb-3 px-2 text-xs sm:text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 transition flex items-center gap-1.5";
            btnHist.className = "pb-3 px-2 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition flex items-center gap-1.5";
        } else {
            tabShare.classList.add('hidden');
            tabHist.classList.remove('hidden');
            btnHist.className = "pb-3 px-2 text-xs sm:text-sm font-semibold border-b-2 border-indigo-600 text-indigo-600 dark:text-indigo-400 transition flex items-center gap-1.5";
            btnShare.className = "pb-3 px-2 text-xs sm:text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition flex items-center gap-1.5";
            loadShareHistory(true);
        }
    }

    let canShareOtherDepartments = true;

    function switchShareTargetMode(mode) {
        if ((mode === 'dept' || mode === 'all') && !canShareOtherDepartments) {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่มีสิทธิ์ใช้งานตัวเลือกนี้',
                text: 'สิทธิ์การแชร์ระบุทั้งแผนก และการแชร์ให้ทุกคนในระบบ อนุญาตเฉพาะ Admin และ Editor เท่านั้น',
            });
            return;
        }

        currentTargetMode = mode;
        const btnUser = document.getElementById('btnTargetModeUser');
        const btnDept = document.getElementById('btnTargetModeDept');
        const btnAll = document.getElementById('btnTargetModeAll');

        const secUser = document.getElementById('targetModeSectionUser');
        const secDept = document.getElementById('targetModeSectionDept');
        const secAll = document.getElementById('targetModeSectionAll');

        const activeClass = "py-2 px-2 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-1.5 bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-xs";
        const inactiveClass = "py-2 px-2 text-xs font-semibold rounded-lg transition-all flex items-center justify-center gap-1.5 text-gray-600 dark:text-gray-300 hover:text-gray-900";

        btnUser.className = (mode === 'user') ? activeClass : inactiveClass;
        btnDept.className = (mode === 'dept') ? activeClass : inactiveClass;
        btnAll.className = (mode === 'all') ? activeClass : inactiveClass;

        secUser.classList.toggle('hidden', mode !== 'user');
        secDept.classList.toggle('hidden', mode !== 'dept');
        secAll.classList.toggle('hidden', mode !== 'all');

        if (mode === 'user') {
            setTimeout(() => document.getElementById('shareTargetSearch').focus(), 50);
        }
    }

    function loadDepartmentsList() {
        const select = document.getElementById('deptQuickSelect');
        select.innerHTML = '<option value="">-- กำลังโหลดรายการแผนก... --</option>';

        fetch(`/form-shares/search-targets?q=&form_type=${encodeURIComponent(currentShareFormType)}&form_id=${encodeURIComponent(currentShareFormId)}`)
            .then(res => res.json())
            .then(data => {
                const depts = data.departments || [];
                const canShareOther = !!data.can_share_other_depts;
                canShareOtherDepartments = canShareOther;
                const filteredDept = data.filtered_dept;

                renderDepartmentOptions(depts, canShareOther, filteredDept);

                // Update segmented switch buttons: disable 'dept' and 'all' for non-admin/editor (normal viewers)
                const btnDept = document.getElementById('btnTargetModeDept');
                const btnAll = document.getElementById('btnTargetModeAll');

                if (!canShareOther) {
                    if (btnDept) {
                        btnDept.disabled = true;
                        btnDept.title = 'สิทธิ์ระบุทั้งแผนก เฉพาะ Admin และ Editor เท่านั้น';
                        btnDept.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
                    }
                    if (btnAll) {
                        btnAll.disabled = true;
                        btnAll.title = 'สิทธิ์แชร์ให้ทุกคนในระบบ เฉพาะ Admin และ Editor เท่านั้น';
                        btnAll.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
                    }
                    // Force switch to 'user' mode
                    switchShareTargetMode('user');
                } else {
                    if (btnDept) {
                        btnDept.disabled = false;
                        btnDept.title = '';
                        btnDept.classList.remove('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
                    }
                    if (btnAll) {
                        btnAll.disabled = false;
                        btnAll.title = '';
                        btnAll.classList.remove('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
                    }
                }
            })
            .catch(() => {
                select.innerHTML = '<option value="">-- โหลดรายการแผนกล้มเหลว --</option>';
            });
    }

    function renderDepartmentOptions(depts, canShareOther = true, filteredDept = '') {
        const select = document.getElementById('deptQuickSelect');
        const hintEl = document.getElementById('deptShareHintText');

        select.innerHTML = '<option value="">-- คลิกเลือกแผนกที่ต้องการให้สิทธิ์เข้าถึง --</option>';
        depts.forEach(d => {
            const opt = document.createElement('option');
            opt.value = d.id;
            opt.dataset.name = d.name;
            opt.dataset.isDocDept = d.is_doc_dept ? '1' : '0';

            let label = d.fullname ? `${d.name} (${d.fullname})` : d.name;

            if (d.is_doc_dept) {
                label += ' ★ (แผนกของเอกสารนี้)';
            }

            if (!canShareOther && !d.is_doc_dept) {
                opt.disabled = true;
                label += ' - [ไม่อนุญาต: เฉพาะ Admin/Editor]';
            }

            opt.textContent = label;
            select.appendChild(opt);
        });

        if (hintEl) {
            if (!canShareOther) {
                hintEl.innerHTML = `
                    <i class="fa-solid fa-lock text-[10px] text-amber-500"></i>
                    <span class="text-amber-600 dark:text-amber-400 font-medium">จำกัดเฉพาะแผนกของเอกสาร (${filteredDept || 'แผนกต้นสังกัด'}) — สิทธิ์แชร์ข้ามแผนกเฉพาะ Admin / Editor</span>
                `;
            } else {
                hintEl.innerHTML = `
                    <i class="fa-solid fa-circle-info text-[10px] text-sky-500"></i>
                    <span>พนักงานทุกคนที่สังกัดในแผนกที่เลือก จะสามารถเปิดดูเอกสารนี้ได้</span>
                `;
            }
        }
    }

    function onSelectDepartment(selectEl) {
        const deptId = selectEl.value;
        if (!deptId) return;

        const selectedOption = selectEl.options[selectEl.selectedIndex];
        const isDocDept = selectedOption.dataset.isDocDept === '1';

        if (!canShareOtherDepartments && !isDocDept) {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่สามารถเลือกแผนกนี้ได้',
                text: 'คุณสามารถแชร์ได้เฉพาะแผนกต้นสังกัดของเอกสารเท่านั้น (สิทธิ์แชร์ข้ามแผนกเฉพาะ Admin และ Editor เท่านั้น)',
            });
            selectEl.value = '';
            return;
        }

        const deptName = selectedOption.dataset.name || selectedOption.textContent;
        const deptCode = selectedOption.dataset.code || '';

        selectShareTarget(deptId, deptName, deptCode, 'dept');
        selectEl.value = ''; // Reset select back to placeholder
    }

    // Search target autocomplete for individual users
    let searchDebounceTimer = null;
    
    function fetchAndRenderTargets(query = '') {
        const dropdown = document.getElementById('searchResultsDropdown');
        dropdown.innerHTML = `<div class="p-3 text-xs text-gray-400 text-center"><i class="fa-solid fa-spinner fa-spin mr-1"></i> กำลังค้นหา...</div>`;
        dropdown.classList.remove('hidden');

        fetch(`/form-shares/search-targets?q=${encodeURIComponent(query)}&form_type=${encodeURIComponent(currentShareFormType)}&form_id=${encodeURIComponent(currentShareFormId)}`)
            .then(res => res.json())
            .then(data => {
                dropdown.innerHTML = '';
                let total = 0;

                if (data.users && data.users.length > 0) {
                    const deptLabel = data.filtered_dept ? ` — แผนก: ${data.filtered_dept}` : '';
                    dropdown.innerHTML += `<div class="px-3 py-1.5 text-[10.5px] font-bold text-gray-400 bg-gray-50 dark:bg-gray-900/50 uppercase tracking-wider flex justify-between">
                        <span>พนักงาน (${data.users.length} ท่าน)${deptLabel}</span>
                        <span class="text-[10px] lowercase text-gray-400">คลิกเพื่อเลือก</span>
                    </div>`;
                    data.users.forEach(u => {
                        total++;
                        const isAlreadySelected = selectedShareUsers.has('user_' + u.id);
                        const item = document.createElement('div');
                        item.className = `px-3 py-2 text-xs hover:bg-indigo-50 dark:hover:bg-indigo-950/40 cursor-pointer flex items-center justify-between transition ${isAlreadySelected ? 'bg-indigo-50/50 dark:bg-indigo-950/20 opacity-70' : ''}`;
                        item.innerHTML = `
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 rounded-full ${u.is_resigned ? 'bg-gray-200 text-gray-500' : 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300'} flex items-center justify-center text-[10px] font-bold shrink-0">
                                    ${u.name ? u.name.substring(0, 1) : 'U'}
                                </div>
                                <div class="min-w-0">
                                    <span class="font-medium text-gray-800 dark:text-gray-200 block truncate">${u.name}</span>
                                    <span class="text-[11px] text-gray-400 block">${u.code ? u.code : ''}${u.is_resigned ? ' <span class="text-red-400 font-semibold">(ลาออก)</span>' : ''}</span>
                                </div>
                            </div>
                            <span class="text-[10px] ${isAlreadySelected ? 'text-emerald-600 font-bold' : 'text-indigo-600 font-semibold'} shrink-0 ml-2">
                                ${isAlreadySelected ? '<i class="fa-solid fa-check"></i> เลือกแล้ว' : '+ เลือก'}
                            </span>
                        `;
                        item.onclick = () => selectShareTarget(u.id, u.name, u.code, 'user');
                        dropdown.appendChild(item);
                    });
                }

                if (total === 0) {
                    dropdown.innerHTML = `<div class="px-4 py-4 text-xs text-gray-400 text-center">ไม่พบรายชื่อที่ตรงกับ "${query}"</div>`;
                }
                dropdown.classList.remove('hidden');
            })
            .catch(() => {
                dropdown.innerHTML = `<div class="px-4 py-3 text-xs text-red-400 text-center">โหลดข้อมูลไม่สำเร็จ กรุณาลองใหม่อีกครั้ง</div>`;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('shareTargetSearch');
        if (!searchInput) return;

        // Show targets on click or focus
        searchInput.addEventListener('focus', function() {
            fetchAndRenderTargets(this.value.trim());
        });

        searchInput.addEventListener('click', function() {
            fetchAndRenderTargets(this.value.trim());
        });

        // Filter as user types
        searchInput.addEventListener('input', function() {
            clearTimeout(searchDebounceTimer);
            const val = this.value.trim();
            searchDebounceTimer = setTimeout(() => {
                fetchAndRenderTargets(val);
            }, 200);
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('searchResultsDropdown');
            const input = document.getElementById('shareTargetSearch');
            if (dropdown && !dropdown.contains(e.target) && e.target !== input) {
                dropdown.classList.add('hidden');
            }
        });
    });

    function selectShareTarget(id, name, code, type) {
        // If selecting 'all', clear other targets or just add it
        if (type === 'all') {
            selectedShareUsers.clear();
            selectedShareUsers.set('all_0', { id: 0, name: 'ทุกคนในระบบ (พนักงานทั้งหมด)', code: '', type: 'all' });
            updateSelectedTargetsUI();
            return;
        }

        // If 'all' was previously selected, remove it
        if (selectedShareUsers.has('all_0')) {
            selectedShareUsers.delete('all_0');
        }

        const key = type + '_' + id;
        selectedShareUsers.set(key, { id, name, code, type });
        updateSelectedTargetsUI();

        if (type === 'user') {
            document.getElementById('shareTargetSearch').value = '';
            document.getElementById('searchResultsDropdown').classList.add('hidden');
        }
    }

    function removeShareTarget(key) {
        selectedShareUsers.delete(key);
        updateSelectedTargetsUI();
    }

    function clearAllSelectedTargets() {
        selectedShareUsers.clear();
        updateSelectedTargetsUI();
    }

    function updateSelectedTargetsUI() {
        const container = document.getElementById('selectedTargetsContainer');
        const countBadge = document.getElementById('selectedTargetsCount');
        const btnClear = document.getElementById('btnClearAllTargets');

        if (countBadge) countBadge.innerText = selectedShareUsers.size;
        if (btnClear) btnClear.classList.toggle('hidden', selectedShareUsers.size === 0);

        container.innerHTML = '';

        if (selectedShareUsers.size === 0) {
            container.innerHTML = '<span class="text-xs text-gray-400 self-center" id="noTargetPlaceholder"><i class="fa-solid fa-arrow-up text-[10px] mr-1"></i>ยังไม่ได้เลือกผู้รับสิทธิ์ (เลือกจากด้านบน)</span>';
            return;
        }

        selectedShareUsers.forEach((val, key) => {
            const badge = document.createElement('div');
            
            if (val.type === 'all') {
                badge.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-bold shadow-xs';
                badge.innerHTML = `
                    <i class="fa-solid fa-globe text-xs"></i>
                    <span>${val.name}</span>
                    <button type="button" onclick="removeShareTarget('${key}')" class="hover:text-red-200 ml-1 transition">
                        <i class="fa-solid fa-xmark text-xs"></i>
                    </button>
                `;
            } else if (val.type === 'dept') {
                badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-200 text-xs font-semibold';
                badge.innerHTML = `
                    <i class="fa-regular fa-building text-[11px] text-sky-600 dark:text-sky-400"></i>
                    <span>แผนก: ${val.name}</span>
                    <button type="button" onclick="removeShareTarget('${key}')" class="hover:text-red-600 ml-1 transition">
                        <i class="fa-solid fa-xmark text-[11px]"></i>
                    </button>
                `;
            } else {
                badge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-200 text-xs font-semibold';
                badge.innerHTML = `
                    <i class="fa-regular fa-user text-[11px] text-indigo-600 dark:text-indigo-400"></i>
                    <span>${val.name}${val.code ? ' (' + val.code + ')' : ''}</span>
                    <button type="button" onclick="removeShareTarget('${key}')" class="hover:text-red-600 ml-1 transition">
                        <i class="fa-solid fa-xmark text-[11px]"></i>
                    </button>
                `;
            }
            container.appendChild(badge);
        });
    }

    function copyShareDirectLink() {
        const input = document.getElementById('shareDirectLinkInput');
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'คัดลอกลิงก์สำเร็จ!',
                text: 'ลิงก์นี้เปิดดูได้เฉพาะผู้ที่คุณได้แชร์สิทธิ์ให้เท่านั้น',
                timer: 2000,
                showConfirmButton: false
            });
        });
    }

    function submitShareForm() {
        if (selectedShareUsers.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'กรุณาเลือกผู้รับสิทธิ์',
                text: 'กรุณาเลือกบุคคลหรือแผนกที่คุณต้องการแชร์ให้ จากตัวเลือกด้านบน',
            });
            return;
        }

        const userIds = [];
        const deptIds = [];
        selectedShareUsers.forEach((val) => {
            if (val.type === 'all') {
                userIds.push(0); // 0 signifies all users
            } else if (val.type === 'user') {
                userIds.push(val.id);
            } else if (val.type === 'dept') {
                deptIds.push(val.id);
            }
        });

        const btn = document.getElementById('btnSubmitShare');
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> กำลังบันทึก...';

        fetch('/form-shares', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                form_type: currentShareFormType,
                form_id: currentShareFormId,
                user_ids: userIds,
                department_ids: deptIds,
                share_channel: document.getElementById('shareChannelSelect').value,
                note: document.getElementById('shareNoteInput').value,
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>บันทึกการแชร์</span>';

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'บันทึกสำเร็จ!',
                    text: data.message,
                    timer: 2200,
                    showConfirmButton: false
                });
                selectedShareUsers.clear();
                updateSelectedTargetsUI();
                document.getElementById('shareNoteInput').value = '';
                switchShareTab('history');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: data.message || 'ไม่สามารถแชร์เอกสารได้',
                });
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>บันทึกการแชร์</span>';
            Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด', text: err.message });
        });
    }

    function loadShareHistory(renderList = true) {
        if (!currentShareFormType || !currentShareFormId) return;

        fetch(`/form-shares/history?form_type=${currentShareFormType}&form_id=${currentShareFormId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const list = data.data || [];
                    const badge = document.getElementById('shareCountBadge');
                    if (badge) badge.innerText = list.length;

                    if (renderList) {
                        const container = document.getElementById('shareHistoryList');
                        container.innerHTML = '';

                        if (list.length === 0) {
                            container.innerHTML = '<div class="text-center py-8 text-xs text-gray-400"><i class="fa-regular fa-folder-open text-2xl mb-1 block"></i>ยังไม่มีประวัติการแชร์เอกสารนี้</div>';
                            return;
                        }

                        list.forEach(item => {
                            const row = document.createElement('div');
                            const isRevoked = item.is_revoked;
                            row.className = `p-3 rounded-xl ${isRevoked ? 'bg-red-50/50 dark:bg-red-950/20 border-red-100 dark:border-red-800/40 opacity-75' : 'bg-gray-50 dark:bg-gray-700/60 border-gray-100 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500'} border flex items-start justify-between gap-2 transition`;
                            
                            let iconHtml = '<i class="fa-solid fa-user text-indigo-500"></i>';
                            if (item.recipient_type === 'dept') {
                                iconHtml = '<i class="fa-solid fa-building text-sky-500"></i>';
                            } else if (item.recipient_type === 'all') {
                                iconHtml = '<i class="fa-solid fa-globe text-indigo-600"></i>';
                            }

                            // Status badge
                            const statusBadge = isRevoked
                                ? '<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400">ยกเลิกแล้ว</span>'
                                : '<span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400">ใช้งาน</span>';

                            // Revoke info line
                            const revokeInfo = isRevoked
                                ? `<div class="text-red-500 dark:text-red-400 text-[11px] mt-0.5"><i class="fa-solid fa-ban text-[10px] mr-0.5"></i>ยกเลิกโดย: ${item.revoked_by} • เมื่อ: ${item.revoked_at}</div>`
                                : '';

                            // Revoke button (only show for active shares)
                            const actionBtn = isRevoked
                                ? ''
                                : `<button type="button" onclick="revokeShare(${item.id})" class="p-1.5 text-amber-500 hover:text-amber-700 hover:bg-amber-50 dark:hover:bg-amber-950/40 rounded-lg transition shrink-0" title="ยกเลิกสิทธิ์การเข้าถึง">
                                    <i class="fa-solid fa-ban text-xs"></i>
                                </button>`;

                            row.innerHTML = `
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <div class="flex items-center gap-1.5">
                                            ${iconHtml}
                                            <span class="text-xs font-bold ${isRevoked ? 'text-gray-500 dark:text-gray-400 line-through' : 'text-gray-800 dark:text-gray-100'}">${item.recipient}</span>
                                        </div>
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300">${item.channel}</span>
                                        ${statusBadge}
                                    </div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400 space-y-0.5">
                                        <div><span class="text-gray-400">ผู้แชร์:</span> ${item.sender} &bull; <span class="text-gray-400">เมื่อ:</span> ${item.created_at}</div>
                                        ${item.note ? `<div class="italic text-gray-600 dark:text-gray-300">"${item.note}"</div>` : ''}
                                        ${revokeInfo}
                                    </div>
                                </div>
                                ${actionBtn}
                            `;
                            container.appendChild(row);
                        });
                    }
                }
            });
    }

    function revokeShare(shareId) {
        Swal.fire({
            title: 'ยกเลิกสิทธิ์การเข้าถึง?',
            text: 'บุคคลหรือแผนกนี้จะไม่สามารถเปิดดูเอกสารนี้ได้อีกต่อไป',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'ใช่, ยกเลิกสิทธิ์',
            cancelButtonText: 'ปิด'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/form-shares/${shareId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'สำเร็จ', text: data.message, timer: 1500, showConfirmButton: false });
                        loadShareHistory(true);
                    } else {
                        Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message });
                    }
                });
            }
        });
    }
</script>
