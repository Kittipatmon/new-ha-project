<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>แจ้งผลการพิจารณารับเข้าทำงาน</title>
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
            border: 1px solid #e5e7eb;
        }

        .header {
            background-color: #ffbe6fff;
            padding: 30px 20px;
            text-align: center;
            color: #1f2937;
        }

        .header img {
            max-height: 60px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            font-size: 16px;
            margin-bottom: 25px;
            color: #1f2937;
            font-weight: 600;
        }

        .message-body {
            color: #4b5563;
            margin-bottom: 30px;
        }

        .highlight {
            background-color: #f6fbf4;
            border: 1px solid #d1eac6;
            border-left: 4px solid #4ade80;
            padding: 16px 20px;
            margin: 25px 0;
            border-radius: 6px;
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
            <img src="{{ $message->embed(public_path('images/logos/th-kumwell-logo.png')) }}" alt="Kumwell Logo"
                style="height: 40px;">
            <h1>แจ้งผลการพิจารณารับเข้าทำงาน</h1>
        </div>
        <div class="content">
            <p class="greeting">เรียน คุณ{{ $applicantName }}</p>

            <div class="message-body">
                <p>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) มีความยินดีที่จะแจ้งให้ท่านทราบว่า
                    ท่านผ่านการคัดเลือกเพื่อบรรจุเข้าเป็นพนักงานของบริษัทฯ ในตำแหน่งดังต่อไปนี้</p>

                <div class="highlight">
                    <p style="margin: 0; color: #166534;"><strong>ตำแหน่ง:</strong> <span
                            style="color: #166534; font-weight: 600;">{{ $positionName }}</span></p>
                </div>

                <p>ทางบริษัทฯ ขอแสดงความยินดีและต้อนรับท่านเข้าเป็นส่วนหนึ่งของครอบครัวคัมเวล ทั้งนี้
                    เจ้าหน้าที่ฝ่ายทรัพยากรบุคคลจะติดต่อกลับไปยังท่านอีกครั้ง
                    เพื่อแจ้งรายละเอียดเกี่ยวกับการนัดหมายวันเริ่มต้นทำงานตลอดจนเอกสารต่างๆ ที่ต้องจัดเตรียม</p>
                <p>หากท่านมีข้อสงสัยหรือต้องการสอบถามข้อมูลเพิ่มเติม สามารถติดต่อเราได้ตามช่องทางด้านล่าง</p>
            </div>

            <div class="signature">
                <p style="margin: 0; font-weight: 600; color: #1f2937;">ด้วยความเคารพอย่างสูง,</p>
                <p style="margin: 5px 0 0 0; color: #4b5563;">
                    <strong>ฝ่ายทรัพยากรบุคคล</strong><br>
                    บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)
                </p>
            </div>
        </div>
        <div class="footer">
            สงวนลิขสิทธิ์ &copy; {{ date('Y') + 543 }} คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)
        </div>
    </div>
</body>

</html>