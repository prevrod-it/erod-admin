<?php
//Include validation functions
include '../base/functions/validations.php';
//Include mail functions
include '../base/functions/mailcontent.php';
include '../base/functions/sendmail.php';

//Authentication
$matchcode = "b86027f4f0b60cf0234557b55744a9bf6ecf26f71df497e8533c721e1c85ec6d";
if (isset($_POST['reqcode'])) {
    $reqcode = $_POST['reqcode'];
} else {
	$reqcode = "";
}

if ($reqcode == $matchcode) {
	/*
	Initialize app
	*/

	//Resources
	include '../base/core.php';
	include '../base/base.php';
	include '../base/publicdata.php';
	include '../base/publiclists.php';
	include '../base/publicop.php';
	
	//Initialize core instances
	$core_elements = Core::init();

	//Base elements
	$public_data = new PublicData;
	$public_lists = new PublicLists;
	$public_op = new PublicOperations;

	date_default_timezone_set('EUROPE/LISBON');
	$lastupdate = date("Y-m-d H:i:s");
	
	if (isset($_POST['userid'])) {
	    $userid = $_POST['userid'];
	} else {
		$userid = 0;
	}
	if (isset($_POST['qaction'])) {
	    $qaction = $_POST['qaction'];
	} else {
		$qaction = "";
	}
	if (isset($_POST['qid'])) {
	    $qid = $_POST['qid'];
	} else {
		$qid = 0;
	}
	if (isset($_POST['pref'])) {
	    $pref = $_POST['pref'];
	} else {
		$pref = null;
	}

	// General data
	$user_company = $public_data->get_user_company($userid)[0];
	$user_opu = $public_data->get_user_company($userid)[1];
	$user_lastjourneyid = $public_data->get_user_lastjorney($userid);

	$formdesc = array();
	$formdesc[0] = "Erro! O relarório não foi enviado...";
	$formdesc[1] = "O relatório foi enviado com sucesso!";
	$formdesc[2] = "Ação invãlida...";
	$valdesc = array();
	//Format
	$valdesc[1] = "e-mail inválido";
	$valdesc[2] = "Entidade fiscalizadora inválida!";

	$valerrors[] = array(1,$formdesc[1]);

	//Collect http vars
	if (isset($_POST['rptType'])) {
	    $rptType = $_POST['rptType'];
	} else {
		$rptType = 0;
	}
	if (isset($_POST['rptEmail'])) {
	    $rptEmail = $_POST['rptEmail'];
	} else {
		$rptEmail = "";
	}

	if ($qaction == "emlreport") {
		//Validations
		//e-mail
		if (!emailValidation($rptEmail)) {
			$valerrors[] = array("rptEmail",$valdesc[1]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}

		//Functional validations
		if ($valerrors[0][0] == 1) {
			//Check authorities email
			if ($rptType == 0) {
				$emlfqdn = substr($rptEmail,strripos($rptEmail,"@"));
				if (
					strpos($emlfqdn,"gnr.pt") === false
					&& strpos($emlfqdn,"psp.pt") === false
					&& strpos($emlfqdn,"imt-ip.pt") === false
					&& strpos($emlfqdn,"asae.gov.pt") === false
					&& strpos($emlfqdn,"act.gov.pt") === false
					&& $rptEmail != "e-rod@prevrod.com"
				) {
					$valerrors[] = array("rptEmail",$valdesc[2]);
					$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];	
				}
			}
		}

		//No errors
		if ($valerrors[0][0] == 1) {
			if ($rptType == 0) {
				$user_info = $public_data->get_driver_abvrdata($userid);
				$user_name = $user_info[4];
				$user_taxid = $user_info[10];

				$jrnlist = $public_lists->get_user_rptjourneys($userid,90);
				$mailbody = array();
				$mailbody[] = "<font class=\"hd0\">Relatório de atividades de condução/outros trabalhos para entidade fiscalizadora</font><br>";
				foreach ($jrnlist as $jrnitem) {
					$actlist = $public_lists->get_user_rptactivities($userid,$jrnitem[0]);
					if ($jrnitem[7] == 1) {
						$mailbody[] = "<font class=\"hd1\">Jornada terminada a $jrnitem[4] em $jrnitem[6] ($jrnitem[5])</font>";	
					} else {
						$mailbody[] = "<font class=\"hd1\">Jornada a decorrer no momento da criação deste relatório...</font>";
					}
					foreach($actlist as $actitem) {
						$mailbody[] = "<font class=\"hl$actitem[0]\">$actitem[1]</font> - (Duração: $actitem[2]), " . "$actitem[3] &#8614; &#8612 $actitem[4], viatura: $actitem[5]";
					}
					$mailbody[] = "<font class=\"hd1\">Jornada iniciada a $jrnitem[1] em $jrnitem[3] ($jrnitem[2])</font>";
				}

				$mailtit = "Relatório de atividades E-ROD ($user_name, NIF: $user_taxid)";
				$mailhead = "$user_name";
				$mailsubject = $mailtit;
				$mailcontent = mail_content($mailtit,$mailhead,$mailbody);
				$simg = "../../../skin/publichtml/img/email_logo.png";
				send_mail("E-ROD","no-reply@prevrod.com","Entidade fiscalizadora",$rptEmail,$mailsubject,$mailcontent,$simg);
			} elseif ($rptType == 1) {
				$user_info = $public_data->get_driver_abvrdata($userid);
				$user_name = $user_info[4];
				$user_taxid = $user_info[10];

				$jrnlist = $public_lists->get_user_rptjourneys($userid,90);
				$mailbody = array();
				$mailbody[] = "<font class=\"hd0\">Relatório de atividades de condução/outros trabalhos para análise da entidade empregadora</font><br>";
				foreach ($jrnlist as $jrnitem) {
					$actlist = $public_lists->get_user_rptactivities($userid,$jrnitem[0]);
					if ($jrnitem[7] == 1) {
						$mailbody[] = "<font class=\"hd1\">Jornada terminada a $jrnitem[4] em $jrnitem[6] ($jrnitem[5])</font>";	
					} else {
						$mailbody[] = "<font class=\"hd1\">Jornada a decorrer no momento da criação deste relatório...</font>";
					}
					foreach($actlist as $actitem) {
						$mailbody[] = "<font class=\"hl$actitem[0]\">$actitem[1]</font> - (Duração: $actitem[2]), " . "$actitem[3] &#8614; &#8612 $actitem[4], viatura: $actitem[5]";
					}
					$mailbody[] = "<font class=\"hd1\">Jornada iniciada a $jrnitem[1] em $jrnitem[3] ($jrnitem[2])</font>";
				}

				$mailtit = "Relatório de atividades E-ROD ($user_name, NIF: $user_taxid)";
				$mailhead = "$user_name";
				$mailsubject = $mailtit;
				$mailcontent = mail_content($mailtit,$mailhead,$mailbody);
				$simg = "../../../skin/publichtml/img/email_logo.png";
				send_mail("E-ROD","no-reply@prevrod.com","Entidade empregadora",$rptEmail,$mailsubject,$mailcontent,$simg);
			} elseif ($rptType == 2) {
				$user_info = $public_data->get_driver_abvrdata($userid);
				$user_name = $user_info[4];
				$user_taxid = $user_info[10];

				$jrnlist = $public_lists->get_user_rptjourneys($userid,90);
				$mailbody = array();
				$mailbody[] = "<font class=\"hd0\">Relatório de atividades de condução/outros trabalhos para informação do trabalhador</font><br>";
				foreach ($jrnlist as $jrnitem) {
					$actlist = $public_lists->get_user_rptactivities($userid,$jrnitem[0]);
					if ($jrnitem[7] == 1) {
						$mailbody[] = "<font class=\"hd1\">Jornada terminada a $jrnitem[4] em $jrnitem[6] ($jrnitem[5])</font>";	
					} else {
						$mailbody[] = "<font class=\"hd1\">Jornada a decorrer no momento da criação deste relatório...</font>";
					}
					foreach($actlist as $actitem) {
						$mailbody[] = "<font class=\"hl$actitem[0]\">$actitem[1]</font> - (Duração: $actitem[2]), " . "$actitem[3] &#8614; &#8612 $actitem[4], viatura: $actitem[5]";
					}
					$mailbody[] = "<font class=\"hd1\">Jornada iniciada a $jrnitem[1] em $jrnitem[3] ($jrnitem[2])</font>";
				}

				$mailtit = "Relatório de atividades E-ROD ($user_name, NIF: $user_taxid)";
				$mailhead = "$user_name";
				$mailsubject = $mailtit;
				$mailcontent = mail_content($mailtit,$mailhead,$mailbody);
				$simg = "../../../skin/publichtml/img/email_logo.png";
				send_mail("E-ROD","no-reply@prevrod.com",$user_name,$rptEmail,$mailsubject,$mailcontent,$simg);
			} 
		}
	}

	//Response
	$response = $valerrors;	

	//Return the staus of the operation
	$jsonresp = json_encode($response);
	echo $jsonresp;
} else {
	echo "ERROR - Unathorized Access!";
}
?>