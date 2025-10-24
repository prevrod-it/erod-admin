<?php
/**
**/
class AdminLists extends BaseElements
{
	
	public function group_list()
	{
		$result = Core::$mysqli->query("SELECT iGRPid, sGRPname FROM usrgroups WHERE iGRPstatus = '1' AND iGRPdel = '0' ORDER BY sGRPname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
		}

		return $list; 
	}

	public function users_list($groupid)
	{
		
		if ($groupid == 0) {
			$condflt = "iUSRgroup >= '0'";
		} else {	
			$condflt = "(iUSRgroup = '$groupid' OR iUSRgroup = '0')";
		}

		$result = Core::$mysqli->query("SELECT iUSRid, sUSRname, sUSRemail, iUSRgroup FROM users WHERE $condflt AND iUSRstatus = '1' AND iUSRdel = '0' ORDER BY sUSRname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1],$row[2],$row[3]);
		}
 		
		return $list; 
	}

	public function nut_list()
	{
		$result = Core::$mysqli->query("SELECT iNUTid, sNUTname FROM nuts WHERE iNUTid > '1' ORDER BY iNUTid ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
		}

		return $list; 
	}

	public function zone_list($nutid)
	{
		if ($nutid == "0") {
			$condflt = "iZONnut > '0'";
		} else {
			$condflt = "iZONnut = '$nutid'";
		}

		$result = Core::$mysqli->query("SELECT iZONid, iZONnut, sZONname FROM zones WHERE iZONid > '1' AND $condflt ORDER BY sZONname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[2],$row[1]);
		}

		return $list; 
	}

	public function segment_list()
	{
		
		$result = Core::$mysqli->query("SELECT iSEGid, sSEGname FROM segments WHERE iSEGstatus = '1' AND iSEGdel = '0' ORDER BY sSEGname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
		}

		return $list; 
	}

	public function entity_segment_list($entityid)
	{
	
		$allsegemnts = self::get_entity_segments($entityid);
		$allsegemnts_arr = explode("|",$allsegemnts);

		$result = Core::$mysqli->query("SELECT iSEGid, sSEGname FROM segments WHERE iSEGstatus = '1' AND iSEGdel = '0' ORDER BY sSEGname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$match = array_search($row[0],$allsegemnts_arr);
			if ($match !== false && (int)$match >= 0) {
				$list[] = array($row[0],$row[1]);	
			}
		}

		return $list; 
	}

	public function etype_list()
	{
		$result = Core::$mysqli->query("SELECT iETPid, sETPname FROM entity_type WHERE iETPstatus = '1' AND iETPdel = '0' ORDER BY sETPname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
		}

		return $list; 
	}

	public function contscope_list()
	{
		$list = array();
		$list[] = array(0,"Contrato individual");
		$list[] = array(1,"Contrato coletivo");
 		
		return $list; 
	}

	public function oputype_list()
	{
		$list = array();
		$list[] = array(0,"Parque");
		$list[] = array(1,"Armazém");
		$list[] = array(2,"Centro logístico");
		$list[] = array(3,"Escritório");
		$list[] = array(4,"Estaleiro");
		$list[] = array(5,"Fábrica");
		$list[] = array(6,"Loja");

		return $list; 
	}

	public function pubusertype_list()
	{
		$list = array();
		$list[] = array(1,"Administrador");
		$list[] = array(2,"Condutor");
		$list[] = array(3,"Ajudante");

		return $list;
	}

	public function pubusersubsc_type_list()
	{
		$list = array();
		$list[] = array(0,"Individual");
		$list[] = array(1,"Empresa");

		return $list;
	}

	public function pubusersubsc_status_list()
	{
		$list = array();
		$list[] = array(-1,"Avaliação");
		$list[] = array(0,"Expirada");
		$list[] = array(1,"Válida");

		return $list;
	}

	public function plantype_list()
	{
		$result = Core::$mysqli->query("SELECT iPLNid, sPLNname FROM plan_type WHERE iPLNstatus = '1' AND iPLNdel = '0' ORDER BY iPLNusersmin ASC, sPLNname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
		}
 		
		return $list;
	}

	public function vehiclebrands_list()
	{
		$result = Core::$mysqli->query("SELECT iVHCid, sVHCbrand FROM vehbrands ORDER BY sVHCbrand ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
		}
 		
		return $list;
	}

