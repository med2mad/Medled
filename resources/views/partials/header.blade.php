<?php if (session_id()=="") session_start();?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedLed Social</title>
    <link rel="icon" href="/assets/compiled/png/favicon.png" type="image/png">
    <link rel="stylesheet" href="/assets/extensions/filepond/filepond.css">
    <link rel="stylesheet" href="/assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.css">
    <link rel="stylesheet" href="/assets/compiled/css/app.css">
    <link rel="stylesheet" href="/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="/style.css">
    <style>
        .svg-inline--fa{
            width: 1.2rem !important;
        }
        .sidebar-title{
            margin-top: 1.5rem !important;
            margin-bottom: 0 !important;
            padding-left: 0 !important;
        }
        .sidebar-header{
            padding: 1rem !important;
            background-color: rgba(30, 30, 45, 0.2); /*1e1e2d */
            border-radius: 0 0 20px 20px;
        }
        .logo img{
            height: 3.2rem !important;
        }
        .service{
            text-align: center;
            align-items: center;
            gap: 10px;
        }

        .form-check-input{
            margin: 0 !important;
        }
        .form-switch{
            padding-left: 0 !important;
        }
        .menu{
            margin-top:0 !important;
        }
        #main-content{
            padding-top:0 !important;
        }
        .avatar img{
            object-fit: contain !important;
            background-color: rgba(0,0,0,0.4);
            border:solid 2px;
            background-color:black;
        }
        .userstatusactive{
            background-color: #edfff6 !important;
        }
        
    </style>
</head>

