<?php
/**
**/
class PublicLists extends BaseElements
{
	//****************************************************************************************************
	//Users (drivers, workers)
	//****************************************************************************************************
	public function get_vehicles_list($comnpanyid)
	{
		if ($comnpanyid == 0) {
			$condflt = "iVHCcompanyid >= '0'";
		} else {	
			$condflt = "iVHCcompanyid = '$comnpanyid'";
		}

		$result = Core::$mysqli->query("SELECT iVHCid, iVHCcompanyid, sVHCregid, iVHCbrand, sVHCmodel, iVHCstatus FROM vehicles WHERE $condflt AND iVHCdel = '0' ORDER BY sVHCregid ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			if (is_array($row)) {
 				$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]);
			}
		}
 		
		return $list; 
	}

	public function get_vehicles_abvrlist($comnpanyid,$userid)
	{
		$list = array();
		if ($comnpanyid == 0) {
			$condflt = "iVHCcompanyid >= '0'";
		} else {	
			$condflt = "iVHCcompanyid = '$comnpanyid'";
		}
		if ($userid > 0) {
			$muopts = self::most_used_vehicles($userid);
			if (count($muopts) > 0) {
				foreach ($muopts as $optid) {
					$list[] = self::get_vehicle_abvrinfo($optid);
				}
				$condflt .= " AND iVHCid NOT IN (" . implode(",",$muopts) . ")";
			} 
		}

		$result = Core::$mysqli->query("SELECT iVHCid, sVHCregid FROM vehicles WHERE $condflt AND iVHCstatus = '1' AND iVHCdel = '0' ORDER BY sVHCregid ASC");
 		while ($row = $result->fetch_row()) {
 			if (is_array($row)) {
 				$list[] = array($row[0],$row[1]);
 			}
		}
 		
		return $list; 
	}

	public function get_userjourney_list($userid,$limit)
	{
		if ($limit == 0) {
			$limitstr = "";
		} else {
			$limitstr = " LIMIT $limit";
		}
		$result = Core::$mysqli->query("SELECT iJRNid, iJRNtype, iJRNuserid, iJRNcouserid, dJRNstart, iJRNiniloctype, sJRNiniloc, dJRNend, iJRNendloctype, sJRNendloc, iJRNstate, sJRNuident FROM journeys WHERE iJRNuserid = '$userid' AND iJRNdel = '0' ORDER BY dJRNstart DESC, iJRNid DESC$limitstr");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			if (is_array($row)) {	
 				$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11]);
			}
		}

		if (!is_array($list) || count($list) == 0) {
			$list[] = array(0,0,0,0,null,0,"",null,0,"",1,"");
		}else {
			$list = array_reverse($list);
		}

		return $list;	
	}

	public function get_allocvehicles_list($userid,$limit)
	{
		if ($limit == 0) {
			$limitstr = "";
		} else {
			$limitstr = " LIMIT $limit";
		}
		$result = Core::$mysqli->query("SELECT vehalloc.iVALid, vehalloc.iVALvehicle, vehalloc.sVALvehicle, vehalloc.iVALjourney, vehalloc.dVALstart, vehalloc.iVALinikms, vehalloc.iVALiniloctype, vehalloc.sVALiniloc, vehalloc.dVALend, vehalloc.iVALendkms, vehalloc.iVALendloctype, vehalloc.sVALendloc, vehalloc.iVALstate, vehalloc.sVALjrnstr FROM vehalloc, journeys WHERE vehalloc.iVALjourney = journeys.iJRNid AND journeys.iJRNuserid = '$userid' ORDER BY vehalloc.dVALstart DESC, vehalloc.iVALid DESC$limitstr");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			if (is_array($row)) {
 				$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[13]);
 			}
		}

		if (!is_array($list) || count($list) == 0) {
			$list[] = array(0,0,"",0,null,0,0,"",null,0,0,"",1,"");
		} else {
			$list = array_reverse($list);
		}

		return $list;
	}

	public function most_used_vehicles($userid)
	{
		$result = Core::$mysqli->query("SELECT vehalloc.iVALvehicle, COUNT(vehalloc.iVALvehicle) AS iVALmuv FROM vehalloc, journeys, vehicles WHERE journeys.iJRNuserid = '$userid' AND journeys.iJRNdel = '0' AND journeys.iJRNid = vehalloc.iVALjourney AND vehalloc.iVALdel = '0' AND vehicles.iVHCid = vehalloc.iVALvehicle AND vehicles.iVHCstatus = '1' AND vehicles.iVHCdel = '0' GROUP BY vehalloc.iVALvehicle ORDER BY iVALmuv DESC, vehalloc.iVALvehicle ASC LIMIT 3");
		$list = array();
		while ($row = $result->fetch_row()) {
 			if (is_array($row)) {
 				$list[] = $row[0];
 			}
		}

		return $list;
	}

	public function get_useractivities_list($userid,$journeyid,$limit)
	{
		if ($journeyid == 0) {
			$condflt = "(iACTjourney > '0' OR iACTjourney <= '-1')";
		} else {
			$condflt = "iACTjourney = '$journeyid'";
		}
		if ($limit == 0) {
			$limitstr = "";
		} else {
			$limitstr = " LIMIT $limit";
		}
		$result = Core::$mysqli->query("SELECT iACTid, iACTtype, iACTuserid, iACTjourney, iACTvehicle, sACTvehicle, dACTstart, dACTend, sACTjrnstr FROM activities WHERE iACTuserid = '$userid' AND $condflt AND iACTdel = '0' ORDER BY dACTstart DESC, iACTid DESC$limitstr");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			if (is_array($row)) {
 				$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8]);
			}
		}

		if (!is_array($list) || count($list) == 0) {
			$list[] = array(0,0,0,0,0,"",null,null,"");
		}else {
			$list = array_reverse($list);
		}

		return $list;	
	}

	public function get_userremarks_list($userid,$limit)
	{
		if ($limit == 0) {
			$limitstr = "";
		} else {
			$limitstr = " LIMIT $limit";
		}
		$result = Core::$mysqli->query("SELECT iRMKid, iRMKuserid, iRMKjourney, dRMKdate, sRMKremark, sRMKjrnstr FROM userremarks WHERE iRMKuserid = '$userid' AND iRMKdel = '0' ORDER BY dRMKdate DESC, iRMKid DESC$limitstr");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			if (is_array($row)) {	
 				$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]);
			}
		}

		if (!is_array($list) || count($list) == 0) {
			$list[] = array(0,0,0,null,"","");
		}else {
			$list = array_reverse($list);
		}

		return $list;	
	}

	public function get_user_rptjourneys($id,$interval)
	{
		$today = strtotime(date("Y-m-d"));
		$dayfrom = $today - ($interval * 86400);
		$datefrom = date("Y-m-d H:i:s",$dayfrom);

		$loctypes = array("sede","unidade operacional","domicílio do trabalhador","outro local");
		$list = array();
		$result = Core::$mysqli->query("SELECT iJRNid, DATE_FORMAT(dJRNstart,'%d-%m-%Y %H:%i'), iJRNiniloctype, sJRNiniloc, DATE_FORMAT(dJRNend,'%d-%m-%Y %H:%i'), iJRNendloctype, sJRNendloc, iJRNstate FROM journeys WHERE iJRNuserid = '$id' AND dJRNstart >= '$datefrom' AND iJRNdel = '0' ORDER BY dJRNstart DESC, iJRNid DESC");
		while ($row = $result->fetch_row()) {
			if (is_array($row)) {
				$inilocdesc = $loctypes[$row[2]];
				$endlocdesc = $loctypes[$row[5]];
				$list[] = array($row[0],$row[1],$inilocdesc,$row[3],$row[4],$endlocdesc,$row[6],$row[7]); 
			}
		}

		return $list;
	}

	public function get_user_rptactivities($id,$journeyid)
	{
		$acttypes = array("Pausa","Condução","Outros trabalhos","Disponibilidade");
		$list = array();
		$result = Core::$mysqli->query("SELECT iACTid, iACTtype, iACTvehicle, sACTvehicle, DATE_FORMAT(dACTstart,'%d-%m-%Y %H:%i'), DATE_FORMAT(dACTend,'%d-%m-%Y %H:%i'), UNIX_TIMESTAMP(dACTend)-UNIX_TIMESTAMP(dACTstart) FROM activities WHERE iACTuserid = '$id' AND iACTjourney = '$journeyid' ORDER by dACTstart DESC, iACTid DESC");
		while ($row = $result->fetch_row()) {
			if (is_array($row)) {
				$actstr = $acttypes[$row[1]];
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
				$list[] = array($row[1],$actstr,$actdur,$row[4],$row[5],$vehreg); 
			}
		}

		return $list;
	}

	//****************************************************************************************************
	//Management users
	//****************************************************************************************************
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

	public function segment_list()
	{
		
		$result = Core::$mysqli->query("SELECT iSEGid, sSEGname FROM segments WHERE iSEGstatus = '1' AND iSEGdel = '0' ORDER BY sSEGname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
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

	public function vehiclebrands_list()
	{
		$result = Core::$mysqli->query("SELECT iVHCid, sVHCbrand FROM vehbrands ORDER BY sVHCbrand ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
		}
 		
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

	public function entityopus_select_list($entityid)
	{
		$result = Core::$mysqli->query("SELECT iENTid, sENTname FROM opunits WHERE iENTentityid = '$entityid' AND iENTstatus = '1' AND iENTdel = '0' ORDER BY sENTname ASC");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1]);
 		}

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

	public function publicusers_list_count($comnpanyid,$filter)
	{
		$puserflt = $filter["puserflt"];
		$utypeflt = $filter["utypeflt"];

		$condflt = "publicusers.iUSRcompanyid = '$comnpanyid'";
		$condflt .= " AND (publicusers.sUSRname LIKE '%$puserflt%' OR publicusers.sUSRtaxid LIKE '%$puserflt%')";
		if ($utypeflt == "-1") {
			$condflt .= " AND publicusers.iUSRtype > '-1'"; 
		} else {
			$condflt .= " AND publicusers.iUSRtype = '$utypeflt'";
		}
		
		$result = Core::$mysqli->query("SELECT COUNT(*) FROM publicusers, entities WHERE $condflt AND publicusers.iUSRcompanyid = entities.iENTid AND entities.iENTdel = '0' AND publicusers.iUSRdel = '0'");
 		$tot = $result->fetch_row();

 		return $tot[0];		
	}

	public function publicusers_list($comnpanyid,$filter,$offset,$limit,$sortfld)
	{
		
		$puserflt = $filter["puserflt"];
		$utypeflt = $filter["utypeflt"];

		$condflt = "publicusers.iUSRcompanyid = '$comnpanyid'";
		$condflt .= " AND (publicusers.sUSRname LIKE '%$puserflt%' OR publicusers.sUSRtaxid LIKE '%$puserflt%')";
		if ($utypeflt == "-1") {
			$condflt .= " AND publicusers.iUSRtype > '-1'"; 
		} else {
			$condflt .= " AND publicusers.iUSRtype = '$utypeflt'";
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