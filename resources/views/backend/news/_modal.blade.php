<div id="newsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50 transition-opacity">
    <div class="modal-content bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-xl mx-4 overflow-hidden">
        
        <div class="modal-header flex justify-between items-center p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <h5 class="modal-title text-lg font-semibold text-gray-800 dark:text-white" id="modalTitle">
                จัดการข่าวสาร
            </h5>
            <button type="button" class="close-modal text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                <i class="fa-solid fa-times fa-lg"></i>
            </button>
        </div>

        <form id="newsForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="news_id" id="news_id">
            
            <div class="modal-body p-6 max-h-[80vh] overflow-y-auto"> <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">หัวข้อข่าวสาร <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>
                    <div>
                        <label for="published_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">วันที่ประกาศ</label>
                        <input type="date" name="published_date" id="published_date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เนื้อหา</label>
                    <textarea name="content" id="content" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">รูปภาพ (เลือกได้หลายไฟล์)</label>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 dark:file:bg-gray-600 dark:file:text-gray-200">
                        <div id="images_preview" class="mt-2 grid grid-cols-3 gap-2"></div>
                    </div>
                    
                    <div>
                        <label for="file_news" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">แนบไฟล์ (เลือกได้หลายไฟล์)</label>
                        <input type="file" name="file_news[]" id="file_news" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-gray-600 dark:file:text-gray-200">
                        <div id="file_news_preview" class="mt-2 space-y-1 text-xs"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="link_news" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ลิงก์ข่าวสาร (ถ้ามี)</label>
                    <input type="url" name="link_news" id="link_news" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="https://example.com">
                </div>

                <div class="flex items-center mt-2">
                    <input type="checkbox" name="is_active" id="is_active" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" value="1" checked>
                    <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300 cursor-pointer">เผยแพร่ทันที</label>
                </div>
            </div>

            <div class="modal-footer flex justify-end p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <button type="button" class="btn btn-secondary close-modal mr-2 px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">ยกเลิก</button>
                <button type="submit" id="saveBtn" class="btn btn-success bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center shadow-md transition-all">
                    <svg id="btn-spinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>บันทึกข้อมูล</span>
                </button>
            </div>
        </form>
    </div>
</div>