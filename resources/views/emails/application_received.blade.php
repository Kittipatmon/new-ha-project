<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>ยืนยันการรับสมัครงาน</title>
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
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #D71920;
            padding: 30px 20px;
            text-align: center;
            color: white;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }

        .content {
            padding: 30px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 20px;
            color: #1f2937;
        }

        .job-title {
            font-weight: 600;
            color: #4f46e5;
        }

        .info-card {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }

        .footer {
            padding: 20px;
            text-align: center;
            font-size: 14px;
            color: #9ca3af;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/logos/th-kumwell-logo.png')) }}" alt="Kumwell Logo"
                style="height: 40px; margin-bottom: 15px;">
            <h1>ยืนยันการรับสมัครงาน</h1>
        </div>
        <div class="content">
            <p class="greeting">เรียน คุณ{{ $application->applicant->first_name }}
                {{ $application->applicant->last_name }},
            </p>

            <p>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ได้รับใบสมัครของคุณในตำแหน่ง <span
                    class="job-title">{{ $application->jobPost->position_name }}</span> เรียบร้อยแล้ว</p>

            <div class="info-card">
                <p style="margin: 0;"><strong>เลขที่ใบสมัคร:</strong> {{ $application->application_no }}</p>
                <p style="margin: 5px 0 0 0;"><strong>วันที่สมัคร:</strong>
                    {{ $application->applied_at->format('d/m/Y H:i') }} น.</p>
            </div>

            <p>ขณะนี้ฝ่ายทรัพยากรบุคคลกำลังดำเนินการพิจารณาคุณสมบัติของท่าน
                หากคุณสมบัติของท่านตรงตามความต้องการของทางบริษัท
                เจ้าหน้าที่จะติดต่อกลับเพื่อขอนัดหมายสัมภาษณ์ในลำดับถัดไป</p>

            <p>ขอขอบคุณที่ให้ความสนใจร่วมงานกับครอบครัว Kumwell</p>

            <p>ขอแสดงความนับถือ,<br>
                <strong>ฝ่ายทรัพยากรบุคคล</strong><br>
                คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') + 543 }} Kumwell Corporation Public Company Limited.
        </div>
    </div>
</body>

</html>