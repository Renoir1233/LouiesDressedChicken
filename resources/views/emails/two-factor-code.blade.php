<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Verification Code</title>
    <style>
        body { margin:0; padding:0; background:#f4f4f4; font-family:Arial,sans-serif; }
        .wrapper { width:100%; padding:40px 0; background:#f4f4f4; }
        .card { max-width:520px; margin:0 auto; background:#ffffff; border-radius:10px;
                overflow:hidden; box-shadow:0 4px 20px rgba(0,0,0,0.1); }
        .header { background:linear-gradient(to right,#660b05,#f4a100); padding:30px 40px; text-align:center; }
        .header h1 { color:#ffffff; margin:0; font-size:22px; letter-spacing:1px; }
        .body { padding:36px 40px; color:#333333; }
        .body p { font-size:15px; line-height:1.6; margin:0 0 16px; }
        .code-box { background:#fff3e0; border:2px solid #f4a100; border-radius:10px;
                    text-align:center; padding:20px; margin:24px 0; }
        .code-box span { font-size:40px; font-weight:bold; letter-spacing:10px;
                         color:#660b05; font-family:monospace; }
        .btn { display:block; width:fit-content; margin:24px auto 0;
               background:#f4a100; color:#660b05 !important; text-decoration:none;
               padding:13px 36px; border-radius:30px; font-weight:bold; font-size:15px; }
        .footer { background:#f9f6f2; padding:18px 40px; text-align:center;
                  font-size:12px; color:#888888; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <div class="header">
            <h1>&#128274; Verification Code</h1>
        </div>
        <div class="body">
            <p>Hello <strong>{{ $user->name }}</strong>,</p>
            <p>You are logging in from a new or unrecognized device. Use the code below to verify your identity:</p>
            <div class="code-box">
                <span>{{ $code }}</span>
            </div>
            <p style="font-size:13px;color:#888;">This code expires in <strong>15 minutes</strong>. Do not share it with anyone.</p>
            <p>If you did not attempt to log in, please ignore this email and consider changing your password immediately.</p>
            <a href="{{ route('verify.2fa.show') }}" class="btn">Enter Verification Code</a>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</div>
</body>
</html>
