<?php 
require('../../../database_config/epiaccount/config.inc.php');
require('../config/configuration.php');
require('../config/database.php'); 
require('../config/api.php'); 

$db = new Database();
$conn = $db->conn();

$return = array();

$stage = '';
if(!isset($_GET['stage'])){ 
    $return['status'] = 'Fail';
    $return['error_stage'] = '0';
    $return['error_message'] = 'NO STAGE FOUND';
    echo json_encode($return);
    $db->close(); 
    die(); 
}

$stage = mysqli_real_escape_string($conn, $_GET['stage']);

if($stage == 'checklogin'){
    if(
        (!isset($_REQUEST['username'])) ||
        (!isset($_REQUEST['password']))
    ){
        $return['status'] = 'Fail';
        $return['error_stage'] = '1';
        echo json_encode($return);
        $db->close(); 
        die(); 
    }

    $username = mysqli_real_escape_string($conn, $_REQUEST['username']);
    $password = mysqli_real_escape_string($conn, $_REQUEST['password']);

    $strSQL = "SELECT * FROM bcn_account WHERE username = '$username' AND active_status = '1'";
    $result = $db->fetch($strSQL, false);

    if($result){
        if (password_verify($password, $result['password'])) {
            $return['status'] = 'Success';
            $return['uid'] = $result['ID'];
        } else {
            $return['status'] = 'Fail';
            $return['error_stage'] = '3';
        }
    }else{
        $return['status'] = 'Fail';
        $return['error_stage'] = '2';
    }

    echo json_encode($return);
    $db->close(); 
    die(); 
}

if($stage == 'login'){
    if((!isset($_REQUEST['username'])) || (!isset($_REQUEST['password'])) || (!isset($_REQUEST['api_key']))){
        $return['status'] = 'Fail';
        $return['error_stage'] = '1';
        $return['error_message'] = 'INVALID PARAMETER';
        echo json_encode($return);
        $db->close(); 
        die(); 
    }

    $username = mysqli_real_escape_string($conn, $_REQUEST['username']);
    $password = mysqli_real_escape_string($conn, base64_encode($_REQUEST['password']));
    $api_key = mysqli_real_escape_string($conn, $_REQUEST['api_key']);

    if(!checkApi($db, $api_key)){
        $return['status'] = 'Fail';
        $return['error_stage'] = '2';
        $return['error_message'] = 'INVALID API KEY';
        echo json_encode($return);
        $db->close(); 
        die(); 
    }

    $strSQL = "SELECT * FROM epic_useraccount WHERE USERNAME = '$username' AND PASSWORD = '$password' AND ACTIVE_STATUS = 'Y' AND DELETE_STATUS = 'N'";
    $result = $db->fetch($strSQL, false);

    if($result){
        $return['status'] = 'Success';
        $return['uid'] = $result['UID'];
    }else{
        $return['status'] = 'Fail';
        $return['error_stage'] = '3';
        $return['error_message'] = 'EPIACCOUNT NOT FOUND';
    }

    echo json_encode($return);
    $db->close(); 
    die(); 
}


?>