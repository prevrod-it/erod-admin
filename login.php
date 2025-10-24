<div class="container pb-4">
    <div class="row">
        <div class="col-md-4"></div>   
        <div class="col-md-4 text-center mt-3">
            <form method="post" action="/admin" role="login" enctype="multipart/form-data">
                <img src="<?php echo $core_elements->tree_struct()->mediadir->adminimg; ?>/top_navbar_logo.png" class="img-fluid mb-4" alt="E-ROD">
                <h3 class="pb-3">Portal de administração</h3>
                <input type="text" name="usr" class="form-control mb-3" id="usr" placeholder="e-mail" value="<?php echo $usr; ?>" autocomplete="on"<?php echo ($usr == "") ? " autofocus" : ""; ?>>         
                <input type="password" name="pwd" class="form-control mb-3" id="pwd" placeholder="Password" autocomplete="on"<?php echo ($usr == "") ? "" : " autofocus"; ?>>
                <input type="hidden" name="vardumb" id="vardumb" value="!ghUT98B%09fDe#5j7G%dYU7ap8_Bd7Hw">
                <input type="hidden" name="loginact" id="loginact" value="1">
                <div id="login-form-status" class="m-2 text-left text-danger<?php echo ($user == -2) ? " d-block" : " d-none"; ?>"><?php echo $login_err; ?></div>                               
                <button type="submit" name="submitBtn" class="btn btn-custom btn-block btn-lg mt-3" id="login">Entrar</button>
            </form>  
        </div>
        <div class="col-md-4"></div>
    </div>
</div>

<!-- Scripts -->
<!-- Forms -->
<script src="<?php echo $core_elements->tree_struct()->skindir->adminjs; ?>/adminautentication.js"></script>