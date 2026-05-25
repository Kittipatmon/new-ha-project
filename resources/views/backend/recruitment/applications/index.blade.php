@extends('layouts.sidebar')

@section('title', 'จัดการรายชื่อผู้สมัคร')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold dark:text-white text-gray-800">จัดการรายชื่อผู้สมัคร (Applicant Tracking)</h2>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6">
            <form id="filter_form" action="{{ route('backend.recruitment.applications.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">ค้นหา</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="ชื่อ-นามสกุล หรือ อีเมล..."
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-2.5 text-sm filter-input">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">สถานะ</label>
                    <select name="status"
                        class="w-full bg-gray-50 dark:bg-kumwell-dark border-none rounded-xl px-4 py-2.5 text-sm filter-input">
                        <option value="">ทั้งหมด</option>
                        <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New (สมัครใหม่)</option>
                        <option value="screening" {{ request('status') == 'screening' ? 'selected' : '' }}>Screening
                            (กำลังคัดกรอง)</option>
                        <option value="interview" {{ request('status') == 'interview' ? 'selected' : '' }}>Interview
                            (นัดสัมภาษณ์)</option>
                        <option value="offered" {{ request('status') == 'offered' ? 'selected' : '' }}>Offered
                            (เสนอรับเข้างาน)</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected (ไม่ผ่าน)
                        </option>
                    </select>
                </div>
                <div class="md:col-span-2 flex items-end gap-2">
                    @if(request()->anyFilled(['search', 'status', 'job_post_id']))
                        <a href="{{ route('backend.recruitment.applications.index') }}"
                            class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-6 py-2.5 rounded-xl hover:bg-gray-200 transition-all font-semibold text-sm">
                            ล้างค่า
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('filter_form');
                const inputs = form.querySelectorAll('.filter-input');
                const tableContainer = document.getElementById('dynamic_table_container');
                let timeout = null;

                const fetchResults = () => {
                    const formData = new FormData(form);
                    const params = new URLSearchParams(formData).toString();
                    const url = `${form.getAttribute('action')}?${params}`;

                    // Update URL display without reloading
                    window.history.pushState({}, '', url);

                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.text())
                        .then(html => {
                            tableContainer.innerHTML = html;
                            attachPaginationLinks();
                        })
                        .catch(error => console.error('Error:', error));
                };

                const attachPaginationLinks = () => {
                    const links = tableContainer.querySelectorAll('#pagination-links a');
                    links.forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            const url = link.getAttribute('href');

                            window.history.pushState({}, '', url);

                            fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                                .then(response => response.text())
                                .then(html => {
                                    tableContainer.innerHTML = html;
                                    attachPaginationLinks();
                                    // Scroll to top of table
                                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                });
                        });
                    });
                };

                inputs.forEach(input => {
                    input.addEventListener('input', () => {
                        clearTimeout(timeout);
                        timeout = setTimeout(fetchResults, 500); // Debounce for 500ms
                    });

                    input.addEventListener('change', fetchResults);
                });

                // Initial attachment
                attachPaginationLinks();
            });
        </script>

        <div id="dynamic_table_container"
            class="bg-white dark:bg-kumwell-card rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            @include('backend.recruitment.applications.table')
        </div>
    </div>
@endsection