<?php
//PHP Variables
//Role
if (isset($_GET['role'])) {
    $role = $_GET['role'];
} else {
	$role = "public";
}

//Redirecting...
if ($role == "public") {
    include 'frontend.php';
} elseif ($role == "admin") {
	include 'backend.php';
} else {
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8">
    <title>ERRO SISTEMA</title>
</head>
 
<body>
	<div style="text-align: center; padding-top: 10%"><h4>OCORREU UM ERRO...<br>PÁGINA NÃO ENCONTRADA OU ACESSO NÃO AUTORIZADO...</h4></div>
</body>

</html>
<?php } ?>