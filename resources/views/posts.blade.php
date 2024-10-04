@include( 'partials.header' )
<?php
    // if (session_id()=="") session_start();
    $perpage=10;
    if(isset($_POST["perpage"]) && is_numeric($_POST["perpage"])){
        $perpage = $_POST["perpage"];
    }
    include ("conn.blade.php");
    mysqli_query ($c, "update posts set red=1 where users_id_r='".$_SESSION["id"]."'" ) ; $_SESSION["notif"]=0;
    mysqli_close($c);

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true" || !isset($_SESSION["verified"]) || $_SESSION["verified"]==0){
        exit("Activate your account !");
    }
?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Chat</h3>
            </div>
        </div>
    </div>
    <section class="section">


<form id="form1" method="post" action="/post">
    @csrf
    <textarea name="message" id="message1" maxlength="500" cols="30" rows="6" class="form-control"></textarea>
    <input type="submit" name="post" class="btn btn-primary btn-lg mb-1" value="OK">
</form>


<div class="card mb-2" style="margin-left:auto; width:200px; border:1px solid rgb(230,230,230);">
<div class="card-body" style="padding:10px;">
    <form id="perpageform" method="post" action="/posts" class="form form-horizontal">
        @csrf
        <input name="friend" type="hidden" value="<?= $_POST["friend"] ?>">
        <div class="form-body">
        <div style="display:flex; align-items:flex-end;">
            <div style="width:150px; display:flex; align-items:flex-end; margin:auto;">
                <nobr>Show last</nobr>
                <select name="perpage" onchange="refresh()" class="form-control" style="width:100px; margin:0px 5px; padding:0 6px;">
                    <option value ="1" <?php if($perpage==1) {echo "selected";} ?> >1</option>
                    <option value ="5" <?php if($perpage==5) {echo "selected";} ?> >5</option>
                    <option value="10" <?php if($perpage==10){echo "selected";} ?> >10</option>
                    <option value="15" <?php if($perpage==15){echo "selected";} ?> >15</option>
                    <option value="20" <?php if($perpage==20){echo "selected";} ?> >20</option>
                    <option value="30" <?php if($perpage==30){echo "selected";} ?> >30</option>
                    <option value="100" <?php if($perpage==100){echo "selected";} ?> >All</option>
                </select>
            </div>
            </div>
        </div>
    </form>
</div>
</div>

    <div class="card" style="background-color:#1e1e2d;">
        <div id="messages" class="card-body py-0">

<?php include ("conn.blade.php");
$q = "select users_id_w, users_name_w, users_img_w, message from posts where (users_id_w = ".$_SESSION["id"]." OR users_id_r = ".$_SESSION["id"].") AND (users_id_w = ".$_POST["friend"]." OR users_id_r = ".$_POST["friend"].")";
$d1 = mysqli_query ($c, $q." ORDER BY id DESC LIMIT $perpage");
mysqli_close($c);
$y=-1; $_SESSION["message"]=[];
while($r= mysqli_fetch_array($d1))
{ $y++;
    $id = $r["users_id_w"];
    $img = $r["users_img_w"];
    $name = htmlspecialchars($r["users_name_w"]);
    $_SESSION["message"][$y] = nl2br(htmlspecialchars($r["message"]));
?>

<div style="display:flex; align-items:center; gap:5px; text-align:center; border-radius:10px; background-color:<?= $_SESSION["id"]==$id ? 'rgba(0, 255, 0, 0.1)':''?>" class="my-3 p-2">
<?php if($_SESSION["id"]==$id) { ?>
    <div width="120">
        <img style="border:solid; object-fit:contain; background-color:black; border-radius:50%;" src="/uploads/profiles/<?= $img ?>" width="100" height="100" alt="photo<?= $id ?>">
        <br><?= $name ?>
    </div>
<?php } ?>
    <div style="padding:5px 0; flex-grow:1;">
        <div id="oldmessage<?=$y?>" class="oldmessage"></div>
    </div>
<?php if($_SESSION["id"]!=$id) { ?>
    <div width="120">
        <img style="border:solid; object-fit:contain; background-color:black; border-radius:50%;" src="/uploads/profiles/<?= $img ?>" width="100" height="100" alt="photo<?= $id ?>">
        <br><?= $name ?>
    </div>
<?php } ?>
</div>

<?php } ?>

            </div>
        </div>

    </section>
</div>

<script>
    function refresh(){
        const perpageform = document.getElementById("perpageform");
        perpageform.submit();
    }
</script>

<script src="/assets/extensions/tinymce/tinymce.min.js"></script>
<script src="/assets/static/js/pages/tinymce.js"></script>
<script>
    tinymce.init({
        selector: '#message1',
        plugins: [
          'advlist','autolink','image',
          'lists','link','charmap','preview','anchor','searchreplace','visualblocks',
          'media', 'emoticons',
        ],
        toolbar:'fontsize | bold italic underline forecolor backcolor | ' +
                'alignleft aligncenter alignright alignjustify |' +
                'bullist numlist checklist | link emoticons | removeformat | image',
        font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
        menubar: false,
        height: 220, 
        file_picker_callback: function (callback, value, meta) {
            if (meta.filetype === 'image') {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                input.onchange = function () {
                    var file = this.files[0];
                    var reader = new FileReader();
                    reader.onload = function () {
                        callback(reader.result, {
                            alt: file.name
                        });
                    };
                    reader.readAsDataURL(file);
                };
                input.click();
            }
        }
    });
</script>

<div id="sourcediv" style="display:none;">
    <div style="display:flex; align-items:center; gap:5px; text-align:center; border-radius:10px; background-color:rgba(0, 255, 0, 0.1)" class="my-3 p-2">
        <div width="120">
            <img style="border:solid; object-fit:contain; background-color:black; border-radius:50%;" src="/uploads/profiles/<?= $_SESSION["photo"] ?>" width="100" height="100" alt="photo<?= $_SESSION["id"] ?>">
            <br><?= $_SESSION["name"] ?>
        </div>
        <div style="padding:5px 0; flex-grow:1;">
            <div class="newmessage"></div>
        </div>
    </div>
</div>

<script>
 <?php print_r($_SESSION["message"]); ?>

    document.addEventListener('DOMContentLoaded', function() {
        var messages = document.getElementsByClassName('oldmessage');
        <?php $y=-1; ?>
        for (var i = 0; i < messages.length; i++) {
            <?php $y++; ?>
            document.getElementById("oldmessage"+i).innerHTML = <?php echo($_SESSION["message"][$y]); ?>
        }
    });

    document.getElementById("form1").addEventListener("submit", function(event) {
        event.preventDefault();
        const formData = new FormData();
        const messageContent = tinymce.get('message1').getContent();
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('message', messageContent);
        formData.append('friend', <?= $_POST["friend"] ?>);
        formData.append('_token', csrfToken);
        fetch('/post', { method:'POST', body:formData })
        .then(response => response.json())
        .then(data => {
            const newDiv = document.createElement("div");
            const sourceDiv = document.getElementById("sourcediv");
            newDiv.innerHTML = sourceDiv.innerHTML;
            const newTextarea = newDiv.querySelector(".newmessage");
            newTextarea.innerHTML = messageContent;
            document.getElementById("messages").prepend(newDiv);
            tinymce.get('message1').setContent('');
        })
    });



</script>

@include( 'partials.footer' )