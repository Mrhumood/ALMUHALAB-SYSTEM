<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terms & Conditions — {{ config('app.name', 'ALMuhalab International Co.') }}</title>

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    @endif

    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #f0f2f5; color: #1e293b; }
        .terms-header {
            background: #0f172a;
            padding: 2.5rem 1rem 2rem;
            text-align: center;
        }
        .terms-header h1 { color: #fff; font-size: 1.6rem; font-weight: 700; margin: 0; }
        .terms-header p  { color: rgba(255,255,255,.5); font-size: .85rem; margin: .4rem 0 0; }
        .terms-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 6px rgba(0,0,0,.07);
            padding: 2.5rem;
            max-width: 820px;
            margin: 2rem auto 3rem;
        }
        .terms-card h2 { font-size: 1.1rem; font-weight: 700; margin-top: 2rem; margin-bottom: .6rem; color: #0f172a; }
        .terms-card h2:first-child { margin-top: 0; }
        .terms-card p, .terms-card li { font-size: .9rem; color: #374151; line-height: 1.7; }
        .terms-card ul { padding-left: 1.4rem; }
        .terms-back { text-align: center; margin-bottom: 2rem; }
        .terms-back a { color: #2563eb; font-size: .85rem; text-decoration: none; }
        .terms-back a:hover { text-decoration: underline; }
        .last-updated { font-size: .78rem; color: #9ca3af; margin-bottom: 2rem; }
    </style>
</head>
<body>

<div class="terms-header">
    <h1><i class="bi bi-file-text me-2"></i>Terms & Conditions</h1>
    <p>ALMuhalab International Co.</p>
</div>

<div class="container px-3">
    <div class="terms-card">
        <div class="last-updated">Last updated: {{ date('F j, Y') }}</div>

        <h2>1. Acceptance of Terms</h2>
        <p>By registering for and using the ALMuhalab International Co. service request platform ("Platform"), you agree to be bound by these Terms & Conditions. If you do not agree to these terms, please do not use the Platform.</p>

        <h2>2. Use of the Platform</h2>
        <p>The Platform is provided to facilitate service requests between clients and ALMuhalab International Co. staff. You agree to:</p>
        <ul>
            <li>Provide accurate, complete, and truthful information when creating an account and submitting requests.</li>
            <li>Maintain the confidentiality of your account credentials and notify us immediately of any unauthorized access.</li>
            <li>Use the Platform solely for lawful purposes and in compliance with all applicable laws and regulations.</li>
            <li>Not attempt to interfere with, disrupt, or gain unauthorized access to the Platform or its data.</li>
        </ul>

        <h2>3. Account Registration</h2>
        <p>You must be at least 18 years of age to create an account. Each person may only hold one account. You are responsible for all activity that occurs under your account.</p>

        <h2>4. Service Requests</h2>
        <p>Service requests submitted through the Platform are subject to review and acceptance by ALMuhalab International Co. Submission of a request does not guarantee service delivery. The Company reserves the right to decline any request at its sole discretion.</p>

        <h2>5. Payment</h2>
        <p>Payment obligations arise only after a service request has been reviewed, prepared, and confirmed by both parties. Payment receipts submitted through the Platform must be authentic. Submission of fraudulent payment documentation is a serious violation of these Terms and may result in immediate account termination and legal action.</p>

        <h2>6. Uploaded Files & Attachments</h2>
        <p>By uploading files to the Platform, you represent that you have the right to share those files and that they do not infringe any third-party rights. The Company will handle uploaded files in accordance with its Privacy Policy. Do not upload files containing sensitive personal data beyond what is required to process your service request.</p>

        <h2>7. Privacy</h2>
        <p>Your personal information is collected and processed in accordance with our Privacy Policy. By using the Platform, you consent to such processing.</p>

        <h2>8. Limitation of Liability</h2>
        <p>To the fullest extent permitted by applicable law, ALMuhalab International Co. shall not be liable for any indirect, incidental, special, or consequential damages arising from your use of the Platform.</p>

        <h2>9. Modifications to Terms</h2>
        <p>ALMuhalab International Co. reserves the right to modify these Terms at any time. Continued use of the Platform after changes are posted constitutes acceptance of the revised Terms.</p>

        <h2>10. Governing Law</h2>
        <p>These Terms shall be governed by and construed in accordance with the laws of the State of Kuwait, without regard to its conflict of law provisions.</p>

        <h2>11. Contact</h2>
        <p>If you have questions about these Terms, please contact us through the Platform's support channels.</p>
    </div>

    <div class="terms-back">
        @if(Route::has('register'))
            <a href="{{ route('register') }}"><i class="bi bi-arrow-left me-1"></i>Back to Registration</a>
        @endif
    </div>
</div>

</body>
</html>
