<?php
//Include validation functions
include '../base/functions/validations.php';
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
	include '../base/adminop.php';
	
	//Initialize core instances
	$core_elements = Core::init();

	//Base elements
	$admin_op = new AdminOperations;

	date_default_timezone_set('EUROPE/LISBON');
	$lastupdate = date("Y-m-d H:i:s");
	
	if (isset($_POST['itemid'])) {
	    $itemid = $_POST['itemid'];
	} else {
		$itemid = 0;
	}

	if (isset($_POST['userid'])) {
	    $userid = $_POST['userid'];
	} else {
		$userid = 0;
	}
	if (isset($_POST['formaction'])) {
	    $formaction = $_POST['formaction'];
	} else {
		$formaction = "";
	}

	$formdesc = array();
	$formdesc[0] = "Erro! Os dados não foram submetidos...";
	$formdesc[1] = "Os dados foram processados com sucesso!";
	$formdesc[2] = "O item foi eliminado com sucesso!";
	$formdesc[3] = "Não é possível eliminar entidades com registos..";
	$formdesc[4] = "Ação invãlida...";
	$valdesc = array();
	//Format
	$valdesc[1] = "Designação inválida";
	$valdesc[2] = "NIF inválido";
	$valdesc[3] = "Morada inválida";
	$valdesc[4] = "Código invãido";
	$valdesc[5] = "Localidade invãida";	
	$valdesc[6] = "e-mail inválido";
	$valdesc[7] = "Telefone inválido";	
	$valdesc[8] = "Segento obrigatório";
	//Functional
	$valdesc[9] = "Entidade já existe";
	$valdesc[10] = "NIF já existe";
	$valdesc[11] = "e-mail já existe";
	$valdesc[12] = "telefone já existe";
	$valdesc[13] = "Escolha concelho ou país";
	$valdesc[14] = "Não é possível associar subscrição quando em avaliação";
	$valdesc[15] = "Subscrição invãlida";
	$valdesc[16] = "Nº de utilizadores ativos excedido";

	$valerrors[] = array(1,$formdesc[1]);

	//Collect http vars
	if (isset($_POST['entityname'])) {
	    $entityname = $_POST['entityname'];
	} else {
		$entityname = "";
	}
	if (isset($_POST['taxid'])) {
	    $taxid = $_POST['taxid'];
	} else {
		$taxid = "";
	}
	if (isset($_POST['planid'])) {
	    $planid = $_POST['planid'];
	} else {
		$planid = 0;
	}
	if (isset($_POST['address'])) {
	    $address = $_POST['address'];
	} else {
		$address = "";
	}
	if (isset($_POST['zipcode'])) {
	    $zipcode = $_POST['zipcode'];
	} else {
		$zipcode = "";
	}
	if (isset($_POST['ziploc'])) {
	    $ziploc = $_POST['ziploc'];
	} else {
		$ziploc = "";
	}
	if (isset($_POST['email'])) {
	    $email = $_POST['email'];
	} else {
		$email = "";
	}
	if (isset($_POST['tel'])) {
	    $tel = $_POST['tel'];
	} else {
		$tel = "";
	}
	if (isset($_POST['entityzone'])) {
	    $entityzone = $_POST['entityzone'];
	} else {
		$entityzone = 1;
	}
	if (isset($_POST['entitytype'])) {
	    $entitytype = $_POST['entitytype'];
	} else {
		$entitytype = 0;
	}
	if (isset($_POST['contscope'])) {
	    $contscope = $_POST['contscope'];
	} else {
		$contscope = 0;
	}
	if (isset($_POST['segment'])) {
	    $segment = $_POST['segment'];
	} else {
		$segment = "";
	}
	if (isset($_POST['status'])) {
	    $status = $_POST['status'];
	} else {
		$status = 0;
	}

	//Trsnformations
	$seg_list = implode("|",json_decode($segment));

	//Validations
	if ($formaction == "insert" || $formaction == "update") {
		//Name
		if (!textValidation($entityname) || strlen($entityname) < 3) {
			$valerrors[] = array("entityname",$valdesc[1]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Tax id
		if (!nifValidation($taxid)) {
			$valerrors[] = array("taxid",$valdesc[2]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Address
		if (!textValidation($address) || strlen($address) < 7) {
			$valerrors[] = array("address",$valdesc[3]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Zip code
		if (!zipcodeValidation($zipcode)) {
			$valerrors[] = array("zipcode",$valdesc[4]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Zip loc
		if (!textValidation($ziploc) || strlen($ziploc) < 3) {
			$valerrors[] = array("ziploc",$valdesc[5]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//e-mail
		if (!emailValidation($email) || strlen($email) < 7) {
			$valerrors[] = array("email",$valdesc[6]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Phone
		if (!phoneValidation($tel) || strlen($tel) < 9) {
			$valerrors[] = array("tel",$valdesc[7]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Segment
		if (strlen($segment) == 0 || $segment == "[0]") {
			$valerrors[] = array("mainsegment",$valdesc[8]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
	}
	//Delete validation
	if ($formaction == "delete") {
		if ($admin_op->check_entity_regs($itemid)) {
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[3];
		}
	}

	//Functional validations
	if ($valerrors[0][0] == 1) {
		//Check if entity exists
		//Check chages
		$chg = $admin_op->check_entity_changes($itemid,$entityname,$taxid);
		if ($itemid == 0 || $chg == 1 || $chg == 3) {
			if ($admin_op->check_if_entityname_exists($entityname)) {
				$valerrors[] = array("entityname",$valdesc[9]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}	
		}
		if ($itemid == 0 || $chg == 2 || $chg == 3) {
			if ($admin_op->check_if_taxid_exists($taxid)) {
				$valerrors[] = array("taxid",$valdesc[10]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		}
		$chgc = $admin_op->check_entity_contact_changes($itemid,$email,$tel);
		if ($itemid == 0 || $chgc == 1 || $chgc == 3) {
			if ($admin_op->check_if_email_exists($email)) {
				$valerrors[] = array("email",$valdesc[11]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}	
		}
		if ($itemid == 0 || $chgc == 2 || $chgc == 3) {
			if ($admin_op->check_if_tel_exists($tel)) {
				$valerrors[] = array("tel",$valdesc[12]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}	
		}
		//Zone
		if (strlen($zipcode) >= 4 && $entityzone == "1") {
			$valerrors[] = array("entityzone",$valdesc[13]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}			
		//Check subscription
		$chgp = $admin_op->check_entity_plan_changes($itemid,$planid);
		if ($itemid == 0 || $chgp == 1) {
			if (!$admin_op->check_plan_state($planid)) {
				$valerrors[] = array("planid",$valdesc[15]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			} else {
				if (!$admin_op->check_plan_users($planid)) {
					$valerrors[] = array("planid",$valdesc[16]);
					$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
				}
			}
		}
		if ($planid > 0 && $entitytype == 0) {
			$valerrors[] = array("planid",$valdesc[14]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
	}	

	//No errors
	if ($valerrors[0][0] == 1) {
		if ($formaction == "insert") {
			//Insert
			$datecreate = $lastupdate;
			$admin_op->insert_entity($planid,$seg_list,$entityname,$taxid,$address,$zipcode,$ziploc,$email,$tel,$entityzone,$entitytype,$contscope,$datecreate,$lastupdate,$status);
		} elseif ($formaction == "update") {
			//Update
			$admin_op->update_entity($itemid,$planid,$seg_list,$entityname,$taxid,$address,$zipcode,$ziploc,$email,$tel,$entityzone,$entitytype,$contscope,$lastupdate,$status);
		} elseif ($formaction == "delete") {
			//Delete
			$admin_op->delete_entity($itemid,$lastupdate);
			$admin_op->delete_entity_users($itemid,$lastupdate);
			$valerrors[0][0] = 1; $valerrors[0][1] = $formdesc[2];
		} else {
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[4];
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