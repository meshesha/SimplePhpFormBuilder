<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['data_indx'])){
    $data_indx = $_POST["data_indx"];
 
    $db_mntly = new Database("formbuilder");
    $conn = $db_mntly->getConnection();

    $sql = "SELECT * FROM form_data WHERE form_id='$data_indx'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        $form_fields_obj = getFormFields($conn, $data_indx);
        if($count > 0 && $form_fields_obj != ""){
            //$total = count($form_fields_obj);
            $form_fields_ary = array();
            $total_cols = 0;
            foreach($form_fields_obj as $field){
                $form_fields_ary[] = $field->name;
                $total_cols++;
            }
            $params_row = array();
            $params_cell = array();
            //$params_cell[] = "";
            $uid = "";
            $row_indx = 1;
            $cell_indx = 1;
            while($row = mysqli_fetch_assoc($result)){
                if($total_cols == 1){ //if totla cells is only one
                    $uid = $row['UID'];
                    $params_cell = array();
                    $params_cell[] = "";
                    $params_cell[] = $row['user_id']; //user id here
                    $params_cell[] = getFormDatetime($conn, $data_indx, $uid);
                    $chkData = checkData($row['field_name'],$row['field_type'],$row['field_value'],$form_fields_ary);
                    $params_cell[] = $chkData[0];
                    $params_cell[] = "<button class='btn btn-primary btn-sm'  onclick='getFormDetails(\"$data_indx\",\"$uid\")'  >Datails</buttn>
                                        <button class='btn btn-danger btn-sm'  onclick='delFormRecord(\"$data_indx\",\"$uid\")'  >Delete</buttn>";
                    $params_row[] = $params_cell;
                }else{
                    if($row_indx == 1){
                        $uid = $row['UID']; 
                        $total = checkTotalUID($conn, $data_indx , $uid); //check total for this UID
                        $params_cell = array();
                        $params_cell[0] = "";
                        $params_cell[1] = $row['user_id']; //user id/ip here
                        $params_cell[2] = getFormDatetime($conn, $data_indx, $uid);
                        $chkData = checkData($row['field_name'],$row['field_type'],$row['field_value'],$form_fields_ary);
                        $params_cell[3] = $chkData[0];
                        $row_indx++;
                    }else if($row_indx < $total){
                        $chkData = checkData($row['field_name'],$row['field_type'],$row['field_value'],$form_fields_ary);
                        if($chkData[0] != ""){
                            $params_cell[$chkData[1] + 3] = $chkData[0];
                        }
                        $row_indx++;
                    }else if($row_indx == $total){
                        $uid = $row['UID'];
                        $chkData = checkData($row['field_name'],$row['field_type'],$row['field_value'],$form_fields_ary);
                        if($chkData[0] != ""){
                            $params_cell[$chkData[1] + 3] = $chkData[0];
                        }
                        
                        $total_cells = (count($form_fields_ary) + 2); //3-1 = 3[index, user id/ip, date] - 1 [buttons]
                        for($i=0;$i <= $total_cells; $i++){
                            if(!isset($params_cell[$i])){
                                $params_cell[$i] = "";
                            }
                        }
                        
                        $params_cell[] = "<button class='btn btn-primary btn-sm'  onclick='getFormDetails(\"$data_indx\",\"$uid\")'  >Datails</button>
                                        <button class='btn btn-danger btn-sm'  onclick='delFormRecord(\"$data_indx\",\"$uid\")'  >Delete</button>
                                        <input type='hidden' class='form_id_uid' value='$data_indx,$uid'>";
                        $params_row[] = $params_cell;
                        $row_indx = 1;
                    }
                    $cell_indx ++;
                }
            }

            $data_obj = new stdClass();
            $data_obj->draw = 1;
            $data_obj->data = $params_row;

            echo json_encode($data_obj);
        }else{
            echo '{
                "draw": 0,
                "recordsTotal": 0,
                "data": [
                    ]
                }';
        }
    }else {
        echo '{
            "draw": 0,
            "recordsTotal": 0,
            "data": [
            ]
        }';
    }
}else{
    echo '{
        "draw": 0,
        "recordsTotal": 0,
        "data": [
        ]
    }';
}
function checkData($fName, $type, $data, $fNamesAry){
    $rData = "";
    $foundIndex = array_search($fName,$fNamesAry,true);
    if($foundIndex !== false){
        if($type != "checkbox-group"){
            $rData = $data;
        }else{
            if($data != ""){
                //if(strpos($data, ",") !== false){
                    $rData =  implode(",",json_decode($data));
                //}else{
                //    $rData = $data;
                //}
            }
        }
    }
    return [$rData,$foundIndex];
}
function checkTotalUID($conn, $formId , $uid){
    $count = 0;
    $sql = "SELECT * FROM form_data WHERE UID='$uid' AND form_id='$formId'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
    }
    return $count;
}
function getFormFields($conn, $formId){
    $filds_str = "";
    $sql = "SELECT submit_fields FROM form_content WHERE form_id='$formId'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $filds_str = $row['submit_fields'];
            }
        }
    }
    if($filds_str != ""){
        return json_decode($filds_str);
    }else{
        return "";
    }
}

function getFormDatetime($conn, $formId, $uid){
    $datetime = "";
    $sql = "SELECT datetimes FROM form_data_datetimes WHERE UID='$uid' AND form_id='$formId'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $datetime = $row['datetimes'];
            }
        }
    }
    return $datetime;
}

///////////////////////////////////////////////////////////////////////
function getPublishTypes($publish_type){
    switch($publish_type){
        case "0":
            return "";
            break;
        case "1":
            return "public";
            break;
        case "2":
            return "Users group";
            break;
        default:
            return "undefined group";
    }
}
function getPublishStatus($publish_status){
    switch($publish_status){
        case "0":
            return "";
            break;
        case "1":
            return "Published";
            break;
        case "2":
            return "Unpublished";
            break;
        default:
            return "undefined status";
    }
}
?>