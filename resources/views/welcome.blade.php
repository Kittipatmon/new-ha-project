@extends('layouts.app')

@section('content')
    <title>Human Assetment — Kumwell Group</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ===== BASE ===== */
        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Prompt', sans-serif;
            margin: 0;
        }

        :root {
            --navy: #b91c1c;
            --navy-dk: #7f1d1d;
            --navy-md: #991b1b;
            --blue-lt: #ef4444;
            --accent: #f87171;
            --white: #ffffff;
            --light-bg: #fef2f2;
            --text: #1e293b;
            --text-sub: #64748b;
            --text-gray: #bdbdbdff;
            --border: #fecaca;
        }

        /* ===== SCROLL REVEAL ===== */
        .reveal {
            position: relative;
            opacity: 0;
            transition: opacity .9s ease, transform .9s ease;
        }

        .reveal.reveal-up {
            transform: translateY(40px);
        }

        .reveal.reveal-left {
            transform: translateX(-40px);
        }

        .reveal.reveal-right {
            transform: translateX(40px);
        }

        .reveal.active {
            opacity: 1;
            transform: translate(0, 0);
        }

        /* ===== FADE ANIMATIONS ===== */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(28px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fu {
            opacity: 0;
            animation: fadeUp .8s ease-out forwards;
        }

        .d1 {
            animation-delay: .1s;
        }

        .d2 {
            animation-delay: .25s;
        }

        .d3 {
            animation-delay: .4s;
        }

        .d4 {
            animation-delay: .55s;
        }

        /* ===== HERO SLIDER ===== */
        .hero-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
        }

        .hero-slide.active {
            opacity: 1;
        }

        .hero-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* เพิ่มความคมชัดเบื้องต้นด้วย CSS */
            filter: contrast(1.1) brightness(1.05);
            image-rendering: -webkit-optimize-contrast;
        }

        /* ===== TOPBAR ===== */
        .topbar {
            background: var(--navy);
            color: rgba(255, 255, 255, .7);
            font-size: .74rem;
            padding: 7px 0;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }

        .topbar a {
            color: rgba(255, 255, 255, .7);
            text-decoration: none;
        }

        .topbar a:hover {
            color: #fff;
        }

        .topbar-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .topbar-left {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .topbar-right {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* ===== HERO ===== */
        .hero-wrap {
            position: relative;
            min-height: 100vh;
            min-height: 100dvh;
            /* เปลี่ยนจาก 52vh → 88vh */
            display: flex;
            align-items: center;
            overflow: hidden;
            background: #0a1223;
        }

        /* Dark uniform overlay — ไม่ใช้ gradient ข้างซ้าย */
        .hero-overlay {
            position: absolute;
            inset: 0;
            z-index: 2;
            background: rgba(10, 18, 35, 0.15);
            /* ลดความมืดเพื่อให้ภาพสว่างและชัดขึ้น */
        }

        .hero-content {
            position: relative;
            z-index: 10;
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-title {
            font-size: clamp(2rem, 5vw, 3.2rem);
            font-weight: 800;
            line-height: 1.12;
            color: #fff;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: .06em;
            text-shadow: 0 2px 20px rgba(0, 0, 0, .5);
        }

        .hero-title span {
            color: #fff;
        }

        .hero-desc {
            font-size: clamp(.9rem, 1.4vw, 1rem);
            color: rgba(255, 255, 255, .85);
            max-width: 680px;
            line-height: 1.75;
            margin: 0 auto 32px;
            text-shadow: 0 1px 8px rgba(0, 0, 0, .4);
        }

        /* ปุ่ม View More สไตล์ pill พื้นหลังทึบสีขาว เห็นง่าย */
        .btn-hero-viewmore {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1.5px solid #fff;
            color: var(--navy);
            font-weight: 600;
            font-size: .9rem;
            padding: 11px 40px;
            border-radius: 999px;
            text-decoration: none;
            letter-spacing: .04em;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            transition: all .25s;
        }

        /* ===== HERO DOT NAV ===== */
        .hero-dots {
            display: none;
        }

        /* ===== SERVICES STRIP ===== */
        .svc-strip {
            background: transparent;
            padding: 0 24px 60px;
            margin-top: -80px;
            position: relative;
            z-index: 10;
        }

        .svc-strip::before {
            content: '';
            position: absolute;
            top: 20%;
            left: 0;
            width: 100%;
            height: 100%;
            background: #f8fafc;
            transform: skewY(-4deg);
            z-index: -1;
        }

        .svc-strip-inner {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
            align-items: stretch;
        }

        .svc-card {
            background: linear-gradient(145deg, #b91c1c 0%, #991b1b 100%);
            border-radius: 1.25rem;
            padding: 44px 24px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
        }

        .svc-card::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,0.06);
            pointer-events: none;
        }

        .svc-card-icon {
            width: 72px;
            height: 72px;
            border-radius: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 1.75rem;
            color: #fff;
            background: rgba(255,255,255,0.08);
        }

        .svc-card-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .svc-card-desc {
            font-size: .85rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.7;
        }

        /* ===== SECTION UTILS ===== */
        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--blue-lt);
            margin-bottom: 10px;
        }

        .section-tag::before {
            content: '';
            width: 20px;
            height: 2px;
            background: var(--blue-lt);
            border-radius: 2px;
        }

        .section-title {
            font-size: clamp(1.6rem, 3vw, 2.4rem);
            font-weight: 800;
            color: var(--navy);
            line-height: 1.2;
            margin-bottom: 14px;
        }

        .dark .section-title {
            color: #f1f5f9;
        }

        .section-desc {
            color: var(--text-sub);
            font-size: .95rem;
            line-height: 1.75;
        }

        /* ===== ABOUT SECTION ===== */
        .about-section {
            padding: 96px 0;
            background: #fff;
            position: relative;
            z-index: 11;
        }

        .dark .about-section {
            background: #111827;
        }

        .about-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .about-img-wrap {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(153, 27, 27, .18);
        }

        .about-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .about-img-badge {
            position: absolute;
            bottom: 24px;
            left: 24px;
            background: var(--navy);
            border-radius: 10px;
            padding: 16px 22px;
            color: #fff;
        }

        .about-img-badge strong {
            display: block;
            font-size: 1.6rem;
            font-weight: 800;
        }

        .about-img-badge span {
            font-size: .78rem;
            color: rgba(255, 255, 255, .7);
        }

        /* ===== GOALS / IMAGE GRID ===== */
        .goals-section {
            padding: 80px 24px;
            background: #fff;
        }

        .dark .goals-section {
            background: #111827;
        }

        .goals-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .goals-grid {
                grid-template-columns: 1fr;
            }
        }

        .goal-cell {
            position: relative;
            overflow: hidden;
            min-height: 380px;
            border-radius: 4px;
        }

        .goal-cell img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .6s ease;
        }

        .goal-cell:hover img {
            transform: scale(1.05);
        }

        .goal-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.65));
        }

        .goal-body {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
            padding: 40px;
        }

        .goal-cell-main .goal-body {
            align-items: center;
            text-align: center;
        }

        .goal-body h3 {
            font-size: clamp(1.4rem, 2.5vw, 2.2rem);
            font-weight: 900;
            color: #fff;
            text-transform: uppercase;
            margin-bottom: 16px;
            letter-spacing: .02em;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
        }

        .goal-body p {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            max-width: 90%;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        .goal-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 24px;
            background: #fff;
            color: var(--navy);
            font-weight: 800;
            font-size: 0.95rem;
            padding: 14px 36px;
            border-radius: 50px;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s;
        }

        .goal-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            color: var(--blue-lt);
        }

        .goal-cell-main {
            grid-row: span 2;
        }

        /* ===== HR SERVICES (Cards) ===== */
        .hr-section {
            padding: 88px 0 32px 0;
            background: #fff;
        }

        .dark .hr-section {
            background: #111827;
        }

        .hr-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .hr-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .hr-grid-centered {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            max-width: 640px;
            margin: 0 auto;
        }

        @media(max-width:640px) {
            .hr-grid-centered {
                grid-template-columns: 1fr;
            }
        }

        .hr-card {
            background: var(--navy);
            border-radius: 12px;
            padding: 36px 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            text-decoration: none;
            color: #ffffff;
            transition: all .3s;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        .dark .hr-card {
            background: #1e2535;
        }

        .hr-card:hover {
            background: var(--navy-md);
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(153, 27, 27, .25);
            color: #ffffff;
        }

        .hr-card-icon {
            font-size: 3.2rem;
            margin-bottom: 20px;
            color: #ffffff;
        }

        .hr-card-title {
            font-size: 1.15rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 12px;
            color: #ffffff;
            letter-spacing: 0.03em;
        }

        .dark .hr-card-title {
            color: #f1f5f9;
        }

        .hr-card-desc {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
        }

        .subsec-label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .subsec-label .bar {
            width: 4px;
            height: 26px;
            border-radius: 3px;
        }

        .subsec-label h3 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0;
            color: var(--navy);
        }

        .dark .subsec-label h3 {
            color: #f1f5f9;
        }

        .subsec-label span {
            font-size: .76rem;
            color: var(--text-sub);
            margin-left: 6px;
        }

        /* ===== WHY US (PORTFOLIO STYLE) ===== */
        .why-section {
            padding: 100px 0 180px 0;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dk) 100%);
            position: relative;
            z-index: 10;
            overflow: hidden;
        }

        .why-bg-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 2px, transparent 2px);
            background-size: 32px 32px;
            z-index: 1;
        }

        .why-bg-glow {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 50% 50%, rgba(59, 130, 246, 0.15), transparent 60%);
            z-index: 1;
            pointer-events: none;
        }

        /* Top Concave Curve */
        .why-section::before {
            content: '';
            position: absolute;
            top: -60px;
            left: -10%;
            width: 120%;
            height: 120px;
            background: #fff;
            border-radius: 50%;
            z-index: 2;
        }

        .dark .why-section::before {
            background: #111827;
        }

        /* Bottom Concave Curve */
        .why-section::after {
            content: '';
            position: absolute;
            bottom: -120px;
            left: -10%;
            width: 120%;
            height: 240px;
            background: var(--light-bg);
            border-radius: 50%;
            z-index: 2;
        }

        .dark .why-section::after {
            background: #161b27;
        }

        .why-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            position: relative;
            z-index: 5;
        }

        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-top: 20px;
        }

        @media (max-width: 1024px) {
            .portfolio-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .portfolio-grid {
                grid-template-columns: 1fr;
            }

            .why-section {
                padding: 120px 0;
            }

            .why-section::before,
            .why-section::after {
                height: 120px;
                width: 120%;
                left: -10%;
            }

            .why-section::before {
                top: -60px;
            }

            .why-section::after {
                bottom: -60px;
            }
        }

        .portfolio-item {
            position: relative;
            aspect-ratio: 4 / 3;
            overflow: hidden;
            background: #1e2535;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .portfolio-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.6s ease;
        }

        .portfolio-item:hover img {
            transform: scale(1.1);
        }

        .portfolio-overlay {
            position: absolute;
            inset: 0;
            background: rgba(10, 18, 35, 0.5);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 24px;
            text-align: center;
            transition: background 0.3s;
        }

        .portfolio-item:hover .portfolio-overlay {
            background: rgba(10, 18, 35, 0.75);
        }

        .portfolio-icon {
            font-size: 2.5rem;
            color: #f87171;
            margin-bottom: 12px;
        }

        .portfolio-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .portfolio-desc {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.6;
        }

        /* ===== BLOG SECTION ===== */
        .blog-section {
            padding: 88px 0;
            background: #fff;
        }

        .blog-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .blog-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 36px;
        }

        .blog-viewmore {
            display: inline-block;
            padding: 10px 48px;
            border: 1px solid var(--blue-lt);
            border-radius: 4px;
            font-size: .95rem;
            color: var(--blue-lt);
            text-decoration: none;
            transition: all .3s;
            text-align: center;
        }

        .blog-viewmore:hover {
            background: var(--blue-lt);
            color: #fff;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        @media (max-width: 1024px) {
            .blog-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .blog-grid {
                grid-template-columns: 1fr;
            }
        }

        .blog-item {
            display: flex;
            flex-direction: column;
            background: transparent;
            text-decoration: none;
            color: inherit;
        }

        .blog-item:hover .blog-title {
            color: var(--blue-lt);
            text-shadow: 0 0 8px rgba(59, 130, 246, 0.4);
        }

        .blog-thumb {
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            position: relative;
            background: #e2e8f0;
            border: 1px solid var(--text-gray);
        }

        .dark .blog-thumb {
            border-color: rgba(255, 255, 255, .05);
        }

        .blog-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }



        .blog-tag-wrap {
            position: absolute;
            bottom: 0;
            left: 0;
            background: var(--blue-lt);
            color: #fff;
            font-size: .75rem;
            padding: 4px 16px;
            z-index: 10;
        }

        .blog-body {
            padding: 16px 0 0 0;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .blog-title {
            font-size: 1.1rem;
            font-weight: 400;
            color: var(--text);
            margin-bottom: 12px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .dark .blog-title {
            color: #f1f5f9;
        }

        .blog-date {
            font-size: .8rem;
            color: var(--text-sub);
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
        }

        /* ===== CTA BANNER ===== */
        .cta-banner {
            background: linear-gradient(100deg, var(--navy-dk) 0%, var(--navy-md) 100%);
            padding: 48px 0;
        }

        .cta-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 25px;
            flex-wrap: wrap;
        }

        .cta-banner .btn-red {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--blue-lt);
            color: #fff;
            font-weight: 700;
            font-size: .92rem;
            padding: 13px 30px;
            border-radius: 6px;
            text-decoration: none;
            white-space: nowrap;
            box-shadow: 0 6px 20px rgba(239, 68, 68, .35);
            transition: all .25s;
        }

        .cta-banner .btn-red:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            color: #fff;
        }

        /* ===== FOOTER ===== */
        .site-footer {
            background: var(--navy);
            padding: 56px 0 0;
        }

        .footer-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            text-align: center;
        }

        .footer-logo {
            margin-bottom: 20px;
        }

        .footer-logo img {
            height: 56px;
        }

        .footer-logo-text {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 700;
        }

        .footer-logo-sub {
            color: rgba(255, 255, 255, .5);
            font-size: .8rem;
        }

        .footer-contact {
            color: rgba(255, 255, 255, .7);
            font-size: .85rem;
            line-height: 2;
            margin-bottom: 24px;
        }

        .footer-social {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 40px;
        }

        .social-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .95rem;
            color: #fff;
            text-decoration: none;
            transition: opacity .2s;
        }

        .social-btn:hover {
            opacity: .8;
            color: #fff;
        }

        .footer-copy {
            border-top: 1px solid rgba(255, 255, 255, .1);
            padding: 16px 0;
            margin-top: 0;
            color: rgba(255, 255, 255, .4);
            font-size: .75rem;
        }

        /* ===== NO SCROLLBAR ===== */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* ===== DARK MODE ADJUSTMENTS ===== */
        .dark .about-section {
            background: #111827;
        }

        .dark .hr-section {
            background: #111827;
        }

        .dark .blog-section {
            background: #161b27;
        }

        .dark .svc-strip::before {
            background: #1e293b;
        }

        /* ===== RESPONSIVE ===== */
        @media(max-width:1024px) {
            .svc-strip-inner {
                grid-template-columns: repeat(2, 1fr);
                max-width: 800px;
            }

            .about-inner {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .goals-grid {
                grid-template-columns: 1fr;
            }

            .goal-cell-main {
                grid-row: auto;
            }

            .hr-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .why-inner {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .blog-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width:640px) {
            .svc-strip-inner {
                grid-template-columns: 1fr;
            }

            .hr-grid {
                grid-template-columns: 1fr 1fr;
            }

            .topbar {
                display: none;
            }
        }
    </style>

    <!-- Theme toggle & Reveal -->
    <script>
        if (localStorage.getItem('color-theme') === 'dark' ||
            (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Scroll reveal
            const revealObs = new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.classList.add('active');
                        // ปรับปรุง: เมื่อแสดงผลแล้ว ให้ยกเลิกการ observe เพื่อลดภาระการประมวลผล ป้องกันไม่ให้หน้าเว็บค้างหรือกระตุก (Scroll Jank)
                        revealObs.unobserve(e.target);
                    }
                });
            }, {
                threshold: .1
            });
            document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

            // Hero slider
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.hero-dot');
            let cur = 0;
            let timer; // ปรับปรุง: ประกาศ timer ไว้ก่อนเพื่อหลีกเลี่ยงปัญหา ReferenceError (Temporal Dead Zone)

            // ปรับปรุง: เช็คว่ามี element slider อยู่จริงหรือไม่ เพื่อป้องกัน JS Error ที่อาจทำให้การทำงานหยุดชะงัก
            if (slides.length > 0 && dots.length > 0) {
                function goSlide(n) {
                    slides[cur].classList.remove('active');
                    dots[cur].classList.remove('active');
                    cur = n % slides.length;
                    slides[cur].classList.add('active');
                    dots[cur].classList.add('active');
                }
                dots.forEach((d, i) => d.addEventListener('click', () => {
                    clearInterval(timer);
                    goSlide(i);
                    timer = setInterval(() => goSlide(cur + 1), 5500);
                }));
                timer = setInterval(() => goSlide(cur + 1), 5500);
            }
        });
    </script>

    <!-- ==================== TOPBAR ==================== -->
    {{-- <div class="topbar">
        <div class="topbar-inner">
            <div class="topbar-left">
                <span><i class="fas fa-phone mr-1"></i> +66 2 123 4567</span>
                <span><i class="fas fa-envelope mr-1"></i> info@kumwell.com</span>
                <span><i class="fas fa-clock mr-1"></i> 08:00 – 17:30 น.</span>
            </div>
            <div class="topbar-right">
                <button onclick="toggleTheme()"
                    style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.7);font-size:.8rem;"
                    class="hover:text-white">
                    <i class="fas fa-circle-half-stroke mr-1"></i> Theme
                </button>
                @auth
                    <span style="color:rgba(255,255,255,.7);">สวัสดี, {{ Auth::user()->name }}</span>
                @else
                    <a href="{{ route('login') }}">เข้าสู่ระบบ</a>
                @endauth
            </div>
        </div>
    </div> --}}

    <!-- ==================== HERO ==================== -->
    <div class="hero-wrap">

        <!-- Slides -->
        <div class="hero-slide active">
            <img src="{{ asset('images/welcome/hero_industrial.png') }}" alt="Kumwell Plant">
        </div>
        <div class="hero-slide">
            <img src="{{ asset('images/welcome/ro1.jpg') }}" alt="Plant Night">
        </div>
        <div class="hero-slide">
            <img src="https://image.makewebeasy.net/makeweb/m_1920x0/0etpaXZ92/Corporate/Banner_ab_1_.webp?v=202405291424"
                alt="Team">
        </div>

        <div class="hero-overlay"></div>

        <!-- Centered content -->
        <div class="hero-content">
            <!-- <div style="padding: 100px 0 120px;"> -->
            <div style="padding: 180px 0 160px;">
                <h1 class="hero-title fu d1">Human Assetment</h1>
                <p class="hero-desc fu d2">
                    ยกระดับการบริหารทรัพยากรบุคคล มุ่งเน้นการประเมินที่มีประสิทธิภาพ
                    และวัฒนธรรมการเรียนรู้ตลอดชีวิต (Life Long Learning)
                    เพื่อขับเคลื่อนองค์กรสู่อนาคต
                </p>
                <div class="fu d3">
                    <a href="#" class="btn-hero-viewmore">View More</a>
                </div>
            </div>
        </div>

        <!-- Slider Dots -->
        <div class="hero-dots">
            <div class="hero-dot active"></div>
            <div class="hero-dot"></div>
            <div class="hero-dot"></div>
        </div>
    </div>

    <!-- ==================== SERVICES STRIP ==================== -->
    <div class="svc-strip">
        <div class="svc-strip-inner">
            <div class="svc-card reveal reveal-up">
                <div class="svc-card-icon">
                    <i class="fa-solid fa-handshake"></i>
                </div>
                <div class="svc-card-title">C (Corporation)</div>
                <div class="svc-card-desc">ร่วมมือกับผู้มีส่วนได้เสีย ในการบูรณาการเพื่อส่งมอบผลิตภัณฑ์และบริการ พร้อมทั้งสร้างความพึงพอใจให้กับลูกค้า</div>
            </div>
            <div class="svc-card reveal reveal-up" style="transition-delay:.1s">
                <div class="svc-card-icon">
                    <i class="fa-solid fa-network-wired"></i>
                </div>
                <div class="svc-card-title">C (Creating)</div>
                <div class="svc-card-desc">สร้างสรรค์กับพันธมิตรและเครือข่ายในการวิจัยและพัฒนาเพื่อส่งมอบ นวัตกรรมด้านความปลอดภัยและป้องกันฟ้าผ่า</div>
            </div>
            <div class="svc-card reveal reveal-up" style="transition-delay:.2s">
                <div class="svc-card-icon">
                    <i class="fa-solid fa-chalkboard-teacher"></i>
                </div>
                <div class="svc-card-title">S (Shared)</div>
                <div class="svc-card-desc">ร่วมกันกับทุกภาคส่วนในการสร้างสรรค์และเพิ่มคุณค่า เพื่อส่งมอบความ ปลอดภัยสู่สังคม</div>
            </div>
            <div class="svc-card reveal reveal-up" style="transition-delay:.3s">
                <div class="svc-card-icon">
                    <i class="fa-solid fa-people-carry-box"></i>
                </div>
                <div class="svc-card-title">V (Value)</div>
                <div class="svc-card-desc">เสริมสร้างคุณค่า ยกระดับขีดความสามารถทรัพยากรมนุษย์ให้เป็นองค์กร นวัตกรรมระดับสากลสู่ความยั่งยืน</div>
            </div>
        </div>
    </div>

    <!-- ==================== ABOUT US ==================== -->
    <div class="about-section">
        <div class="about-inner">
            <div class="reveal reveal-left">
                <div class="section-tag">About Us</div>
                <h2 class="section-title">เกี่ยวกับ<br>Kumwell Group</h2>
                <p class="section-desc" style="margin-bottom:16px;">
                    บริษัท คัมเวล คอร์ปอเรชั่น จำกัด (มหาชน) เป็นผู้ผลิตและจัดจำหน่ายผลิตภัณฑ์ในระบบต่อลงดิน เช่น แท่งหลักดิน อุปกรณ์เชื่อมตัวนำด้วยความร้อน สารปรับปรุงค่าความต้านทานดิน บ่อทดสอบหลักดิน เป็นต้น ระบบป้องกันฟ้าผ่า เช่น แท่งล่อฟ้า อุปกรณ์จับยึดตัวนำ เป็นต้น ระบบป้องกันเสิร์จ ระบบตรวจจับฟ้าผ่า และระบบแจ้งเตือนภัยฟ้าผ่า ตามมาตรฐานสากลอย่างครบวงจร ภายใต้ตราสินค้าแบรนด์ Kumwell ที่มีการส่งออกและตัวแทนจำหน่ายครอบคลุม 40 ประเทศทั่วโลก โดยมีวิสัยทัศน์ที่จะเป็นผู้นำระบบป้องกันฟ้าผ่า และนวัตกรรมด้านความปลอดภัย เป็นแบรนด์ที่มีความแข็งแกร่งระดับโลก และเป็นองค์กรที่เติบโตอย่างยั่งยืน
                </p>
                <p class="section-desc" style="margin-bottom:32px;">
                    {{-- The goal of the petrochemical industry is to produce a variety of chemicals and materials from petroleum
                    or natural gas,
                    which are used in various industries such as manufacturing, construction, and agriculture. --}}
                </p>
                <a href="https://www.kumwell.com/" target="_blank"
                    style="display:inline-flex;align-items:center;gap:10px;background:var(--navy);color:#fff;font-weight:700;font-size:.9rem;padding:13px 28px;border-radius:6px;text-decoration:none;transition:all .25s;"
                    onmouseover="this.style.background='var(--navy-md)'" onmouseout="this.style.background='var(--navy)'">
                    View More <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="reveal reveal-right">
                <div class="about-img-wrap" style="height:440px;">
                    <img src="{{ asset('images/welcome/kmlhq.jpg') }}" alt="About Kumwell" loading="lazy">
                    <div class="about-img-badge">
                        <strong>25</strong>
                        <span>ปีแห่งประสบการณ์</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==================== GOALS IMAGE GRID ==================== -->
    {{-- <div class="goals-section">
        <div class="goals-grid">
            <!-- Main big cell -->
            <div class="goal-cell goal-cell-main reveal reveal-left">
                <img src="https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?q=80&w=1000&auto=format&fit=crop"
                    alt="Goal" loading="lazy">
                <div class="goal-overlay"></div>
                <div class="goal-body">
                    <h3>Goal of the<br>Petrochemical Industry</h3>
                    <p>The goal of the petrochemical industry is to produce a variety of chemicals and materials from
                        petroleum or natural gas, which are used in various industries such as manufacturing, construction,
                        and agriculture.</p>
                    <a href="#" class="goal-btn">View More <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <!-- Right top -->
            <div class="goal-cell reveal reveal-right">
                <img src="https://images.unsplash.com/photo-1635070041078-e363dbe005cb?q=80&w=1000&auto=format&fit=crop"
                    alt="Professional" loading="lazy">
                <div class="goal-overlay"></div>
                <div class="goal-body">
                    <h3>Professional in the<br>Petrochemical Industry</h3>
                    <p>Latest technological advancements, safety procedures, and regulatory requirements.</p>
                </div>
            </div>
            <!-- Right bottom -->
            <div class="goal-cell reveal reveal-right" style="transition-delay:.1s">
                <img src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?q=80&w=1000&auto=format&fit=crop"
                    alt="Efficiency" loading="lazy">
                <div class="goal-overlay"></div>
                <div class="goal-body">
                    <h3>Operational Efficiency</h3>
                    <p>The industry aims to meet the growing demand for petrochemical products while also ensuring
                        operational efficiency, sustainability, and compliance with regulations to minimize environmental
                        impact.</p>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- ==================== HR SERVICES ==================== -->
    <div id="services-grid" class="hr-section scroll-mt-20 relative overflow-hidden z-10">

        <!-- Watermark Background -->
        <div class="absolute inset-0 z-[-20] pointer-events-none opacity-10 bg-cover bg-bottom bg-no-repeat bg-fixed"
            style="background-image: url('{{ asset('images/welcome/plant_night.png') }}');">
        </div>

        <div class="hr-inner relative z-10 mt-1">
            <div style="text-align:center; margin-bottom:56px;" class="reveal reveal-up">
                <div class="section-tag" style="justify-content:center;">Our Services</div>
                <h2 class="section-title">ระบบบริการ <span style="color:var(--blue-lt)">HR</span></h2>
                <p class="section-desc" style="max-width:520px;margin:0 auto;">เลือกใช้งานระบบต่างๆ
                    ที่ออกแบบมาเพื่อการบริหารทรัพยากรบุคคลอย่างมีประสิทธิภาพ</p>
            </div>

            <!-- Employee Services -->
            @php
                $isHrOrAdmin = Auth::check() && Auth::user()->isHrOrAdmin();
            @endphp

            <div class="{{ $isHrOrAdmin ? 'hr-grid' : 'hr-grid-centered' }}" style="margin-bottom:48px;">

                <!-- HR Request -->
                @auth
                    <a href="{{ route('request.hr') }}" class="hr-card reveal reveal-up" style="transition-delay:.05s">
                @else
                    <div class="hr-card reveal reveal-up cursor-pointer login-open-btn" style="transition-delay:.05s">
                @endauth
                        <div class="hr-card-icon">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                        <div class="hr-card-title">ระบบจัดการคำร้อง</div>
                        <div class="hr-card-desc">คำร้องทุกประเภท แก้ไขเวลา ใบรับรอง ฯลฯ</div>
                @auth
                    </a>
                @else
                    </div>
                @endauth

                <!-- Training -->
                @auth
                    <a href="{{ route('training.index') }}" class="hr-card reveal reveal-up" style="transition-delay:.1s">
                @else
                    <div class="hr-card reveal reveal-up cursor-pointer login-open-btn" style="transition-delay:.1s">
                @endauth
                        <div class="hr-card-icon">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <div class="hr-card-title">ระบบฝึกอบรม</div>
                        <div class="hr-card-desc">ระบบฝึกอบรมและพัฒนาทักษะพนักงาน</div>
                @auth
                    </a>
                @else
                    </div>
                @endauth

                <!-- Management Dashboards (HR Admin Only) -->
                @if ($isHrOrAdmin)
                    <a href="{{ route('manpower.dashboard') }}" class="hr-card reveal reveal-up" style="transition-delay:.15s">
                        <div class="hr-card-icon">
                            <i class="fa-solid fa-users-gear"></i>
                        </div>
                        <div class="hr-card-title">ระบบอัตรากำลังพล</div>
                        <div class="hr-card-desc">ระบบจัดการและวิเคราะห์อัตรากำลังพล</div>
                    </a>

                    <a href="{{ route('request.data') }}" class="hr-card reveal reveal-up" style="transition-delay:.2s">
                        <div class="hr-card-icon">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <div class="hr-card-title">ระบบจัดการข้อมูล</div>
                        <div class="hr-card-desc">จัดการข้อมูลพนักงานและฐานข้อมูลระบบ</div>
                    </a>
                @endif

            </div>
    </div>
    </div>

    <!-- ==================== WHY US ==================== -->
    {{-- <div class="why-section">
        <div class="why-bg-pattern"></div>
        <div class="why-bg-glow"></div>
        <div class="why-inner">
            <h2 class="section-title reveal reveal-up"
                style="color:#fff; text-align:center; margin-bottom:48px; text-transform:uppercase;">
                ทำไมต้องเลือก <span style="color:#f87171;">Kumwell Group</span>
            </h2>

            <div class="portfolio-grid">
                <!-- 1. ความน่าเชื่อถือ -->
                <div class="portfolio-item reveal reveal-up" style="transition-delay: .05s">
                    <img src="https://images.unsplash.com/photo-1504328345606-18bbc8c9d7d1?q=80&w=600&auto=format&fit=crop"
                        alt="Reliability" loading="lazy">
                    <div class="portfolio-overlay">
                        <div class="portfolio-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="portfolio-title">ความน่าเชื่อถือ</div>
                        <div class="portfolio-desc">ระบบ HR ที่ได้รับการพิสูจน์แล้วด้วยมาตรฐานระดับสากล
                            รองรับการดำเนินงานขนาดใหญ่</div>
                    </div>
                </div>

                <!-- 2. Image Only -->
                <div class="portfolio-item reveal reveal-up" style="transition-delay: .1s">
                    <img src="https://images.unsplash.com/photo-1541888087525-4c071661603d?q=80&w=600&auto=format&fit=crop"
                        alt="Project" loading="lazy">
                </div>

                <!-- 3. เน้นการพัฒนาบุคคล -->
                <div class="portfolio-item reveal reveal-up" style="transition-delay: .15s">
                    <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?q=80&w=600&auto=format&fit=crop"
                        alt="Development" loading="lazy">
                    <div class="portfolio-overlay">
                        <div class="portfolio-icon"><i class="fas fa-brain"></i></div>
                        <div class="portfolio-title">เน้นการพัฒนาบุคคล</div>
                        <div class="portfolio-desc">ระบบ Training และ Assessment ที่ส่งเสริม Life Long Learning
                            ของพนักงานทุกระดับ</div>
                    </div>
                </div>

                <!-- 4. Image Only -->
                <div class="portfolio-item reveal reveal-up" style="transition-delay: .2s">
                    <img src="https://images.unsplash.com/photo-1503387762-592deb58ef4e?q=80&w=600&auto=format&fit=crop"
                        alt="Construction" loading="lazy">
                </div>

                <!-- 5. ยืดหยุ่นและปรับแต่งได้ -->
                <div class="portfolio-item reveal reveal-up" style="transition-delay: .25s">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?q=80&w=600&auto=format&fit=crop"
                        alt="Flexible" loading="lazy">
                    <div class="portfolio-overlay">
                        <div class="portfolio-icon"><i class="fas fa-sliders"></i></div>
                        <div class="portfolio-title">ยืดหยุ่นและปรับแต่งได้</div>
                        <div class="portfolio-desc">ออกแบบมาให้รองรับกระบวนการทำงานที่หลากหลาย
                            เชื่อมต่อกับระบบองค์กรได้อย่างราบรื่น</div>
                    </div>
                </div>

                <!-- 6. ครอบคลุมทั่วประเทศ -->
                <div class="portfolio-item reveal reveal-up" style="transition-delay: .3s">
                    <img src="https://images.unsplash.com/photo-1586281380349-632531db7ed4?q=80&w=600&auto=format&fit=crop"
                        alt="Nationwide" loading="lazy">
                    <div class="portfolio-overlay">
                        <div class="portfolio-icon"><i class="fas fa-globe-asia"></i></div>
                        <div class="portfolio-title">ครอบคลุมทั่วประเทศ</div>
                        <div class="portfolio-desc">รองรับการดำเนินงานหลายสาขาทั่วไทย พร้อมระบบรายงานแบบ Real-time</div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- ==================== BLOG / NEWS ==================== -->
    <div id="news-grid" class="blog-section">
        <div class="blog-inner">
            <div class="blog-header reveal reveal-up">
                <div>
                    <div class="section-tag">Blog & News</div>
                    <h2 class="section-title" style="margin-bottom:0;">ข่าวสาร <span style="color:var(--blue-lt)">&
                            ประชาสัมพันธ์</span></h2>
                </div>
            </div>

            @if ($highlight)
                <!-- highlight as first big card + grid -->
                <div class="blog-grid reveal reveal-up" style="margin-bottom:20px;">

                    <!-- Main highlight (span 2 cols on desktop via a separate row) -->
                </div>
                <div class="blog-grid reveal reveal-up">
                    @foreach ($otherNews->take(3) as $item)
                        <a href="{{ route('news.detail', $item->news_id) }}" class="blog-item">
                            <div class="blog-thumb">
                                <img src="{{ $item->image_path ? asset(is_array($item->image_path) ? $item->image_path[0] : $item->image_path) : 'https://placehold.co/600x400/e2e8f0/FFF?text=News' }}"
                                    alt="{{ $item->title }}" loading="lazy">
                                <div class="blog-tag-wrap">ข่าวสาร / News</div>
                            </div>
                            <div class="blog-body">
                                <div class="blog-title">{{ $item->title }}</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div style="border:2px dashed var(--border); border-radius:12px; padding:56px; text-align:center; color:var(--text-sub);"
                    class="reveal reveal-up">
                    <i class="fa-regular fa-newspaper"
                        style="font-size:2.5rem; margin-bottom:12px; display:block; opacity:.4;"></i>
                    ยังไม่มีข่าวประชาสัมพันธ์ในขณะนี้
                </div>
            @endif

            <div style="margin-top:40px; text-align:center;" class="reveal reveal-up">
                <a href="{{ route('news.newsAll') }}" class="blog-viewmore">View More</a>
            </div>
        </div>
    </div>

    <!-- ==================== CTA BANNER ==================== -->
    <div class="topbar">
        <div class="topbar-inner">
            <div class="topbar-left">
                <span><i class="fas fa-phone mr-1"></i> +66 2 123 4567</span>
                <span><i class="fas fa-envelope mr-1"></i> info@kumwell.com</span>
                <span><i class="fas fa-clock mr-1"></i> 08:00 – 17:30 น.</span>
            </div>
            <div class="topbar-right">
                <button onclick="toggleTheme()"
                    style="background:none;border:none;cursor:pointer;color:rgba(255,255,255,.7);font-size:.8rem;"
                    class="hover:text-white">
                    <i class="fas fa-circle-half-stroke mr-1"></i> Theme
                </button>
                @auth
                    <span style="color:rgba(255,255,255,.7);">สวัสดี, {{ Auth::user()->name }}</span>
                @else
                    <a href="{{ route('login') }}">เข้าสู่ระบบ</a>
                @endauth
            </div>
        </div>
    </div>
    {{-- <div class="cta-banner">
        <div class="cta-inner">
            <div>
                <div style="color:#fff;font-weight:800;font-size:1.2rem;margin-bottom:4px;">บริการ HR ระดับองค์กร
                    ครบจบในที่เดียว</div>
                <div style="color:rgba(255,255,255,.6);font-size:.85rem;">Kumwell Group — Human Asset Management Platform
                </div>
            </div>
            <a href="#" class="btn-red">ติดต่อฝ่าย HR <i class="fas fa-arrow-right"></i></a>
        </div>
    </div> --}}

    <!-- ==================== FOOTER ==================== -->
    {{-- <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-logo">
                <div class="footer-logo-text">Kumwell Group</div>
                <div class="footer-logo-sub">Human Asset Management</div>
            </div>
            <div class="footer-contact">
                Address : 123/4 Somewhere Bldg., Street Name, District Name, Province, 10400<br>
                Tel : 012 345 6789 &nbsp;|&nbsp; Email : info@kumwell.com
            </div>
            <div class="footer-social">
                <a href="#" class="social-btn" style="background:#1877f2;"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-btn" style="background:#000;"><i class="fab fa-x-twitter"></i></a>
                <a href="#" class="social-btn"
                    style="background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);"><i
                        class="fab fa-instagram"></i></a>
                <a href="#" class="social-btn" style="background:#ff0000;"><i class="fab fa-youtube"></i></a>
                <a href="#" class="social-btn" style="background:#06c755;"><i class="fab fa-line"></i></a>
            </div>
        </div>
        <div class="footer-copy">
            <div style="max-width:1280px;margin:0 auto;padding:0 24px;">
                Copyright 2024 | All Rights Reserved | Kumwell Group
            </div>
        </div>
    </footer> --}}

@endsection
