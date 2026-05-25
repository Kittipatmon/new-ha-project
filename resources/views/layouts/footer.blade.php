<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>

<!-- <style>
    @import url('https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap');
    body { font-family: 'Prompt', sans-serif; }
</style> -->

<footer class=" text-[#333333] border-t border-gray-100/20 ">
    <div class="max-w-[1920px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-4 flex flex-col items-start">
                <div class="flex items-center gap-2 mb-1">
                    <div class="w-10 h-10 bg-[#D71920] rounded-lg flex items-center justify-center shadow-sm">
                        <span class="text-white font-bold text-xl">H</span>
                    </div>
                    
                    <div class="flex flex-col leading-none">
                        <span class="text-2xl font-bold text-[#D71920]">Kumwell</span>
                        <span class="text-[0.6rem] font-bold text-[#D71920] tracking-widest uppercase">Human Asset</span>
                    </div>
                    <span class="self-start text-[0.6rem] text-[#D71920] font-bold mt-1 ml-1">HA</span>
                </div>

                <p class="text-sm text-gray-600 font-medium mb-4 pl-1 dark:text-white">ฝ่ายทรัพยากรมนุษย์</p>

                <div class="flex space-x-3 pl-1">
                    <a href="#" class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-blue-600 hover:bg-gray-50 transition">
                        <i class="fab fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#" class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-pink-600 hover:bg-gray-50 transition">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="#" class="w-8 h-8 bg-white rounded-full shadow flex items-center justify-center text-[#D71920] hover:bg-gray-50 transition">
                        <i class="fas fa-globe text-sm"></i>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-3 lg:pl-8">
                <h3 class="text-[#D71920] font-bold text-xs uppercase tracking-wider mb-4">Quick Links</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="{{ route('welcome') }}" class="hover:text-[#D71920] transition-colors dark:text-white">หน้าหลัก</a></li>
                    <li><a href="#" class="hover:text-[#D71920] transition-colors dark:text-white">ข่าวสาร/ประชาสัมพันธ์</a></li>
                    <li><a href="#" class="hover:text-[#D71920] transition-colors dark:text-white">HA Huide Book</a></li>
                    <li><a href="#" class="hover:text-[#D71920] transition-colors dark:text-white">Calendar</a></li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h3 class="text-[#D71920] font-bold text-xs uppercase tracking-wider mb-4">Support</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="#" class="hover:text-[#D71920] transition-colors dark:text-white">ติดต่อเรา</a></li>
                    <li><a href="#" class="hover:text-[#D71920] transition-colors dark:text-white" onclick="Swal.fire('Info', 'Coming Soon', 'info'); return false;">ข้อเสนอแนะ</a></li>
                    <li><a href="#" class="hover:text-[#D71920] transition-colors dark:text-white">นโยบาย</a></li>
                </ul>
            </div>

             <div class="lg:col-span-2">
                <h3 class="text-[#D71920] font-bold text-xs uppercase tracking-wider mb-4">เกี่ยวกับ</h3>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><a href="#" class="hover:text-[#D71920] transition-colors dark:text-white">เว็บไซต์บริษัท</a></li>
                    <li><a href="#" class="hover:text-[#D71920] transition-colors dark:text-white">HAMS Portal</a></li>
                </ul>
            </div>
        </div>

        <hr class="border-gray-200/20 my-6">

        <div class="flex flex-col md:flex-row justify-between items-center text-xs text-gray-600 gap-4">
            <div class="font-medium">
                © 2025 Kumwell Corporation PCL. All rights reserved.
            </div>

            <div class="flex flex-wrap items-center gap-4 justify-center md:justify-end">
                <span class="hidden md:inline dark:text-white">Developed by ICT Kumwell</span>
                <span class="hidden md:inline text-gray-300 dark:text-white">|</span>
                <span class="hidden md:inline dark:text-white"><i class="fas fa-globe mr-1"></i>Version 0.1</span>
                <span class="text-gray-300 dark:text-white">|</span>
                <span class="text-gray-300 dark:text-white"><i class="fas fa-phone-alt mr-1"></i> Tel: 3544</span>
                
                <div class="h-4 w-px bg-red-200 mx-2"></div>

                <div class="flex items-center gap-3 text-[#003366] dark:text-white">
                    <div class="flex items-center gap-1">
                        <i class="fas fa-globe"></i> 0
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="far fa-calendar-alt"></i> 0
                    </div>
                    <div class="flex items-center gap-1">
                        <i class="fas fa-users"></i> 0
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>