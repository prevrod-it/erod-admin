<?php
class PublicOperations extends BaseElements
{
	/*
	ACTIONS
	*/
	
	public function sync_vehicle_list($entityid,$driverid,$vehjsonarr)
	{
		$list_temp = array(0,"");
		$veharr = json_decode($vehjsonarr);
		$list = array();
		$chrtorplcns = array(".","/","_");
		$chrtorplcws = array(" ",".","/","_");	
		if (is_array($veharr)) {
			foreach ($veharr as $vehitem) {
				if ((int)$vehitem[0] == 0) {
					if (strpos($vehitem[1],"-") !== false) {
						$stdvrg = str_replace($chrtorplcns,"-",$vehitem[1]);
					} else {
						$stdvrg = str_replace($chrtorplcws,"-",$vehitem[1]);
					}
					$stdvrg = strtoupper($stdvrg); 
					$vehexixts = self::check_company_vehicle($entityid,$stdvrg);
					if (!$vehexixts) {
						$newvehid = self::insert_company_vehicle(array($entityid,$stdvrg));
						sleep(1);
						self::update_vehalloc_vehid($newvehid,$vehitem[1]);
						self::update_useractivities_vehid($newvehid,$vehitem[1]);
					}	
				}
			}
			$list = self::get_updated_vehicles_list($entityid,$driverid);
		}

		return $list;
	}

	public function sync_userjourney_list($driverid,$jrnjsonarr)
	{
		$jrnarr = json_decode($jrnjsonarr);
		$list = array();
		if (is_array($jrnarr)) {
			if (count($jrnarr) == 1 && $jrnarr[0][0] == 0 && $jrnarr[0][2] == 0 && is_null($jrnarr[0][4]) && $jrnarr[0][10] == 1) {
				$list[] = array(0,0,0,0,null,0,"",null,0,"",1,"");
			} else {
				foreach ($jrnarr as $jrnitem) {
					if ($jrnitem[2] == $driverid) {
						if ((int)$jrnitem[0] == 0) {
							$jrnexixts = self::check_user_journey($jrnitem[2],$jrnitem[4]);
							if (!$jrnexixts) {
								self::insert_user_journey($jrnitem);
							}
						} else {
							$jrncurstate = self::check_user_journey_state($jrnitem[0]);
							if ($jrncurstate == 0 && $jrnitem[10] == 1) {
								self::update_user_journey($jrnitem);
							}
						}
					}
				}
				$list = self::get_updated_userjourney_list($driverid,45);
			}
		}

		return $list;
	}

	public function sync_uservehalloc_list($driverid,$valjsonarr)
	{
		$valarr = json_decode($valjsonarr);
		$list = array();
		if (is_array($valarr)) {
			if (count($valarr) == 1 && $valarr[0][0] == 0 && $valarr[0][1] == 0 && $valarr[0][2] == "" && $valarr[0][3] == 0 && (is_null($valarr[0][4]) || strlen($valarr[0][4]) == 0) && (is_null($valarr[0][13]) || strlen($valarr[0][13]) == 0)) {
				$list[] = array(0,0,"",0,null,0,0,"",null,0,0,"",1,"");
			} else {
				foreach ($valarr as $valitem) {
					if ($valitem[3] == self::crosscheck_user_journey($driverid,$valitem[3])) {
						if ((int)$valitem[0] == 0) {
							$valexixts = self::check_user_vehalloc($valitem[1],$valitem[2],$valitem[3],$valitem[4],$valitem[13]);
							if (!$valexixts) {
								if ($valitem[3] == 0) {
									$updjrnid = self::get_jorney_from_cstr($valitem[13]);
									$valitem[3] = $updjrnid;
								}
								self::insert_journey_vehalloc($valitem);
							}
						} else {
							$valcurstate = self::check_journey_vehalloc_state($valitem[0]);
							if ($valcurstate == 0 && $valitem[12] == 1) {
								self::update_journey_vehalloc($valitem);
							}
						}
					}
				}
				$list = self::get_updated_uservehalloc_list($driverid,120);
			}
		}

		return $list;
	}

	public function sync_useractivities_list($driverid,$actjsonarr)
	{
		$actarr = json_decode($actjsonarr);
		$list = array();
		if (is_array($actarr)) {
			if (count($actarr) == 1 && $actarr[0][0] == 0 && $actarr[0][2] == 0 && (is_null($actarr[0][6]) || strlen($actarr[0][6]) == 0)) {
				$list[] = array(0,0,0,0,0,"",null,null,"");
			} else {
				foreach ($actarr as $actitem) {
					if ($actitem[2] == $driverid) {
						if ((int)$actitem[0] == 0) {
							$actexixts = self::check_user_activity($actitem[1],$actitem[2],$actitem[6]);
							if (!$actexixts) {
								if ($actitem[3] == 0) {
									$updjrnid = self::get_jorney_from_cstr($actitem[13]);
									$actitem[3] = $updjrnid;
								}
								self::insert_user_activity($actitem);
							}
						} else {
							$actcurstate = self::check_user_activity_state($actitem[0]);
							if ($actcurstate == 0 && strlen($actitem[7]) >= 14) {
								self::update_user_activity($actitem);
							}
						}
					}
				}
				$list = self::get_updated_useractivities_list($driverid,0,1440);
			}
		}

		return $list;	
	}

