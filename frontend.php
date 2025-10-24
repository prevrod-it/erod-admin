<?php
/*
Initialize app
*/

//Resources
include 'app/code/base/core.php';
include 'app/code/base/base.php';
include 'app/code/base/publicinfo.php';
include 'app/code/base/publiclists.php';
include 'app/code/base/publicdata.php';

//Initialize core instances
$core_elements = Core::init();

//Base elements
$public_info = new PublicInfo;
$public_lists = new PublicLists;
$public_data = new PublicData;

//PHP Variables
//Public user data
if (isset($_POST['pusr'])) {
    $pusr = $_POST['pusr'];
} else {
    $pusr = "";
}
if (isset($_POST['ppwd'])) {
    $ppwd = $_POST['ppwd'];
} else {
    $ppwd = "";
}
if (isset($_POST['vardumb'])) {
    $ppwdenc = $_POST['vardumb'];
} else {
    $ppwdenc = "";
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
if (isset($_GET['id'])) {
    $id = $_GET['id'];
} else {
    $id = 0;
}
if (isset($_GET['param'])) {
    $param = $_GET['param'];
} else {
    $param = "";
}

//Define User
if (isset($_COOKIE["publicactivesession"])) {
    $pusersession = explode("|",$_COOKIE["publicactivesession"]);
    $pusertmp = $pusersession[0];
    $psession = $pusersession[1];
    $puser = $core_elements->check_public_session($pusertmp,$psession);
}
if (!isset($_COOKIE["publicactivesession"]) || $puser <= 0) {
    if ($loginact == 1) {
        if (strlen($pusr) >= 6) {
            if ((strlen($ppwd) == (strlen($ppwdenc) - 5)/2) && strlen($ppwd) > 0) {
                $decppwd = $core_elements->decrypt_pwd($ppwdenc);
                $puser = $core_elements->get_public_user($pusr,$decppwd);
                if ($puser > 0) {    
                    //Verify existing user sessions
                    $usersesallowed = $core_elements->public_user_session_allowed($puser);
                    if ($usersesallowed) {
                        $psession = $core_elements->set_public_session($pusr,$puser);
                        $cvalue = "$puser|$psession";
                        setcookie("publicactivesession", $cvalue, time() + (7*24*3600), "/");
                    } else {
                        $puser = -2;
                        $login_err = "Só pode iniciar uma sessão por utilizador!";
                    }
                    $_POST = array(); $loginact = 0;
                } else {
                    $puser = -2;
                    $login_err = "Dados de acesso inválidos!";    
                } 
            } else {
                $puser = -2;
                if (strlen($ppwd) == 0) {        
                    $login_err = "Password obrigatória!";    
                } else {
                    $login_err = "Erro no módulo de login!";
                }    
            }
        } else {
            $puser = -2;
            $login_err = "Utilizador obrigatório!";
        }
    } else {
        $puser = -1;
        $login_err = "Sessão expirada!";
        $currpg = "home";
    }
}

//Logout
if ($currpg == "logout") {
    if (!isset($psession)) {
        $psession = "";
    }
    $core_elements->end_public_session($puser,$psession);
    $puser = -1;
    $cvalue = "$puser|0";
    setcookie("publicactivesession", $cvalue, 0, "/");
    $currpg = "home";

    if ($id == "request") {
        if ($param == "android") {
            $storeurl = "https://play.google.com/store";
        } elseif ($param == "ios") {
            $storeurl = "https://www.apple.com/app-store/";
        } else {
            $storeurl = "";
        }
        $redirect = filter_var($storeurl, FILTER_SANITIZE_URL);
        header("Location: $redirect");
    }
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Prevrod - Sistema de registo de atividade de condução.">
    <meta name="author" content="Prevrod - Consultadoria, Lda.">
    <meta name="keywords" content="tacógrafos, transportes, trânsito, 561, multas, motorista, CAM, formação">
    <meta name="robots" content="index, follow">

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>E-Rod</title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo $core_elements->tree_struct()->skindir->publiccss; ?>/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo $core_elements->tree_struct()->skindir->publiccss; ?>/custom.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=PT Sans" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo $core_elements->tree_struct()->skindir->publicfonts; ?>/font-awesome/all.min.css" rel="stylesheet" type="text/css">
</head>

<?php
if ($puser > 0) {
    //get pusertype
    $pusertp = $core_elements->get_public_usertype($puser);
    if ($pusertp <= 1) {
        include "pubadmin.php";
    } else { 
        include "app.php";
    }
} else {
    include "site.php";
}
?>

</html>