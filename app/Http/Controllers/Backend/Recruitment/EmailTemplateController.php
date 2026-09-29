<?php

namespace App\Http\Controllers\Backend\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Recruitment\Application;
use App\Models\Recruitment\RecruitmentMailLog;
use App\Models\Recruitment\RecruitmentMailTemplate;
use App\Models\User;
use App\Models\UserMicrosoftToken;
use App\Services\MicrosoftGraphService;
use App\Jobs\SendRecruitmentEmailJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailTemplateController extends Controller
{
    /**
     * Display the email template editor with split-screen live preview
     */
    public function index(Request $request)
    {
        $templates = RecruitmentMailTemplate::orderBy('id')->get();
        if ($templates->isEmpty()) {
            RecruitmentMailTemplate::seedDefaults();
            $templates = RecruitmentMailTemplate::orderBy('id')->get();
        }

        $activeKey = $request->query('key', 'application_received');
        $activeTemplate = $templates->firstWhere('key', $activeKey) ?: $templates->first();

        // Sample application data for live preview
        $sampleApp = Application::with(['applicant', 'jobPost.department', 'interviews'])->latest()->first();
        $sampleVariables = self::getSampleVariables($sampleApp, Auth::user());
        $defaultHeaderFooter = RecruitmentMailTemplate::defaultHeaderFooter();

        return view('backend.recruitment.email-templates.index', compact(
            'templates',
            'activeTemplate',
            'sampleVariables',
            'defaultHeaderFooter'
        ));
    }

    /**
     * Update email template content
     */
    public function update(Request $request, string $key)
    {
        $template = RecruitmentMailTemplate::where('key', $key)->firstOrFail();

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'theme_color' => 'nullable|string|max:20',
            'badge_text' => 'nullable|string|max:255',
            'greeting' => 'nullable|string|max:255',
            'body_text' => 'nullable|string',
            'notice_title' => 'nullable|string|max:255',
            'notice_text' => 'nullable|string',
            'closing_text' => 'nullable|string',
            'header_logo_url' => 'nullable|string|max:500',
            'header_tagline' => 'nullable|string|max:255',
            'footer_salutation' => 'nullable|string|max:255',
            'sender_name' => 'nullable|string|max:255',
            'sender_position' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:255',
            'contact_website' => 'nullable|string|max:255',
            'contact_email' => 'nullable|string|max:255',
            'footer_copyright' => 'nullable|string',
        ]);

        $validated['updated_by'] = Auth::id();
        $template->update($validated);

        if ($request->boolean('apply_to_all_templates')) {
            $globalHeaderFooter = [
                'header_logo_url' => $validated['header_logo_url'] ?? null,
                'header_tagline' => $validated['header_tagline'] ?? null,
                'footer_salutation' => $validated['footer_salutation'] ?? null,
                'sender_name' => $validated['sender_name'] ?? null,
                'sender_position' => $validated['sender_position'] ?? null,
                'company_name' => $validated['company_name'] ?? null,
                'contact_phone' => $validated['contact_phone'] ?? null,
                'contact_website' => $validated['contact_website'] ?? null,
                'contact_email' => $validated['contact_email'] ?? null,
                'footer_copyright' => $validated['footer_copyright'] ?? null,
                'updated_by' => Auth::id(),
            ];
            RecruitmentMailTemplate::query()->update($globalHeaderFooter);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'บันทึกการตั้งค่าเทมเพลตอีเมลเรียบร้อยแล้ว',
                'template' => $template->fresh(['updater']),
            ]);
        }

        return redirect()->route('backend.recruitment.email-templates.index', ['key' => $key])
            ->with('success', 'บันทึกการตั้งค่าเทมเพลตอีเมลเรียบร้อยแล้ว');
    }

    /**
     * Reset a template to its default definition
     */
    public function reset(Request $request, string $key)
    {
        $template = RecruitmentMailTemplate::where('key', $key)->firstOrFail();
        $defaults = RecruitmentMailTemplate::defaultTemplates();

        if (isset($defaults[$key])) {
            $defData = array_merge($defaults[$key], RecruitmentMailTemplate::defaultHeaderFooter());
            $defData['updated_by'] = Auth::id();
            $template->update($defData);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'รีเซ็ตเทมเพลตเป็นค่าเริ่มต้นเรียบร้อยแล้ว',
                'template' => $template,
            ]);
        }

        return redirect()->route('backend.recruitment.email-templates.index', ['key' => $key])
            ->with('success', 'รีเซ็ตเทมเพลตเป็นค่าเริ่มต้นเรียบร้อยแล้ว');
    }

    /**
     * Send a live test email of this customized template
     */
    public function testSend(Request $request, string $key)
    {
        $request->validate([
            'recipient_email' => 'required|email',
        ]);

        $template = RecruitmentMailTemplate::where('key', $key)->firstOrFail();
        $targetEmail = $request->input('recipient_email');

        $sender = Auth::user();
        if (!$sender || !app(MicrosoftGraphService::class)->getValidAccessToken($sender)) {
            $tokenRecord = UserMicrosoftToken::latest('updated_at')->first();
            if ($tokenRecord && $tokenRecord->user_id) {
                $sender = User::find($tokenRecord->user_id) ?: $sender;
            }
        }

        $sampleApp = Application::with(['applicant', 'jobPost.department', 'interviews'])->latest()->first();

        // Allow live editor form values to override DB template
        $subjectRaw = $request->input('subject') ?: $template->subject;
        $titleRaw = $request->input('title') ?: $template->title;
        $themeColor = $request->input('theme_color') ?: $template->theme_color;
        $badgeTextRaw = $request->has('badge_text') ? $request->input('badge_text') : $template->badge_text;
        $greetingRaw = $request->input('greeting') ?: $template->greeting;
        $bodyTextRaw = $request->input('body_text') ?: $template->body_text;
        $noticeTitleRaw = $request->has('notice_title') ? $request->input('notice_title') : $template->notice_title;
        $noticeTextRaw = $request->has('notice_text') ? $request->input('notice_text') : $template->notice_text;
        $closingTextRaw = $request->has('closing_text') ? $request->input('closing_text') : $template->closing_text;

        // Custom Header & Footer from form or template
        $headerLogoUrl = $request->input('header_logo_url') ?: $template->getHeaderLogo();
        $headerTagline = $request->input('header_tagline') ?: $template->getHeaderTagline();
        $footerSalutation = $request->input('footer_salutation') ?: $template->getFooterSalutation();
        $senderNameCustom = $request->input('sender_name') ?: $template->getResolvedSenderName($sender?->fullname);
        $senderPositionCustom = $request->input('sender_position') ?: $template->getResolvedSenderPosition($sender?->position);
        $companyNameCustom = $request->input('company_name') ?: $template->getResolvedCompanyName();
        $contactPhoneCustom = $request->input('contact_phone') ?: $template->getResolvedContactPhone();
        $contactWebsiteCustom = $request->input('contact_website') ?: $template->getResolvedContactWebsite();
        $contactEmailCustom = $request->input('contact_email') ?: $template->getResolvedContactEmail($sender?->email);
        $footerCopyrightCustom = $request->input('footer_copyright') ?: $template->getResolvedFooterCopyright();

        // Rich realistic test data from the shared sample variables helper
        $variables = self::getSampleVariables($sampleApp, $sender);

        // Temporary object or attribute update for styling
        $template->theme_color = $themeColor;

        $subject = RecruitmentMailTemplate::replacePlaceholders($subjectRaw, $variables);
        $title = RecruitmentMailTemplate::replacePlaceholders($titleRaw, $variables);
        $greeting = RecruitmentMailTemplate::replacePlaceholders($greetingRaw, $variables);
        $badgeText = RecruitmentMailTemplate::replacePlaceholders($badgeTextRaw, $variables);
        $bodyText = RecruitmentMailTemplate::replacePlaceholders($bodyTextRaw, $variables);
        $noticeTitle = RecruitmentMailTemplate::replacePlaceholders($noticeTitleRaw, $variables);
        $noticeText = RecruitmentMailTemplate::replacePlaceholders($noticeTextRaw, $variables);
        $closingText = RecruitmentMailTemplate::replacePlaceholders($closingTextRaw, $variables);

        // Render preview content
        $previewHtml = view('emails.partials.template_preview_content', compact(
            'greeting',
            'badgeText',
            'bodyText',
            'noticeTitle',
            'noticeText',
            'closingText',
            'template',
            'variables'
        ))->render();

        // Render preview content inside master layout with custom header & footer
        $htmlContent = view('emails.layouts.master', [
            'subject' => $subject,
            'emailTitle' => $title,
            'themeColor' => $themeColor,
            'titleColor' => $themeColor,
            'headerLogoUrl' => $headerLogoUrl,
            'headerTagline' => $headerTagline,
            'footerSalutation' => $footerSalutation,
            'senderName' => $senderNameCustom,
            'senderEmail' => $contactEmailCustom,
            'senderPosition' => $senderPositionCustom,
            'companyName' => $companyNameCustom,
            'contactPhone' => $contactPhoneCustom,
            'contactWebsite' => $contactWebsiteCustom,
            'footerCopyright' => $footerCopyrightCustom,
            'content' => $previewHtml,
        ])->render();

        try {
            $log = RecruitmentMailLog::create([
                'user_id' => $sender?->id,
                'recipient_email' => $targetEmail,
                'recipient_name' => 'ทดสอบเทมเพลตอีเมล',
                'subject' => '[ทดสอบเทมเพลต] ' . $subject,
                'mail_type' => 'test_' . $key,
                'channel' => 'microsoft_graph',
                'status' => 'queued',
                'payload' => ['html_content' => $htmlContent],
            ]);

            $job = new SendRecruitmentEmailJob(
                $log->id,
                $sender?->id,
                $targetEmail,
                'ผู้รับการทดสอบ',
                '[ทดสอบเทมเพลต] ' . $subject,
                $htmlContent,
                [],
                'test_' . $key
            );
            $job->handle(app(MicrosoftGraphService::class));

            $log->refresh();

            return response()->json([
                'success' => true,
                'message' => "ส่งอีเมลทดสอบไปยัง {$targetEmail} สำเร็จเรียบร้อยแล้ว!",
                'log_id' => $log->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'การส่งอีเมลทดสอบล้มเหลว: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get shared sample variables for preview and test send
     */
    public static function getSampleVariables(?Application $sampleApp = null, ?User $sender = null): array
    {
        if (!$sampleApp) {
            $sampleApp = Application::with(['applicant', 'jobPost.department', 'interviews'])->latest()->first();
        }

        $applicantName = $sampleApp?->applicant ? trim($sampleApp->applicant->full_name) : 'เจได ซีโฟ';
        $positionName = $sampleApp?->jobPost?->position_name ?? 'นักพัฒนาซอฟต์แวร์';
        $departmentName = $sampleApp?->jobPost?->department?->department_name ?? 'ICT';
        $applicationNo = $sampleApp?->application_no ?? 'APP-DUN5PPHQ';
        $appliedDate = $sampleApp?->applied_at ? $sampleApp->applied_at->format('d/m/Y H:i น.') : date('d/m/Y H:i น.');

        return [
            'applicant_name' => $applicantName,
            'position_name' => $positionName,
            'department_name' => $departmentName,
            'application_no' => $applicationNo,
            'applied_date' => $appliedDate,
            'interview_round' => '1',
            'interview_date' => 'วันพุธที่ 15 ตุลาคม 2569',
            'interview_time' => '10:00 น. - 11:30 น.',
            'onboarding_date' => '01 พฤศจิกายน 2569',
            'recipient_name' => $applicantName,
            'sender_name' => $sender?->fullname ?? Auth::user()?->fullname ?? 'ฝ่ายทรัพยากรบุคคลและบริหารงานกลาง',
            'sender_email' => $sender?->email ?? Auth::user()?->email ?? 'recruitment@kumwell.com',
            'admin_name' => $sender?->fullname ?? Auth::user()?->fullname ?? 'กิตติพัฒน์ มานุช',
            'admin_email' => $sender?->email ?? Auth::user()?->email ?? 'Kittipat.Ma@kumwell.com',
            'company_name' => 'บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)',
            'year' => (string) (date('Y') + 543),
        ];
    }
}
