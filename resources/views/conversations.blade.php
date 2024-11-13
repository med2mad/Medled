@include( 'partials.header' )

<?php
    if(!isset($_SESSION["friendId"]) || $_SESSION["friendId"]==-1)
    { echo '<script>window.location.href = "/page/users?title=Friends";</script>'; exit;}
    
    if(!isset($_SESSION["friendPhoto"]) || $_SESSION["friendPhoto"]=='')
    { echo '<script>window.location.href = "/page/users?title=Friends";</script>'; exit;}

    include ("conn.blade.php");
    mysqli_query ($c, "update conversations set red=1 where users_id_w=".$_SESSION["friendId"]." and users_id_r=".$_SESSION["id"]) ;
    mysqli_close($c);

    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("Login !");
    }
?>

<div class="page-heading" style="margin-bottom: 0;">

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    Chat Room
                    <img id="headerphoto" src="/uploads/profiles/<?= $_SESSION["friendPhoto"] ?>" alt="photo<?= $_SESSION["friendId"] ?>">
                    <?php if(isset($_SESSION["friendId"]) && $_SESSION["friendId"]!=0) { ?>
                        <span id="active" style="display:none;" class="badge bg-light-success">Active</span>
                        <span id="inactive" style="display:none;" class="badge bg-light-secondary">Inactive</span>
                    <?php } ?>
                </h3>
            </div>
        </div>
    </div>

    <section class="section">

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
$q = "select id, users_id_w, users_name_w, users_img_w, message from conversations where room=NULL AND (users_id_w = ".$_SESSION["id"]." OR users_id_r = ".$_SESSION["id"].") AND (users_id_w = ".$_SESSION["friendId"]." OR users_id_r = ".$_SESSION["friendId"].") AND users_id_w <> 0 AND users_id_r <> 0";
$d = mysqli_query ($c, $q." ORDER BY id DESC LIMIT ".$_SESSION["perpage"]);
mysqli_close($c);?>

<div id="noconversations" style="text-align:center; margin-bottom:0; <?= mysqli_num_rows($d)!=0 ? 'display:none !important;':'' ?>">No Conversations !</div>

<?php
$rows = [];
while ($row = mysqli_fetch_array($d)) {
    $rows[] = $row;
}
$rows = array_reverse($rows);

foreach ($rows as $r)
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

<?php } ?>

            </div>
        </div>

    </section>
</div>

<div id="sendmsg">
    <form id="form1">
        @csrf
        <textarea rows="2" name="message" id="message1" maxlength="500" class="form-control"></textarea>
    </form>

    <button id="form1btn" class="btn btn-primary btn-lg">
        <img src="/send.svg" alt="Send" width="24" height="24">
    </button> 
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
        plugins: ['link','emoticons'],
        toolbar:'bold underline forecolor emoticons',
        font_size_formats: '8pt 10pt 12pt 14pt 16pt 18pt 24pt 36pt 48pt',
        menubar: false,
        height: 150, 
        resize: false,
        content_style: "p { margin: 0; }",
        setup: function(editor) {
            editor.on('init', function() {
                window.location.href = "#footer";
            });
        },
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

<?php if(isset($_SESSION["auth"]) && $_SESSION["auth"]=="true" && isset($_SESSION["blocked"]) && $_SESSION["blocked"]==0) { ?>

<script>
    function deletemessage(span){
        const formData = new FormData();
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('_token', csrfToken);
        formData.append('messageid', span.getAttribute('data-value'));
        span.closest('.messagerow').remove();
        fetch('/deleteconversation', { method:'POST', body:formData })
    }

    document.getElementById("form1btn").addEventListener("click", function(event) {
        if (tinymce.get('message1').getContent({format: 'text'}).trim()==""){return;}
        const messageContent = tinymce.get('message1').getContent();
        const formData = new FormData();
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('message', messageContent);
        formData.append('friendId', <?= $_SESSION["friendId"] ?>);
        formData.append('_token', csrfToken);
        const div = addMessage(messageContent, document.getElementById("sourcediv1"),0); //message id, instead of 0
        tinymce.get('message1').setContent('');
        fetch('/create_conversation', { method:'POST', body:formData })
        .then(response => response.json())
        .then( ()=>{ 
            <?php if($_SESSION["friendId"]==0){ ?>
                fetch('http://localhost:5000/?message='+div.innerText.trim()).then(response => response.json())
                .then(data => { 
                    addMessage(data.answer, document.getElementById("sourcediv2"), 0);
                })
            <?php } ?>
        })
    });

    function newMessages() {
        fetch("/getmessages?friendId=<?=$_SESSION["friendId"]?>").then(response => { if(!response.ok)throw new Error('login'); return response.json(); })
        .then(messages=>{
            for (let id in messages) {
                addMessage(messages[id], document.getElementById("sourcediv2"), 0);
            }
        })
        .catch(error=>{
            window.location.href = `/logout`;
        });
    }
    setInterval(newMessages, 5000); //every 5 seconds

    function addMessage(message, source, id) {
        const newDiv = document.createElement("div");
        newDiv.innerHTML = source.innerHTML;
        if(id==0)
        { newDiv.querySelector(".newmessage").innerHTML = message; }
        else{ newDiv.querySelector(".newmessage").innerHTML = message + '<span onclick="deletemessage(this)" data-value="'+id+'" class="delete" >X</span>'; }
        document.getElementById("messages").append(newDiv);
        document.getElementById('noconversations').style.display = 'none';
        window.location.href = "#footer";
        return newDiv;
    }

    function timeDifference(currantTime, oldTime) {
        const differenceInSeconds = Math.abs(currantTime - oldTime);
        const differenceInMinutes = differenceInSeconds / 60;
        return differenceInMinutes;
    }
    <?php if(isset($_SESSION["friendId"]) && $_SESSION["friendId"]!=0) { ?>
        function updateStatus() {
            fetch("/gettime?q=<?=$_SESSION["friendId"]?>").then(response => { if(!response.ok)throw new Error('login'); return response.json(); })
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
            .catch(error=>{
                window.location.href = `/logout`;
            });
        }
        updateStatus();

        setInterval(updateStatus, 30000); //every 30 seconds
    <?php } ?>
</script>

<?php } ?>





<?php ///////////////////////////////////////////////footer/////////////////////////////////////////////// ?>



<div id="footer" style="height:120px;"></div>
</div>
</div>
</div>
<script src="/assets/static/js/components/dark.js"></script>
<script src="/assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="/assets/compiled/js/app.js"></script>
</body>
</html>