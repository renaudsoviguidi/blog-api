<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Activez votre compte MonBlog</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f0f7ff;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            max-width: 600px;
            margin: 40px auto;
            padding: 0 16px 40px;
        }

        .header {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%);
            border-radius: 16px 16px 0 0;
            padding: 40px 48px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header-pattern {
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }

        .header-logo {
            position: relative;
            z-index: 1;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .header-logo-mark {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.2);
            border: 1.5px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
        }

        .header-logo-name {
            font-size: 21px;
            font-weight: 700;
            color: white;
        }

        .header-title {
            position: relative;
            z-index: 1;
            font-size: 24px;
            font-weight: 700;
            color: white;
            margin-bottom: 6px;
        }

        .header-sub {
            position: relative;
            z-index: 1;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.78);
            font-weight: 300;
        }

        .body {
            background: #fff;
            padding: 40px 48px;
            border-left: 1px solid #e0f2fe;
            border-right: 1px solid #e0f2fe;
        }

        .greeting {
            font-size: 16px;
            color: #1e293b;
            margin-bottom: 14px;
            font-weight: 500;
        }

        .greeting strong {
            color: #0284c7;
        }

        .message {
            font-size: 14.5px;
            color: #64748b;
            line-height: 1.75;
            margin-bottom: 32px;
        }

        /* ── CTA ── */
        .cta-wrap {
            text-align: center;
            margin-bottom: 28px;
        }

        .cta-btn {
            display: inline-block;
            padding: 15px 44px;
            background: linear-gradient(135deg, #38bdf8, #0284c7);
            color: white !important;
            text-decoration: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.01em;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);
        }

        .cta-expiry {
            margin-top: 12px;
            font-size: 12px;
            color: #f59e0b;
            font-weight: 600;
        }

        .divider {
            border: none;
            border-top: 1px solid #f1f5f9;
            margin: 28px 0;
        }

        .security-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 24px;
        }

        .security-title {
            font-size: 13px;
            font-weight: 600;
            color: #0284c7;
            margin-bottom: 8px;
        }

        .security-box ul {
            list-style: none;
        }

        .security-box ul li {
            font-size: 13px;
            color: #64748b;
            line-height: 1.7;
            padding-left: 14px;
            position: relative;
        }

        .security-box ul li::before {
            content: '·';
            position: absolute;
            left: 0;
            color: #38bdf8;
            font-weight: 700;
        }

        /* ── Lien de secours ── */
        .fallback {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.65;
            margin-bottom: 20px;
        }

        .fallback a {
            color: #0284c7;
            word-break: break-all;
            font-size: 12px;
        }

        .no-request {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
            font-style: italic;
        }

        .footer {
            background: #f8faff;
            border: 1px solid #e0f2fe;
            border-top: none;
            border-radius: 0 0 16px 16px;
            padding: 24px 48px;
            text-align: center;
        }

        .footer-logo {
            font-size: 16px;
            font-weight: 700;
            color: #0c4a6e;
            margin-bottom: 8px;
        }

        .footer-links {
            margin-bottom: 10px;
        }

        .footer-links a {
            font-size: 12px;
            color: #94a3b8;
            text-decoration: none;
            margin: 0 8px;
        }

        .footer-copy {
            font-size: 11px;
            color: #cbd5e1;
            line-height: 1.6;
        }

        @media (max-width: 600px) {

            .header,
            .body,
            .footer {
                padding-left: 24px;
                padding-right: 24px;
            }

            .cta-btn {
                padding: 13px 28px;
                font-size: 14px;
            }
        }
    </style>
</head>
