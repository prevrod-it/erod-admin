<?php
/**
**/
class AdminOperations extends BaseElements
{
	/*
	QUERIES
	*/

	public function check_entity_changes($entityid,$entityname,$taxid)
	{
		if ($entityid <> 0) {	
			$actual_name = self::get_entity_name($entityid);
			$actual_taxid = self::get_entity_taxid($entityid);
			
			if ($actual_name != $entityname && $actual_taxid == $taxid) {
				$result = 1;
			} elseif ($actual_name == $entityname && $actual_taxid != $taxid) {
				$result = 2;
			} elseif ($actual_name != $entityname && $actual_taxid != $taxid) {
				$result = 3;
			} else {
				$result = 0;
			}
		} else {
			$result = 0;
		}

		return $result;
	}

	public function check_entity_contact_changes($entityid,$entityemail,$entitytel)
	{
		if ($entityid <> 0) {	
			$actual_email = self::get_entity_email($entityid);
			$actual_tel = self::get_entity_tel($entityid);
			
			if ($actual_email != $entityemail && $actual_tel == $entitytel) {
				$result = 1;
			} elseif ($actual_email == $entityemail && $actual_tel != $entitytel) {
				$result = 2;
			} elseif ($actual_email != $entityemail && $actual_tel != $entitytel) {
				$result = 3;
			} else {
				$result = 0;
			}
		} else {
			$result = 0;
		}

		return $result;
	}

	public function check_entity_cdate($entityid)
	{
		if ($entityid <> 0) {
			$creation_datetime = self::get_entity_creationdate($entityid);
			$creation_date = substr($creation_datetime,0,10);
		} else {
			$creation_date = "1970-01-01";
		}

		return $creation_date;
	}

	public function check_entity_plan($entityid)
	{
		if ($entityid <> 0) {	
			$actual_plan = self::get_entity_actualplan($entityid);
			if ($actual_plan > 0) {
				$actual_plan_type = self::get_plan_type($actual_plan);
				$actual_plan_nusers = self::get_plan_users($actual_plan);
				$actual_plan_expdate = self::get_plan_expdate($actual_plan);
			} else {
				$actual_plan_type = 0;
				$actual_plan_nusers = 0;
				$actual_plan_expdate = date("Y-m-d",0);
			}
		} else {
			$actual_plan = 0;
			$actual_plan_type = 0;
			$actual_plan_nusers = 0;
			$actual_plan_expdate = date("Y-m-d",0);
		}

		return array($actual_plan,$actual_plan_type,$actual_plan_nusers,$actual_plan_expdate);
	}

	public function check_entity_plan_changes($entityid,$planid)
	{
		if ($entityid <> 0) {	
			$actual_plan = self::get_entity_actualplan($entityid);
			if ($actual_plan != $planid) {
				$result = 1;
			} else {
				$result = 0;
			}
		} else {
			$result = 0;
		}

		return $result;
	}

	public function check_entity_evaluation($entityid)
	{
		$type = self::get_entity_type($entityid);

		$result = false;
		if ($type == 0) {
			$result = true;
		}

		return $result;
	}

	public function check_opunit_changes($opunitid,$opunitname)
	{
		if ($opunitid <> 0) {	
			$actual_name = self::get_opunit_name($opunitid);
			
			if ($actual_name != $opunitname) {
				$result = 1;
			} else {
				$result = 0;
			}
		} else {
			$result = 0;
		}

		return $result;
	}

	public function check_vehicle_changes($vehid,$vehregnum)
	{
		if ($vehid <> 0) {	
			$actual_name = self::get_vehicle_regnum($vehid);
			
			if ($actual_name != $vehregnum) {
				$result = 1;
			} else {
				$result = 0;
			}
		} else {
			$result = 0;
		}

		return $result;
	}

	public function check_pubuser_changes($puserid,$pusertaxid,$pusertel)
	{
		if ($puserid <> 0) {	
			$actual_taxid = self::get_pubuser_taxid($puserid);
			$actual_tel = self::get_pubuser_tel($puserid);
			
			if ($actual_taxid != $pusertaxid && $actual_tel == $pusertel) {
				$result = 1;
			} elseif ($actual_taxid == $pusertaxid && $actual_tel != $pusertel) {
				$result = 2;
			} elseif ($actual_taxid != $pusertaxid && $actual_tel != $pusertel) {
				$result = 3;
			} else {
				$result = 0;
			}
		} else {
			$result = 0;
		}

		return $result;
	}

