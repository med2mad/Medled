@include( 'partials.header' )

<?php
    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true" || !isset($_SESSION["verified"]) || $_SESSION["verified"]==0){
        exit("Activate your account !");
    }
    $searchName = isset($_GET["searchName"]) ? trim($_GET["searchName"]):''; //if search happend
    $include = isset($_GET["include"]) && (($_GET["include"]=="on" ||  $_GET["include"]==1 ||  $_GET["include"]=='checked'));
?>

<?php 
include ("conn.blade.php");
$d=mysqli_query ($c, "select friends from users WHERE id='".$_SESSION["id"]."'");
$data= mysqli_fetch_array($d);
$MyfriendsArray = json_decode($data["friends"], true);

$q = "select count(id) from users where id<>'".$_SESSION["id"]."'";
$q .= " AND name like '%".$searchName."%'";
if(isset($_SESSION["type"]) && $_SESSION["type"]=="user") {$q .= " AND blocked = 0";};
if($_GET["title"]=="Users" && !$include) {
    $q .= " AND id NOT IN (-1";
    foreach ($MyfriendsArray as $key => $value) {
        $q .= ",".$key;
    }
    $q .= ")";
}
if($_GET["title"]=="Friends") {
    $q .= " AND id IN (-1";
    foreach ($MyfriendsArray as $key => $value) {
        $q .= ",".$key;
    }
    $q .= ")";
}
$d = mysqli_query($c, $q);
mysqli_close($c);
$perpage = 5;
if(isset($_GET["perpage"]) && is_numeric($_GET["perpage"])){
    $perpage = $_GET["perpage"];
}
$pagesnbr = ceil(mysqli_fetch_array($d)[0]/$perpage);

if(isset($_GET["page"]) && is_numeric($_GET["page"])){
    $currantpage = ceil($_GET["page"]);
    if($currantpage>0) {$debut = ($currantpage-1)*$perpage;}
    else {$debut = 0;}
}
else{
    $currantpage = 1;
    $debut = 0;
}
?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $_GET["title"] ?></h3>
            </div>
        </div>
    </div>
    <section class="section">


<div class="card mb-2" style="margin-right:auto; width:350px; border:1px solid rgb(230,230,230);">
<div class="card-body" style="padding:10px;">
    <form method="get" action="/page/users" class="form form-horizontal">
        <input name="title" type="hidden" value="<?= $_GET["title"] ?>">
        <input name="page" type="hidden" value="1">
        <input name="perpage" type="hidden" value="<?= $perpage ?>">

        <div class="form-body">
            <div style="display:flex;" class="mb-2">
                <div class="form-group has-icon-left" style="margin-bottom:0;">
                    <div class="position-relative">
                        <input name="searchName" value="<?= $searchName ?>" type="text" class="form-control" placeholder="Name" id="first-name-horizontal-icon">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>            
                <div style="margin-left:10px;">
                    <span class="btn btn-secondary" id="all">All</span>
                </div>
            </div>

        <div style="display:flex; align-items:flex-end;">
            <div style="width:150px; display:flex; align-items:flex-end;">
                <nobr>Per page : </nobr>
                <select id="perpage" onchange="refresh()" class="form-control" style="width:100px; margin-left:5px; padding:0 6px;">
                    <option value ="5" <?php if($perpage==5) {echo "selected";} ?> >5</option>
                    <option value="10" <?php if($perpage==10){echo "selected";} ?> >10</option>
                    <option value="15" <?php if($perpage==15){echo "selected";} ?> >15</option>
                    <option value="20" <?php if($perpage==20){echo "selected";} ?> >20</option>
                    <option value="30" <?php if($perpage==30){echo "selected";} ?> >30</option>
                </select>
            </div>
            <?php if($_GET["title"]=="Users") { ?>
            <div style="width:40px; text-align:center;">|</div>
            <div class="form-group mb-0" style="text-align:center;">
                <div class="form-check mb-0" class="form-group mb-0" style="text-align:left; padding-left:0">
                    <div class="checkbox">
                        <input name="include" type="checkbox" id="myCheckbox" class="form-check-input" <?php if($include){ ?> checked <?php } ?> >
                        <label for="myCheckbox" style="padding-left:5px;"> Include friends</label>
                    </div>
                </div>
            </div>
            <?php } ?>
            </div>
        </div>
    </form>
</div>
</div>

<div class="card" style="border:1px solid rgb(230,230,230);"> <?php $_SESSION["select"]='' ?>
    <div class="card-body">

