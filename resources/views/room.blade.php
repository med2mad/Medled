@include( 'partials.header' )

@isset($room)
    @if(strlen($room) < 1 && strlen($room) > 10)
        <script>window.location.href = "/"</script>
        exit;
    @endif
@else
    <script>window.location.href = "/"</script>
    exit;
@endisset

<?php
    if(isset($perpage) && $perpage!=''){ $_SESSION["perpage"]=$perpage; }
    else{$_SESSION["perpage"]=10;}
    
    if(!isset($_SESSION["auth"]) || $_SESSION["auth"]!="true"){
        exit("Login !");
    }
?>

<div class="page-heading" style="margin-bottom: 0;">

    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>
                    Group Chat : <span id="roomid">{{$room}}</span>
                </h3>
            </div>
        </div>
    </div>

    <section class="section">

<div class="card mb-0" style="margin-left:auto; width:200px;">
<div class="card-body messagesCard" style="padding:10px;">
<form id="perpageform" method="post" action="/conversations" class="form form-horizontal">
    @csrf
    <input name="friendId" type="hidden">
    <input name="friendPhoto" type="hidden">

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
$q = "select id, users_id_w, users_name_w, users_img_w, message from conversations where room<>NULL AND (users_id_w = ".$_SESSION["id"]." OR users_id_r = ".$_SESSION["id"].") AND (users_id_w = ".$_SESSION["friendId"]." OR users_id_r = ".$_SESSION["friendId"].")";
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

        <!-- room -->
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
            <img class="photo" src="/uploads/profiles/136.jpg">
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
</script>


<!-- room -->
    <script type="module">
        import { io } from "https://cdn.socket.io/4.4.1/socket.io.esm.min.js";
        let socket = io("http://localhost:4000");

        socket.on('connect', ()=>{
            socket.on('receive', (message, id, photo)=>{
                if(socket.id==id) { addMessage(message, document.getElementById("sourcediv1"), 0, photo); }
                else { addMessage(message, document.getElementById("sourcediv2"), 0, photo); }
            })

            socket.emit('join', "<?= $_SESSION["name"] ?>", "<?= $room ?>", "<?= $_SESSION["photo"] ?>");
        })

        document.getElementById("form1btn").addEventListener("click", function(event) {
            if (tinymce.get('message1').getContent({format:'text'}).trim()==""){return;}
            const messageContent = tinymce.get('message1').getContent();
            addMessage(messageContent, document.getElementById("sourcediv1"), 0, "<?= $_SESSION["photo"] ?>");
            tinymce.get('message1').setContent('');
            socket.emit('send', messageContent);
        });
    </script>


<script>
    function addMessage(message, source, id, photo='136.jpg') {
        const newDiv = document.createElement("div");
        newDiv.innerHTML = source.innerHTML;
        if(id==0)
        { newDiv.querySelector(".newmessage").innerHTML = message; newDiv.querySelector(".photo").src= "/uploads/profiles/"+photo; }
        else{ newDiv.querySelector(".newmessage").innerHTML = message + '<span onclick="deletemessage(this)" data-value="'+id+'" class="delete" >X</span>'; }
        document.getElementById("messages").append(newDiv);
        document.getElementById('noconversations').style.display = 'none';
        window.location.href = "#footer";
        return newDiv;
    }
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