@extends('layouts.recruitment.app')

@section('content')
    <div class="min-h-screen bg-gray-50 dark:bg-slate-900 pb-20">
        <div class="max-w-4xl mx-auto pt-12 px-6">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('recruitment.show', $post->slug) }}"
                    class="text-gray-400 hover:text-kumwell-red transition-colors">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">ใบสมัครงาน (Application Form)</h1>
                    <p class="text-sm text-gray-500">ตำแหน่ง: {{ $post->position_name }}</p>
                </div>
            </div>

            @php
                $initialStep = 1;
                if ($errors->hasAny(['resume', 'photo', 'portfolio', 'pdpa_consent'])) {
                    $initialStep = 3;
                } elseif ($errors->hasAny(['education', 'education.*', 'experience', 'experience.*'])) {
                    $initialStep = 2;
                }
            @endphp

            @if ($errors->any())
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'พบข้อผิดพลาดในการกรอกข้อมูล',
                            html: `<ul class="text-left text-sm" style="list-style-type: disc; padding-left: 20px;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>`,
                            confirmButtonText: 'ตกลง',
                            confirmButtonColor: '#F2704E'
                        });
                    });
                </script>
            @endif

            <div x-data="{ 
                                    step: {{ $initialStep }},
                                    hasExperience: false,
                                    education: [{ level: '', institution_name: '', faculty: '', major: '', start_year: '', end_year: '', gpa: '' }],
                                    experience: [{ company_name: '', position: '', start_date: '', end_date: '', job_detail: '', salary: '', reason_for_leaving: '' }],
                                    submitting: false,
                                    resumeName: '',
                                    photoName: '',
                                    photoPreview: null,
                                    portfolioName: '',
                                    handleFileChange(event, type) {
                                        const file = event.target.files[0];
                                        if (!file) return;

                                        if (type === 'resume') this.resumeName = file.name;
                                        if (type === 'portfolio') this.portfolioName = file.name;
                                        if (type === 'photo') {
                                            this.photoName = file.name;
                                            const reader = new FileReader();
                                            reader.onload = (e) => { this.photoPreview = e.target.result; };
                                            reader.readAsDataURL(file);
                                        }
                                    },
                                    addEducation() { this.education.push({ level: '', institution_name: '', faculty: '', major: '', start_year: '', end_year: '', gpa: '' }) },
                                    removeEducation(index) { this.education.splice(index, 1) },
                                    addExperience() { this.experience.push({ company_name: '', position: '', start_date: '', end_date: '', job_detail: '', salary: '', reason_for_leaving: '' }) },
                                    removeExperience(index) { this.experience.splice(index, 1) },
                                    validateStep(s) {
                                        if (s === 1) {
                                            const step1Fields = ['prefix', 'first_name', 'last_name', 'gender', 'date_of_birth', 'phone', 'email'];
                                            for (const field of step1Fields) {
                                                const el = document.getElementsByName(field)[0];
                                                if (el && !el.value.trim()) {
                                                    Swal.fire({ icon: 'warning', title: 'ข้อมูลไม่ครบถ้วน', text: 'กรุณากรอกข้อมูลส่วนตัวที่จำเป็นให้ครบถ้วนก่อนส่ง', confirmButtonColor: '#F2704E' });
                                                    return false;
                                                }
                                            }
                                        }
                                        if (s === 2) {
                                            // Simple check for first education entry
                                            if (!this.education[0].level || !this.education[0].institution_name) {
                                                Swal.fire({ icon: 'warning', title: 'ข้อมูลการศึกษาไม่ครบถ้วน', text: 'กรุณากรอกระดับการศึกษาและชื่อสถาบันอย่างน้อย 1 รายการ', confirmButtonColor: '#F2704E' });
                                                return false;
                                            }
                                        }
                                        return true;
                                    },
                                    nextStep() {
                                        if (this.validateStep(this.step)) {
                                            this.step++;
                                            window.scrollTo({ top: 0, behavior: 'smooth' });
                                        }
                                    },
                                    confirmSubmit(e) {
                                        if (this.submitting) return;
                                        
                                        Swal.fire({
                                            title: 'ยืนยันการส่งใบสมัคร?',
                                            text: 'กรุณาตรวจสอบข้อมูลให้ถูกต้องก่อนกดยืนยัน',
                                            icon: 'question',
                                            showCancelButton: true,
                                            confirmButtonColor: '#F2704E',
                                            cancelButtonColor: '#9ca3af',
                                            confirmButtonText: 'ยืนยันและส่งใบสมัคร',
                                            cancelButtonText: 'ย้อนกลับไปตรวจสอบ'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                this.submitting = true;
                                                this.$el.submit();
                                            }
                                        });
                                    }
                                }">
                <form action="{{ route('recruitment.submit', $post->slug) }}" method="POST" enctype="multipart/form-data" 
                    @submit.prevent="confirmSubmit"
                    class="bg-white dark:bg-slate-800 rounded-3xl shadow-xl border border-gray-100 dark:border-slate-700 overflow-hidden">
                    @csrf

                    <!-- Form Stepper -->
                    <div
                        class="bg-gray-50 dark:bg-slate-900 px-8 py-6 border-b border-gray-100 dark:border-slate-800 flex justify-between items-center text-xs font-bold uppercase tracking-widest transition-all">
                        <div class="flex items-center gap-3 transition-colors"
                            :class="step >= 1 ? 'text-kumwell-red' : 'text-gray-400'">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs transition-colors"
                                :class="step >= 1 ? 'bg-kumwell-red text-white' : 'bg-gray-200 dark:bg-slate-800 text-gray-500'">1</span>
                            <span class="hidden md:block">ข้อมูลส่วนตัว</span>
                        </div>
                        <div class="h-px flex-grow mx-4 transition-colors"
                            :class="step >= 2 ? 'bg-kumwell-red' : 'bg-gray-200 dark:bg-slate-800'"></div>

                        <div class="flex items-center gap-3 transition-colors"
                            :class="step >= 2 ? 'text-kumwell-red' : 'text-gray-400'">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs transition-colors"
                                :class="step >= 2 ? 'bg-kumwell-red text-white' : 'bg-gray-200 dark:bg-slate-800 text-gray-500'">2</span>
                            <span class="hidden md:block">การศึกษา & ประวัติ</span>
                        </div>
                        <div class="h-px flex-grow mx-4 transition-colors"
                            :class="step >= 3 ? 'bg-kumwell-red' : 'bg-gray-200 dark:bg-slate-800'"></div>

                        <div class="flex items-center gap-3 transition-colors"
                            :class="step >= 3 ? 'text-v-red' : 'text-gray-400'">
                            <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs transition-colors"
                                :class="step >= 3 ? 'bg-kumwell-red text-white' : 'bg-gray-200 dark:bg-slate-800 text-gray-500'">3</span>
                            <span class="hidden md:block">อัปโหลด Resume</span>
                        </div>
                    </div>

                    <div class="p-8 md:p-10">
                        <!-- Step 1: Personal Info -->
                        <div x-show="step === 1" x-transition class="space-y-8">
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                                <i class="fa-solid fa-user-tag text-kumwell-red"></i>
                                ข้อมูลส่วนตัว (Personal Information)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
                                <div class="md:col-span-1 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">คำนำหน้า</label>
                                    <select name="prefix"
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                        <option value="">เลือก...</option>
                                        <option value="นาย">นาย</option>
                                        <option value="นาง">นาง</option>
                                        <option value="นางสาว">นางสาว</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">ชื่อ (First Name)</label>
                                    <input type="text" name="first_name" required
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                </div>
                                <div class="md:col-span-3 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">นามสกุล (Last Name)</label>
                                    <input type="text" name="last_name" required
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">เพศ</label>
                                    <div class="flex gap-4 p-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="gender" value="ชาย" class="text-kumwell-red">
                                            <span class="text-sm dark:text-gray-300">ชาย</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" name="gender" value="หญิง" class="text-kumwell-red">
                                            <span class="text-sm dark:text-gray-300">หญิง</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">วันเกิด</label>
                                    <input type="date" name="date_of_birth"
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                </div>
                                <div class="md:col-span-2 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">เลขบัตรประชาชน</label>
                                    <input type="text" name="national_id"
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                </div>

                                <div class="md:col-span-3 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">อีเมล (Email)</label>
                                    <input type="email" name="email" required
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                </div>
                                <div class="md:col-span-3 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">เบอร์โทรศัพท์ (Phone)</label>
                                    <input type="tel" name="phone" required
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                </div>

                                <div class="md:col-span-6 space-y-2">
                                    <label
                                        class="text-xs font-bold text-gray-500 uppercase">ที่อยู่ตามทะเบียนบ้าน/ที่อยู่ปัจจุบัน</label>
                                    <textarea name="address" rows="3"
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red"></textarea>
                                </div>
                                <div class="md:col-span-3 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">จังหวัด</label>
                                    <input type="text" name="province"
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                </div>
                                <div class="md:col-span-3 space-y-2">
                                    <label class="text-xs font-bold text-gray-500 uppercase">Line ID</label>
                                    <input type="text" name="line_id"
                                        class="w-full bg-gray-50 dark:bg-slate-900 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Education & Experience -->
                        <div x-show="step === 2" x-transition class="space-y-12">
                            <!-- Education -->
                            <div class="space-y-6">
                                <div
                                    class="flex justify-between items-end border-b border-gray-100 dark:border-slate-700 pb-2">
                                    <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                                        <i class="fa-solid fa-graduation-cap text-kumwell-red"></i>
                                        ประวัติการศึกษา (Education)
                                    </h3>
                                    <button type="button" @click="addEducation"
                                        class="text-xs font-bold text-kumwell-red hover:underline">
                                        + เพิ่มประวัติการศึกษา
                                    </button>
                                </div>

                                <template x-for="(edu, index) in education" :key="index">
                                    <div
                                        class="p-6 bg-gray-50 dark:bg-slate-900/50 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-6 relative">
                                        <button type="button" x-show="education.length > 1" @click="removeEducation(index)"
                                            class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[10px] font-bold text-gray-400 uppercase">ระดับการศึกษา</label>
                                                <select :name="'education['+index+'][level]'" required x-model="edu.level"
                                                    class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red">
                                                    <option value="">เลือก...</option>
                                                    <option value="มัธยมศึกษา">มัธยมศึกษา</option>
                                                    <option value="ปวช./ปวส.">ปวช./ปวส.</option>
                                                    <option value="ปริญญาตรี">ปริญญาตรี</option>
                                                    <option value="ปริญญาโท">ปริญญาโท</option>
                                                    <option value="ปริญญาเอก">ปริญญาเอก</option>
                                                </select>
                                            </div>
                                            <div class="md:col-span-2 space-y-2">
                                                <label
                                                    class="text-[10px] font-bold text-gray-400 uppercase">ชื่อสถาบัน</label>
                                                <input type="text" :name="'education['+index+'][institution_name]'" required x-model="edu.institution_name"
                                                    class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-bold text-gray-400 uppercase">เกรดเฉลี่ย
                                                    (GPA)</label>
                                                <input type="number" step="0.01" min="0" max="4.00" :name="'education['+index+'][gpa]'" x-model="edu.gpa"
                                                    class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red"
                                                    placeholder="0.00 - 4.00">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-bold text-gray-400 uppercase">คณะ</label>
                                                <input type="text" :name="'education['+index+'][faculty]'" x-model="edu.faculty"
                                                    class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red">
                                            </div>
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[10px] font-bold text-gray-400 uppercase">สาขาวิชา</label>
                                                <input type="text" :name="'education['+index+'][major]'" x-model="edu.major"
                                                    class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red">
                                            </div>
                                            <div class="space-y-2">
                                                <label
                                                    class="text-[10px] font-bold text-gray-400 uppercase">ปีที่เริ่ม</label>
                                                <input type="text" :name="'education['+index+'][start_year]'" x-model="edu.start_year"
                                                    class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red"
                                                    placeholder="เช่น 2560">
                                            </div>
                                            <div class="space-y-2">
                                                <label class="text-[10px] font-bold text-gray-400 uppercase">ปีที่จบ</label>
                                                <input type="text" :name="'education['+index+'][end_year]'" x-model="edu.end_year"
                                                    class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-kumwell-red"
                                                    placeholder="เช่น 2564">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Experience -->
                            <div class="space-y-6">
                                <div
                                    class="flex flex-col md:flex-row justify-between items-start md:items-end border-b border-gray-100 dark:border-slate-700 pb-2 gap-4">
                                    <div class="space-y-1">
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                                            <i class="fa-solid fa-briefcase text-kumwell-red"></i>
                                            ประวัติการทำงาน (Experience)
                                        </h3>
                                        <label class="flex items-center gap-2 cursor-pointer group">
                                            <input type="checkbox" x-model="hasExperience"
                                                class="rounded text-kumwell-red focus:ring-kumwell-red">
                                            <span
                                                class="text-sm text-gray-500 group-hover:text-kumwell-red transition-colors font-medium ml-6">ข้าพเจ้ามีประสบการณ์การทำงาน</span>
                                        </label>
                                    </div>
                                    <button type="button" x-show="hasExperience" @click="addExperience"
                                        class="text-xs font-bold text-kumwell-red hover:underline">
                                        + เพิ่มประวัติการทำงาน
                                    </button>
                                </div>

                                <div x-show="hasExperience" x-transition>
                                    <template x-for="(exp, index) in experience" :key="index">
                                        <div
                                            class="p-6 bg-gray-50 dark:bg-slate-900/50 rounded-2xl border border-gray-100 dark:border-slate-800 space-y-6 relative mb-6">
                                            <button type="button" x-show="experience.length > 1"
                                                @click="removeExperience(index)"
                                                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                <div class="md:col-span-2 space-y-2">
                                                    <label
                                                        class="text-[10px] font-bold text-gray-400 uppercase">ชื่อบริษัท</label>
                                                    <input type="text" :name="'experience['+index+'][company_name]'" x-model="exp.company_name"
                                                        :required="hasExperience" :disabled="!hasExperience"
                                                        class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                                </div>
                                                <div class="md:col-span-2 space-y-2">
                                                    <label
                                                        class="text-[10px] font-bold text-gray-400 uppercase">ตำแหน่ง</label>
                                                    <input type="text" :name="'experience['+index+'][position]'" x-model="exp.position"
                                                        :required="hasExperience" :disabled="!hasExperience"
                                                        class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                                </div>
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-[10px] font-bold text-gray-400 uppercase">วันที่เริ่มงาน</label>
                                                    <input type="date" :name="'experience['+index+'][start_date]'" x-model="exp.start_date"
                                                        :disabled="!hasExperience"
                                                        class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                                </div>
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-[10px] font-bold text-gray-400 uppercase">วันที่สิ้นสุด</label>
                                                    <input type="date" :name="'experience['+index+'][end_date]'" x-model="exp.end_date"
                                                        :disabled="!hasExperience"
                                                        class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                                </div>
                                                <div class="space-y-2">
                                                    <label
                                                        class="text-[10px] font-bold text-gray-400 uppercase">เงินเดือนสุดท้าย</label>
                                                    <input type="number" :name="'experience['+index+'][salary]'" x-model="exp.salary"
                                                        :disabled="!hasExperience"
                                                        class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                                </div>
                                                <div class="md:col-span-3 space-y-2">
                                                    <label
                                                        class="text-[10px] font-bold text-gray-400 uppercase">รายละเอียดงานโดยย่อ</label>
                                                    <input type="text" :name="'experience['+index+'][job_detail]'" x-model="exp.job_detail"
                                                        :disabled="!hasExperience"
                                                        class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                                </div>
                                                <div class="md:col-span-4 space-y-2">
                                                    <label
                                                        class="text-[10px] font-bold text-gray-400 uppercase">เหตุผลที่ลาออก</label>
                                                    <input type="text" :name="'experience['+index+'][reason_for_leaving]'" x-model="exp.reason_for_leaving"
                                                        :disabled="!hasExperience"
                                                        class="w-full bg-white dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-kumwell-red">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Files & PDPA -->
                        <div x-show="step === 3" x-transition class="space-y-10">
                            <h3
                                class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3 border-b border-gray-100 dark:border-slate-700 pb-2">
                                <i class="fa-solid fa-file-arrow-up text-kumwell-red"></i>
                                อัปโหลดเอกสาร (Documents)
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Resume Upload -->
                                <div class="space-y-4">
                                    <label class="text-sm font-bold text-gray-600 dark:text-gray-300">Resume / CV (PDF
                                        เท่านั้น) *</label>
                                    <div class="relative group cursor-pointer border-2 border-dashed rounded-2xl p-6 text-center transition-all"
                                        :class="resumeName ? 'border-green-500 bg-green-50 dark:bg-green-900/10' : 'border-gray-200 dark:border-slate-800 hover:border-kumwell-red'">
                                        <input type="file" name="resume" id="resume_input" accept=".pdf" required
                                            @change="handleFileChange($event, 'resume')"
                                            class="absolute inset-0 opacity-0 cursor-pointer">
                                        <div x-show="!resumeName">
                                            <i id="upload_icon"
                                                class="fa-solid fa-cloud-arrow-up text-3xl text-gray-300 group-hover:text-kumwell-red mb-2 block"></i>
                                            <p id="file_name" class="text-sm font-medium dark:text-gray-400">คลิกที่นี่เพื่ออัปโหลด Resume
                                            </p>
                                            <p id="file_hint" class="text-[10px] text-gray-400 mt-1 uppercase font-bold">ขนาดไฟล์ไม่เกิน 5MB (PDF format only)</p>
                                        </div>
                                        <div x-show="resumeName" class="flex flex-col items-center">
                                            <i class="fa-solid fa-circle-check text-3xl text-green-500 mb-2"></i>
                                            <p class="text-sm font-bold text-green-600 dark:text-green-400"
                                                x-text="resumeName"></p>
                                            <p class="text-[10px] text-green-500 mt-1 uppercase font-bold">เลือกไฟล์สำเร็จ
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Photo Upload -->
                                <div class="space-y-4">
                                    <label class="text-sm font-bold text-gray-600 dark:text-gray-300">รูปถ่ายหน้าตรง (JPG,
                                        PNG)</label>
                                    <div class="relative group cursor-pointer border-2 border-dashed rounded-2xl p-6 text-center transition-all min-h-[140px] flex flex-col items-center justify-center"
                                        :class="photoName ? 'border-green-500 bg-green-50 dark:bg-green-900/10' : 'border-gray-200 dark:border-slate-800 hover:border-kumwell-red'">
                                        <input type="file" name="photo" accept="image/*"
                                            @change="handleFileChange($event, 'photo')"
                                            class="absolute inset-0 opacity-0 cursor-pointer z-10">

                                        <template x-if="!photoPreview">
                                            <div>
                                                <i
                                                    class="fa-solid fa-camera text-3xl text-gray-300 group-hover:text-kumwell-red mb-2 block"></i>
                                                <p class="text-sm font-medium dark:text-gray-400">คลิกเพื่ออัปโหลดรูปถ่าย
                                                </p>
                                            </div>
                                        </template>

                                        <template x-if="photoPreview">
                                            <div class="flex flex-col items-center">
                                                <img :src="photoPreview"
                                                    class="w-20 h-20 object-cover rounded-lg border-2 border-white shadow-md mb-2">
                                                <p class="text-xs font-bold text-green-600 dark:text-green-400 truncate max-w-[150px]"
                                                    x-text="photoName"></p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Portfolio Upload -->
                                <div class="md:col-span-2 space-y-4">
                                    <label class="text-sm font-bold text-gray-600 dark:text-gray-300">Portfolio (PDF
                                        ถ้ามี)</label>
                                    <div class="relative group cursor-pointer border-2 border-dashed rounded-2xl p-6 text-center transition-all"
                                        :class="portfolioName ? 'border-green-500 bg-green-50 dark:bg-green-900/10' : 'border-gray-200 dark:border-slate-800 hover:border-kumwell-red'">
                                        <input type="file" name="portfolio" accept=".pdf"
                                            @change="handleFileChange($event, 'portfolio')"
                                            class="absolute inset-0 opacity-0 cursor-pointer">
                                        <div x-show="!portfolioName">
                                            <i
                                                class="fa-solid fa-id-card text-3xl text-gray-300 group-hover:text-kumwell-red mb-2 block"></i>
                                            <p class="text-sm font-medium dark:text-gray-400">คลิกเพื่ออัปโหลด Portfolio</p>
                                        </div>
                                        <div x-show="portfolioName" class="flex flex-col items-center">
                                            <i class="fa-solid fa-circle-check text-3xl text-green-500 mb-2"></i>
                                            <p class="text-sm font-bold text-green-600 dark:text-green-400"
                                                x-text="portfolioName"></p>
                                            <p class="text-[10px] text-green-500 mt-1 uppercase font-bold">เลือกไฟล์สำเร็จ
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PDPA -->
                            <div
                                class="bg-gray-50 dark:bg-slate-900/50 rounded-2xl p-8 border border-gray-100 dark:border-slate-800 space-y-4">
                                <h4 class="font-bold text-gray-800 dark:text-white flex items-center gap-2">
                                    <i class="fa-solid fa-shield-halved text-blue-500"></i>
                                    นโยบายคุ้มครองข้อมูลส่วนบุคคล (PDPA)
                                </h4>
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)
                                    มีความจำเป็นต้องเก็บรวบรวมและใช้ข้อมูลส่วนบุคคลของคุณเพื่อประโยชน์ในการพิจารณารับเข้าทำงาน
                                    โดยข้อมูลของคุณจะถูกเก็บรักษาไว้เป็นความลับตามมาตราฐานความปลอดภัยของบริษัท
                                </p>
                                <label class="flex items-start gap-3 cursor-pointer group">
                                    <input type="checkbox" id="pdpa_consent" name="pdpa_consent" value="1" required
                                        class="mt-1 rounded text-kumwell-red focus:ring-kumwell-red">
                                    <span
                                        class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-kumwell-red transition-colors">ฉันได้อ่านและยอมรับเงื่อนไขการคุ้มครองข้อมูลส่วนบุคคล</span>
                                </label>
                            </div>
                        </div>

                        <!-- Form Navigation -->
                        <div class="pt-10 flex justify-between border-t border-gray-50 dark:border-slate-700 mt-10">
                            <button type="button" x-show="step > 1" @click="step--"
                                class="px-8 py-3 rounded-xl bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-white font-bold hover:bg-gray-200 transition-all">
                                ย้อนกลับ
                            </button>
                            <div class="flex-grow"></div>
                            <button type="button" x-show="step < 3" @click="nextStep"
                                class="px-10 py-3 rounded-xl bg-kumwell-red text-white font-bold shadow-lg shadow-red-500/20 hover:bg-red-700 transition-all">
                                หน้าถัดไป <i class="fa-solid fa-chevron-right ml-2 text-xs"></i>
                            </button>
                            <button type="submit" x-show="step === 3" id="submit_btn" :disabled="submitting"
                                class="px-12 py-3 rounded-xl bg-kumwell-red text-white font-bold shadow-lg shadow-red-500/30 hover:bg-red-700 transition-all active:scale-95 flex items-center gap-2"
                                :class="submitting ? 'opacity-50 cursor-not-allowed' : ''">
                                <span x-show="!submitting">ส่งใบสมัคร</span>
                                <span x-show="submitting"><i class="fa-solid fa-spinner animate-spin"></i> กำลังบันทึก...</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('pdpa_consent');
            const submitBtn = document.getElementById('submit_btn');
            const fileInput = document.getElementById('resume_input');
            const fileNameDisplay = document.getElementById('file_name');
            const fileHint = document.getElementById('file_hint');
            const uploadIcon = document.getElementById('upload_icon');

            // PDPA Checkbox Logic
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.add('bg-kumwell-red', 'hover:bg-red-700', 'shadow-xl', 'shadow-red-500/30', 'active:scale-95');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                    submitBtn.classList.remove('bg-kumwell-red', 'hover:bg-red-700', 'shadow-xl', 'shadow-red-500/30', 'active:scale-95');
                }
            });

            // File Input Change Logic
            fileInput.addEventListener('change', function () {
                if (this.files && this.files.length > 0) {
                    const fileName = this.files[0].name;
                    fileNameDisplay.textContent = 'ไฟล์ที่เลือก: ' + fileName;
                    fileNameDisplay.classList.add('text-kumwell-red');
                    fileHint.textContent = 'ขนาดไฟล์: ' + (this.files[0].size / (1024 * 1024)).toFixed(2) + ' MB';
                    uploadIcon.innerHTML = '<i class="fa-solid fa-file-pdf"></i>';
                    uploadIcon.classList.remove('bg-red-50');
                    uploadIcon.classList.add('bg-green-50', 'text-green-600');
                } else {
                    fileNameDisplay.textContent = 'คลิกเพื่ออัปโหลด หรือลากไฟล์มาวางที่นี่';
                    fileNameDisplay.classList.remove('text-kumwell-red');
                    fileHint.textContent = 'ขนาดไฟล์ไม่เกิน 5MB (PDF format only)';
                    uploadIcon.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i>';
                    uploadIcon.classList.add('bg-red-50');
                    uploadIcon.classList.remove('bg-green-50', 'text-green-600');
                }
            });
        });
    </script>
@endsection