<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medled, signup</title>
    <link rel="icon" href="/assets/compiled/png/favicon.png" type="image/png">
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="/assets/compiled/css/auth.css">
    <style>
        .auth-left{
            padding-top:0 !important;
        }
        .auth-logo{
            margin-bottom: 1rem !important;
            text-align:center;
        }
        .auth-logo img{
            height: 3rem !important;
        }
        .photo{
            width: 100px;
            height: 100px;
            margin: 5px;
            border: solid;
            object-fit: contain;
            background-color: black;
            border-radius: 50%;
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
            <h1 class="auth-title">Sign Up</h1>

            <form action="/signup" method="POST" enctype="multipart/form-data">
                @csrf
                <input name="page" type="hidden" value="create_user">
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="text" class="form-control form-control-xl" name="name" placeholder="Username" required maxlength="20" minlength="5">
                    <div class="form-control-icon">
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="password" class="form-control form-control-xl" name="pass" placeholder="Password" required maxlength="20" minlength="5">
                    <div class="form-control-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>
                <div class="form-group position-relative has-icon-left mb-4">
                    <input type="password" class="form-control form-control-xl" name="pass2" placeholder="Confirm Password" required maxlength="20" minlength="5">
                    <div class="form-control-icon">
                        <i class="bi bi-shield-lock"></i>
                    </div>
                </div>
                <div>
                    <div style="display:flex; gap:10px; align-items:center; justify-content:center;">
                        <div style="display:flex; flex-direction:column; gap:2px;">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('photo').click();" >Pick Photo</button>
                            <button type="button" class="btn btn-secondary" id="nophoto">No Photo</button>
                        </div>
                        <div>
                            <input name="photo" id="photo" type="file" accept=".jpg,.jpeg,.png,.bmp,.gif" class="form-control" style="display:none;" />
                            <label for="photo"><img id="img" class="photo" width="100" height="100" src="/uploads/profiles/136.jpg" alt="photo profile" /></label>
                        </div>
                    </div>
                </div>
                <div style="margin-top:4px;">
                    <input type="checkbox" name="conditions" required id="terms"> <label for="terms"> Accept <a href="/page/conditions" style="text-decoration:underline;">Terms &amp; Conditions</a></label>
                </div>
                <input type="submit" name="signup" class="btn btn-primary btn-block btn-lg shadow-lg mt-5" value="Sign Up">
            </form>
            <div class="text-center mt-5 text-lg fs-4">
                <p class='text-gray-600'>Already have an account?<br><a href="/page/login" class="font-bold">Log in</a>.</p>
            </div>
        </div>
    </div>
</div>

    </div>


    
<script type="text/javascript">
    document.getElementById("photo").onchange=function() {
        document.getElementById("img").setAttribute("src",URL.createObjectURL(document.getElementById("photo").files[0]));
    }
    document.getElementById("nophoto").onclick=function() {
        document.getElementById("photo").value= null;
        document.getElementById("img").setAttribute("src","/uploads/profiles/136.jpg");
    }
</script>

</body>

</html>