	public function sync_userremarks_list($driverid,$rmkjsonarr)
	{
		$rmkarr = json_decode($rmkjsonarr);
		$list = array();
		if (is_array($rmkarr)) {
			if (count($rmkarr) == 1 && $rmkarr[0][0] == 0 && $rmkarr[0][1] == 0 && is_null($rmkarr[0][3]) && $rmkarr[0][4] == "") {
				$list[] = array(0,0,0,null,"","");
			} else {
				foreach ($rmkarr as $rmkitem) {
					if ($rmkitem[1] == $driverid) {
						$delrmk = 0;
						if (isset($rmkitem[6])) { $delrmk = (int)$rmkitem[6]; }
						if ((int)$rmkitem[0] == 0) {
							$rmkexixts = self::check_user_remark($rmkitem[1],$rmkitem[2],$rmkitem[3],$rmkitem[4]);
							if (!$rmkexixts && $delrmk == 0) {
								if ($rmkitem[2] == 0) {
									$updjrnid = self::get_jorney_from_cstr($rmkitem[5]);
									$rmkitem[2] = $updjrnid;
								}
								self::insert_user_remark($rmkitem);
							}
						} else {
							if ($delrmk == 1) {	
								self::delete_user_remark($rmkitem[0]);
							}
						}
					}
				}
				$list = self::get_updated_userremark_list($driverid,120);
			}
		}

		return $list;
	}


	public function insert_company_vehicle($varr)
	{
		$result = Core::$mysqli->query("INSERT INTO vehicles (iVHCcompanyid, sVHCregid) VALUES ('$varr[0]', '$varr[1]')");
		return Core::$mysqli->insert_id;
	}

	public function insert_user_journey($jarr)
	{
		$result = Core::$mysqli->query("INSERT INTO journeys (iJRNtype, iJRNuserid, iJRNcouserid, dJRNstart, iJRNiniloctype, sJRNiniloc, dJRNend, iJRNendloctype, sJRNendloc, iJRNstate, sJRNuident) VALUES ('$jarr[1]', '$jarr[2]', '$jarr[3]', NULLIF('$jarr[4]',''), '$jarr[5]', '$jarr[6]', NULLIF('$jarr[7]',''), '$jarr[8]', '$jarr[9]', '$jarr[10]', '$jarr[11]')");
	}

	public function update_user_journey($jarr)
	{
		$result = Core::$mysqli->query("UPDATE journeys SET iJRNtype = '$jarr[1]', iJRNuserid = '$jarr[2]', iJRNcouserid = '$jarr[3]', dJRNstart = NULLIF('$jarr[4]',''), iJRNiniloctype = '$jarr[5]', sJRNiniloc = '$jarr[6]', dJRNend = NULLIF('$jarr[7]',''), iJRNendloctype = '$jarr[8]', sJRNendloc = '$jarr[9]', iJRNstate = '$jarr[10]', sJRNuident = '$jarr[11]' WHERE iJRNid = '$jarr[0]'");
	}

	public function insert_journey_vehalloc($varr)
	{
		$result = Core::$mysqli->query("INSERT INTO vehalloc (iVALvehicle, sVALvehicle, iVALjourney, dVALstart, iVALinikms, iVALiniloctype, sVALiniloc, dVALend, iVALendkms, iVALendloctype, sVALendloc, iVALstate, sVALjrnstr) VALUES ('$varr[1]', '$varr[2]', '$varr[3]', NULLIF('$varr[4]',''), '$varr[5]', '$varr[6]', '$varr[7]', NULLIF('$varr[8]',''), '$varr[9]', '$varr[10]', '$varr[11]', '$varr[12]', '$varr[13]')");
	}

	public function update_journey_vehalloc($varr)
	{
		$result = Core::$mysqli->query("UPDATE vehalloc SET iVALvehicle = '$varr[1]', sVALvehicle = '$varr[2]', iVALjourney = '$varr[3]', dVALstart = NULLIF('$varr[4]',''), iVALinikms = '$varr[5]', iVALiniloctype = '$varr[6]', sVALiniloc = '$varr[7]', dVALend = NULLIF('$varr[8]',''), iVALendkms = '$varr[9]', iVALendloctype = '$varr[10]', sVALendloc = '$varr[11]', iVALstate = '$varr[12]', sVALjrnstr = '$varr[13]' WHERE iVALid = '$varr[0]'");
	}

	public function update_vehalloc_vehid($vehid,$orgnrg)
	{
		$result = Core::$mysqli->query("UPDATE vehalloc SET iVALvehicle = '$vehid', sVALvehicle = '' WHERE iVALvehicle = '0' AND sVALvehicle LIKE '%$orgnrg%'");
	}

