<?php
function alphaValidation($field) {
	$test = "/^[a-zA-Z áãâÁéçíóõÓúÚ]*$/";
	$result = preg_match($test, $field);
	return $result;
}

function textValidation($field) {
	$test = "/^[a-zA-Z0-9\-\+=<> _áàãâÁÀÃÂéèêÉÈçíÍóõôÓúÚ!,.;:?ºª@€\$£()\[\]\{\}\/\"\'\«\»]*$/";
	$result = preg_match($test, $field);
	return $result;
}

function longTextValidation($field) {
	$test = "/^[a-zA-Z0-9\-\+=<>\r\n _áàãâÁÀÃÂéèêÉÈçíÍóõôÓúÚ!,.;:?ºª@€\$£()\[\]\{\}\/\"\'\«\»]*$/";
	$result = preg_match($test, $field);
	return $result;
}

function alphaNumValidation($field) {
	$test = "/^[a-zA-Z0-9 áãâÁéçíóõÓúÚ]*$/";
	$result = preg_match($test, $field);
	return $result;
}

function emailValidation($field) {
	$test = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/";
	$result = preg_match($test, $field);
	return $result;
}

function phoneValidation($field) {
	$test = "/^(([+]{1}|[0]{2})[0-9]{2,4}){0,1}[\s]{0,1}[0-9]{9}$/";
	$result = preg_match($test, $field);
	return $result;
}

function nemericValidation($field) {
	$test = "/^\d+$/";
	$result = preg_match($test, $field);
	return $result;
}

function dateValidation($field) {
	$test = "/^([0-9]{2})-([0-9]{2})-([0-9]{4})$/";
	$result = preg_match($test, $field);
	if ($result) {
		$dateparts = explode("-", $field);
		$ISOdate = $dateparts[2] . "-" . $dateparts[1] . "-" . $dateparts[0];
		$dateCheck = date("d", strtotime($ISOdate));
		if ($dateCheck == $dateparts[0]) {
			return true;
		} else {
			return false;	
		}
	} else {
		return false;
	}
}

function dateCompare($date1,$date2) {
	$dateparts1 = explode("-", $date1);
	$ISOSate1 = $dateparts1[2] . "-" . $dateparts1[1] . "-" . $dateparts1[0];
	$date1 = strtotime($ISOSate1);
	$dateparts2 = explode("-", $date2);
	$ISOSate2 = $dateparts2[2] . "-" . $dateparts2[1] . "-" . $dateparts2[0];
	$date2 = strtotime($ISOSate2);	
	$datedif = $date2-$date1;
	if ($datedif <> 0) {
		//1=>GT, -1=>LT
		$datecomp = $datedif/abs($datedif); 	
	} else {
		//0=>EQ
		$datecomp = 0;
	}
	return $datecomp;
}

function coordinateValidation($field) {
	$test = "/^[-]{0,1}[0-9]{1,2}[.]{1}[0-9]{4,7}$/";
	$result = preg_match($test, $field);
	return $result;
}

function zipcodeValidation($field) {
	$test = "/^([0-9]{4})-([0-9]{3})$/";
	$result = preg_match($test, $field);
	return $result;
}

function nifValidation($field) {
	$nif = trim($field);
	$ptvt = true;

	$cc = substr($nif, 0, 2);
	$test = "/^[A-Z]{2}$/";
	$result = preg_match($test, $cc);
	if ($result) {
		if ($cc == "PT") {
			$nif = substr($nif, -9);
		} else {
			$ptvt = false;
		}
	}

	if ($ptvt) {
	    $nif_split = str_split($nif);
	    $nif_primeiro_digito = array(1, 2, 3, 5, 6, 7, 8, 9);
	    if (is_numeric($nif) && strlen($nif) == 9 && in_array($nif_split[0], $nif_primeiro_digito)) {
	        $check_digit = 0;
	        for ($i = 0; $i < 8; $i++) {
	            $check_digit += $nif_split[$i] * (10 - $i - 1);
	        }
	        $check_digit = 11 - ($check_digit % 11);
	        $check_digit = $check_digit >= 10 ? 0 : $check_digit;
	        if ($check_digit == $nif_split[8]) {
	            return true;
	        }
	    }
	} else {
		$test = "/^([A-Z]{2})([0-9A-Z]{5,11})$/";
		$result = preg_match($test, $nif);
		return $result;
	}
    return false;
}

function regplateValidation($field) {
	$test = "/^([0-9a-zA-Z .\/_\-]{7,10})$/";
	$result = preg_match($test, $field);
	return $result;
}

function imageValidation($field) {
	$test = "/^[\._a-zA-Z0-9\- ãàáÃÀÁéÉêÊíÍóõôÓÕÔúÚçÇ]+\.(jpeg|JPEG|jpg|JPG|png|PNG|gif|GIF)$/";
	$result = preg_match($test, $field);
	return $result;
}

function pwdValidation($field) {
	$test = "/^[a-zçA-ZÇ0-9!@#\$%&\-\+\*_=\?€\(\)\{\}\[\]]{6,24}$/";
	$result1 = preg_match($test, $field);
	$test = "/^.*[a-zç]+.*$/";
	$result2 = preg_match($test, $field);
	$test = "/^.*[A-ZÇ]+.*$/";
	$result3 = preg_match($test, $field);
	$test = "/^.*[0-9]+.*$/";
	$result4 = preg_match($test, $field);
	$test = "/^.*[!@#\$%&\-\+\*_=\?€\(\)\{\}\[\]]+.*$/";
	$result5 = preg_match($test, $field);

	if ($result1 && $result2 && $result3 && $result4 && $result5) {
		$result = true;
	} else {
		$result = false;
	}
	return $result;
}

function makePwd($length,$chrset,$case) {
    //Type of password
    if ($length < 8 || $length > 16) {
    	$length = 12;
    }
    if ($chrset == 0) {
    	if ($case) {
    		$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!#$%&";
    	} else {
    		$chars = "0123456789abcdefghijklmnopqrstuvwxyz_!#$%&()";
    	}
    } elseif ($chrset == 1) {
    	if ($case) {
    		$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    	} else {
    		$chars = "0123456789abcdefghijklmnopqrstuvwxyz";
    	}
    } elseif ($chrset == 2) {
    	if ($case) {
    		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    	} else {
    		$chars = "abcdefghijklmnopqrstuvwxyz";
    	}
    } elseif ($chrset == 3) {
    	$chars = "0123456789";
    } else {
    	$chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!#$%&";
    }
   	
    $randomidxarr = array();
    for ($i = 0; $i < $length ; $i++) {
        $index = rand(0,strlen($chars) - 1);
        if ($i == 0) {
        	$randomidxarr[0] = substr($chars,$index,1);
        } else {
        	$randomidxarr[$i] = substr($chars,$index,1);
        	//Prevent consecutive chars
        	while ($randomidxarr[$i] == $randomidxarr[$i-1]) {
        		$index2 = rand(0,strlen($chars) - 1);
        		$randomidxarr[$i] = substr($chars,$index2,1);
        	}
        }
    }
    $randomString = implode($randomidxarr);
    return $randomString;
}
?>