	public function entities_list_count($groupid,$filter)
	{
		
		$entityflt = $filter["entityflt"];
		$nutflt = $filter["nutflt"];
		$zoneflt = $filter["zoneflt"];
		$segmentflt = $filter["segmentflt"];
		$etypeflt = $filter["etypeflt"];

		$condflt = "(entities.sENTname LIKE '%$entityflt%' OR entities.sENTtaxid LIKE '%$entityflt%')"; 	
		if ($nutflt == "0") {
			$condflt .= " AND zones.iZONnut = nuts.iNUTid AND zones.iZONnut > '0'"; 
		} else {
			$condflt .= " AND zones.iZONnut = nuts.iNUTid AND nuts.iNUTid = '$nutflt'";
		}
		if ($zoneflt == "0") {
			$condflt .= " AND entities.iENTzone = zones.iZONid AND entities.iENTzone > '0'"; 
		} else {
			$condflt .= " AND entities.iENTzone = zones.iZONid AND zones.iZONid = '$zoneflt'";
		}
		if ($segmentflt > "0") {
			$condflt .= " AND FIND_IN_SET($segmentflt,REPLACE(entities.sENTsegment,'|','\,'))";
		} elseif ($segmentflt == "") {
			$condflt .= " AND entities.sENTsegment = ''";
		}
		if ($etypeflt == "-1") {
			$condflt .= " AND entities.iENTtype > '-1'"; 
		} else {
			$condflt .= " AND entities.iENTtype = '$etypeflt'";
		}
		if ($groupid > "0") {
			$condflt .= " AND entities.iENTstatus = '1'";
		}		

		$result = Core::$mysqli->query("SELECT COUNT(*) FROM entities, zones, nuts WHERE $condflt AND entities.iENTdel = '0'");
		$tot = $result->fetch_row();
 		
		return $tot[0]; 
	}

	public function entities_list($groupid,$filter,$offset,$limit,$sortfld)
	{
		
		$entityflt = $filter["entityflt"];
		$nutflt = $filter["nutflt"];
		$zoneflt = $filter["zoneflt"];
		$segmentflt = $filter["segmentflt"];
		$etypeflt = $filter["etypeflt"];

		$condflt = "(entities.sENTname LIKE '%$entityflt%' OR entities.sENTtaxid LIKE '%$entityflt%')"; 	
		if ($nutflt == "0") {
			$condflt .= " AND zones.iZONnut = nuts.iNUTid AND zones.iZONnut > '0'"; 
		} else {
			$condflt .= " AND zones.iZONnut = nuts.iNUTid AND nuts.iNUTid = '$nutflt'";
		}
		if ($zoneflt == "0") {
			$condflt .= " AND entities.iENTzone = zones.iZONid AND entities.iENTzone > '0'"; 
		} else {
			$condflt .= " AND entities.iENTzone = zones.iZONid AND zones.iZONid = '$zoneflt'";
		}
		if ($segmentflt > "0") {
			$condflt .= " AND FIND_IN_SET($segmentflt,REPLACE(entities.sENTsegment,'|','\,'))";
		} elseif ($segmentflt == "") {
			$condflt .= " AND entities.sENTsegment = ''";
		}
		if ($etypeflt == "-1") {
			$condflt .= " AND entities.iENTtype > '-1'"; 
		} else {
			$condflt .= " AND entities.iENTtype = '$etypeflt'";
		}
		if ($groupid > "0") {
			$condflt .= " AND entities.iENTstatus = '1'";
		}		

		if ($sortfld == "1") {
			$sortcmd = "entities.sENTname ASC";
		} elseif ($sortfld == "-1") {
			$sortcmd = "entities.sENTname DESC";
		} else {
			$sortcmd = "entities.sENTname ASC";
		}		

		$result = Core::$mysqli->query("SELECT entities.iENTid, entities.iENTplanid, entities.sENTsegment, entities.sENTname, entities.iENTzone, entities.iENTtype, entities.iENTstatus FROM entities, zones, nuts WHERE $condflt AND entities.iENTdel = '0' ORDER BY $sortcmd LIMIT $offset,$limit");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			//Ref
 			if (strlen($row[0]) < 6) {
 				$remz = 9-strlen($row[0]);
 				$eref = str_repeat("0",$remz) . $row[0];
 			} else {
 				$eref = $row[0];
 			}
 			//Segments
 			$seg_arr = explode("|",$row[2]);
 			$i=0;
 			$seg_str = "";
 			foreach ($seg_arr as $seg_item) {
 				if ($i>0) { $seg_str .= ", "; }
 				$seg_name = self::get_segment_name($seg_item);
 				$seg_str .= $seg_name;
 				$i++;
 			}
 			//Zones
 			$zone = self::get_zone_name($row[4]);
 			//Type
 			if ($row[5] != 0) {
 				$enttype = self::get_entitytype_name($row[5]);
 			} else {
 				$enttype = "Em avaliação";
 			}

 			$list[] = array($row[0],$eref,$row[3],$zone,$seg_str,$enttype,$row[6]);
		}
 		
