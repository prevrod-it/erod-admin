<?php
/**
**/
class AdminData extends BaseElements
{
	public function get_user_info($id)
	{

		$name = self::get_user_name($id);
		
		return $name;
	}

	public function get_entity_data($id)
	{

		$data = array();
		$result = Core::$mysqli->query("SELECT iENTid, iENTplanid, sENTsegment, sENTname, sENTtaxid, sENTaddress, sENTzipcode, sENTziploc, sENTemail, sENTtel, iENTzone, iENTtype, iENTcontscope, dENTdatecreate, dENTlastupdate, iENTstatus FROM entities WHERE iENTid = '$id' AND iENTdel = '0'");
		$row = $result->fetch_row();

		$nutid = self::get_nut_fromzone($row[10]);

		$data = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$nutid,$row[10],$row[11],$row[12],$row[13],$row[14],$row[15]);
		
		return $data;
	}

	public function get_company_name($id)
	{
		$cname = self::get_entity_name($id);
		
		return $cname;
	}

	public function get_pubuser_data($id)
	{

		$data = array();
		$result = Core::$mysqli->query("SELECT iUSRid, iUSRcompanyid, iUSRopunit, iUSRtype, sUSRname, sUSRaddress, sUSRzipcode, sUSRziploc, sUSRemail, sUSRtel, sUSRtaxid, sUSRdriverlic, sUSRsegment, dUSRcontractini, dUSRexpdate, iUSRsubscstype, iUSRsubscstatus, dUSRdatecreate, dUSRlastupdate, iUSRstatus FROM publicusers WHERE iUSRid = '$id' AND iUSRdel = '0'");
		$row = $result->fetch_row();

		$data = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[13],$row[14],$row[15],$row[16],$row[17],$row[18],$row[19]);
		
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
}
?>