<body>
    <script src="/assets/static/js/initTheme.js"></script>
    <div id="app">
        <div id="sidebar">
            <div class="sidebar-wrapper active">
    <div class="sidebar-header position-relative">
        <div class="d-flex justify-content-between align-items-center">
            <div class="logo">
                <a href="/"><img src="/assets/static/images/logo/logo.png" alt="Logo" srcset=""></a>
            </div>
            <div class="theme-toggle d-flex gap-2  align-items-center mt-2">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                    role="img" class="iconify iconify--system-uicons" width="20" height="20"
                    preserveAspectRatio="xMidYMid meet" viewBox="0 0 21 21">
                    <g fill="none" fill-rule="evenodd" stroke="currentColor" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path
                            d="M10.5 14.5c2.219 0 4-1.763 4-3.982a4.003 4.003 0 0 0-4-4.018c-2.219 0-4 1.781-4 4c0 2.219 1.781 4 4 4zM4.136 4.136L5.55 5.55m9.9 9.9l1.414 1.414M1.5 10.5h2m14 0h2M4.135 16.863L5.55 15.45m9.899-9.9l1.414-1.415M10.5 19.5v-2m0-14v-2"
                            opacity=".3"></path>
                        <g transform="translate(-210 -1)">
                            <path d="M220.5 2.5v2m6.5.5l-1.5 1.5"></path>
                            <circle cx="220.5" cy="11.5" r="4"></circle>
                            <path d="m214 5l1.5 1.5m5 14v-2m6.5-.5l-1.5-1.5M214 18l1.5-1.5m-4-5h2m14 0h2"></path>
                        </g>
                    </g>
                </svg>
                <div class="form-check form-switch fs-6">
                    <input class="form-check-input  me-0" type="checkbox" id="toggle-dark" style="cursor: pointer">
                    <label class="form-check-label"></label>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true"
                    role="img" class="iconify iconify--mdi" width="20" height="20" preserveAspectRatio="xMidYMid meet"
                    viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="m17.75 4.09l-2.53 1.94l.91 3.06l-2.63-1.81l-2.63 1.81l.91-3.06l-2.53-1.94L12.44 4l1.06-3l1.06 3l3.19.09m3.5 6.91l-1.64 1.25l.59 1.98l-1.7-1.17l-1.7 1.17l.59-1.98L15.75 11l2.06-.05L18.5 9l.69 1.95l2.06.05m-2.28 4.95c.83-.08 1.72 1.1 1.19 1.85c-.32.45-.66.87-1.08 1.27C15.17 23 8.84 23 4.94 19.07c-3.91-3.9-3.91-10.24 0-14.14c.4-.4.82-.76 1.27-1.08c.75-.53 1.93.36 1.85 1.19c-.27 2.86.69 5.83 2.89 8.02a9.96 9.96 0 0 0 8.02 2.89m-1.64 2.02a12.08 12.08 0 0 1-7.8-3.47c-2.17-2.19-3.33-5-3.49-7.82c-2.81 3.14-2.7 7.96.31 10.98c3.02 3.01 7.84 3.12 10.98.31Z">
                    </path>
                </svg>
            </div>
        </div>
    </div>
    <div class="sidebar-menu">
        <ul class="menu">
            
        <?php 
        //  if (session_id()=="") session_start(); 
        if(isset($_SESSION["auth"]) && $_SESSION["auth"]=="true") { ?>

            <li class="sidebar-title">Menu</li>

            <li class="sidebar-item  ">
                <a href="/page/conversations" class='sidebar-link'>
                    <i class="bi bi-chat-dots-fill"></i>
                    <span>Last Conversation</span>
                </a>
            </li>

            <li class="sidebar-item  ">
                <a href="/page/gallery?user=<?= $_SESSION["id"] ?>" class='sidebar-link'>
                    <i class="bi bi-image-fill"></i>
                    <span>Gallery</span>
                </a>
            </li>

            <li class="sidebar-item  ">
                <a href="/page/users?title=Friends" class='sidebar-link'>
                    <svg width="24px" height="24px" viewBox="0 0 24.00 24.00" fill="currentColor" xmlns="http://www.w3.org/2000/svg" stroke="#000000" stroke-width="0.00024000000000000003"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC" stroke-width="0.43200000000000005"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M5 9.5C5 7.01472 7.01472 5 9.5 5C11.9853 5 14 7.01472 14 9.5C14 11.9853 11.9853 14 9.5 14C7.01472 14 5 11.9853 5 9.5Z" fill="currentColor"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M4.64115 15.6993C5.87351 15.1644 7.49045 15 9.49995 15C11.5112 15 13.1293 15.1647 14.3621 15.7008C15.705 16.2847 16.5212 17.2793 16.949 18.6836C17.1495 19.3418 16.6551 20 15.9738 20H3.02801C2.34589 20 1.85045 19.3408 2.05157 18.6814C2.47994 17.2769 3.29738 16.2826 4.64115 15.6993Z" fill="currentColor"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M21.7071 2.29289C22.0976 2.68342 22.0976 3.31658 21.7071 3.70711L18.2071 7.20711C17.5404 7.87377 16.4596 7.87377 15.7929 7.20711L14.2929 5.70711C13.9024 5.31658 13.9024 4.68342 14.2929 4.29289C14.6834 3.90237 15.3166 3.90237 15.7071 4.29289L17 5.58579L20.2929 2.29289C20.6834 1.90237 21.3166 1.90237 21.7071 2.29289Z" fill="currentColor"></path> </g></svg>
                    <span>Friends</span>
                </a>
            </li>

            <li class="sidebar-item  ">
                <a href="/page/users?title=Users&name=" class='sidebar-link'>
                    <svg class="svg-inline--fa fa-users fa-w-20 fa-fw select-all" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="users" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><path fill="currentColor" d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z"></path></svg>
                    <span>Look for Users</span>
                </a>
            </li>
            <?php } ?>
            <li class="sidebar-title">Navigation</li>

            <li class="sidebar-item  ">
                <a href="/" class='sidebar-link'>
                    <svg class="svg-inline--fa fa-home fa-w-18 fa-fw select-all" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="home" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M280.37 148.26L96 300.11V464a16 16 0 0 0 16 16l112.06-.29a16 16 0 0 0 15.92-16V368a16 16 0 0 1 16-16h64a16 16 0 0 1 16 16v95.64a16 16 0 0 0 16 16.05L464 480a16 16 0 0 0 16-16V300L295.67 148.26a12.19 12.19 0 0 0-15.3 0zM571.6 251.47L488 182.56V44.05a12 12 0 0 0-12-12h-56a12 12 0 0 0-12 12v72.61L318.47 43a48 48 0 0 0-61 0L4.34 251.47a12 12 0 0 0-1.6 16.9l25.5 31A12 12 0 0 0 45.15 301l235.22-193.74a12.19 12.19 0 0 1 15.3 0L530.9 301a12 12 0 0 0 16.9-1.6l25.5-31a12 12 0 0 0-1.7-16.93z"></path></svg>
                    <span>Home</span>
                </a>
            </li>

            <li class="sidebar-item  ">
                <a href="/page/contacts" class='sidebar-link'>
                    <svg class="svg-inline--fa fa-phone fa-w-16 fa-fw select-all" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="phone" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M493.4 24.6l-104-24c-11.3-2.6-22.9 3.3-27.5 13.9l-48 112c-4.2 9.8-1.4 21.3 6.9 28l60.6 49.6c-36 76.7-98.9 140.5-177.2 177.2l-49.6-60.6c-6.8-8.3-18.2-11.1-28-6.9l-112 48C3.9 366.5-2 378.1.6 389.4l24 104C27.1 504.2 36.7 512 48 512c256.1 0 464-207.5 464-464 0-11.2-7.7-20.9-18.6-23.4z"></path></svg>
                    <span>Contacts</span>
                </a>
            </li>

            <li class="sidebar-item  ">
                <a href="/page/create_user" class='sidebar-link'>
                    <svg class="svg-inline--fa fa-user-plus fa-w-20 fa-fw select-all" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user-plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><path fill="currentColor" d="M624 208h-64v-64c0-8.8-7.2-16-16-16h-32c-8.8 0-16 7.2-16 16v64h-64c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h64v64c0 8.8 7.2 16 16 16h32c8.8 0 16-7.2 16-16v-64h64c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm-400 48c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>
                    <span>SignUp</span>
                </a>
            </li>

            <li class="sidebar-item  ">
                <a href="/page/login" class='sidebar-link'>
                    <svg class="svg-inline--fa fa-user-lock fa-w-20 fa-fw select-all" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user-lock" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" data-fa-i2svg=""><path fill="currentColor" d="M224 256A128 128 0 1 0 96 128a128 128 0 0 0 128 128zm96 64a63.08 63.08 0 0 1 8.1-30.5c-4.8-.5-9.5-1.5-14.5-1.5h-16.7a174.08 174.08 0 0 1-145.8 0h-16.7A134.43 134.43 0 0 0 0 422.4V464a48 48 0 0 0 48 48h280.9a63.54 63.54 0 0 1-8.9-32zm288-32h-32v-80a80 80 0 0 0-160 0v80h-32a32 32 0 0 0-32 32v160a32 32 0 0 0 32 32h224a32 32 0 0 0 32-32V320a32 32 0 0 0-32-32zM496 432a32 32 0 1 1 32-32 32 32 0 0 1-32 32zm32-144h-64v-80a32 32 0 0 1 64 0z"></path></svg>
                    <span>Login</span>
                </a>
            </li>

        </ul>
    </div>
