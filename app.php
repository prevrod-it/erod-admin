<?php
//common locations
$fe_img_path = $core_elements->tree_struct()->mediadir->publicimg;
$img_path = $core_elements->tree_struct()->mediadir->img;
$server_img_path = $_SERVER['DOCUMENT_ROOT'] . $img_path;
$file_path = $core_elements->tree_struct()->mediadir->file;

//
$user_info = $public_data->get_driver_data($puser);
$loginuserid = $user_info[0];
?>
<body class="lowh" id="tgfsm">
    <input type="hidden" id="loginuserid" value="<?php echo $loginuserid; ?>">
	<!-- Top -->
	<?php include $core_elements->tree_struct()->templatedir->publicmain . "/drivertop.phtml"; ?>
    
    <!-- Pages -->
    <div id="midelm">
    <?php
    if ($currpg == "home") {
        include $core_elements->tree_struct()->templatedir->publicpages . "/appredirect.phtml";
        //include $core_elements->tree_struct()->templatedir->publicpages . "/driverreg.phtml";
    }
    ?>
    </div>

    <!-- Scripts -->
    <!-- Bootstrap -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/bootstrap.bundle.min.js"></script>
    <!-- Pure Swipe Evenrs -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/swiped-events.min.js"></script>
    <!-- Lightbox for BS5 -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/bs5lightbox.bundle.min.js"></script>
    <!-- Forms -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/app_form_send.js"></script>
    <!-- Main Script -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/appmain.js"></script>
</body>