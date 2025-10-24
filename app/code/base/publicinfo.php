<?php
/**
**/
class PublicInfo extends BaseElements
{
	public function get_session($userid,$session)
	{
		$sesinfo = array();
		$result = Core::$mysqli->query("SELECT iSESid, DATE_FORMAT(dSESstart, '%d/%m/%Y %T') FROM publicsessions WHERE iSESuserid = '$userid' AND sSEShash = '$session'");
		$sesdata = $result->fetch_row();
 
 		$sesinfo[0] = self::get_pubuser_name($userid);
 		$sesinfo[1] = $sesdata[1];

 		return $sesinfo;
	}
}
?>