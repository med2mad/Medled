<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; 

Route::get('/', function () {
    return view('index');
});

Route::get('/page/{page}', function ($page) {
    return view($page);
});

Route::post('/posts', function () {
    return view('posts');
});

Route::post('/post', function () {
    $message = trim($_POST["message"]);
    if(!strlen($message)){
        return view('posts');
    }
    
    include ("conn.blade.php");
    if(!$c){
        mysqli_close($c); exit(mysqli_connect_error());
    }

    $friend = mysqli_real_escape_string ($c , $_POST["friend"]) ;
    $d = mysqli_query ($c, "select name,mail,img from users where id = '".$friend."'");
    if(mysqli_num_rows($d)!=1){
        exit("404 post");
    }
    $r= mysqli_fetch_array($d);

    if (session_id()=="") session_start();
    $message = mysqli_real_escape_string($c , $message) ;
    $name_w = mysqli_real_escape_string ($c , $_SESSION["name"]) ;
    $mail_w = mysqli_real_escape_string ($c , $_SESSION["mail"]) ;
    $name_r = mysqli_real_escape_string ($c , $r["name"]) ;
    $mail_r = mysqli_real_escape_string ($c , $r["mail"]) ;
    $img_r = mysqli_real_escape_string ($c , $r["img"]) ;

    $query="insert into posts(message,file,users_id_w,users_name_w,users_mail_w,users_img_w,users_id_r,users_name_r,users_mail_r,users_img_r)
                        values('".$message."','','".$_SESSION["id"]."','".$name_w."','".$mail_w."','".$_SESSION["photo"]."','".$friend."','".$name_r."','".$mail_r."','".$img_r."')";
    mysqli_query ($c, $query);
    mysqli_close($c);
    return ["message"=>$message];
});

Route::get('/deletepost', function () {
    if (session_id()=="") session_start();
    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true" || !isset($_SESSION["verified"]) || $_SESSION["verified"]==0){
        exit("404 6");
    }

    include ("conn.blade.php");
     if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{
        mysqli_query ($c, "delete from posts where id=" . $_GET["deletemessage"] . " and (users_id_w=".$_SESSION["id"]." or users_id_r=".$_SESSION["id"].")") ;
        if(mysqli_affected_rows($c)<1){mysqli_close($c);exit("404 7");} 
        mysqli_close($c);
        if(isset($_GET["file"]) && $_GET["file"]!=""){unlink("uploads/posts/".$_GET["file"]);}
        return view('posts');
    }
});

Route::post('/create_gallery', function () {
    $newimgname = "";
    $filecount = count($_FILES["img"]["name"]);
    
    for ($i=0; $i<$filecount ; $i++) { 
        if(isset($_FILES["img"]["name"][$i]) && $_FILES["img"]["error"][$i]===0 && $_FILES["img"]["size"][$i]<11048576){
            $name=pathinfo($_FILES["img"]["name"][$i], PATHINFO_FILENAME);
            $ext= strtolower(pathinfo($_FILES["img"]["name"][$i], PATHINFO_EXTENSION));
            $exts=["png","jpg","jpeg","bmp","gif"];
            if(in_array($ext,$exts))
            {
                $newimgname=$name."_".rand(1000, 9999).".".$ext;
                move_uploaded_file($_FILES["img"]["tmp_name"][$i], "uploads/gallery/".$newimgname);
            }
        }
        else{
            return view("create_gallery");
        }
    
        include ("conn.blade.php");
        if (session_id()=="") session_start();
        $query="insert into gallery(img, time, text, user)values('".$newimgname."', now(), '', '". $_SESSION["id"] ."')";
        mysqli_query($c, $query);
        mysqli_close($c);
    }

    return view("gallery");
});

Route::get('/deletegallery', function () {
    if (session_id()=="") session_start();
    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true" || !isset($_SESSION["verified"]) || $_SESSION["verified"]==0){
        exit("404 8");
    }
    if(isset($_SESSION["type"]) && $_SESSION["type"]=="user"){ //if user is not admin he cannot delete others gallery
        if($_GET["user"]!=$_SESSION["id"]){
            exit("404 9");
        }
    }

    include ("conn.blade.php");
     if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{
        mysqli_query ($c, "delete from gallery where id=" . $_GET["id"]) ;
        if(mysqli_affected_rows($c)<1){mysqli_close($c); return view("gallery");} 
        mysqli_close($c);
        unlink("uploads/gallery/".$_GET["img"]);
        return view("gallery");
    }
});

