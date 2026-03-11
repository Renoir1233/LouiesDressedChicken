<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Louie's Dressed Chicken - Verify Identity</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    margin: 0;
    background: linear-gradient(to right, #2b0000, #2b0000);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    font-family: Arial, Helvetica, sans-serif;
}

body:before {
    content: '';
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    backdrop-filter: blur(6px);
    z-index: -1;
}

.frame {
    width: 80%;
    max-width: 1100px;
    padding: 20px;
    animation: fadeIn 1s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(30px); }
    to   { opacity: 1; transform: translateY(0); }
}

.card-frame {
    display: flex;
    background: #f9f6f2;
    min-height: 550px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0,0,0,0.6);
}

.left {
    width: 50%;
    background: linear-gradient(to bottom, #660b05, #7a1205, #f4a100);
    display: flex;
    justify-content: center;
    align-items: center;
}

.left img {
    width: 95%;
    max-width: 550px;
}

.right {
    width: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.verify-box {
    width: 85%;
    animation: slideRight 0.8s ease;
}

@keyframes slideRight {
    from { opacity: 0; transform: translateX(40px); }
    to   { opacity: 1; transform: translateX(0); }
}

.verify-box h2 {
    color: #660b05;
    font-weight: 800;
    margin-bottom: 6px;
    letter-spacing: 2px;
}

.verify-box .subtitle {
    color: #555;
    font-size: 0.93rem;
    margin-bottom: 24px;
    line-height: 1.5;
}

.otp-input {
    width: 100%;
    border: 2px solid #660b05;
    border-radius: 30px;
    padding: 14px 20px;
    outline: none;
    transition: .3s;
    text-align: center;
    font-size: 2rem;
    font-family: monospace;
    letter-spacing: 0.5em;
    background: #fff;
}

.otp-input:focus {
    border-color: #f4a100;
    box-shadow: 0 0 6px rgba(244,161,0,0.8);
}

.btn-verify {
    width: 100%;
    background: #f4a100;
    color: #660b05;
    border: none;
    border-radius: 30px;
    padding: 12px 25px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
    font-size: 16px;
    margin-top: 14px;
}

.btn-verify:hover {
    background: #e69500;
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(244,160,0,0.43);
}

.error-box {
    background: #ffeded;
    color: #b30000;
    padding: 10px 15px;
    border-left: 5px solid red;
    border-radius: 10px;
    margin-bottom: 15px;
    animation: shake .4s;
}

.success-box {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 10px 15px;
    border-left: 5px solid #4caf50;
    border-radius: 10px;
    margin-bottom: 15px;
}

@keyframes shake {
    0%   { transform: translateX(0); }
    25%  { transform: translateX(-5px); }
    50%  { transform: translateX(5px); }
    75%  { transform: translateX(-5px); }
    100% { transform: translateX(0); }
}

.trust-box {
    background: #f0ebe5;
    border: 1px solid #d4b8a0;
    border-radius: 10px;
    padding: 12px 15px;
    margin-top: 14px;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.trust-box input[type="checkbox"] {
    margin-top: 3px;
    accent-color: #660b05;
    width: 16px;
    height: 16px;
    flex-shrink: 0;
}

.resend-btn {
    background: none;
    border: none;
    color: #660b05;
    font-weight: 600;
    cursor: pointer;
    font-size: 0.9rem;
    transition: 0.3s;
}

.resend-btn:hover { color: #f4a100; }

@media (max-width: 768px) {
    .card-frame { flex-direction: column; }
    .left, .right { width: 100%; padding: 40px 0; }
    .frame { width: 95%; }
}
</style>
</head>
<body>

<div class="frame">
    <div class="card-frame">

        <!-- LEFT — Logo -->
        <div class="left">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>

        <!-- RIGHT — Verify form -->
        <div class="right">
            <div class="verify-box">

                <h2><i class="fas fa-shield-alt me-2"></i>VERIFY</h2>
                <p class="subtitle">
                    A 6-digit verification code has been sent to your Gmail address.<br>
                    Enter it below to access your account.
                </p>

                @if ($errors->any())
                    <div class="error-box">
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif

                @if (session('status'))
                    <div class="success-box">
                        <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('verify.2fa.verify') }}">
                    @csrf

                    <label style="color:#660b05;font-weight:600;display:block;margin-bottom:8px;">
                        Verification Code
                    </label>
                    <input type="text" name="code" id="code" class="otp-input"
                           placeholder="000000" maxlength="6" pattern="[0-9]{6}"
                           inputmode="numeric" required autofocus>
                    @error('code')
                        <p style="color:#b30000;font-size:0.85rem;margin-top:6px;padding-left:10px;">{{ $message }}</p>
                    @enderror

                    <!-- Trust this device -->
                    <div class="trust-box">
                        <input type="checkbox" id="trust_device" name="trust_device" value="1">
                        <label for="trust_device" style="cursor:pointer;line-height:1.4;">
                            <span style="font-weight:600;color:#333;">Trust this device</span><br>
                            <span style="font-size:0.8rem;color:#777;">Skip the code on this device for future logins.</span>
                        </label>
                    </div>

                    <button type="submit" class="btn-verify">
                        <i class="fas fa-check me-2"></i>Verify &amp; Continue
                    </button>
                </form>

                <!-- Resend code -->
                <div style="margin-top:16px;text-align:center;">
                    <form action="{{ route('verify.2fa.resend') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="resend-btn">
                            <i class="fas fa-redo me-1"></i>Didn't receive a code? Resend
                        </button>
                    </form>
                </div>

                <!-- Back to login -->
                <div style="margin-top:12px;text-align:center;">
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" style="background:none;border:none;color:#aaa;font-size:0.83rem;cursor:pointer;">
                            <i class="fas fa-sign-out-alt me-1"></i>Back to Login
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<script>
// Only allow digits in the OTP field
document.getElementById('code').addEventListener('input', function () {
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6);
});
</script>

</body>
</html>

