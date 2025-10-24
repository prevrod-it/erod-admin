<?php
//Include validation functions
include '../base/functions/validations.php';
//Include mail functions
include '../base/functions/mailcontent.php';
include '../base/functions/sendmail.php';

//Authentication
$matchcode = "12348c35ae265f8b6c875de5b5e6eb8e1106e6d365ac03412be1ef78dcb0800b";
$json_form_data = file_get_contents('php://input');
$form_data = json_decode($json_form_data, true);

if (isset($form_data['reqcode'])) {
    $reqcode = $form_data['reqcode'];
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
	
	if (isset($form_data['userid'])) {
	    $userid = $form_data['userid'];
	} else {
		$userid = 0;
	}
	if (isset($form_data['qaction'])) {
	    $qaction = $form_data['qaction'];
	} else {
		$qaction = "";
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
	if (isset($form_data['rptType'])) {
	    $rptType = $form_data['rptType'];
	} else {
		$rptType = 0;
	}
	if (isset($form_data['rptEmail'])) {
	    $rptEmail = $form_data['rptEmail'];
	} else {
		$rptEmail = "";
	}

	if ($qaction == "emlreport") {
		//Validations
		//e-mail
		if (!emailValidation($rptEmail)) {
			$valerrors[] = array("rptEmail",$valdesc[1] . " - " . $rptEmail);
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
				$rptspan = 16;
				
				$jrnlist = $public_lists->get_user_rptjourneys($userid,$rptspan);
				$mailbody = array();
				$mailbody[] = "<font class=\"hd0\">Relatório de atividades de condução/outros trabalhos para entidade fiscalizadora</font><br>";
				foreach ($jrnlist as $jrnitem) {
					$actlist = $public_lists->get_user_rptactivities($userid,$jrnitem[0]);
					$actcount = 0;
					foreach($actlist as $actitem) {
						if ($actcount == 0) {
							//Check for rest between journeys
							$restbj = $public_data->get_user_rptrestbj($userid,$jrnitem[0]);
							if (is_array($restbj)) {
								$mailbody[] = "<font class=\"hl0\">$restbj[1]</font> - (Duração: $restbj[2]), " . "$restbj[3] &#8614; &#8612; $restbj[4]";
							}
							if ($jrnitem[7] == 1) {
								$mailbody[] = "<font class=\"hd1\">Jornada terminada a $jrnitem[4] em $jrnitem[6] ($jrnitem[5])</font>";	
							} else {
								$mailbody[] = "<font class=\"hd1\">Jornada a decorrer no momento da criação deste relatório...</font>";
							}
						}
						if (strlen($actitem[5] > 0)) { $vehstr = ", viatura: $actitem[5]"; } else { $vehstr = ""; }
						$mailbody[] = "<font class=\"hl$actitem[0]\">$actitem[1]</font> - (Duração: $actitem[2]), " . "$actitem[3] &#8614; &#8612; $actitem[4]" . $vehstr;
						$actcount ++;
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
				$rptspan = 31;

				$jrnlist = $public_lists->get_user_rptjourneys($userid,$rptspan);
				$mailbody = array();
				$mailbody[] = "<font class=\"hd0\">Relatório de atividades de condução/outros trabalhos para análise da entidade empregadora</font><br>";
				foreach ($jrnlist as $jrnitem) {
					$actlist = $public_lists->get_user_rptactivities($userid,$jrnitem[0]);
					$actcount = 0;
					foreach($actlist as $actitem) {
						if ($actcount == 0) {
							//Check for rest between journeys
							$restbj = $public_data->get_user_rptrestbj($userid,$jrnitem[0]);
							if (is_array($restbj)) {
								$mailbody[] = "<font class=\"hl0\">$restbj[1]</font> - (Duração: $restbj[2]), " . "$restbj[3] &#8614; &#8612; $restbj[4]";
							}
							if ($jrnitem[7] == 1) {
								$mailbody[] = "<font class=\"hd1\">Jornada terminada a $jrnitem[4] em $jrnitem[6] ($jrnitem[5])</font>";	
							} else {
								$mailbody[] = "<font class=\"hd1\">Jornada a decorrer no momento da criação deste relatório...</font>";
							}
						}
						if (strlen($actitem[5]) > 0) { $vehstr = ", viatura: $actitem[5]"; } else { $vehstr = ""; }
						$mailbody[] = "<font class=\"hl$actitem[0]\">$actitem[1]</font> - (Duração: $actitem[2]), " . "$actitem[3] &#8614; &#8612; $actitem[4]" . $vehstr;
						$actcount ++;
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
				$rptspan = 31;

				$jrnlist = $public_lists->get_user_rptjourneys($userid,$rptspan);
				$mailbody = array();
				$mailbody[] = "<font class=\"hd0\">Relatório de atividades de condução/outros trabalhos para informação do trabalhador</font><br>";
				foreach ($jrnlist as $jrnitem) {
					$actlist = $public_lists->get_user_rptactivities($userid,$jrnitem[0]);
					$actcount = 0;
					foreach($actlist as $actitem) {
						if ($actcount == 0) {
							//Check for rest between journeys
							$restbj = $public_data->get_user_rptrestbj($userid,$jrnitem[0]);
							if (is_array($restbj)) {
								$mailbody[] = "<font class=\"hl0\">$restbj[1]</font> - (Duração: $restbj[2]), " . "$restbj[3] &#8614; &#8612; $restbj[4]";
							}
							if ($jrnitem[7] == 1) {
								$mailbody[] = "<font class=\"hd1\">Jornada terminada a $jrnitem[4] em $jrnitem[6] ($jrnitem[5])</font>";	
							} else {
								$mailbody[] = "<font class=\"hd1\">Jornada a decorrer no momento da criação deste relatório...</font>";
							}
						}
						if (strlen($actitem[5] > 0)) { $vehstr = ", viatura: $actitem[5]"; } else { $vehstr = ""; }
						$mailbody[] = "<font class=\"hl$actitem[0]\">$actitem[1]</font> - (Duração: $actitem[2]), " . "$actitem[3] &#8614; &#8612; $actitem[4]" . $vehstr;
						$actcount ++;
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