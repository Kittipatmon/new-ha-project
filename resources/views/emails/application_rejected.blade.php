<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>แจ้งผลการพิจารณาใบสมัครงาน</title>
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
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            border-left: 4px solid #ef4444;
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
            <h1>แจ้งผลการพิจารณาใบสมัครงาน</h1>
        </div>
        <div class="content">
            <p class="greeting">เรียน คุณ{{ $applicantName }}</p>

            <div class="message-body">
                <p>อ้างถึงการสมัครงานของท่าน ในตำแหน่ง:</p>

                <div class="highlight">
                    <p style="margin: 0;"><strong>ตำแหน่ง:</strong> <span
                            style="color: #ff0000;">{{ $positionName }}</span></p>
                </div>

                <p>บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)
                    ขอขอบพระคุณเป็นอย่างยิ่งที่ท่านให้ความสนใจและสละเวลาในการเข้าร่วมขั้นตอนการคัดเลือกบุคลากรของบริษัทฯ
                </p>
                <p>ทางบริษัทฯ ได้พิจารณาคุณสมบัติและประสบการณ์ของท่านอย่างรอบคอบ แต่ต้องขออภัยที่ต้องแจ้งให้ทราบว่า
                    ในขณะนี้บริษัทฯ ยังไม่ผ่านการพิจารณาเข้าทำงานในตำแหน่งดังกล่าว
                    เนื่องจากมีผู้สมัครท่านอื่นที่มีคุณสมบัติตรงตามที่บริษัทพิจารณาไว้ในขณะนี้</p>
                <p>ทั้งนี้ ทางบริษัทฯ ขอเก็บประวัติของท่านไว้ในระบบ
                    หากมีตำแหน่งงานอื่นที่เหมาะสมกับคุณสมบัติของท่านในอนาคต ทางบริษัทฯ
                    จะติดต่อท่านเพื่อเชิญมาร่วมงานกับเราต่อไป</p>
                <p>ขอขอบพระคุณอีกครั้งที่ท่านให้ความสนใจร่วมงานกับบริษัทฯ
                    และขออวยพรให้ท่านประสบความสำเร็จในหน้าที่การงานต่อไป</p>
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