<nav aria-label="Page navigation example">
    <ul class="pagination pagination-primary" style="justify-content:center; flex-wrap:wrap;">
        <li class="page-item">
            <span class="page-link" onclick="pagelink(1, <?= $perpage ?>)">
                <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
            </span>
        </li>

        <?php for ($i=1; $i<=$pagesnbr; $i++) { ?>
            <li class="page-item <?php if($currantpage==$i){ ?>active<?php } ?>">
                <span class="page-link" onclick="pagelink(<?= $i ?>, <?= $perpage ?>)"><?= $i ?></span>
            </li>
        <?php } ?> 

        <li class="page-item">
            <span class="page-link" onclick="pagelink(<?= $pagesnbr ?>, <?= $perpage ?>)">
                <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
            </span>
        </li>
    </ul>
</nav>


                <table class="table table-striped userstable" id="table1">
                    <tbody>


                        
<?php include ("conn.blade.php");
$q = "select * from users where id<>".$_SESSION["id"];
$q .= " AND name like '%".$searchName."%'";
if(isset($_SESSION["type"]) && $_SESSION["type"]=="user") {$q .= " AND blocked = 0";};
if($_GET["title"]=="Users" && !$include) {
    $q .= " AND id NOT IN (-1";
    foreach ($MyfriendsArray as $key => $value) {
        $q .= ",".$key;
    }
    $q .= ")";
}
if($_GET["title"]=="Friends") {
    $q .= " AND id IN (-1";
    foreach ($MyfriendsArray as $key => $value) {
        $q .= ",".$key;
    }
    $q .= ")";
}
$d1 = mysqli_query ($c, $q." ORDER BY id DESC LIMIT $perpage OFFSET $debut");

mysqli_close($c);

while($r= mysqli_fetch_array ($d1))
{ 
    // $notShow = $_GET["title"]=="Users" && isset($MyfriendsArray[$r["id"]]) && !$include; //Users page: is friend and include is not checked
    // $notShow = $notShow || $r["blocked"]==1 && isset($_SESSION["type"]) && $_SESSION["type"]=="user"; //users/friends pages: blocked by admin and i'm not admin 
    // if($notShow) continue;

    $name = htmlspecialchars($r["name"]);
    $mail = htmlspecialchars($r["mail"]);
    $colorRed = $_GET["title"]=="Friends" && isset($MyfriendsArray) && $MyfriendsArray[$r["id"]]==1 || $r["blocked"]==1 ? 'bg-danger bg-gradient' : '';
    $username = urlencode($r["name"]);
    $imAdmin = isset($_SESSION["type"]) && $_SESSION["type"]=="admin";
    $user_friends = json_decode($r["friends"], true);
        $is_friend_not_blocking_me = isset($user_friends[$_SESSION["id"]]) ? $user_friends[$_SESSION["id"]]==0:true;
?>

<tr>
    <td rowspan="2" width="120">
        <img style="border:solid; object-fit:contain; background-color:black; border-radius:50%;" src="/uploads/profiles/<?= $r["img"] ?>" width="100" height="100" alt="photo<?= $r["id"] ?>">
    </td>
    <td style="padding:5px 0;">
        <?= $name.($r['type']=='admin'?' (Admin)':'') ?> <br> <?= $mail ?>
    </td>
    <?php if($_GET["title"]=="Friends") { ?>
        <td rowspan="2" class="activetd" id="activetd<?= $r["id"] ?>">
            <span id="active<?= $r["id"] ?>" style="display:none" class="badge bg-success">Active</span>
            <span id="inactive<?= $r["id"] ?>" style="display:none" class="badge bg-light-secondary">Inactive</span>
            <?php $_SESSION["select"] .= ','.$r["id"] ?>
        </td>
    <?php } ?>
</tr>
<tr style="border-bottom:solid;">
    <td>
        <?php if(($_GET["title"]=="Friends") || $imAdmin) { ?>
            <?php if(($_SESSION["blocked"]==0 && $is_friend_not_blocking_me) || $imAdmin) { ?>
                <a class="btn btn-primary" style="border-radius:6px;" href="/page/create_post?id_r=<?=$r["id"]?>&name_r=<?=$username?>">Post</a> | 
            <?php }else{ ?>
                <a class="btn btn-secondary disabled" style="border-radius:6px;">Post</a> | 
            <?php } ?>
        <?php } ?>
        
        <?php if($_GET["title"]=="Friends" || $imAdmin) { ?>
            <a class="btn btn-success" href="/page/posts?name=<?=$username?>" style="border-radius:6px;">All Posts</a> | 
        <?php } ?>
        
        <?php if(($_GET["title"]=="Friends") || $imAdmin) { ?>
            <?php if(($_SESSION["blocked"]==0 && $is_friend_not_blocking_me) || $imAdmin) { ?>
                <a class="btn btn-warning" href="/page/gallery?user=<?= $r["id"] ?>&name=<?= $r["name"] ?>" style="border-radius:6px;">Gallery</a> | 
            <?php }else{ ?>
                <a class="btn btn-secondary disabled" style="border-radius:6px;">Gallery</a> | 
            <?php } ?>
        <?php } ?>

        <?php if($_GET["title"]=="Users" && $imAdmin) { ?>
            <?php if($r["blocked"]==1) { ?>
                <a href="/unblockuser?id=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn" style="background-color:purple; color:white; border-radius:6px;">UnBlock</a> | 
            <?php }else{ ?>
                <a href="/blockuser?id=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn" style="background-color:purple; color:white; border-radius:6px;">Block</a> | 
            <?php } ?>
        <?php } else if($_GET["title"]=="Friends") { ?>
            <?php if(isset($MyfriendsArray) && $MyfriendsArray[$r["id"]]==1) { ?>
                <a href="/unblockfriend?id=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn" style="background-color:purple; color:white; border-radius:6px;">UnBlock</a> | 
            <?php }else{ ?>
                <a href="/blockfriend?id=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn" style="background-color:purple; color:white; border-radius:6px;">Block</a> | 
            <?php } ?>
        <?php } ?>

        <?php if(isset($MyfriendsArray[$r["id"]])) { ?>
            <a href="/unfriend?id=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>&include=<?= $include ?>&img=<?= $r["img"] ?>" class="btn btn-danger" style="border-radius:6px;" onclick="return confirm('Remove from Friends list ?');"><?= $_GET["title"]=="Friends" ? 'Remove' : 'Unfriend' ?></a>
        <?php }else{ ?>
            <a href="/befriend?id=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>&include=<?= $include ?>" class="btn btn-success" style="border-radius:6px;">Befriend</a>
        <?php } ?>

    </td>

</tr>

<?php } ?>

                    </tbody>
                </table>