</div>
    </div>
        <div id="main" class='layout-navbar navbar-fixed'>
            <header>
                <nav class="navbar navbar-expand navbar-light navbar-top">
                    <div class="container-fluid">
                        <a href="#" class="burger-btn d-block">
                            <i class="bi bi-justify fs-3"></i>
                        </a>

                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <?php 
                        // if (session_id()=="") session_start();
                        if(isset($_SESSION["auth"]) && $_SESSION["auth"]=="true") { ?>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent" style="justify-content:flex-end;">
                            <ul id="notificationbell" class="navbar-nav ms-auto mb-lg-0" style="dsplay:none !important;">
                                <li class="nav-item dropdown me-3">
                                    <a class="nav-link active dropdown-toggle text-gray-600" href="#" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                        <i class='bi bi-bell bi-sub fs-4'></i>
                                        <span id="badgenotifications" class="badge badge-notification bg-danger"></span>
                                    </a>
                                    <ul id="notificationsid" class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="dropdownMenuButton">

                                    </ul>
                                </li>
                            </ul>
                            <div class="dropdown">
                                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <div class="user-menu d-flex" style="align-items:center;">
                                        <div class="user-name text-end me-3">
                                            <h6 class="mb-0 text-gray-600"><?= $_SESSION["name"] ?></h6>
                                            <?php if($_SESSION["type"]=='admin'){ ?> <p class="mb-0 text-sm text-gray-600">Administrator</p> <?php } ?>
                                        </div>
                                        <div class="user-img d-flex align-items-center">
                                            <div class="avatar avatar-md">
                                                <img src="/uploads/profiles/<?= $_SESSION["photo"] ?>">
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton" style="min-width: 11rem;">
                                    <li>
                                        <a class="dropdown-item" href="/page/edit_user">
                                            <i class="icon-mid bi bi-person me-2"></i>My Profile
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="/logout">
                                            <i class="icon-mid bi bi-box-arrow-left me-2"></i> Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <?php }else{ ?>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav ms-auto mb-lg-0">
                            </ul>
                            <div class="user-menu d-flex" style="align-items:center;">
                                <div class="user-name text-end me-3 d-flex" style="gap:10px;">
                                   <div><a href="/page/login"><h6 class="mb-0 text-gray-600">Login</h6></a></div><div>/</div><div><a href="/page/create_user"><h6 class="mb-0 text-gray-600">Signup</h6></a></div>
                                </div>
                                <div class="user-img d-flex align-items-center">
                                    <div class="avatar avatar-md">
                                        <a href="/page/login"><img src="/uploads/profiles/136.jpg"></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php } ?>
                    </div>
                </nav>
            </header>
            <div id="main-content">

