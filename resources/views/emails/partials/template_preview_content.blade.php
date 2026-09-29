<div class="message-body">
    @if(!empty($greeting))
        <p class="greeting" style="font-size: 16px; font-weight: 600; margin-bottom: 18px; color: #111827;">{{ $greeting }}</p>
    @endif

    @if(!empty($badgeText) || !empty($variables['position_name']))
        <div class="highlight-box" style="background-color: #f8fafc; border: 1.5px solid #e2e8f0; border-left: 5px solid {{ $template->theme_color ?? '#ea580c' }}; padding: 18px 20px; border-radius: 10px; margin: 20px 0;">
            @if(!empty($badgeText))
                <div style="margin-bottom: 10px;">
                    <span class="badge" style="display: inline-block; background-color: #f1f5f9; color: {{ $template->theme_color ?? '#ea580c' }}; font-weight: 700; font-size: 12.5px; padding: 4px 12px; border-radius: 20px; border: 1px solid #e2e8f0;">
                        {{ $badgeText }}
                    </span>
                </div>
            @endif

            <table style="width: 100%; border-collapse: collapse; font-size: 14px; color: #334155;">
                @if(!empty($variables['position_name']))
                    <tr>
                        <td style="padding: 4px 0; width: 130px; font-weight: 600; color: #64748b;">ตำแหน่งงาน:</td>
                        <td style="padding: 4px 0; font-weight: 700; color: {{ $template->theme_color ?? '#ea580c' }}; font-size: 15px;">{{ $variables['position_name'] }}</td>
                    </tr>
                @endif
                @if(!empty($variables['department_name']))
                    <tr>
                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">สังกัด / แผนก:</td>
                        <td style="padding: 4px 0; font-weight: 500; color: #1e293b;">{{ $variables['department_name'] }}</td>
                    </tr>
                @endif
                @if(!empty($variables['application_no']))
                    <tr>
                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">เลขที่ใบสมัคร:</td>
                        <td style="padding: 4px 0; font-family: monospace; font-weight: 600; color: #0f172a;">{{ $variables['application_no'] }}</td>
                    </tr>
                @endif
                @if(!empty($variables['applied_date']))
                    <tr>
                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">วันที่ส่งใบสมัคร:</td>
                        <td style="padding: 4px 0; color: #334155;">{{ $variables['applied_date'] }}</td>
                    </tr>
                @endif
                @if(!empty($variables['interview_date']) && (str_contains($template->key, 'interview') || isset($isInterview)))
                    <tr>
                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">วันสัมภาษณ์:</td>
                        <td style="padding: 4px 0; font-weight: 600; color: #ea580c;">{{ $variables['interview_date'] }} (เวลา {{ $variables['interview_time'] ?? '-' }})</td>
                    </tr>
                @endif
                @if(!empty($variables['onboarding_date']) && (str_contains($template->key, 'hired') || str_contains($template->key, 'offered')))
                    <tr>
                        <td style="padding: 4px 0; font-weight: 600; color: #64748b;">วันเริ่มงาน:</td>
                        <td style="padding: 4px 0; font-weight: 700; color: #10b981;">{{ $variables['onboarding_date'] }}</td>
                    </tr>
                @endif
            </table>
        </div>
    @endif

    @if(!empty($bodyText))
        <div style="color: #374151; font-size: 14.5px; line-height: 1.75; white-space: pre-line; margin: 18px 0;">{!! nl2br(e($bodyText)) !!}</div>
    @endif

    @if(!empty($noticeTitle) || !empty($noticeText))
        <div class="notice-card" style="background-color: #fff7ed; border: 1px solid #fed7aa; border-left: 4px solid {{ $template->theme_color ?? '#ea580c' }}; border-radius: 8px; padding: 14px 18px; margin: 22px 0; font-size: 13.5px; color: #9a3412;">
            @if(!empty($noticeTitle))
                <strong style="color: #c2410c; font-size: 14px; display: block; margin-bottom: 4px;">{{ $noticeTitle }}</strong>
            @endif
            @if(!empty($noticeText))
                <div style="margin-top: 4px; white-space: pre-line; line-height: 1.6; color: #7c2d12;">{!! nl2br(e($noticeText)) !!}</div>
            @endif
        </div>
    @endif

    @if(!empty($closingText))
        <p style="margin-top: 22px; color: #4b5563; font-size: 14px; line-height: 1.6;">{{ $closingText }}</p>
    @endif
</div>
