@include( 'partials.header' )

<?php 
    // if (session_id()=="") session_start();

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true" || !isset($_SESSION["verified"]) || $_SESSION["verified"]==0){
        exit("Activate your account !");
    }

    if($_GET["user"] != $_SESSION["id"] && isset($_SESSION["type"]) && $_SESSION["type"]=="user") { //do not check if its your gallery or if i'm admin
        if($_SESSION["blocked"]==1){ exit("404 1"); } //if i'm blocked by admin

        include ("conn.blade.php");
        $d=mysqli_query ($c, "select friends,blocked from users WHERE id='".$_GET["user"]."'");
        $data= mysqli_fetch_array($d);

        if($data["blocked"]==1){ exit("404 2"); } //if user is blocked by admin

        $friendsArray = json_decode($data["friends"], true);
        mysqli_close($c);

        if (isset($friendsArray[$_SESSION["id"]]) && $friendsArray[$_SESSION["id"]]==1) { //remove this
            exit("This user blocked you"); //if friend blocked you
        }
    }

    $query ="select count(id) from gallery WHERE user = '". $_GET["user"] ."'";
    $date1 = ""; $defdate1="";
    $date2 = ""; $defdate2="";
    if(isset($_GET["date1"]) && !empty($_GET["date1"])){
        $query = $query . " and time >= '" . $_GET["date1"] . " 00:00:00'";
        $date1 = "&date1=".$_GET["date1"];
        $defdate1= date( "Y-m-d" , strtotime($_GET["date1"]) );
    }
    if(isset($_GET["date2"]) && !empty($_GET["date2"])){
        $query = $query . " and time <= '" . $_GET["date2"] . " 23:59:59'";
        $date2 = "&date2=".$_GET["date2"];
        $defdate2= date( "Y-m-d" , strtotime($_GET["date2"]) );
    }

    include ("conn.blade.php");
    $d = mysqli_query ($c, $query) ;
    mysqli_close($c);
    
    $perpage = 4;
    if(isset($_GET["perpage"]) && is_numeric($_GET["perpage"])){
        $perpage = $_GET["perpage"];
    }
    $pagesnbr = ceil(mysqli_fetch_array($d)[0]/$perpage);

    $debut = 0;
    if(isset($_GET["page"]) && is_numeric($_GET["page"])){
        $currentpage = ceil($_GET["page"]);
        if($currentpage>0) {$debut = ($currentpage-1)*$perpage;}
    }
    elseif(isset($_GET["last"])){
        $currentpage = $pagesnbr ;
        $debut = ($currentpage-1)*$perpage;
    }
    else{
        $currentpage = 1;
    }
?>

