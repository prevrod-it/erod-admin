<?php
/**
**/
class PublicData extends BaseElements
{
	//****************************************************************************************************
	//Users (drivers, workers)
	//****************************************************************************************************
	public function get_driver_data($id)
	{
		$result = Core::$mysqli->query("SELECT iUSRid, iUSRcompanyid, iUSRopunit, iUSRtype, sUSRname, sUSRaddress, sUSRzipcode, sUSRziploc, sUSRemail, sUSRtel, sUSRtaxid, sUSRdriverlic, dUSRcontractini, dUSRexpdate, iUSRsubscstype, iUSRsubscstatus, dUSRdatecreate, dUSRlastupdate, iUSRstatus FROM publicusers WHERE iUSRid = '$id' AND (iUSRtype = '0' OR iUSRtype >= '2') AND iUSRdel = '0'");
		$row = $result->fetch_row();

		return $row;
	}

	public function get_driver_abvrdata($id)
	{
		$result = Core::$mysqli->query("SELECT iUSRid, iUSRcompanyid, iUSRopunit, iUSRtype, sUSRname, sUSRaddress, sUSRzipcode, sUSRziploc, sUSRemail, sUSRtel, sUSRtaxid, sUSRdriverlic, dUSRcontractini, iUSRsubscstatus, iUSRstatus FROM publicusers WHERE iUSRid = '$id' AND (iUSRtype = '0' OR iUSRtype >= '2') AND iUSRdel = '0'");
		$row = $result->fetch_row();

		return $row;
	}

	public function get_user_company($id)
	{
		$result = Core::$mysqli->query("SELECT iUSRcompanyid, iUSRopunit FROM publicusers WHERE iUSRid = '$id'");
		$usrdata = $result->fetch_row();

		$usercomp = array(0,0);
		if (is_array($usrdata)) {
			if (!is_null($usrdata[0]) && $usrdata[0] != "") {
				$usercomp[0] = $usrdata[0];
				$usercomp[1] = $usrdata[1];
			}
		}

		return $usercomp; 
	}

	public function get_company_data($id)
	{
		$result = Core::$mysqli->query("SELECT iENTid, sENTname, sENTtaxid, sENTaddress, sENTzipcode, sENTziploc, sENTemail, sENTtel, dENTdatecreate, dENTlastupdate, iENTstatus FROM entities WHERE iENTid = '$id' AND iENTdel = '0'");
		$row = $result->fetch_row();

		return $row;
	}
	
	public function get_company_abvrdata($id)
	{
		$result = Core::$mysqli->query("SELECT iENTid, sENTname, sENTtaxid, sENTaddress, sENTzipcode, sENTziploc, sENTemail, sENTtel, iENTcontscope, iENTstatus FROM entities WHERE iENTid = '$id' AND iENTdel = '0'");
		$row = $result->fetch_row();

		return $row;
	}

	public function get_companyopu_data($id)
	{
		$result = Core::$mysqli->query("SELECT iENTid, sENTname, sENTaddress, sENTzipcode, sENTziploc, dENTlastupdate, iENTstatus FROM opunits WHERE iENTid = '$id' AND iENTdel = '0'");
		$row = $result->fetch_row();

		return $row;
	}

	public function get_companyopu_abvrdata($id)
	{
		$result = Core::$mysqli->query("SELECT iENTid, sENTname, sENTaddress, sENTzipcode, sENTziploc FROM opunits WHERE iENTid = '$id' AND iENTdel = '0'");
		$row = $result->fetch_row();

		return $row;
	}

	public function get_user_lastjorney($id)
	{
		$result = Core::$mysqli->query("SELECT iJRNid FROM journeys WHERE iJRNuserid = '$id' AND iJRNdel = '0' ORDER BY dJRNstart DESC, iJRNid DESC LIMIT 1");
		$row = $result->fetch_row();

		if (is_array($row)) {
			$jrnid = $row[0];
		} else {
			$jrnid = 0;
		}

		return $jrnid;
	}

