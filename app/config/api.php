<?php 
function checkApi($db, $key){
    $strSQL = "SELECT * FROM epic_api WHERE api_key = '$key' AND api_active = 'Y' AND api_delete = 'N'";
    $res = $db->fetch($strSQL, false);
    
    $return = ($res) ? true : false;

    $api_stage = 'Fail';
    $api_stage = ($return) ? 'Success' : 'Fail';

    $strSQL = "INSERT INTO epic_api_log (`alog_datetime`, `alog_ip`, `alog_key`, `alog_stage`) VALUES ('$datetime', '$ip', '$key', '$api_stage')";
    $db->insert($strSQL, false);

    return $return;
}
?>