<div class="page-heading" style="margin-bottom:0;">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Gallery</h3>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row gallery" data-bs-toggle="modal" data-bs-target="#galleryModal">
                        <?php include ("conn.blade.php");
                        $query = "select * from gallery WHERE user = '". $_GET["user"] ."'";
                        if(isset($_GET["date1"]) && !empty($_GET["date1"])){
                            $query = $query . " and time >= '" . $_GET["date1"] . " 00:00:00'";
                        }
                        if(isset($_GET["date2"]) && !empty($_GET["date2"])){
                            $query = $query . " and time <= '" . $_GET["date2"] . " 23:59:59'";
                        }
                        $query = $query . " order by time desc";
                        $query = $query . " limit $debut,$perpage";
                        $d=mysqli_query ($c, $query);
                        mysqli_close($c);
                        if(mysqli_num_rows($d)==0){ ?>
                            <p style="text-align:center;">No Media !</p>
                        <?php }else{
                            $i=0;
                            while($r= mysqli_fetch_array ($d))
                            { ?>
                                <div class="col-6 col-sm-6 col-lg-3 mt-2 mt-md-0 mb-md-0 mb-2">
                                    <a href="#">
                                        <img class="w-100" data-bs-slide-to="<?=$i?>" src="/uploads/gallery/<?= $r["img"] ?>" width="100" height="200" data-bs-target="#Gallerycarousel">
                                    </a>
                                    <div style="text-align:center;">
                                        <span class="no-click" ><?= date ( "d/m/Y" , strtotime($r["time"]) ) ?></span>
                                        <?php if(isset($_SESSION["id"]) && $_SESSION["id"]==$_GET["user"] || isset($_SESSION["type"]) && $_SESSION["type"]=="admin"){ ?>
                                            <p><a class="btn btn-danger" href="/deletegallery?deletegallery=<?= $r["id"] ?>&user=<?= $_SESSION["id"] ?>&img=<?= $r["img"] ?>&page=<?= $currentpage ?>&perpage=<?= $perpage ?>" onclick="event.cancelBubble = true;return confirm('Delete this image ?');event.cancelBubble = true;">Delete</a></p>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php $i++;}  } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<p id="ppp" class="pagenum" style="text-align: center;">
    <a class="text-primary" href="/page/gallery?user=<?= $_GET["user"] ?>&page=1&perpage=<?= $perpage ?><?= $date1.$date2 ?>#ppp"> &nbsp; << &nbsp; </a> <?php
    for ($i=1; $i<=$pagesnbr; $i++){ 
        if($currentpage!=$i){?>
            <a class="text-primary" href="/page/gallery?user=<?= $_GET["user"] ?>&page=<?= $i ?>&perpage=<?= $perpage ?><?= $date1.$date2 ?>#ppp"> &nbsp; <?= $i ?> &nbsp; </a>
        <?php }else{ ?>
            &nbsp; <?= $i ?> &nbsp; 
    <?php }}?> 
    <a class="text-primary" href="/page/gallery?user=<?= $_GET["user"] ?>&page=<?= $pagesnbr ?>&perpage=<?= $perpage ?><?= $date1.$date2 ?>&last=yes#ppp"> &nbsp; >> &nbsp; </a>
</p>

<div style="display:flex;flex-wrap: wrap;justify-content: space-between" class="pb-2 pt-0">
    <div>
        <nobr>
            Images per page : 
            <select id="perpage" onchange="refresh()">
                <option value ="4" <?php if($perpage==4) {echo "selected";} ?> >4</option>
                <option value="8" <?php if($perpage==8){echo "selected";} ?> >8</option>
                <option value="12" <?php if($perpage==12){echo "selected";} ?> >12</option>
                <option value="16" <?php if($perpage==16){echo "selected";} ?> >16</option>
                <option value="30" <?php if($perpage==30){echo "selected";} ?> >30</option>
            </select>
        </nobr>
    </div>

    <div> <nobr>
        <form method="get" action="/page/gallery#ppp">
        From : <input type="date" name="date1" id="date1" value = "<?= $defdate1 ?>">
        To : <input type="date" name="date2" id="date2" value = "<?= $defdate2 ?>">
        <input type="hidden" name="user" value = "<?= $_GET["user"] ?>">
        <input type="submit" class="btn btn-secondary" value="Filter">
        </form></nobr>
    </div>
</div>


<script type="text/javascript">
    let currentimg = 1;
    let poped = false;
    next(0);
    document.addEventListener("keydown", e=>{ if(e.key.toLowerCase()==="arrowleft")next(-1);if(e.key.toLowerCase()==="arrowright")next(1);if(e.key.toLowerCase()==="escape")popupremove();})
    
    <?php if(isset($_GET["last"])){?> 
        currentimg = perpage;
        let nextsrc = document.getElementById("img" + currentimg).src;
        document.getElementById("136").src=nextsrc;
    <?php } ?>

    function refresh(){
        const perpage = document.getElementById("perpage").value;
        if(perpage!=0){ window.location.href = "/page/gallery?user=<?= $_GET["user"] ?>&perpage=" + perpage + "<?= $date1.$date2 ?>#ppp"; }
    }
</script>


@include( 'partials.footer' )