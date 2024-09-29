@include( 'partials.header' )


<?php 
    if (session_id()=="") session_start();


    $query ="select count(id) from gallery";
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
    
    $perpage = 5;
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

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Photo Gallery</h3>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Our Gallery</h5>
                    </div>
                    <div class="card-body">
                        <div class="row gallery" data-bs-toggle="modal" data-bs-target="#galleryModal">

                            <div class="col-6 col-sm-6 col-lg-3 mt-2 mt-md-0 mb-md-0 mb-2">

                                <div class="row" style="text-align:center">
                                    <?php include ("conn.blade.php");
                                        $query = "select * from gallery";
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

                                        if(mysqli_num_rows($d)==0){?>
                                            <p style="text-align:center;">No Media !</p>
                                        <?php  }else{
                                            $i=0;
                                            while($r= mysqli_fetch_array ($d))
                                            { ?>
                                                <div class="col">
                                                    <div>
                                                        <a href="#"><img class="w-100 active" src="/uploads/gallery/<?= $r["img"] ?>" data-bs-target="#Gallerycarousel" data-bs-slide-to="0"></a>
                                                        <br><span style="font-family:'Courier New', Courier, monospace;"><?= date ( "d/m/Y"  , strtotime($r["time"]) ) ?></span>
       
                                                            <p><a class="btn btn-danger" href="/deletegallery?deletegallery=<?= $r["id"] ?>&img=<?= $r["img"] ?>&page=<?= $currentpage ?>&perpage=<?= $perpage ?>" onclick="return confirm('Delete this image ?');">Delete</a></p>

                                                    </div>
                                                </div>
                                    <?php   } 
                                        echo "<script type='text/javascript'>let perpage = $i;</script>";
                                    }?>
                                </div>



                            </div>
                            
                        </div>

                        <div class="row mt-2 mt-md-4 gallery" data-bs-toggle="modal" data-bs-target="#galleryModal">
                            <div class="col-6 col-sm-6 col-lg-3 mt-2 mt-md-0 mb-md-0 mb-2">
                                <a href="#">
                                    <img class="w-100 active" src="" data-bs-target="#Gallerycarousel" data-bs-slide-to="0">
                                </a>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<p id="ppp" class="pagenum">
    <a class="text-primary" href="/page/gallery?&page=1&perpage=<?= $perpage ?><?= $date1.$date2 ?>#ppp"> &nbsp; << &nbsp; </a> <?php
    for ($i=1; $i<=$pagesnbr; $i++){ 
        if($currentpage!=$i){?>
            <a class="text-primary" href="/page/gallery?page=<?= $i ?>&perpage=<?= $perpage ?><?= $date1.$date2 ?>#ppp"> &nbsp; <?= $i ?> &nbsp; </a>
        <?php }else{ ?>
            &nbsp; <?= $i ?> &nbsp; 
    <?php }}?> 
    <a class="text-primary" href="/page/gallery?page=<?= $pagesnbr ?>&perpage=<?= $perpage ?><?= $date1.$date2 ?>&last=yes#ppp"> &nbsp; >> &nbsp; </a>
</p>


<div class="modal fade" id="galleryModal" tabindex="-1" role="dialog"
aria-labelledby="galleryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered"
role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="galleryModalTitle">Our Gallery Example</h5>
                <button type="button" class="close" data-bs-dismiss="modal"
                    aria-label="Close">
                    <i data-feather="x"></i>
                </button>
            </div>
            <div class="modal-body">

                <div id="Gallerycarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        <button type="button" data-bs-target="#Gallerycarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#Gallerycarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#Gallerycarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                        <button type="button" data-bs-target="#Gallerycarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                        </div>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100" src="https://images.unsplash.com/photo-1633008808000-ce86bff6c1ed?ixid=MnwxMjA3fDB8MHxlZGl0b3JpYWwtZmVlZHwyN3x8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="https://images.unsplash.com/photo-1524758631624-e2822e304c36?ixid=MnwxMjA3fDF8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=870&q=80">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="https://images.unsplash.com/photo-1632951634308-d7889939c125?ixid=MnwxMjA3fDB8MHxlZGl0b3JpYWwtZmVlZHw0M3x8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        </div>
                        <div class="carousel-item">
                            <img class="d-block w-100" src="https://images.unsplash.com/photo-1632949107130-fc0d4f747b26?ixid=MnwxMjA3fDB8MHxlZGl0b3JpYWwtZmVlZHw3OHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60">
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#Gallerycarousel" role="button" type="button" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </a>
                    <a class="carousel-control-next" href="#Gallerycarousel" role="button" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </a>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@include( 'partials.footer' )