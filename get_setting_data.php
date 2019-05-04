<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

//include "chech_restricted.php";
$isGetSetting = true;
if(!isset($isSetDBclass)){
  require 'settings/database.class.php';
}

function getSetting($settingGroup, $settingName){
    if($settingGroup == "" && $settingName == ""){
        return "";
    }
    $db_setting = new Database("formbuilder");
    $conn = $db_setting->getConnection();
    $isSettingNameOnly = true;
    if($settingGroup != "" && $settingName ==""){
        $sql = "SELECT * FROM settings WHERE setting_group='$settingGroup'";
        $isSettingNameOnly = false;
    }else if($settingGroup == "" && $settingName !=""){
        $sql = "SELECT * FROM settings WHERE setting_name='$settingName'";
    }else{
        $sql = "SELECT * FROM settings WHERE setting_group='$settingGroup' AND setting_name='$settingName'";
    }
    $params_row = array();
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $params_row[$row['setting_name']] =  $row['setting_value'] ;
            }
        }
    }
    if(!empty($params_row)){
        if($isSettingNameOnly){
            return $params_row[$settingName];
        }else{
             return $params_row;
        }
    }else{
        return "";
    }
}

?>