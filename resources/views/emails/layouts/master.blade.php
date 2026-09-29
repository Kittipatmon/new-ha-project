<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject', $subject ?? 'การแจ้งเตือนจากระบบสรรหาบุคลากร Kumwell')</title>
    <style>
        body {
            font-family: 'Sarabun', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 20px;
            color: #1f2937;
            line-height: 1.6;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        .container {
            max-width: 620px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-top: 5px solid {{ $themeColor ?? '#ea580c' }};
        }

        .header {
            background-color: #ffffff;
            padding: 30px 20px 20px 20px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .header h1 {
            color: {{ $titleColor ?? $themeColor ?? '#0f172a' }};
            font-size: 20px;
            margin: 15px 0 0 0;
            font-weight: 700;
        }

        .header .tagline {
            color: #ea580c;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-top: 6px;
        }

        .content {
            padding: 30px;
        }

        .greeting {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #111827;
        }

        .message-body {
            color: #4b5563;
            font-size: 14px;
        }

        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 13px;
        }

        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }

        a {
            color: {{ $themeColor ?? '#ea580c' }};
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        /* Responsive Styles for Mobile, iPad, and Desktop */
        @media only screen and (max-width: 620px) {
            body {
                padding: 0 !important;
                background-color: #ffffff !important;
            }

            .container {
                max-width: 100% !important;
                width: 100% !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                border-left: 0 !important;
                border-right: 0 !important;
            }

            .header {
                padding: 24px 16px 18px 16px !important;
            }

            .header img {
                max-width: 170px !important;
            }

            .header h1 {
                font-size: 18px !important;
                margin-top: 12px !important;
            }

            .content {
                padding: 22px 16px !important;
            }

            .highlight-box, .section-card {
                padding: 14px !important;
                margin: 16px 0 !important;
            }

            .notice-card, .test-box, .doc-section, .welfare-section, .meeting-box, .note-card {
                padding: 12px 14px !important;
            }

            .btn, .btn-meeting, .btn-maps, .track-btn, .btn-action {
                display: block !important;
                width: auto !important;
                text-align: center !important;
            }

            .info-table td {
                display: block !important;
                width: 100% !important;
                padding: 3px 0 !important;
            }

            .info-label {
                color: #64748b !important;
                font-size: 12px !important;
            }

            .info-value {
                margin-bottom: 8px !important;
                font-size: 14px !important;
            }
        }
    </style>
    @yield('extra_css')
</head>

<body>
@php
    try {
        if (!isset($headerLogoUrl) || !isset($footerSalutation)) {
            $defaultTpl = \App\Models\Recruitment\RecruitmentMailTemplate::first();
            if ($defaultTpl) {
                $headerLogoUrl = $headerLogoUrl ?? $defaultTpl->getHeaderLogo();
                $headerTagline = $headerTagline ?? $defaultTpl->getHeaderTagline();
                $footerSalutation = $footerSalutation ?? $defaultTpl->getFooterSalutation();
                $senderName = $senderName ?? $defaultTpl->getResolvedSenderName();
                $senderPosition = $senderPosition ?? $defaultTpl->getResolvedSenderPosition();
                $companyName = $companyName ?? $defaultTpl->getResolvedCompanyName();
                $contactPhone = $contactPhone ?? $defaultTpl->getResolvedContactPhone();
                $contactWebsite = $contactWebsite ?? $defaultTpl->getResolvedContactWebsite();
                $contactEmail = $contactEmail ?? $defaultTpl->getResolvedContactEmail();
                $footerCopyright = $footerCopyright ?? $defaultTpl->getResolvedFooterCopyright();
            }
        }
    } catch (\Throwable $e) {}
@endphp
    <div class="container">
        <!-- Unified Header -->
        <div class="header">
            <img src="{{ $headerLogoUrl ?? 'https://raw.githubusercontent.com/Kittipatmon/new-ha-project/main/public/images/logos/th-kumwell-logo.png' }}" alt="Kumwell Logo"
                style="max-width: 200px; height: auto; display: block; margin: 0 auto; border: 0; background-color: #ffffff;">
            @if(!empty($headerTagline))
                <div class="tagline">{{ $headerTagline }}</div>
            @else
                <div class="tagline">POWER OF INNOVATION</div>
            @endif
            <h1>@yield('title', $emailTitle ?? '')</h1>
        </div>

        <!-- Main Content -->
        <div class="content">
            @hasSection('content')
                @yield('content')
            @elseif(isset($content))
                {!! $content !!}
            @endif

            <!-- Unified or Custom Signature -->
            @hasSection('signature')
                @yield('signature')
            @else
                <div class="signature">
                    <p style="margin: 0; font-weight: 600; color: #1f2937;">{{ $footerSalutation ?? 'ด้วยความเคารพอย่างสูง,' }}</p>
                    <p style="margin: 5px 0 0 0; color: #4b5563;">
                        <strong>{{ $senderName ?? 'กิตติพัฒน์ มานุช' }}</strong><br>
                        <span style="font-size: 13px; color: #6b7280;">{{ $senderPosition ?? 'เจ้าหน้าที่ฝ่ายทรัพยากรบุคคล' }}</span><br>
                        {{ $companyName ?? 'บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)' }}<br>
                        <span style="font-size: 13px; color: #6b7280;">
                            โทร: {{ $contactPhone ?? '02-954-3455' }} | เว็บไซต์: <a href="{{ (!empty($contactWebsite) && str_starts_with($contactWebsite, 'http')) ? $contactWebsite : ('https://' . ($contactWebsite ?? 'www.kumwell.com')) }}" target="_blank" style="color: {{ $themeColor ?? '#ea580c' }};">{{ $contactWebsite ?? 'www.kumwell.com' }}</a>
                            @php
                                $resolvedMail = !empty($contactEmail) ? $contactEmail : ($senderEmail ?? 'Kittipat.Ma@kumwell.com');
                            @endphp
                            @if(!empty($resolvedMail))
                                | อีเมล: <a href="mailto:{{ $resolvedMail }}" style="color: {{ $themeColor ?? '#ea580c' }};">{{ $resolvedMail }}</a>
                            @endif
                        </span>
                    </p>
                </div>
            @endif
        </div>

        <!-- Unified Footer -->
        <div class="footer">
            @if(!empty($footerCopyright))
                {!! str_replace('{year}', date('Y') + 543, $footerCopyright) !!}
            @else
                สงวนลิขสิทธิ์ &copy; {{ date('Y') + 543 }} บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน)
            @endif
        </div>
    </div>
</body>

</html>
