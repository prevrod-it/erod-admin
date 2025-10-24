<?php
/**
**/
class AdminInfo extends BaseElements
{
	public function get_session($userid,$session)
	{
		$sesinfo = array();
		$result = Core::$mysqli->query("SELECT iSESid, DATE_FORMAT(dSESstart, '%d/%m/%Y %T') FROM sessions WHERE iSESuserid = '$userid' AND sSEShash = '$session'");
		$sesdata = $result->fetch_row();
 
 		$sesinfo[0] = self::get_user_name($userid);
 		$sesinfo[1] = $sesdata[1];

 		return $sesinfo;
	}

	public function get_group_info($usrid)
	{
		$groupid = self::get_user_group($usrid);
		$result = Core::$mysqli->query("SELECT iGRPid, sGRPname, sGRPimg FROM usrgroups WHERE iGRPid = '$groupid'");
		$groupdata = $result->fetch_row();

		if ($groupid == "0") {
			$groupinfo = array(0,"ADMIN","admin_logo.png");
		} else {
			if (!is_null($groupdata[0]) && $groupdata[0] != "") {
				$groupinfo = array($groupdata[0],$groupdata[1],$groupdata[2]);
			} else {
				$groupinfo = array(0,"ERRO","ERRO");
			}
		}
		
		return $groupinfo;
	}
}
?>