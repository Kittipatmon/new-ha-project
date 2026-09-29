@extends('emails.layouts.master', [
    'themeColor' => '#2563eb',
    'titleColor' => '#1e40af',
    'emailTitle' => $subject
])

@section('subject', $subject)

@section('content')
    @if(!empty($applicantName))
        <p class="greeting">เรียน คุณ{{ $applicantName }}</p>
    @endif

    <div class="message-body">
        {!! nl2br(e($content)) !!}
    </div>
@endsection

@section('signature')
    <div class="signature">
        <p style="margin: 0; font-weight: 600; color: #1f2937;">ด้วยความเคารพอย่างสูง,</p>
        <p style="margin: 5px 0 0 0; color: #4b5563;">
            <strong>{{ $senderName ?? 'ฝ่ายทรัพยากรบุคคล' }}</strong><br>
            <span style="font-size: 13px; color: #6b7280;">{{ $senderPosition ?? 'เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล' }}</span><br>
            บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)<br>
            @if(!empty($senderEmail))
                <span style="font-size: 13px; color: #6b7280;">อีเมล: <a href="mailto:{{ $senderEmail }}" style="color: #2563eb; text-decoration: none;">{{ $senderEmail }}</a> | โทร: 02-954-3455</span>
            @endif
        </p>
    </div>
@endsection
