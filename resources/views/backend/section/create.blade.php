@extends('layouts.sidebar')

@section('content')
<div class="max-w-xl bg-base-100 rounded-lg shadow-md p-8 mt-8">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Create Section</h2>
    <form action="{{ route('sections.store') }}" method="POST" class="space-y-5">
        @csrf
        <div>
            <label for="section_code" class="block text-sm font-medium text-gray-700 mb-1">Section Code</label>
            <input type="text" id="section_code" name="section_code" required class="input input-bordered w-full">
        </div>
        <div>
            <label for="section_name" class="block text-sm font-medium text-gray-700 mb-1">Section Name</label>
            <input type="text" id="section_name" name="section_name" required class="input input-bordered w-full">
        </div>
        <div>
            <label for="section_fullname" class="block text-sm font-medium text-gray-700 mb-1">Section Full Name</label>
            <input type="text" id="section_fullname" name="section_fullname" required class="input input-bordered w-full">
        </div>
        <div>
            <label for="section_status" class="block text-sm font-medium text-gray-700 mb-1">Section Status</label>
            <select id="section_status" name="section_status" required class="select select-bordered w-full">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
        <div>
            <label for="section_description" class="block text-sm font-medium text-gray-700 mb-1">Section Description</label>
            <textarea id="section_description" name="section_description" rows="3" class="textarea textarea-bordered w-full"></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-full">Create Section</button>
    </form>
</div>
@endsection