@include( 'partials.header' )

<div id="myModal" class="modal">
    <span class="close">&times;</span>
    <img class="modal-content" id="imgModal">
</div>

<?php 
    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("Login !");
    }

    if($_GET["user"] != $_SESSION["id"] && isset($_SESSION["type"]) && $_SESSION["type"]=="user") { //do not check if its your gallery or if i'm admin
        if($_SESSION["blocked"]==1){ exit("Blocked by admin"); } //if i'm blocked by admin

        include ("conn.blade.php");
        $d=mysqli_query ($c, "select friends,blocked from users WHERE id='".$_GET["user"]."'");
        $data= mysqli_fetch_array($d);

        if($data["blocked"]==1){ exit("Blocked by admin"); } //if user is blocked by admin

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

    $name = "My ";
    if(isset($_GET["name"]) && $_GET["name"]!='') {$name = $_GET["name"].' ';}
?>

<div class="page-heading" style="margin-bottom:0;">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $name ?> Gallery</h3>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body pb-1">
                        <div style="display:flex; flex-wrap:wrap; justify-content:space-around" class="gallery">
                        <?php include ("conn.blade.php");
                        $query = "select * from gallery WHERE user = '". $_GET["user"] ."'";
                        if(isset($_GET["date1"]) && !empty($_GET["date1"])){
                            $query = $query . " and time >= '" . $_GET["date1"] . " 00:00:00'";
                        }
                        if(isset($_GET["date2"]) && !empty($_GET["date2"])){
                            $query = $query . " and time <= '" . $_GET["date2"] . " 23:59:59'";
                        }
                        $query = $query . " order by id desc";
                        $query = $query . " limit $debut,$perpage";
                        $d=mysqli_query ($c, $query);
                        mysqli_close($c);
                        if(mysqli_num_rows($d)==0){ ?>
                            <p style="text-align:center;">No Media !</p>
                        <?php }else{
                            $i=0;
                            while($r= mysqli_fetch_array($d))
                            { ?>
                                <div class="mt-2 mt-md-0 mb-md-0 mb-2">
                                    <img style="width:200px; cursor:pointer; object-fit:contain; border:solid; background-color:black" class="gallery-img" data-bs-slide-to="<?=$i?>" src="/uploads/gallery/<?= $r["img"] ?>" width="100" height="200" data-bs-target="#Gallerycarousel">
                                    <div style="text-align:center;">
                                        <span class="no-click" ><?= date ( "d/m/Y" , strtotime($r["time"]) ) ?></span>
                                        <?php if(isset($_SESSION["id"]) && $_SESSION["id"]==$_GET["user"] || isset($_SESSION["type"]) && $_SESSION["type"]=="admin"){ ?>
                                            <div class="mb-2">
                                                <a class="btn btn-danger" href="/deletegallery?id=<?= $r["id"] ?>&user=<?= $_GET["user"] ?>&name=<?= $name ?>&img=<?= $r["img"] ?>&page=<?= $currentpage ?>&perpage=<?= $perpage ?>" onclick="event.cancelBubble = true;return confirm('Delete this image ?');event.cancelBubble = true;">Delete</a>
                                            </div>
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


<div class="pagenum" style="text-align: center;">
    <a class="text-primary" href="/page/gallery?user=<?= $_GET["user"] ?>&page=1&name=<?= $name ?>&perpage=<?= $perpage ?><?= $date1.$date2 ?>"> &nbsp; << &nbsp; </a> <?php
    for ($i=1; $i<=$pagesnbr; $i++){ 
        if($currentpage!=$i){?>
            <a class="text-primary" href="/page/gallery?user=<?= $_GET["user"] ?>&page=<?= $i ?>&name=<?= $name ?>&perpage=<?= $perpage ?><?= $date1.$date2 ?>"> &nbsp; <?= $i ?> &nbsp; </a>
        <?php }else{ ?>
            &nbsp; <?= $i ?> &nbsp; 
    <?php }}?> 
    <a class="text-primary" href="/page/gallery?user=<?= $_GET["user"] ?>&page=<?= $pagesnbr ?>&name=<?= $name ?>&perpage=<?= $perpage ?><?= $date1.$date2 ?>&last=yes"> &nbsp; >> &nbsp; </a>
</div>

<div class="py-4" style="display:flex; align-items:center; flex-wrap:wrap; justify-content:space-between">
    <div>
        Per page : 
        <select id="perpage" onchange="refresh()" class="form-control" style="width:100px; display:inline;">
            <option value ="4" <?php if($perpage==4) {echo "selected";} ?> >4</option>
            <option value="8" <?php if($perpage==8){echo "selected";} ?> >8</option>
            <option value="12" <?php if($perpage==12){echo "selected";} ?> >12</option>
            <option value="16" <?php if($perpage==16){echo "selected";} ?> >16</option>
            <option value="30" <?php if($perpage==30){echo "selected";} ?> >30</option>
        </select> 
    </div>

    <div id="datefilter">
        <form method="get" action="/page/gallery" style="display:flex; align-items:center; flex-wrap:wrap;">
            <div style="text-align:right">From : </div><div><input type="date" name="date1" id="date1" value="<?= $defdate1 ?>" class="form-control"></div>
            <div style="width:50px;text-align:right"> - To : </div><div><input type="date" name="date2" id="date2" value="<?= $defdate2 ?>" class="form-control"></div>
            <div style="width:20px;text-align:right"><input type="hidden" name="user" value = "<?= $_GET["user"] ?>"></div>
            <input type="hidden" name="name" value = "<?= $name ?>">
            <div><input type="submit" class="btn btn-secondary" value="Filter" class="form-control"></div>
        </form>
    </div>
</div>

<?php if($_GET["user"] == $_SESSION["id"]) { ?>
<div>
    <div class="card">
        <div class="card-header pb-0">
            <h5 class="card-title">Add to gallery</h5>
        </div>
        <div class="card-content">
            <div class="card-body pt-0">
                <form method="post" action="/create_gallery?user=<?= $_SESSION["id"] ?>" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="img[]" class="multiple-files-filepond" multiple>
                    <button type="submit" class="btn btn-primary" value="upload">Upload</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script type="text/javascript">
    let currentimg = 1;
    let poped = false;
    // next(0);
    // document.addEventListener("keydown", e=>{ if(e.key.toLowerCase()==="arrowleft")next(-1);if(e.key.toLowerCase()==="arrowright")next(1);if(e.key.toLowerCase()==="escape")popupremove();})
    
    <?php if(isset($_GET["last"])){?> 
        currentimg = perpage;
        let nextsrc = document.getElementById("img" + currentimg).src;
        document.getElementById("136").src=nextsrc;
    <?php } ?>

    function refresh(){
        const perpage = document.getElementById("perpage").value;
        if(perpage!=0){ window.location.href = "/page/gallery?user=<?= $_GET["user"] ?>&name=<?= $name ?>&perpage=" + perpage + "<?= $date1.$date2 ?>"; }
    }


    var modal = document.getElementById('myModal');
    var img = document.getElementById('image1');
    var modalImg = document.getElementById('imgModal');

    var images = document.getElementsByClassName('gallery-img');
    for (var i = 0; i < images.length; i++) {
        images[i].onclick = function() {
            modal.style.display = 'flex';
            modalImg.src = this.src;  // Set the clicked image as the modal image
        }
    }

    //<span> element that closes the modal
    var closeSpan = document.getElementsByClassName('close')[0];
    closeSpan.onclick = function() {
        modal.style.display = 'none';
    }

    // Optional: Close the modal when clicking outside of the modal content
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>


<script src="/assets/extensions/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js"></script>
<script src="/assets/extensions/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js"></script> 
<script src="/assets/extensions/filepond-plugin-image-filter/filepond-plugin-image-filter.min.js"></script> 
<script src="/assets/extensions/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js"></script>
<script src="/assets/extensions/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js"></script>
<script src="/assets/extensions/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js"></script>
<script src="/assets/extensions/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js"></script>

<script src="/assets/extensions/filepond/filepond.js"></script>
<script src="/assets/static/js/pages/filepond.js"></script>

@include( 'partials.footer' )