	public function get_user_rptrestbj($id,$journeyid)
	{
		$restjrnid = -1 * $journeyid;
		$restinfo = false;
		$result = Core::$mysqli->query("SELECT iACTid, iACTtype, iACTvehicle, sACTvehicle, DATE_FORMAT(dACTstart,'%d-%m-%Y %H:%i'), DATE_FORMAT(dACTend,'%d-%m-%Y %H:%i'), UNIX_TIMESTAMP(dACTend)-UNIX_TIMESTAMP(dACTstart) FROM activities WHERE iACTuserid = '$id' AND iACTjourney = '$restjrnid' LIMIT 1");
		$row = $result->fetch_row();
		if (is_array($row)) {
			$actstr = "Descanso";
			if ($row[1] == 0) { 
				if ($row[2] > 0) {
					$vehreg = self::get_vehicle_abvrinfo($row[2])[1];
				} else {
					$vehreg = $row[3];
				}
				//Duration of activity
				$actmins = ceil($row[6]/60);
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
					$actday = $actdays . "d ";
				} else {
					$actday = "";
				}
				$actdur = $actday . $acthr . "h" . $actmin . "m";  
				$restinfo = array($row[1],$actstr,$actdur,$row[4],$row[5],$vehreg);
			} 
		}

		return $restinfo;
	}

	//****************************************************************************************************
	//Management users
	//****************************************************************************************************
	public function get_entity_data($id)
	{
		$data = array();
		$result = Core::$mysqli->query("SELECT iENTid, iENTplanid, sENTsegment, sENTname, sENTtaxid, sENTaddress, sENTzipcode, sENTziploc, sENTemail, sENTtel, iENTzone, iENTtype, iENTcontscope, dENTdatecreate, dENTlastupdate, iENTstatus FROM entities WHERE iENTid = '$id' AND iENTdel = '0'");
		$row = $result->fetch_row();

		$nutid = self::get_nut_fromzone($row[10]);

		$data = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$nutid,$row[10],$row[11],$row[12],$row[13],$row[14],$row[15]);
		
		return $data;
	}

	public function get_eplan_desc($id)
	{
		if ($id > 0) {
			$ptype = self::get_plan_type($id);
			$nusers = self::get_plan_users($id);
			$ptname = self::get_plantype_name($ptype);

			$data = $ptname . " - " . $nusers . "U";
		} else {
			$data = "Sem licença";
		}

		return $data;
	}

	public function get_eplan_sl($id)
	{
		$data = "Sem serviço";
		if ($id > 0) {
			$ps = self::get_plan_service($id);
			if ($ps == 1) {
				$data = "Com serviço de acompanhamento";	
			}
		}
			
		return $data;
	}

	public function get_eusers_active($entityid)
	{
		$data = self::get_active_pusers($entityid);

		return $data;
	}

	public function get_segment_data($id)
	{
		$data = self::get_segment_name($id);

		return $data;
	}

	public function get_segment_array($segarray)
	{
		$data = array();
		foreach ($segarray as $segitem) {
			$data[] = self::get_segment_name($segitem);
		}

		return $data; 
	}

	public function get_pubuser_lastactinfo($userid)
	{	
		$lastuser_act = self::get_pubuser_lastact($userid);
		if (!is_array($lastuser_act)) {
			$info = null;
		} else {
			$lastact = $lastuser_act[0];
			$actstart = date("d-m-Y H:i",strtotime($lastuser_act[1]));
			if (!is_null($lastuser_act[2])) {
				$actdursec = strtotime($lastuser_act[2]) - strtotime($lastuser_act[1]);
			} else {
				$actdursec = time() - strtotime($lastuser_act[1]);
			}
			$actdurstr = self::get_activity_timestr($actdursec);

			$info = array($lastact,$actstart,$actdurstr);
		}

		return $info;
	}

	public function get_pubuser_data($id,$comnpanyid)
	{

		$data = array();
		$result = Core::$mysqli->query("SELECT iUSRid, iUSRcompanyid, iUSRopunit, iUSRtype, sUSRname, sUSRaddress, sUSRzipcode, sUSRziploc, sUSRemail, sUSRtel, sUSRtaxid, sUSRdriverlic, sUSRsegment, dUSRcontractini, dUSRexpdate, iUSRsubscstype, iUSRsubscstatus, dUSRdatecreate, dUSRlastupdate, iUSRstatus FROM publicusers WHERE iUSRid = '$id' AND iUSRcompanyid = '$comnpanyid' AND iUSRdel = '0'");
		$row = $result->fetch_row();

		$data = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[13],$row[14],$row[15],$row[16],$row[17],$row[18],$row[19]);
		
		return $data;
	}

	public function get_company_name($id)
	{
		$cname = self::get_entity_name($id);
		
		return $cname;
	}
}
?>