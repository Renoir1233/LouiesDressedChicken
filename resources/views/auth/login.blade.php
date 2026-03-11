<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Louie's Dressed Chicken - Login</title>
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
    height:100vh;
    font-family: Arial, Helvetica, sans-serif;
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
    width:80%;
    max-width:1100px;
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
    min-height:550px;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 25px 50px rgba(0,0,0,0.6);
}

/* LEFT SIDE */
.left{
    width:50%;
    background:linear-gradient(to bottom,#660b05,#7a1205,#f4a100);
    display:flex;
    justify-content:center;
    align-items:center;
}

.left img{
    width:95%;
    max-width:550px;
    animation: floatLogo 4s ease-in-out infinite;
}

/* FLOATING LOGO */
@keyframes floatLogo{
    0%,100%{ transform:translateY(0); }
    50%{ transform:translateY(-15px); }
}

/* RIGHT SIDE */
.right{
    width:50%;
    display:flex;
    justify-content:center;
    align-items:center;
}

.login-box{
    width:85%;
    animation: slideRight 0.8s ease;
}

@keyframes slideRight{
    from{ opacity:0; transform:translateX(40px); }
    to{ opacity:1; transform:translateX(0); }
}

.login-box h2{
    color:#660b05;
    font-weight:800;
    margin-bottom:30px;
    letter-spacing:2px;
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
    padding-right:45px;
    outline:none;
    transition:.3s;
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
}

@keyframes shake{
    0%{ transform:translateX(0); }
    25%{ transform:translateX(-5px); }
    50%{ transform:translateX(5px); }
    75%{ transform:translateX(-5px); }
    100%{ transform:translateX(0); }
}

/* MOBILE */
@media(max-width:768px){
    .card-frame{
        flex-direction:column;
    }

    .left,.right{
        width:100%;
        padding:40px 0;
    }

    .frame{
        width:95%;
    }
}
</style>
</head>
<body>

<div class="frame">

    <div class="card-frame">

        <!-- LEFT -->
        <div class="left">
            <img src="{{ asset('images/logo.png') }}">
        </div>

        <!-- RIGHT -->
        <div class="right">
            <div class="login-box">

                <h2>LOGIN</h2>

                <!-- LOCKOUT WARNING -->
                @if(session('lockout_warning'))
                <div style="background: #fff3cd; color: #856404; padding: 12px 15px; border-left: 5px solid #ffc107; border-radius: 8px; margin-bottom: 15px; animation: shake .4s;">
                    <i class="fa fa-exclamation-triangle" style="margin-right: 8px;"></i>
                    <strong>{{ session('lockout_warning') }}</strong>
                </div>
                @endif

                <!-- ERRORS -->
                @if($errors->any())
                <div class="error-box">
                    @foreach($errors->all() as $error)
                        {{ $error }} <br>
                    @endforeach
                </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <label>Email</label>
                    <div class="input-box">
                        <input type="email" name="email" required>
                    </div>

                    <label>Password</label>
                    <div class="input-box">
                        <input type="password" id="password" name="password" required>
                        <i class="fa fa-eye eye" id="togglePassword" onclick="togglePassword()"></i>
                    </div>

                    <button type="submit">
                        <i class="fa fa-sign-in-alt"></i> Login
                    </button>

                </form>

                <!-- FORGOT PASSWORD LINK -->
                <div style="margin-top: 20px; text-align: center;">
                    <a href="{{ route('password.request') }}" style="color: #660b05; font-weight: 600; text-decoration: none; transition: 0.3s;">
                        Forgot Password?
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(){
    const pass = document.getElementById("password");
    const icon = document.getElementById("togglePassword");

    if(pass.type === "password"){
        pass.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else{
        pass.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>
