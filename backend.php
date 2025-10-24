<?php
/*
Initialize app
*/

//Resources
include 'app/code/base/core.php';
include 'app/code/base/base.php';
include 'app/code/base/admininfo.php';
include 'app/code/base/adminlists.php';
include 'app/code/base/admindata.php';

//Initialize core instances
$core_elements = Core::init();

//Base elements
$admin_info = new AdminInfo;
$admin_lists = new AdminLists;
$admin_data = new AdminData;

//PHP Variables
//User data
if (isset($_POST['usr'])) {
    $usr = $_POST['usr'];
} else {
    $usr = "";
}
if (isset($_POST['pwd'])) {
    $pwd = $_POST['pwd'];
} else {
    $pwd = "";
}
if (isset($_POST['vardumb'])) {
    $pwdenc = $_POST['vardumb'];
} else {
    $pwdenc = "";
}
if (isset($_POST['loginact'])) {
    $loginact = $_POST['loginact'];
} else {
    $loginact = 0;
}
$login_err = "";

//Page
if (isset($_GET['pg'])) {
    $currpg = $_GET['pg'];
} else {
    $currpg = "home";
}
//Subpage
if (
    $currpg == "dashboard" ||
    $currpg == "entities" ||
    $currpg == "publicusers" ||
    $currpg == "users" ||
    $currpg == "config"
) {    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $id = 0;
    }
    if ($id == "new") {
        if (isset($_GET['param'])) {
            $param = $_GET['param'];
        } else {
            $param = 0;
        }
    }
}

//Define User
if (isset($_COOKIE["adminactivesession"])) {
    $usersession = explode("|",$_COOKIE["adminactivesession"]);
    $usertmp = $usersession[0];
    $session = $usersession[1];
    $user = $core_elements->check_session($usertmp,$session);        
}
if (!isset($_COOKIE["adminactivesession"]) || $user <= 0) {
    if ($loginact == 1) {
        if (strlen($usr) >= 6) {
            if ((strlen($pwd) == (strlen($pwdenc) - 5)/2) && strlen($pwd) > 0) {
                $decpwd = $core_elements->decrypt_pwd($pwdenc);
                $user = $core_elements->get_user($usr,$decpwd);
                if ($user > 0) {    
                    //Verify existing user sessions
                    $usersesallowed = $core_elements->user_session_allowed($user);
                    if ($usersesallowed) {
                        $session = $core_elements->set_session($usr,$user);
                        $cvalue = "$user|$session";
                        setcookie("adminactivesession", $cvalue, time() + (8 * 3600), "/");
                    } else {
                        $puser = -2;
                        $login_err = "Só pode iniciar uma sessão por utilizador!";
                    }
                    $_POST = array(); $loginact = 0;
                } else {
                    $user = -2;
                    $login_err = "Dados de acesso inválidos!";    
                } 
            } else {
                $user = -2;
                if (strlen($pwd) == 0) {        
                    $login_err = "Password obrigatória!";    
                } else {
                    $login_err = "Erro no módulo de login!";
                }    
            }
        } else {
            $user = -2;
            $login_err = "Utilizador obrigatório!";
        }
    } else {
        $user = -1;
        $login_err = "Sessão expirada!";
    }
}

//Define group
if ($user >= 0) {
    $group_info = $admin_info->get_group_info($user);
    $groupid = $group_info[0];
    $groupname = $group_info[1];
    $groupimg = $group_info[2];
}

//Logout
if ($currpg == "logout") {
    if (!isset($session)) {
        $session = "";
    }
    $core_elements->end_session($user,$session);
    $user = -1;
    $cvalue = "$user|0";
    setcookie("adminactivesession", $cvalue, 0, "/");
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Prevrod - Sistema de registo de atividade de condução.">
    <meta name="author" content="Prevrod - Consultadoria, Lda.">
    <meta name="keywords" content="tacógrafos, transportes, trânsito, 561, multas, motorista, CAM, formação">
    <meta name="robots" content="index, follow">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>E-ROD - Administração</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo $core_elements->tree_struct()->skindir->admincss; ?>/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $core_elements->tree_struct()->skindir->admincss; ?>/custom.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=PT Sans" rel="stylesheet">
    <!-- Custom Fonts -->
    <link href="<?php echo $core_elements->tree_struct()->skindir->adminfonts; ?>/font-awesome/all.min.css" rel="stylesheet" type="text/css">
</head>

<?php
if ($user >= 0) {
    include "system.php";
} else {
    include "login.php";
}
?>

</html>