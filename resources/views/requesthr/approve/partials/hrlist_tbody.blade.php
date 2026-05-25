@foreach ($hrrequests as $request)
<tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
    <td class="text-sm text-gray-700 dark:text-gray-300">{{ $request->request_code }}</td>
    <td class="text-sm text-gray-700 dark:text-gray-300">{{ $request->user->employee_code }}</td>
    <td class="text-sm text-gray-700 dark:text-gray-300">{{ $request->user->fullname }}</td>
    <td class="text-sm text-gray-700 dark:text-gray-300">{{ $request->category->name_th ?? '-' }}</td>
    <td class="text-sm text-gray-700 dark:text-gray-300">{{ $request->type->name_th ?? '-' }}</td>
    <td class="text-sm text-gray-700 dark:text-gray-300">{{ $request->subtype->name_th ?? '-' }}</td>
    <td class="text-sm text-gray-700 dark:text-gray-300">{{ $request->created_at ? $request->created_at->format('d/m/Y') : '-' }}</td>
    <td class="text-sm">
        <span class="px-2 py-1 rounded-full badge {{ $request->status_color }}">
            {{ $request->status_label }}
        </span>
    </td>
    <td class="text-sm text-gray-700 dark:text-gray-300">
        <a href="{{ route('requesthr.detailhr', $request->hr_request_id ) }}" class="btn btn-info btn-xs text-white hover:underline">
            รายละเอียด
        </a>
    </td>
</tr>
@endforeach
@if($hrrequests->isEmpty())
<tr>
    <td colspan="9" class="px-4 py-3 text-center text-gray-500 dark:text-gray-300">
        ไม่มีคำร้องรอดำเนินการ
    </td>
</tr>
@endif