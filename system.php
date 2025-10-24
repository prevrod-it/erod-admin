<body>
    <!--Loop functions-->
    <!--div id="loopjsfn" class="d-none" data-looptime="10" data-loopfn="newevents"></div-->
    <!--Charts plugin (must be loaded before content-->
    <script src="<?php echo $core_elements->tree_struct()->skindir->adminjs; ?>/chart.umd.js"></script>
	<!-- Navigation -->
	<?php include $core_elements->tree_struct()->templatedir->adminmain . "/menu.phtml"; ?>
	<?php include $core_elements->tree_struct()->templatedir->adminmain . "/userinfo.phtml"; ?>
	<?php
    if ($currpg == "home") {
        include $core_elements->tree_struct()->templatedir->adminpages . "/home.php";   
    } elseif ($currpg == "entities") {
    	include $core_elements->tree_struct()->templatedir->adminpages . "/entities.phtml";
    } elseif ($currpg == "publicusers") {
        include $core_elements->tree_struct()->templatedir->adminpages . "/publicusers.phtml";
    } elseif ($currpg == "config") {
        include $core_elements->tree_struct()->templatedir->adminpages . "/config.phtml";
    }
    ?>
    <?php include $core_elements->tree_struct()->templatedir->adminmain . "/footer.phtml"; ?>
    <!--Scroll To Top-->
    <a href="#" id="topButton" class="hc_scrollup"><i class="fa fa-chevron-up"></i></a>
    <!--/Scroll To Top-->

    <!-- New events notifications Modal -->
    <div class="modal fade" id="neweventsmodal" tabindex="-1" aria-labelledby="neweventsmodalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="neweventsmodalLabel">Notificações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
    <audio id="notificationaudio" src="<?php echo $core_elements->tree_struct()->mediadir->file; ?>/audio/notification.mp3"></audio>

    <!-- Scripts -->
    <!-- Bootstrap -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->adminjs; ?>/bootstrap.bundle.min.js"></script>
    <!-- Forms -->
    <!--script src="<?php echo $core_elements->tree_struct()->skindir->adminjs; ?>/form_validation.js"></script-->
    <script src="<?php echo $core_elements->tree_struct()->skindir->adminjs; ?>/form_send.js"></script>
    <!-- Main Script -->
    <script src="<?php echo $core_elements->tree_struct()->skindir->adminjs; ?>/main.js"></script>
</body>