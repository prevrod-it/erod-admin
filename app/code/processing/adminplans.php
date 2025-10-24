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
	$dateaction = date("Y-m-d H:i:s");
	$lastcontact = date("Y-m-d");
	$filepath = $_SERVER['DOCUMENT_ROOT'] . $core_elements->tree_struct()->mediadir->file;
	
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
	$formdesc[2] = "Item eliminado!";
	$formdesc[3] = "Não é permitido eliminar planos ativos...";
	$formdesc[4] = "Ação invãlida...";

	$valdesc = array();
	//Format
	$valdesc[1] = "Erro";
	$valdesc[2] = "Data inválida";
	//Functional
	$valdesc[3] = "Erro";
	$valdesc[4] = "Data anterior a hoje";
	$valdesc[5] = "Data igual ou anterior à de início";
	
	$valerrors[] = array(1,$formdesc[1]);

	//Collect http vars
	if (isset($_POST['entity'])) {
	    $entity = $_POST['entity'];
	} else {
		$entity = 0;
	}
	if (isset($_POST['plantype'])) {
	    $plantype = $_POST['plantype'];
	} else {
		$plantype = 0;
	}
	if (isset($_POST['nusers'])) {
	    $nusers = $_POST['nusers'];
	} else {
		$nusers = "";
	}
	if (isset($_POST['service'])) {
	    $service = $_POST['service'];
	} else {
		$service = 0;
	}
	if (isset($_POST['pstart'])) {
	    $pstart =  str_replace("/","-",$_POST['pstart']);
	} else {
		$pstart = "";
	}
	if (isset($_POST['pend'])) {
	    $pend =  str_replace("/","-",$_POST['pend']);
	} else {
		$pend = "";
	}
	if (isset($_POST['paystatus'])) {
	    $paystatus = $_POST['paystatus'];
	} else {
		$paystatus = 0;
	}
	if (isset($_POST['status'])) {
	    $status = $_POST['status'];
	} else {
		$status = 0;
	}
	
	//Validations
	if ($formaction == "insert") {
		
	} 
	if ($formaction == "insert" || $formaction == "update") {
		//#users
		if (!nemericValidation($nusers)) {
			$valerrors[] = array("nusers",$valdesc[1]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Dates
		if (!dateValidation($pstart)) {
			$valerrors[] = array("pstart",$valdesc[2]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		if (!dateValidation($pend)) {
			$valerrors[] = array("pend",$valdesc[2]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
	}
	//Delete validation
	if ($formaction == "delete") {
		//verificar plano ligado
		$isused = $admin_op->check_plan_link($itemid);
		if ($isused) {
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[3];
		}
	}

	//Functional validations
	if ($valerrors[0][0] == 1) {
		if ($formaction == "insert") {
			//Start date
			$compstart = dateCompare(date("d-m-Y"),$pstart);
			if ($compstart < 0) {
				$valerrors[] = array("pstart",$valdesc[4]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		}
		if ($formaction == "insert" || $formaction == "update") {
			//#users min/max
			$nuchk = $admin_op->check_nusers_interval($plantype,$nusers);
			if (!$nuchk) {
				$valerrors[] = array("nusers",$valdesc[3]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
			//End date
			$compend = dateCompare($pstart,$pend);
			if ($compend <= 0) {
				$valerrors[] = array("pend",$valdesc[5]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		}
	}

	//No errors
	if ($valerrors[0][0] == 1) {
		if ($formaction == "insert") {
			//Insert
			$admin_op->insert_plan($entity,$plantype,$nusers,$service,$pstart,$pend,$paystatus,$status);
		} elseif ($formaction == "update") {
			//Update
			$admin_op->update_plan($itemid,$entity,$plantype,$nusers,$service,$pstart,$pend,$paystatus,$status);
		} elseif ($formaction == "delete") {
			//Delete
			$admin_op->delete_plan($itemid);
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