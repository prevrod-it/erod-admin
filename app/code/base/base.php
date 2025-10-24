<?php
/**
**/
class BaseElements
{
	
	/*
	QUERIES
	*/

	protected function get_user_name($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT sUSRname FROM users WHERE iUSRid = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$usrname = "ERRO";
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$usrname = $usrdata[0];
			}
		}

		return $usrname; 
	}

	protected function get_user_group($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT iUSRgroup FROM users WHERE iUSRid = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		if (!is_null($usrdata[0]) && $usrdata[0] != "") {
			$usrgroup = $usrdata[0];
		} else {
			$usrgroup = 0;
		}

		return $usrgroup; 
	}

	protected function get_group_name($groupuniqueid)
	{
		$infostr = "Todos";
		if ($groupuniqueid != "0") {
			$result = Core::$mysqli->query("SELECT sGRPname FROM usrgroups WHERE iGRPid = '$groupuniqueid'");
			$info = $result->fetch_row();

			$infostr = $info[0];
		}
		return $infostr;
	}

	protected function get_entity_name($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT sENTname FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function get_entity_taxid($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT sENTtaxid FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function get_entity_email($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT sENTemail FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function get_entity_tel($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT sENTtel FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function get_entity_actualplan($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iENTplanid FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_entity_segments($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT sENTsegment FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function get_entity_status($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iENTstatus, iENTdel FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info; 
	}

	protected function get_entity_creationdate($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT dENTdatecreate FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_entity_type($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iENTtype FROM entities WHERE iENTid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function count_entity_byname($entityuniquename)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iENTid) FROM entities WHERE sENTname = '$entityuniquename' AND iENTdel = '0'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function count_entity_bytaxid($entityuniquetaxid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iENTid) FROM entities WHERE sENTtaxid = '$entityuniquetaxid' AND iENTdel = '0'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function count_entity_byemail($entityuniqueemail)
	{
		$result = Core::$mysqli->query("SELECT COUNT(sENTemail) FROM entities WHERE sENTemail = '$entityuniqueemail' AND iENTdel = '0'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function count_entity_bytel($entityuniquetel)
	{
		$result = Core::$mysqli->query("SELECT COUNT(sENTtel) FROM entities WHERE sENTtel = '$entityuniquetel' AND iENTdel = '0'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function count_opunit_byname($opuuniquename,$entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iENTid) FROM opunits WHERE sENTname = '$opuuniquename' AND iENTentityid = '$entityuniqueid' AND iENTdel = '0'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function count_vehicles_byregnum($vehuniqueregnum,$entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iVHCid) FROM vehicles WHERE sVHCregid = '$vehuniqueregnum' AND iVHCcompanyid = '$entityuniqueid' AND iVHCdel = '0'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function count_puser_bytaxid($puseruniquetaxid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iUSRid) FROM publicusers WHERE sUSRtaxid = '$puseruniquetaxid' AND iUSRdel = '0'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function count_puser_byemail($puseruniqueemail)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iUSRid) FROM publicusers WHERE sUSRemail = '$puseruniqueemail' AND iUSRdel = '0'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function count_puser_bytel($puseruniquetel)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iUSRid) FROM publicusers WHERE sUSRtel = '$puseruniquetel' AND iUSRdel = '0'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function count_puser_bydrvlic($uniquedrvlic)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iUSRid) FROM publicusers WHERE sUSRdriverlic = '$uniquedrvlic' AND iUSRdel = '0'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_active_pusers($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(*) FROM publicusers WHERE iUSRcompanyid = '$entityuniqueid' AND iUSRtype > '1' AND dUSRexpdate >= NOW() AND iUSRstatus = '1' AND iUSRdel = '0'");
		$tot = $result->fetch_row();

		return $tot[0];
	}

	protected function get_active_padmusers($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(*) FROM publicusers WHERE iUSRcompanyid = '$entityuniqueid' AND iUSRtype = '1' AND dUSRexpdate >= NOW() AND iUSRstatus = '1' AND iUSRdel = '0'");
		$tot = $result->fetch_row();

		return $tot[0];
	}	

	protected function get_vehicle_abvrinfo($vehuniqueid)
	{
		$info = array();
		$result = Core::$mysqli->query("SELECT iVHCid, sVHCregid FROM vehicles WHERE iVHCid = '$vehuniqueid'");
		$vhedata = $result->fetch_row();

		if (is_array($vhedata)) {
			$info = $vhedata;
		}

		return $info; 
	}

	protected function get_vehicle_regnum($vehuniqueid)
	{
		$info = "";
		$result = Core::$mysqli->query("SELECT sVHCregid FROM vehicles WHERE iVHCid = '$vehuniqueid'");
		$vhedata = $result->fetch_row();

		if (is_array($vhedata)) {	
			$info = $vhedata[0];
		}

		return $info; 
	}

	protected function check_company_vehicle($entityuniqueid,$vehreg)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iVHCid) FROM vehicles WHERE sVHCregid LIKE '%$vehreg%' AND iVHCcompanyid = '$entityuniqueid' AND iVHCdel = '0'");
		$count = $result->fetch_row();

		if ($count[0] > 0) {
			$check = true;
		} else {
			$check = false;
		}

		return $check;
	}

	protected function get_jorney_from_cstr($jrncontrolstr)
	{
		$result = Core::$mysqli->query("SELECT iJRNid FROM journeys WHERE  sJRNuident = '$jrncontrolstr' AND iJRNdel = '0'");
		$data = $result->fetch_row();

		if (!is_array($data)) {
			$id = 0;
		} else {
			$id = $data[0];
		}
		
		return $id;
	}
	

	protected function check_user_journey($usruniqueid,$start)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iJRNid) FROM journeys WHERE iJRNuserid = '$usruniqueid' AND dJRNstart = '$start' AND iJRNdel = '0'");
		$count = $result->fetch_row();

		if ($count[0] > 0) {
			$check = true;
		} else {
			$check = false;
		}

		return $check;
	}

	protected function check_user_journey_state($jrnuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iJRNstate FROM journeys WHERE iJRNid = '$jrnuniqueid' AND iJRNdel = '0'");
		$data = $result->fetch_row();

		if (!is_array($data)) {
			$state = -1;
		} else {
			$state = $data[0];
		}
		
		return $state;
	}

	protected function crosscheck_user_journey($usruniqueid,$jrnuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iJRNid FROM journeys WHERE iJRNid = '$jrnuniqueid' AND iJRNuserid = '$usruniqueid' AND iJRNdel = '0'");
		$data = $result->fetch_row();

		if ($jrnuniqueid > 0) {
			if (!is_array($data)) {
				$check = -1;
			} else {
				$check = $data[0];
			}
		} else {
			$check = 0;
		}
		
		return $check;
	}

	protected function check_user_vehalloc($vehuniqueid,$vehstr,$jrnuniqueid,$start,$jrncontrolstr)
	{
		if (is_null($start)) { $start = date('Y-m-d H:i:s', 0); }
		$result = Core::$mysqli->query("SELECT COUNT(iVALid) FROM vehalloc WHERE iVALvehicle = '$vehuniqueid' AND sVALvehicle = '$vehstr' AND (iVALjourney = '$jrnuniqueid' OR sVALjrnstr = '$jrncontrolstr') AND dVALstart = '$start' AND iVALdel = '0'");
		$count = $result->fetch_row();

		if ($count[0] > 0) {
			$check = true;
		} else {
			$check = false;
		}

		return $check;
	}

	protected function check_journey_vehalloc_state($valuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iVALstate FROM vehalloc WHERE iVALid = '$valuniqueid' AND iVALdel = '0'");
		$data = $result->fetch_row();

		if (!is_array($data)) {
			$state = -1;
		} else {
			$state = $data[0];
		}
		
		return $state;
	}

	protected function check_user_activity($acttype,$usruniqueid,$start)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iACTid) FROM activities WHERE iACTtype = '$acttype' AND iACTuserid = '$usruniqueid' AND dACTstart = '$start' AND iACTdel = '0'");
		$count = $result->fetch_row();

		if ($count[0] > 0) {
			$check = true;
		} else {
			$check = false;
		}

		return $check;
	}

	protected function check_user_activity_state($actuniqueid)
	{
		$result = Core::$mysqli->query("SELECT dACTend FROM activities WHERE iACTid = '$actuniqueid' AND iACTdel = '0'");
		$data = $result->fetch_row();

		if (!is_array($data)) {
			$state = -1;
		} else {
			if (!is_null($data[0]) && $data[0] != "") {
				$state = 1;	
			} else {
				$state = 0;
			}
		}
		
		return $state;
	}

	protected function check_user_remark($usruniqueid,$jrnuniqueid,$date,$remark)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iRMKid) FROM userremarks WHERE iRMKuserid = '$usruniqueid' AND iRMKjourney = '$jrnuniqueid' AND dRMKdate = '$date' AND sRMKremark = '$remark' AND iRMKdel = '0'");
		$count = $result->fetch_row();

		if ($count[0] > 0) {
			$check = true;
		} else {
			$check = false;
		}

		return $check;
	}

	protected function get_segment_name($seguniqueid)
	{
		$infostr = "Não definido";
		if ($seguniqueid != "0") {
			$result = Core::$mysqli->query("SELECT sSEGname FROM segments WHERE iSEGid = '$seguniqueid'");
			$info = $result->fetch_row();

			$infostr = $info[0];
		}

		return $infostr; 
	}

	protected function get_nut_fromzone($zonuniqueid)
	{
		if ($zonuniqueid > 0) {
			$result = Core::$mysqli->query("SELECT iZONnut FROM zones WHERE iZONid = '$zonuniqueid'");
			$info = $result->fetch_row();
			$nutid = $info[0]; 
		} else {
			$nutid = 0;
		}

		return $nutid;
	}

	protected function get_zone_name($zonuniqueid)
	{
		if ($zonuniqueid > 0) {
			$result = Core::$mysqli->query("SELECT sZONname FROM zones WHERE iZONid = '$zonuniqueid'");
			$info = $result->fetch_row();
			$name = $info[0];
		} else {
			$name = "N/A";
		}

		return $name; 
	}

	protected function get_entitytype_name($octuniqueid)
	{
		$result = Core::$mysqli->query("SELECT sETPname FROM entity_type WHERE iETPid = '$octuniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function get_oputype_name($octuniqueid)
	{
		if ($octuniqueid == 0) {
			$info = "Parque";
		} elseif ($octuniqueid == 1) {
			$info = "Armazém";
		} elseif ($octuniqueid == 2) {
			$info = "Centro logístico";
		} elseif ($octuniqueid == 3) {
			$info = "Escritório";
		} elseif ($octuniqueid == 4) {
			$info = "Estaleiro";
		} elseif ($octuniqueid == 5) {
			$info = "Fábrica";
		} elseif ($octuniqueid == 6) {
			$info = "Loja";
		} else {
			$info = "Outro";
		}

		return $info; 
	}

	protected function get_plantype_name($uniqueid)
	{
		$result = Core::$mysqli->query("SELECT sPLNname FROM plan_type WHERE iPLNid = '$uniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_vehbrand_name($uniqueid)
	{
		if ($uniqueid > 0) {
			$result = Core::$mysqli->query("SELECT sVHCbrand FROM vehbrands WHERE iVHCid = '$uniqueid'");
			$info = $result->fetch_row();
			$brand = $info[0];
		} else {
			$brand = "N/A";
		}

		return $brand;
	}

	protected function get_pubuser_type($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT iUSRtype FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$type = 0;
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$type = $usrdata[0];
			}
		}

		return $type;
	}

	protected function get_pubuser_name($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT sUSRname FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$usrname = "ERRO";
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$usrname = $usrdata[0];
			}
		}

		return $usrname;
	}

	protected function get_pubuser_taxid($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT sUSRtaxid FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$usrtaxid = "999999999";
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$usrtaxid = $usrdata[0];
			}
		}

		return $usrtaxid;
	}

	protected function get_pubuser_tel($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT sUSRtel FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$usrtel = "ERRO";
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$usrtel = $usrdata[0];
			}
		}

		return $usrtel;
	}

	protected function get_pubuser_email($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT sUSRemail FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$usremail = "N/A";
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$usremail = $usrdata[0];
			}
		}

		return $usremail;
	}

	protected function get_pubuser_driverlic($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT sUSRdriverlic FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$driverlic = "N/A";
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$driverlic = $usrdata[0];
			}
		}
		
		return $driverlic;
	}

	protected function get_pubuser_typedesc($uniquetype)
	{
		if ($uniquetype == 1) {
			$type = "Administrador";
		} elseif ($uniquetype == 2) {
			$type = "Condutor";
		} elseif ($uniquetype == 3) {
			$type = "Ajudante";
		} else {
			$type = "ERRO!";
		}

		return $type;
	}

	protected function get_pubuser_subsctypedesc($uniquetype)
	{
		if ($uniquetype == 0) {
			$type = "Individual";
		} elseif ($uniquetype == 1) {
			$type = "Empresa";
		} else {
			$type = "ERRO!";
		}

		return $type;
	}

	protected function get_pubuser_subscstatusdesc($uniquestatus)
	{
		if ($uniquestatus == -1) {
			$status = "Avaliação";
		} elseif ($uniquestatus == 0) {
			$status = "Expirada";
		} elseif ($uniquestatus == 1) {
			$status = "Válida";
		} else {
			$status = "ERRO!";
		}

		return $status;
	}

	protected function get_pubuser_lastact($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT iACTtype, dACTstart, dACTend FROM activities WHERE iACTuserid = '$usruniqueid' ORDER BY dACTstart DESC LIMIT 1");
		$info = $result->fetch_row();

		return $info; 
	}

	protected function get_pubuser_activejourney($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT iJRNid FROM journeys WHERE iJRNuserid = '$usruniqueid' AND iJRNstate = '0' AND iJRNdel = '0' ORDER by iJRNid DESC LIMIT 1");
		$info = $result->fetch_row();
		if (is_array($info)) {
			$journey = $info[0];
		} else {
			$journey = 0;
		}

		return $journey;	
	}

	protected function get_pubuser_actcount($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iACTid) FROM activities WHERE iACTuserid = '$usruniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function get_pubuser_company($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT iUSRcompanyid FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$usrentity = 0;
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$usrentity = $usrdata[0];
			}
		}

		return $usrentity;
	}

	protected function get_pubuser_subscstatus($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT iUSRsubscstatus FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$subscstatus = 0;
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$subscstatus = $usrdata[0];
			}
		}

		return $subscstatus;
	}

	protected function get_pubuser_status($usruniqueid)
	{
		$result = Core::$mysqli->query("SELECT iUSRstatus FROM publicusers WHERE iUSRid  = '$usruniqueid'");
		$usrdata = $result->fetch_row();

		$usrstatus = 0;
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$usrstatus = $usrdata[0];
			}
		}

		return $usrstatus;
	}

	protected function get_activity_timestr($seconds)
	{
		//Duration of activity
		$actmins = ceil($seconds/60);
		if ($actmins > 59) {
			$actmin = $actmins % 60;
			$acthrs = ($actmins - $actmin) / 60;
		} else {
			$actmin = $actmins;
			$acthrs = 0;
		}
		if ($acthrs > 23) {
			$acthr = $acthrs % 24;
			$actdays = ($acthrs - $acthr) / 24;
		} else {
			$acthr = $acthrs;
			$actdays = 0;
		}
		if ($actmin < 10) {
			$actmin = "0$actmin";
		}
		if ($acthr < 10) {
			$acthr = "0$acthr";
		}
		if ($actdays > 0) {
			$actday = $actdays . "d ";;
		} else {
			$actday = "";
		}
		$actdur = $actday . $acthr . "h" . $actmin . "m";

		return $actdur;
	}

	protected function get_opunit_name($opuuniqueid)
	{
		if ($opuuniqueid > 0) {
			$result = Core::$mysqli->query("SELECT sENTname FROM opunits WHERE iENTid = '$opuuniqueid'");
			$info = $result->fetch_row();
			$unit = $info[0];
		} else {
			$unit = "Sede";
		} 

		return $unit; 
	}

	protected function get_vehicle_actlinkstatus($vehuniqueid)
	{
		$actlink = false;
		if ($vehuniqueid > 0) {
			$result = Core::$mysqli->query("SELECT COUNT(iACTid) FROM activities WHERE iACTvehicle = '$vehuniqueid'");
			$tot = $result->fetch_row();
			if ($tot[0] > 0) {
				$actlink = true;
			}
		}

		return $actlink;	
	}

	protected function get_vehicle_useagestatus($vehuniqueid)
	{
		$vassoc = false;
		if ($vehuniqueid > 0) {
			$result = Core::$mysqli->query("SELECT iVALstate FROM vehalloc WHERE iVALvehicle = '$vehuniqueid' AND iVALdel = '0' ORDER BY iVALid DESC LIMIT 1");
			$data = $result->fetch_row();
			if (is_array($data)) {
				if ($data[0] == 0) {
					$vassoc = true;
				}
			}
		}

		return $vassoc;
	}

	protected function get_plan_entity($planuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iPLNentityid FROM plans WHERE iPLNid = '$planuniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_plan_type($planuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iPLNplantype FROM plans WHERE iPLNid = '$planuniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_plan_service($planuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iPLNservice FROM plans WHERE iPLNid = '$planuniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_plan_status($planuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iPLNstatus FROM plans WHERE iPLNid = '$planuniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_plan_expdate($planuniqueid)
	{
		$result = Core::$mysqli->query("SELECT dPLNend FROM plans WHERE iPLNid = '$planuniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_plan_users($planuniqueid)
	{
		$result = Core::$mysqli->query("SELECT iPLNusers FROM plans WHERE iPLNid = '$planuniqueid'");
		$info = $result->fetch_row();

		return $info[0];
	}

	protected function get_plan_linkstatus($planuniqueid)
	{
		$passoc = false;
		if ($planuniqueid > 0) {
			$result = Core::$mysqli->query("SELECT COUNT(iENTplanid) FROM entities WHERE iENTplanid = '$planuniqueid' AND iENTdel = '0'");
			$tot = $result->fetch_row();
			if ($tot[0] > 0) {
				$passoc = true;	
			}
		}

		return $passoc;
	}

	protected function get_plan_userslimit($plantypeuniqueid)
	{
		$limits = array(0,0);
		$result = Core::$mysqli->query("SELECT iPLNusersmin, iPLNusersmax FROM plan_type WHERE iPLNid = '$plantypeuniqueid'");
		$info = $result->fetch_row();
		if (is_array($info)) {
			$limits = $info;
		}

		return $limits;
	}

	protected function get_entity_actcount($entityuniqueid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(activities.iACTid) FROM activities, publicusers WHERE activities.iACTuserid = publicusers.iUSRid AND publicusers.iUSRcompanyid = '$entityuniqueid'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	protected function get_opunit_usrcount($opuuniqueid)
	{
		$result = Core::$mysqli->query("SELECT COUNT(iUSRid) FROM publicusers WHERE iUSRopunit = '$opuuniqueid' AND iUSRdel = '0'");
		$info = $result->fetch_row();

		return $info[0]; 
	}

	/*
	END QUERIES
	*/

	/*
	ACTIONS
	*/

	/*
	END ACTIONS
	*/
}
?>