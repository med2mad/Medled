@include( 'partials.header' )
<?php
    // if (session_id()=="") session_start();
    if((!isset($_SESSION["friendId"]) || $_SESSION["friendId"]=='') && (!isset($friendId) || $friendId==''))
    { echo '<script>window.location.href = "/page/users?title=Friends";</script>'; exit;}
    if(isset($friendId) && $friendId!=''){$_SESSION["friendId"]=$friendId;}
    
    if((!isset($_SESSION["friendPhoto"]) || $_SESSION["friendPhoto"]=='') && (!isset($friendPhoto) || $friendPhoto==''))
    { echo '<script>window.location.href = "/page/users?title=Friends";</script>'; exit;}
    if(isset($friendPhoto) && $friendPhoto!=''){$_SESSION["friendPhoto"]=$friendPhoto;}
    
    if(isset($perpage) && $perpage!=''){ $_SESSION["perpage"]=$perpage; }
    else{$_SESSION["perpage"]=10;}
    
    include ("conn.blade.php");
    mysqli_query ($c, "update conversations set red=1 where users_id_r='".$_SESSION["id"]."'" ) ;
    mysqli_close($c);

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("Activate your account !");
    }
?>

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    Chat Room
                    <img id="headerphoto" src="/uploads/profiles/<?= $_SESSION["friendPhoto"] ?>" alt="photo<?= $_SESSION["friendId"] ?>">
                    <span id="active" style="display:none;" class="badge bg-light-success">Active</span>
                    <span id="inactive" style="display:none;" class="badge bg-light-secondary">Inactive</span>
                </h3>
            </div>
        </div>
    </div>
    <section class="section">


<form id="form1">
    @csrf
    <textarea name="message" id="message1" maxlength="500" cols="30" rows="6" class="form-control"></textarea>
</form>
<button id="form1btn" class="btn btn-primary btn-lg">
    <img src="/send.svg" alt="Send" width="24" height="24">
</button>

<div class="card mb-0" style="margin-left:auto; width:200px;">
<div class="card-body messagesCard" style="padding:10px;">
<form id="perpageform" method="post" action="/conversations" class="form form-horizontal">
    @csrf
    <input name="friendId" type="hidden" value="<?= $_SESSION["friendId"] ?>">
    <input name="friendPhoto" type="hidden" value="<?= $_SESSION["friendPhoto"] ?>">

    <div class="form-body">
        <div style="width:150px; display:flex; align-items:flex-end; margin:auto; ">
            <nobr>Show last</nobr>
            <select name="perpage" onchange="refresh()" class="form-control" style="width:100px; margin:0px 5px; padding:0 6px;">
                <option value ="5" <?php if($_SESSION["perpage"]==5) {echo "selected";} ?> >5</option>
                <option value="10" <?php if($_SESSION["perpage"]==10){echo "selected";} ?> >10</option>
                <option value="15" <?php if($_SESSION["perpage"]==15){echo "selected";} ?> >15</option>
                <option value="20" <?php if($_SESSION["perpage"]==20){echo "selected";} ?> >20</option>
                <option value="30" <?php if($_SESSION["perpage"]==30){echo "selected";} ?> >30</option>
                <option value="100" <?php if($_SESSION["perpage"]==100){echo "selected";} ?> >All</option>
            </select>
        </div>
    </div>
</form>
</div>
</div>


    <div class="card" style="margin-bottom:0;">
        <div id="messages" class="card-body py-3 messagesCard">

<?php include ("conn.blade.php");
$q = "select id, users_id_w, users_name_w, users_img_w, message from conversations where (users_id_w = ".$_SESSION["id"]." OR users_id_r = ".$_SESSION["id"].") AND (users_id_w = ".$_SESSION["friendId"]." OR users_id_r = ".$_SESSION["friendId"].")";
$d = mysqli_query ($c, $q." ORDER BY id DESC LIMIT ".$_SESSION["perpage"]);
mysqli_close($c);

