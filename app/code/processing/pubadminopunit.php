<?php
//Include validation functions
include '../base/functions/validations.php';
//Authentication
$matchcode = "b86027f4f0b60cf0234557b55744a9bf6ecf26f71df497e8533c721e1c85ec6d";
if (isset($_POST['reqcode'])) {
    $reqcode = $_POST['reqcode'];
} else {
    $reqcode = "";
}

if ($reqcode == $matchcode) {
    /*
    Initialize app
    */

    //Resources
    include '../base/core.php';
    include '../base/base.php';
    include '../base/adminop.php';
    
    //Initialize core instances
    $core_elements = Core::init();

    //Base elements
    $admin_op = new AdminOperations;

    date_default_timezone_set('EUROPE/LISBON');
    $lastupdate = date("Y-m-d H:i:s");
    
    if (isset($_POST['itemid'])) {
        $itemid = $_POST['itemid'];
    } else {
        $itemid = 0;
    }

    if (isset($_POST['userid'])) {
        $userid = $_POST['userid'];
    } else {
        $userid = 0;
    }
    if (isset($_POST['formaction'])) {
        $formaction = $_POST['formaction'];
    } else {
        $formaction = "";
    }

    $formdesc = array();
    $formdesc[0] = "Erro! Os dados não foram submetidos...";
    $formdesc[1] = "Os dados foram processados com sucesso!";
    $formdesc[2] = "O item foi eliminado com sucesso!";
    $formdesc[3] = "Não é possível eliminar unidades com utilizadores atribuídos..";
    $formdesc[4] = "Ação invãlida...";
    $valdesc = array();
    //Format
    $valdesc[1] = "Designação inválida";
    $valdesc[2] = "Morada inválida";
    $valdesc[3] = "Código invãido";
    $valdesc[4] = "Localidade invãida"; 
    //Functional
    $valdesc[5] = "Unidade já existe";
    $valdesc[6] = "Escolha concelho ou país";
    
    $valerrors[] = array(1,$formdesc[1]);

    //Collect http vars
    if (isset($_POST['entityid'])) {
        $entityid = $_POST['entityid'];
    } else {
        $entityid = 0;
    }
    if (isset($_POST['opuname'])) {
        $opuname = $_POST['opuname'];
    } else {
        $opuname = "";
    }
    if (isset($_POST['address'])) {
        $address = $_POST['address'];
    } else {
        $address = "";
    }
    if (isset($_POST['zipcode'])) {
        $zipcode = $_POST['zipcode'];
    } else {
        $zipcode = "";
    }
    if (isset($_POST['ziploc'])) {
        $ziploc = $_POST['ziploc'];
    } else {
        $ziploc = "";
    }
    if (isset($_POST['opuzone'])) {
        $opuzone = $_POST['opuzone'];
    } else {
        $opuzone = 1;
    }
    if (isset($_POST['oputype'])) {
        $oputype = $_POST['oputype'];
    } else {
        $oputype = 0;
    }
    if (isset($_POST['status'])) {
        $status = $_POST['status'];
    } else {
        $status = 0;
    }

    //Validations
    if ($formaction == "insert" || $formaction == "update") {
        //Name
        if (!textValidation($opuname) || strlen($opuname) < 3) {
            $valerrors[] = array("opuname",$valdesc[1]);
            $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
        }
        //Address
        if (!textValidation($address) || strlen($address) < 7) {
            $valerrors[] = array("address",$valdesc[2]);
            $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
        }
        //Zip code
        if (!zipcodeValidation($zipcode)) {
            $valerrors[] = array("zipcode",$valdesc[3]);
            $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
        }
        //Zip loc
        if (!textValidation($ziploc) || strlen($ziploc) < 3) {
            $valerrors[] = array("ziploc",$valdesc[4]);
            $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
        }
    }
    //Delete validation
    if ($formaction == "delete") {
        if ($admin_op->check_opunit_users($itemid)) {
            $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[3];
        }
    }

    //Functional validations
    if ($valerrors[0][0] == 1) {
        //Check if op unit exists
        //Check chages
        $chg = $admin_op->check_opunit_changes($itemid,$opuname);
        if ($itemid == 0 || $chg == 1) {
            if ($admin_op->check_if_opunit_exists($opuname,$entityid)) {
                $valerrors[] = array("opuname",$valdesc[5]);
                $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
            }   
        }
        //Zone
        if (strlen($zipcode) >= 4 && $opuzone == "1") {
            $valerrors[] = array("opuzone",$valdesc[6]);
            $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[0];
        }           
    }   

    //No errors
    if ($valerrors[0][0] == 1) {
        if ($formaction == "insert") {
            //Insert
            $datecreate = $lastupdate;
            $admin_op->insert_opunit($entityid,$opuname,$address,$zipcode,$ziploc,$opuzone,$oputype,$datecreate,$lastupdate,$status);
        } elseif ($formaction == "update") {
            //Update
            $admin_op->update_opunit($itemid,$entityid,$opuname,$address,$zipcode,$ziploc,$opuzone,$oputype,$lastupdate,$status);
            //Move users to headquarters
            if ($status == 0){
                $admin_op->remove_users_from_opunit($itemid,$entityid,$lastupdate);
            }
        } elseif ($formaction == "delete") {
            //Delete
            $admin_op->delete_opunit($itemid,$lastupdate);
            $valerrors[0][0] = 1; $valerrors[0][1] = $formdesc[2];
        } else {
            $valerrors[0][0] = 0; $valerrors[0][1] = $formdesc[4];
        }
    }

    //Response
    $response = $valerrors; 

    //Return the staus of the operation
    $jsonresp = json_encode($response);
    echo $jsonresp;
} else {
    echo "ERROR - Unathorized Access!";
}
?>