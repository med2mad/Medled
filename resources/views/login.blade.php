<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medled, login</title>
    <link rel="icon" href="/assets/compiled/png/favicon.png" type="image/png">
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="/assets/compiled/css/auth.css">
    <style>
        .auth-left{
            padding-top: 0 !important;
        }
        .auth-logo{
            margin-bottom: 1rem !important;
            text-align: center;
        }
        .auth-logo img{
            height: 3rem !important;
        }
        .auth-title{
            text-align: center !important;
        }
    </style>
</head>

<body>
    <script src="/assets/static/js/initTheme.js"></script>
    <div id="auth">
        
<div class="h-100">
    <div class="col-lg-5 col-12" style="margin:auto;">
        <div id="auth-left">
            <div class="auth-logo">
                <a href="/">
                    <i class="bi bi-arrow-left-circle-fill" style="font-size:2rem;"></i>
                    <img src="/assets/static/images/logo/logo.png" alt="Logo">
                </a>
            </div>
            <h1 class="auth-title">Log in.</h1>

            <?php if(isset($errorlogin)){ ?> <div class="alert alert-light-danger">Incorrect Name or Password</div> <?php } ?>

            <form action="/login" method="post">
            <input name="page" type="hidden" value="login"/>
                @csrf
                <div class="form-group position-relative has-icon-left mb-4">
                    <input name="name" type="text" class="form-control form-control-xl" placeholder="username" required maxlength="20" minlength="5">
                    <div class="form-control-icon">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                <div class="form-group position-relative has-icon-left mb-4">
                    <input name="pass" type="password" class="form-control form-control-xl" placeholder="password" required maxlength="20" minlength="5">
                    <div class="form-control-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>
                <button class="btn btn-primary btn-block btn-lg shadow-lg mt-5">Log in</button>
            </form>
            <div class="text-center mt-5 text-lg fs-4">
                <p class="text-gray-600">Don't have an account?<br><a href="/page/create_user" class="font-bold">Sign up</a>.</p>
            </div>
        </div>
     </div>
</div>

    </div>
</body>

</html>