Route::get('/befriend', function () {
    if (session_id()=="") session_start();

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("404 3");
    }

    include ("conn.blade.php");
    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{ 
        $d = mysqli_query ($c, "select friends from users where id = '".$_SESSION["id"]."'");
        if(mysqli_num_rows($d)!=1){
            exit("404 block");
        }
    
        $r= mysqli_fetch_array($d);
        $phpArray = json_decode($r["friends"], true);
        $phpArray[$_GET["id"]] = 0;

        mysqli_query ($c, "update users set friends='".json_encode($phpArray)."' where id=" . $_SESSION["id"]) ;
        mysqli_close($c);
        return view('users');
    }
});

Route::get('/unfriend', function () {
    if (session_id()=="") session_start();

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("404 3");
    }

    include ("conn.blade.php");
    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{ 
        $d = mysqli_query ($c, "select friends from users where id = '".$_SESSION["id"]."'");
        if(mysqli_num_rows($d)!=1){
            exit("404 block");
        }
    
        $r= mysqli_fetch_array($d);
        $phpArray = json_decode($r["friends"], true);
        unset($phpArray[$_GET["id"]]);

        mysqli_query ($c, "update users set friends='".json_encode($phpArray)."' where id=" . $_SESSION["id"]) ;
        mysqli_close($c);
        return view('users');
    }
});  

Route::get('/blockuser', function () {
    if (session_id()=="") session_start();

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true" || !isset($_SESSION["type"]) || $_SESSION["type"]!="admin"){
        exit("404 blockuser");
    }

    include ("conn.blade.php");
    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{
        mysqli_query ($c, "update users set blocked='1' where id=" . $_GET["id"]) ;
        mysqli_close($c);
        return view('users');
    }
});

Route::get('/unblockuser', function () {
    if (session_id()=="") session_start();

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true" || !isset($_SESSION["type"]) || $_SESSION["type"]!="admin"){
        exit("404 unblockuser");
    }

    include ("conn.blade.php");
    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{
        mysqli_query ($c, "update users set blocked='0' where id=" . $_GET["id"]) ;
        mysqli_close($c);
        return view('users');
    }
});

Route::get('/blockfriend', function () {
    if (session_id()=="") session_start();

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("404 4");
    }

    include ("conn.blade.php");
    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{
        $d = mysqli_query ($c, "select friends from users where id = '".$_SESSION["id"]."'");
        if(mysqli_num_rows($d)!=1){
            exit("404 block");
        }

        $r= mysqli_fetch_array($d);
        $phpArray = json_decode($r["friends"], true);
        $phpArray[$_GET["id"]]=1;

        mysqli_query ($c, "update users set friends='".json_encode($phpArray)."' where id=" . $_SESSION["id"]) ;
        mysqli_close($c);
        return view('users');
    }
});

Route::get('/unblockfriend', function () {
    if (session_id()=="") session_start();

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("404 5");
    }

    include ("conn.blade.php");
    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{
        $d = mysqli_query ($c, "select friends from users where id = '".$_SESSION["id"]."'");
        if(mysqli_num_rows($d)!=1){
            exit("404 unblock");
        }

        $r= mysqli_fetch_array($d);
        $phpArray = json_decode($r["friends"], true);
        $phpArray[$_GET["id"]]=0;

        mysqli_query ($c, "update users set friends='".json_encode($phpArray)."' where id=" . $_SESSION["id"]) ;
        mysqli_close($c);
        return view('users');
    }
});

