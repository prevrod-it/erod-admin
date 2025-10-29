<?php
/**
**/
class Core
{	
	//Parameters
	private $xml_obj;

	//Globals
	private static $instance = null;
	public static $mysqli;

	private function __construct () {
		if (file_exists('app/config/config.xml')) {
	    	$this->xml_obj = simplexml_load_file('app/config/config.xml');
	    } else {
	    	if (file_exists('../../config/config.xml')) {
		    	$this->xml_obj = simplexml_load_file('../../config/config.xml');
		    }	
	    }

    	//DB Server and DB Parameters
    	//DB parameters
		$servername = null;
		$username = null;
		$password = null;
		$database = null;

    	$xml_db = $this->xml_obj->dbconnection;
    	$servername = $xml_db->servername;
    	$username = $xml_db->username;
    	$password = $xml_db->password;
		$dbname = $xml_db->dbname;
  	
		//DB Connection
		self::$mysqli = new mysqli($servername, $username, $password, $dbname);
		if (self::$mysqli->connect_error) {
			die("Connection failed: " . self::$mysqli->connect_error);
		}
		self::$mysqli->set_charset("utf8"); 
	}

	public static function init()
	{
		if(!self::$instance) {
      		self::$instance = new Core();
    	}
    	return self::$instance;
	}

	public function tree_struct()
	{
		//Directory structure
		$tree_obj = (object) array();
		$templatedir = array();
		$skindir = array();
		$mediadir  = array();

		$templatedir['adminmain'] = "app/design/adminhtml";
		$templatedir['adminblock'] = "app/design/adminhtml/block";
		$templatedir['adminpages'] = "app/design/adminhtml/pages";

		$templatedir['publicmain'] = "app/design/publichtml";
		$templatedir['publicblock'] = "app/design/publichtml/block";
		$templatedir['publicpages'] = "app/design/publichtml/pages";

		$skindir['admincss'] = "/skin/adminhtml/css";
		$skindir['adminjs'] = "/skin/adminhtml/js";
		$skindir['adminfonts'] = "/skin/adminhtml/custom-fonts";
		$mediadir['adminimg'] = "/skin/adminhtml/img";
		
		$skindir['publiccss'] = "/skin/publichtml/css";
		$skindir['publicjs'] = "/skin/publichtml/js";
		$skindir['publicfonts'] = "/skin/publichtml/custom-fonts";
		$mediadir['publicimg'] = "/skin/publichtml/img";
		
		$mediadir['img'] = "/media/img";
		$mediadir['file'] = "/media/file";

		$tree_obj->templatedir = (object) array();
		$tree_obj->skindir = (object) array();
		$tree_obj->mediadir = (object) array();
		
		$tree_obj->templatedir->adminmain = $templatedir['adminmain'];
		$tree_obj->templatedir->adminblock = $templatedir['adminblock'];
		$tree_obj->templatedir->adminpages = $templatedir['adminpages'];
		$tree_obj->skindir->admincss = $skindir['admincss'];
		$tree_obj->skindir->adminjs = $skindir['adminjs'];
		$tree_obj->skindir->adminfonts = $skindir['adminfonts'];	
		$tree_obj->mediadir->adminimg = $mediadir['adminimg'];
		
		$tree_obj->templatedir->publicmain = $templatedir['publicmain'];
		$tree_obj->templatedir->publicblock = $templatedir['publicblock'];
		$tree_obj->templatedir->publicpages = $templatedir['publicpages'];
		$tree_obj->skindir->publiccss = $skindir['publiccss'];
		$tree_obj->skindir->publicjs = $skindir['publicjs'];
		$tree_obj->skindir->publicfonts = $skindir['publicfonts'];	
		$tree_obj->mediadir->publicimg = $mediadir['publicimg'];
		
		$tree_obj->mediadir->img = $mediadir['img'];
		$tree_obj->mediadir->file = $mediadir['file'];

		return $tree_obj;
	}

	public function get_user($usr,$pwd)
	{
		$hashpwd = hash("sha256",$pwd);
		$result = self::$mysqli->query("SELECT iUSRid, sUSRname, sUSRpwd, sUSRperm FROM users WHERE sUSRemail = '$usr' AND sUSRpwd = '$hashpwd' AND iUSRdel = '0'");
		$userdata = $result->fetch_row();
		if (is_array($userdata)) {
			if ($userdata[0] != "" && $userdata[0] >= 0) {
	 			$user = $userdata[0];  
	 		} else {
	 			$user = -1;
	 		}
	 	} else {
	 		$user = -1;
	 	}
		return $user;	
	}

	public function set_session($usr,$user)
	{
		$userstr = $usr . time();
		$session = hash("md5",$userstr);

		$result = self::$mysqli->query("INSERT INTO sessions (iSESuserid, dSESstart, sSEShash, iSESexp) VALUES ('$user', NOW(), '$session', '3600')");
		
		return $session;	
	}

	public function check_session($user,$session)
	{
		$result = self::$mysqli->query("SELECT COUNT(*), iSESid, iSESuserid, iSESexp, UNIX_TIMESTAMP(dSESstart), UNIX_TIMESTAMP() FROM sessions WHERE iSESuserid = '$user' AND sSEShash = '$session' AND (UNIX_TIMESTAMP(dSESstart)+iSESexp) > UNIX_TIMESTAMP()");
		$sesdata = $result->fetch_row();
		if ($sesdata[0] == 1) {
			$sesid = $sesdata[1];
			$autuser = $sesdata[2];
			$newexp = 3600 + ($sesdata[5] - $sesdata[4]);
			$result2 = self::$mysqli->query("UPDATE sessions SET iSESexp = '$newexp' WHERE iSESid = '$sesid'");
		} else {
			$autuser = -1;
			$cvalue = "$autuser|0";
			$result2 = self::$mysqli->query("UPDATE sessions SET iSESexp = '-1' WHERE iSESuserid = '$user' AND sSEShash = '$session'");
			setcookie("adminactivesession", $cvalue, 0, "/");
		}

		return $autuser;
	}