	public function insert_user_activity($aarr)
	{
		$result = Core::$mysqli->query("INSERT INTO activities (iACTtype, iACTuserid, iACTjourney, iACTvehicle, sACTvehicle, dACTstart, dACTend, sACTjrnstr) VALUES ('$aarr[1]', '$aarr[2]', '$aarr[3]', '$aarr[4]', '$aarr[5]', NULLIF('$aarr[6]',''), NULLIF('$aarr[7]',''), '$aarr[8]')");
	}

	public function update_user_activity($aarr)
	{
		$result = Core::$mysqli->query("UPDATE activities SET iACTtype = '$aarr[1]', iACTuserid = '$aarr[2]', iACTjourney = '$aarr[3]', iACTvehicle = '$aarr[4]', sACTvehicle = '$aarr[5]', dACTstart = NULLIF('$aarr[6]',''), dACTend = NULLIF('$aarr[7]',''), sACTjrnstr = '$aarr[8]' WHERE iACTid = '$aarr[0]'");
	}

	public function update_useractivities_vehid($vehid,$orgnrg)
	{
		$result = Core::$mysqli->query("UPDATE activities SET iACTvehicle = '$vehid', sACTvehicle = '' WHERE iACTvehicle = '0' AND sACTvehicle LIKE '%$orgnrg%'");
	}

	public function insert_user_remark($jarr)
	{
		$result = Core::$mysqli->query("INSERT INTO userremarks (iRMKuserid, iRMKjourney, dRMKdate, sRMKremark, sRMKjrnstr) VALUES ('$jarr[1]', '$jarr[1]', NULLIF('$jarr[3]',''), '$jarr[4]', '$jarr[5]')");
	}

	public function delete_user_remark($rmkid)
	{
		$result = Core::$mysqli->query("UPDATE userremarks SET iRMKdel = '1' WHERE iRMKid = '$rmkid'");
	}	
	/*
	END ACTIONS
	*/

	/*
	QUERIES
	*/

	public function get_updated_vehicles_list($comnpanyid,$userid)
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
 			$list[] = array($row[0],$row[1]);
		}
 		
		return $list;
	}

	public function get_updated_userjourney_list($userid,$limit)
	{
		if ($limit == 0) {
			$limitstr = "";
		} else {
			$limitstr = " LIMIT $limit";
		}
		$result = Core::$mysqli->query("SELECT iJRNid, iJRNtype, iJRNuserid, iJRNcouserid, dJRNstart, iJRNiniloctype, sJRNiniloc, dJRNend, iJRNendloctype, sJRNendloc, iJRNstate, sJRNuident FROM journeys WHERE iJRNuserid = '$userid' AND iJRNdel = '0' ORDER BY dJRNstart DESC, iJRNid DESC$limitstr");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11]);
		}
		$list = array_reverse($list);

		return $list;	
	}

	public function get_updated_uservehalloc_list($userid,$limit)
	{
		if ($limit == 0) {
			$limitstr = "";
		} else {
			$limitstr = " LIMIT $limit";
		}
		$result = Core::$mysqli->query("SELECT vehalloc.iVALid, vehalloc.iVALvehicle, vehalloc.sVALvehicle, vehalloc.iVALjourney, vehalloc.dVALstart, vehalloc.iVALinikms, vehalloc.iVALiniloctype, vehalloc.sVALiniloc, vehalloc.dVALend, vehalloc.iVALendkms, vehalloc.iVALendloctype, vehalloc.sVALendloc, vehalloc.iVALstate, vehalloc.sVALjrnstr FROM vehalloc, journeys WHERE vehalloc.iVALjourney = journeys.iJRNid AND journeys.iJRNuserid = '$userid' ORDER BY vehalloc.dVALstart DESC, vehalloc.iVALid DESC$limitstr");
		$list = array();
 		while ($row = $result->fetch_row()) {
 			$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],$row[12],$row[13]);
		}
		$list = array_reverse($list);

		return $list;
	}

	public function get_updated_useractivities_list($userid,$journeyid,$limit)
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
 			$list[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8]);
		}
		$list = array_reverse($list);

		return $list;	
	}

	public function get_updated_userremark_list($userid,$limit)
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
		$list = array_reverse($list);

		return $list;
	}

	public function most_used_vehicles($userid)
	{
		$result = Core::$mysqli->query("SELECT vehalloc.iVALvehicle, COUNT(vehalloc.iVALvehicle) AS iVALmuv FROM vehalloc, journeys, vehicles WHERE journeys.iJRNuserid = '$userid' AND journeys.iJRNdel = '0' AND journeys.iJRNid = vehalloc.iVALjourney AND vehalloc.iVALdel = '0' AND vehicles.iVHCid = vehalloc.iVALvehicle AND vehicles.iVHCstatus = '1' AND vehicles.iVHCdel = '0' GROUP BY vehalloc.iVALvehicle ORDER BY iVALmuv DESC, vehalloc.iVALvehicle ASC LIMIT 3");
		$list = array();
		while ($row = $result->fetch_row()) {
 			$list[] = $row[0];
		}

		return $list;
	}

	/*
	END QUERIES
	*/
}
?>