<nav aria-label="Page navigation example">
    <ul class="pagination pagination-primary" style="justify-content:center; flex-wrap:wrap;">
        <li class="page-item">
            <span class="page-link" onclick="pagelink(1, <?= $perpage ?>)">
                <span aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
            </span>
        </li>

        <?php for ($i=1; $i<=$pagesnbr; $i++) { ?>
            <li class="page-item <?php if($currantpage==$i){ ?>active<?php } ?>">
                <span class="page-link" onclick="pagelink(<?= $i ?>, <?= $perpage ?>)"><?= $i ?></span>
            </li>
        <?php } ?> 

        <li class="page-item">
            <span class="page-link" onclick="pagelink(<?= $pagesnbr ?>, <?= $perpage ?>)">
                <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
            </span>
        </li>
    </ul>
</nav>


            </div>
        </div>

    </section>
</div>

<?php if($_GET["title"]=="Friends") { ?> <!-- track if user still active -->

    <script>
        function beneath1Minute(time) {
            const differenceInMilliseconds = Math.abs(Date.now() - new Date(time).getTime());
            const differenceInMinutes = differenceInMilliseconds / (1000 * 60);
            return differenceInMinutes < 1;
        }

        function myFunction() {
            fetch("/gettimes?q=<?=$_SESSION["select"]?>").then(response => response.json())
            .then(response=>{
                for (let key in response) {
                    if(beneath1Minute(response[key])){
                        document.getElementById('activetd'+key).style.backgroundColor='#edfff6';
                        document.getElementById('active'+key).style.display='';
                        document.getElementById('inactive'+key).style.display='none';
                    }
                    else{
                        document.getElementById('activetd'+key).style.backgroundColor='';
                        document.getElementById('active'+key).style.display='none';
                        document.getElementById('inactive'+key).style.display='';
                    }
                }
            })
        }
        myFunction();

        setInterval(myFunction, 60000); //every minute
    </script>

<?php } ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('all').addEventListener('click', function() {
            let originalUrl = "/page/users?title=<?=$_GET["title"]?>&page=1&searchName=&perpage="+<?= $perpage ?>;
            send(originalUrl);
        });
    });

    function pagelink(page, perpage) {
        let originalUrl = "/page/users?title=<?=$_GET["title"]?>&page="+page+"&searchName=<?= $searchName ?>&perpage="+perpage;
        send(originalUrl);
    }

    function refresh(){
        const perpage = document.getElementById("perpage").value;
        if(perpage!=0){ pagelink(<?= $currantpage ?>, perpage) }
    }

    function send(originalUrl) {
        let isChecked = document.getElementById('myCheckbox').checked ? 1 : 0;
        let newUrl = `${originalUrl}&include=${isChecked}`;
        window.location.href = newUrl;
    }
</script>

@include( 'partials.footer' )