@extends('layouts.sidebar')

@section('content')


<div class="max-w-xl bg-base-100 rounded-lg shadow-md p-8 mt-8">
	<h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">
        แก้ไขสายงาน
    </h2>
	<form action="{{ route('sections.update', $section->section_id) }}" method="POST" class="space-y-2">
		@csrf
		@method('PUT')
		<div>
			<label for="section_code" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">	รหัสสายงาน</label>
			<input type="text" id="section_code" name="section_code" value="{{ old('section_code', $section->section_code) }}" required class="input input-bordered w-full">
		</div>
		<div>
			<label for="section_name" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">ชื่อสายงาน</label>
			<input type="text" id="section_name" name="section_name" value="{{ old('section_name', $section->section_name) }}" required class="input input-bordered w-full">
		</div>
		<div>
			<label for="section_fullname" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">ชื่อเต็มสายงาน</label>
			<input type="text" id="section_fullname" name="section_fullname" value="{{ old('section_fullname', $section->section_fullname) }}" required class="input input-bordered w-full">
		</div>
		<div>
			<label for="section_status" class="block text-sm font-medium text-gray-700 dark:text-white mb-1">สถานะสายงาน</label>
			<select id="section_status" name="section_status" required class="select select-bordered w-full">
				<option value="active" {{ old('section_status', $section->section_status) == 'active' ? 'selected' : '' }}>Active</option>
				<option value="inactive" {{ old('section_status', $section->section_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
			</select>
		</div>
		<div>
			<label for="section_description" class="block text-sm font-medium text-gray-700      dark:text-white mb-1">รายละเอียดสายงาน</label>
			<textarea id="section_description" name="section_description" rows="2" class="textarea textarea-bordered w-full">{{ old('section_description', $section->section_description) }}</textarea>
		</div>
		<button type="submit" class="btn btn-success">
            บันทึกข้อมูล
        </button>
        <a href="{{ route('sections.index') }}" class="btn btn-error">
            ย้อนกลับ
        </a>
	</form>
</div>

@endsection