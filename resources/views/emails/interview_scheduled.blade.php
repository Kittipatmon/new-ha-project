<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>เชิญเข้าร่วมสัมภาษณ์งาน - Kumwell</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap');
        
        body {
            font-family: 'Sarabun', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 650px;
            margin: 20px auto;
            background: #ffffff;
            border-top: 6px solid #F2704E;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            padding: 30px;
            text-align: center;
            background-color: #ffffff;
        }
        .header img {
            max-width: 180px;
        }
        .tagline {
            color: #F2704E;
            font-size: 14px;
            font-weight: 600;
            margin-top: 5px;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px;
            background-color: #ffffff;
        }
        .greeting {
            font-size: 18px;
            margin-bottom: 25px;
            color: #1f2937;
            font-weight: 600;
        }
        .main-text {
            text-indent: 40px;
            margin-bottom: 20px;
            text-align: justify;
        }
        .highlight-box {
            background-color: #fff5f2;
            border-left: 4px solid #F2704E;
            padding: 20px;
            margin: 25px 0;
        }
        .highlight-title {
            color: #F2704E;
            font-weight: 600;
            margin-bottom: 5px;
            font-size: 16px;
        }
        .test-link-box {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e1;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .footer {
            padding: 30px;
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
            color: #64748b;
        }
        .signature {
            margin-top: 30px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .welfare-section {
            font-size: 13px;
            color: #6b7280;
            font-style: italic;
            background: #fdfdfd;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #f0f0f0;
            margin-top: 30px;
        }
        .btn-maps {
            display: inline-block;
            padding: 10px 20px;
            background-color: #F2704E;
            color: white !important;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 10px;
        }
        a { color: #F2704E; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/logos/th-kumwell-logo.png')) }}" alt="Kumwell Logo">
            <div class="tagline">POWER OF INNOVATION</div>
        </div>
        
        <div class="content">
            <p class="greeting">เรียน คุณ{{ $interview->application->applicant->first_name }} {{ $interview->application->applicant->last_name }}</p>

            <p class="main-text">
                ตามที่ท่านได้สมัครงาน กับทาง Jobthai บริษัท คัมเวล คอร์ปอเรชั่น จำกัด ( มหาชน ) ซึ่งบริษัทเป็นผู้ผลิตและประกอบระบบสายล่อฟ้าและสายดินสำหรับอุปกรณ์ไฟฟ้า ( Grounding & Lightning Protection ) ที่ได้มาตรฐานระดับโลกภายใต้แบรนด์ Kumwell จำหน่ายทั้งไทยและต่างประเทศ โดยบริษัทเปิดดำเนินกิจการมานานกว่า 25 ปี ได้รับรางวัลอุตสาหกรรมดีเด่นด้านการบริหารอุตสาหกรรมขนาดกลางและขนาดย่อมประจำปี 2555 รางวัลผู้ส่งออกดีเด่น รางวัลความรับผิดชอบต่อสังคม (CSR-DIW) เว็บไซต์ <a href="http://www.kumwell.com">www.kumwell.com</a>
            </p>

            <p class="main-text">
                บริษัทฯ ได้พิจารณาแล้วเห็นว่าท่านมีคุณสมบัติเบื้องต้นตรงตามความต้องการของบริษัท ในตำแหน่ง <strong>“{{ $interview->application->jobPost->position_name }}”</strong> จึงขอนัดสัมภาษณ์งานใน
            </p>

            <div class="highlight-box">
                <div class="highlight-title">กำหนดการนัดสัมภาษณ์</div>
                วัน{{ \Carbon\Carbon::parse($interview->interview_date)->locale('th')->dayName }}ที่ {{ $interview->interview_date->format('d') }} {{ \Carbon\Carbon::parse($interview->interview_date)->locale('th')->monthName }} {{ $interview->interview_date->format('Y') + 543 }}<br>
                เวลา: <strong>{{ \Carbon\Carbon::parse($interview->interview_time)->format('H.i') }} น.</strong><br>
                ช่องทาง: <strong>{{ $interview->interview_type == 'online' ? 'Microsoft Team' : 'Walk-in Interview' }}</strong>
            </div>

            <p><strong>หากสะดวกในวันเวลาดังกล่าว รบกวนคอนเฟิร์มกลับมาทางอีเมลฉบับนี้ครับ</strong></p>

            <div class="test-link-box">
                <p style="margin-top: 0;"><strong>รบกวนทำแบบทดสอบบุคลิกภาพ 16 personalities เพื่อประกอบการสัมภาษณ์งานตามลิ้งค์แนบครับ</strong></p>
                <a href="https://shorturl.asia/3BcrT">https://shorturl.asia/3BcrT</a>
                <p style="margin-bottom: 0; font-size: 14px; color: #64748b;">* หลังจากทำแบบทดสอบเสร็จแล้ว รบกวนแคปเจอร์หน้าจอและส่งกลับทางอีเมลฉบับนี้ครับ</p>
            </div>

            <p>
                <strong>สถานที่สัมภาษณ์งาน :</strong> {{ $interview->location ?: 'สำนักงานใหญ่' }}<br>
                <a href="https://maps.app.goo.gl/Xsu7hgYL4moRC6Hf9" class="btn-maps">ดูแผนที่สำนักงานใหญ่</a>
            </p>

            <div style="margin-top: 30px;">
                <p><strong>สำหรับเอกสารใช้ประกอบในการสมัครงานมีดังนี้</strong></p>
                <ul style="padding-left: 20px;">
                    <li>สำเนาบัตรประชาชน 1 ใบ</li>
                    <li>สำเนาทะเบียนบ้าน 1 ใบ</li>
                    <li>วุฒิการศึกษา 1 ใบ</li>
                    <li>รูปถ่าย 1 ใบ</li>
                    <li>เอกสารอื่นๆ เช่น (สลิปเงินเดือนล่าสุด, Resume, ใบรับรองการผ่านงาน, ใบประกาศผ่านฝึกอบรม)</li>
                </ul>
                <p style="color: #F2704E;"><strong>** เอกสารการสมัครงาน รบกวนนำมาในวันสัมภาษณ์งานครับ **</strong></p>
            </div>

            <p>
                หากท่านมีข้อสงสัยประการใด สามารถสอบถามได้ทางโทรศัพท์หมายเลข <strong>02-954-3455 ต่อ 3245</strong> ติดต่อ <strong>คุณพีรสรณ์ หรั่งชั้น</strong> จักขอบพระคุณยิ่ง
            </p>

            <p>
                จึงเรียนมาเพื่อขอเชิญท่านเข้าร่วมสัมภาษณ์งานในครั้งนี้และหวังว่าท่านจะได้มาเป็นบุคคลากรที่สำคัญอีก 1 ท่านของบริษัทและพร้อมที่จะเติบโตไปกับองค์กร
            </p>

            <div class="welfare-section">
                <strong>*** สวัสดิการ อาทิเช่น</strong> กองทุนสำรองเลี้ยงชีพ , กองทุนสวัสดิการกู้ยืม , ประกันอุบัติเหตุ , ตรวจสุขภาพก่อนเริ่มงาน , ตรวจสุขภาพประจำปี เงินช่วยเหลือกรณีสมรส , สวัสดิการของเยี่ยมคลอด , เงินช่วยเหลือกรณี บิดา มารดา และบุตร เสียชีวิต , ทุนการศึกษาบุตร ตามเงื่อนไข ท่องเที่ยวประจำปี , ปรับเงินประจำปี , โบนัส , ฯลฯ ***
            </div>

            <div class="signature">
                <p style="margin-bottom: 5px;">Best Regards,</p>
                <p style="margin: 0; font-weight: 600;">Kumwell</p>
                <p style="margin: 0;"><strong>พีรสรณ์ หรั่งชั้น (บิ๊กเอ็ม)</strong></p>
                <p style="margin: 0; color: #64748b; font-size: 14px;">Pirasorn Rangchan (Bigm)</p>
                <p style="margin: 0; color: #64748b; font-size: 14px;">Sr.Recruitment Planning Officer</p>
                <p style="margin: 5px 0 0 0; font-size: 14px;">Phone: +662 954 3455 #3245</p>
                <p style="margin: 0; font-size: 14px;">Web: <a href="http://www.kumwell.com">www.kumwell.com</a></p>
                <p style="margin: 0; font-size: 14px;">Email: <a href="mailto:Pirasorn.Ra@kumwell.com">Pirasorn.Ra@kumwell.com</a></p>
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') + 543 }} Kumwell Corporation Public Company Limited. All rights reserved.
        </div>
    </div>
</body>
</html>
