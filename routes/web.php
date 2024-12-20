<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request; 

Route::get('/', function () {
    return view('index');
});

Route::get('/room', function (Request $request) {
    return view('room', ['room'=>$request->input('room')]);
});
Route::get('/chatbot', function (Request $request) {
    return view('room', ['chatbot'=>1]);
});

Route::get('/page/{page}', function ($page) {
    return view($page);
});

Route::post('/conversations', function () {
    if (session_id()=="") session_start();
    $_SESSION["friendPhoto"] = "136.jpg"; $_SESSION["friendId"] = -1; $_SESSION["perpage"]=10;
    if(isset($_POST["perpage"])) {$_SESSION["perpage"]=$_POST["perpage"];}
    if(isset($_POST["friendId"])) {$_SESSION["friendId"]=$_POST["friendId"];}
    if(isset($_POST["friendPhoto"])) {$_SESSION["friendPhoto"]=$_POST["friendPhoto"];}
    return redirect()->route('routename')->with('friendId', $_SESSION["friendId"])->with('perpage', $_SESSION["perpage"])->with('friendPhoto', $_SESSION["friendPhoto"]);
});
Route::get('/form-success', function(){ //Redirect After Post (PRG Pattern)
    return view('conversations', ["friendId"=>session('friendId'), "perpage"=>session('perpage'), "friendPhoto"=>session('friendPhoto')]);
})->name('routename');

Route::post('/create_conversation', function () {
    $message = $_POST["message"];

    include ("conn.blade.php");
    if(!$c){
        mysqli_close($c); exit(mysqli_connect_error());
    }

    $d = mysqli_query ($c, "select name,img from users where id = '".$_POST["friendId"]."'");
    if(mysqli_num_rows($d)!=1){
        exit("404 post");
    }
    $r= mysqli_fetch_array($d);

    if (session_id()=="") session_start();
    $message = mysqli_real_escape_string($c , $message) ;
    $name_w = mysqli_real_escape_string ($c , $_SESSION["name"]) ;
    $name_r = mysqli_real_escape_string ($c , $r["name"]) ;
    $img_r = $r["img"] ;

    $query="insert into conversations(message,users_id_w,users_name_w,users_img_w,users_id_r,users_name_r,users_img_r)
                        values('".$message."','".$_SESSION["id"]."','".$name_w."','".$_SESSION["photo"]."','".$_POST["friendId"]."','".$name_r."','".$img_r."')";
    mysqli_query ($c, $query);
    $id = mysqli_insert_id($c);
    mysqli_close($c);
    
    return ["id"=>$id];
});

Route::post('/deleteconversation', function () {
    if (session_id()=="") session_start();
    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("404 6");
    }

    include ("conn.blade.php");
     if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    else{
        mysqli_query ($c, "delete from conversations where id=" . $_POST["messageid"]) ;
        if(mysqli_affected_rows($c)<1){mysqli_close($c); exit("404 7");} 
        mysqli_close($c);
        return ['deleted' => 1];
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
            return view("gallery");
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
    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
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
        // unlink("uploads/gallery/".$_GET["img"]);
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
        return ["id" => $_GET["id"]];
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
        return ["id" => $_GET["id"]];
    }
});

Route::post('/signup', function () {
    if($_POST["page"] != "create_user" && $_POST["page"] != "edit"){
        exit("404 1");
    }

    $name = trim($_POST["name"]);

    if(empty($name) || empty($_POST["pass"]) || empty($_POST["pass2"])){
        exit("404 empty !");
    }
    elseif (mb_strlen($name,"UTF-8") < 5 || mb_strlen($name,"UTF-8") > 20) {
        exit("404 name 5 to 20");
    }
    elseif (mb_strlen($_POST["pass"],"UTF-8") < 5 || mb_strlen($_POST["pass"],"UTF-8") > 20) {
        exit("404 pass 5 to 20");
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
        $pass = mysqli_real_escape_string ($c , $_POST["pass"] ) ;

        if (session_id()=="") session_start();

        if($_POST["page"]=="create_user" || trim($_POST["name"])!=$_SESSION["name"]) {
            $d = mysqli_query ($c, "select id from users where name = '".$name."' limit 1");
            if(mysqli_num_rows($d)>0) {
                return view($_POST["page"], ['error'=>'Already existing name"']);
            }
        }

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
            $query="insert into users(name,pass,img)values('".$name."','".$pass."','".$newimgname."')";
        }
        else{
            $query="update users set name='".$name."', pass='".$pass."', img='".$newimgname."' where id='".$_SESSION["id"]."'";
        }
        mysqli_query($c, $query);
        mysqli_close($c);

        return redirect()->route('routenamelogin')->with('name', trim($_POST["name"]))->with('pass', $_POST["pass"])->with('page', $_POST["page"]);
    }
});