	public function end_session($user,$session)
	{
		$result = self::$mysqli->query("UPDATE sessions SET iSESexp = '-1' WHERE iSESuserid = '$user' AND sSEShash = '$session'");
	}

	public function user_session_allowed($user)
	{
		$result = self::$mysqli->query("SELECT COUNT(iSESid)FROM sessions WHERE iSESuserid = '$user' AND (UNIX_TIMESTAMP(dSESstart)+iSESexp) > UNIX_TIMESTAMP()");
		$sesdata = $result->fetch_row();
		if ($sesdata[0] >= 1) {
			$allow = false;
		} else {
			$allow = true;
		}

		//Default
		return true;
	}

	public function get_public_user($usr,$pwd)
	{
		$hashpwd = hash("sha256",$pwd);
		$result = self::$mysqli->query("SELECT iUSRid, sUSRname, sUSRpwd, dUSRexpdate FROM publicusers WHERE (sUSRtaxid = '$usr' OR sUSRemail = '$usr') AND sUSRpwd = '$hashpwd' AND iUSRdel = '0'");
		$userdata = $result->fetch_row();
		if (is_array($userdata)) {
			if ($userdata[0] != "" && $userdata[0] >= 0) {
	 			$user = $userdata[0];  
	 		} else {
	 			$user = -1;
	 		}
	 	} else {
	 		$user = -1;
	 	}
		return $user;	
	}

	public function set_public_session($usr,$user)
	{
		$userstr = $usr . time();
		$session = hash("md5",$userstr);
		$expiretime = 7*24*3600; //one week

		$result = self::$mysqli->query("INSERT INTO publicsessions (iSESuserid, dSESstart, sSEShash, iSESexp) VALUES ('$user', NOW(), '$session', '$expiretime')");
		
		return $session;	
	}

	public function check_public_session($user,$session)
	{
		$result = self::$mysqli->query("SELECT COUNT(*), iSESid, iSESuserid, iSESexp, UNIX_TIMESTAMP(dSESstart), UNIX_TIMESTAMP() FROM publicsessions WHERE iSESuserid = '$user' AND sSEShash = '$session' AND (UNIX_TIMESTAMP(dSESstart)+iSESexp) > UNIX_TIMESTAMP()");
		$sesdata = $result->fetch_row();
		if ($sesdata[0] >= 1) {
			$sesid = $sesdata[1];
			$autuser = $sesdata[2];
			$newexp = (7*24*3600) + ($sesdata[5] - $sesdata[4]);
			$result2 = self::$mysqli->query("UPDATE publicsessions SET iSESexp = '$newexp' WHERE iSESid = '$sesid'");
		} else {
			$autuser = -1;
			$cvalue = "$autuser|0";
			$result2 = self::$mysqli->query("UPDATE publicsessions SET iSESexp = '-1' WHERE iSESuserid = '$user' AND sSEShash = '$session'");
			setcookie("publicactivesession", $cvalue, 0, "/");
		}

		return $autuser;
	}

	public function end_public_session($user,$session)
	{
		$result = self::$mysqli->query("UPDATE publicsessions SET iSESexp = '-1' WHERE iSESuserid = '$user' AND sSEShash = '$session'");
	}

	public function decrypt_pwd($pwdencraw)
	{
		$pwdenc = substr($pwdencraw,0,strlen($pwdencraw) - 5);
		$pwd = "";
		$pwdinv = "";
		$numseq = "";
		$numseqcomp = "";

		$tc = date("iwH");
		$tcc = strrev(substr($pwdencraw,-5));

		for ($i=0; $i < strlen($pwdenc); $i++) { 
			if ($i % 2 == 0) {
				$curchar = substr($pwdenc,$i,1);				
				if (strtoupper($curchar) == $curchar) {
					$curchar = strtolower($curchar);
				} else {
					$curchar = strtoupper($curchar);
				}
				$curchar = chr(ord($curchar) - 5); 
				$pwdinv .= $curchar;
			} else {
				$numseq .= substr($pwdenc,$i,1);
			}
		}
		
		$numseqlen = strlen($numseq);
		for ($i=0; $i < $numseqlen; $i++) { 
			$numseqcomp .= ($i % 10);
		}

		if ($tc == $tcc && $numseq == $numseqcomp) {
			$pwd = strrev($pwdinv);
		} else {
			$pwd = "_ERROR_";
		}

		return $pwd;
	}

	public function public_user_session_allowed($user)
	{
		$result = self::$mysqli->query("SELECT COUNT(iSESid)FROM publicsessions WHERE iSESuserid = '$user' AND (UNIX_TIMESTAMP(dSESstart)+iSESexp) > UNIX_TIMESTAMP()");
		$sesdata = $result->fetch_row();
		$usertp = self::get_public_usertype($user);
		if ($sesdata[0] >= 1 && $usertp > 1) {
			$allow = false;
		} else {
			$allow = true;
		}

		$allow = true;
		return $allow;
	}

	public function get_public_usertype($user)
	{
		$result = self::$mysqli->query("SELECT iUSRtype FROM publicusers WHERE iUSRid = '$user'");
		$userdata = $result->fetch_row();
		if (is_array($userdata)) {
			if ($userdata[0] != "" && $userdata[0] > 0) {
	 			$usertype = $userdata[0];  
	 		} else {
	 			$usertype = 0;
	 		}
	 	}

	 	return $usertype;	
	}
}
?>