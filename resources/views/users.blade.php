@include( 'partials.header' )

<?php
    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true" || !isset($_SESSION["verified"]) || $_SESSION["verified"]==0){
        exit("Activate your account !");
    }

    $name = isset($_GET["name"]) ? trim($_GET["name"]):''; //if research happend
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
        <div class="card">
            <div class="card-body">




    <form method="get" class="form form-horizontal"> <input name="title" type="hidden" value="<?= $_GET["title"] ?>">
        <div class="form-body">
            <div style="display:flex;">
                <div class="form-group has-icon-left" style="margin-bottom:0;">
                    <div class="position-relative">
                        <input name="name" value="<?= $name ?>" type="text" class="form-control" placeholder="Name" id="first-name-horizontal-icon">
                        <div class="form-control-icon">
                            <i class="bi bi-person"></i>
                        </div>
                    </div>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <div style="margin-left:1rem;"><a class="btn btn-secondary" href="/page/users?title=<?= $_GET["title"] ?>&include=1" role="button">All</a></div>
                </div>
            </div>

            <?php if($_GET["title"]=="Users") { ?>
            <div class="form-group">
                <div class='form-check'>
                    <div class="checkbox">
                        <input type="checkbox" id="checkbox2" class='form-check-input' <?php if(isset($_GET["include"])){ ?> checked <?php } ?> >
                        <label for="checkbox2">Include friends</label>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </form>
    

                <table class="table table-striped userstable" id="table1">
                    <tbody>


                        
<?php include ("conn.blade.php");
$q = "select id,name,img,mail,friends,blocked,type,status from users where id<>'".$_SESSION["id"]."'";
$q .= " AND name like '%".$name."%'";
$d1 = mysqli_query ($c, $q." ORDER BY id DESC");

$d2=mysqli_query ($c, "select friends from users WHERE id='".$_SESSION["id"]."'");
$data= mysqli_fetch_array($d2);
$MyfriendsArray = json_decode($data["friends"], true);

mysqli_close($c);

while($r= mysqli_fetch_array ($d1))
{
    $notShow = $_GET["title"]=="Friends" && !isset($MyfriendsArray[$r["id"]]); //in Friends page if not in friends list
    $notShow = $notShow || $_GET["title"]=="Users" && isset($MyfriendsArray[$r["id"]]) && !isset($_GET["include"]); //in Users page if in friends list but include is not checked
    $notShow = $notShow || $r["blocked"]==1 && isset($_SESSION["type"]) && $_SESSION["type"]=="user"; //in users/friends pages if it's blocked and i'm not admin 
    if($notShow) continue;

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
        <img class="rounded" style="border:solid; object-fit:contain; background-color:black" src="/uploads/profiles/<?= $r["img"] ?>" width="100" height="100" alt="photo<?= $r["id"] ?>">
    </td>
    <td style="display:flex; justify-content:space-evenly; padding:15px 0;"><div><?= $r["name"] ?></div> <div><?= $r["mail"] ?></div></td>
    <?php if($_GET["title"]=="Friends") { ?>
        <td rowspan="2">
        <?php if($r["status"]=='active'){ ?>
            <span class="badge bg-success">Active</span>
        <?php }else{ ?>
            <span class="badge bg-danger">Inactive</span>
        <?php } ?>
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
                <a class="btn btn-warning" href="/page/gallery?user=<?= $r["id"] ?>" style="border-radius:6px;">Gallery</a> | 
            <?php }else{ ?>
                <a class="btn btn-secondary disabled" style="border-radius:6px;">Gallery</a> | 
            <?php } ?>
        <?php } ?>

        <?php if($_GET["title"]=="Users" && $imAdmin) { ?>
            <?php if($r["blocked"]==1) { ?>
                <a href="/unblockuser?unblockuser=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn" style="background-color:purple; color:white; border-radius:6px;">UnBlock</a> | 
            <?php }else{ ?>
                <a href="/blockuser?blockuser=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn" style="background-color:purple; color:white; border-radius:6px;">Block</a> | 
            <?php } ?>
        <?php } else if($_GET["title"]=="Friends") { ?>
            <?php if(isset($MyfriendsArray) && $MyfriendsArray[$r["id"]]==1) { ?>
                <a href="/unblockfriend?unblockfriend=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn" style="background-color:purple; color:white; border-radius:6px;">UnBlock</a> | 
            <?php }else{ ?>
                <a href="/blockfriend?blockfriend=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn" style="background-color:purple; color:white; border-radius:6px;">Block</a> | 
            <?php } ?>
        <?php } ?>

        <?php if(isset($MyfriendsArray[$r["id"]])) { ?>
            <a href="/unfriend?unfriend=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>&img=<?= $r["img"] ?>" class="btn btn-danger" style="border-radius:6px;" onclick="return confirm('Remove from Friends list ?');">Unfriend</a>
        <?php }else{ ?>
            <a href="/befriend?befriend=<?= $r["id"] ?>&title=<?= $_GET["title"] ?>" class="btn btn-success" style="border-radius:6px;">Befriend</a>
        <?php } ?>

    </td>

</tr>

<?php } ?>

                    </tbody>
                </table>
            </div>
        </div>

    </section>
</div>

      

@include( 'partials.footer' )