if(mysqli_num_rows($d)==0){?>
    <p style="text-align:center; margin-bottom:0;">No Conversations !</p>
<?php  }else{
while($r= mysqli_fetch_array($d))
{
    $messageid = $r["id"];
    $id = $r["users_id_w"];
    $img = $r["users_img_w"];
    $name = htmlspecialchars($r["users_name_w"]);
    $message = $r["message"];
?>

<div class="messagerow p-2" style="justify-content:<?= $_SESSION["id"]==$id ? 'flex-start':'flex-end'?> !important;">
<?php if($_SESSION["id"]==$id) { ?>
    <div width="120">
        <img class="photo" src="/uploads/profiles/<?= $img ?>" alt="photo<?= $id ?>">
    </div>
<?php } ?>
    <div style="padding:5px 0;">
        <div class="oldmessage" style="background-color:<?= $_SESSION["id"]==$id ? 'rgba(199, 255, 206)':'white'?>">
            <?= $message ?>
            <?php if($_SESSION["id"]==$id) { ?> <span onclick="deletemessage(this)" data-value="<?= $messageid ?>" class="delete">X</span> <?php } ?>
        </div>
    </div>
<?php if($_SESSION["id"]!=$id) { ?>
    <div width="120">
        <img class="photo" src="/uploads/profiles/<?= $img ?>" alt="photo<?= $id ?>">
    </div>
<?php } ?>
</div>

<?php }} ?>

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
          'lists','link','emoticons',
        ],
        toolbar:'bold underline forecolor backcolor ' 
                + 'emoticons bullist numlist checklist',
        font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
        menubar: false,
        height: 220, 
        content_style: "p { margin: 0; }",
    });
</script>

<div id="sourcediv1" style="display:none;">
    <div class="messagerow p-2" style="justify-content:flex-start;">
        <div width="120">
            <img class="photo" src="/uploads/profiles/<?= $_SESSION["photo"] ?>" alt="photo<?= $_SESSION["id"] ?>">
        </div>
        <div style="padding:5px 0;">
            <div class="newmessage oldmessage" style="background-color:rgba(199, 255, 206);">
            </div> 
        </div>
    </div>
</div>

<div id="sourcediv2" style="display:none;">
    <div class="messagerow p-2" style="justify-content:flex-end;">
        <div style="padding:5px 0;">
            <div class="newmessage oldmessage" style="background-color:white;"></div>
        </div>
        <div width="120">
            <img class="photo" src="/uploads/profiles/<?= $_SESSION["friendPhoto"] ?>" alt="photo<?= $_SESSION["friendId"] ?>">
        </div>
    </div>
</div>

<script>
    function deletemessage(span){
        const formData = new FormData();
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('_token', csrfToken);
        formData.append('messageid', span.getAttribute('data-value'));
        span.closest('.messagerow').remove();
        fetch('/deleteconversation', { method:'POST', body:formData })
    }
</script>


<script>
    document.getElementById("form1btn").addEventListener("click", function(event) {
        if (tinymce.get('message1').getContent({format: 'text'}).trim()==""){return;}
        let messageContent = tinymce.get('message1').getContent();
        const formData = new FormData();
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('message', messageContent);
        formData.append('friendId', <?= $_SESSION["friendId"] ?>);
        formData.append('_token', csrfToken);
        fetch('/create_conversation', { method:'POST', body:formData })
        .then(response => response.json())
        .then(data => { 
            addMessage(messageContent, document.getElementById("sourcediv1"), data.id);
            tinymce.get('message1').setContent('');
        })
    });

    function newMessages() {
        fetch("/getmessages?friendId=<?=$_SESSION["friendId"]?>").then(response => response.json())
        .then(messages=>{
            for (let id in messages) {
                addMessage(messages[id], document.getElementById("sourcediv2"), 0);
            }
        });
    }
    setInterval(newMessages, 5000); //every 5 seconds

    function addMessage(message, source, id){
        const newDiv = document.createElement("div");
        newDiv.innerHTML = source.innerHTML;
        if(id==0)
        { newDiv.querySelector(".newmessage").innerHTML = message; }
        else{ newDiv.querySelector(".newmessage").innerHTML = message + '<span onclick="deletemessage(this)" data-value="'+id+'" class="delete" >X</span>'; }
        document.getElementById("messages").prepend(newDiv);
    }
</script>

<script>
    function timeDifference(currantTime, oldTime) {
        const differenceInSeconds = Math.abs(currantTime - oldTime);
        const differenceInMinutes = differenceInSeconds / 60;
        return differenceInMinutes;
    }

    function updateStatus() {
        fetch("/gettime?q=<?=$_SESSION["friendId"]?>").then(response => response.json())
        .then(response=>{
            if(timeDifference(response.curranttime, response.oldtime) > 0.5){ //not active if more that half a minute
                document.getElementById('active').style.display='none';
                document.getElementById('inactive').style.display='';
            }
            else{
                document.getElementById('active').style.display='';
                document.getElementById('inactive').style.display='none';
            }
        })
    }
    updateStatus();

    setInterval(updateStatus, 30000); //every 30 seconds
</script>

@include( 'partials.footer' )