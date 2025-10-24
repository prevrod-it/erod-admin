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
			$journey_list = $public_lists->get_userjourney_list($userid,15);
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