		return $list; 
	}

	public function entityopus_list($entityid,$opustate)
	{
		if ($opustate == -1) {
			$condflt = "iENTstatus >= '0'";
		} else {
			$condflt = "iENTstatus = '$opustate'";
		}

		$result = Core::$mysqli->query("SELECT iENTid, iENTentityid, sENTname, sENTaddress, sENTzipcode, sENTziploc, iENTzone, iENTtype, dENTdatecreate, dENTlastupdate, iENTstatus FROM opunits WHERE iENTentityid = '$entityid' AND $condflt AND iENTdel = '0' ORDER BY sENTname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			//Nut
 			$nutid = self::get_nut_fromzone($row[6]);
 			//Zones
 			$zone = self::get_zone_name($row[6]);
 			//Type
 			$oputype = self::get_oputype_name($row[7]);
 			
 			$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$nutid,$row[6],$zone,$row[7],$oputype,$row[8],$row[9],$row[10]);
 		}

 		return $list; 
	}

	public function entityopus_select_list($entityid)
	{
		$result = Core::$mysqli->query("SELECT iENTid, sENTname FROM opunits WHERE iENTentityid = '$entityid' AND iENTstatus = '1' AND iENTdel = '0' ORDER BY sENTname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
 		}

 		return $list; 
	}

	public function entityvehcs_list($entityid,$vehstate)
	{
		if ($vehstate == -1) {
			$condflt = "iVHCstatus >= '0'";
		} else {
			$condflt = "iVHCstatus = '$vehstate'";
		}

		$result = Core::$mysqli->query("SELECT iVHCid, iVHCcompanyid, sVHCregid, iVHCbrand, sVHCmodel, iVHCstatus FROM vehicles WHERE iVHCcompanyid = '$entityid' AND $condflt AND iVHCdel = '0' ORDER BY sVHCregid ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			//Brand
 			$vehbrabd = self::get_vehbrand_name($row[3]);
 			
 			$list[] = array($row[0],$row[1],$row[2],$row[3],$vehbrabd,$row[4],$row[5]);
 		}

 		return $list; 
	}

	public function entityplans_list($entityid,$planstate)
	{
		if ($planstate == "0") {
			$condflt = "iPLNstatus >= '0'";
		} elseif ($planstate == "1") {
			$condflt = "iPLNstatus = '1' AND dPLNend > NOW()";
		}	

		$result = Core::$mysqli->query("SELECT iPLNid, iPLNplantype, iPLNusers, iPLNservice, dPLNstart, dPLNend, iPLNpayment, iPLNstatus FROM plans WHERE iPLNentityid = '$entityid' AND $condflt AND iPLNdel = '0'");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$plandes = self::get_plantype_name($row[1]);
 			$pstart = date("d/m/Y",strtotime($row[4]));
 			$pend = date("d/m/Y",strtotime($row[5]));
 			$link = self::get_plan_linkstatus($row[0]);

 			$list[] = array($row[0],$row[1],$plandes,$row[2],$row[3],$pstart,$pend,$row[6],$row[7],$link);
		}
 		
		return $list;
	}

	public function entityusers_list_count($entityid,$usertype)
	{
		$condflt = "iUSRcompanyid = '$entityid'";
		if ($usertype > "0") {
			$condflt .= " AND iUSRtype= '$usertype'";
		}		

		$result = Core::$mysqli->query("SELECT COUNT(*) FROM publicusers WHERE $condflt AND iUSRdel = '0'");
 		$tot = $result->fetch_row();
 		
		return $tot[0]; 
	}

	public function entityusers_list($entityid,$usertype)
	{
		$condflt = "iUSRcompanyid = '$entityid'";
		if ($usertype > "0") {
			$condflt .= " AND iUSRtype = '$usertype'";
		}		

		$result = Core::$mysqli->query("SELECT iUSRid, iUSRtype, sUSRname, sUSRtaxid, iUSRsubscstatus, iUSRstatus FROM publicusers WHERE $condflt AND iUSRdel = '0' ORDER BY iUSRtype ASC, sUSRname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			//Type
 			$usertype = self::get_pubuser_typedesc($row[1]);
 			//Subscription status
 			$subscstatus = self::get_pubuser_subscstatusdesc($row[4]);

 			$list[] = array($row[0],$row[1],$row[2],$row[3],$usertype,$subscstatus,$row[5]);
		}
 		
		return $list; 
	}

	public function entitylastact_list($entityid,$limit)
	{
		$result = Core::$mysqli->query("SELECT activities.iACTtype, activities.dACTstart, activities.dACTend, publicusers.sUSRname, publicusers.sUSRtaxid FROM activities, publicusers, entities WHERE entities.iENTid = '$entityid' AND activities.iACTuserid = publicusers.iUSRid AND publicusers.iUSRcompanyid = entities.iENTid ORDER BY activities.dACTstart DESC, activities.iACTid DESC LIMIT $limit");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$actstart = date("d-m-Y H:i",strtotime($row[1]));
			if (!is_null($row[2])) {
				$actstend = date("d-m-Y H:i",strtotime($row[2]));
				$actdursec = strtotime($row[2]) - strtotime($row[1]);
			} else {
				$actstend = "-";
				$actdursec = time() - strtotime($row[1]);
			}
			$actdurstr = self::get_activity_timestr($actdursec);
 			$list[] = array($row[0],$actstart,$actstend,$actdurstr,$row[3],$row[4]);
 		}

 		return $list;
	}

	public function publicusers_list_count($groupid,$filter)
	{
		$puserflt = $filter["puserflt"];
		$entityflt = $filter["entityflt"];
		$segmentflt = $filter["segmentflt"];
		$utypeflt = $filter["utypeflt"];

		$condflt = "(publicusers.sUSRname LIKE '%$puserflt%' OR publicusers.sUSRtaxid LIKE '%$puserflt%')";
		$condflt .= " AND (entities.sENTname LIKE '%$entityflt%' OR entities.sENTtaxid LIKE '%$entityflt%')"; 	
		if ($segmentflt > "0") {
			$condflt .= " AND FIND_IN_SET($segmentflt,REPLACE(publicusers.sUSRsegment,'|','\,'))";
		} elseif ($segmentflt == "") {
			$condflt .= " AND publicusers.sUSRsegment = ''";
		}
		if ($utypeflt == "-1") {
			$condflt .= " AND publicusers.iUSRtype > '-1'"; 
		} else {
			$condflt .= " AND publicusers.iUSRtype = '$utypeflt'";
		}
		if ($groupid > "0") {
			$condflt .= " AND publicusers.iUSRstatus = '1'";
		}

		$result = Core::$mysqli->query("SELECT COUNT(*) FROM publicusers, entities WHERE $condflt AND publicusers.iUSRcompanyid = entities.iENTid AND entities.iENTdel = '0' AND publicusers.iUSRdel = '0'");
 		$tot = $result->fetch_row();

 		return $tot[0];		
	}

	public function publicusers_list($groupid,$filter,$offset,$limit,$sortfld)
	{
		
		$puserflt = $filter["puserflt"];
		$entityflt = $filter["entityflt"];
		$segmentflt = $filter["segmentflt"];
		$utypeflt = $filter["utypeflt"];

		$condflt = "(publicusers.sUSRname LIKE '%$puserflt%' OR publicusers.sUSRtaxid LIKE '%$puserflt%')";
		$condflt .= " AND (entities.sENTname LIKE '%$entityflt%' OR entities.sENTtaxid LIKE '%$entityflt%')"; 	
		if ($segmentflt > "0") {
			$condflt .= " AND FIND_IN_SET($segmentflt,REPLACE(publicusers.sUSRsegment,'|','\,'))";
		} elseif ($segmentflt == "") {
			$condflt .= " AND publicusers.sUSRsegment = ''";
		}
		if ($utypeflt == "-1") {
			$condflt .= " AND publicusers.iUSRtype > '-1'"; 
		} else {
			$condflt .= " AND publicusers.iUSRtype = '$utypeflt'";
		}
		if ($groupid > "0") {
			$condflt .= " AND publicusers.iUSRstatus = '1'";
		}		

		if ($sortfld == "1") {
			$sortcmd = "publicusers.sUSRname ASC";
		} elseif ($sortfld == "-1") {
			$sortcmd = "publicusers.sUSRname DESC";
		} else {
			$sortcmd = "publicusers.sUSRname ASC";
		}		

		$result = Core::$mysqli->query("SELECT publicusers.iUSRid, publicusers.sUSRname, publicusers.sUSRtaxid, entities.sENTname, publicusers.iUSRopunit, publicusers.iUSRtype, publicusers.iUSRsubscstype, publicusers.iUSRsubscstatus, publicusers.iUSRstatus FROM publicusers, entities WHERE $condflt AND publicusers.iUSRcompanyid = entities.iENTid AND entities.iENTdel = '0' AND publicusers.iUSRdel = '0' ORDER BY $sortcmd LIMIT $offset,$limit");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			//Ref
 			if (strlen($row[0]) < 6) {
 				$remz = 9-strlen($row[0]);
 				$uref = str_repeat("0",$remz) . $row[0];
 			} else {
 				$uref = $row[0];
 			}
 			//OP Unit
 			$opunit = self::get_opunit_name($row[4]);
 			//Type
 			$usertype = self::get_pubuser_typedesc($row[5]);
 			//Subscriprion type
 			$subsctype = self::get_pubuser_subsctypedesc($row[6]);
 			//Subscription status
 			$subscstatus = self::get_pubuser_subscstatusdesc($row[7]);

 			$list[] = array($row[0],$uref,$row[1],$row[2],$row[3],$opunit,$usertype,$subsctype,$subscstatus,$row[8]);
		}
 		
		return $list; 
	}

	public function pubuserlastact_list($userid,$limit)
	{
		$lastact = self::get_pubuser_lastact($userid);
		if (is_array($lastact)) {
			$lastactdate = $lastact[1];	
		} else {
			$lastactdate = date("Y-m-d H:i");
		}
		if (strtotime($lastactdate) < time()-86399) {
			$dateoffet = strtotime($lastactdate);
		} else {
			$dateoffet = time();
		}

		if ($limit > 0 && $limit <= 90) {
			$timediff = date("Y-m-d",$dateoffet-$limit*86400);
		} else {
			$limit = 15;
			$timediff = date("Y-m-d",$dateoffet-$limit*86400);
		}

		$result = Core::$mysqli->query("SELECT activities.iACTtype, activities.dACTstart, activities.dACTend, activities.iACTvehicle, activities.sACTvehicle, activities.iACTjourney, journeys.dJRNstart, journeys.dJRNend, journeys.iJRNstate FROM activities, journeys WHERE activities.iACTuserid = '$userid' AND activities.iACTjourney = journeys.iJRNid AND activities.dACTstart >= '$timediff' ORDER BY activities.dACTstart DESC, activities.iACTid DESC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$actstart = date("d-m-Y H:i",strtotime($row[1]));
			if (!is_null($row[2])) {
				$actstend = date("d-m-Y H:i",strtotime($row[2]));
				$actdursec = strtotime($row[2]) - strtotime($row[1]);
			} else {
				$actstend = "-";
				$actdursec = time() - strtotime($row[1]);
			}
			$actdurstr = self::get_activity_timestr($actdursec);

			$jrnstart = date("d-m-Y H:i",strtotime($row[6]));
			if (!is_null($row[7])) {
				$jrnend = date("d-m-Y H:i",strtotime($row[7]));
				$jrndursec = strtotime($row[7]) - strtotime($row[6]);
			} else {
				$jrnend = "-";
				$jrndursec = time() - strtotime($row[6]);
			}
			$jrndurstr = self::get_activity_timestr($jrndursec);

			if ($row[3] > 0) {
				$vehreg = self::get_vehicle_regnum($row[3]);
			} else {
				if (strlen($row[4]) > 0) {
					$vehreg = $row[4];
				} else {
					$vehreg = "N/A";
				}
			}

 			$list[] = array($row[0],$actstart,$actstend,$actdurstr,$row[5],$jrnstart,$jrnend,$jrndurstr,$row[8],$vehreg);
 		}

 		return $list;
	}	
}
?>