	public function check_pubuser_altchanges($puserid,$puseremail,$driverlic)
	{
		if ($puserid <> 0) {	
			$actual_email = self::get_pubuser_email($puserid);
			$actual_drvlic = self::get_pubuser_driverlic($puserid);
			
			if ($actual_email != $puseremail && $actual_drvlic == $driverlic) {
				$result = 1;
			} elseif ($actual_email == $puseremail && $actual_drvlic != $driverlic) {
				$result = 2;
			} elseif ($actual_email != $puseremail && $actual_drvlic != $driverlic) {
				$result = 3;
			} else {
				$result = 0;
			}
		} else {
			$result = 0;
		}

		return $result;
	}

	public function check_pubuser_regs($puserid)
	{
		$useractcount = self::get_pubuser_actcount($puserid);

		if ($useractcount > 0) {
			return true;
		} else {
			return false;	
		}
	}

	public function check_pubuser_jrnstate($puserid)
	{
		if (self::get_pubuser_activejourney($puserid) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	public function check_if_entityname_exists($entityname)
	{
		if (self::count_entity_byname($entityname) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_if_taxid_exists($taxid)
	{
		if (strlen($taxid) > 0 && self::count_entity_bytaxid($taxid) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_if_email_exists($email)
	{
		if (strlen($email) > 0 && self::count_entity_byemail($email) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_if_tel_exists($tel)
	{
		if (strlen($tel) > 0 && self::count_entity_bytel($tel) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_entity_regs($entityid)
	{
		$useractcount = self::get_entity_actcount($entityid);

		if ($useractcount > 0) {
			return true;
		} else {
			return false;	
		}
	}

	public function check_if_opunit_exists($opunitname,$entityid)
	{
		if (self::count_opunit_byname($opunitname,$entityid) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_opunit_users($opuid)
	{
		$opuusercount = self::get_opunit_usrcount($opuid);

		if ($opuusercount > 0) {
			return true;
		} else {
			return false;	
		}
	}
	
	public function check_plan_state($planid)
	{
		if ($planid > 0) {
			$status = self::get_plan_status($planid);
			$expdate = self::get_plan_expdate($planid);
		} else {
			$status = 1;
			$expdate = date("Y-m-d");
		}

		if ($status == 1 && $expdate >= date("Y-m-d")) {
			return true;
		} else {
			return false;
		}
	}

	public function check_plan_link($planid)
	{
		$check = self::get_plan_linkstatus($planid);
	
		return $check;
	}

	public function check_plan_users($planid)
	{
		if ($planid > 0) {
			$licusers = self::get_plan_users($planid);
			$comnpanyid = self::get_plan_entity($planid);
			$activeusers = self::get_active_pusers($comnpanyid);
		
			if ($activeusers <= $licusers) {
				$usersallow = true;
			} else {
				$usersallow = false;
			}
		} else {
			$usersallow = true;
		}

		return $usersallow;
	}

	public function chack_max_activeusers($entityid,$puserid,$pusertp)
	{
		$userprevstatus = self::get_pubuser_status($puserid);
		$planid = self::get_entity_actualplan($entityid);
		if ($pusertp == 1) {
			$activeusers = self::get_active_padmusers($entityid);
			if ($planid >= 0 && $planid < 4) {
				$licusers = 1;
			} elseif ($planid >= 4 && $planid < 5) {
				$licusers = 2;
			} elseif ($planid == 6) {
				$licusers = 3;
			} else {
				$licusers = 1;
			}
		} elseif ($pusertp == 2 || $pusertp == 3) {
			$activeusers = self::get_active_pusers($entityid);
			if ($planid > 0) {
				$licusers = self::get_plan_users($planid);
			} else {
				$licusers = $activeusers + 1;
			}
		} else {
			$activeusers = 0;
			$licusers = 0;
		}

		if ($userprevstatus == 1) {
			if ($activeusers > $licusers) {
				$restrictuseron = true;
			} else {
				$restrictuseron = false;
			}
		} else {
			if ($activeusers >= $licusers) {
				$restrictuseron = true;
			} else {
				$restrictuseron = false;
			}
		}

		return $restrictuseron;
	}

	public function check_nusers_interval($ptypeid,$pusern)
	{
		$intrv = self::get_plan_userslimit($ptypeid);
		if ($pusern > 0 && $pusern >= $intrv[0] && $pusern <= $intrv[1]) {
			return true;
		} else {
			return false;
		}
	}

	public function check_if_vehicle_exists($vehregnum,$entityid)
	{
		if (self::count_vehicles_byregnum($vehregnum,$entityid) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_vehicle_link($vehid)
	{
		$checkact = self::get_vehicle_actlinkstatus($vehid);
		$checkusage = self::get_vehicle_useagestatus($vehid);
	
		if ($checkact && $checkusage) {
			$check = true;
		} else {
			$check = false;
		}

		return $check;
	}

	public function check_vehicle_usage($vehid)
	{
		$check = self::get_vehicle_useagestatus($vehid);
	
		return $check;
	}

	public function check_if_putaxid_exists($taxid)
	{
		if (strlen($taxid) > 0 && self::count_puser_bytaxid($taxid) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_if_puemail_exists($email)
	{
		if (strlen($email) > 0 && self::count_puser_byemail($email) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_if_putel_exists($tel)
	{
		if (strlen($tel) > 0 && self::count_puser_bytel($tel) > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function check_if_pudrvlic_exists($driverlic)
	{
		if (strlen($driverlic) > 0 && self::count_puser_bydrvlic($driverlic) > 0) {
			return true;
		} else {
			return false;
		}
	}

	/*
	END QUERIES
	*/

	/*
	ACTIONS
	*/
	
	public function insert_entity($planid,$segment,$name,$taxid,$address,$zipcode,$ziploc,$email,$tel,$zone,$etype,$contscope,$creationdate,$updatedate,$status)
	{
		$result = Core::$mysqli->query("INSERT INTO entities (iENTplanid, sENTsegment, sENTname, sENTtaxid, sENTaddress, sENTzipcode, sENTziploc, sENTemail, sENTtel, iENTzone, iENTtype, iENTcontscope, dENTdatecreate, dENTlastupdate, iENTstatus) VALUES ('$planid', '$segment', '$name', '$taxid', '$address', '$zipcode', '$ziploc', '$email', '$tel', '$zone', '$etype', '$contscope', '$creationdate', '$updatedate', '$status')");
	}

	public function update_entity($id,$planid,$segment,$name,$taxid,$address,$zipcode,$ziploc,$email,$tel,$zone,$etype,$contscope,$updatedate,$status)
	{
		$result = Core::$mysqli->query("UPDATE entities SET iENTplanid = '$planid', sENTsegment = '$segment', sENTname = '$name', sENTtaxid = '$taxid', sENTaddress = '$address', sENTzipcode = '$zipcode', sENTziploc = '$ziploc', sENTemail = '$email', sENTtel = '$tel', iENTzone = '$zone', iENTtype = '$etype', iENTcontscope = '$contscope', dENTlastupdate = '$updatedate', iENTstatus = '$status' WHERE iENTid = '$id'");
	}

	public function delete_entity($id,$updatedate)
	{
		$result = Core::$mysqli->query("UPDATE entities SET iENTdel = '1', dENTlastupdate = '$updatedate' WHERE iENTid = '$id'");
	}

	public function delete_entity_users($id,$updatedate)
	{
		$result = Core::$mysqli->query("UPDATE publicusers SET iUSRdel = '1', dUSRlastupdate = '$updatedate' WHERE iUSRcompanyid = '$id'");
	}

	public function remove_users_from_opunit($id,$entityid,$updatedate)
	{
		$result = Core::$mysqli->query("UPDATE publicusers SET iUSRopunit = '0', dUSRlastupdate = '$updatedate' WHERE iUSRopunit = '$id' AND iUSRcompanyid = '$entityid'");
	}
	
	public function insert_opunit($entityid,$name,$address,$zipcode,$ziploc,$zone,$oputype,$creationdate,$updatedate,$status)
	{
		$result = Core::$mysqli->query("INSERT INTO opunits (iENTentityid, sENTname, sENTaddress, sENTzipcode, sENTziploc, iENTzone, iENTtype, dENTdatecreate, dENTlastupdate, iENTstatus) VALUES ('$entityid', '$name', '$address', '$zipcode', '$ziploc', '$zone', '$oputype', '$creationdate', '$updatedate', '$status')");
	}

	public function update_opunit($id,$entityid,$name,$address,$zipcode,$ziploc,$zone,$oputype,$updatedate,$status)
	{
		$result = Core::$mysqli->query("UPDATE opunits SET iENTentityid = '$entityid', sENTname = '$name', sENTaddress = '$address', sENTzipcode = '$zipcode', sENTziploc = '$ziploc', iENTzone = '$zone', iENTtype = '$oputype', dENTlastupdate = '$updatedate', iENTstatus = '$status' WHERE iENTid = '$id'");
	}

	public function delete_opunit($id,$updatedate)
	{
		$result = Core::$mysqli->query("UPDATE opunits SET iENTdel = '1', dENTlastupdate = '$updatedate' WHERE iENTid = '$id'");
	}

	public function insert_plan($entityid,$ptypeid,$pusern,$servlv,$inidate,$enddate,$paystatus,$status)
	{
		//Prepare dates
		$inidateparts = explode("-",$inidate);
		if (strlen($inidateparts[2]) == 4) {
			$ISOinidate = $inidateparts[2] . "-" . $inidateparts[1] . "-" . $inidateparts[0];
		} else {
			$ISOinidate = $inidate;
		}
		$enddateparts = explode("-",$enddate);
		if (strlen($enddateparts[2]) == 4) {
			$ISOenddate = $enddateparts[2] . "-" . $enddateparts[1] . "-" . $enddateparts[0];
		} else {
			$ISOenddate = $enddate;
		}

		$result = Core::$mysqli->query("INSERT INTO plans (iPLNentityid, iPLNplantype, iPLNusers, iPLNservice, dPLNstart, dPLNend, iPLNpayment, iPLNstatus) VALUES ('$entityid', '$ptypeid', '$pusern', '$servlv', '$ISOinidate', '$ISOenddate', '$paystatus', '$status')");
	}

	public function update_plan($id,$entityid,$ptypeid,$pusern,$servlv,$inidate,$enddate,$paystatus,$status)
	{
		//Prepare dates
		$inidateparts = explode("-",$inidate);
		if (strlen($inidateparts[2]) == 4) {
			$ISOinidate = $inidateparts[2] . "-" . $inidateparts[1] . "-" . $inidateparts[0];
		} else {
			$ISOinidate = $inidate;
		}
		$enddateparts = explode("-",$enddate);
		if (strlen($enddateparts[2]) == 4) {
			$ISOenddate = $enddateparts[2] . "-" . $enddateparts[1] . "-" . $enddateparts[0];
		} else {
			$ISOenddate = $enddate;
		}

		$result = Core::$mysqli->query("UPDATE plans SET iPLNentityid = '$entityid', iPLNplantype = '$ptypeid', iPLNusers = '$pusern', iPLNservice = '$servlv', dPLNstart = '$ISOinidate', dPLNend = '$ISOenddate', iPLNpayment = '$paystatus', iPLNstatus = '$status' WHERE iPLNid = '$id'");
	}

	public function delete_plan($id)
	{
		$result = Core::$mysqli->query("UPDATE plans SET iPLNdel = '1' WHERE iPLNid = '$id'");
	}

	public function insert_vehicle($entityid,$vehregnum,$vehbransid,$vehmodel,$status)
	{
		$result = Core::$mysqli->query("INSERT INTO vehicles (iVHCcompanyid, sVHCregid, iVHCbrand, sVHCmodel, iVHCstatus) VALUES ('$entityid', '$vehregnum', '$vehbransid', '$vehmodel', '$status')");
	}

	public function update_vehicle($id,$entityid,$vehregnum,$vehbransid,$vehmodel,$status)
	{
		$result = Core::$mysqli->query("UPDATE vehicles SET iVHCcompanyid = '$entityid', sVHCregid = '$vehregnum', iVHCbrand = '$vehbransid', sVHCmodel = '$vehmodel', iVHCstatus = '$status' WHERE iVHCid = '$id'");
	}

	public function delete_vehicle($id)
	{
		$result = Core::$mysqli->query("UPDATE vehicles SET iVHCdel = '1' WHERE iVHCid = '$id'");
	}

	public function insert_pubuser($name,$taxid,$type,$address,$zipcode,$ziploc,$email,$tel,$entityid,$opunit,$driverlic,$contractini,$subsctype,$subscstatus,$segment,$puserpwd,$expdate,$creationdate,$updatedate,$status)
	{
		//Prepare dates
		$cinidateparts = explode("-",$contractini);
		if (strlen($cinidateparts[2]) == 4) {
			$ISOcinidate = $cinidateparts[2] . "-" . $cinidateparts[1] . "-" . $cinidateparts[0];
		} else {
			$ISOcinidate = $contractini;
		}
		$expdateparts = explode("-",$expdate);
		if (strlen($expdateparts[2]) == 4) {
			$ISOexpdate = $expdateparts[2] . "-" . $expdateparts[1] . "-" . $expdateparts[0];
		} else {
			$ISOexpdate = $expdate;
		}

		$result = Core::$mysqli->query("INSERT INTO publicusers (iUSRcompanyid, iUSRopunit, iUSRtype, sUSRname, sUSRaddress, sUSRzipcode, sUSRziploc, sUSRemail, sUSRtel, sUSRtaxid, sUSRdriverlic, sUSRsegment, sUSRpwd, dUSRcontractini, dUSRexpdate, iUSRsubscstype, iUSRsubscstatus, dUSRdatecreate, dUSRlastupdate, iUSRstatus) VALUES ('$entityid', '$opunit', '$type', '$name', '$address', '$zipcode', '$ziploc', '$email', '$tel', '$taxid', '$driverlic', '$segment', '$puserpwd', '$ISOcinidate', '$ISOexpdate', '$subsctype', '$subscstatus', '$creationdate', '$updatedate', iUSRstatus = '$status')");
		return Core::$mysqli->error;
	}

	public function update_pubuser($id,$name,$taxid,$type,$address,$zipcode,$ziploc,$email,$tel,$entityid,$opunit,$driverlic,$contractini,$subsctype,$subscstatus,$segment,$expdate,$updatedate,$status)
	{
		//Prepare dates
		$cinidateparts = explode("-",$contractini);
		if (strlen($cinidateparts[2]) == 4) {
			$ISOcinidate = $cinidateparts[2] . "-" . $cinidateparts[1] . "-" . $cinidateparts[0];
		} else {
			$ISOcinidate = $contractini;
		}
		$expdateparts = explode("-",$expdate);
		if (strlen($expdateparts[2]) == 4) {
			$ISOexpdate = $expdateparts[2] . "-" . $expdateparts[1] . "-" . $expdateparts[0];
		} else {
			$ISOexpdate = $expdate;
		}

		$result = Core::$mysqli->query("UPDATE publicusers SET iUSRcompanyid = '$entityid', iUSRopunit = '$opunit', iUSRtype = '$type', sUSRname = '$name', sUSRaddress = '$address', sUSRzipcode = '$zipcode', sUSRziploc = '$ziploc', sUSRemail = '$email', sUSRtel = '$tel', sUSRtaxid = '$taxid', sUSRdriverlic = '$driverlic', sUSRsegment = '$segment', dUSRcontractini = '$ISOcinidate', dUSRexpdate = '$ISOexpdate', iUSRsubscstype = '$subsctype', iUSRsubscstatus = '$subscstatus', dUSRlastupdate = '$updatedate', iUSRstatus = '$status' WHERE iUSRid = '$id'");
	}

	public function update_pubuser_pwd($id,$puserpwd)
	{
		$result = Core::$mysqli->query("UPDATE publicusers SET sUSRpwd = '$puserpwd' WHERE iUSRid = '$id'");
	}

	public function delete_pubuser($id,$updatedate)
	{
		$result = Core::$mysqli->query("UPDATE publicusers SET iUSRdel = '1', dUSRlastupdate = '$updatedate' WHERE iUSRid = '$id'");
	}

	/*
	END ACTIONS
	*/
}
?>