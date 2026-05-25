<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $subject }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap');
        
        body {
            font-family: 'Sarabun', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f7f9;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }
        .header {
            background-color: #D71920;
            padding: 30px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .content {
            padding: 40px 30px;
            min-height: 200px;
        }
        .greeting {
            font-size: 16px;
            margin-bottom: 25px;
            color: #1f2937;
            font-weight: 600;
        }
        .message-body {
            white-space: pre-line;
            color: #4b5563;
            margin-bottom: 30px;
        }
        .signature {
            border-top: 1px solid #f3f4f6;
            padding-top: 25px;
            margin-top: 30px;
        }
        .footer {
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            background-color: #f9fafb;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/logos/th-kumwell-logo.png')) }}" alt="Kumwell Logo" style="height: 40px; margin-bottom: 15px;">
            <h1>KUMWELL CORPORATION</h1>
        </div>
        <div class="content">
            <p class="greeting">เรียน คุณ{{ $applicantName }}</p>
            
            <div class="message-body">
                {!! nl2br(e($content)) !!}
            </div>

            <div class="signature">
                <p style="margin: 0; font-weight: 600; color: #1f2937;">Best Regards,</p>
                <p style="margin: 5px 0 0 0; color: #4b5563;">
                    <strong>พีรสรณ์ หรั่งชั้น (บิ๊กเอ็ม)</strong><br>
                    Pirasorn Rangchan (Bigm)<br>
                    Sr.Recruitment Planning Officer<br>
                    Kumwell Corporation Public Company Limited
                </p>
            </div>
        </div>
        <div class="footer">
            สงวนลิขสิทธิ์ &copy; {{ date('Y') + 543 }} คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)
        </div>
    </div>
</body>
</html>