Route::post('/signup', function () {
    if($_POST["page"] != "create_user" && $_POST["page"] != "edit"){
        exit("404 1");
    }

    $name = trim($_POST["name"]);
    $mail = trim($_POST["mail"]);

    if(empty($name) || empty($mail) || empty($_POST["pass"]) || empty($_POST["pass2"])){
        exit("404 empty !");
    }
    elseif (mb_strlen($name,"UTF-8") < 5 || mb_strlen($name,"UTF-8") > 20) {
        exit("404 name 5 to 20");
    }
    elseif (mb_strlen($_POST["pass"],"UTF-8") < 5 || mb_strlen($_POST["pass"],"UTF-8") > 20) {
        exit("404 pass 5 to 20");
    }
    elseif (mb_strlen($_POST["pass2"],"UTF-8") < 5 || mb_strlen($_POST["pass2"],"UTF-8") > 20) {
        exit("404 pass2 5 to 20");
    }
    elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        exit("404 Email not valid!");
    }
    elseif($_POST["pass"] !== $_POST["pass2"]){
        return view($_POST["page"], ['error'=>'Password and Confirmation not identical']);
    }
    elseif(isset($_POST["signup"]) && !$_POST["conditions"]){
        return view($_POST["page"], ['error'=>'Accept conditions']);
    }

    include ("conn.blade.php");
    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{
        $name = mysqli_real_escape_string ($c , $name) ;
        $mail = mysqli_real_escape_string ($c , $mail) ;
        $pass = mysqli_real_escape_string ($c , $_POST["pass"] ) ;

        if (session_id()=="") session_start();

        if($_POST["page"]=="create_user" || trim($_POST["mail"])!=$_SESSION["mail"]) {
            $d = mysqli_query ($c, "select id from users where mail = '".$mail."' limit 3");
            if(mysqli_num_rows($d)>=2) {
                return view($_POST["page"], ['error'=>'1 email address can be used to create only 2 accounts maximum']);
            }
            $_SESSION["verified"]=0;
        }
        if($_POST["page"]=="create_user" || trim($_POST["name"])!=$_SESSION["name"]) {
            $d = mysqli_query ($c, "select id from users where name = '".$name."' limit 1");
            if(mysqli_num_rows($d)>0) {
                return view($_POST["page"], ['error'=>'Already existing name"']);
            }
        }

        $token = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

        $newimgname = ($_POST["page"]=="create_user") ? "136.jpg" : $_SESSION["photo"];
        if(isset($_FILES["photo"]) && $_FILES["photo"]["error"]===0 && $_FILES["photo"]["size"]<11048576){
            $ext= strtolower(pathinfo( $_FILES["photo"]["name"], PATHINFO_EXTENSION));
            $exts=["png","jpg","jpeg","bmp","gif"];
            if(in_array($ext,$exts))
            {
                $newimgname=uniqid("IMG-",true).".".$ext;
                move_uploaded_file($_FILES["photo"]["tmp_name"], "uploads/profiles/".$newimgname);
            }
        }

        if($_POST["page"]=="create_user"){
            $query="insert into users(name,pass,mail,img,token)values('".$name."','".$pass."','".$mail."','".$newimgname."','$token')";
        }
        else{
            $query="update users set name='".$name."', pass='".$pass."', mail='".$mail."', img='".$newimgname."', verified='".$_SESSION["verified"]."', token='".$token."' where id='".$_SESSION["id"]."'";
        }
        mysqli_query($c, $query);
        mysqli_close($c);

        if($_POST["page"]=="create_user"){
            session_unset();
            session_destroy();
            return view('signup0', ['name'=>$_POST["name"], 'pass'=>$_POST["pass"], 'mail'=>$_POST["mail"]]);
        }
        else{
            $_SESSION["name"]=$_POST["name"];
            $_SESSION["mail"]=$_POST["mail"];
            $_SESSION["pass"]=$_POST["pass"];
            $_SESSION["token"]=$token;
            $_SESSION["photo"]=$newimgname;
    
            if($_SESSION["verified"]==0)
                return view('signup1');
            else
                return view('index');
        }

        exit;
    }
});

Route::post('/login', function () {
    include ("conn.blade.php");

    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else
	{
        $name = mysqli_real_escape_string ($c , trim($_POST["profilname"])) ;
        $pass = mysqli_real_escape_string ($c , $_POST["profilpass"] ) ;

        $query="select * from users where name='".$name."' and pass = '".$pass."' limit 1";
        $d = mysqli_query ($c, $query);
		if(mysqli_num_rows($d)==1)
		{
            $r= mysqli_fetch_array ($d);
            
            if (session_id()=="") session_start();
            $_SESSION["auth"]="true";
            $_SESSION["id"]=$r["id"];
            $_SESSION["name"]=$r["name"];
            $_SESSION["mail"]=$r["mail"];
            $_SESSION["pass"]=$r["pass"];
            $_SESSION["photo"]=$r["img"];
            $_SESSION["type"]=$r["type"];
            $_SESSION["token"]=$r["token"];
            $_SESSION["blocked"]=$r["blocked"];
            $_SESSION["verified"]=$r["verified"];
            $_SESSION["notif"]=0;

            if($r["verified"]==0){
                return view('signup1');
            }
            
            $d = mysqli_query ($c, "select count(red) from posts where users_id_r='".$r["id"]."' and red=0");
            $_SESSION["notif"]=mysqli_fetch_array($d)[0];
            
            mysqli_close($c);
            return view('index');
		}
		else
		{
            mysqli_close($c);
            return view('login', ['errorlogin'=>'Incorrect Name or Password']);
		}
	}
});

Route::get('/logout', function () {
    if (session_id()=="") session_start();
    session_unset();
    session_destroy();
    return view('index');
});

Route::get('/gettimes', function(Request $request){
    include ("conn.blade.php");
    $d = mysqli_query ($c, "select id,time from users where id IN (-1".$request->input('q').")");
    mysqli_close($c);
    $friends = [];
    while($r = mysqli_fetch_array($d)){
        $friends[$r["id"]] = $r["time"];
    }

    return $friends;
});

Route::get('/updatetime', function(){
    include ("conn.blade.php");
    if (session_id()=="") session_start();
    mysqli_query ($c, "update users set time=now() where id=" . $_SESSION["id"]);
    mysqli_close($c);
    return '[]';
});