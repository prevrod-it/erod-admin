<?php
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
	
	if (isset($form_data['qaction'])) {
	    $qaction = $form_data['qaction'];
	} else {
		$qaction = "";
	}
	$appuser = 0; $appsession = null; $ttl = 0; $authstate = false; $autherror = ""; 
	$auth = array("appuser" => $appuser, "appsession" => $appsession, "ttl" => $ttl, "authstate" => $authstate, "autherror" => $autherror);

	if ($qaction == "applogin") {
		if (isset($form_data['appusr'])) {
		    $appusr = $form_data['appusr'];
		} else {
		    $appusr = "";
		}
		if (isset($form_data['apppwd'])) {
		    $apppwd = $form_data['apppwd'];
		} else {
		    $apppwd = "";
		}
		$decapppwd = "";

		if (strlen($appusr) >= 6) {
            if (strlen($apppwd) > 5) {
                $decapppwd = $core_elements->decrypt_pwd($apppwd);
                $appuser = $core_elements->get_public_user($appusr,$decapppwd);
                if ($appuser > 0) {
                	//Verify licences and status
                	$appuserdata = $public_data->get_driver_abvrdata($appuser);
                	$companydata = $public_data->get_company_abvrdata($appuserdata[1]);
                	$companystatus = $companydata[9];
                	$subscstatus = $appuserdata[13];
                	$appuserstatus = $appuserdata[14];
                	if ($companystatus == 1 && $subscstatus <> 0 && $appuserstatus == 1) {
	                	//Verifu user type
	                	$appusertype = $appuserdata[3];
	                	if ($appusertype >= 2) {   
		                    //Verify existing user sessions
		                    $usersesallowed = $core_elements->public_user_session_allowed($appuser);
		                    if ($usersesallowed) {
		                        $appsession = $core_elements->set_public_session($appusr,$appuser);
		                        $ttl = time() + (7*24*3600);
		                        $authstate = true;
		                    } else {
		                        $appuser = -2;
		                        $autherror = "Só pode iniciar uma sessão por utilizador!";
		                    }
		                } else {
		                	$appuser = -2;
		                    $autherror = "Tipo de utilizador inválido!";
		                }
		            } else {
		            	$appuser = -2;
		                $autherror = "Licença invãlida ou expirada!";
		            }
                } else {
                    $appuser = -2;
                    $autherror = "Dados de acesso inválidos!";    
                }
            } else {
                $appuser = -2;
                if (strlen($apppwd) == 5) {        
                    $autherror = "Password obrigatória!";    
                } else {
                    $autherror = "Erro no módulo de login!";
                }    
            }
        } else {
            $appuser = -2;
            $autherror = "Utilizador obrigatório!";
        }
	} elseif ($qaction == "appsescheck") {
		if (isset($form_data['appuser'])) {
		    $appuser = $form_data['appuser'];
		} else {
		    $appuser = 0;
		}
		if (isset($form_data['appses'])) {
		    $appses = $form_data['appses'];
		} else {
		    $appses = "";
		}

		$sesuserid = $core_elements->check_public_session($appuser,$appses);
		if ($sesuserid > 0) {
			//Verify licences and status
        	//Verify licences and status
        	$appuserdata = $public_data->get_driver_abvrdata($appuser);
        	$companydata = $public_data->get_company_abvrdata($appuserdata[1]);
        	$companystatus = $companydata[9];
        	$subscstatus = $appuserdata[13];
        	$appuserstatus = $appuserdata[14];
        	if ($companystatus == 1 && $subscstatus <> 0 && $appuserstatus == 1) {
				$appuser = $sesuserid;
				$appsession = $appses;
				$ttl = time() + (7*24*3600);
	            $authstate = true;
	        } else {
	        	$autherror = "Licença invãlida ou expirada!";
	        }
		} else {
			$autherror = "Sessão expirada ou inexistente!";
		}
	} elseif ($qaction == "applogout") {
		if (isset($form_data['appuser'])) {
		    $appuser = $form_data['appuser'];
		} else {
		    $appuser = 0;
		}
		if (isset($form_data['appses'])) {
		    $appses = $form_data['appses'];
		} else {
		    $appses = "";
		}

		if ($appuser > 0 && strlen($appses) == 32) {
			$core_elements->end_public_session($appuser,$appses);
		}
	}
	$auth["appuser"] = $appuser; $auth["appsession"] = $appsession; $auth["ttl"] = $ttl; $auth["authstate"] = $authstate; $auth["autherror"] = $autherror;

	//Return the staus of the operation
	$jsonresp = json_encode($auth);
	echo $jsonresp;
	
} else {
	echo "ERROR - Unathorized Access!";
}
?>