<?php
if (isset($plogindest)) {
    if (strlen($plogindest) > 1) {
        $lgidest = "/$plogindest";
    } else {
        $lgidest = "";
    }
} else {
    $lgidest = "";
}
?>
<div class="container pb-4">
    <div class="row">
        <div class="col-md-4"></div>   
        <div class="col-md-4 text-center mt-3">
            <form method="post" action="/public<?php echo $lgidest; ?>" role="login" enctype="multipart/form-data">
                <input type="text" name="pusr" class="form-control mb-3" id="pusr" placeholder="NIF/e-mail" value="<?php echo $pusr; ?>" autocomplete="on"<?php echo ($pusr == "") ? " autofocus" : ""; ?>>         
                <input type="password" name="ppwd" class="form-control mb-3" id="ppwd" placeholder="Password" autocomplete="on"<?php echo ($pusr == "") ? "" : " autofocus"; ?>>
                <input type="hidden" name="vardumb" id="vardumb" value="!ghUT98B%09fDe#5j7G%dYU7ap8_Bd7Hw">
                <input type="hidden" name="loginact" id="loginact" value="1">
                <div id="login-form-status" class="m-2 text-left text-danger<?php echo ($puser == -2) ? " d-block" : " d-none"; ?>"><?php echo $login_err; ?></div>                               
                <button type="submit" name="submitBtn" class="btn btn-custom btn-block btn-lg mt-3" id="login">Entrar</button>
            </form>  
        </div>
        <div class="col-md-4"></div>
    </div>
</div>

<!-- Scripts -->
<!-- Forms -->
<script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/publicautentication.js"></script>