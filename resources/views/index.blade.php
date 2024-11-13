@include( 'partials.header' )

<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>MedLed</h3>
            </div>
        </div>
    </div>
    <section class="section">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Where you can gather friends and exchange conversations and media.</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr><td>- Start by <a href="/page/create_user">creating an account</a> (no email required, sign up easily and start right away).</td></tr>
                    <tr><td>- Then <a href="/page/users?title=Users&name=">build a list of friends</a> from all users of the platform.</td></tr>
                    <tr><td>- Once they add you as well, they will start getting notified when you send them a message.</td></tr>
                    <tr><td>- <?php if(isset($_SESSION["id"])){ ?><a href="/page/gallery?user=<?= $_SESSION["id"] ?>"> <?php } ?>Create a Gallery<?php if(isset($_SESSION["id"])){ ?></a> <?php } ?> and fill it with images.</td></tr>
                    <tr><td>- Your friends can access your gallery, and you can <a href="/page/users?title=Friends">access theirs</a> too.</td></tr>
                </table>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Users account policy</h4>
            </div>
            <div class="card-body">
                <p>
                    Our platform is family friendly.
                </p>
                <p>
                    Respects privacy of its users.
                </p>                
                <p>
                    Listening for users feed back (for the platform development and reporting users).
                </p>
                <p>
                    Read the <a href="/page/conditions" style="text-decoration:underline;">Terms &amp; Conditions</a> to know more about the platform's users account policy. 
                </p>
            </div>
        </div>
    </section>
</div>

@include( 'partials.footer' )