Route::post('/login', function () {
    return redirect()->route('routenamelogin')->with('name', $_POST["name"])->with('pass', $_POST["pass"])->with('page', $_POST["page"]);
});
Route::get('/routenamelogin', function(){ //Redirect After Post (PRG Pattern)
    if (session_id()=="") session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"]=="true" && session('page')==null) {
        return view('index');
    }

    include ("conn.blade.php");
    if(!$c){mysqli_close($c); 
        exit(mysqli_connect_error());
    }
    $query="select * from users where name='".mysqli_real_escape_string($c, trim(session('name')))."' and pass = '".session('pass')."' limit 1";
    $d = mysqli_query ($c, $query);
    
    if(mysqli_num_rows($d)==1)
    {
        $r= mysqli_fetch_array ($d);
        $_SESSION["auth"]="true";
        $_SESSION["id"]=$r["id"];
        $_SESSION["name"]=$r["name"];
        $_SESSION["pass"]=$r["pass"];
        $_SESSION["photo"]=$r["img"];
        $_SESSION["type"]=$r["type"];
        $_SESSION["blocked"]=$r["blocked"];
        $_SESSION["friendId"]=0;
        $_SESSION["friendPhoto"]="";
        mysqli_close($c);
    }
    else
    {
        mysqli_close($c);
        return view('login', ['errorlogin'=>'Incorrect Name or Password']);
    }

    return view('index');
})->name('routenamelogin');

Route::get('/logout', function () {
    if (session_id()=="") session_start();
    session_unset();
    session_destroy();
    return view('index');
});

Route::get('/gettime', function(Request $request){
    if (session_id()=="") session_start();
    if (isset($_SESSION["auth"]) && $_SESSION["auth"]=="true" && isset($_SESSION["id"]) && $_SESSION["id"]){
        include ("conn.blade.php");
        $d = mysqli_query ($c, "select time from users where id = ".$request->input('q'));
        mysqli_close($c);
        $r = mysqli_fetch_array($d);
        return ["oldtime"=>$r["time"], "curranttime"=>time()];
    }
    else{
        return response()->json(['error' => 'login'], 401);
    }
});

Route::get('/gettimes', function(Request $request){
    if (session_id()=="") session_start();
    if (isset($_SESSION["auth"]) && $_SESSION["auth"]=="true" && isset($_SESSION["id"]) && $_SESSION["id"]){
        include ("conn.blade.php");
        $d = mysqli_query ($c, "select id,time from users where id IN (-1".$request->input('q').")");
        mysqli_close($c);
        $friends = [];
        while($r = mysqli_fetch_array($d)){
            $friends[$r["id"]] = $r["time"];
        }
        return  ["friends"=>$friends, "curranttime"=>time()];
    }
    else{
        return response()->json(['error' => 'login'], 401);
    }
});

Route::get('/getmessages', function(Request $request){
    if (session_id()=="") session_start();
    if (isset($_SESSION["auth"]) && $_SESSION["auth"]=="true" && isset($_SESSION["id"]) && $_SESSION["id"]){
        include ("conn.blade.php");
        $q = "select id,message from conversations where red = 0 and users_id_w = " . $request->input('friendId') ." and users_id_r = " . $_SESSION["id"] . " ORDER BY id DESC";
        $d = mysqli_query ($c, $q);
        $messages = [];
        while($r = mysqli_fetch_array($d)){
            $messages[$r["id"]] = $r["message"];
        }
        mysqli_query ($c, "update conversations set red=1 where users_id_w=" . $request->input('friendId') . " and users_id_r = " . $_SESSION["id"] ) ;
        mysqli_close($c);
        return $messages;
    }
    else{
        return response()->json(['error' => 'login'], 401);
    }
});

Route::get('/getnotifications', function(){
    if (session_id()=="") session_start();
    if (isset($_SESSION["auth"]) && $_SESSION["auth"]=="true" && isset($_SESSION["id"]) && $_SESSION["id"]){
        include ("conn.blade.php");
        $q = "select users_id_w, users_name_w, users_img_w, count(id) as count from conversations where red = 0 and users_id_r=".$_SESSION["id"]." GROUP BY users_id_w ORDER BY id DESC";
        $d = mysqli_query ($c, $q);
        $notifications = array();
        while ($row = mysqli_fetch_array($d, MYSQLI_ASSOC)) {
            $notifications[] = $row;
        }
        return $notifications;
    }
    else{
        return response()->json(['error' => 'login'], 401);
    }
});

Route::get('/updatetime', function(){
    if (session_id()=="") session_start();
    if (isset($_SESSION["auth"]) && $_SESSION["auth"]=="true" && isset($_SESSION["id"]) && $_SESSION["id"]){
        include ("conn.blade.php");
        mysqli_query ($c, "update users set time=UNIX_TIMESTAMP() where id=" . $_SESSION["id"]);
        mysqli_close($c);
        return ["1"=>1];
    }
    else{
        return response()->json(['error' => 'login'], 401);
    }
});