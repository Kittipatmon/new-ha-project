@extends('layouts.recruitment.app')

@section('content')
    <style>
        /* Document / Printed form paper styling */
        .paper-sheet-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 12px;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        .paper-sheet-container::-webkit-scrollbar {
            height: 6px;
        }
        .paper-sheet-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        .paper-sheet-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .paper-sheet-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .paper-sheet {
            background-color: #ffffff;
            color: #111827;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.12), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f2f5ff;
            font-family: 'Prompt', 'Kanit', sans-serif !important;
            font-size: 15px;
            line-height: 1.45;
            width: 100%;
            max-width: 1060px;
            margin-left: auto;
            margin-right: auto;
            box-sizing: border-box;
        }
        /* Force consistent font family across steps 1-5 */
        .paper-sheet, .paper-sheet *,
        .mobile-form-card, .mobile-form-card *,
        .paper-sheet-container, .paper-sheet-container * {
            font-family: 'Prompt', 'Kanit', sans-serif !important;
        }
        .fa, .fas, .far, .fal, .fab, .fa-solid, .fa-regular, .fa-light, .fa-thin, .fa-duotone, .fa-brands,
        i[class*="fa-"], i[class^="fa-"], span[class*="fa-"], span[class^="fa-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", "FontAwesome" !important;
        }
        @media (min-width: 820px) {
            .paper-sheet {
                font-size: 15px;
                line-height: 1.45;
            }
            .paper-sheet .text-xs {
                font-size: 11.5px !important;
            }
            .paper-sheet .text-sm {
                font-size: 13.5px !important;
                line-height: 1.25 !important;
            }
            .paper-sheet .text-base {
                font-size: 15px !important;
                line-height: 1.25 !important;
            }
            .paper-sheet .text-lg {
                font-size: 17px !important;
            }
            .paper-sheet .text-xl {
                font-size: 19px !important;
            }
            .paper-sheet .text-2xl {
                font-size: 22px !important;
            }
            .paper-sheet .sub-label {
                font-size: 11px !important;
                line-height: 1.1 !important;
                margin-top: 0px !important;
                height: auto !important;
                color: #4b5563 !important;
                font-weight: 500 !important;
            }
            .paper-sheet .doc-input {
                font-size: 15px !important;
                height: 26px !important;
                line-height: 24px !important;
                padding-bottom: 0px !important;
                margin-bottom: 2px !important;
            }
            .paper-sheet .doc-table th {
                font-size: 13px !important;
                line-height: 1.2 !important;
                padding: 6px 4px !important;
                text-align: center !important;
                vertical-align: middle !important;
            }
            .paper-sheet .doc-table th .sub-label {
                margin-top: 1px !important;
                font-size: 10.5px !important;
                line-height: 1.1 !important;
                font-weight: 500 !important;
                display: block !important;
                color: #4b5563 !important;
            }
            .paper-sheet .doc-table td {
                font-size: 13px !important;
                padding: 4px 6px !important;
            }
        }
        @media (max-width: 820px) {
            .paper-sheet {
                font-size: 14px !important;
                line-height: 1.45 !important;
                padding: 20px 14px !important;
            }
            .doc-input {
                font-size: 14px !important;
                height: 26px !important;
                line-height: 24px !important;
                margin-bottom: 6px !important;
            }
            .sub-label {
                font-size: 10.5px !important;
                height: auto !important;
            }
            .doc-table th, .doc-table td {
                padding: 4px 6px !important;
                font-size: 12px !important;
            }
        }
        @media (max-width: 1024px) {
            .paper-sheet .flex.items-end.w-full,
            .paper-sheet .flex.items-end.gap-3.w-full,
            .paper-sheet .flex.items-end.gap-2.w-full {
                flex-wrap: wrap !important;
                gap: 8px 12px !important;
            }
            .paper-sheet .flex.items-end.w-full > div,
            .paper-sheet .flex.items-end.gap-3.w-full > div,
            .paper-sheet .flex.items-end.gap-2.w-full > div {
                min-width: 0 !important;
            }
        }
        .dark .paper-sheet {
            background-color: #ffffff;
            color: #111827;
        }

        /* Mobile-Friendly Form Mode Styles */
        .mobile-form-card {
            background-color: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
            padding: 22px 18px;
            margin-bottom: 18px;
        }
        .dark .mobile-form-card {
            background-color: #1e293b;
            border-color: #334155;
            color: #f8fafc;
        }
        .mobile-field-group {
            margin-bottom: 18px;
        }
        .mobile-label {
            display: block;
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }
        .dark .mobile-label {
            color: #f1f5f9;
        }
        .mobile-sublabel {
            font-size: 12.5px;
            font-weight: 400;
            color: #64748b;
            margin-left: 4px;
        }
        .mobile-input {
            width: 100%;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 16px;
            background-color: #ffffff;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
        }
        .dark .mobile-input {
            background-color: #0f172a;
            border-color: #475569;
            color: #ffffff;
        }
        .mobile-input:focus {
            border-color: #B21F24;
            box-shadow: 0 0 0 3px rgba(178, 31, 36, 0.15);
        }
        .mobile-section-title {
            font-size: 18px;
            font-weight: 800;
            color: #B21F24;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 2px solid #fee2e2;
            padding-bottom: 8px;
            margin-bottom: 18px;
        }
        .dark .mobile-section-title {
            border-bottom-color: #374151;
        }
        @media (max-width: 820px) {
            .mobile-scroll-hint {
                display: flex !important;
            }
        }
        @media (min-width: 821px) {
            .mobile-scroll-hint {
                display: none !important;
            }
        }
        /* Dotted underline input simulation */
        .doc-input {
            border: none;
            border-bottom: 2px dotted #1f2937;
            background: transparent;
            padding: 0 4px 0px 4px;
            font-family: inherit;
            font-size: 15px;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            border-radius: 0;
            transition: border-color 0.2s;
            height: 26px;
            line-height: 24px;
            min-width: 0;
            max-width: 100%;
            margin-bottom: 2px;
        }
        .doc-input:focus {
            border-bottom: 2.5px solid #B21F24;
            background-color: #fef2f2;
        }

        /* 🚨 Invalid Field Highlight (Matches Probation Evaluation Form Style) */
        .doc-input.is-invalid, .mobile-input.is-invalid, input.is-invalid, select.is-invalid, textarea.is-invalid {
            border-bottom: 2.5px solid #ef4444 !important;
            background-color: #fef2f2 !important;
            color: #dc2626 !important;
        }
        .doc-input.is-invalid::placeholder, .mobile-input.is-invalid::placeholder, input.is-invalid::placeholder {
            color: #ef4444 !important;
            font-style: italic !important;
            font-weight: bold !important;
        }
        .doc-table th, .doc-table td {
            border: 1.5px solid #111827;
            padding: 6px 8px;
            font-family: 'Prompt', 'Kanit', sans-serif !important;
            font-size: 13px;
        }
        .doc-table th {
            background-color: #f8fafc;
            font-weight: bold;
            text-align: center;
        }
        .sub-label {
            font-size: 11px;
            color: #4b5563;
            font-style: normal !important;
            display: block;
            margin-top: 0px;
            line-height: 1.1;
            font-weight: 500;
        }

        /* 🔍 Font Scale Zoom Modifiers (for Prompt / Kanit Font) */
        .paper-sheet.font-scale-md {
            font-size: 17px !important;
        }
        .paper-sheet.font-scale-md .doc-input {
            font-size: 17px !important;
            height: 28px !important;
            line-height: 26px !important;
        }
        .paper-sheet.font-scale-md .sub-label {
            font-size: 12.5px !important;
        }
        .paper-sheet.font-scale-md .doc-table th, 
        .paper-sheet.font-scale-md .doc-table td {
            font-size: 14.5px !important;
            padding: 7px 10px !important;
        }

        .paper-sheet.font-scale-lg {
            font-size: 19.5px !important;
        }
        .paper-sheet.font-scale-lg .doc-input {
            font-size: 19.5px !important;
            height: 31px !important;
            line-height: 29px !important;
        }
        .paper-sheet.font-scale-lg .sub-label {
            font-size: 14px !important;
        }
        .paper-sheet.font-scale-lg .doc-table th, 
        .paper-sheet.font-scale-lg .doc-table td {
            font-size: 16.5px !important;
            padding: 8px 12px !important;
        }
        .paper-checkbox {
            width: 16px;
            height: 16px;
            border-radius: 2px;
            border-color: #1f2937;
            accent-color: #111827;
        }
        /* Floating Thai Address Autocomplete Dropdown */
        .thai-addr-dropdown {
            position: absolute;
            z-index: 99999;
            min-width: 280px;
            max-width: 420px;
            max-height: 240px;
            overflow-y: auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 12px 28px -4px rgba(0, 0, 0, 0.25), 0 8px 10px -4px rgba(0, 0, 0, 0.1);
            font-size: 13px;
            font-family: 'Noto Sans Thai', 'Prompt', sans-serif;
        }
        .thai-addr-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f1f5f9;
            color: #1f2937;
            text-align: left;
            transition: all 0.12s ease-in-out;
        }
        .thai-addr-item:last-child {
            border-bottom: none;
        }
        .thai-addr-item:hover, .thai-addr-item.selected {
            background-color: #fef2f2;
            color: #B21F24;
        }
        .thai-addr-empty {
            padding: 10px 14px;
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
        }

        /* Flatpickr Thai theme styling */
        .flatpickr-calendar {
            font-family: 'Noto Sans Thai', 'Prompt', sans-serif !important;
            border-radius: 14px !important;
            box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.22), 0 8px 12px -4px rgba(0, 0, 0, 0.08) !important;
            border: 1px solid #e2e8f0 !important;
            padding: 4px !important;
        }
        .flatpickr-months {
            align-items: center !important;
        }
        .flatpickr-current-month {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 6px !important;
            padding: 4px 0 0 0 !important;
            position: relative !important;
            width: auto !important;
            left: 0 !important;
        }
        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-family: inherit !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            color: #1e293b !important;
            padding: 3px 22px 3px 8px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            background-color: #f8fafc !important;
            cursor: pointer !important;
            outline: none !important;
            height: 32px !important;
            line-height: 24px !important;
        }
        .flatpickr-current-month .numInputWrapper {
            display: none !important;
        }
        /* Flatpickr Year Picker Button & Grid Modal */
        .flatpickr-buddhist-year-btn {
            font-family: inherit !important;
            font-weight: 700 !important;
            font-size: 15px !important;
            color: #1e293b !important;
            padding: 3px 10px !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            background-color: #f8fafc !important;
            cursor: pointer !important;
            height: 32px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            transition: all 0.15s ease !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05) !important;
        }
        .flatpickr-buddhist-year-btn:hover {
            border-color: #cbd5e1 !important;
            background-color: #f1f5f9 !important;
            color: #B21F24 !important;
        }
        .flatpickr-year-grid-overlay {
            position: absolute !important;
            top: 40px !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            background: #ffffff !important;
            z-index: 100 !important;
            border-radius: 0 0 14px 14px !important;
            padding: 10px 8px !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
        }
        .flatpickr-year-grid-header {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 0 4px 8px 4px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #475569 !important;
        }
        .flatpickr-year-grid-close {
            cursor: pointer !important;
            font-size: 16px !important;
            color: #94a3b8 !important;
            line-height: 1 !important;
            padding: 2px 6px !important;
            border-radius: 4px !important;
        }
        .flatpickr-year-grid-close:hover {
            color: #dc2626 !important;
            background: #fee2e2 !important;
        }
        .flatpickr-year-grid-body {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 6px !important;
            overflow-y: auto !important;
            max-height: 220px !important;
            padding: 8px 2px 4px 2px !important;
        }
        .flatpickr-year-item {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 6px 2px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            border-radius: 6px !important;
            border: 1px solid #f1f5f9 !important;
            background: #f8fafc !important;
            cursor: pointer !important;
            transition: all 0.12s ease !important;
        }
        .flatpickr-year-item:hover {
            background-color: #fee2e2 !important;
            border-color: #fca5a5 !important;
            color: #B21F24 !important;
            transform: scale(1.03) !important;
        }
        .flatpickr-year-item.active {
            background-color: #B21F24 !important;
            border-color: #B21F24 !important;
            color: #ffffff !important;
            font-weight: 700 !important;
        }
        .yearpicker-popup {
            position: absolute;
            z-index: 999999;
            width: 240px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 10px;
            box-shadow: 0 16px 36px -4px rgba(0, 0, 0, 0.22), 0 8px 12px -4px rgba(0, 0, 0, 0.08);
            font-family: 'Prompt', 'Noto Sans Thai', sans-serif;
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <div class="min-h-screen bg-slate-200 dark:bg-slate-900 py-4 sm:py-6 md:py-8 px-1 sm:px-2 md:px-4 lg:px-6">
        <div class="max-w-5xl lg:max-w-6xl mx-auto" x-data="recruitmentForm()">

            <!-- Top Action & Navigation Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4 bg-white dark:bg-slate-800 p-3 sm:p-3.5 rounded-2xl shadow-sm border border-slate-300 dark:border-slate-700 max-w-[1060px] mx-auto">
                <div class="flex items-center justify-between sm:justify-start gap-3">
                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <a href="{{ route('recruitment.show', $post->slug) }}"
                            class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 hover:text-[#B21F24] transition-colors shrink-0"
                            title="ย้อนกลับ">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                        <div class="min-w-0">
                            <h2 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white truncate">
                                <span>แบบฟอร์มใบสมัครงาน (QF-HR-14)</span>
                            </h2>
                            <p class="text-xs text-slate-500 truncate">ตำแหน่ง: <strong class="text-[#B21F24]">{{ $post->position_name }}</strong></p>
                        </div>
                    </div>
                </div>

                <!-- Mode Switcher Toggle Pill (รองรับทุกอุปกรณ์: มือถือ, iPad, โน้ตบุ๊ก, จอคอม) -->
                <div class="flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-700/60 rounded-xl border border-slate-200 dark:border-slate-600 self-stretch sm:self-auto justify-center">
                    <button type="button" @click="viewMode = 'paper'"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                        :class="viewMode === 'paper' ? 'bg-white dark:bg-slate-800 text-[#B21F24] shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'">
                        <i class="fa-solid fa-file-lines text-xs"></i>
                        <span>แบบฟอร์มเอกสาร</span>
                    </button>
                    <button type="button" @click="viewMode = 'mobile'"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer"
                        :class="viewMode === 'mobile' ? 'bg-[#B21F24] text-white shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-slate-900'">
                        <i class="fa-solid fa-mobile-screen text-xs"></i>
                        <span>โหมดกรอกง่าย</span>
                    </button>
                </div>
            </div>

            <!-- Mobile & Tablet Scroll Hint (Show only when in Paper Mode) -->
            <div x-show="viewMode === 'paper'" class="mobile-scroll-hint hidden items-center justify-between bg-amber-50 border border-amber-200 text-amber-800 text-xs px-3.5 py-2 rounded-xl mb-3 shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-arrows-left-right text-amber-600"></i>
                    <span>สามารถเลื่อนซ้าย-ขวา เพื่อดูและกรอกแบบฟอร์มเอกสาร หรือกดปุ่ม <strong>"โหมดกรอกง่าย"</strong> ด้านบนได้</span>
                </div>
                <button type="button" @click="viewMode = 'mobile'" class="text-[10px] text-white bg-[#B21F24] hover:bg-red-700 px-2.5 py-1 rounded-md font-bold transition-all shrink-0 cursor-pointer">สลับโหมดกรอกง่าย</button>
            </div>

            @php
                $initialStep = 1;
                if ($errors->hasAny(['resume', 'photo', 'portfolio', 'pdpa_consent'])) {
                    $initialStep = 5;
                } elseif ($errors->hasAny(['computer_skills', 'special_abilities', 'application_questions', 'expected_salary'])) {
                    $initialStep = 4;
                } elseif ($errors->hasAny(['education', 'education.*', 'experience', 'experience.*', 'language_skills'])) {
                    $initialStep = 3;
                } elseif ($errors->hasAny(['family_info', 'emergency_contact'])) {
                    $initialStep = 2;
                }
            @endphp

            @if ($errors->any())
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    (function() {
                        function showErrorAlert() {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'พบข้อผิดพลาดในการกรอกข้อมูล',
                                    html: `<ul class="text-left text-xs space-y-1" style="list-style-type: disc; padding-left: 20px;">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>`,
                                    confirmButtonText: 'ตกลง',
                                    confirmButtonColor: '#B21F24'
                                });
                            }
                        }
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', showErrorAlert);
                        } else {
                            showErrorAlert();
                        }
                    })();
                </script>
            @endif

            <script>
                // Enforce numeric-only input on target fields (Phone, Postcode, House No, National ID, Moo, etc.)
                document.addEventListener('input', function(e) {
                    const el = e.target;
                    if (!el || el.tagName !== 'INPUT') return;
                    const name = el.name || '';

                    // House numbers may contain digits, slashes (/), and hyphens (-) e.g. 123/45 or 99-10
                    if (name === 'house_no' || name.includes('[house_no]')) {
                        el.value = el.value.replace(/[^0-9\/\-]/g, '');
                        return;
                    }

                    const isNumericOnly = el.getAttribute('inputmode') === 'numeric' ||
                        ['moo', 'postcode', 'phone', 'tel', 'national_id'].includes(name) ||
                        name.includes('[phone]') || name.includes('[mobile]') || name.includes('[work_phone]') ||
                        name.includes('[moo]');

                    if (isNumericOnly) {
                        el.value = el.value.replace(/[^0-9]/g, '');
                    }
                });

                function recruitmentForm() {
                    return {
                        step: {{ $initialStep }},
                        viewMode: (window.innerWidth < 1024) ? 'mobile' : 'paper',
                        fontScale: 'sm',
                        submitting: false,

                        // Personal Name & Signature
                        firstName: '{{ old('first_name') }}',
                        lastName: '{{ old('last_name') }}',
                        applicantSignature: '{{ old('applicant_signature', old('m_applicant_signature', '')) }}',
                        signatureManuallyEdited: {{ old('applicant_signature') || old('m_applicant_signature') ? 'true' : 'false' }},

                        syncSignature() {
                            if (!this.signatureManuallyEdited) {
                                const full = (this.firstName + ' ' + this.lastName).trim();
                                this.applicantSignature = full;
                            }
                        },
                        checkAutoSignature() {
                            if (!this.applicantSignature) {
                                const full = (this.firstName + ' ' + this.lastName).trim();
                                if (full) {
                                    this.applicantSignature = full;
                                }
                            }
                        },

                        // Marital Status
                        maritalStatus: '{{ old('marital_status', 'โสด') }}',

                        // Page 1
                        dob: '{{ old('date_of_birth') }}',
                        age: '{{ old('age') }}',
                        calcAge() {
                            if (!this.dob) return;
                            const birthDate = new Date(this.dob);
                            const diff = Date.now() - birthDate.getTime();
                            const ageDt = new Date(diff);
                            this.age = Math.abs(ageDt.getUTCFullYear() - 1970);
                        },

                        // Page 2: Siblings
                        siblings: [
                            { name: '', age: '', occupation: '' },
                            { name: '', age: '', occupation: '' },
                            { name: '', age: '', occupation: '' },
                            { name: '', age: '', occupation: '' },
                            { name: '', age: '', occupation: '' }
                        ],
                        addSibling() {
                            this.siblings.push({ name: '', age: '', occupation: '' });
                        },
                        removeSibling(idx) {
                            this.siblings.splice(idx, 1);
                        },

                        // Page 3: Education & Experience
                        education: [
                            { level: '', institution_name: '', major: '', start_year: '', end_year: '' },
                            { level: '', institution_name: '', major: '', start_year: '', end_year: '' },
                            { level: '', institution_name: '', major: '', start_year: '', end_year: '' },
                            { level: '', institution_name: '', major: '', start_year: '', end_year: '' },
                            { level: '', institution_name: '', major: '', start_year: '', end_year: '' }
                        ],
                        addEducation() {
                            this.education.push({ level: '', institution_name: '', major: '', start_year: '', end_year: '' });
                        },
                        removeEducation(idx) {
                            this.education.splice(idx, 1);
                        },

                        experience: [
                            { company_name: '', start_date: '', end_date: '', position: '', job_detail: '', salary: '', reason_for_leaving: '' },
                            { company_name: '', start_date: '', end_date: '', position: '', job_detail: '', salary: '', reason_for_leaving: '' },
                            { company_name: '', start_date: '', end_date: '', position: '', job_detail: '', salary: '', reason_for_leaving: '' },
                            { company_name: '', start_date: '', end_date: '', position: '', job_detail: '', salary: '', reason_for_leaving: '' },
                            { company_name: '', start_date: '', end_date: '', position: '', job_detail: '', salary: '', reason_for_leaving: '' }
                        ],
                        addExperience() {
                            this.experience.push({ company_name: '', start_date: '', end_date: '', position: '', job_detail: '', salary: '', reason_for_leaving: '' });
                        },
                        removeExperience(idx) {
                            this.experience.splice(idx, 1);
                        },

                        // Files
                        resumeFiles: [],
                        photoName: '',
                        photoPreview: null,
                        portfolioFiles: [],
                        handleFileChange(event, type) {
                            if (!event.target.files || event.target.files.length === 0) return;

                            if (type === 'photo') {
                                const file = event.target.files[0];
                                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                                if (file.type === 'image/gif' || !allowedTypes.includes(file.type)) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'รูปแบบไฟล์ไม่ถูกต้อง',
                                        text: 'กรุณาอัปโหลดรูปถ่ายเป็นไฟล์ JPG, JPEG หรือ PNG เท่านั้น (ไม่รองรับไฟล์ GIF)',
                                        confirmButtonColor: '#B21F24'
                                    });
                                    event.target.value = '';
                                    this.photoName = '';
                                    this.photoPreview = null;
                                    return;
                                }

                                this.photoName = file.name;
                                const reader = new FileReader();
                                reader.onload = (e) => { this.photoPreview = e.target.result; };
                                reader.readAsDataURL(file);
                                return;
                            }

                            if (type === 'resume') {
                                const selected = Array.from(event.target.files);
                                const allowedExts = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'webp'];
                                const invalidFiles = selected.filter(f => {
                                    const ext = f.name.split('.').pop().toLowerCase();
                                    return !allowedExts.includes(ext);
                                });
                                if (invalidFiles.length > 0) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'รูปแบบไฟล์ไม่ถูกต้อง',
                                        text: 'ไฟล์ Resume / CV ต้องเป็นไฟล์ประเภท PDF, Word (.doc, .docx) หรือรูปภาพเท่านั้น',
                                        confirmButtonColor: '#B21F24'
                                    });
                                    event.target.value = '';
                                    this.resumeFiles = [];
                                    this.syncFileInput('resume');
                                    return;
                                }
                                const existingNames = this.resumeFiles.map(f => typeof f === 'string' ? f : f.name);
                                const newFiles = selected.filter(f => !existingNames.includes(f.name));
                                this.resumeFiles = [...this.resumeFiles, ...newFiles];
                                this.syncFileInput('resume');
                                return;
                            }

                            if (type === 'portfolio') {
                                const selected = Array.from(event.target.files);
                                const invalidFiles = selected.filter(f => f.type !== 'application/pdf' && !f.name.toLowerCase().endsWith('.pdf'));
                                if (invalidFiles.length > 0) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'รูปแบบไฟล์ไม่ถูกต้อง',
                                        text: 'ไฟล์ Portfolio / เอกสารอื่นๆ ต้องเป็นไฟล์ประเภท PDF (.pdf) เท่านั้น',
                                        confirmButtonColor: '#B21F24'
                                    });
                                    event.target.value = '';
                                    this.portfolioFiles = [];
                                    this.syncFileInput('portfolio');
                                    return;
                                }
                                const existingNames = this.portfolioFiles.map(f => typeof f === 'string' ? f : f.name);
                                const newFiles = selected.filter(f => !existingNames.includes(f.name));
                                this.portfolioFiles = [...this.portfolioFiles, ...newFiles];
                                this.syncFileInput('portfolio');
                                return;
                            }
                        },

                        removeFile(type, index) {
                            if (type === 'resume') {
                                this.resumeFiles.splice(index, 1);
                                this.syncFileInput('resume');
                            } else if (type === 'portfolio') {
                                this.portfolioFiles.splice(index, 1);
                                this.syncFileInput('portfolio');
                            }
                        },

                        syncFileInput(type) {
                            try {
                                const dt = new DataTransfer();
                                const list = type === 'resume' ? this.resumeFiles : this.portfolioFiles;
                                list.forEach(f => {
                                    if (f instanceof File) {
                                        dt.items.add(f);
                                    }
                                });
                                const selector = type === 'resume' ? 'input[name="resume[]"]' : 'input[name="portfolio[]"]';
                                document.querySelectorAll(selector).forEach(input => {
                                    input.files = dt.files;
                                });
                            } catch (err) {
                                console.warn('DataTransfer sync error:', err);
                            }
                        },

                        validateStep(s, showAlert = true) {
                            let invalidCount = 0;
                            const missingFieldLabels = [];
                            let firstInvalidEl = null;

                            // Clear previous invalid highlights in current step
                            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                            // Define required field rules per step (All steps 1-5 required)
                            const stepRules = {
                                1: [
                                    { name: 'first_name', label: 'ชื่อ (First Name)' },
                                    { name: 'last_name', label: 'นามสกุล (Last Name)' },
                                    { name: 'nickname', label: 'ชื่อเล่น (Nickname)' },
                                    { name: 'house_no', label: 'ที่อยู่ปัจจุบันเลขที่ (Present Address)' },
                                    { name: 'province', label: 'จังหวัด (Province)' },
                                    { name: 'district', label: 'อำเภอ/เขต (District)' },
                                    { name: 'subdistrict', label: 'ตำบล/แขวง (Subdistrict)' },
                                    { name: 'postcode', label: 'รหัสไปรษณีย์ (Postcode)' },
                                    { name: 'phone', label: 'เบอร์มือถือ (Mobile Phone)' },
                                    { name: 'line_id', label: 'ID Line' },
                                    { name: 'email', label: 'อีเมล (E-mail)' },
                                    { name: 'housing_type', label: 'ประเภทที่อยู่อาศัย (Housing Type)' },
                                    { name: 'date_of_birth', label: 'วัน/เดือน/ปีเกิด (Date of Birth)' },
                                    { name: 'age', label: 'อายุ (Age)' },
                                    { name: 'place_of_birth', label: 'สถานที่เกิด (Place of Birth)' },
                                    { name: 'race', label: 'เชื้อชาติ (Race)' },
                                    { name: 'nationality', label: 'สัญชาติ (Nationality)' },
                                    { name: 'religion', label: 'ศาสนา (Religion)' },
                                    { name: 'national_id', label: 'เลขบัตรประชาชน (ID Card No.)' },
                                    { name: 'id_card_issued_by', label: 'ออกโดย (Issued By)' },
                                    { name: 'id_card_issued_province', label: 'จังหวัดที่ออกบัตร (Issued Province)' },
                                    { name: 'id_card_issued_date', label: 'วันออกบัตร (Issued Date)' },
                                    { name: 'id_card_expiry_date', label: 'วันหมดอายุบัตร (Expiry Date)' },
                                    { name: 'height_cm', label: 'ส่วนสูง (Height)' },
                                    { name: 'weight_kg', label: 'น้ำหนัก (Weight)' },
                                    { name: 'military_status', label: 'สถานะทางทหาร (Military Status)' },
                                    { name: 'marital_status', label: 'สถานภาพ (Marital Status)' },
                                    { name: 'gender', label: 'เพศ (Gender)' }
                                ],
                                2: [
                                    { name: 'emergency_contact[name]', label: 'ชื่อ-สกุล ผู้ติดต่อฉุกเฉิน (Emergency Contact Name)' },
                                    { name: 'emergency_contact[relationship]', label: 'ความสัมพันธ์ ผู้ติดต่อฉุกเฉิน (Emergency Contact Relationship)' },
                                    { name: 'emergency_contact[house_no]', label: 'ที่อยู่ ผู้ติดต่อฉุกเฉิน (Emergency Address)' },
                                    { name: 'emergency_contact[district]', label: 'อำเภอ/เขต ผู้ติดต่อฉุกเฉิน (Emergency District)' },
                                    { name: 'emergency_contact[subdistrict]', label: 'ตำบล/แขวง ผู้ติดต่อฉุกเฉิน (Emergency Subdistrict)' },
                                    { name: 'emergency_contact[province]', label: 'จังหวัด ผู้ติดต่อฉุกเฉิน (Emergency Province)' },
                                    { name: 'emergency_contact[mobile]', label: 'เบอร์มือถือ ผู้ติดต่อฉุกเฉิน (Emergency Mobile Phone)' }
                                ],
                                3: [
                                    { name: 'education[0][level]', label: 'ระดับการศึกษา (Educational Level)' }
                                ],
                                4: [
                                    { name: 'application_questions[reason_to_apply]', label: 'เหตุผลที่มาสมัครงาน (Reason for Applying)' },
                                    { name: 'expected_salary', label: 'อัตราเงินเดือนที่คาดหวัง (Expected Salary)' }
                                ],
                                5: [
                                    { name: 'applicant_signature', label: 'ลายมือชื่อผู้สมัคร (Applicant Signature)' },
                                    { name: 'pdpa_consent', label: 'คำรับรองและความยินยอม (PDPA Consent)' }
                                ]
                            };

                            const rules = stepRules[s] || [];
                            for (const rule of rules) {
                                const rawEls = Array.from(document.querySelectorAll(`[name="${rule.name}"], [name="m_${rule.name}"]`));

                                const visibleEls = [];
                                rawEls.forEach(el => {
                                    let target = el;
                                    if (el._flatpickr && el._flatpickr.altInput) {
                                        target = el._flatpickr.altInput;
                                    }
                                    if (target && (target.offsetParent !== null || target.offsetWidth > 0 || target.offsetHeight > 0)) {
                                        visibleEls.push(target);
                                    }
                                });

                                if (visibleEls.length > 0) {
                                    let isValid = rawEls.some(el => {
                                        if (el.type === 'checkbox' || el.type === 'radio') return el.checked;
                                        return el.value && el.value.trim().length > 0;
                                    });

                                    // For emergency address, if house_no or moo is provided, consider it valid
                                    if (!isValid && rule.name === 'emergency_contact[house_no]') {
                                        const mooEls = Array.from(document.querySelectorAll('[name="emergency_contact[moo]"], [name="m_emergency_contact[moo]"]'));
                                        if (mooEls.some(el => el.value && el.value.trim().length > 0)) {
                                            isValid = true;
                                        }
                                    }

                                    if (!isValid) {
                                        invalidCount++;
                                        missingFieldLabels.push(rule.label);

                                        visibleEls.forEach(el => {
                                            el.classList.add('is-invalid');
                                            if (el.type !== 'checkbox' && el.type !== 'radio') {
                                                el.dataset.origPlaceholder = el.placeholder || '';
                                                el.placeholder = 'กรุณาระบุข้อมูล *';
                                            }
                                            const clearInvalid = () => {
                                                el.classList.remove('is-invalid');
                                                if (el.dataset.origPlaceholder !== undefined) {
                                                    el.placeholder = el.dataset.origPlaceholder;
                                                }
                                            };
                                            el.addEventListener('input', clearInvalid, { once: true });
                                            el.addEventListener('change', clearInvalid, { once: true });
                                        });

                                        if (!firstInvalidEl) {
                                            firstInvalidEl = visibleEls[0];
                                        }
                                    }
                                }
                            }

                            if (invalidCount > 0) {
                                const scrollToTarget = () => {
                                    if (firstInvalidEl) {
                                        firstInvalidEl.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
                                        setTimeout(() => {
                                            try {
                                                firstInvalidEl.focus({ preventScroll: true });
                                            } catch (e) {}
                                        }, 150);
                                    }
                                };

                                scrollToTarget();

                                if (!showAlert) {
                                    return false;
                                }

                                Swal.fire({
                                    icon: 'error',
                                    title: 'กรุณาระบุข้อมูลให้ครบถ้วน',
                                    html: `
                                        <div class="text-left space-y-2">
                                            <p class="text-xs font-bold text-gray-700">พบช่องข้อมูลที่ยังไม่ได้ระบุ (<span class="text-red-600 font-extrabold">ไฮไลต์ขีดแดง "กรุณาระบุข้อมูล"</span>):</p>
                                            <ul class="text-xs space-y-1 text-red-600 font-bold max-h-40 overflow-y-auto pl-5 list-disc">
                                                ${missingFieldLabels.map(l => `<li>${l}</li>`).join('')}
                                            </ul>
                                        </div>
                                    `,
                                    confirmButtonText: 'ตกลง (ไปกรอกข้อมูล)',
                                    confirmButtonColor: '#B21F24'
                                }).then(() => {
                                    setTimeout(scrollToTarget, 100);
                                });
                                return false;
                            }

                            return true;
                        },

                        nextStep() {
                            if (this.step < 5) {
                                this.step++;
                                if (this.step === 5) this.checkAutoSignature();
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }
                        },
                        prevStep() {
                            if (this.step > 1) {
                                this.step--;
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }
                        },
                        goToStep(targetStep) {
                            if (targetStep === this.step) return;
                            if (targetStep === 5) this.checkAutoSignature();
                            this.step = targetStep;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        },

                        async confirmSubmit(e) {
                            if (this.submitting) return;
                            this.checkAutoSignature();

                            // Clear previous invalid highlights
                            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                            const stepNames = {
                                1: 'สเต็ป 1 (ข้อมูลส่วนตัว)',
                                2: 'สเต็ป 2 (ประวัติครอบครัว)',
                                3: 'สเต็ป 3 (การศึกษาและประสบการณ์)',
                                4: 'สเต็ป 4 (ข้อมูลเพิ่มเติม)',
                                5: 'สเต็ป 5 (บุคคลอ้างอิงและการยินยอม)'
                            };

                            // Define required field rules per step
                            const stepRules = {
                                1: [
                                    { name: 'first_name', label: 'ชื่อ (First Name)' },
                                    { name: 'last_name', label: 'นามสกุล (Last Name)' },
                                    { name: 'nickname', label: 'ชื่อเล่น (Nickname)' },
                                    { name: 'house_no', label: 'ที่อยู่ปัจจุบันเลขที่ (Present Address)' },
                                    { name: 'province', label: 'จังหวัด (Province)' },
                                    { name: 'district', label: 'อำเภอ/เขต (District)' },
                                    { name: 'subdistrict', label: 'ตำบล/แขวง (Subdistrict)' },
                                    { name: 'postcode', label: 'รหัสไปรษณีย์ (Postcode)' },
                                    { name: 'phone', label: 'เบอร์มือถือ (Mobile Phone)' },
                                    { name: 'line_id', label: 'ID Line' },
                                    { name: 'email', label: 'อีเมล (E-mail)' },
                                    { name: 'housing_type', label: 'ประเภทที่อยู่อาศัย (Housing Type)' },
                                    { name: 'date_of_birth', label: 'วัน/เดือน/ปีเกิด (Date of Birth)' },
                                    { name: 'age', label: 'อายุ (Age)' },
                                    { name: 'place_of_birth', label: 'สถานที่เกิด (Place of Birth)' },
                                    { name: 'race', label: 'เชื้อชาติ (Race)' },
                                    { name: 'nationality', label: 'สัญชาติ (Nationality)' },
                                    { name: 'religion', label: 'ศาสนา (Religion)' },
                                    { name: 'national_id', label: 'เลขบัตรประชาชน (ID Card No.)' },
                                    { name: 'id_card_issued_by', label: 'ออกโดย (Issued By)' },
                                    { name: 'id_card_issued_province', label: 'จังหวัดที่ออกบัตร (Issued Province)' },
                                    { name: 'id_card_issued_date', label: 'วันออกบัตร (Issued Date)' },
                                    { name: 'id_card_expiry_date', label: 'วันหมดอายุบัตร (Expiry Date)' },
                                    { name: 'height_cm', label: 'ส่วนสูง (Height)' },
                                    { name: 'weight_kg', label: 'น้ำหนัก (Weight)' },
                                    { name: 'military_status', label: 'สถานะทางทหาร (Military Status)' },
                                    { name: 'marital_status', label: 'สถานภาพ (Marital Status)' },
                                    { name: 'gender', label: 'เพศ (Gender)' }
                                ],
                                2: [
                                    { name: 'emergency_contact[name]', label: 'ชื่อ-สกุล ผู้ติดต่อฉุกเฉิน (Emergency Contact Name)' },
                                    { name: 'emergency_contact[relationship]', label: 'ความสัมพันธ์ ผู้ติดต่อฉุกเฉิน (Emergency Contact Relationship)' },
                                    { name: 'emergency_contact[house_no]', label: 'ที่อยู่ ผู้ติดต่อฉุกเฉิน (Emergency Address)' },
                                    { name: 'emergency_contact[district]', label: 'อำเภอ/เขต ผู้ติดต่อฉุกเฉิน (Emergency District)' },
                                    { name: 'emergency_contact[subdistrict]', label: 'ตำบล/แขวง ผู้ติดต่อฉุกเฉิน (Emergency Subdistrict)' },
                                    { name: 'emergency_contact[province]', label: 'จังหวัด ผู้ติดต่อฉุกเฉิน (Emergency Province)' },
                                    { name: 'emergency_contact[mobile]', label: 'เบอร์มือถือ ผู้ติดต่อฉุกเฉิน (Emergency Mobile Phone)' }
                                ],
                                3: [
                                    { name: 'education[0][level]', label: 'ระดับการศึกษา (Educational Level)' }
                                ],
                                4: [
                                    { name: 'application_questions[reason_to_apply]', label: 'เหตุผลที่มาสมัครงาน (Reason for Applying)' },
                                    { name: 'expected_salary', label: 'อัตราเงินเดือนที่คาดหวัง (Expected Salary)' }
                                ],
                                5: [
                                    { name: 'applicant_signature', label: 'ลายมือชื่อผู้สมัคร (Applicant Signature)' },
                                    { name: 'resume', label: 'ไฟล์ Resume / CV (PDF)' },
                                    { name: 'pdpa_consent', label: 'คำรับรองและความยินยอม (PDPA Consent)' }
                                ]
                            };

                            let firstProblemStep = null;
                            let firstInvalidElAll = null;
                            const errorsByStep = {};

                            for (let s = 1; s <= 5; s++) {
                                const rules = stepRules[s] || [];
                                const missingLabels = [];

                                for (const rule of rules) {
                                    let rawEls = Array.from(document.querySelectorAll(`[name="${rule.name}"], [name="m_${rule.name}"], [name="${rule.name}[]"], [name="m_${rule.name}[]"]`));
                                    
                                    if (rawEls.length > 0) {
                                        let isValid = rawEls.some(el => {
                                            if (el.type === 'checkbox' || el.type === 'radio') return el.checked;
                                            if (el.type === 'file') return el.files && el.files.length > 0;
                                            return el.value && el.value.trim().length > 0;
                                        });

                                        // For emergency address, if house_no or moo is provided, consider it valid
                                        if (!isValid && rule.name === 'emergency_contact[house_no]') {
                                            const mooEls = Array.from(document.querySelectorAll('[name="emergency_contact[moo]"], [name="m_emergency_contact[moo]"]'));
                                            if (mooEls.some(el => el.value && el.value.trim().length > 0)) {
                                                isValid = true;
                                            }
                                        }

                                        if (!isValid) {
                                            missingLabels.push(rule.label);

                                            if (!firstProblemStep) {
                                                firstProblemStep = s;
                                                let targetEl = rawEls[0];
                                                if (targetEl._flatpickr && targetEl._flatpickr.altInput) {
                                                    targetEl = targetEl._flatpickr.altInput;
                                                }
                                                firstInvalidElAll = targetEl;
                                            }
                                        }
                                    }
                                }

                                if (missingLabels.length > 0) {
                                    errorsByStep[s] = missingLabels;
                                }
                            }

                            // If errors exist in any step:
                            if (firstProblemStep) {
                                // Switch view to the FIRST problem step
                                this.step = firstProblemStep;
                                await this.$nextTick();

                                // Highlight missing fields on that step without duplicate popup
                                this.validateStep(firstProblemStep, false);

                                // Construct HTML list grouped by Step
                                let htmlContent = '<div class="text-left space-y-3 max-h-64 overflow-y-auto pr-2">';
                                for (const [stepNum, labels] of Object.entries(errorsByStep)) {
                                    htmlContent += `
                                        <div class="bg-red-50 p-2.5 rounded-lg border border-red-100">
                                            <p class="text-xs font-extrabold text-[#B21F24] mb-1.5 flex items-center gap-1.5">
                                                <i class="fa-solid fa-triangle-exclamation"></i> ${stepNames[stepNum]}:
                                            </p>
                                            <ul class="text-xs space-y-1 text-gray-700 font-semibold pl-5 list-disc">
                                                ${labels.map(l => `<li>${l}</li>`).join('')}
                                            </ul>
                                        </div>
                                    `;
                                }
                                htmlContent += '</div>';

                                const scrollToError = () => {
                                    if (firstInvalidElAll) {
                                        firstInvalidElAll.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'center' });
                                        setTimeout(() => {
                                            try {
                                                firstInvalidElAll.focus({ preventScroll: true });
                                            } catch (err) {}
                                        }, 150);
                                    }
                                };

                                scrollToError();

                                Swal.fire({
                                    icon: 'error',
                                    title: 'พบช่องข้อมูลที่ยังไม่ได้ระบุ',
                                    html: htmlContent,
                                    confirmButtonText: `ไปกรอกข้อมูล (${stepNames[firstProblemStep]})`,
                                    confirmButtonColor: '#B21F24'
                                }).then(() => {
                                    setTimeout(scrollToError, 100);
                                });

                                return;
                            }

                            const targetForm = (e && e.target) ? (e.target.closest('form') || e.target) : document.querySelector('form');

                            Swal.fire({
                                title: 'ยืนยันการส่งใบสมัครงาน?',
                                text: 'กรุณาตรวจสอบความถูกต้องของข้อมูลตามแบบฟอร์มเอกสาร QF-HR-14 ก่อนส่ง',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#B21F24',
                                cancelButtonColor: '#6b7280',
                                confirmButtonText: 'ยืนยันและส่งใบสมัคร',
                                cancelButtonText: 'กลับไปตรวจสอบ'
                            }).then((res) => {
                                if (res.isConfirmed) {
                                    this.submitting = true;
                                    Swal.fire({
                                        title: 'กำลังส่งข้อมูลใบสมัคร...',
                                        text: 'กรุณารอสักครู่ ระบบกำลังบันทึกข้อมูลและไฟล์เอกสาร',
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        showConfirmButton: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });

                                    const activeMode = this.viewMode;
                                    const inactiveSelector = activeMode === 'paper' 
                                        ? '[x-show*="viewMode === \'mobile\'"]' 
                                        : '.paper-sheet-container';
                                    
                                    document.querySelectorAll(inactiveSelector).forEach(container => {
                                        container.querySelectorAll('input, select, textarea').forEach(el => {
                                            el.disabled = true;
                                        });
                                    });

                                    const formEl = targetForm || document.querySelector('form');
                                    if (formEl) {
                                        HTMLFormElement.prototype.submit.call(formEl);
                                    }
                                }
                            });
                        }
                    };
                }
            </script>

                <!-- 3-Tier Adaptive Stepper Progress Bar (Mobile, iPad/Tablet, Notebook, Desktop PC) -->
                
                <!-- Tier 1: Mobile (< 640px) -->
                <div class="block sm:hidden bg-white dark:bg-slate-800 p-3 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 mb-4 max-w-[1060px] mx-auto">
                    <div class="flex items-center justify-between mb-2.5">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-[#B21F24] text-white flex items-center justify-center text-xs font-black shadow-sm">
                                <span x-text="step"></span>
                            </span>
                            <div class="leading-tight">
                                <div class="text-xs font-bold text-slate-800 dark:text-white" x-text="['', 'ข้อมูลส่วนตัว', 'ประวัติครอบครัว', 'การศึกษาและทำงาน', 'ข้อมูลเพิ่มเติม', 'บุคคลอ้างอิง'][step]"></div>
                                <div class="text-[10px] text-slate-400" x-text="['', 'Personal Info', 'Family Info', 'Education & Work', 'Additional Info', 'References & Consent'][step]"></div>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-red-50 text-[#B21F24] dark:bg-red-950/40 dark:text-red-400 border border-red-200 dark:border-red-900/50">
                            ขั้นตอนที่ <span x-text="step" class="mx-0.5"></span>/5 (<span x-text="(step * 20) + '%'"></span>)
                        </span>
                    </div>

                    <!-- 5 Connected Interactive Nodes -->
                    <div class="relative flex items-center justify-between px-3 pt-1 pb-0.5">
                        <!-- Progress line background -->
                        <div class="absolute left-6 right-6 top-1/2 -translate-y-1/2 h-1 bg-slate-200 dark:bg-slate-700"></div>
                        <!-- Active progress fill -->
                        <div class="absolute left-6 top-1/2 -translate-y-1/2 h-1 bg-[#B21F24] transition-all duration-300"
                            :style="'width: ' + ((step - 1) * 25) + '%;'"></div>

                        <!-- Step 1 Dot -->
                        <button type="button" @click="goToStep(1)" class="relative z-10 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold transition-all cursor-pointer"
                            :class="step === 1 ? 'bg-[#B21F24] text-white ring-4 ring-red-100 dark:ring-red-900/40 shadow-sm' : (step > 1 ? 'bg-emerald-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-400 border-2 border-slate-300 dark:border-slate-600')">
                            <template x-if="step > 1"><i class="fa-solid fa-check text-[10px]"></i></template>
                            <template x-if="step <= 1"><span>1</span></template>
                        </button>

                        <!-- Step 2 Dot -->
                        <button type="button" @click="goToStep(2)" class="relative z-10 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold transition-all cursor-pointer"
                            :class="step === 2 ? 'bg-[#B21F24] text-white ring-4 ring-red-100 dark:ring-red-900/40 shadow-sm' : (step > 2 ? 'bg-emerald-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-400 border-2 border-slate-300 dark:border-slate-600')">
                            <template x-if="step > 2"><i class="fa-solid fa-check text-[10px]"></i></template>
                            <template x-if="step <= 2"><span>2</span></template>
                        </button>

                        <!-- Step 3 Dot -->
                        <button type="button" @click="goToStep(3)" class="relative z-10 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold transition-all cursor-pointer"
                            :class="step === 3 ? 'bg-[#B21F24] text-white ring-4 ring-red-100 dark:ring-red-900/40 shadow-sm' : (step > 3 ? 'bg-emerald-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-400 border-2 border-slate-300 dark:border-slate-600')">
                            <template x-if="step > 3"><i class="fa-solid fa-check text-[10px]"></i></template>
                            <template x-if="step <= 3"><span>3</span></template>
                        </button>

                        <!-- Step 4 Dot -->
                        <button type="button" @click="goToStep(4)" class="relative z-10 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold transition-all cursor-pointer"
                            :class="step === 4 ? 'bg-[#B21F24] text-white ring-4 ring-red-100 dark:ring-red-900/40 shadow-sm' : (step > 4 ? 'bg-emerald-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-400 border-2 border-slate-300 dark:border-slate-600')">
                            <template x-if="step > 4"><i class="fa-solid fa-check text-[10px]"></i></template>
                            <template x-if="step <= 4"><span>4</span></template>
                        </button>

                        <!-- Step 5 Dot -->
                        <button type="button" @click="goToStep(5)" class="relative z-10 w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold transition-all cursor-pointer"
                            :class="step === 5 ? 'bg-[#B21F24] text-white ring-4 ring-red-100 dark:ring-red-900/40 shadow-sm' : 'bg-white dark:bg-slate-800 text-slate-400 border-2 border-slate-300 dark:border-slate-600'">
                            <span>5</span>
                        </button>
                    </div>
                </div>

                <!-- Tier 2: iPad / Tablet (640px to 1024px) -->
                <div class="hidden sm:grid lg:hidden grid-cols-5 gap-1.5 bg-white dark:bg-slate-800 p-2.5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 mb-4 max-w-[1060px] mx-auto">
                    <button type="button" @click="goToStep(1)" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl text-center transition-all cursor-pointer"
                        :class="step === 1 ? 'bg-red-50 dark:bg-red-950/40 text-[#B21F24] ring-1 ring-red-300 dark:ring-red-800 font-bold' : (step > 1 ? 'text-emerald-700 dark:text-emerald-400 hover:bg-slate-50' : 'text-slate-500 hover:bg-slate-50')">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs mb-1"
                             :class="step === 1 ? 'bg-[#B21F24] text-white shadow-sm' : (step > 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700')">
                            <template x-if="step > 1"><i class="fa-solid fa-check text-[10px]"></i></template>
                            <template x-if="step <= 1"><i class="fa-solid fa-id-card text-xs"></i></template>
                        </div>
                        <span class="text-[11px] font-bold truncate w-full">1. ส่วนตัว</span>
                    </button>

                    <button type="button" @click="goToStep(2)" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl text-center transition-all cursor-pointer"
                        :class="step === 2 ? 'bg-red-50 dark:bg-red-950/40 text-[#B21F24] ring-1 ring-red-300 dark:ring-red-800 font-bold' : (step > 2 ? 'text-emerald-700 dark:text-emerald-400 hover:bg-slate-50' : 'text-slate-500 hover:bg-slate-50')">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs mb-1"
                             :class="step === 2 ? 'bg-[#B21F24] text-white shadow-sm' : (step > 2 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700')">
                            <template x-if="step > 2"><i class="fa-solid fa-check text-[10px]"></i></template>
                            <template x-if="step <= 2"><i class="fa-solid fa-users text-xs"></i></template>
                        </div>
                        <span class="text-[11px] font-bold truncate w-full">2. ครอบครัว</span>
                    </button>

                    <button type="button" @click="goToStep(3)" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl text-center transition-all cursor-pointer"
                        :class="step === 3 ? 'bg-red-50 dark:bg-red-950/40 text-[#B21F24] ring-1 ring-red-300 dark:ring-red-800 font-bold' : (step > 3 ? 'text-emerald-700 dark:text-emerald-400 hover:bg-slate-50' : 'text-slate-500 hover:bg-slate-50')">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs mb-1"
                             :class="step === 3 ? 'bg-[#B21F24] text-white shadow-sm' : (step > 3 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700')">
                            <template x-if="step > 3"><i class="fa-solid fa-check text-[10px]"></i></template>
                            <template x-if="step <= 3"><i class="fa-solid fa-graduation-cap text-xs"></i></template>
                        </div>
                        <span class="text-[11px] font-bold truncate w-full">3. เรียน/งาน</span>
                    </button>

                    <button type="button" @click="goToStep(4)" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl text-center transition-all cursor-pointer"
                        :class="step === 4 ? 'bg-red-50 dark:bg-red-950/40 text-[#B21F24] ring-1 ring-red-300 dark:ring-red-800 font-bold' : (step > 4 ? 'text-emerald-700 dark:text-emerald-400 hover:bg-slate-50' : 'text-slate-500 hover:bg-slate-50')">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs mb-1"
                             :class="step === 4 ? 'bg-[#B21F24] text-white shadow-sm' : (step > 4 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700')">
                            <template x-if="step > 4"><i class="fa-solid fa-check text-[10px]"></i></template>
                            <template x-if="step <= 4"><i class="fa-solid fa-star text-xs"></i></template>
                        </div>
                        <span class="text-[11px] font-bold truncate w-full">4. เพิ่มเติม</span>
                    </button>

                    <button type="button" @click="goToStep(5)" class="flex flex-col items-center justify-center py-2 px-1 rounded-xl text-center transition-all cursor-pointer"
                        :class="step === 5 ? 'bg-red-50 dark:bg-red-950/40 text-[#B21F24] ring-1 ring-red-300 dark:ring-red-800 font-bold' : 'text-slate-500 hover:bg-slate-50'">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs mb-1"
                             :class="step === 5 ? 'bg-[#B21F24] text-white shadow-sm' : 'bg-slate-100 text-slate-400 dark:bg-slate-700'">
                            <i class="fa-solid fa-file-signature text-xs"></i>
                        </div>
                        <span class="text-[11px] font-bold truncate w-full">5. อ้างอิง</span>
                    </button>
                </div>

                <!-- Tier 3: Notebook & Desktop PC (>= 1024px) -->
                <div class="hidden lg:flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 mb-5 max-w-[1060px] mx-auto px-4 gap-2">
                    
                    <!-- Step 1 -->
                    <button type="button" @click="goToStep(1)" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base font-bold transition-all shrink-0"
                            :class="step === 1 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : (step > 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200')">
                            <template x-if="step > 1">
                                <i class="fa-solid fa-check text-sm"></i>
                            </template>
                            <template x-if="step <= 1">
                                <i class="fa-solid fa-id-card"></i>
                            </template>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold transition-colors leading-tight"
                                :class="step === 1 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : (step > 1 ? 'text-slate-800 dark:text-slate-200' : 'text-slate-500 dark:text-slate-400')">
                                ข้อมูลส่วนตัว
                            </span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                Personal Info
                            </span>
                        </div>
                    </button>

                    <!-- Arrow 1 -> 2 -->
                    <div class="text-slate-300 dark:text-slate-600 text-sm shrink-0">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>

                    <!-- Step 2 -->
                    <button type="button" @click="goToStep(2)" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base font-bold transition-all shrink-0"
                            :class="step === 2 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : (step > 2 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200')">
                            <template x-if="step > 2">
                                <i class="fa-solid fa-check text-sm"></i>
                            </template>
                            <template x-if="step <= 2">
                                <i class="fa-solid fa-users"></i>
                            </template>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold transition-colors leading-tight"
                                :class="step === 2 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : (step > 2 ? 'text-slate-800 dark:text-slate-200' : 'text-slate-500 dark:text-slate-400')">
                                ประวัติครอบครัว
                            </span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                Family Info
                            </span>
                        </div>
                    </button>

                    <!-- Arrow 2 -> 3 -->
                    <div class="text-slate-300 dark:text-slate-600 text-sm shrink-0">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>

                    <!-- Step 3 -->
                    <button type="button" @click="goToStep(3)" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base font-bold transition-all shrink-0"
                            :class="step === 3 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : (step > 3 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200')">
                            <template x-if="step > 3">
                                <i class="fa-solid fa-check text-sm"></i>
                            </template>
                            <template x-if="step <= 3">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </template>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold transition-colors leading-tight"
                                :class="step === 3 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : (step > 3 ? 'text-slate-800 dark:text-slate-200' : 'text-slate-500 dark:text-slate-400')">
                                การศึกษาและทำงาน
                            </span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                Education & Work
                            </span>
                        </div>
                    </button>

                    <!-- Arrow 3 -> 4 -->
                    <div class="text-slate-300 dark:text-slate-600 text-sm shrink-0">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>

                    <!-- Step 4 -->
                    <button type="button" @click="goToStep(4)" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base font-bold transition-all shrink-0"
                            :class="step === 4 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : (step > 4 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200')">
                            <template x-if="step > 4">
                                <i class="fa-solid fa-check text-sm"></i>
                            </template>
                            <template x-if="step <= 4">
                                <i class="fa-solid fa-star"></i>
                            </template>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold transition-colors leading-tight"
                                :class="step === 4 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : (step > 4 ? 'text-slate-800 dark:text-slate-200' : 'text-slate-500 dark:text-slate-400')">
                                ข้อมูลเพิ่มเติม
                            </span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                Additional Info
                            </span>
                        </div>
                    </button>

                    <!-- Arrow 4 -> 5 -->
                    <div class="text-slate-300 dark:text-slate-600 text-sm shrink-0">
                        <i class="fa-solid fa-chevron-right"></i>
                    </div>

                    <!-- Step 5 -->
                    <button type="button" @click="goToStep(5)" class="flex items-center gap-3 group text-left cursor-pointer transition-all">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-base font-bold transition-all shrink-0"
                            :class="step === 5 ? 'bg-[#B21F24] text-white shadow-md shadow-red-500/20 scale-105' : 'bg-slate-100 text-slate-400 dark:bg-slate-700/60 dark:text-slate-400 group-hover:bg-slate-200'">
                            <i class="fa-solid fa-file-signature"></i>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold transition-colors leading-tight"
                                :class="step === 5 ? 'text-[#B21F24] dark:text-red-400 font-extrabold' : 'text-slate-500 dark:text-slate-400'">
                                บุคคลอ้างอิง
                            </span>
                            <span class="text-xs text-slate-400 dark:text-slate-500 leading-tight mt-0.5">
                                References & Consent
                            </span>
                        </div>
                    </button>
                </div>

                <form action="{{ route('recruitment.submit', $post->slug) }}" method="POST" enctype="multipart/form-data"
                    @submit.prevent="confirmSubmit" novalidate>
                    @csrf
                    <input type="hidden" name="position_applied" value="{{ $post->position_name }}">

                    <!-- =========================================================================
                         DOCUMENT SHEET 1 / 5: PERSONAL INFORMATION (PAPER MODE)
                    ========================================================================== -->
                    <div x-show="viewMode === 'paper' && step === 1" class="paper-sheet-container mb-6">
                    <div class="paper-sheet pt-10 pb-8 px-5 sm:pt-12 sm:pb-10 sm:px-8 md:px-12 lg:px-14 rounded-lg relative" :class="{ 'font-scale-md': fontScale === 'md', 'font-scale-lg': fontScale === 'lg' }">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <input type="text" name="application_no_custom" class="doc-input w-32 sm:w-36 text-center">
                            </div>
                            <div class="font-bold text-xs">1/5</div>
                        </div>

                        <!-- Form Title & Photo Box -->
                        <div class="relative mb-6 min-h-[140px] flex justify-between items-start">
                            <div class="text-center flex-1 pr-2 sm:pr-4 pl-0 pt-1">
                                <h2 class="text-sm sm:text-base md:text-lg font-black tracking-wide uppercase font-sans">APPLICATION FOR EMPLOYMENT</h2>
                                <h3 class="text-lg sm:text-xl md:text-2xl font-black mt-0.5 sm:mt-1">ใบสมัครงาน</h3>
                                <p class="text-xs font-bold text-gray-800 mt-1">กรอกข้อมูลด้วยตัวท่านเอง</p>
                                <p class="text-[10px] sm:text-[11px] text-gray-500 italic">(To be completed in own handwriting)</p>
                            </div>
                            <!-- Photo Box on Top Right with border matching document -->
                            <div class="shrink-0">
                                <div class="w-[100px] sm:w-[110px] h-[130px] sm:h-[140px] border border-black flex flex-col items-center justify-center p-2 text-center relative bg-white group cursor-pointer hover:border-[#B21F24]">
                                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" @change="handleFileChange($event, 'photo')" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                    <template x-if="!photoPreview">
                                        <div class="space-y-1 text-center">
                                            <svg class="w-8 h-8 text-gray-400 group-hover:text-[#B21F24] transition-colors mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <p class="text-[10px] font-bold text-gray-500">ติดรูปถ่าย<br>1.5 - 2 นิ้ว<br><span class="text-[9px] font-normal text-gray-400">(Photo)</span></p>
                                        </div>
                                    </template>
                                    <template x-if="photoPreview">
                                        <div class="w-full h-full flex flex-col items-center justify-center">
                                            <img :src="photoPreview" class="w-full h-full object-cover">
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Main Form Body: Page 1 -->
                        <div class="space-y-5 text-sm">
                            <!-- Row 1: Name, Last Name & Nickname -->
                            <div class="flex items-end gap-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold text-base leading-tight">ชื่อ :</span>
                                    <span class="sub-label">Name</span>
                                </div>
                                <input type="text" name="first_name" x-model="firstName" @input="syncSignature()" required value="{{ old('first_name') }}" class="doc-input flex-grow font-semibold">
                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-bold text-base leading-tight">นามสกุล :</span>
                                    <span class="sub-label">Last Name</span>
                                </div>
                                <input type="text" name="last_name" x-model="lastName" @input="syncSignature()" required value="{{ old('last_name') }}" class="doc-input flex-grow font-semibold">
                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-bold text-base leading-tight">ชื่อเล่น</span>
                                    <span class="sub-label">Nickname</span>
                                </div>
                                <input type="text" name="nickname" value="{{ old('nickname') }}" class="doc-input w-28 text-center font-semibold">
                            </div>

                            <!-- Row 2: Position Applied for -->
                            <div class="flex items-end gap-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold text-base leading-tight">ตำแหน่งที่ต้องการสมัคร</span>
                                    <span class="sub-label">Position Applied for</span>
                                </div>
                                <input type="text" name="position_applied_custom" value="{{ $post->position_name }}" class="doc-input flex-grow font-bold text-[#B21F24]">
                            </div>

                            <!-- Section: Personal Information -->
                            <div class="pt-2">
                                <h4 class="font-bold text-base text-black">Personal information (ประวัติส่วนตัว)</h4>
                            </div>

                            <!-- Present Address Line 1 -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ที่อยู่ปัจจุบันเลขที่</span>
                                    <span class="sub-label">Present address</span>
                                </div>
                                <input type="text" name="house_no" value="{{ old('house_no') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input w-20 text-center font-semibold shrink-0">

                                <div class="shrink-0 flex flex-col ml-1">
                                    <span class="shrink-0 font-medium text-sm leading-tight">หมู่ที่</span>
                                    <span class="sub-label">Moo</span>
                                </div>
                                <input type="text" name="moo" value="{{ old('moo') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input w-16 text-center font-semibold shrink-0">

                                <div class="shrink-0 flex flex-col ml-1">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ถนน</span>
                                    <span class="sub-label">Road</span>
                                </div>
                                <input type="text" name="road" value="{{ old('road') }}" class="doc-input flex-1 min-w-[80px] font-semibold">

                                <div class="shrink-0 flex flex-col ml-1">
                                    <span class="shrink-0 font-medium text-sm leading-tight">จังหวัด</span>
                                    <span class="sub-label">Province</span>
                                </div>
                                <input type="text" id="addr_province" name="province" value="{{ old('province') }}" autocomplete="off" class="doc-input flex-1 min-w-[80px] font-semibold thai-addr-input">
                            </div>

                            <!-- Present Address Line 2 -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">อำเภอ/เขต</span>
                                    <span class="sub-label">District</span>
                                </div>
                                <input type="text" id="addr_district" name="district" value="{{ old('district') }}" autocomplete="off" class="doc-input flex-1 min-w-[80px] font-semibold thai-addr-input">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ตำบล/แขวง</span>
                                    <span class="sub-label">Subdistrict</span>
                                </div>
                                <input type="text" id="addr_subdistrict" name="subdistrict" value="{{ old('subdistrict') }}" autocomplete="off" class="doc-input flex-1 min-w-[80px] font-semibold thai-addr-input">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">รหัสไปรษณีย์</span>
                                    <span class="sub-label">Post code</span>
                                </div>
                                <input type="text" id="addr_postcode" name="postcode" value="{{ old('postcode') }}" autocomplete="off" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input w-32 text-center font-semibold shrink-0 thai-addr-input">
                            </div>

                            <!-- Phone, Mobile, ID Line -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">โทรศัพท์</span>
                                    <span class="sub-label">Tel.</span>
                                </div>
                                <input type="text" name="tel" value="{{ old('tel') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input flex-1 min-w-[60px]">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium font-bold text-sm leading-tight">มือถือ</span>
                                    <span class="sub-label">Mobile</span>
                                </div>
                                <input type="tel" name="phone" required value="{{ old('phone') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input flex-1 min-w-[80px] font-bold text-[#B21F24]">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ID Line</span>
                                    <span class="sub-label">&nbsp;</span>
                                </div>
                                <input type="text" name="line_id" value="{{ old('line_id') }}" class="doc-input flex-1 min-w-[60px]">
                            </div>

                            <!-- Facebook & Email -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">Facebook</span>
                                    <span class="sub-label">&nbsp;</span>
                                </div>
                                <input type="text" name="facebook" value="{{ old('facebook') }}" class="doc-input flex-1 min-w-[80px]">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium font-bold text-sm leading-tight">อีเมล์</span>
                                    <span class="sub-label">E-mail</span>
                                </div>
                                <input type="email" name="email" required value="{{ old('email') }}" class="doc-input flex-1 min-w-[80px] font-bold text-[#B21F24]">
                            </div>

                            <!-- Housing Type Checkboxes -->
                            <div class="pt-2 pb-1 flex items-center justify-between font-medium">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="housing_type" value="อาศัยกับครอบครัว" class="paper-checkbox">
                                    <span class="text-sm">อาศัยกับครอบครัว <span class="sub-label">Living with parent</span></span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="housing_type" value="บ้านตัวเอง" class="paper-checkbox">
                                    <span class="text-sm">บ้านตัวเอง <span class="sub-label">Own house</span></span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="housing_type" value="บ้านเช่า" class="paper-checkbox">
                                    <span class="text-sm">บ้านเช่า <span class="sub-label">Rented house</span></span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="housing_type" value="หอพัก" class="paper-checkbox">
                                    <span class="text-sm">หอพัก <span class="sub-label">Dormitory / Hostel</span></span>
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="housing_type" value="คอนโดมิเนียม" class="paper-checkbox">
                                    <span class="text-sm">คอนโดมิเนียม <span class="sub-label">Condominium</span></span>
                                </label>
                            </div>

                            <!-- DOB, Age, Place of Birth -->
                            <div class="flex items-end gap-2 pt-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">วัน เดือน ปีเกิด</span>
                                    <span class="sub-label">Date of birth</span>
                                </div>
                                <input type="text" id="dob_input" name="date_of_birth" x-model="dob" @change="calcAge()" @input="calcAge()" value="{{ old('date_of_birth') }}" placeholder="วว/ดด/ปปปป" class="doc-input w-40 shrink-0 text-center datepicker-th font-semibold">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">อายุ</span>
                                    <span class="sub-label">Age Yrs.</span>
                                </div>
                                <input type="number" name="age" x-model="age" class="doc-input w-16 text-center font-bold shrink-0">
                                <span class="shrink-0 font-medium text-sm pb-1">ปี</span>

                                <div class="shrink-0 flex flex-col ml-4">
                                    <span class="shrink-0 font-medium text-sm leading-tight">สถานที่เกิด</span>
                                    <span class="sub-label">Place of Birth</span>
                                </div>
                                <input type="text" name="place_of_birth" value="{{ old('place_of_birth') }}" class="doc-input flex-1 min-w-[80px]">
                            </div>

                            <!-- Race, Nationality, Religion -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">เชื้อชาติ</span>
                                    <span class="sub-label">Race</span>
                                </div>
                                <input type="text" name="race" value="{{ old('race', 'ไทย') }}" class="doc-input flex-1 min-w-[60px] text-center">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">สัญชาติ</span>
                                    <span class="sub-label">Nationality</span>
                                </div>
                                <input type="text" name="nationality" value="{{ old('nationality', 'ไทย') }}" class="doc-input flex-1 min-w-[60px] text-center">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ศาสนา</span>
                                    <span class="sub-label">Religion</span>
                                </div>
                                <input type="text" name="religion" value="{{ old('religion', 'พุทธ') }}" class="doc-input flex-1 min-w-[60px] text-center">
                            </div>

                            <!-- ID Card Details -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">บัตรประชาชนเลขที่</span>
                                    <span class="sub-label">Identity card no.</span>
                                </div>
                                <input type="text" name="national_id" value="{{ old('national_id') }}" maxlength="17" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input flex-1 min-w-[130px] font-mono">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ออกโดย</span>
                                    <span class="sub-label">Issued by</span>
                                </div>
                                <input type="text" name="id_card_issued_by" value="{{ old('id_card_issued_by') }}" class="doc-input flex-1 min-w-[80px]">

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="shrink-0 font-medium text-sm leading-tight">จังหวัด</span>
                                    <span class="sub-label">Province</span>
                                </div>
                                <input type="text" id="id_card_issued_province" name="id_card_issued_province" value="{{ old('id_card_issued_province') }}" autocomplete="off" class="doc-input flex-1 min-w-[80px] font-semibold thai-addr-input">
                            </div>

                            <!-- Issued Date & Expiry Date -->
                            <div class="flex items-end gap-2 w-full">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">วันออกบัตร</span>
                                    <span class="sub-label">Issued Date</span>
                                </div>
                                <input type="text" name="id_card_issued_date" value="{{ old('id_card_issued_date') }}" placeholder="วว/ดด/ปปปป" class="doc-input flex-1 min-w-[100px] text-center datepicker-th font-semibold">

                                <div class="shrink-0 flex flex-col ml-4">
                                    <span class="shrink-0 font-medium text-sm leading-tight">วันหมดอายุ</span>
                                    <span class="sub-label">Expiration date</span>
                                </div>
                                <input type="text" name="id_card_expiry_date" value="{{ old('id_card_expiry_date') }}" placeholder="วว/ดด/ปปปป" class="doc-input flex-1 min-w-[100px] text-center datepicker-th font-semibold">
                            </div>

                            <!-- Height & Weight -->
                            <div class="flex items-end gap-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium text-sm leading-tight">ส่วนสูง</span>
                                    <span class="sub-label">Height cm.</span>
                                </div>
                                <input type="number" step="0.5" name="height_cm" value="{{ old('height_cm') }}" class="doc-input w-24 text-center">
                                <span class="shrink-0 font-medium text-sm pb-1">ซม.</span>

                                <div class="shrink-0 flex flex-col ml-12">
                                    <span class="shrink-0 font-medium text-sm leading-tight">น้ำหนัก</span>
                                    <span class="sub-label">Weight kgs.</span>
                                </div>
                                <input type="number" step="0.5" name="weight_kg" value="{{ old('weight_kg') }}" class="doc-input w-24 text-center">
                                <span class="shrink-0 font-medium text-sm pb-1">กก.</span>
                            </div>

                            <!-- Military Status -->
                            <div class="grid grid-cols-12 gap-2 pt-2 items-center">
                                <div class="col-span-3">
                                    <span class="font-medium text-sm">สถานะทางทหาร</span>
                                    <span class="sub-label">Military status</span>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="military_status" value="ได้รับการยกเว้น" class="paper-checkbox">
                                        <span class="text-sm">ได้รับการยกเว้น <span class="sub-label">Exempted</span></span>
                                    </label>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="military_status" value="ปลดเป็นทหารกองหนุน" class="paper-checkbox">
                                        <span class="text-sm">ปลดเป็นทหารกองหนุน <span class="sub-label">Served</span></span>
                                    </label>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="military_status" value="ยังไม่ได้รับการเกณฑ์" class="paper-checkbox">
                                        <span class="text-sm">ยังไม่ได้รับการเกณฑ์ <span class="sub-label">Not yet served</span></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Marital Status -->
                            <div class="grid grid-cols-12 gap-2 pt-2 items-center">
                                <div class="col-span-3">
                                    <span class="font-medium text-sm">สถานภาพ</span>
                                    <span class="sub-label">Marital status</span>
                                </div>
                                <div class="col-span-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="marital_status" value="โสด" x-model="maritalStatus" :checked="maritalStatus === 'โสด'" class="paper-checkbox">
                                        <span class="text-sm">โสด <span class="sub-label">Single</span></span>
                                    </label>
                                </div>
                                <div class="col-span-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="marital_status" value="แต่งงาน" x-model="maritalStatus" :checked="maritalStatus === 'แต่งงาน'" class="paper-checkbox">
                                        <span class="text-sm">แต่งงาน <span class="sub-label">Married</span></span>
                                    </label>
                                </div>
                                <div class="col-span-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="marital_status" value="หม้าย" x-model="maritalStatus" :checked="maritalStatus === 'หม้าย'" class="paper-checkbox">
                                        <span class="text-sm">หม้าย <span class="sub-label">Widowed</span></span>
                                    </label>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="marital_status" value="แยกกัน" x-model="maritalStatus" :checked="maritalStatus === 'แยกกัน'" class="paper-checkbox">
                                        <span class="text-sm">แยกกัน <span class="sub-label">Separated</span></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Sex -->
                            <div class="grid grid-cols-12 gap-2 pt-2 items-center">
                                <div class="col-span-3">
                                    <span class="font-medium text-sm">เพศ</span>
                                    <span class="sub-label">Sex</span>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="gender" value="ชาย" checked class="paper-checkbox">
                                        <span class="text-sm">ชาย <span class="sub-label">Male</span></span>
                                    </label>
                                </div>
                                <div class="col-span-3">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="gender" value="หญิง" class="paper-checkbox">
                                        <span class="text-sm">หญิง <span class="sub-label">Female</span></span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-14 pt-4 border-t border-gray-300 flex justify-between items-center text-[11px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                    </div>


                    <!-- =========================================================================
                         DOCUMENT SHEET 2 / 5: FAMILY INFORMATION & EMERGENCY CONTACT (PAPER MODE)
                    ========================================================================== -->
                    <div x-show="viewMode === 'paper' && step === 2" class="paper-sheet-container mb-6">
                    <div class="paper-sheet pt-10 pb-8 px-5 sm:pt-12 sm:pb-10 sm:px-8 md:px-12 lg:px-14 rounded-lg relative" :class="{ 'font-scale-md': fontScale === 'md', 'font-scale-lg': fontScale === 'lg' }">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <input type="text" class="doc-input w-36 text-center">
                            </div>
                            <div class="font-bold text-xs">2/5</div>
                        </div>

                        <div class="space-y-5 text-sm">
                            <h4 class="font-bold text-base text-black">Family Information (ประวัติครอบครัว)</h4>

                            <!-- Father Information -->
                            <div class="space-y-3 pt-1">
                                <!-- Father Row 1: Name, Age, Occupation -->
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">บิดา ชื่อ-สกุล <span class="text-[11px] font-normal text-gray-500">(ไม่บังคับกรอก)</span></span>
                                            <span class="sub-label">Father’s name-surname (Optional)</span>
                                        </div>
                                        <input type="text" name="family_info[father][name]" placeholder="ชื่อ-นามสกุล บิดา (ไม่บังคับ)" class="doc-input flex-grow font-semibold">
                                    </div>
                                    <div class="flex items-end gap-1 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อายุ</span>
                                            <span class="sub-label">Age Yrs.</span>
                                        </div>
                                        <input type="number" name="family_info[father][age]" class="doc-input w-12 text-center font-semibold">
                                        <span class="shrink-0 font-medium">ปี</span>
                                    </div>
                                    <div class="flex items-end gap-2 flex-1 min-w-[160px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อาชีพ</span>
                                            <span class="sub-label">Occupation</span>
                                        </div>
                                        <input type="text" name="family_info[father][occupation]" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>

                                <!-- Father Row 2: Workplace, Phone -->
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">สถานที่ทำงาน</span>
                                            <span class="sub-label">Place of work</span>
                                        </div>
                                        <input type="text" name="family_info[father][workplace]" class="doc-input flex-grow font-semibold">
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">เบอร์โทร</span>
                                            <span class="sub-label">Mobile</span>
                                        </div>
                                        <input type="tel" name="family_info[father][phone]" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>
                            </div>

                            <!-- Mother Information -->
                            <div class="space-y-3 pt-2 border-t border-gray-200">
                                <!-- Mother Row 1: Name, Age, Occupation -->
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">มารดา ชื่อ-สกุล <span class="text-[11px] font-normal text-gray-500">(ไม่บังคับกรอก)</span></span>
                                            <span class="sub-label">Mother’s name-surname (Optional)</span>
                                        </div>
                                        <input type="text" name="family_info[mother][name]" placeholder="ชื่อ-นามสกุล มารดา (ไม่บังคับ)" class="doc-input flex-grow font-semibold">
                                    </div>
                                    <div class="flex items-end gap-1 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อายุ</span>
                                            <span class="sub-label">Age Yrs.</span>
                                        </div>
                                        <input type="number" name="family_info[mother][age]" class="doc-input w-12 text-center font-semibold">
                                        <span class="shrink-0 font-medium">ปี</span>
                                    </div>
                                    <div class="flex items-end gap-2 flex-1 min-w-[160px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อาชีพ</span>
                                            <span class="sub-label">Occupation</span>
                                        </div>
                                        <input type="text" name="family_info[mother][occupation]" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>

                                <!-- Mother Row 2: Workplace, Phone -->
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">สถานที่ทำงาน</span>
                                            <span class="sub-label">Place of work</span>
                                        </div>
                                        <input type="text" name="family_info[mother][workplace]" class="doc-input flex-grow font-semibold">
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">เบอร์โทร</span>
                                            <span class="sub-label">Mobile</span>
                                        </div>
                                        <input type="tel" name="family_info[mother][phone]" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>
                            </div>

                            <!-- Single status indicator (Spouse hidden) -->
                            <div x-show="maritalStatus === 'โสด'" class="p-3 my-2 rounded-lg border border-dashed border-gray-300 bg-gray-50 text-xs text-gray-500 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                <span>สถานภาพ <strong>โสด</strong> — ไม่ต้องระบุข้อมูลภรรยา/สามี (ระบบซ่อนช่องกรอกข้อมูลคู่สมรส)</span>
                            </div>

                            <!-- Spouse Information (Shown only when Married / Widowed / Separated) -->
                            <div x-show="maritalStatus !== 'โสด'" x-transition class="space-y-3 pt-2 border-t border-gray-200">
                                <div class="flex items-center justify-between pb-1">
                                    <span class="font-bold text-sm text-gray-800 flex items-center gap-2">
                                        <span>ข้อมูลภรรยา/สามี (Spouse Information)</span>
                                        <template x-if="maritalStatus === 'แต่งงาน'">
                                            <span class="text-xs font-normal text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded">ระบุข้อมูลคู่สมรส</span>
                                        </template>
                                        <template x-if="maritalStatus === 'หม้าย' || maritalStatus === 'แยกกัน'">
                                            <span class="text-xs font-normal text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">สามารถกรอกได้ตามความเหมาะสม (ไม่บังคับ)</span>
                                        </template>
                                    </span>
                                </div>

                                <!-- Spouse Row 1: Name, Age, Occupation -->
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ชื่อภรรยา/สามี</span>
                                            <span class="sub-label">Name of wife / Husband</span>
                                        </div>
                                        <input type="text" name="family_info[spouse][name]" :disabled="maritalStatus === 'โสด'" placeholder="ชื่อ-นามสกุล สามี/ภรรยา" class="doc-input flex-grow font-semibold">
                                    </div>
                                    <div class="flex items-end gap-1 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อายุ</span>
                                            <span class="sub-label">Age Yrs.</span>
                                        </div>
                                        <input type="number" name="family_info[spouse][age]" :disabled="maritalStatus === 'โสด'" class="doc-input w-12 text-center font-semibold">
                                        <span class="shrink-0 font-medium">ปี</span>
                                    </div>
                                    <div class="flex items-end gap-2 flex-1 min-w-[160px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อาชีพ</span>
                                            <span class="sub-label">Occupation</span>
                                        </div>
                                        <input type="text" name="family_info[spouse][occupation]" :disabled="maritalStatus === 'โสด'" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>

                                <!-- Spouse Row 2: Workplace, Phone -->
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">สถานที่ทำงาน</span>
                                            <span class="sub-label">Place of work</span>
                                        </div>
                                        <input type="text" name="family_info[spouse][workplace]" :disabled="maritalStatus === 'โสด'" class="doc-input flex-grow font-semibold">
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">เบอร์โทร</span>
                                            <span class="sub-label">Mobile</span>
                                        </div>
                                        <input type="tel" name="family_info[spouse][phone]" :disabled="maritalStatus === 'โสด'" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>
                            </div>

                            <!-- Children Count -->
                            <div class="flex items-end gap-2 pt-1">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-medium leading-tight">มีบุตร</span>
                                    <span class="sub-label">Number of children</span>
                                </div>
                                <input type="number" name="family_info[children_count]" min="0" value="{{ old('family_info.children_count', '0') }}" placeholder="0" class="doc-input w-24 text-center font-bold">
                                <span class="shrink-0 font-medium">คน</span>
                                <span class="text-xs text-gray-500 ml-2">(หากไม่มีบุตร ให้ระบุ 0 คน หรือเว้นว่างได้ ไม่บังคับกรอกรายละเอียด)</span>
                            </div>

                            <!-- Siblings Stats -->
                            <div class="flex items-end gap-2 w-full flex-wrap pt-1">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-medium leading-tight">มีพี่น้อง (รวมผู้สมัคร)</span>
                                    <span class="sub-label">Number of Members in the family</span>
                                </div>
                                <input type="number" name="family_info[total_siblings]" class="doc-input w-16 text-center font-bold">
                                <span class="shrink-0 font-medium">คน</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-medium leading-tight">ชาย</span>
                                    <span class="sub-label">Male</span>
                                </div>
                                <input type="number" name="family_info[siblings_male]" class="doc-input w-14 text-center">
                                <span class="shrink-0 font-medium">คน</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-medium leading-tight">หญิง</span>
                                    <span class="sub-label">Female</span>
                                </div>
                                <input type="number" name="family_info[siblings_female]" class="doc-input w-14 text-center">
                                <span class="shrink-0 font-medium">คน</span>

                                <div class="shrink-0 flex flex-col ml-2">
                                    <span class="font-medium leading-tight">เป็นบุตรคนที่</span>
                                    <span class="sub-label">Ordinal number of children</span>
                                </div>
                                <input type="number" name="family_info[birth_order]" class="doc-input w-14 text-center font-bold">
                            </div>

                            <!-- Siblings Table (Exact box from PDF) -->
                            <div class="pt-2">
                                <table class="w-full doc-table text-xs">
                                    <thead>
                                        <tr>
                                            <th class="w-1/2 py-1.5">ชื่อ<br><span class="sub-label">Name</span></th>
                                            <th class="w-1/4 py-1.5">อายุ (ปี)<br><span class="sub-label">Age</span></th>
                                            <th class="w-1/4 py-1.5">อาชีพ<br><span class="sub-label">Occupation</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(sib, sIdx) in siblings" :key="sIdx">
                                            <tr>
                                                <td class="p-1">
                                                    <input type="text" :name="'family_info[siblings]['+sIdx+'][name]'" x-model="sib.name" class="w-full doc-input">
                                                </td>
                                                <td class="p-1">
                                                    <input type="number" :name="'family_info[siblings]['+sIdx+'][age]'" x-model="sib.age" class="w-full doc-input text-center">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'family_info[siblings]['+sIdx+'][occupation]'" x-model="sib.occupation" class="w-full doc-input">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Section: Emergency Contact -->
                            <div class="pt-4 border-t-2 border-gray-400 space-y-3">
                                <h4 class="font-bold text-sm text-black">บุคคลสำหรับติดต่อในกรณีฉุกเฉิน<br><span class="sub-label text-xs">Emergency Contact</span></h4>

                                <!-- Name & Relationship -->
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ชื่อ- สกุล</span>
                                            <span class="sub-label">Name – Surname</span>
                                        </div>
                                        <input type="text" name="emergency_contact[name]" class="doc-input flex-grow font-semibold">
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ความสัมพันธ์</span>
                                            <span class="sub-label">Relationship</span>
                                        </div>
                                        <input type="text" name="emergency_contact[relationship]" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>

                                <!-- Emergency Address Line 1 -->
                                <div class="flex items-end gap-2 w-full">
                                    <div class="shrink-0 flex flex-col">
                                        <span class="font-medium leading-tight">ที่อยู่เลขที่</span>
                                        <span class="sub-label">Address</span>
                                    </div>
                                    <input type="text" name="emergency_contact[house_no]" value="{{ old('emergency_contact.house_no') }}" class="doc-input w-20 text-center font-semibold shrink-0">

                                    <div class="shrink-0 flex flex-col ml-1">
                                        <span class="font-medium leading-tight">หมู่ที่ <span class="text-[10px] font-normal text-gray-500">(ถ้ามี)</span></span>
                                        <span class="sub-label">Moo</span>
                                    </div>
                                    <input type="text" name="emergency_contact[moo]" value="{{ old('emergency_contact.moo') }}" inputmode="numeric" class="doc-input w-16 text-center">

                                    <div class="shrink-0 flex flex-col ml-1">
                                        <span class="font-medium leading-tight">ซอย <span class="text-[10px] font-normal text-gray-500">(ถ้ามี)</span></span>
                                        <span class="sub-label">Soi (Optional)</span>
                                    </div>
                                    <input type="text" name="emergency_contact[soi]" value="{{ old('emergency_contact.soi') }}" class="doc-input w-28">

                                    <div class="shrink-0 flex flex-col ml-1">
                                        <span class="font-medium leading-tight">ถนน <span class="text-[10px] font-normal text-gray-500">(ถ้ามี)</span></span>
                                        <span class="sub-label">Road (Optional)</span>
                                    </div>
                                    <input type="text" name="emergency_contact[road]" value="{{ old('emergency_contact.road') }}" class="doc-input flex-grow">
                                </div>

                                <!-- Emergency Address Line 2: Province, District, Subdistrict, Mobile -->
                                <div class="flex items-end gap-2.5 w-full">
                                    <div class="flex items-end gap-1.5 flex-1 min-w-[120px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">จังหวัด</span>
                                            <span class="sub-label">Province</span>
                                        </div>
                                        <input type="text" id="emerg_province" name="emergency_contact[province]" autocomplete="off" class="doc-input flex-grow thai-addr-input font-semibold">
                                    </div>
                                    <div class="flex items-end gap-1.5 flex-1 min-w-[120px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">อำเภอ/เขต</span>
                                            <span class="sub-label">District</span>
                                        </div>
                                        <input type="text" id="emerg_district" name="emergency_contact[district]" autocomplete="off" class="doc-input flex-grow thai-addr-input">
                                    </div>
                                    <div class="flex items-end gap-1.5 flex-1 min-w-[120px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ตำบล/แขวง</span>
                                            <span class="sub-label">Subdistrict</span>
                                        </div>
                                        <input type="text" id="emerg_subdistrict" name="emergency_contact[subdistrict]" autocomplete="off" class="doc-input flex-grow thai-addr-input">
                                    </div>
                                    <div class="flex items-end gap-1.5 w-56 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium font-bold leading-tight">เบอร์โทร</span>
                                            <span class="sub-label">Mobile</span>
                                        </div>
                                        <input type="tel" name="emergency_contact[mobile]" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="doc-input flex-grow font-bold text-[#B21F24]">
                                    </div>
                                </div>

                                <!-- Workplace & Work Phone -->
                                <div class="flex items-end gap-3 w-full">
                                    <div class="flex items-end gap-2 flex-1 min-w-[220px]">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">ที่ทำงาน <span class="text-[10px] font-normal text-gray-500">(ถ้ามี)</span></span>
                                            <span class="sub-label">Place of work (Optional)</span>
                                        </div>
                                        <input type="text" name="emergency_contact[workplace]" value="{{ old('emergency_contact.workplace') }}" class="doc-input flex-grow font-semibold">
                                    </div>
                                    <div class="flex items-end gap-2 w-64 shrink-0">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-medium leading-tight">โทรศัพท์ <span class="text-[10px] font-normal text-gray-500">(ถ้ามี)</span></span>
                                            <span class="sub-label">Working Place Phone (Optional)</span>
                                        </div>
                                        <input type="tel" name="emergency_contact[work_phone]" value="{{ old('emergency_contact.work_phone') }}" inputmode="numeric" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-12 pt-4 border-t border-gray-300 flex justify-between items-center text-[10px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                    </div>


                    <!-- =========================================================================
                         DOCUMENT SHEET 3 / 5: EDUCATION, EXPERIENCE & LANGUAGE (PAPER MODE)
                    ========================================================================== -->
                    <div x-show="viewMode === 'paper' && step === 3" class="paper-sheet-container mb-6">
                    <div class="paper-sheet pt-10 pb-8 px-5 sm:pt-12 sm:pb-10 sm:px-8 md:px-12 lg:px-14 rounded-lg relative" :class="{ 'font-scale-md': fontScale === 'md', 'font-scale-lg': fontScale === 'lg' }">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <input type="text" class="doc-input w-36 text-center">
                            </div>
                            <div class="font-bold text-xs">3/5</div>
                        </div>

                        <div class="space-y-6 text-sm">
                            <!-- Section: Education -->
                            <div class="space-y-2">
                                <h4 class="font-bold text-base text-black">Education (การศึกษา)</h4>
                                <table class="w-full doc-table text-sm">
                                    <thead>
                                        <tr>
                                            <th class="w-1/4 py-1.5">ระดับการศึกษา<br><span class="sub-label">Educational Level</span></th>
                                            <th class="w-1/3 py-1.5">สถาบันการศึกษา<br><span class="sub-label">Institution</span></th>
                                            <th class="w-1/4 py-1.5">สาขาวิชา<br><span class="sub-label">Major</span></th>
                                            <th class="w-20 py-1.5">ตั้งแต่<br><span class="sub-label">From</span></th>
                                            <th class="w-20 py-1.5">ถึง<br><span class="sub-label">To</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(edu, eIdx) in education" :key="eIdx">
                                            <tr>
                                                <td class="p-1 font-medium">
                                                    <select :name="'education['+eIdx+'][level]'" x-model="edu.level" class="w-full doc-input font-semibold bg-transparent focus:outline-none cursor-pointer">
                                                        <option value=""></option>
                                                        <option value="ประถมศึกษา">ประถมศึกษา</option>
                                                        <option value="มัธยมศึกษา">มัธยมศึกษา</option>
                                                        <option value="ปวช./ปวส.">ปวช./ปวส.</option>
                                                        <option value="ปริญญาตรี">ปริญญาตรี</option>
                                                        <option value="ปริญญาโท">ปริญญาโท</option>
                                                        <option value="ปริญญาเอก">ปริญญาเอก</option>
                                                        <option value="อื่นๆ">อื่นๆ</option>
                                                    </select>
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'education['+eIdx+'][institution_name]'" x-model="edu.institution_name" class="w-full doc-input">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'education['+eIdx+'][major]'" x-model="edu.major" class="w-full doc-input">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'education['+eIdx+'][start_year]'" x-model="edu.start_year" placeholder="" class="w-full doc-input text-center yearpicker-th cursor-pointer font-semibold" readonly autocomplete="off">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'education['+eIdx+'][end_year]'" x-model="edu.end_year" placeholder="" class="w-full doc-input text-center yearpicker-th cursor-pointer font-semibold" readonly autocomplete="off">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Section: Working Experience -->
                            <div class="space-y-2 pt-2">
                                <h4 class="font-bold text-sm text-black">Working Experience In Chronological (รายละเอียดของงานที่ผ่าน เรียงลำดับก่อน-หลัง)</h4>
                                <table class="w-full doc-table text-xs">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="w-[20%] py-1.5">สถานที่ทำงาน<br><span class="sub-label">Company</span></th>
                                            <th colspan="2" class="w-[220px] py-1">ระยะเวลา<br><span class="sub-label">TIME</span></th>
                                            <th rowspan="2" class="w-[16%] py-1.5">ตำแหน่งงาน<br><span class="sub-label">Position</span></th>
                                            <th rowspan="2" class="w-[20%] py-1.5">ลักษณะงาน<br><span class="sub-label">Job description</span></th>
                                            <th rowspan="2" class="w-[95px] min-w-[95px] py-1.5">ค่าจ้าง<br><span class="sub-label">Salary</span></th>
                                            <th rowspan="2" class="w-[16%] py-1.5">เหตุที่ออก<br><span class="sub-label">Reasons of resignation</span></th>
                                        </tr>
                                        <tr>
                                            <th class="w-[110px] min-w-[110px] py-1">เริ่ม<br><span class="sub-label">From</span></th>
                                            <th class="w-[110px] min-w-[110px] py-1">ถึง<br><span class="sub-label">To</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(exp, xIdx) in experience" :key="xIdx">
                                            <tr>
                                                <td class="p-1">
                                                    <input type="text" :name="'experience['+xIdx+'][company_name]'" x-model="exp.company_name" class="w-full doc-input">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'experience['+xIdx+'][start_date]'" x-model="exp.start_date" placeholder="วว/ดด/ปปปป" class="w-full doc-input text-[11px] text-center datepicker-th px-0.5">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'experience['+xIdx+'][end_date]'" x-model="exp.end_date" placeholder="วว/ดด/ปปปป" class="w-full doc-input text-[11px] text-center datepicker-th px-0.5">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'experience['+xIdx+'][position]'" x-model="exp.position" class="w-full doc-input">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'experience['+xIdx+'][job_detail]'" x-model="exp.job_detail" class="w-full doc-input">
                                                </td>
                                                <td class="p-1">
                                                    <input type="number" :name="'experience['+xIdx+'][salary]'" x-model="exp.salary" class="w-full doc-input text-right">
                                                </td>
                                                <td class="p-1">
                                                    <input type="text" :name="'experience['+xIdx+'][reason_for_leaving]'" x-model="exp.reason_for_leaving" class="w-full doc-input">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>

                                <!-- CV explanation & file upload section -->
                                <div class="pt-3 space-y-3">
                                    <p class="font-bold text-xs">
                                        อธิบายโดยสังเขป หรือ แนบเอกสาร Resume / Curriculum Vitae (CV) <span class="text-[#B21F24]">*</span><br>
                                        <span class="sub-label">Giving more detail of working experience or attach Resume or CV</span>
                                    </p>

                                    <!-- Resume / CV File Upload Box (Page 3) -->
                                    <div class="border-2 border-dashed border-red-300 dark:border-slate-600 rounded-xl p-3 sm:p-4 text-center bg-red-50/50 dark:bg-slate-800/40 relative hover:border-[#B21F24] transition-all cursor-pointer group shadow-sm">
                                        <input type="file" name="resume[]" accept=".pdf,.doc,.docx,image/*" multiple @change="handleFileChange($event, 'resume')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                        <div x-show="resumeFiles.length === 0" class="flex items-center justify-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-red-100 dark:bg-slate-700 flex items-center justify-center text-[#B21F24] group-hover:scale-110 transition-transform shrink-0">
                                                <svg class="w-5 h-5 text-[#B21F24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-xs font-bold text-slate-800 dark:text-white group-hover:text-[#B21F24] transition-colors">
                                                    คลิกเพื่อแนบเอกสาร Resume / Curriculum Vitae (CV) (เลือกได้หลายไฟล์) <span class="text-[#B21F24]">*</span>
                                                </p>
                                                <p class="text-[11px] text-slate-500">รองรับไฟล์ .pdf, .doc, .docx หรือไฟล์รูปภาพ</p>
                                            </div>
                                        </div>
                                        <div x-show="resumeFiles.length > 0" class="flex flex-col items-center justify-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                            <div class="flex items-center justify-center gap-2 mb-0.5">
                                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>แนบไฟล์ Resume / CV เรียบร้อยแล้ว (<span x-text="resumeFiles.length"></span> ไฟล์):</span>
                                            </div>
                                            <div class="flex flex-col gap-1 w-full max-w-md text-left px-2 max-h-40 overflow-y-auto">
                                                <template x-for="(f, idx) in resumeFiles" :key="idx">
                                                    <div class="flex items-center justify-between gap-2 text-[11px] text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700/80 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600 w-full shadow-2xs">
                                                        <div class="flex items-center gap-1.5 min-w-0 flex-grow">
                                                            <span class="text-[#B21F24] font-mono text-[10px] font-bold shrink-0" x-text="(idx + 1) + '.'"></span>
                                                            <span class="truncate font-medium" :title="typeof f === 'string' ? f : f.name" x-text="typeof f === 'string' ? f : f.name"></span>
                                                        </div>
                                                        <button type="button" @click.stop.prevent="removeFile('resume', idx)" class="z-20 relative text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 p-1 rounded-md transition-colors shrink-0" title="ลบไฟล์นี้">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-normal mt-0.5">(คลิกพื้นที่ว่างหากต้องการแนบเพิ่ม หรือเลือกไฟล์ใหม่)</span>
                                        </div>
                                    </div>

                                    <textarea name="experience_summary" rows="3" placeholder="พิมพ์รายละเอียดประสบการณ์ทำงานเพิ่มเติมตรงนี้..." class="w-full border border-gray-300 rounded p-2 text-xs focus:border-[#B21F24] focus:outline-none"></textarea>
                                </div>
                            </div>

                            <!-- Section: Language Ability -->
                            <div class="space-y-2 pt-2">
                                <h4 class="font-bold text-sm text-black">Language Ability (ภาษา)</h4>
                                <table class="w-full doc-table text-xs text-center">
                                    <thead>
                                        <tr>
                                            <th rowspan="2" class="w-1/4 py-1.5 text-left pl-3">ภาษา<br><span class="sub-label">Language</span></th>
                                            <th colspan="3" class="py-1">พูด (Speaking)</th>
                                            <th colspan="3" class="py-1">เขียน (Writing)</th>
                                            <th colspan="3" class="py-1">อ่าน (Reading)</th>
                                        </tr>
                                        <tr class="text-[10px]">
                                            <th class="w-10 py-1">ดี<br><span class="sub-label">Good</span></th>
                                            <th class="w-10 py-1">ปานกลาง<br><span class="sub-label">Fair</span></th>
                                            <th class="w-10 py-1">พอใช้<br><span class="sub-label">Poor</span></th>

                                            <th class="w-10 py-1">ดี<br><span class="sub-label">Good</span></th>
                                            <th class="w-10 py-1">ปานกลาง<br><span class="sub-label">Fair</span></th>
                                            <th class="w-10 py-1">พอใช้<br><span class="sub-label">Poor</span></th>

                                            <th class="w-10 py-1">ดี<br><span class="sub-label">Good</span></th>
                                            <th class="w-10 py-1">ปานกลาง<br><span class="sub-label">Fair</span></th>
                                            <th class="w-10 py-1">พอใช้<br><span class="sub-label">Poor</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(['thai' => 'ภาษาไทย (Thai)', 'english' => 'ภาษาอังกฤษ (English)', 'other1' => 'อื่นๆ...................', 'other2' => 'อื่นๆ...................'] as $lCode => $lText)
                                            @php
                                                $defaultLevel = ($lCode === 'thai') ? 'Good' : (($lCode === 'english') ? 'Fair' : '');
                                                $curSpk = old("language_skills.{$lCode}.speaking", $defaultLevel);
                                                $curWrt = old("language_skills.{$lCode}.writing", $defaultLevel);
                                                $curRdg = old("language_skills.{$lCode}.reading", $defaultLevel);
                                            @endphp
                                            <tr>
                                                <td class="p-1 text-left pl-2 font-medium">
                                                    @if(str_contains($lCode, 'other'))
                                                        <div class="flex items-center gap-1">
                                                            <span>อื่นๆ</span>
                                                            <input type="text" name="language_skills[{{ $lCode }}][name]" value="{{ old("language_skills.{$lCode}.name") }}" placeholder="ระบุภาษา" class="doc-input flex-grow text-xs">
                                                        </div>
                                                    @else
                                                        {{ $lText }}
                                                    @endif
                                                </td>
                                                <!-- Speaking -->
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][speaking]" value="Good" {{ $curSpk === 'Good' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black" onchange="autoSyncLanguage('{{ $lCode }}', this.value)"></td>
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][speaking]" value="Fair" {{ $curSpk === 'Fair' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black" onchange="autoSyncLanguage('{{ $lCode }}', this.value)"></td>
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][speaking]" value="Poor" {{ $curSpk === 'Poor' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black" onchange="autoSyncLanguage('{{ $lCode }}', this.value)"></td>
                                                <!-- Writing -->
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][writing]" value="Good" {{ $curWrt === 'Good' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black"></td>
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][writing]" value="Fair" {{ $curWrt === 'Fair' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black"></td>
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][writing]" value="Poor" {{ $curWrt === 'Poor' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black"></td>
                                                <!-- Reading -->
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][reading]" value="Good" {{ $curRdg === 'Good' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black"></td>
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][reading]" value="Fair" {{ $curRdg === 'Fair' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black"></td>
                                                <td class="p-1"><input type="radio" name="language_skills[{{ $lCode }}][reading]" value="Poor" {{ $curRdg === 'Poor' ? 'checked' : '' }} class="w-3.5 h-3.5 text-black"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-12 pt-4 border-t border-gray-300 flex justify-between items-center text-[10px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                    </div>


                    <!-- =========================================================================
                         DOCUMENT SHEET 4 / 5: COMPUTER & SPECIAL ABILITY & QUESTIONNAIRE
                    ========================================================================== -->
                    <div x-show="viewMode === 'paper' && step === 4" class="paper-sheet-container mb-6">
                    <div class="paper-sheet pt-10 pb-8 px-5 sm:pt-12 sm:pb-10 sm:px-8 md:px-12 lg:px-14 rounded-lg relative" :class="{ 'font-scale-md': fontScale === 'md', 'font-scale-lg': fontScale === 'lg' }">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <input type="text" class="doc-input w-36 text-center">
                            </div>
                            <div class="font-bold text-xs">4/5</div>
                        </div>

                        <div class="space-y-5 text-sm">
                            <!-- Computer Skills -->
                            <div>
                                <h4 class="font-bold text-base text-black">ความสามารถด้านคอมพิวเตอร์ (Computer skill)</h4>
                                <div class="flex items-end gap-2 mt-1">
                                    <span class="shrink-0 font-medium">Program</span>
                                    <input type="text" name="computer_skills" class="doc-input flex-grow font-semibold">
                                </div>
                            </div>

                            <!-- Special Ability Box (exact bordered box layout from PDF) -->
                            <div class="pt-2">
                                <h4 class="font-bold text-base text-black mb-1.5">Special Ability (ความสามารถพิเศษ)</h4>
                                <div class="border border-black p-2.5 space-y-1 text-xs sm:text-sm">
                                    <!-- Row 1: Car Driving -->
                                    <div class="grid grid-cols-1 sm:grid-cols-[330px_1fr] items-center gap-2 py-0.5">
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold w-32">ขับรถยนต์ :</span>
                                            <label class="inline-flex items-center gap-1 cursor-pointer"><input type="radio" name="special_abilities[car_driving]" value="ได้" class="w-3.5 h-3.5 text-black"><span>ได้ <span class="sub-label">Yes</span></span></label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer"><input type="radio" name="special_abilities[car_driving]" value="ไม่ได้" class="w-3.5 h-3.5 text-black"><span>ไม่ได้ <span class="sub-label">No</span></span></label>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold">ใบขับขี่ :</span>
                                            <label class="inline-flex items-center gap-1 cursor-pointer"><input type="radio" name="special_abilities[car_license]" value="มี" class="w-3.5 h-3.5 text-black"><span>มี <span class="sub-label">Yes</span></span></label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer"><input type="radio" name="special_abilities[car_license]" value="ไม่มี" class="w-3.5 h-3.5 text-black"><span>ไม่มี <span class="sub-label">No</span></span></label>
                                        </div>
                                    </div>

                                    <!-- Row 2: Motorcycle Driving -->
                                    <div class="grid grid-cols-1 sm:grid-cols-[330px_1fr] items-center gap-2 py-0.5">
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold w-32">ขับรถจักรยานยนต์ :</span>
                                            <label class="inline-flex items-center gap-1 cursor-pointer"><input type="radio" name="special_abilities[motor_driving]" value="ได้" class="w-3.5 h-3.5 text-black"><span>ได้ <span class="sub-label">Yes</span></span></label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer"><input type="radio" name="special_abilities[motor_driving]" value="ไม่ได้" class="w-3.5 h-3.5 text-black"><span>ไม่ได้ <span class="sub-label">No</span></span></label>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="font-bold">ใบขับขี่ :</span>
                                            <label class="inline-flex items-center gap-1 cursor-pointer"><input type="radio" name="special_abilities[motor_license]" value="มี" class="w-3.5 h-3.5 text-black"><span>มี <span class="sub-label">Yes</span></span></label>
                                            <label class="inline-flex items-center gap-1 cursor-pointer"><input type="radio" name="special_abilities[motor_license]" value="ไม่มี" class="w-3.5 h-3.5 text-black"><span>ไม่มี <span class="sub-label">No</span></span></label>
                                        </div>
                                    </div>

                                    <!-- Row 3: Office Machine -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">ความสามารถในการใช้เครื่องใช้สำนักงาน</span>
                                            <span class="sub-label">Office Machine</span>
                                        </div>
                                        <input type="text" name="special_abilities[office_machine]" class="doc-input flex-grow font-semibold">
                                    </div>

                                    <!-- Row 4: Hobbies -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">งานอดิเรก : ระบุ</span>
                                            <span class="sub-label">Hobbies Please Mention</span>
                                        </div>
                                        <input type="text" name="special_abilities[hobbies]" class="doc-input flex-grow font-semibold">
                                    </div>

                                    <!-- Row 5: Favourite Sport -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">กีฬาทีชอบ : ระบุ</span>
                                            <span class="sub-label">Favourite Sport Please Mention</span>
                                        </div>
                                        <input type="text" name="special_abilities[sports]" class="doc-input flex-grow font-semibold">
                                    </div>

                                    <!-- Row 6: Special Knowledge -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">ความรู้พิเศษ : ระบุ</span>
                                            <span class="sub-label">Special knowledge Please Mention</span>
                                        </div>
                                        <input type="text" name="special_abilities[special_knowledge]" class="doc-input flex-grow font-semibold">
                                    </div>

                                    <!-- Row 7: Others -->
                                    <div class="flex items-end gap-2 w-full py-0.5">
                                        <div class="shrink-0 flex flex-col">
                                            <span class="font-bold leading-tight">อื่นๆ : ระบุ</span>
                                            <span class="sub-label">Others Please Mention</span>
                                        </div>
                                        <input type="text" name="special_abilities[others]" class="doc-input flex-grow font-semibold">
                                    </div>
                                </div>
                            </div>

                            <!-- Work Up Country -->
                            <div class="flex flex-wrap items-center gap-6 pt-3">
                                <span class="font-bold shrink-0">สามารถไปปฏิบัติงานต่างจังหวัด<br><span class="sub-label">Do you can work up Country?</span></span>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="application_questions[work_up_country]" value="ได้" class="w-3.5 h-3.5 text-black">
                                    <span>ได้ <span class="sub-label">Yes</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="application_questions[work_up_country]" value="ไม่ได้" class="w-3.5 h-3.5 text-black">
                                    <span>ไม่ได้ <span class="sub-label">No</span></span>
                                </label>
                                <div class="flex items-end gap-2 flex-grow min-w-[200px]">
                                    <span class="shrink-0 font-medium">อื่นๆ ระบุ</span>
                                    <input type="text" name="application_questions[work_up_country_other]" class="doc-input flex-grow">
                                </div>
                            </div>

                            <!-- Source of Job Info -->
                            <div class="flex items-end gap-2 pt-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold leading-tight">ทราบข่าวการรับสมัครจาก</span>
                                    <span class="sub-label">Sources of job information</span>
                                </div>
                                <input type="text" name="application_questions[source_info]" class="doc-input flex-grow">
                            </div>

                            <!-- Ever Applied Before -->
                            <div class="flex flex-wrap items-center gap-6 pt-2">
                                <span class="font-bold shrink-0">ท่านเคยสมัครงานกับบริษัทฯ นี้มาก่อนหรือไม่<br><span class="sub-label">Have you ever applied for employment with us before?</span></span>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="application_questions[applied_before]" value="เคย" class="w-3.5 h-3.5 text-black">
                                    <span>เคย <span class="sub-label">Yes</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="application_questions[applied_before]" value="ไม่เคย" checked class="w-3.5 h-3.5 text-black">
                                    <span>ไม่เคย <span class="sub-label">No</span></span>
                                </label>
                            </div>
                            <div class="flex items-end gap-2 pt-1">
                                <div class="shrink-0 flex flex-col">
                                    <span class="shrink-0 font-medium leading-tight">ถ้าเคย เมื่อไร ?</span>
                                    <span class="sub-label">If yes, When?</span>
                                </div>
                                <input type="text" name="application_questions[applied_before_when]" class="doc-input flex-grow">
                            </div>

                            <!-- Relatives / Friends Working Here -->
                            <div class="flex items-end gap-2 pt-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold leading-tight">ระบุชื่อญาติ / เพื่อน ที่ทำงานอยู่ในบริษัทฯ ซึ่งท่านรู้จักดี</span>
                                    <span class="sub-label">Give the name of relatives / friends , working with us known to you</span>
                                </div>
                                <input type="text" name="application_questions[relative_known]" class="doc-input flex-grow">
                            </div>

                            <!-- Why Apply -->
                            <div class="flex items-end gap-2 pt-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold leading-tight">เพราะเหตุใดท่านจึงมาสมัครงานกับบริษัท</span>
                                    <span class="sub-label">Why did you come to apply for a job at the company?</span>
                                </div>
                                <input type="text" name="application_questions[reason_to_apply]" class="doc-input flex-grow">
                            </div>

                            <!-- Expected Salary -->
                            <div class="flex items-end gap-2 pt-2">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold text-sm leading-tight">อัตราเงินเดือนที่คาดหวัง</span>
                                    <span class="sub-label">Expected Salary</span>
                                </div>
                                <input type="number" name="expected_salary" value="{{ old('expected_salary') }}" class="doc-input flex-grow text-center font-bold text-base text-[#B21F24]">
                                <div class="shrink-0 flex flex-col">
                                    <span class="font-bold text-sm leading-tight">บาท / เดือน</span>
                                    <span class="sub-label">baht / month</span>
                                </div>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-12 pt-4 border-t border-gray-300 flex justify-between items-center text-[10px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                    </div>


                    <!-- =========================================================================
                         DOCUMENT SHEET 5 / 5: REFERENCES & DECLARATION (PAPER MODE)
                    ========================================================================== -->
                    <div x-show="viewMode === 'paper' && step === 5" class="paper-sheet-container mb-6">
                    <div class="paper-sheet pt-10 pb-8 px-5 sm:pt-12 sm:pb-10 sm:px-8 md:px-12 lg:px-14 rounded-lg relative" :class="{ 'font-scale-md': fontScale === 'md', 'font-scale-lg': fontScale === 'lg' }">
                        <!-- Top Header Row -->
                        <div class="flex justify-between items-start text-xs mb-6">
                            <div class="flex items-center gap-1.5">
                                <span class="font-bold text-sm">ใบสมัครเลขที่</span>
                                <input type="text" class="doc-input w-36 text-center">
                            </div>
                            <div class="font-bold text-xs">5/5</div>
                        </div>

                        <div class="space-y-5 text-sm">
                            <!-- References Section -->
                            <div class="space-y-2">
                                <p class="font-bold text-black text-sm leading-relaxed">
                                    เขียนชื่อ ที่อยู่ โทรศัพท์ และอาชีพของผู้ที่อ้างถึง 2 คน (ซึ่งไม่ใช่ญาติ หรือนายจ้างเดิม) ที่รู้จักคุ้นเคยตัวท่านดี<br>
                                    <span class="sub-label font-normal">List name, address, telephone and occupation of 2 references (Other than relatives or former employers) who know you</span>
                                </p>
                                <div class="flex items-end gap-2">
                                    <span class="font-bold shrink-0">1.</span>
                                    <input type="text" name="references_info[ref1][text]" class="doc-input flex-grow">
                                </div>
                                <div class="flex items-end gap-2">
                                    <span class="font-bold shrink-0">2.</span>
                                    <input type="text" name="references_info[ref2][text]" class="doc-input flex-grow">
                                </div>
                            </div>

                            <!-- Permission to check history -->
                            <div class="flex flex-wrap items-center gap-6 pt-3 border-t border-gray-200">
                                <span class="font-bold shrink-0">ท่านอนุญาตหรือไม่หากบริษัท ฯ จะสอบประวัติการทำงานของท่าน<br><span class="sub-label">Do you have problem if company check your work history</span></span>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="references_info[allow_check_history]" value="อนุญาต" checked class="w-3.5 h-3.5 text-black">
                                    <span>อนุญาต <span class="sub-label">Yes</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="references_info[allow_check_history]" value="ไม่อนุญาต" class="w-3.5 h-3.5 text-black">
                                    <span>ไม่อนุญาต <span class="sub-label">No</span></span>
                                </label>
                            </div>

                            <!-- Truth Questionnaire Table (exact border box from PDF) -->
                            <div class="pt-2">
                                <table class="w-full doc-table text-xs">
                                    <thead>
                                        <tr>
                                            <th class="text-left pl-3 py-2 w-4/5 font-bold text-gray-800">
                                                กรุณาตอบคำถามด้านล่างนี้ตามความเป็นจริง (บริษัทจะเก็บรักษาข้อมูลไว้เป็นความลับ)<br>
                                                <span class="sub-label">Please reply the truth for these questions (Company will keep for the secret)</span>
                                            </th>
                                            <th class="w-16 text-center py-2">เคย<br><span class="sub-label">Yes</span></th>
                                            <th class="w-16 text-center py-2">ไม่เคย<br><span class="sub-label">No</span></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="p-2.5 leading-relaxed">
                                                คุณเคยเป็นโรคที่สังคมไม่ยอมรับ เช่น โรคติดต่อร้ายแรง อาการป่วยทางจิต หรือโรคพิษสุราเรื้อรัง<br>
                                                <span class="sub-label">Have you ever been seriously or contracted with contagious disease, mental illness or alcoholism?</span>
                                            </td>
                                            <td class="p-2 text-center"><input type="radio" name="health_criminal_questionnaire[contagious_disease]" value="เคย" class="w-3.5 h-3.5 text-black"></td>
                                            <td class="p-2 text-center"><input type="radio" name="health_criminal_questionnaire[contagious_disease]" value="ไม่เคย" checked class="w-3.5 h-3.5 text-black"></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2.5 leading-relaxed">
                                                คุณเคยถูกจับกุมดำเนินคดี หรือถูกคุมขังเป็นนักโทษ เนื่องจากกระทำผิดกฎหมายบ้านเมือง<br>
                                                <span class="sub-label">Have you ever been arrested or being the prisoner because of violation the law?</span>
                                            </td>
                                            <td class="p-2 text-center"><input type="radio" name="health_criminal_questionnaire[arrested_or_prisoner]" value="เคย" class="w-3.5 h-3.5 text-black"></td>
                                            <td class="p-2 text-center"><input type="radio" name="health_criminal_questionnaire[arrested_or_prisoner]" value="ไม่เคย" checked class="w-3.5 h-3.5 text-black"></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2.5 leading-relaxed">
                                                คุณเคยถูกไล่ออกจากงานเนื่องจากกระทำผิดกฎ หรือฝ่าฝืนระเบียบข้อบังคับของบริษัทฯ<br>
                                                <span class="sub-label">Have you ever been fired from the company because of violation the company’s regulations?</span>
                                            </td>
                                            <td class="p-2 text-center"><input type="radio" name="health_criminal_questionnaire[fired_from_company]" value="เคย" class="w-3.5 h-3.5 text-black"></td>
                                            <td class="p-2 text-center"><input type="radio" name="health_criminal_questionnaire[fired_from_company]" value="ไม่เคย" checked class="w-3.5 h-3.5 text-black"></td>
                                        </tr>
                                        <tr>
                                            <td class="p-2.5 leading-relaxed">
                                                คุณเป็นผู้ติดหรือเคยติดยาเสพติดหรือไม่<br>
                                                <span class="sub-label">Have you ever been an addict or addicted to drugs?</span>
                                            </td>
                                            <td class="p-2 text-center"><input type="radio" name="health_criminal_questionnaire[drug_addict]" value="เคย" class="w-3.5 h-3.5 text-black"></td>
                                            <td class="p-2 text-center"><input type="radio" name="health_criminal_questionnaire[drug_addict]" value="ไม่เคย" checked class="w-3.5 h-3.5 text-black"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Drug test consent -->
                            <div class="flex flex-wrap items-center gap-6 pt-2">
                                <span class="font-bold shrink-0">คุณยินยอมให้บริษัทฯตรวจสอบสารเสพติดในร่างกายหรือไม่<br><span class="sub-label">Do you permit company to check the narcotics in your body?</span></span>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="health_criminal_questionnaire[permit_drug_check]" value="ยินยอม" checked class="w-3.5 h-3.5 text-black">
                                    <span>ยินยอม <span class="sub-label">Agree</span></span>
                                </label>
                                <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                    <input type="radio" name="health_criminal_questionnaire[permit_drug_check]" value="ไม่ยินยอม" class="w-3.5 h-3.5 text-black">
                                    <span>ไม่ยินยอม <span class="sub-label">Not agree</span></span>
                                </label>
                            </div>

                            <!-- Certification Statement (Exact text from PDF) -->
                            <div class="pt-4 border-t border-gray-300 text-center space-y-2 px-2">
                                <p class="text-[11px] leading-relaxed text-gray-800 font-semibold">
                                    ข้าพเจ้าขอรับรองว่า ข้อความดังกล่าวทั้งหมดในใบสมัครนี้เป็นความจริงทุกประการ และยินยอมให้เก็บ ใช้ เปิดเผย ตรวจสอบ ข้อมูลดังกล่าวได้ตลอดเวลาตามที่จำเป็น<br>
                                    หลังจากบริษัทจ้างเข้ามาทำงานแล้วปรากฏว่า ข้อความในใบสมัครงานเอกสารที่นำมาแสดง หรือรายละเอียดที่ให้ไว้ไม่เป็นความจริง บริษัทฯ มีสิทธิ์ที่จะเลิกจ้างข้าพเจ้าได้โดยไม่ต้องจ่ายเงินชดเชยหรือค่าเสียหายใดๆ ทั้งสิ้น
                                </p>
                                <p class="text-[9.5px] italic text-gray-500 leading-normal max-w-2xl mx-auto">
                                    I certify all statement given in this application form is true and agree company to keep, checking, sharing my detail all necessary time. If detail is found to be untrue after engagement. The Company has right to terminate my employment without any compensation or severance pay whatsoever.
                                </p>

                                <div class="pt-2">
                                    <label class="inline-flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="pdpa_consent" value="1" class="w-4 h-4 text-[#B21F24] focus:ring-[#B21F24]">
                                        <span class="text-xs font-bold text-black">ข้าพเจ้าขอรับรองว่าข้อความทั้งหมดเป็นความจริง และยินยอมตามเงื่อนไขทุกประการ *</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Bottom Row: Attached Documents Checklist & Signature -->
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 pt-4 border-t border-gray-300 items-end">
                                <!-- Attached Checklist (Left) -->
                                <div class="md:col-span-7 space-y-1.5 text-[11px]">
                                    <p class="font-bold text-black">เอกสารแนบใบสมัครงาน</p>
                                    <div class="grid grid-cols-2 gap-y-1.5 gap-x-2">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="attached_documents_check[]" value="สำเนาวุฒิการศึกษา" class="w-3.5 h-3.5 text-black"><span>( ) สำเนาวุฒิการศึกษา</span></label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="attached_documents_check[]" value="สำเนาบัตรประชาชน" class="w-3.5 h-3.5 text-black"><span>( ) สำเนาบัตรประชาชน</span></label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="attached_documents_check[]" value="สำเนาทะเบียนบ้าน" class="w-3.5 h-3.5 text-black"><span>( ) สำเนาทะเบียนบ้าน</span></label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="attached_documents_check[]" value="ใบรับรองการทำงาน" class="w-3.5 h-3.5 text-black"><span>( ) ใบรับรองการทำงาน</span></label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="attached_documents_check[]" value="สำเนาใบเปลี่ยนชื่อ-สกุล" class="w-3.5 h-3.5 text-black"><span>( ) สำเนาใบเปลี่ยนชื่อ – สกุล</span></label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer"><input type="checkbox" name="attached_documents_check[]" value="สำเนาเอกสารทางทหาร" class="w-3.5 h-3.5 text-black"><span>( ) สำเนาเอกสารทางทหาร</span></label>
                                    </div>
                                    <div class="flex items-end gap-1 pt-1">
                                        <span>( ) อื่นๆ</span>
                                        <input type="text" name="attached_documents_other" class="doc-input flex-grow text-[11px]">
                                    </div>
                                </div>

                                <!-- Signature Placeholder (Right) -->
                                <div class="md:col-span-5 text-center space-y-2">
                                    <input type="text" 
                                           name="applicant_signature" 
                                           x-model="applicantSignature" 
                                           @input="signatureManuallyEdited = true" 
                                           class="doc-input w-full text-center font-bold text-gray-900 tracking-wide text-sm" 
                                           placeholder="พิมพ์ชื่อ-นามสกุล เพื่อลงลายมือชื่อ *">
                                    <p class="font-bold text-xs text-black">ลายมือชื่อผู้สมัคร<br><span class="sub-label">Applicants signature</span></p>
                                </div>
                            </div>

                            <!-- Electronic File Upload Boxes for Modern convenience -->
                            <div class="mt-4 pt-4 border-t border-dashed border-gray-300">
                                <p class="font-bold text-xs text-slate-700 mb-2">อัปโหลดไฟล์เอกสาร (PDF):</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <!-- Resume -->
                                    <div class="border-2 border-dashed border-gray-300 rounded p-3 text-center bg-gray-50 relative hover:border-[#B21F24] cursor-pointer">
                                        <input type="file" name="resume[]" accept=".pdf,.doc,.docx,image/*" multiple @change="handleFileChange($event, 'resume')" class="absolute inset-0 opacity-0 cursor-pointer">
                                        <div x-show="resumeFiles.length === 0">
                                            <svg class="w-7 h-7 text-[#B21F24] mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-xs font-bold text-gray-700">แบบ Resume / CV (เลือกได้หลายไฟล์) *</p>
                                        </div>
                                        <div x-show="resumeFiles.length > 0" class="text-xs font-bold text-green-600 flex flex-col items-center justify-center gap-1">
                                            <div class="flex items-center justify-center gap-1 mb-1">
                                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>แนบสำเร็จ <span x-text="resumeFiles.length"></span> ไฟล์</span>
                                            </div>
                                            <div class="flex flex-col gap-1 w-full text-left px-1 max-h-40 overflow-y-auto">
                                                <template x-for="(f, idx) in resumeFiles" :key="idx">
                                                    <div class="flex items-center justify-between gap-2 text-[11px] text-slate-700 bg-white px-2 py-1 rounded border border-gray-200 w-full shadow-2xs">
                                                        <div class="flex items-center gap-1.5 min-w-0 flex-grow">
                                                            <span class="text-[#B21F24] font-mono text-[10px] font-bold shrink-0" x-text="(idx + 1) + '.'"></span>
                                                            <span class="truncate font-medium text-slate-800" :title="typeof f === 'string' ? f : f.name" x-text="typeof f === 'string' ? f : f.name"></span>
                                                        </div>
                                                        <button type="button" @click.stop.prevent="removeFile('resume', idx)" class="z-20 relative text-slate-400 hover:text-red-600 hover:bg-red-50 p-1 rounded transition-colors shrink-0" title="ลบไฟล์นี้">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-normal underline mt-1">(คลิกพื้นที่ว่างหากต้องการแนบเพิ่ม)</span>
                                        </div>
                                    </div>
                                    <!-- Portfolio / Other Docs (Multiple PDFs) -->
                                    <div class="border-2 border-dashed border-gray-300 rounded p-3 text-center bg-gray-50 relative hover:border-[#B21F24] cursor-pointer">
                                        <input type="file" name="portfolio[]" accept=".pdf" multiple @change="handleFileChange($event, 'portfolio')" class="absolute inset-0 opacity-0 cursor-pointer">
                                        <div x-show="portfolioFiles.length === 0">
                                            <svg class="w-7 h-7 text-gray-500 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-xs font-bold text-gray-700">แนบ Portfolio / เอกสารอื่นๆ (PDF เลือกได้หลายไฟล์)</p>
                                        </div>
                                        <div x-show="portfolioFiles.length > 0" class="text-xs font-bold text-green-600 flex flex-col items-center justify-center gap-1">
                                            <div class="flex items-center justify-center gap-1 mb-1">
                                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span>แนบสำเร็จ <span x-text="portfolioFiles.length"></span> ไฟล์</span>
                                            </div>
                                            <div class="flex flex-col gap-1 w-full text-left px-1 max-h-40 overflow-y-auto">
                                                <template x-for="(f, idx) in portfolioFiles" :key="idx">
                                                    <div class="flex items-center justify-between gap-2 text-[11px] text-slate-700 bg-white px-2 py-1 rounded border border-gray-200 w-full shadow-2xs">
                                                        <div class="flex items-center gap-1.5 min-w-0 flex-grow">
                                                            <span class="text-blue-600 font-mono text-[10px] font-bold shrink-0" x-text="(idx + 1) + '.'"></span>
                                                            <span class="truncate font-medium text-slate-800" :title="typeof f === 'string' ? f : f.name" x-text="typeof f === 'string' ? f : f.name"></span>
                                                        </div>
                                                        <button type="button" @click.stop.prevent="removeFile('portfolio', idx)" class="z-20 relative text-slate-400 hover:text-red-600 hover:bg-red-50 p-1 rounded transition-colors shrink-0" title="ลบไฟล์นี้">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-normal underline mt-1">(คลิกพื้นที่ว่างหากต้องการแนบเพิ่ม)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Document Footer Info -->
                        <div class="mt-12 pt-4 border-t border-gray-300 flex justify-between items-center text-[10px] text-gray-500 font-mono">
                            <span>Kumwell Corporation Public Company Limited</span>
                            <span>QF-HR-14 : REV.07 : 15-06-22</span>
                        </div>
                    </div>
                    </div>

                    <!-- =========================================================================
                         MOBILE-FRIENDLY MODE (FOR SMARTPHONES & EASY DATA ENTRY)
                    ========================================================================== -->

                    <!-- MOBILE STEP 1: PERSONAL INFORMATION & ADDRESS -->
                    <div x-show="viewMode === 'mobile' && step === 1" class="space-y-4 mb-6">
                        <!-- Header banner -->
                        <div class="mobile-form-card !bg-gradient-to-r !from-[#B21F24] !to-red-700 text-white !p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded">ขั้นตอนที่ 1/5</span>
                                    <h3 class="text-base font-black mt-1">ประวัติส่วนตัวและที่อยู่</h3>
                                </div>
                                <span class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Card 1: Photo & Basic Name -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-id-badge text-[#B21F24]"></i>
                                <span>ข้อมูลเบื้องต้นและรูปถ่าย</span>
                            </div>

                            <!-- Photo Upload -->
                            <div class="mobile-field-group text-center">
                                <label class="mobile-label justify-center">รูปถ่ายหน้าตรง (1.5 - 2 นิ้ว)</label>
                                <div class="w-32 h-40 mx-auto border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl flex flex-col items-center justify-center relative bg-slate-50 dark:bg-slate-800/50 hover:border-[#B21F24] cursor-pointer overflow-hidden transition-all shadow-inner">
                                    <input type="file" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" @change="handleFileChange($event, 'photo')" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                    <template x-if="!photoPreview">
                                        <div class="p-3 text-center">
                                            <svg class="w-8 h-8 text-slate-400 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <p class="text-xs font-bold text-slate-600 dark:text-slate-300">แตะเพื่อถ่ายรูป<br>หรือเลือกรูป</p>
                                            <p class="text-[10px] text-slate-400 mt-0.5">JPG, PNG (ห้าม GIF)</p>
                                        </div>
                                    </template>
                                    <template x-if="photoPreview">
                                        <div class="w-full h-full relative group">
                                            <img :src="photoPreview" class="w-full h-full object-cover">
                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all text-white text-xs font-bold">แตะเพื่อเปลี่ยนรูป</div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">ชื่อ <span class="text-[#B21F24]">*</span></label>
                                    <input type="text" name="first_name" x-model="firstName" @input="syncSignature()" required value="{{ old('first_name') }}" placeholder="กรอกชื่อจริง" class="mobile-input font-medium">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">นามสกุล <span class="text-[#B21F24]">*</span></label>
                                    <input type="text" name="last_name" x-model="lastName" @input="syncSignature()" required value="{{ old('last_name') }}" placeholder="กรอกนามสกุล" class="mobile-input font-medium">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">ชื่อเล่น</label>
                                    <input type="text" name="nickname" value="{{ old('nickname') }}" placeholder="ชื่อเล่น" class="mobile-input">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">เพศ</label>
                                    <div class="flex items-center gap-4 pt-2">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-sm">
                                            <input type="radio" name="gender" value="ชาย" checked class="w-4 h-4 text-[#B21F24] focus:ring-[#B21F24]">
                                            <span>ชาย</span>
                                        </label>
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer text-sm">
                                            <input type="radio" name="gender" value="หญิง" class="w-4 h-4 text-[#B21F24] focus:ring-[#B21F24]">
                                            <span>หญิง</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mobile-field-group">
                                <label class="mobile-label">ตำแหน่งที่สมัคร</label>
                                <input type="text" readonly value="{{ $post->position_name }}" class="mobile-input bg-slate-100 dark:bg-slate-700/60 font-bold text-[#B21F24]">
                            </div>
                        </div>

                        <!-- Card 2: Contact Information -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-phone text-[#B21F24]"></i>
                                <span>ข้อมูลการติดต่อ</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">เบอร์โทรศัพท์มือถือ <span class="text-[#B21F24]">*</span></label>
                                    <input type="tel" name="phone" required value="{{ old('phone') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="08x-xxx-xxxx" class="mobile-input font-bold text-[#B21F24]">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">อีเมล <span class="text-[#B21F24]">*</span></label>
                                    <input type="email" name="email" required value="{{ old('email') }}" placeholder="example@email.com" class="mobile-input font-bold text-[#B21F24]">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">เบอร์โทรศัพท์บ้าน</label>
                                    <input type="text" name="tel" value="{{ old('tel') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="02-xxx-xxxx" class="mobile-input">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">Line ID</label>
                                    <input type="text" name="line_id" value="{{ old('line_id') }}" placeholder="ไอดีไลน์" class="mobile-input">
                                </div>
                                <div class="mobile-field-group col-span-2 sm:col-span-1">
                                    <label class="mobile-label">Facebook</label>
                                    <input type="text" name="facebook" value="{{ old('facebook') }}" placeholder="ชื่อเฟซบุ๊ก" class="mobile-input">
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Present Address -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-location-dot text-[#B21F24]"></i>
                                <span>ที่อยู่ปัจจุบัน</span>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">บ้านเลขที่</label>
                                    <input type="text" name="house_no" value="{{ old('house_no') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="เลขที่" class="mobile-input">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">หมู่ที่</label>
                                    <input type="text" name="moo" value="{{ old('moo') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="หมู่" class="mobile-input">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">ถนน</label>
                                    <input type="text" name="road" value="{{ old('road') }}" placeholder="ถนน" class="mobile-input">
                                </div>
                            </div>

                            <div class="p-3 bg-red-50/50 dark:bg-slate-800/40 border border-red-100 dark:border-slate-700 rounded-xl mb-3">
                                <p class="text-[11px] text-slate-500 mb-2 flex items-center gap-1.5">
                                    <i class="fa-solid fa-magnifying-glass text-[#B21F24]"></i>
                                    <span>ระบบค้นหาที่อยู่อัตโนมัติ (พิมพ์ชื่อ ตำบล, อำเภอ, จังหวัด หรือรหัสไปรษณีย์)</span>
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="mobile-field-group !mb-0">
                                        <label class="mobile-label">จังหวัด</label>
                                        <input type="text" id="m_addr_province" name="province" value="{{ old('province') }}" autocomplete="off" placeholder="เลือกหรือพิมพ์จังหวัด" class="mobile-input thai-addr-input font-medium">
                                    </div>
                                    <div class="mobile-field-group !mb-0">
                                        <label class="mobile-label">อำเภอ / เขต</label>
                                        <input type="text" id="m_addr_district" name="district" value="{{ old('district') }}" autocomplete="off" placeholder="เลือกหรือพิมพ์อำเภอ" class="mobile-input thai-addr-input font-medium">
                                    </div>
                                    <div class="mobile-field-group !mb-0">
                                        <label class="mobile-label">ตำบล / แขวง</label>
                                        <input type="text" id="m_addr_subdistrict" name="subdistrict" value="{{ old('subdistrict') }}" autocomplete="off" placeholder="เลือกหรือพิมพ์ตำบล" class="mobile-input thai-addr-input font-medium">
                                    </div>
                                    <div class="mobile-field-group !mb-0">
                                        <label class="mobile-label">รหัสไปรษณีย์</label>
                                        <input type="text" id="m_addr_postcode" name="postcode" value="{{ old('postcode') }}" autocomplete="off" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="รหัสไปรษณีย์ 5 หลัก" class="mobile-input thai-addr-input font-bold text-[#B21F24]">
                                    </div>
                                </div>
                            </div>

                            <div class="mobile-field-group">
                                <label class="mobile-label">ลักษณะที่อยู่อาศัย</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 pt-1 text-xs">
                                    @foreach(['อาศัยกับครอบครัว', 'บ้านตัวเอง', 'บ้านเช่า', 'หอพัก', 'คอนโดมิเนียม'] as $htype)
                                        <label class="inline-flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 cursor-pointer hover:border-[#B21F24]">
                                            <input type="radio" name="housing_type" value="{{ $htype }}" class="w-3.5 h-3.5 text-[#B21F24] focus:ring-[#B21F24]">
                                            <span>{{ $htype }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: Birth Details & ID Card -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-address-card text-[#B21F24]"></i>
                                <span>ข้อมูลเกิดและบัตรประชาชน</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">วัน เดือน ปีเกิด (พ.ศ.)</label>
                                    <input type="text" name="date_of_birth" x-model="dob" @change="calcAge()" @input="calcAge()" value="{{ old('date_of_birth') }}" placeholder="วว/ดด/ปปปป" class="mobile-input datepicker-th text-center font-bold">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">อายุ (ปี)</label>
                                    <input type="number" name="age" x-model="age" placeholder="อายุ" class="mobile-input text-center font-bold">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">สถานที่เกิด</label>
                                    <input type="text" name="place_of_birth" value="{{ old('place_of_birth') }}" placeholder="จังหวัดที่เกิด" class="mobile-input">
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">เชื้อชาติ</label>
                                    <input type="text" name="race" value="{{ old('race', 'ไทย') }}" class="mobile-input text-center">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">สัญชาติ</label>
                                    <input type="text" name="nationality" value="{{ old('nationality', 'ไทย') }}" class="mobile-input text-center">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">ศาสนา</label>
                                    <input type="text" name="religion" value="{{ old('religion', 'พุทธ') }}" class="mobile-input text-center">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">เลขประจำตัวประชาชน (13 หลัก)</label>
                                    <input type="text" name="national_id" value="{{ old('national_id') }}" maxlength="17" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="x-xxxx-xxxxx-xx-x" class="mobile-input font-mono font-medium">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">ออกโดย / จังหวัด</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="text" name="id_card_issued_by" value="{{ old('id_card_issued_by') }}" placeholder="ออกโดย" class="mobile-input text-xs">
                                        <input type="text" id="m_id_card_issued_province" name="id_card_issued_province" value="{{ old('id_card_issued_province') }}" placeholder="จังหวัด" autocomplete="off" class="mobile-input text-xs thai-addr-input">
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">วันออกบัตร</label>
                                    <input type="text" name="id_card_issued_date" value="{{ old('id_card_issued_date') }}" placeholder="วว/ดด/ปปปป" class="mobile-input datepicker-th text-xs text-center">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">วันหมดอายุ</label>
                                    <input type="text" name="id_card_expiry_date" value="{{ old('id_card_expiry_date') }}" placeholder="วว/ดด/ปปปป" class="mobile-input datepicker-th text-xs text-center">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">ส่วนสูง (ซม.)</label>
                                    <input type="number" step="0.5" name="height_cm" value="{{ old('height_cm') }}" placeholder="ซม." class="mobile-input text-center">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">น้ำหนัก (กก.)</label>
                                    <input type="number" step="0.5" name="weight_kg" value="{{ old('weight_kg') }}" placeholder="กก." class="mobile-input text-center">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-100 dark:border-slate-700">
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label">สถานะทางทหาร</label>
                                    <div class="space-y-1.5 text-xs">
                                        @foreach(['ได้รับการยกเว้น', 'ปลดเป็นทหารกองหนุน', 'ยังไม่ได้รับการเกณฑ์'] as $mstatus)
                                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-[#B21F24]">
                                                <input type="radio" name="military_status" value="{{ $mstatus }}" class="w-3.5 h-3.5 text-[#B21F24]">
                                                <span>{{ $mstatus }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label">สถานภาพการสมรส</label>
                                    <div class="grid grid-cols-2 gap-1.5 text-xs">
                                        @foreach(['โสด', 'แต่งงาน', 'หม้าย', 'แยกกัน'] as $mstat)
                                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-700 cursor-pointer hover:border-[#B21F24]">
                                                <input type="radio" name="marital_status" value="{{ $mstat }}" x-model="maritalStatus" :checked="maritalStatus === '{{ $mstat }}'" class="w-3.5 h-3.5 text-[#B21F24]">
                                                <span>{{ $mstat }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- MOBILE STEP 2: FAMILY & EMERGENCY CONTACT -->
                    <div x-show="viewMode === 'mobile' && step === 2" class="space-y-4 mb-6">
                        <!-- Header banner -->
                        <div class="mobile-form-card !bg-gradient-to-r !from-[#B21F24] !to-red-700 text-white !p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded">ขั้นตอนที่ 2/5</span>
                                    <h3 class="text-base font-black mt-1">ข้อมูลครอบครัวและผู้ติดต่อฉุกเฉิน</h3>
                                </div>
                                <span class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-people-roof"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Card 1: Parents & Spouse -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-users text-[#B21F24]"></i>
                                <span>ประวัติบิดา - มารดา - คู่สมรส</span>
                            </div>

                            <!-- Father -->
                            <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 space-y-2 mb-3">
                                <p class="text-xs font-bold text-slate-800 dark:text-white flex items-center justify-between">
                                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-mars text-blue-500"></i> ข้อมูลบิดา</span>
                                    <span class="text-[10px] font-normal text-slate-400">ไม่บังคับกรอก (Optional)</span>
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    <input type="text" name="family_info[father][name]" placeholder="ชื่อ-นามสกุล บิดา (ไม่บังคับ)" class="mobile-input col-span-2 text-xs">
                                    <input type="number" name="family_info[father][age]" placeholder="อายุ (ปี)" class="mobile-input text-xs text-center">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <input type="text" name="family_info[father][occupation]" placeholder="อาชีพ" class="mobile-input text-xs">
                                    <input type="text" name="family_info[father][workplace]" placeholder="สถานที่ทำงาน" class="mobile-input text-xs">
                                    <input type="tel" name="family_info[father][phone]" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="เบอร์โทรศัพท์" class="mobile-input text-xs">
                                </div>
                            </div>

                            <!-- Mother -->
                            <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 space-y-2 mb-3">
                                <p class="text-xs font-bold text-slate-800 dark:text-white flex items-center justify-between">
                                    <span class="flex items-center gap-1.5"><i class="fa-solid fa-venus text-pink-500"></i> ข้อมูลมารดา</span>
                                    <span class="text-[10px] font-normal text-slate-400">ไม่บังคับกรอก (Optional)</span>
                                </p>
                                <div class="grid grid-cols-3 gap-2">
                                    <input type="text" name="family_info[mother][name]" placeholder="ชื่อ-นามสกุล มารดา (ไม่บังคับ)" class="mobile-input col-span-2 text-xs">
                                    <input type="number" name="family_info[mother][age]" placeholder="อายุ (ปี)" class="mobile-input text-xs text-center">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <input type="text" name="family_info[mother][occupation]" placeholder="อาชีพ" class="mobile-input text-xs">
                                    <input type="text" name="family_info[mother][workplace]" placeholder="สถานที่ทำงาน" class="mobile-input text-xs">
                                    <input type="tel" name="family_info[mother][phone]" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="เบอร์โทรศัพท์" class="mobile-input text-xs">
                                </div>
                            </div>

                            <!-- Single status indicator (Spouse hidden) -->
                            <div x-show="maritalStatus === 'โสด'" class="p-3 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/30 text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                <span>สถานภาพ: <strong>โสด</strong> (ไม่ต้องกรอกข้อมูลภรรยา/สามี - ซ่อนช่องข้อมูล)</span>
                            </div>

                            <!-- Spouse (Shown only when Married / Widowed / Separated) -->
                            <div x-show="maritalStatus !== 'โสด'" x-transition class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 space-y-2">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                        <i class="fa-solid fa-ring text-amber-500"></i> ข้อมูลคู่สมรส
                                    </p>
                                    <span class="text-[10px] font-medium" :class="maritalStatus === 'แต่งงาน' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'" x-text="maritalStatus === 'แต่งงาน' ? 'ระบุข้อมูลคู่สมรส' : 'ไม่บังคับกรอก (ถ้ามี)'"></span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <input type="text" name="family_info[spouse][name]" :disabled="maritalStatus === 'โสด'" placeholder="ชื่อ-นามสกุล สามี/ภรรยา" class="mobile-input col-span-2 text-xs">
                                    <input type="number" name="family_info[spouse][age]" :disabled="maritalStatus === 'โสด'" placeholder="อายุ (ปี)" class="mobile-input text-xs text-center">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <input type="text" name="family_info[spouse][occupation]" :disabled="maritalStatus === 'โสด'" placeholder="อาชีพ" class="mobile-input text-xs">
                                    <input type="text" name="family_info[spouse][workplace]" :disabled="maritalStatus === 'โสด'" placeholder="สถานที่ทำงาน" class="mobile-input text-xs">
                                    <input type="tel" name="family_info[spouse][phone]" :disabled="maritalStatus === 'โสด'" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="เบอร์โทรศัพท์" class="mobile-input text-xs">
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Children & Siblings -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-child-reaching text-[#B21F24]"></i>
                                <span>บุตรและพี่น้อง</span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 mb-4">
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">มีบุตร (คน) <span class="text-[10px] font-normal text-slate-400">(ไม่มีระบุ 0)</span></label>
                                    <input type="number" name="family_info[children_count]" min="0" value="{{ old('family_info.children_count', '0') }}" placeholder="0" class="mobile-input text-center font-bold">
                                </div>
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">พี่น้องรวม (คน)</label>
                                    <input type="number" name="family_info[total_siblings]" placeholder="0" class="mobile-input text-center font-bold">
                                </div>
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">ชาย (คน)</label>
                                    <input type="number" name="family_info[siblings_male]" placeholder="0" class="mobile-input text-center">
                                </div>
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">หญิง (คน)</label>
                                    <input type="number" name="family_info[siblings_female]" placeholder="0" class="mobile-input text-center">
                                </div>
                                <div class="mobile-field-group !mb-0 col-span-2 sm:col-span-1">
                                    <label class="mobile-label text-xs">บุตรคนที่</label>
                                    <input type="number" name="family_info[birth_order]" placeholder="1" class="mobile-input text-center font-bold">
                                </div>
                            </div>

                            <!-- Siblings List Cards -->
                            <div class="space-y-2 pt-2 border-t border-slate-100 dark:border-slate-700">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold text-slate-700 dark:text-slate-300">รายชื่อพี่น้อง</p>
                                    <button type="button" @click="addSibling()" class="text-xs text-[#B21F24] hover:text-red-700 font-bold flex items-center gap-1">
                                        <i class="fa-solid fa-plus-circle"></i> เพิ่มพี่น้อง
                                    </button>
                                </div>

                                <template x-for="(sib, sIdx) in siblings" :key="sIdx">
                                    <div class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 relative">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[11px] font-bold text-slate-500" x-text="'คนที่ ' + (sIdx + 1)"></span>
                                            <button type="button" x-show="siblings.length > 1" @click="removeSibling(sIdx)" class="text-[11px] text-red-500 hover:text-red-700">
                                                <i class="fa-solid fa-trash-can mr-0.5"></i> ลบ
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                            <input type="text" :name="'family_info[siblings]['+sIdx+'][name]'" x-model="sib.name" placeholder="ชื่อ-นามสกุล" class="mobile-input text-xs">
                                            <input type="number" :name="'family_info[siblings]['+sIdx+'][age]'" x-model="sib.age" placeholder="อายุ (ปี)" class="mobile-input text-xs text-center">
                                            <input type="text" :name="'family_info[siblings]['+sIdx+'][occupation]'" x-model="sib.occupation" placeholder="อาชีพ" class="mobile-input text-xs">
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Card 3: Emergency Contact -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-bell text-[#B21F24]"></i>
                                <span>บุคคลสำหรับติดต่อในกรณีฉุกเฉิน</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">ชื่อ - สกุล ผู้ติดต่อฉุกเฉิน <span class="text-[#B21F24]">*</span></label>
                                    <input type="text" name="emergency_contact[name]" placeholder="ชื่อ-นามสกุล" class="mobile-input font-medium">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">ความสัมพันธ์</label>
                                    <input type="text" name="emergency_contact[relationship]" placeholder="เช่น บิดา, มารดา, พี่สาว" class="mobile-input">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="mobile-field-group">
                                    <label class="mobile-label">เบอร์โทรศัพท์มือถือ <span class="text-[#B21F24]">*</span></label>
                                    <input type="tel" name="emergency_contact[mobile]" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="08x-xxx-xxxx" class="mobile-input font-bold text-[#B21F24]">
                                </div>
                                <div class="mobile-field-group">
                                    <label class="mobile-label">เบอร์ที่ทำงาน <span class="text-gray-400 font-normal text-xs">(ถ้ามี)</span></label>
                                    <input type="tel" name="emergency_contact[work_phone]" value="{{ old('emergency_contact.work_phone') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="เบอร์ที่ทำงาน" class="mobile-input">
                                </div>
                            </div>

                            <div class="mobile-field-group">
                                <label class="mobile-label">สถานที่ทำงาน <span class="text-gray-400 font-normal text-xs">(ถ้ามี)</span></label>
                                <input type="text" name="emergency_contact[workplace]" value="{{ old('emergency_contact.workplace') }}" placeholder="ชื่อบริษัทหรือสถานที่ทำงาน" class="mobile-input">
                            </div>

                            <!-- Address of emergency contact -->
                            <div class="p-3 bg-slate-50 dark:bg-slate-800/40 rounded-xl border border-slate-200 dark:border-slate-700 space-y-2">
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300">ที่อยู่ผู้ติดต่อฉุกเฉิน</p>
                                <div class="grid grid-cols-4 gap-2">
                                    <input type="text" name="emergency_contact[house_no]" value="{{ old('emergency_contact.house_no') }}" placeholder="เลขที่" class="mobile-input text-xs">
                                    <input type="text" name="emergency_contact[moo]" value="{{ old('emergency_contact.moo') }}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="หมู่" class="mobile-input text-xs">
                                    <input type="text" name="emergency_contact[soi]" value="{{ old('emergency_contact.soi') }}" placeholder="ซอย" class="mobile-input text-xs">
                                    <input type="text" name="emergency_contact[road]" value="{{ old('emergency_contact.road') }}" placeholder="ถนน" class="mobile-input text-xs">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                    <input type="text" id="m_emerg_province" name="emergency_contact[province]" autocomplete="off" placeholder="เลือกหรือพิมพ์จังหวัด" class="mobile-input text-xs thai-addr-input">
                                    <input type="text" id="m_emerg_district" name="emergency_contact[district]" autocomplete="off" placeholder="เลือกหรือพิมพ์อำเภอ" class="mobile-input text-xs thai-addr-input">
                                    <input type="text" id="m_emerg_subdistrict" name="emergency_contact[subdistrict]" autocomplete="off" placeholder="เลือกหรือพิมพ์ตำบล" class="mobile-input text-xs thai-addr-input">
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- MOBILE STEP 3: EDUCATION, EXPERIENCE & LANGUAGE -->
                    <div x-show="viewMode === 'mobile' && step === 3" class="space-y-4 mb-6">
                        <!-- Header banner -->
                        <div class="mobile-form-card !bg-gradient-to-r !from-[#B21F24] !to-red-700 text-white !p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded">ขั้นตอนที่ 3/5</span>
                                    <h3 class="text-base font-black mt-1">ประวัติการศึกษาและการทำงาน</h3>
                                </div>
                                <span class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Card 1: Education -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-user-graduate text-[#B21F24]"></i>
                                <span>ประวัติการศึกษา</span>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(edu, eIdx) in education" :key="eIdx">
                                    <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-black text-[#B21F24]" x-text="edu.level || ('ระดับที่ ' + (eIdx + 1))"></span>
                                            <button type="button" x-show="education.length > 1" @click="removeEducation(eIdx)" class="text-[11px] text-red-500 hover:text-red-700">
                                                <i class="fa-solid fa-trash-can mr-0.5"></i> ลบ
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <select :name="'education['+eIdx+'][level]'" x-model="edu.level" class="mobile-input text-xs font-semibold bg-white dark:bg-slate-800 cursor-pointer">
                                                <option value="">-- เลือกระดับการศึกษา --</option>
                                                <option value="ประถมศึกษา">ประถมศึกษา</option>
                                                <option value="มัธยมศึกษา">มัธยมศึกษา</option>
                                                <option value="ปวช./ปวส.">ปวช./ปวส.</option>
                                                <option value="ปริญญาตรี">ปริญญาตรี</option>
                                                <option value="ปริญญาโท">ปริญญาโท</option>
                                                <option value="ปริญญาเอก">ปริญญาเอก</option>
                                                <option value="อื่นๆ">อื่นๆ</option>
                                            </select>
                                            <input type="text" :name="'education['+eIdx+'][institution_name]'" x-model="edu.institution_name" placeholder="ชื่อสถาบันการศึกษา" class="mobile-input text-xs">
                                        </div>
                                        <div class="grid grid-cols-3 gap-2">
                                            <input type="text" :name="'education['+eIdx+'][major]'" x-model="edu.major" placeholder="สาขาวิชา / เอก" class="mobile-input col-span-2 text-xs">
                                            <div class="grid grid-cols-2 gap-1">
                                                <input type="text" :name="'education['+eIdx+'][start_year]'" x-model="edu.start_year" placeholder="ปีเริ่ม" class="mobile-input text-xs text-center">
                                                <input type="text" :name="'education['+eIdx+'][end_year]'" x-model="edu.end_year" placeholder="ปีจบ" class="mobile-input text-xs text-center">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="addEducation()" class="w-full py-2.5 rounded-xl border-2 border-dashed border-[#B21F24]/40 hover:border-[#B21F24] text-[#B21F24] text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                                    <i class="fa-solid fa-plus"></i> เพิ่มประวัติการศึกษา
                                </button>
                            </div>
                        </div>

                        <!-- Card 2: Working Experience -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-briefcase text-[#B21F24]"></i>
                                <span>ประวัติการทำงาน (เรียงจากปัจจุบันย้อนหลัง)</span>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(exp, xIdx) in experience" :key="xIdx">
                                    <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-black text-slate-700 dark:text-white" x-text="'ที่ทำงานลำดับที่ ' + (xIdx + 1)"></span>
                                            <button type="button" x-show="experience.length > 1" @click="removeExperience(xIdx)" class="text-[11px] text-red-500 hover:text-red-700">
                                                <i class="fa-solid fa-trash-can mr-0.5"></i> ลบ
                                            </button>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <input type="text" :name="'experience['+xIdx+'][company_name]'" x-model="exp.company_name" placeholder="ชื่อบริษัท / องค์กร" class="mobile-input text-xs font-medium">
                                            <input type="text" :name="'experience['+xIdx+'][position]'" x-model="exp.position" placeholder="ตำแหน่งงาน" class="mobile-input text-xs font-medium">
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                            <input type="text" :name="'experience['+xIdx+'][start_date]'" x-model="exp.start_date" placeholder="วันที่เริ่มงาน" class="mobile-input text-xs datepicker-th text-center">
                                            <input type="text" :name="'experience['+xIdx+'][end_date]'" x-model="exp.end_date" placeholder="วันที่สิ้นสุด" class="mobile-input text-xs datepicker-th text-center">
                                            <input type="number" :name="'experience['+xIdx+'][salary]'" x-model="exp.salary" placeholder="เงินเดือน (บาท)" class="mobile-input col-span-2 sm:col-span-1 text-xs text-right font-bold text-[#B21F24]">
                                        </div>
                                        <input type="text" :name="'experience['+xIdx+'][job_detail]'" x-model="exp.job_detail" placeholder="ลักษณะงานที่รับผิดชอบ" class="mobile-input text-xs">
                                        <input type="text" :name="'experience['+xIdx+'][reason_for_leaving]'" x-model="exp.reason_for_leaving" placeholder="เหตุผลที่ลาออก" class="mobile-input text-xs">
                                    </div>
                                </template>

                                <button type="button" @click="addExperience()" class="w-full py-2.5 rounded-xl border-2 border-dashed border-[#B21F24]/40 hover:border-[#B21F24] text-[#B21F24] text-xs font-bold flex items-center justify-center gap-1.5 transition-all">
                                    <i class="fa-solid fa-plus"></i> เพิ่มประวัติการทำงาน
                                </button>
                            </div>

                            <div class="mobile-field-group mt-4 !mb-0 space-y-2">
                                <label class="mobile-label">แนบเอกสาร Resume / Curriculum Vitae (CV) <span class="text-[#B21F24]">*</span></label>
                                
                                <!-- Mobile Resume Upload Box -->
                                <div class="border-2 border-dashed border-red-300 dark:border-slate-600 rounded-xl p-3 text-center bg-red-50/50 dark:bg-slate-800/40 relative hover:border-[#B21F24] transition-all cursor-pointer group shadow-sm">
                                    <input type="file" name="resume[]" accept=".pdf,.doc,.docx,image/*" multiple @change="handleFileChange($event, 'resume')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                    <div x-show="resumeFiles.length === 0" class="flex items-center justify-center gap-2.5">
                                        <i class="fa-solid fa-cloud-arrow-up text-[#B21F24] text-lg"></i>
                                        <div class="text-left">
                                            <p class="text-xs font-bold text-slate-800 dark:text-white">แตะเพื่อเลือกไฟล์ Resume / CV (เลือกได้หลายไฟล์)</p>
                                            <p class="text-[10px] text-slate-500">รองรับ PDF, DOC, DOCX หรือไฟล์รูปภาพ</p>
                                        </div>
                                    </div>
                                    <div x-show="resumeFiles.length > 0" class="flex flex-col items-center justify-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        <div class="flex items-center justify-center gap-2 mb-0.5">
                                            <i class="fa-solid fa-circle-check text-base"></i>
                                            <span>แนบสำเร็จ <span x-text="resumeFiles.length"></span> ไฟล์</span>
                                        </div>
                                        <div class="flex flex-col gap-1 w-full text-left px-1 max-h-40 overflow-y-auto">
                                            <template x-for="(f, idx) in resumeFiles" :key="idx">
                                                <div class="flex items-center justify-between gap-2 text-[11px] text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700/80 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-600 w-full shadow-2xs">
                                                    <div class="flex items-center gap-1.5 min-w-0 flex-grow">
                                                        <span class="text-[#B21F24] font-mono text-[10px] font-bold shrink-0" x-text="(idx + 1) + '.'"></span>
                                                        <span class="truncate font-medium" :title="typeof f === 'string' ? f : f.name" x-text="typeof f === 'string' ? f : f.name"></span>
                                                    </div>
                                                    <button type="button" @click.stop.prevent="removeFile('resume', idx)" class="z-20 relative text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 p-1 rounded transition-colors shrink-0" title="ลบไฟล์นี้">
                                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-normal underline mt-1">(แตะพื้นที่ว่างเพื่อแนบเพิ่ม)</span>
                                    </div>
                                </div>

                                <textarea name="experience_summary" rows="3" placeholder="อธิบายเพิ่มเติมเกี่ยวกับประสบการณ์ทำงานของคุณ..." class="mobile-input text-xs"></textarea>
                            </div>
                        </div>

                        <!-- Card 3: Language Ability -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-language text-[#B21F24]"></i>
                                <span>ความสามารถทางภาษา</span>
                            </div>

                            <div class="space-y-3">
                                @foreach([
                                    'thai' => 'ภาษาไทย (Thai)',
                                    'english' => 'ภาษาอังกฤษ (English)',
                                    'other1' => 'ภาษาอื่นๆ 1',
                                    'other2' => 'ภาษาอื่นๆ 2'
                                ] as $lCode => $lText)
                                    @php
                                        $mDef = ($lCode === 'thai') ? 'Good' : (($lCode === 'english') ? 'Fair' : 'Fair');
                                        $mSpk = old("language_skills.{$lCode}.speaking", $mDef);
                                        $mWrt = old("language_skills.{$lCode}.writing", $mDef);
                                        $mRdg = old("language_skills.{$lCode}.reading", $mDef);
                                    @endphp
                                    <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40">
                                        <div class="flex items-center justify-between mb-2">
                                            @if(str_contains($lCode, 'other'))
                                                <input type="text" name="language_skills[{{ $lCode }}][name]" value="{{ old("language_skills.{$lCode}.name") }}" placeholder="ระบุภาษาอื่น เช่น จีน, ญี่ปุ่น" class="mobile-input text-xs w-48 font-medium">
                                            @else
                                                <span class="text-xs font-bold text-slate-800 dark:text-white">{{ $lText }}</span>
                                            @endif
                                        </div>
                                        <div class="grid grid-cols-3 gap-2 text-[11px]">
                                            <!-- Speaking -->
                                            <div>
                                                <span class="block text-slate-500 font-bold mb-1">พูด</span>
                                                <select name="language_skills[{{ $lCode }}][speaking]" class="mobile-input !py-1 text-xs">
                                                    <option value="Good" {{ $mSpk === 'Good' ? 'selected' : '' }}>ดี (Good)</option>
                                                    <option value="Fair" {{ $mSpk === 'Fair' ? 'selected' : '' }}>ปานกลาง (Fair)</option>
                                                    <option value="Poor" {{ $mSpk === 'Poor' ? 'selected' : '' }}>พอใช้ (Poor)</option>
                                                </select>
                                            </div>
                                            <!-- Writing -->
                                            <div>
                                                <span class="block text-slate-500 font-bold mb-1">เขียน</span>
                                                <select name="language_skills[{{ $lCode }}][writing]" class="mobile-input !py-1 text-xs">
                                                    <option value="Good" {{ $mWrt === 'Good' ? 'selected' : '' }}>ดี (Good)</option>
                                                    <option value="Fair" {{ $mWrt === 'Fair' ? 'selected' : '' }}>ปานกลาง (Fair)</option>
                                                    <option value="Poor" {{ $mWrt === 'Poor' ? 'selected' : '' }}>พอใช้ (Poor)</option>
                                                </select>
                                            </div>
                                            <!-- Reading -->
                                            <div>
                                                <span class="block text-slate-500 font-bold mb-1">อ่าน</span>
                                                <select name="language_skills[{{ $lCode }}][reading]" class="mobile-input !py-1 text-xs">
                                                    <option value="Good" {{ $mRdg === 'Good' ? 'selected' : '' }}>ดี (Good)</option>
                                                    <option value="Fair" {{ $mRdg === 'Fair' ? 'selected' : '' }}>ปานกลาง (Fair)</option>
                                                    <option value="Poor" {{ $mRdg === 'Poor' ? 'selected' : '' }}>พอใช้ (Poor)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>


                    <!-- MOBILE STEP 4: SKILLS, SPECIAL ABILITIES & QUESTIONS -->
                    <div x-show="viewMode === 'mobile' && step === 4" class="space-y-4 mb-6">
                        <!-- Header banner -->
                        <div class="mobile-form-card !bg-gradient-to-r !from-[#B21F24] !to-red-700 text-white !p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded">ขั้นตอนที่ 4/5</span>
                                    <h3 class="text-base font-black mt-1">ทักษะ ความสามารถพิเศษ และคำถาม</h3>
                                </div>
                                <span class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-laptop-code"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Card 1: Computer Skills -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-desktop text-[#B21F24]"></i>
                                <span>ทักษะด้านคอมพิวเตอร์และโปรแกรม</span>
                            </div>
                            <div class="mobile-field-group !mb-0">
                                <label class="mobile-label">โปรแกรมคอมพิวเตอร์ที่ชำนาญ</label>
                                <input type="text" name="computer_skills" placeholder="เช่น MS Office, Photoshop, AutoCAD, SAP, Laravel" class="mobile-input font-medium">
                            </div>
                        </div>

                        <!-- Card 2: Driving & Special Abilities -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-car text-[#B21F24]"></i>
                                <span>การขับขี่และความสามารถพิเศษ</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <!-- Car -->
                                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 space-y-2">
                                    <p class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                        <i class="fa-solid fa-car-side text-blue-500"></i> ขับรถยนต์
                                    </p>
                                    <div class="flex items-center gap-4 text-xs">
                                        <span class="text-slate-500">ขับได้:</span>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="special_abilities[car_driving]" value="ได้" class="w-3.5 h-3.5 text-[#B21F24]"><span>ได้</span></label>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="special_abilities[car_driving]" value="ไม่ได้" class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่ได้</span></label>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs pt-1 border-t border-slate-200 dark:border-slate-700">
                                        <span class="text-slate-500">ใบขับขี่:</span>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="special_abilities[car_license]" value="มี" class="w-3.5 h-3.5 text-[#B21F24]"><span>มี</span></label>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="special_abilities[car_license]" value="ไม่มี" class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่มี</span></label>
                                    </div>
                                </div>

                                <!-- Motorcycle -->
                                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 space-y-2">
                                    <p class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                                        <i class="fa-solid fa-motorcycle text-emerald-500"></i> ขับรถจักรยานยนต์
                                    </p>
                                    <div class="flex items-center gap-4 text-xs">
                                        <span class="text-slate-500">ขับได้:</span>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="special_abilities[motor_driving]" value="ได้" class="w-3.5 h-3.5 text-[#B21F24]"><span>ได้</span></label>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="special_abilities[motor_driving]" value="ไม่ได้" class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่ได้</span></label>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs pt-1 border-t border-slate-200 dark:border-slate-700">
                                        <span class="text-slate-500">ใบขับขี่:</span>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="special_abilities[motor_license]" value="มี" class="w-3.5 h-3.5 text-[#B21F24]"><span>มี</span></label>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="special_abilities[motor_license]" value="ไม่มี" class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่มี</span></label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2.5 text-xs">
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">การใช้เครื่องใช้สำนักงาน</label>
                                    <input type="text" name="special_abilities[office_machine]" placeholder="เช่น เครื่องถ่ายเอกสาร, แฟกซ์, สแกนเนอร์" class="mobile-input text-xs">
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="mobile-field-group !mb-0">
                                        <label class="mobile-label text-xs">งานอดิเรก</label>
                                        <input type="text" name="special_abilities[hobbies]" placeholder="เช่น อ่านหนังสือ, ถ่ายภาพ" class="mobile-input text-xs">
                                    </div>
                                    <div class="mobile-field-group !mb-0">
                                        <label class="mobile-label text-xs">กีฬาที่ชอบ</label>
                                        <input type="text" name="special_abilities[sports]" placeholder="เช่น ฟุตบอล, แบดมินตัน, วิ่ง" class="mobile-input text-xs">
                                    </div>
                                </div>
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">ความรู้พิเศษ / ทักษะอื่นๆ</label>
                                    <input type="text" name="special_abilities[special_knowledge]" placeholder="ความรู้หรือทักษะเฉพาะทางอื่นๆ" class="mobile-input text-xs">
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Questions & Expected Salary -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-clipboard-question text-[#B21F24]"></i>
                                <span>คำถามทั่วไปและเงินเดือนที่คาดหวัง</span>
                            </div>

                            <div class="space-y-3 text-xs">
                                <!-- Up country -->
                                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 space-y-2">
                                    <p class="font-bold text-slate-800 dark:text-white">สามารถไปปฏิบัติงานต่างจังหวัดได้หรือไม่?</p>
                                    <div class="flex items-center gap-4">
                                        <label class="inline-flex items-center gap-1.5"><input type="radio" name="application_questions[work_up_country]" value="ได้" class="w-3.5 h-3.5 text-[#B21F24]"><span>ได้</span></label>
                                        <label class="inline-flex items-center gap-1.5"><input type="radio" name="application_questions[work_up_country]" value="ไม่ได้" class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่ได้</span></label>
                                    </div>
                                    <input type="text" name="application_questions[work_up_country_other]" placeholder="เงื่อนไขอื่นๆ (ถ้ามี)" class="mobile-input text-xs mt-1">
                                </div>

                                <!-- Source of job info -->
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">ทราบข่าวการรับสมัครจากแหล่งใด?</label>
                                    <input type="text" name="application_questions[source_info]" placeholder="เช่น เว็บไซต์บริษัท, Facebook, JobThai, คนแนะนำ" class="mobile-input text-xs">
                                </div>

                                <!-- Applied before -->
                                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 space-y-2">
                                    <p class="font-bold text-slate-800 dark:text-white">เคยสมัครงานกับบริษัทนี้มาก่อนหรือไม่?</p>
                                    <div class="flex items-center gap-4">
                                        <label class="inline-flex items-center gap-1.5"><input type="radio" name="application_questions[applied_before]" value="เคย" class="w-3.5 h-3.5 text-[#B21F24]"><span>เคย</span></label>
                                        <label class="inline-flex items-center gap-1.5"><input type="radio" name="application_questions[applied_before]" value="ไม่เคย" checked class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่เคย</span></label>
                                    </div>
                                    <input type="text" name="application_questions[applied_before_when]" placeholder="ถ้าเคย เมื่อใด?" class="mobile-input text-xs mt-1">
                                </div>

                                <!-- Relatives working here -->
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">มีญาติหรือเพื่อนที่ทำงานในบริษัทนี้หรือไม่? (ระบุชื่อ)</label>
                                    <input type="text" name="application_questions[relative_known]" placeholder="ระบุชื่อและแผนก (ถ้ามี)" class="mobile-input text-xs">
                                </div>

                                <!-- Reason to apply -->
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">เพราะเหตุใดท่านจึงมาสมัครงานกับบริษัทฯ</label>
                                    <textarea name="application_questions[reason_to_apply]" rows="2" placeholder="เหตุผลและแรงจูงใจในการร่วมงานกับเรา" class="mobile-input text-xs"></textarea>
                                </div>

                                <!-- Expected salary -->
                                <div class="mobile-field-group !mb-0 p-3 bg-red-50/60 dark:bg-slate-800/60 border border-red-200 dark:border-slate-700 rounded-xl">
                                    <label class="mobile-label text-sm text-[#B21F24]">อัตราเงินเดือนที่คาดหวัง (บาท/เดือน)</label>
                                    <div class="relative">
                                        <input type="number" name="expected_salary" value="{{ old('expected_salary') }}" placeholder="เช่น 25000" class="mobile-input text-base font-black text-[#B21F24] pr-12">
                                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">บาท</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- MOBILE STEP 5: REFERENCES, DECLARATION & FILE UPLOADS -->
                    <div x-show="viewMode === 'mobile' && step === 5" class="space-y-4 mb-6">
                        <!-- Header banner -->
                        <div class="mobile-form-card !bg-gradient-to-r !from-[#B21F24] !to-red-700 text-white !p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[11px] font-bold uppercase tracking-wider bg-white/20 px-2 py-0.5 rounded">ขั้นตอนที่ 5/5</span>
                                    <h3 class="text-base font-black mt-1">บุคคลอ้างอิง การรับรอง และเอกสารแนบ</h3>
                                </div>
                                <span class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Card 1: References -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-user-check text-[#B21F24]"></i>
                                <span>บุคคลอ้างอิง 2 ท่าน (ไม่ใช่ญาติหรือนายจ้างเดิม)</span>
                            </div>
                            <div class="space-y-2 text-xs">
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">1. บุคคลอ้างอิงที่ 1 (ชื่อ, ที่อยู่, เบอร์โทร, อาชีพ)</label>
                                    <input type="text" name="references_info[ref1][text]" placeholder="ชื่อ-นามสกุล, ตำแหน่ง, เบอร์โทรศัพท์" class="mobile-input text-xs">
                                </div>
                                <div class="mobile-field-group !mb-0">
                                    <label class="mobile-label text-xs">2. บุคคลอ้างอิงที่ 2 (ชื่อ, ที่อยู่, เบอร์โทร, อาชีพ)</label>
                                    <input type="text" name="references_info[ref2][text]" placeholder="ชื่อ-นามสกุล, ตำแหน่ง, เบอร์โทรศัพท์" class="mobile-input text-xs">
                                </div>

                                <div class="pt-2 flex items-center justify-between">
                                    <span class="font-bold text-slate-700 dark:text-slate-300">อนุญาตให้ตรวจสอบประวัติการทำงานหรือไม่?</span>
                                    <div class="flex items-center gap-3">
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="references_info[allow_check_history]" value="อนุญาต" checked class="w-3.5 h-3.5 text-[#B21F24]"><span>อนุญาต</span></label>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="references_info[allow_check_history]" value="ไม่อนุญาต" class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่อนุญาต</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2: Truth Questionnaire -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-shield-halved text-[#B21F24]"></i>
                                <span>การตอบคำถามตามความเป็นจริง</span>
                            </div>

                            <div class="space-y-2.5 text-xs">
                                @php
                                    $truthQuestions = [
                                        ['key' => 'contagious_disease', 'label' => 'เคยเป็นโรคติดต่อร้ายแรง โรคทางจิต หรือโรคพิษสุราเรื้อรัง'],
                                        ['key' => 'arrested_or_prisoner', 'label' => 'เคยถูกจับกุมดำเนินคดี หรือคุมขังเป็นนักโทษ'],
                                        ['key' => 'fired_from_company', 'label' => 'เคยถูกให้ออกจากงานเนื่องจากทำผิดระเบียบบริษัท'],
                                        ['key' => 'drug_addict', 'label' => 'เป็นผู้ติดหรือเคยติดยาเสพติด'],
                                    ];
                                @endphp

                                @foreach($truthQuestions as $tq)
                                    <div class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 flex items-center justify-between gap-3">
                                        <span class="text-slate-700 dark:text-slate-300 font-medium leading-tight">{{ $tq['label'] }}</span>
                                        <div class="flex items-center gap-3 shrink-0">
                                            <label class="inline-flex items-center gap-1"><input type="radio" name="health_criminal_questionnaire[{{ $tq['key'] }}]" value="เคย" class="w-3.5 h-3.5 text-[#B21F24]"><span>เคย</span></label>
                                            <label class="inline-flex items-center gap-1"><input type="radio" name="health_criminal_questionnaire[{{ $tq['key'] }}]" value="ไม่เคย" checked class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่เคย</span></label>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/40 flex items-center justify-between gap-3">
                                    <span class="text-slate-700 dark:text-slate-300 font-medium leading-tight">ยินยอมให้ตรวจหาสารเสพติดในร่างกายหรือไม่?</span>
                                    <div class="flex items-center gap-3 shrink-0">
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="health_criminal_questionnaire[permit_drug_check]" value="ยินยอม" checked class="w-3.5 h-3.5 text-[#B21F24]"><span>ยินยอม</span></label>
                                        <label class="inline-flex items-center gap-1"><input type="radio" name="health_criminal_questionnaire[permit_drug_check]" value="ไม่ยินยอม" class="w-3.5 h-3.5 text-[#B21F24]"><span>ไม่ยินยอม</span></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: File Uploads & Checklist -->
                        <div class="mobile-form-card">
                            <div class="mobile-section-title">
                                <i class="fa-solid fa-cloud-arrow-up text-[#B21F24]"></i>
                                <span>อัปโหลดเอกสารประกอบ (PDF)</span>
                            </div>

                            <div class="space-y-3 mb-4">
                                <!-- Resume -->
                                <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl p-4 text-center bg-slate-50 dark:bg-slate-800/50 relative hover:border-[#B21F24] cursor-pointer transition-all">
                                    <input type="file" name="resume[]" accept=".pdf,.doc,.docx,image/*" multiple @change="handleFileChange($event, 'resume')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                    <div x-show="resumeFiles.length === 0">
                                        <i class="fa-solid fa-file-pdf text-[#B21F24] text-3xl mb-1.5"></i>
                                        <p class="text-xs font-bold text-slate-800 dark:text-white">แตะเพื่อแนบไฟล์ Resume / CV (เลือกได้หลายไฟล์) *</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">จำเป็นต้องแนบไฟล์ ขนาดไม่เกิน 10MB ต่อไฟล์</p>
                                    </div>
                                    <div x-show="resumeFiles.length > 0" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex flex-col items-center justify-center gap-1">
                                        <div class="flex items-center gap-1 mb-0.5">
                                            <i class="fa-solid fa-circle-check text-base mb-1"></i>
                                            <span>แนบสำเร็จ <span x-text="resumeFiles.length"></span> ไฟล์</span>
                                        </div>
                                        <div class="flex flex-col gap-1 w-full text-left px-1 max-h-40 overflow-y-auto">
                                            <template x-for="(f, idx) in resumeFiles" :key="idx">
                                                <div class="flex items-center justify-between gap-2 text-[11px] text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700/80 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-600 w-full shadow-2xs">
                                                    <div class="flex items-center gap-1.5 min-w-0 flex-grow">
                                                        <span class="text-[#B21F24] font-mono text-[10px] font-bold shrink-0" x-text="(idx + 1) + '.'"></span>
                                                        <span class="truncate font-medium" :title="typeof f === 'string' ? f : f.name" x-text="typeof f === 'string' ? f : f.name"></span>
                                                    </div>
                                                    <button type="button" @click.stop.prevent="removeFile('resume', idx)" class="z-20 relative text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 p-1 rounded transition-colors shrink-0" title="ลบไฟล์นี้">
                                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-normal underline mt-1">(แตะพื้นที่ว่างเพื่อแนบเพิ่ม)</span>
                                    </div>
                                </div>

                                <!-- Portfolio (Multiple PDFs) -->
                                <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-2xl p-4 text-center bg-slate-50 dark:bg-slate-800/50 relative hover:border-[#B21F24] cursor-pointer transition-all">
                                    <input type="file" name="portfolio[]" accept=".pdf" multiple @change="handleFileChange($event, 'portfolio')" class="absolute inset-0 opacity-0 cursor-pointer z-10">
                                    <div x-show="portfolioFiles.length === 0">
                                        <i class="fa-solid fa-folder-open text-slate-400 text-3xl mb-1.5"></i>
                                        <p class="text-xs font-bold text-slate-800 dark:text-white">แนบ Portfolio / เอกสารอื่นๆ (PDF เลือกได้หลายไฟล์)</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">ไฟล์ PDF ขนาดไม่เกิน 10MB ต่อไฟล์</p>
                                    </div>
                                    <div x-show="portfolioFiles.length > 0" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex flex-col items-center justify-center gap-1">
                                        <div class="flex items-center gap-1 mb-0.5">
                                            <i class="fa-solid fa-circle-check text-base"></i>
                                            <span>แนบสำเร็จ <span x-text="portfolioFiles.length"></span> ไฟล์</span>
                                        </div>
                                        <div class="flex flex-col gap-1 w-full text-left px-1 max-h-40 overflow-y-auto">
                                            <template x-for="(f, idx) in portfolioFiles" :key="idx">
                                                <div class="flex items-center justify-between gap-2 text-[11px] text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700/80 px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-600 w-full shadow-2xs">
                                                    <div class="flex items-center gap-1.5 min-w-0 flex-grow">
                                                        <span class="text-blue-500 font-mono text-[10px] font-bold shrink-0" x-text="(idx + 1) + '.'"></span>
                                                        <span class="truncate font-medium" :title="typeof f === 'string' ? f : f.name" x-text="typeof f === 'string' ? f : f.name"></span>
                                                    </div>
                                                    <button type="button" @click.stop.prevent="removeFile('portfolio', idx)" class="z-20 relative text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/50 p-1 rounded transition-colors shrink-0" title="ลบไฟล์นี้">
                                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-normal underline mt-1">(แตะพื้นที่ว่างเพื่อแนบเพิ่ม)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Checklist -->
                            <div class="pt-3 border-t border-slate-100 dark:border-slate-700">
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2">รายการเอกสารแนบ</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    @foreach([
                                        'สำเนาวุฒิการศึกษา',
                                        'สำเนาบัตรประชาชน',
                                        'สำเนาทะเบียนบ้าน',
                                        'ใบรับรองการทำงาน',
                                        'สำเนาใบเปลี่ยนชื่อ-สกุล',
                                        'สำเนาเอกสารทางทหาร',
                                    ] as $docItem)
                                        <label class="inline-flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 cursor-pointer">
                                            <input type="checkbox" name="attached_documents_check[]" value="{{ $docItem }}" class="w-3.5 h-3.5 text-[#B21F24] rounded">
                                            <span class="text-[11px]">{{ $docItem }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Card 4: PDPA & Certification -->
                        <div class="mobile-form-card border-2 border-[#B21F24]/30 bg-red-50/20">
                            <div class="text-center space-y-2 mb-3">
                                <i class="fa-solid fa-handshake text-2xl text-[#B21F24]"></i>
                                <h4 class="text-xs font-bold text-slate-800 dark:text-white">คำรับรองและความยินยอม (PDPA)</h4>
                                <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed text-left">
                                    ข้าพเจ้าขอรับรองว่า ข้อความดังกล่าวทั้งหมดในใบสมัครนี้เป็นความจริงทุกประการ และยินยอมให้บริษัทฯ เก็บ ใช้ เปิดเผย ตรวจสอบข้อมูลดังกล่าวได้ตลอดเวลาตามที่จำเป็น หากปรากฏว่าข้อความหรือเอกสารไม่เป็นความจริง บริษัทฯ มีสิทธิ์เลิกจ้างได้ทันทีโดยไม่ต้องจ่ายค่าชดเชยใดๆ
                                </p>
                            </div>

                            <label class="flex items-start gap-2 p-3 rounded-xl bg-white dark:bg-slate-800 border-2 border-[#B21F24] cursor-pointer shadow-sm mb-3">
                                <input type="checkbox" name="pdpa_consent" value="1" required class="w-5 h-5 text-[#B21F24] focus:ring-[#B21F24] rounded mt-0.5 shrink-0">
                                <span class="text-xs font-black text-slate-800 dark:text-white">
                                    ข้าพเจ้าขอรับรองว่าข้อความทั้งหมดเป็นความจริง และยินยอมตามเงื่อนไขทุกประการ *
                                </span>
                            </label>

                            <!-- Mobile Signature Input -->
                            <div class="p-3 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 text-center space-y-1.5">
                                <label class="mobile-label justify-center text-xs font-bold text-slate-700 dark:text-slate-300">
                                    ลายมือชื่อผู้สมัคร (พิมพ์ชื่อ-นามสกุล เพื่อลงชื่อ) <span class="text-[#B21F24]">*</span>
                                </label>
                                <input type="text" 
                                       name="m_applicant_signature" 
                                       x-model="applicantSignature" 
                                       @input="signatureManuallyEdited = true" 
                                       placeholder="พิมพ์ชื่อ-นามสกุล เพื่อลงลายมือชื่อ *" 
                                       class="mobile-input text-center font-bold text-[#B21F24]">
                                <p class="text-[10px] text-slate-400">Applicants signature</p>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Action Controls with Direct Page Navigation (Responsive for Mobile, iPad, Notebook, Desktop) -->
                    <div class="bg-white dark:bg-slate-800 p-3 sm:p-4 rounded-2xl shadow-md border border-slate-300 dark:border-slate-700 flex flex-col sm:flex-row gap-3 justify-between items-center max-w-[1060px] mx-auto">
                        <button type="button" x-show="step > 1" @click="prevStep"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-white text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <i class="fa-solid fa-chevron-left text-xs"></i> ย้อนกลับ
                        </button>
                        <div x-show="step === 1" class="hidden sm:block w-20"></div>

                        <button type="button" x-show="step < 5" @click="nextStep"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-[#B21F24] hover:bg-red-700 text-white text-xs sm:text-sm font-bold shadow flex items-center justify-center gap-2 transition-all active:scale-95 cursor-pointer">
                            ไปสเต็ปถัดไป (<span x-text="step"></span>/5) <i class="fa-solid fa-chevron-right text-xs"></i>
                        </button>

                        <button type="button" @click.prevent="confirmSubmit($event)" x-show="step === 5" :disabled="submitting"
                            class="w-full sm:w-auto px-7 py-2.5 rounded-xl bg-[#B21F24] hover:bg-red-700 text-white text-xs sm:text-sm font-black shadow-lg shadow-red-600/30 flex items-center justify-center gap-2 transition-all active:scale-95 disabled:opacity-50 cursor-pointer">
                            <span x-show="!submitting"><i class="fa-solid fa-paper-plane mr-1"></i> ยืนยันและส่งใบสมัครงาน</span>
                            <span x-show="submitting"><i class="fa-solid fa-spinner animate-spin mr-1"></i> กำลังส่งใบสมัคร...</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>

    @push('scripts')
    <script>
    window.autoSyncLanguage = function(code, val) {
        ['writing', 'reading'].forEach(function(field) {
            var anyChecked = document.querySelector('input[name="language_skills[' + code + '][' + field + ']"]:checked');
            if (!anyChecked || code.indexOf('other') !== -1) {
                var target = document.querySelector('input[name="language_skills[' + code + '][' + field + ']"][value="' + val + '"]');
                if (target) {
                    target.checked = true;
                }
            }
        });
    };

    (function() {
        const searchApiUrl = "{{ route('api.thai_address.search') }}";
        let activeDropdown = null;
        let activeInput = null;
        let searchTimer = null;

        function closeAllDropdowns() {
            if (activeDropdown) {
                activeDropdown.remove();
                activeDropdown = null;
            }
            activeInput = null;
            document.querySelectorAll('.thai-addr-dropdown').forEach(d => d.remove());
        }

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.thai-addr-dropdown') && !e.target.classList.contains('thai-addr-input')) {
                closeAllDropdowns();
            }
        });

        // Reposition on resize
        window.addEventListener('resize', () => {
            if (activeDropdown && activeInput) {
                positionDropdown(activeDropdown, activeInput);
            }
        });

        function positionDropdown(dropdown, inputEl) {
            const rect = inputEl.getBoundingClientRect();
            dropdown.style.top = (rect.bottom + window.scrollY + 2) + 'px';
            dropdown.style.left = (rect.left + window.scrollX) + 'px';
            dropdown.style.minWidth = Math.max(rect.width, 320) + 'px';
        }

        function createDropdown(inputEl) {
            closeAllDropdowns();
            const dropdown = document.createElement('div');
            dropdown.className = 'thai-addr-dropdown';
            positionDropdown(dropdown, inputEl);
            document.body.appendChild(dropdown);
            activeDropdown = dropdown;
            activeInput = inputEl;
            return dropdown;
        }

        function fillFields(config, item) {
            const subEl = document.getElementById(config.subdistrict);
            const distEl = document.getElementById(config.district);
            const provEl = document.getElementById(config.province);
            const postEl = config.postcode ? document.getElementById(config.postcode) : null;

            if (item.s && subEl) { subEl.value = item.s; subEl.dispatchEvent(new Event('input', { bubbles: true })); }
            if (item.d && distEl) { distEl.value = item.d; distEl.dispatchEvent(new Event('input', { bubbles: true })); }
            if (item.p && provEl) { provEl.value = item.p; provEl.dispatchEvent(new Event('input', { bubbles: true })); }
            if (item.z && postEl) { postEl.value = item.z; postEl.dispatchEvent(new Event('input', { bubbles: true })); }
        }

        function renderFieldDropdown(inputEl, fieldKey, config, response) {
            // Guard: ensure the response is meant for the currently focused/active input
            if (document.activeElement !== inputEl && activeInput !== inputEl) {
                return;
            }

            const dropdown = createDropdown(inputEl);

            // Handle Province list (fieldKey === 'p')
            if (response && response.type === 'p') {
                const list = response.data || [];
                const header = document.createElement('div');
                header.className = 'px-3 py-1.5 bg-slate-50 border-b border-slate-200 text-xs text-slate-600 font-bold sticky top-0 flex justify-between items-center';
                header.innerHTML = `<span><i class="fa-solid fa-location-dot mr-1 text-[#B21F24]"></i> เลือกจังหวัด</span><span class="text-[10px] text-slate-400">${list.length} รายการ</span>`;
                dropdown.appendChild(header);

                if (list.length === 0) {
                    dropdown.innerHTML += '<div class="thai-addr-empty">ไม่พบจังหวัดที่ตรงกับคำค้นหา</div>';
                    return;
                }

                list.forEach(prov => {
                    const div = document.createElement('div');
                    div.className = 'thai-addr-item flex items-center justify-between text-xs py-2 px-3 font-semibold text-slate-700';
                    div.innerHTML = `<span>จ. ${prov}</span><span class="text-[10px] text-slate-400 font-normal">เลือกจังหวัด <i class="fa-solid fa-chevron-right ml-1 text-[9px]"></i></span>`;

                    div.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        inputEl.value = prov;
                        inputEl.dispatchEvent(new Event('input', { bubbles: true }));

                        // Optionally auto focus to District field next
                        const distEl = document.getElementById(config.district);
                        closeAllDropdowns();
                        if (distEl && !distEl.value) {
                            setTimeout(() => distEl.focus(), 50);
                        }
                    });

                    dropdown.appendChild(div);
                });
                return;
            }

            // Handle District list (fieldKey === 'd')
            if (response && response.type === 'd') {
                const list = response.data || [];
                const header = document.createElement('div');
                header.className = 'px-3 py-1.5 bg-slate-50 border-b border-slate-200 text-xs text-slate-600 font-bold sticky top-0 flex justify-between items-center';
                header.innerHTML = `<span><i class="fa-solid fa-city mr-1 text-[#B21F24]"></i> เลือกอำเภอ/เขต</span><span class="text-[10px] text-slate-400">${list.length} รายการ</span>`;
                dropdown.appendChild(header);

                if (list.length === 0) {
                    dropdown.innerHTML += '<div class="thai-addr-empty">ไม่พบอำเภอที่ตรงกับคำค้นหา</div>';
                    return;
                }

                list.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'thai-addr-item flex items-center justify-between text-xs py-2 px-3';
                    div.innerHTML = `<span class="font-semibold text-slate-800">อ. ${item.d}</span>` +
                                    `<span class="text-slate-500 text-[11px]">จ. ${item.p}</span>`;

                    div.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        inputEl.value = item.d;
                        inputEl.dispatchEvent(new Event('input', { bubbles: true }));

                        const provEl = document.getElementById(config.province);
                        if (provEl && !provEl.value) {
                            provEl.value = item.p;
                            provEl.dispatchEvent(new Event('input', { bubbles: true }));
                        }

                        closeAllDropdowns();
                        // Focus next field with a clean slate
                        const subEl = document.getElementById(config.subdistrict);
                        if (subEl) {
                            setTimeout(() => {
                                subEl.focus();
                            }, 50);
                        }
                    });

                    dropdown.appendChild(div);
                });
                return;
            }

            // Handle Subdistrict list (fieldKey === 's')
            if (response && response.type === 's') {
                const list = response.data || [];
                const header = document.createElement('div');
                header.className = 'px-3 py-1.5 bg-slate-50 border-b border-slate-200 text-xs text-slate-600 font-bold sticky top-0 flex justify-between items-center';
                header.innerHTML = `<span><i class="fa-solid fa-map-pin mr-1 text-[#B21F24]"></i> เลือกตำบล/แขวง</span><span class="text-[10px] text-slate-400">${list.length} รายการ</span>`;
                dropdown.appendChild(header);

                if (list.length === 0) {
                    dropdown.innerHTML += '<div class="thai-addr-empty">ไม่พบตำบลที่ตรงกับคำค้นหา</div>';
                    return;
                }

                list.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'thai-addr-item text-xs py-2 px-3';
                    div.innerHTML = `<div class="flex items-center justify-between">` +
                                    `<span class="font-bold text-slate-800">ต. ${item.s}</span>` +
                                    `<span class="font-bold text-[#B21F24]">${item.z}</span>` +
                                    `</div>` +
                                    `<div class="text-[11px] text-slate-500 mt-0.5">อ. ${item.d} &rsaquo; จ. ${item.p}</div>`;

                    div.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        fillFields(config, item);
                        closeAllDropdowns();
                    });

                    dropdown.appendChild(div);
                });
                return;
            }

            // General list (full match or postcode search)
            const list = Array.isArray(response) ? response : [];
            const header = document.createElement('div');
            header.className = 'px-3 py-1.5 bg-slate-50 border-b border-slate-200 text-xs text-slate-600 font-bold sticky top-0 flex justify-between items-center';
            header.innerHTML = `<span><i class="fa-solid fa-list-check mr-1 text-[#B21F24]"></i> คลิกเพื่อเติมข้อมูลอัตโนมัติ</span><span class="text-[10px] text-slate-400">${list.length} รายการ</span>`;
            dropdown.appendChild(header);

            if (list.length === 0) {
                dropdown.innerHTML += '<div class="thai-addr-empty"><i class="fa-solid fa-circle-exclamation mr-1 text-slate-400"></i> ไม่พบข้อมูลที่ตรงกับคำค้นหา</div>';
                return;
            }

            list.forEach(item => {
                const div = document.createElement('div');
                div.className = 'thai-addr-item text-xs py-2 px-3';
                div.innerHTML = `<div class="flex items-center justify-between">` +
                                `<span class="font-bold text-slate-800">ต.${item.s} &rsaquo; อ.${item.d}</span>` +
                                `<span class="font-bold text-[#B21F24]">${item.z}</span>` +
                                `</div>` +
                                `<div class="text-[11px] text-slate-500 mt-0.5">จ.${item.p}</div>`;

                div.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    fillFields(config, item);
                    closeAllDropdowns();
                });

                dropdown.appendChild(div);
            });
        }

        function setupAddressGroup(config) {
            const fields = [
                { input: document.getElementById(config.province), key: 'p' },
                { input: document.getElementById(config.district), key: 'd' },
                { input: document.getElementById(config.subdistrict), key: 's' },
                { input: document.getElementById(config.postcode), key: 'z' }
            ].filter(f => f.input);

            fields.forEach(f => {
                function executeSearch(inputEl) {
                    const query = inputEl.value.trim();

                    // Read current values of other fields to filter hierarchically
                    const provEl = document.getElementById(config.province);
                    const distEl = document.getElementById(config.district);
                    const provVal = provEl ? provEl.value.trim() : '';
                    const distVal = distEl ? distEl.value.trim() : '';

                    const params = new URLSearchParams({
                        q: query,
                        type: f.key,
                        province: provVal,
                        district: distVal
                    });

                    fetch(`${searchApiUrl}?${params.toString()}`)
                        .then(res => res.json())
                        .then(data => {
                            renderFieldDropdown(inputEl, f.key, config, data);
                        })
                        .catch(err => console.error("Address search error:", err));
                }

                f.input.addEventListener('input', function() {
                    clearTimeout(searchTimer);
                    const el = this;
                    searchTimer = setTimeout(() => { executeSearch(el); }, 150);
                });

                f.input.addEventListener('focus', function() {
                    executeSearch(this);
                });

                f.input.addEventListener('click', function() {
                    if (!activeDropdown) {
                        executeSearch(this);
                    }
                });

                f.input.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeAllDropdowns();
                    }
                });
            });
        }

        function initAddressAutocomplete() {
            // Group 1: ที่อยู่ปัจจุบัน (Present address - Paper mode)
            setupAddressGroup({
                subdistrict: 'addr_subdistrict',
                district: 'addr_district',
                province: 'addr_province',
                postcode: 'addr_postcode'
            });

            // Group 1-M: ที่อยู่ปัจจุบัน (Present address - Mobile mode)
            setupAddressGroup({
                subdistrict: 'm_addr_subdistrict',
                district: 'm_addr_district',
                province: 'm_addr_province',
                postcode: 'm_addr_postcode'
            });

            // Group 2: ผู้ติดต่อฉุกเฉิน (Emergency contact address - Paper mode)
            setupAddressGroup({
                subdistrict: 'emerg_subdistrict',
                district: 'emerg_district',
                province: 'emerg_province',
                postcode: null
            });

            // Group 2-M: ผู้ติดต่อฉุกเฉิน (Emergency contact address - Mobile mode)
            setupAddressGroup({
                subdistrict: 'm_emerg_subdistrict',
                district: 'm_emerg_district',
                province: 'm_emerg_province',
                postcode: null
            });

            // Group 3: จังหวัดที่ออกบัตรประชาชน (ID card issued province - Paper mode)
            setupAddressGroup({
                subdistrict: null,
                district: null,
                province: 'id_card_issued_province',
                postcode: null
            });

            // Group 3-M: จังหวัดที่ออกบัตรประชาชน (ID card issued province - Mobile mode)
            setupAddressGroup({
                subdistrict: null,
                district: null,
                province: 'm_id_card_issued_province',
                postcode: null
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initAddressAutocomplete);
        } else {
            initAddressAutocomplete();
        }
    })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/th.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.th) {
            flatpickr.localize(flatpickr.l10ns.th);
        }

        function formatHeaderBuddhistYear(instance) {
            setTimeout(function() {
                if (!instance || !instance.calendarContainer) return;
                let cYear = instance.currentYear;
                if (cYear > 2400) {
                    cYear -= 543;
                }
                const bYear = cYear + 543;
                
                const curMonthContainer = instance.calendarContainer.querySelector('.flatpickr-current-month');
                if (!curMonthContainer) return;

                const numYearInput = curMonthContainer.querySelector('.numInputWrapper');
                if (numYearInput) {
                    numYearInput.remove();
                }

                // Remove existing year select if any
                const oldSelect = curMonthContainer.querySelector('.flatpickr-buddhist-year-select');
                if (oldSelect) oldSelect.remove();

                let yearBtn = curMonthContainer.querySelector('.flatpickr-buddhist-year-btn');
                if (!yearBtn) {
                    yearBtn = document.createElement('button');
                    yearBtn.type = 'button';
                    yearBtn.className = 'flatpickr-buddhist-year-btn';
                    curMonthContainer.appendChild(yearBtn);

                    yearBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        e.preventDefault();
                        
                        let overlay = instance.calendarContainer.querySelector('.flatpickr-year-grid-overlay');
                        if (overlay) {
                            overlay.remove();
                            return;
                        }

                        overlay = document.createElement('div');
                        overlay.className = 'flatpickr-year-grid-overlay';

                        const header = document.createElement('div');
                        header.className = 'flatpickr-year-grid-header';
                        header.innerHTML = '<span>เลือกปี (พ.ศ.)</span><span class="flatpickr-year-grid-close">&times;</span>';
                        header.querySelector('.flatpickr-year-grid-close').addEventListener('click', function(ev) {
                            ev.stopPropagation();
                            overlay.remove();
                        });

                        const body = document.createElement('div');
                        body.className = 'flatpickr-year-grid-body';

                        const currentCEYear = new Date().getFullYear();
                        const maxYear = currentCEYear + 5; // 2574
                        const minYear = currentCEYear - 85; // 2484

                        let activeItem = null;
                        for (let y = maxYear; y >= minYear; y--) {
                            const item = document.createElement('div');
                            item.className = 'flatpickr-year-item' + (y === cYear ? ' active' : '');
                            item.textContent = (y + 543);
                            item.setAttribute('data-year', y);

                            if (y === cYear) {
                                activeItem = item;
                            }

                            item.addEventListener('click', function(ev) {
                                ev.stopPropagation();
                                const chosenYear = parseInt(this.getAttribute('data-year'), 10);
                                overlay.remove();
                                instance.changeYear(chosenYear);
                            });

                            body.appendChild(item);
                        }

                        overlay.appendChild(header);
                        overlay.appendChild(body);
                        instance.calendarContainer.appendChild(overlay);

                        // Auto-scroll to selected year
                        if (activeItem) {
                            setTimeout(function() {
                                activeItem.scrollIntoView({ block: 'center', behavior: 'smooth' });
                            }, 50);
                        }
                    });
                }

                yearBtn.innerHTML = bYear + ' <svg style="width:12px;height:12px;fill:#475569" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>';

                // If overlay exists and year changed externally, update active item
                const overlay = instance.calendarContainer.querySelector('.flatpickr-year-grid-overlay');
                if (overlay) {
                    overlay.querySelectorAll('.flatpickr-year-item').forEach(function(el) {
                        if (parseInt(el.getAttribute('data-year'), 10) === cYear) {
                            el.classList.add('active');
                        } else {
                            el.classList.remove('active');
                        }
                    });
                }
            }, 10);
        }

        function initThaiDatepickers() {
            flatpickr(".datepicker-th", {
                locale: (typeof flatpickr !== 'undefined' && flatpickr.l10ns && flatpickr.l10ns.th) ? flatpickr.l10ns.th : "th",
                altInput: true,
                altFormat: "d/m/Y",
                dateFormat: "Y-m-d",
                allowInput: true,
                parseDate: function(dateStr, formatStr) {
                    if (typeof dateStr === 'string' && dateStr.includes('/')) {
                        const parts = dateStr.split('/');
                        if (parts.length === 3) {
                            let day = parseInt(parts[0], 10);
                            let month = parseInt(parts[1], 10) - 1;
                            let year = parseInt(parts[2], 10);
                            if (year > 2400) {
                                year -= 543;
                            }
                            return new Date(year, month, day);
                        }
                    }
                    return flatpickr.parseDate(dateStr, formatStr);
                },
                formatDate: function(date, formatStr, locale) {
                    if (formatStr === 'd/m/Y') {
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        let year = date.getFullYear();
                        if (year < 2400) {
                            year += 543;
                        }
                        return day + '/' + month + '/' + year;
                    }
                    return flatpickr.formatDate(date, formatStr, locale);
                },
                onReady: function(selectedDates, dateStr, instance) {
                    if (instance.altInput) {
                        instance.altInput.className = instance.input.className;
                        instance.altInput.classList.remove('datepicker-th');
                        instance.altInput.classList.add('flatpickr-input');
                        instance.altInput.setAttribute('placeholder', 'วว/ดด/ปปปป');
                        instance.altInput.style.cursor = 'pointer';

                        // If this is date_of_birth, wire up age calculation on change
                        if (instance.input.name === 'date_of_birth') {
                            instance.altInput.addEventListener('change', function() {
                                instance.input.dispatchEvent(new Event('input', { bubbles: true }));
                                instance.input.dispatchEvent(new Event('change', { bubbles: true }));
                            });
                        }
                    }
                    formatHeaderBuddhistYear(instance);
                },
                onChange: function(selectedDates, dateStr, instance) {
                    // Trigger input event on underlying input for Alpine.js/Vue
                    instance.input.dispatchEvent(new Event('input', { bubbles: true }));
                    instance.input.dispatchEvent(new Event('change', { bubbles: true }));
                },
                onMonthChange: function(selectedDates, dateStr, instance) {
                    formatHeaderBuddhistYear(instance);
                },
                onYearChange: function(selectedDates, dateStr, instance) {
                    formatHeaderBuddhistYear(instance);
                },
                onOpen: function(selectedDates, dateStr, instance) {
                    formatHeaderBuddhistYear(instance);
                }
            });
        }

        initThaiDatepickers();

        function initThaiYearPickers() {
            let activeYearPopup = null;

            function closeYearPopup() {
                if (activeYearPopup) {
                    activeYearPopup.remove();
                    activeYearPopup = null;
                }
            }

            document.addEventListener('click', function(e) {
                const target = e.target;
                if (target && target.classList && target.classList.contains('yearpicker-th')) {
                    e.stopPropagation();
                    closeYearPopup();

                    const inputEl = target;
                    const currentVal = inputEl.value ? parseInt(inputEl.value, 10) : null;

                    const popup = document.createElement('div');
                    popup.className = 'yearpicker-popup';
                    
                    const rect = inputEl.getBoundingClientRect();
                    popup.style.top = (rect.bottom + window.scrollY + 4) + 'px';
                    
                    let leftPos = rect.left + window.scrollX - 75;
                    if (leftPos < 10) leftPos = 10;
                    if (leftPos + 250 > window.innerWidth) {
                        leftPos = window.innerWidth - 260;
                    }
                    popup.style.left = leftPos + 'px';

                    const header = document.createElement('div');
                    header.className = 'flatpickr-year-grid-header';
                    header.innerHTML = '<span>เลือกปี (พ.ศ.)</span><span class="flatpickr-year-grid-close">&times;</span>';
                    header.querySelector('.flatpickr-year-grid-close').addEventListener('click', function(ev) {
                        ev.stopPropagation();
                        closeYearPopup();
                    });

                    const body = document.createElement('div');
                    body.className = 'flatpickr-year-grid-body';

                    const currentBEYear = new Date().getFullYear() + 543;
                    const maxYear = currentBEYear + 5;
                    const minYear = currentBEYear - 80;

                    let activeItem = null;
                    for (let y = maxYear; y >= minYear; y--) {
                        const item = document.createElement('div');
                        item.className = 'flatpickr-year-item' + (y === currentVal ? ' active' : '');
                        item.textContent = y;

                        if (y === currentVal) {
                            activeItem = item;
                        }

                        item.addEventListener('click', function(ev) {
                            ev.stopPropagation();
                            inputEl.value = y;
                            inputEl.dispatchEvent(new Event('input', { bubbles: true }));
                            inputEl.dispatchEvent(new Event('change', { bubbles: true }));
                            closeYearPopup();
                        });

                        body.appendChild(item);
                    }

                    popup.appendChild(header);
                    popup.appendChild(body);
                    document.body.appendChild(popup);
                    activeYearPopup = popup;

                    if (activeItem) {
                        setTimeout(function() {
                            activeItem.scrollIntoView({ block: 'center', behavior: 'smooth' });
                        }, 50);
                    }
                    return;
                }

                if (activeYearPopup && activeYearPopup.contains(e.target)) {
                    return;
                }

                closeYearPopup();
            });
        }

        initThaiYearPickers();

        // Also re-init if Alpine adds new rows (e.g. experience)
        window.initThaiDatepickers = initThaiDatepickers;
    });
    </script>
    @endpush
@endsection