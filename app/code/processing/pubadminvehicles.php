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
	$formdesc[3] = "Não é permitido eliminar viaturas com registos...";
	$formdesc[4] = "Ação invãlida...";

	$valdesc = array();
	//Format
	$valdesc[1] = "Matrícula inválida";
	$valdesc[2] = "Modelo inválido";
	//Functional
	$valdesc[3] = "Viatura já existe!";
	$valdesc[4] = "Viatura em uso";
	
	$valerrors[] = array(1,$formdesc[1]);

	//Collect http vars
	if (isset($_POST['entity'])) {
	    $entity = $_POST['entity'];
	} else {
		$entity = 0;
	}
	if (isset($_POST['regnum'])) {
	    $regnum = $_POST['regnum'];
	} else {
		$regnum = "";
	}
	if (isset($_POST['vehbrand'])) {
	    $vehbrand = $_POST['vehbrand'];
	} else {
		$vehbrand = 0;
	}
	if (isset($_POST['vehmodel'])) {
	    $vehmodel = $_POST['vehmodel'];
	} else {
		$vehmodel = "";
	}
	if (isset($_POST['status'])) {
	    $status = $_POST['status'];
	} else {
		$status = 0;
	}
	
	//Trnasformations
	$chrtorplcns = array(".","/","_");
	$chrtorplcws = array(" ",".","/","_");
	if (strpos($regnum,"-") !== false) {
		$stdvrg = str_replace($chrtorplcns,"-",$regnum);
	} else {
		$stdvrg = str_replace($chrtorplcws,"-",$regnum);
	}
	$stdvrg = strtoupper($stdvrg);

	//Validations
	if ($formaction == "insert" || $formaction == "update") {
		//Registration number
		if (!regplateValidation($regnum)) {
			$valerrors[] = array("regnum",$valdesc[1]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Model
		if (strlen($vehmodel) > 0 && !textValidation($vehmodel)) {
			$valerrors[] = array("vehmodel",$valdesc[2]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}		
	}
	//Delete validation
	if ($formaction == "delete") {
		//verificar viatura ligada
		$isused = $admin_op->check_vehicle_link($itemid);
		if ($isused) {
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[3];
		}
	}

	//Functional validations
	if ($valerrors[0][0] == 1) {
		if ($formaction == "insert" || $formaction == "update") {
			//Check if vehicle exists
	        //Check chages
	        $chg = $admin_op->check_vehicle_changes($itemid,$stdvrg);
	        if ($itemid == 0 || $chg == 1) {
	            if ($admin_op->check_if_vehicle_exists($entity,$stdvrg)) {
	                $valerrors[] = array("regnum",$valdesc[3]);
	                $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
	            }   
	        }
		}
		if ($formaction == "update") {
			//Check if vehicle is in use
			$vehinuse = $admin_op->check_vehicle_usage($itemid);
			if ($status == 0 && $vehinuse) {
				$valerrors[] = array("status",$valdesc[4]);
	            $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		}
	}

	//No errors
	if ($valerrors[0][0] == 1) {
		if ($formaction == "insert") {
			//Insert
			$admin_op->insert_vehicle($entity,$stdvrg,$vehbrand,$vehmodel,$status);
		} elseif ($formaction == "update") {
			//Update
			$admin_op->update_vehicle($itemid,$entity,$stdvrg,$vehbrand,$vehmodel,$status);
		} elseif ($formaction == "delete") {
			//Delete
			$admin_op->delete_vehicle($itemid);
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