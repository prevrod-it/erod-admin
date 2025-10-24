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
	if (isset($form_data['qid'])) {
	    $qid = $form_data['qid'];
	} else {
		$qid = 0;
	}
	if (isset($form_data['pref'])) {
	    $pref = $form_data['pref'];
	} else {
		$pref = null;
	}

	// General data
	$user_company = $public_data->get_user_company($userid)[0];
	$user_opu = $public_data->get_user_company($userid)[1];
	$user_lastjourneyid = $public_data->get_user_lastjorney($userid);

	if ($qaction == "query") {
		if ($qid == 0) {
			$user_info = $public_data->get_driver_abvrdata($userid);
			$response = $user_info;
		} elseif ($qid == 1) {
			$company_info = $public_data->get_company_abvrdata($user_company);
			$response = $company_info;
		} elseif ($qid == 2) {
			if ($user_opu == 0) {
				$company_info = $public_data->get_company_abvrdata($user_company);
				$response = array($company_info[1],$company_info[3],$company_info[4],$company_info[5]);
			} else {
				$company_info = $public_data->get_companyopu_abvrdata($user_opu);
				$response = $company_info;
			}
		} elseif ($qid == 3) {
			$vehicles_list = $public_lists->get_vehicles_abvrlist($user_company,$userid);
			$response = $vehicles_list;
		} elseif ($qid == 4) {
			$journey_list = $public_lists->get_userjourney_list($userid,45);
			$response = $journey_list;
		} elseif ($qid == 5) {
			$vehalloclist_list = $public_lists->get_allocvehicles_list($userid,120);
			$response = $vehalloclist_list;
		} elseif ($qid == 6) {
			$activities_list = $public_lists->get_useractivities_list($userid,0,1440);
			$response = $activities_list;
		} elseif ($qid == 7) {
			$remarks_list = $public_lists->get_userremarks_list($userid,120);
			$response = $remarks_list;
		}
	}

	if ($qaction == "compare") {
		if ($qid == 3) {
			$vehicle_updated_list = $public_op->sync_vehicle_list($user_company,$userid,$pref);
			$response = $vehicle_updated_list;
		} elseif ($qid == 4) {
			$journey_updated_list = $public_op->sync_userjourney_list($userid,$pref);
			$response = $journey_updated_list;
		} elseif ($qid == 5) {
			$vehalloc_updated_list = $public_op->sync_uservehalloc_list($userid,$pref);
			$response = $vehalloc_updated_list;
		} elseif ($qid == 6) {
			$activities_updated_list = $public_op->sync_useractivities_list($userid,$pref);
			$response = $activities_updated_list;
		} elseif ($qid == 7) {
			$remarks_updated_list = $public_op->sync_userremarks_list($userid,$pref);
			$response = $remarks_updated_list;
		}
	}

	//Return the staus of the operation
	$jsonresp = json_encode($response);
	echo $jsonresp;
} else {
	echo "ERROR - Unathorized Access!";
}
?>