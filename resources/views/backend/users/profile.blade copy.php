@extends('layouts.app')

@section('content')
<!-- พื้นหลังเข้มทั้งหน้า -->
<div>
  <div class="max-w-4xl mx-auto px-8 py-8 rounded-xl background-blur-2xl shadow-lg shadow-black/50">

    <!-- แถวบน: การ์ดโปรไฟล์ใหญ่ + การ์ดวันที่เริ่มงาน + ปุ่มด้านขวาบน -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 relative">

      <!-- ปุ่มแก้ไข/ตั้งค่า ด้านขวาบน เหมือนภาพ -->
      <div class="hidden lg:block absolute right-0 -top-2">
        <div class="flex gap-3">
          <!-- <a href=""
            class="btn btn-sm rounded-full bg-[#FFC21A] text-black border-0 shadow hover:brightness-95">แก้ไข</a>
          <details class="dropdown dropdown-end">
            <summary class="btn btn-sm rounded-full bg-[#ff445a] border-0 text-white shadow hover:brightness-95">ตั้งค่า</summary>
            <ul class="menu dropdown-content z-[1] p-2 shadow rounded-xl bg-base-100 text-base-content w-52 mt-2">
              <li><a href="">เปลี่ยนรหัสผ่าน</a></li>
              <li><a href="">ออกจากระบบ</a></li>
            </ul>
          </details> -->
        </div>
      </div>

      <!-- การ์ดโปรไฟล์ใหญ่ (เหมือนภาพซ้าย) -->
      <section class="lg:col-span-7 rounded-2xl shadow-[0_10px_30px_rgba(0,0,0,0.4)] overflow-hidden border border-white/5">
        <!-- แถบคัฟเวอร์สีแดง -->
        <div class="h-[120px] w-full" style="background:linear-gradient(135deg,#B02525 0%,#B02525 100%);"></div>
        <!-- <div class="h-[120px] w-full" style="background:#b0181a;"></div> -->

        <div class="p-6 pt-0 ">
          <div class="-mt-14 flex items-start gap-5">
            <!-- อวตารมีกรอบขาว -->
            <div class="relative shrink-0">
              <div class="avatar">
                <div class="w-28 h-28 rounded-full ring-4 ring-white/95 shadow-lg overflow-hidden">
                  @php $avatar = $user->avatar_path ?? null; @endphp
                  @if($avatar)
                  <img src="{{ asset('storage/'.$avatar) }}" alt="avatar" class="w-full h-full object-cover">
                  @else
                  -
                  @endif
                </div>
              </div>

              <!-- ปุ่มกล้องเล็กมุมขวาล่าง -->
              @php $inputId = 'avatarUpload-'.($user->id ?? 'me'); @endphp
              <label for="{{ $inputId }}"
                class="absolute -right-1 -bottom-1 grid place-items-center w-7 h-7 rounded-full bg-slate-700 text-white border border-white/30 shadow cursor-pointer hover:bg-slate-600">
                <i class="fa-solid fa-camera text-[12px]"></i>
                <input id="{{ $inputId }}" type="file" accept="image/*" class="hidden">
              </label>
            </div>

            <div class="flex-1">
              <h2 class="text-2xl font-semibold tracking-tight leading-snug">
                {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
              </h2>

              <!-- แถวตำแหน่ง + แบ็จ -->
              <div class="mt-2 flex flex-wrap items-center gap-2 text-[13px]">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/5  border border-white/10">
                  <i class="fa-solid fa-graduation-cap"></i>
                  {{ $user->position ?? 'วิศวกรซอฟต์แวร์' }}
                </div>
                <span class="px-3 py-1 rounded-full bg-white/10  border border-white/10">
                  {{ $user->usertype->description ?? '' }}
                </span>
                <span class="px-3 py-1 rounded-full bg-green-600/90 text-white">ใช้งาน</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- การ์ดวันที่เริ่มทำงาน (เหมือนภาพขวาบน) -->
      <section class="lg:col-span-5 rounded-2xl border border-white/5 shadow-[0_10px_30px_rgba(0,0,0,0.4)]">
        <div class="p-6">
          <h3 class=" text-[15px] mb-3">อายุงาน:
            <span class="text-sm text-red-600">
              @php
              $start = \Carbon\Carbon::parse($user->startwork_date ?? \Carbon\Carbon::now());
              $now = \Carbon\Carbon::now();
              $diff = $start->diff($now);
              $months = ($diff->y * 12) + $diff->m;
              $days = $diff->d;
              @endphp
              ({{ $months }} เดือน {{ $days }} วัน)
            </span>
          </h3>
          <div class="flex items-start gap-3">
            <span class="mt-1 inline-block h-3 w-3 rounded-full bg-[#1DA6F0]"></span>
            <div>
              <div class="text-[13px]">
                {{ \Carbon\Carbon::parse($user->startwork_date)->format('d/m/Y') ?? '01/01/2020' }}
              </div>
              <div class="text-[13px]">วันที่เริ่มทำงานของพนักงาน</div>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <span class="mt-1 inline-block h-3 w-3 rounded-full bg-[#b0181a]"></span>
            <div>
              <div class="text-[13px]">
                <!-- {{ \Carbon\Carbon::parse($user->endwork_date)->format('d/m/Y') ?? '' }} -->
                {{ ($user->endwork_date) ?? '-' }}
              </div>
              <div class="text-[13px]">วันที่สิ้นสุดทำงานของพนักงาน</div>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- เส้นคั่นบางแบบในภาพ -->
    <div class="my-6 border-t border-white/10"></div>

    <!-- Tabs (ข้อมูลทั่วไป / ประวัติการทำงาน) -->
    <div class="mb-3">
      <div class="flex gap-6 text-sm">
        <button class="relative after:absolute after:left-0 after:-bottom-[1px] after:h-[2px] after:w-full after:bg-white">
          ข้อมูลทั่วไป
        </button>
        <button class="relative after:absolute after:left-0 after:-bottom-[1px] after:h-[2px] after:w-full after:bg-white">ประวัติการทำงาน</button>
      </div>
    </div>

    <!-- เนื้อหาภายใต้แท็บ: สองคอลัมน์เหมือนภาพ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- การ์ดซ้าย: ข้อมูลพื้นฐาน -->
      <section class="rounded-2xl border border-white/5 p-6 shadow-[0_10px_30px_rgba(0,0,0,0.4)]">
        <h4 class="font-semibold mb-4">ข้อมูลพื้นฐาน</h4>
        <div class="grid grid-cols-2 gap-y-3 text-[13px]">
          <div>รหัสพนักงาน</div>
          <div>{{ $user->employee_code ?? '11648' }}</div>

          <div>เพศ</div>
          <div>{{ $user->sex ?? 'ชาย' }}</div>

          <div>ประเภทพนักงาน</div>
          <div>{{ $user->employee_type ?? 'รายเดือน' }}</div>

          <div>ที่ทำงาน</div>
          <div>{{ $user->workplace ?? 'สมุทรปราการ' }}</div>
        </div>
      </section>

      <!-- การ์ดขวา: หน่วยงาน -->
      <section class="rounded-2xl border border-white/5 p-6 shadow-[0_10px_30px_rgba(0,0,0,0.4)]">
        <h4 class="font-semibold mb-4">หน่วยงาน</h4>
        <div class="grid grid-cols-2 gap-y-3 text-[13px]">
          <div>สายงาน (Section)</div>
          <div>{{ $user->section->section_code }}</div>

          <div>ฝ่าย (Division)</div>
          <div>{{ optional($user->division)->division_name ?? 'ICT' }}</div>

          <div>แผนก (Department)</div>
          <div>{{ optional($user->department)->department_name ?? 'ICT' }}</div>

          <div>HR Level</div>
          <div>{{ $user->hr_level ?? 'ไม่มี' }}</div>
        </div>
      </section>

    </div>
  </div>
</div>
@endsection