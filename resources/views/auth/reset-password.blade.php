<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Louie's Dressed Chicken - Reset Password</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body{
    margin:0;
    background:linear-gradient(to right, #2b0000, #2b0000);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    font-family: Arial, Helvetica, sans-serif;
    padding: 20px;
}

/* Blur background */
body:before{
    content:'';
    position:fixed;
    top:0;
    left:0;
    right:0;
    bottom:0;
    backdrop-filter: blur(6px);
    z-index:-1;
}

/* MAIN FRAME */
.frame{
    width:100%;
    max-width:500px;
    padding:20px;
    animation: fadeIn 1s ease;
}

/* FADE IN */
@keyframes fadeIn{
    from{ opacity:0; transform:translateY(30px); }
    to{ opacity:1; transform:translateY(0); }
}

/* CARD */
.card-frame{
    display:flex;
    background:#f9f6f2;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 25px 50px rgba(0,0,0,0.6);
    padding: 40px;
}

.login-box{
    width:100%;
    animation: slideRight 0.8s ease;
}

@keyframes slideRight{
    from{ opacity:0; transform:translateX(40px); }
    to{ opacity:1; transform:translateX(0); }
}

.login-box h2{
    color:#660b05;
    font-weight:800;
    margin-bottom:20px;
    letter-spacing:2px;
    text-align: center;
}

.login-box label{
    display: block;
    color: #660b05;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
}

/* INPUT */
.input-box{
    position:relative;
    margin-bottom:22px;
}

.input-box input{
    width:100%;
    border:2px solid #660b05;
    border-radius:30px;
    padding:12px 20px;
    outline:none;
    transition:.3s;
    font-size: 14px;
}

.input-box input:focus{
    border-color:#f4a100;
    box-shadow:0 0 5px rgba(244,161,0,0.8);
}

/* EYE ICON */
.eye{
    position:absolute;
    right:15px;
    top:50%;
    transform:translateY(-50%);
    cursor:pointer;
    color:#660b05;
    font-size:20px;
    transition:0.3s;
}

.eye:hover{
    color:#f4a100;
    transform: translateY(-50%) scale(1.2);
}

/* BUTTON */
.login-box button{
    width:100%;
    background:#f4a100;
    color:#660b05;
    border:none;
    border-radius:30px;
    padding:12px 25px;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
    font-size:16px;
    margin-top: 10px;
}

.login-box button:hover{
    background:#e69500;
    transform:translateY(-3px);
    box-shadow:0 10px 20px #f4a0006e;
}

/* ERROR */
.error-box{
    background:#ffeded;
    color:#b30000;
    padding:10px 15px;
    border-left:5px solid red;
    border-radius:10px;
    margin-bottom:15px;
    animation: shake .4s;
    font-size: 13px;
}

@keyframes shake{
    0%{ transform:translateX(0); }
    25%{ transform:translateX(-5px); }
    50%{ transform:translateX(5px); }
    75%{ transform:translateX(-5px); }
    100%{ transform:translateX(0); }
}

.back-link{
    display: block;
    text-align: center;
    margin-top: 20px;
}

.back-link a{
    color: #660b05;
    text-decoration: none;
    font-weight: 600;
    transition: 0.3s;
    font-size: 14px;
}

.back-link a:hover{
    color: #f4a100;
}

/* MOBILE */
@media(max-width:768px){
    .frame{
        width:95%;
        padding: 10px;
    }
    
    .card-frame{
        padding: 20px;
    }
}
</style>
</head>
<body>

<div class="frame">
    <div class="card-frame">
        <div class="login-box">
            
            <h2>RESET PASSWORD</h2>

            <!-- ERRORS -->
            @if($errors->any())
            <div class="error-box">
                @foreach($errors->all() as $error)
                    {{ $error }} <br>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address (Hidden) -->
                <input type="hidden" name="email" value="{{ $request->email }}">
                
                <!-- Email Display (Read-only) -->
                <label>Email Address</label>
                <div class="input-box">
                    <input 
                        type="email" 
                        value="{{ $request->email }}"
                        readonly
                        placeholder="Your email address"
                        style="background-color: #f0f0f0; cursor: not-allowed;"
                    >
                </div>

                <!-- Password -->
                <label>New Password</label>
                <div class="input-box">
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        required
                        placeholder="Enter new password"
                    >
                    <i class="fa fa-eye eye" id="togglePassword" onclick="togglePassword('password', 'togglePassword')"></i>
                </div>

                <!-- Confirm Password -->
                <label>Confirm Password</label>
                <div class="input-box">
                    <input 
                        type="password" 
                        id="password_confirmation"
                        name="password_confirmation" 
                        required
                        placeholder="Confirm your password"
                    >
                    <i class="fa fa-eye eye" id="togglePasswordConfirm" onclick="togglePassword('password_confirmation', 'togglePasswordConfirm')"></i>
                </div>

                <button type="submit">
                    <i class="fa fa-key"></i> Reset Password
                </button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">
                    <i class="fa fa-arrow-left"></i> Back to Login
                </a>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword(fieldId, iconId){
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(iconId);

    if(field.type === "password"){
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else{
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>
