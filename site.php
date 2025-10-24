<?php
//common locations
$fe_img_path = $core_elements->tree_struct()->mediadir->publicimg;
$img_path = $core_elements->tree_struct()->mediadir->img;
$server_img_path = $_SERVER['DOCUMENT_ROOT'] . $img_path;
$file_path = $core_elements->tree_struct()->mediadir->file;
?>
<body class="medh">
	<!-- Top -->
	<?php include $core_elements->tree_struct()->templatedir->publicmain . "/top.phtml"; ?>
    
    <!-- Pages -->
    <div id="midelm">
    <?php
    if ($currpg == "home") {
        include $core_elements->tree_struct()->templatedir->publicpages . "/home.phtml";
    }
    ?>
    </div>

    <!-- Footer -->
    <?php //include $core_elements->tree_struct()->templatedir->publicmain . "/footer.phtml"; ?>

    <!-- Scripts -->
    <!-- Bootstrap -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/bootstrap.bundle.min.js"></script>
     <!-- Pure Swipe Evenrs -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/swiped-events.min.js"></script>
    <!-- Lightbox for BS5 -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/bs5lightbox.bundle.min.js"></script>
    <!-- Forms -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/form_send.js"></script>
    <!-- Main Script -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->publicjs; ?>/main.js"></script>
</body>