<div id="notificationsource" style="display:none;">
    <li class="dropdown-item notification-item">
        <a class="d-flex align-items-center" href="#">
            <div class="avatar avatar-md">
                <img>
            </div>
            <div class="notification-text ms-1">
                <p class="notification-title font-bold"></p>
                <form action="/conversations" method="post">
                    @csrf
                    <input type="hidden" name="friendId" id="inputid">
                    <input type="hidden" name="friendPhoto" id="inputphoto">
                </form>
            </div>
        </a>
    </li>
</div>

<?php if(isset($_SESSION["auth"]) && $_SESSION["auth"]=="true" && isset($_SESSION["blocked"]) && $_SESSION["blocked"]==0) { ?>

    <script>
        function updateTime() {
            fetch("/updatetime").then(response => response.json()).then(data=>{});
        }
        updateTime();
        setInterval(updateTime, 15000); //every 15 seconds
    </script>
    <script>
        function myNotifications() {
            fetch("/getnotifications").then(response => response.json())
            .then(notifications=>{
                let total = 0;
                document.getElementById("notificationsid").innerHTML="";
                notifications.forEach(row => {
                    total += parseInt(row.count, 10);
                    const newDiv = document.createElement("div");
                    newDiv.innerHTML = document.getElementById("notificationsource").innerHTML;
                    newDiv.querySelector("img").src = "/uploads/profiles/"+ row.users_img_w;
                    newDiv.querySelector("#inputid").value = row.users_id_w;
                    newDiv.querySelector("#inputphoto").value = row.users_img_w;
                    newDiv.querySelector("p").innerHTML = row.users_name_w + ' <span class="badge badge-notification bg-danger">'+row.count+'</span></span>';
                    newDiv.querySelector("a").addEventListener('click',()=>{notificationclick(newDiv.querySelector("form"))});
                    document.getElementById("notificationsid").prepend(newDiv);
                });
                if(parseInt(total, 10)>0){
                    document.getElementById("badgenotifications").innerHTML = parseInt(total, 10);
                    document.getElementById("notificationbell").style.display = '';
                }
                else{
                    document.getElementById("notificationbell").style.display = 'none';
                }
            });
        }
        myNotifications();
        setInterval(myNotifications, 5000); //every 5 seconds

        function notificationclick(form){
            form.submit();
        }
    </script>

<?php } ?>