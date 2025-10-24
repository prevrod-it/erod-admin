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
	$formdesc[3] = "Não é possível eliminar o utilizador. ";
	$formdesc[4] = "Ação invãlida...";
	$valdesc = array();
	//Format
	$valdesc[1] = "Nome inválido";
	$valdesc[2] = "NIF inválido";
	$valdesc[3] = "Morada inválida";
	$valdesc[4] = "Código invãido";
	$valdesc[5] = "Localidade invãida";	
	$valdesc[6] = "e-mail inválido";
	$valdesc[7] = "Telefone inválido";
	$valdesc[8] = "Carta de condução inválida";
	$valdesc[9] = "Data inválida";
	$valdesc[10] = "Segento obrigatório";
	//Functional
	$valdesc[11] = "NIF já existe";
	$valdesc[12] = "e-mail já existe";
	$valdesc[13] = "Telefone já existe";
	$valdesc[14] = "Carta de condução já existe";
	$valdesc[15] = "Início de contrato inválido";
	$valdesc[16] = "Tipo de subscrição inválida";
	$valdesc[17] = "Avaliação não permitida";
	$valdesc[18] = "Utilizador não expirado";
	$valdesc[19] = "Licença expirada";
	$valdesc[20] = "Password inválida";
	$valdesc[21] = "Nº de utilizadores ativos excedido";
	$valdesc[22] = "Data de expiração inválida";
	$valdesc[23] = "Registo de atividades existente...";
	$valdesc[24] = "Utilizador em atividade";
	$valdesc[25] = "Tipo de utilizador invãido";

	$valerrors[] = array(1,$formdesc[1]);

	//Collect http vars
	 if (isset($_POST['entityid'])) {
        $entityid = $_POST['entityid'];
    } else {
        $entityid = 0;
    }
	if (isset($_POST['name'])) {
	    $name = $_POST['name'];
	} else {
		$name = "";
	}
	if (isset($_POST['taxid'])) {
	    $taxid = $_POST['taxid'];
	} else {
		$taxid = "";
	}
	if (isset($_POST['type'])) {
	    $type = $_POST['type'];
	} else {
		$type = 0;
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
	if (isset($_POST['opunit'])) {
	    $opunit = $_POST['opunit'];
	} else {
		$opunit = 0;
	}
	if (isset($_POST['driverlic'])) {
	    $driverlic = $_POST['driverlic'];
	} else {
		$driverlic = "";
	}
	if (isset($_POST['contractini'])) {
	    $contractini =  str_replace("/","-",$_POST['contractini']);
	} else {
		$contractini = "";
	}
	if (isset($_POST['subsctype'])) {
	    $subsctype = $_POST['subsctype'];
	} else {
		$subsctype = 0;
	}
	if (isset($_POST['subscstatus'])) {
	    $subscstatus = $_POST['subscstatus'];
	} else {
		$subscstatus = 0;
	}
	if (isset($_POST['segment'])) {
	    $segment = $_POST['segment'];
	} else {
		$segment = "";
	}
	if (isset($_POST['puserpwd'])) {
	    $puserpwd = $_POST['puserpwd'];
	    if ($puserpwd == "KeepPassword") {
	    	$puserpwd = "";
	    }
	} else {
		$puserpwd = "";
	}
	if (isset($_POST['expdate'])) {
	    $expdate =  str_replace("/","-",$_POST['expdate']);
	} else {
		$expdate = "";
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
		if (!textValidation($name) || strlen($name) < 3) {
			$valerrors[] = array("name",$valdesc[1]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Tax id
		if (($type > 1 || strlen($taxid) > 0) && !nifValidation($taxid)) {
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
		if ((strlen($email) > 0 || $type == 1) && (!emailValidation($email) || strlen($email) < 7)) {
			$valerrors[] = array("email",$valdesc[6]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Phone
		if (!phoneValidation($tel) || strlen($tel) < 9) {
			$valerrors[] = array("tel",$valdesc[7]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Driving license
		if (strlen($driverlic) > 0 && (!textValidation($driverlic) || strlen($driverlic) < 6)) {
			$valerrors[] = array("driverlic",$valdesc[8]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Dates
		if (!dateValidation($contractini)) {
			$valerrors[] = array("contractini",$valdesc[9]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		if (!dateValidation($expdate)) {
			$valerrors[] = array("expdate",$valdesc[9]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Segment
		if (strlen($segment) == 0 || $segment == "[0]") {
			$valerrors[] = array("mainsegment",$valdesc[10]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
	}
	//Delete validation
	if ($formaction == "delete") {
		if ($admin_op->check_pubuser_regs($itemid)) {
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[3] . $valdesc[23];
		} else {
			if ($admin_op->check_pubuser_jrnstate($itemid)) {
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[3] . $valdesc[24];
			}
		}
	}

	//Functional validations
	if ($valerrors[0][0] == 1) {
		$evalstatus = $admin_op->check_entity_evaluation($entityid);
		$company_plan = $admin_op->check_entity_plan($entityid);
		$company_cdate = $admin_op->check_entity_cdate($entityid);
		//Check if user exists
		//Check chages
		$chg1 = $admin_op->check_pubuser_changes($itemid,$taxid,$tel);
		if ($itemid == 0 || $chg1 == 1 || $chg1 == 3) {
			if ($admin_op->check_if_putaxid_exists($taxid)) {
				$valerrors[] = array("taxid",$valdesc[11]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		}
		if ($itemid == 0 || $chg1 == 2 || $chg1 == 3) {
			if ($admin_op->check_if_putel_exists($tel)) {
				$valerrors[] = array("tel",$valdesc[13]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}	
		}
		$chg2 = $admin_op->check_pubuser_altchanges($itemid,$email,$driverlic);
		if ($itemid == 0 || $chg2 == 1 || $chg2 == 3) {
			if ($admin_op->check_if_puemail_exists($email)) {
				$valerrors[] = array("email",$valdesc[12]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		}
		if ($itemid == 0 || $chg2 == 2 || $chg2 == 3) {
			if ($admin_op->check_if_pudrvlic_exists($driverlic)) {
				$valerrors[] = array("driverlic",$valdesc[14]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}	
		}
		//Constract start date
		$todayplus30days = date("d-m-Y",time() + 2592000);
		$cicomp = dateCompare($contractini,$todayplus30days);
		if ($cicomp == -1) {
			$valerrors[] = array("contractini",$valdesc[15]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Subscription type
		if ((int)substr($taxid,0,1) >= 4 && (int)substr($taxid,0,2) <> 45) {
			$valerrors[] = array("subsctype",$valdesc[16]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		if ($subsctype == 0 && ($company_plan[1] <> 1 || $company_plan[2] > 1)) {
			$valerrors[] = array("subsctype",$valdesc[16]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
		//Subscription status
		$plan_expdate = date("d-m-Y",strtotime($company_plan[3]));
		$ped_comp = dateCompare(date("d-m-Y"),$plan_expdate);
		if ($subscstatus == -1) {
			if (!$evalstatus) {
				$valerrors[] = array("subscstatus",$valdesc[17]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		} elseif ($subscstatus == 0) {
			if ($ped_comp > 0) {
				$valerrors[] = array("subscstatus",$valdesc[18]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		} elseif ($subscstatus == 1) {
			if ($ped_comp < 0) {
				$valerrors[] = array("subscstatus",$valdesc[19]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		}
		//Password
		if (($formaction == "insert" || strlen($puserpwd) > 0) && (strlen($puserpwd) < 6 || !pwdValidation($puserpwd))) {
			$valerrors[] = array("puserpwd",$valdesc[20]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		} else {
			if (strlen($puserpwd) > 0 && $puserpwd != "") {
				$updatepwd = true;
				$hashpwd = hash("sha256",$puserpwd);
			} else {
				$updatepwd = false;
				$hashpwd = null;
			}
		}
		//Status
		if ($status == 1) {
			if ($admin_op->chack_max_activeusers($entityid,$itemid,$type)) {
				$valerrors[] = array("status",$valdesc[21]);
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];	
			}
		} else {
			if ($type == 2 || $type == 3) {
				if ($admin_op->check_pubuser_jrnstate($itemid)) {
					$valerrors[] = array("status",$valdesc[24]);
					$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
				}
			}
		}
		//Experiation date
		if ($evalstatus) {
			$ccdatets = strtotime($company_cdate);
			$ccdateplus30days = date("d-m-Y",$ccdatets + 2592000);
			$edcomp = dateCompare($expdate,$ccdateplus30days);
			if ($edcomp < 0) {
				$valerrors[] = array("expdate",$valdesc[22] . ", limite: " . str_replace("-","/",$ccdateplus30days));
				$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
			}
		} else {
			if ($company_plan[0] == 0) {
				$edcomp = dateCompare($expdate,date("Y-m-d"));
				if ($edcomp < 0) {
					$valerrors[] = array("expdate",$valdesc[22]);
					$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
				}
			} else {
				$edcomp = dateCompare($expdate,$company_plan[3]);
				if ($edcomp <> 0) {
					$valerrors[] = array("expdate",$valdesc[22] . ", deverá ser " . date("d/m/Y",strtotime($company_plan[3])));
					$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
				}
			}
		}
		//User type
		if ($type < 1 || $type > 2) {
			$valerrors[] = array("type",$valdesc[25]);
			$valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
		}
	}	
	
	//No errors
	if ($valerrors[0][0] == 1) {
		if ($formaction == "insert") {
			//Insert
			$datecreate = $lastupdate;
			$admin_op->insert_pubuser($name,$taxid,$type,$address,$zipcode,$ziploc,$email,$tel,$entityid,$opunit,$driverlic,$contractini,$subsctype,$subscstatus,$seg_list,$hashpwd,$expdate,$datecreate,$lastupdate,$status);
		} elseif ($formaction == "update") {
			//Update
			$admin_op->update_pubuser($itemid,$name,$taxid,$type,$address,$zipcode,$ziploc,$email,$tel,$entityid,$opunit,$driverlic,$contractini,$subsctype,$subscstatus,$seg_list,$expdate,$lastupdate,$status);
			if ($updatepwd) {
				$admin_op->update_pubuser_pwd($itemid,$hashpwd);
			} 
		} elseif ($formaction == "delete") {
			//Delete
			$admin_op->delete_pubuser($itemid,$lastupdate);
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