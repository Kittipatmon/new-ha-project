<?php

namespace App\Models\Recruitment;

use App\Models\User;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecruitmentMailTemplate extends Model
{
    use Auditable;

    public string $auditModule = 'recruitment';
    public string $auditModuleName = 'ระบบสรรหาบุคลากร (เทมเพลตอีเมล)';

    public function getAuditTitle(): string
    {
        return "เทมเพลตอีเมล: {$this->name}";
    }

    protected $table = 'recruitment_mail_templates';

    protected $fillable = [
        'key',
        'name',
        'category',
        'theme_color',
        'subject',
        'title',
        'badge_text',
        'greeting',
        'body_text',
        'notice_title',
        'notice_text',
        'closing_text',
        'header_logo_url',
        'header_tagline',
        'footer_salutation',
        'sender_name',
        'sender_position',
        'company_name',
        'contact_phone',
        'contact_website',
        'contact_email',
        'footer_copyright',
        'extra_data',
        'updated_by',
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Default definitions for all 9 recruitment email templates
     */
    public static function defaultTemplates(): array
    {
        return [
            'application_received' => [
                'name' => '1. ยืนยันการรับสมัครงาน (ส่งหาผู้สมัคร)',
                'category' => 'candidate',
                'theme_color' => '#0284c7',
                'subject' => 'ยืนยันการรับสมัครงาน - {position_name}',
                'title' => 'ยืนยันการรับสมัครงาน',
                'badge_text' => '✓ ได้รับข้อมูลใบสมัครเรียบร้อยแล้ว',
                'greeting' => 'เรียน คุณ{applicant_name}',
                'body_text' => "บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ได้รับใบสมัครงานของคุณในตำแหน่ง {position_name} เรียบร้อยแล้ว\n\nขณะนี้ฝ่ายทรัพยากรบุคคล (HA) กำลังดำเนินการตรวจสอบคุณสมบัติของผู้สมัคร หากประวัติและคุณสมบัติของท่านผ่านการพิจารณาเบื้องต้น เจ้าหน้าที่จะติดต่อกลับเพื่อประสานงานนัดหมายสัมภาษณ์งานในลำดับถัดไป",
                'notice_title' => '📌 คำแนะนำสำหรับผู้สมัคร:',
                'notice_text' => "• ท่านสามารถนำเลขที่ใบสมัคร {application_no} ตรวจสอบความคืบหน้าได้ตลอดเวลา\n• โปรดเตรียมความพร้อมในการรับสายโทรศัพท์หรือตรวจสอบอีเมลสำหรับการติดต่อจากฝ่ายทรัพยากรบุคคล",
                'closing_text' => 'ขอขอบพระคุณที่ให้ความสนใจร่วมงานกับครอบครัว Kumwell',
            ],
            'new_application_ha_notification' => [
                'name' => '2. แจ้งเตือนผู้สมัครใหม่ (ส่งหาฝ่ายบุคคล HA)',
                'category' => 'internal',
                'theme_color' => '#dc2626',
                'subject' => '[แจ้งเตือนผู้สมัครใหม่] ตำแหน่ง {position_name} - คุณ{applicant_name} ({application_no})',
                'title' => 'แจ้งเตือนผู้สมัครงานใหม่',
                'badge_text' => '🔔 ใบสมัครใหม่รอคัดกรอง',
                'greeting' => 'เรียน ฝ่ายทรัพยากรบุคคล (HA)',
                'body_text' => "มีผู้สมัครงานส่งใบสมัครเข้ามาในระบบเรียบร้อยแล้ว ขณะนี้สถานะอยู่ในขั้นตอน \"1. รอคัดกรองเบื้องต้น\" กรุณาเข้าสู่ระบบเพื่อตรวจสอบคุณสมบัติและประวัติผู้สมัคร",
                'notice_title' => 'ℹ️ หมายเหตุ:',
                'notice_text' => 'ระบบได้ส่งอีเมลยืนยันการรับสมัครงานไปยังผู้สมัครเรียบร้อยแล้วโดยอัตโนมัติ',
                'closing_text' => '',
            ],
            'new_candidate_dept_review' => [
                'name' => '3. แจ้งเตือนส่งต่อให้แผนกพิจารณา (ส่งหาหัวหน้าแผนก)',
                'category' => 'internal',
                'theme_color' => '#2563eb',
                'subject' => '[รอหัวหน้าแผนกพิจารณา] ตำแหน่ง {position_name} - คุณ{applicant_name} ({application_no})',
                'title' => 'แจ้งเตือนผู้สมัครงานรอพิจารณา',
                'badge_text' => '📋 รอหัวหน้าแผนกพิจารณา',
                'greeting' => 'เรียน {recipient_name}',
                'body_text' => "ฝ่ายทรัพยากรบุคคล (HA) ได้ทำการตรวจสอบคุณสมบัติเบื้องต้นของผู้สมัครงานเรียบร้อยแล้ว ขณะนี้สถานะอยู่ในขั้นตอน \"2. รอหัวหน้าแผนกพิจารณา\" กรุณาเข้าสู่ระบบเพื่อตรวจสอบคุณสมบัติและร่วมพิจารณา",
                'notice_title' => 'ℹ️ หมายเหตุ:',
                'notice_text' => 'ฝ่ายทรัพยากรบุคคล (HA) ได้ตรวจสอบคุณสมบัติเบื้องต้นแล้ว และส่งต่อให้หัวหน้าแผนกร่วมพิจารณาคุณสมบัติเพื่อเตรียมนัดสัมภาษณ์งานต่อไป',
                'closing_text' => '',
            ],
            'interview_scheduled' => [
                'name' => '4. เชิญเข้าร่วมสัมภาษณ์งาน (ส่งหาผู้สมัคร)',
                'category' => 'candidate',
                'theme_color' => '#ea580c',
                'subject' => 'เชิญเข้าร่วมสัมภาษณ์งาน ตำแหน่ง {position_name}',
                'title' => 'หนังสือเชิญเข้าร่วมสัมภาษณ์งาน',
                'badge_text' => '📅 กำหนดการสัมภาษณ์งาน (รอบที่ {interview_round})',
                'greeting' => 'เรียน คุณ{applicant_name}',
                'body_text' => "ตามที่ท่านได้สมัครงานกับ บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ทางบริษัทฯ ได้พิจารณาคุณสมบัติเบื้องต้นแล้วเห็นว่าตรงตามความต้องการของบริษัท ในตำแหน่ง \"{position_name}\" จึงขอเรียนเชิญท่านเข้าร่วมสัมภาษณ์งานตามกำหนดการดังต่อไปนี้\n\n⚠️ หากสะดวกในวันเวลาดังกล่าว รบกวนตอบกลับเพื่อคอนเฟิร์มการนัดหมายทางอีเมลฉบับนี้ครับ",
                'notice_title' => '📝 แบบทดสอบบุคลิกภาพ (16 Personalities):',
                'notice_text' => "รบกวนทำแบบทดสอบบุคลิกภาพ เพื่อประกอบการพิจารณาสัมภาษณ์งานตามลิ้งค์แนบ:\n👉 https://shorturl.asia/3BcrT\n* หลังจากทำแบบทดสอบเสร็จเรียบร้อยแล้ว รบกวนแคปเจอร์หน้าจอผลการทดสอบและตอบกลับทางอีเมลฉบับนี้",
                'closing_text' => 'จึงเรียนมาเพื่อขอเชิญท่านเข้าร่วมสัมภาษณ์งานในครั้งนี้ และหวังเป็นอย่างยิ่งว่าจะได้มีโอกาสร่วมงานกับท่าน',
            ],
            'interview_scheduled_department' => [
                'name' => '5. แจ้งกำหนดการสัมภาษณ์ (ส่งหากรรมการ/แผนก)',
                'category' => 'internal',
                'theme_color' => '#dc2626',
                'subject' => '[นัดสัมภาษณ์งาน] ตำแหน่ง {position_name} - คุณ{applicant_name} (รอบที่ {interview_round})',
                'title' => 'แจ้งกำหนดการสัมภาษณ์งาน',
                'badge_text' => '📋 รายละเอียดการนัดสัมภาษณ์ (รอบที่ {interview_round})',
                'greeting' => 'เรียน {recipient_name}',
                'body_text' => "ฝ่ายทรัพยากรบุคคล (HA) ได้ทำการกำหนดวันและเวลานัดสัมภาษณ์งาน สำหรับผู้สมัครในตำแหน่ง \"{position_name}\" เรียบร้อยแล้ว จึงขอเรียนแจ้งรายละเอียดเพื่อโปรดเข้าร่วมการสัมภาษณ์ตามกำหนดการดังต่อไปนี้",
                'notice_title' => '📌 ข้อความเพิ่มเติมจากฝ่ายทรัพยากรบุคคล:',
                'notice_text' => 'ท่านสามารถเข้าสู่ระบบ Kumwell HR เพื่อเปิดดูเรซูเม่และบันทึกคะแนนการประเมินการสัมภาษณ์',
                'closing_text' => '',
            ],
            'application_offered' => [
                'name' => '6. แจ้งผลผ่านการคัดเลือกและยื่นข้อเสนอ (Job Offer)',
                'category' => 'candidate',
                'theme_color' => '#0d9488',
                'subject' => 'แจ้งผลผ่านการคัดเลือกและยื่นข้อเสนอการจ้างงาน ตำแหน่ง {position_name} - Kumwell Corporation',
                'title' => 'แจ้งผลผ่านการคัดเลือกและยื่นข้อเสนอการจ้างงาน',
                'badge_text' => '✓ ผ่านการคัดเลือก (Selection Passed)',
                'greeting' => 'เรียน คุณ{applicant_name}',
                'body_text' => "บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ขอแสดงความยินดีที่จะแจ้งให้ท่านทราบว่า ท่านได้ผ่านการคัดเลือกจากการสัมภาษณ์งาน สำหรับตำแหน่งงาน {position_name}\n\nทางบริษัทฯ มีความประสงค์จะยื่นข้อเสนอการจ้างงาน (Job Offer) ให้แก่ท่าน โดยเจ้าหน้าที่ฝ่ายทรัพยากรบุคคล (HA) จะติดต่อประสานงานกับท่านโดยตรง เพื่อชี้แจงรายละเอียดเกี่ยวกับอัตราผลตอบแทน สวัสดิการ ข้อตกลง และการนัดหมายกำหนดวันเริ่มต้นทำงาน",
                'notice_title' => '📌 ขั้นตอนถัดไป:',
                'notice_text' => "1. เจ้าหน้าที่ฝ่ายทรัพยากรบุคคลจะติดต่อท่านผ่านทางโทรศัพท์หรืออีเมลเพื่อยืนยันข้อเสนอ\n2. กำหนดวันเริ่มต้นทำงานและเตรียมเอกสารสัญญาจ้างงาน\n3. ท่านจะได้รับอีเมลยืนยันวันเริ่มงานอย่างเป็นทางการอีกครั้ง",
                'closing_text' => 'หากท่านมีข้อสงสัยหรือต้องการสอบถามข้อมูลเพิ่มเติม สามารถติดต่อฝ่ายทรัพยากรบุคคลได้ตามช่องทางด้านล่างนี้',
            ],
            'application_hired' => [
                'name' => '7. แจ้งยืนยันการรับเข้าทำงานและเริ่มงาน (Hired)',
                'category' => 'candidate',
                'theme_color' => '#10b981',
                'subject' => 'แจ้งผลการพิจารณารับเข้าทำงาน - Kumwell Corporation',
                'title' => 'แจ้งผลการพิจารณารับเข้าทำงาน',
                'badge_text' => '✓ ผ่านการคัดเลือกและได้รับการบรรจุเข้าทำงาน (Hired)',
                'greeting' => 'เรียน คุณ{applicant_name}',
                'body_text' => "บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ขอแสดงความยินดีและมีความยินดีเป็นอย่างยิ่งที่จะแจ้งให้ท่านทราบว่า ท่านได้รับการคัดเลือกให้เข้าปฏิบัติงานในตำแหน่ง {position_name}\n\nทางบริษัทฯ ขอต้อนรับท่านเข้าเป็นส่วนหนึ่งของครอบครัวคัมเวล ทั้งนี้ เจ้าหน้าที่ฝ่ายทรัพยากรบุคคลจะประสานงานรายละเอียดและเอกสารที่ต้องจัดเตรียม",
                'notice_title' => '📌 การเตรียมตัวสำหรับการเริ่มงาน:',
                'notice_text' => "• กรุณาจัดเตรียมเอกสารส่วนตัว เช่น สำเนาบัตรประชาชน, สำเนาทะเบียนบ้าน, วุฒิการศึกษา, และรูปถ่าย\n• รายงานตัว ณ ฝ่ายทรัพยากรบุคคล อาคารสำนักงานใหญ่ ตามวันและเวลาที่กำหนด\n• หากต้องการสอบถามหรือมีข้อติดขัดเรื่องวันเริ่มงาน โปรดแจ้งฝ่ายทรัพยากรบุคคลล่วงหน้า",
                'closing_text' => 'หากท่านมีข้อสงสัยหรือต้องการสอบถามข้อมูลเพิ่มเติม สามารถติดต่อฝ่ายทรัพยากรบุคคลได้ตามช่องทางด้านล่างนี้',
            ],
            'application_rejected' => [
                'name' => '8. แจ้งผลการพิจารณาใบสมัครงาน (Rejected)',
                'category' => 'candidate',
                'theme_color' => '#1e3a8a',
                'subject' => 'แจ้งผลการพิจารณาใบสมัครงาน - Kumwell Corporation',
                'title' => 'แจ้งผลการพิจารณาใบสมัครงาน',
                'badge_text' => 'ผลการพิจารณาใบสมัคร',
                'greeting' => 'เรียน คุณ{applicant_name}',
                'body_text' => "ตามที่ท่านได้ให้ความสนใจสมัครงานกับบริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) ในตำแหน่งงาน {position_name}\n\nบริษัทฯ ขอขอบพระคุณเป็นอย่างยิ่งที่ท่านได้ให้ความสนใจและสละเวลาในการเข้าร่วมขั้นตอนการคัดเลือกบุคลากรของบริษัทฯ\n\nทางบริษัทฯ ได้พิจารณาคุณสมบัติและประสบการณ์ของท่านอย่างรอบคอบ แต่ต้องขออภัยที่ต้องแจ้งให้ทราบว่า ในขณะนี้บริษัทฯ ยังไม่สามารถรับท่านเข้าร่วมงานในตำแหน่งดังกล่าวได้ เนื่องจากมีผู้สมัครท่านอื่นที่มีคุณสมบัติตรงกับความต้องการเฉพาะทางของตำแหน่งงานในปัจจุบันมากกว่า",
                'notice_title' => '📌 การจัดเก็บประวัติ (Talent Pool):',
                'notice_text' => 'ทั้งนี้ บริษัทฯ ขออนุญาตบันทึกและจัดเก็บประวัติการทำงานของท่านไว้ในระบบฐานข้อมูลผู้สมัคร หากในอนาคตมีตำแหน่งงานใหม่ที่สอดคล้องกับทักษะและประสบการณ์ของท่าน ฝ่ายทรัพยากรบุคคลจะติดต่อกลับเพื่อเชิญท่านเข้าร่วมงานต่อไป',
                'closing_text' => 'ขอขอบพระคุณอีกครั้งที่ให้ความไว้วางใจและสนใจร่วมงานกับครอบครัว Kumwell และขออวยพรให้ท่านประสบความสำเร็จในหน้าที่การงานและเป้าหมายในชีวิตต่อไป',
            ],
            'direct_email' => [
                'name' => '9. ข้อความ/อีเมลโดยตรงถึงผู้สมัคร (Direct Email)',
                'category' => 'candidate',
                'theme_color' => '#2563eb',
                'subject' => '{subject}',
                'title' => '{title}',
                'badge_text' => '✉️ ข้อความจากฝ่ายทรัพยากรบุคคล',
                'greeting' => 'เรียน คุณ{applicant_name}',
                'body_text' => "{content}",
                'notice_title' => '',
                'notice_text' => '',
                'closing_text' => 'หากท่านมีข้อสงสัย สามารถติดต่อกลับทางอีเมลนี้ได้ทันทีครับ',
            ],
        ];
    }

    /**
     * Get template model or fallback to default
     */
    public static function getTemplate(string $key): self
    {
        $template = self::where('key', $key)->first();
        if ($template) {
            return $template;
        }

        $defaults = self::defaultTemplates();
        $def = $defaults[$key] ?? [
            'name' => 'เทมเพลตอีเมล',
            'category' => 'candidate',
            'theme_color' => '#ea580c',
            'subject' => 'การแจ้งเตือนจากระบบสรรหาบุคลากร',
            'title' => 'การแจ้งเตือนจากระบบสรรหาบุคลากร',
            'badge_text' => null,
            'greeting' => 'เรียน {applicant_name}',
            'body_text' => '',
            'notice_title' => null,
            'notice_text' => null,
            'closing_text' => null,
        ];

        return new self(array_merge(['key' => $key], $def));
    }

    /**
     * Seed or sync default templates
     */
    public static function seedDefaults(): void
    {
        $defaults = self::defaultTemplates();
        foreach ($defaults as $key => $data) {
            self::firstOrCreate(['key' => $key], $data);
        }
    }

    /**
     * Default header and footer values
     */
    public static function defaultHeaderFooter(): array
    {
        return [
            'header_logo_url' => 'https://raw.githubusercontent.com/Kittipatmon/new-ha-project/main/public/images/logos/th-kumwell-logo.png',
            'header_tagline' => 'POWER OF INNOVATION',
            'footer_salutation' => 'ด้วยความเคารพอย่างสูง,',
            'sender_name' => 'กิตติพัฒน์ มานุช',
            'sender_position' => 'เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล',
            'company_name' => 'บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)',
            'contact_phone' => '02-954-3455',
            'contact_website' => 'www.kumwell.com',
            'contact_email' => 'Kittipat.Ma@kumwell.com',
            'footer_copyright' => 'สงวนลิขสิทธิ์ © {year} บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)',
        ];
    }

    public function getHeaderLogo(): string
    {
        return !empty($this->header_logo_url) ? $this->header_logo_url : self::defaultHeaderFooter()['header_logo_url'];
    }

    public function getHeaderTagline(): string
    {
        return !empty($this->header_tagline) ? $this->header_tagline : self::defaultHeaderFooter()['header_tagline'];
    }

    public function getFooterSalutation(): string
    {
        return !empty($this->footer_salutation) ? $this->footer_salutation : self::defaultHeaderFooter()['footer_salutation'];
    }

    public function getResolvedSenderName(?string $fallback = null): string
    {
        return !empty($this->sender_name) ? $this->sender_name : ($fallback ?: self::defaultHeaderFooter()['sender_name']);
    }

    public function getResolvedSenderPosition(?string $fallback = null): string
    {
        return !empty($this->sender_position) ? $this->sender_position : ($fallback ?: self::defaultHeaderFooter()['sender_position']);
    }

    public function getResolvedCompanyName(): string
    {
        return !empty($this->company_name) ? $this->company_name : self::defaultHeaderFooter()['company_name'];
    }

    public function getResolvedContactPhone(): string
    {
        return !empty($this->contact_phone) ? $this->contact_phone : self::defaultHeaderFooter()['contact_phone'];
    }

    public function getResolvedContactWebsite(): string
    {
        return !empty($this->contact_website) ? $this->contact_website : self::defaultHeaderFooter()['contact_website'];
    }

    public function getResolvedContactEmail(?string $fallback = null): string
    {
        return !empty($this->contact_email) ? $this->contact_email : ($fallback ?: self::defaultHeaderFooter()['contact_email']);
    }

    public function getResolvedFooterCopyright(): string
    {
        $raw = !empty($this->footer_copyright) ? $this->footer_copyright : self::defaultHeaderFooter()['footer_copyright'];
        $thaiYear = date('Y') + 543;
        return str_replace('{year}', (string)$thaiYear, $raw);
    }

    /**
     * Replace template placeholders with real values
     */
    public static function replacePlaceholders(?string $text, array $variables = []): string
    {
        if (empty($text)) {
            return '';
        }

        foreach ($variables as $key => $val) {
            $text = str_replace('{' . $key . '}', (string) $val, $text);
        }

        return $text;
    }
}
