<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['form_id'])){
    $form_id = $_POST["form_id"];
    $uid = $_POST["form_uid"];
    $echo_data = "new";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();
    
    $sql = "SELECT * FROM form_content WHERE form_id='$form_id'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
               $echo_data =  $row['form_form'] ; 
            }
        }
    }
    if($echo_data != "new" && $echo_data != ""){
        $submitBtnObj = new stdClass();
        $submitBtnObj->type = "button";
        $submitBtnObj->subtype = "submit";
        $submitBtnObj->label = "Submit";
        $submitBtnObj->className = "btn-primary btn";
        $submitBtnObj->name = "button-submit-form";
        $submitBtnObj->id = "button-submit-form";
        $submitBtnObj->style = "primary";

        
        $hiddenObj = new stdClass();
        $hiddenObj->type = "hidden";
        $hiddenObj->name = "hidden-form-id";
        $hiddenObj->id = "hidden-form-id";
        $hiddenObj->value = $form_id;

        $form = json_decode($echo_data);
        $frm_ary = array();

        //get form type - if it is type 1 or 2 then show user data
        $publishTyp = getFormData($conn, $form_id);
        if($publishTyp != "" && ($publishTyp == "1" || $publishTyp == "2")){
            $hideShowUserObj = new stdClass();
            $hideShowUserObj->type = "paragraph";
            $hideShowUserObj->subtype = "div";
            $hideShowUserObj->className = "ui-widget-header hide-show-user-data";
            $hideShowUserObj->label = "Show/hide user data";
            $frm_ary[] = $hideShowUserObj;
            //User data
            $userData = getUserData($conn, $form_id, $uid,$publishTyp);
            $user_id = "";
            $user_name = "";
            $user_mail = "";
            if($userData != "" && !empty($userData)){
                $user_id = $userData[0];
                $user_name = $userData[1];
                $user_mail = $userData[2];
            }
            $userIPID = "<label for='sender_user_id_ip'>User id/ip: </label><input type='text' id='sender_user_id_ip' class='form-control' value='$user_id' disabled/><br>";
            $userIPID .= "<label for='sender_user_name'>User name: </label><input type='text' id='sender_user_name' class='form-control' value='$user_name' disabled/><br>";
            $userIPID .= "<label for='sender_user_email'>User email: </label><input type='text' id='sender_user_email' class='form-control' value=' $user_mail' disabled/>";
            $userDataObj = new stdClass();
            $userDataObj->type = "paragraph";
            $userDataObj->subtype = "div";
            $userDataObj->className = "ui-widget-content user-data-content-warper";
            $userDataObj->label = $userIPID;
            $frm_ary[] = $userDataObj;

            $sprtObj = new stdClass();
            $sprtObj->type = "paragraph";
            $sprtObj->subtype = "div";
            $sprtObj->label = "<hr>";
            $frm_ary[] = $sprtObj;
        }

        foreach($form as $fild){
            //get user data if exist

            //remove header and paragraph if exists
            if($fild->type != "file" && $fild->type != "header" && $fild->type != "paragraph" && $fild->type != "table"){ // 
                $frm_ary[] = setFormValues($conn, $form_id, $uid, $fild);
            }
            //if file
            if($fild->type == "file"){
                $fileTbl = getFilesListTable($conn, $form_id, $uid, $fild->name, $fild->label);
                $filesTblObj = new stdClass();
                $filesTblObj->type = "paragraph";
                $filesTblObj->subtype = "div";
                $filesTblObj->label = $fileTbl;
                $filesTblObj->className = "file-list-table";
                $frm_ary[] = $filesTblObj;
            }
            //if table
            if($fild->type == "table"){
                $tblContent = getTableContent($conn, $form_id, $uid,$fild->name, $fild->placeholder, $fild->label);
                $filesTblObj = new stdClass();
                $filesTblObj->type = "paragraph";
                $filesTblObj->subtype = "div";
                $filesTblObj->label = $tblContent;
                $filesTblObj->className = "file-list-table";
                $frm_ary[] = $filesTblObj;
            }

        }
        //array_push($frm_ary,$hiddenObj);
        //array_push($frm_ary,$submitBtnObj);
        $echo_data = json_encode($frm_ary);
    }
    echo $echo_data;
}else{
    echo 'Error: missing form_id';
}
function getUserData($conn, $form_id, $uid , $publishTyp){
    $userId = "";
    $sql = "SELECT user_id FROM form_data WHERE form_id='$form_id' AND UID='$uid' LIMIT 1";
    if($result = $conn->query($sql)) {
        $row = mysqli_fetch_assoc($result);
        $userId = $row['user_id'];
    }
    if($userId == "" || $publishTyp == "1"){
        return [$userId,"",""];
    }else{
        $uSql = "SELECT username,email FROM users WHERE id=$userId ";
        $data_ary = array();
        if($result = $conn->query($uSql)) {
            $count = mysqli_num_rows($result);
            if($count > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $data_ary[] = $userId;
                    $data_ary[] = $row['username'];
                    $data_ary[] = $row['email'];
                }
            }
        }
        return $data_ary;
    }
}
function setFormValues($conn, $form_id, $uid, $field){
    $fType = $field->type;
    $fname = $field->name;
    $fval = getFieldValue($conn, $form_id, $uid,$fname);
    //if $fval == "" - TODO
    /*
    */
    if($fval == "-1"){
        if($fType == "select"){
            $valsAry = array();
            $slctValsObj = new stdClass();
            $slctValsObj->label = "";
            $slctValsObj->value = "";
            $slctValsObj->selected = true;
            $valsAry[] = $slctValsObj;
            $field->values = $valsAry;
        }else{
            $field->value = "";
            $field->disabled = true;
            return $field;
        }
    }
    if($fType == "checkbox-group"){
        if(isset($field->other) && $field->other == true){
            $field->other = false;
            
            //$fvalAry = explode(",",$fval);
            $fvalAry = json_decode($fval);
            $selectedAry = array();
            foreach($field->values as $val){
                if(in_array($val->value,$fvalAry)){
                    $val->selected = true;
                    $selectedAry[] = $val->value;
                }else{
                    $val->selected = false;
                }
                $val->disabled = true;
            }
            $dif = array_diff($fvalAry,$selectedAry);
            $otherSelected = "";
            if(!empty($dif)){
                $otherSelected = implode(",",$dif);
            }
            $otherOptObj = new stdClass();
            $otherOptObj->label = (($otherSelected != "")?"Other: ".$otherSelected:"Other");
            $otherOptObj->value = (($otherSelected != "")?$otherSelected:"other");
            if($otherSelected != "")
                $otherOptObj->selected = true;
            $otherOptObj->disabled = true;
            $field->values[] = $otherOptObj;
        }else{
            //$fvalAry = explode(",",$fval);
            if($fval != ""){
                $fvalAry = json_decode($fval);
                foreach($field->values as $val){
                    if(in_array($val->value,$fvalAry)){
                        $val->selected = true;
                    }else{
                        $val->selected = false;
                    }
                    $val->disabled = true;
                }
            }
        }
    }else if($fType == "radio-group"){
        if(isset($field->other) && $field->other == true){
            $field->other = false;
            $otherOptObj = new stdClass();
            $isOtherSelected = true;
            foreach($field->values as $val){
                if($val->value == $fval){
                    $isOtherSelected = false;
                    break;
                }
            }
            $otherOptObj->label = (($isOtherSelected)?"Other: ".$fval:"Other");
            $otherOptObj->value = (($isOtherSelected)?$fval:"other");
            $field->values[] = $otherOptObj;
        }
        $field->value = $fval;
        $field->disabled = true;
    }else{
        $field->value = $fval;
        $field->disabled = true;
    }

    return $field;
}

function getFormData($conn, $formId){
    $data = "";
    $sql = "SELECT publish_type FROM form_list WHERE indx = $formId";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $data = $row['publish_type'];
            }
        }
    }
    return $data;
}

function getFieldValue($conn, $form_id, $uid,$fname){
    $filds_str = "-1";
    $sql = "SELECT field_value FROM form_data WHERE form_id='$form_id' AND UID='$uid' AND field_name='$fname'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $filds_str = $row['field_value'];
            }
        }
    }
    return $filds_str;
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
function getFilesListTable($conn, $form_id, $uid, $name, $label){
    //$tbl = "<table class='table'><tr><td>#</td><td>File name</td><td>Link</td></tr><td>1</td><td>test.png</td><td><a href='#'>open</a></td><tr></tr></table>";
    
    $tble = "<label for='$name'>$label</label><table id='$name' class='table table-sm'><thead><tr class='table-primary'><td>#</td><td>File name</td><td>Link</td></tr></thead>";
    $sql = "SELECT * FROM form_files WHERE UID='$uid' AND form_id='$form_id'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            $row_indx = 1;
            while($row = mysqli_fetch_assoc($result)){
                $tble .= "<tr><td>$row_indx</td><td>" .$row['file_name']."</td><td><a href='".$row['file_path']."' target='_blank' class='btn btn-outline-primary' role='button'>open</a></td></tr>";
            }
        }else{
            $tble .= "<tr><td colspan='3'>No file</td></tr>";
        }
    }else{
        $err =  mysqli_error($conn);
        $tble .= "<tr><td colspan='3'>sql error:  $err</td></tr>";
    }
    $tble .= "</table><br>";
    return $tble;
}

function getTableContent($conn, $form_id, $uid, $tblName, $attr, $label){
    $columns = $attr;
    $columns = str_replace("&quot;","\"",$columns);
    //$columns = str_replace("\n"," ",$columns);
    //$columns = str_replace("\r"," ",$columns);
    //return $columns;
    $columnsObj = json_decode($columns);
    if(empty($columnsObj)){
        return $columns;
    }
    $headerAry = array();
    $tble = "<label for='$tblName'>$label</label><table id='tblName' class='table table-bordered table-sm'><thead><tr class='table-primary'>";
    foreach($columnsObj as $column){
        $columName = $column->name;
        $columName = str_replace("&quot;","\"",$columName);
        $headerAry[] = $columName;
        $tble .= "<td>".$columName."</td>";
    }
    $colsLen = count($headerAry);
    $tble .= "</tr></thead>";
    $sql = "SELECT * FROM form_tables WHERE UID='$uid' AND form_id='$form_id' AND table_name='$tblName' ";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            $tblRows = "";
            while($row = mysqli_fetch_assoc($result)){
                $tblRows = $row['table_data'];
            }
            if($tblRows != ""){
                $tblRows = str_replace("&quot;","\"",$tblRows);
                $tblRows = str_replace("\n","<br>",$tblRows);
                $tblRows = str_replace("\r","<br>",$tblRows);
                $tblRowsObj = json_decode($tblRows);
                foreach($tblRowsObj as $tblRow){
                    $tble .= "<tr>";
                    foreach($tblRow as $tblCol){
                        $tblCol = str_replace("&quot;","\"",$tblCol);
                        $tble .= "<td>$tblCol</td>";
                    }
                    $tble .= "</tr>";
                }
                //$tble .= "<tr><td></td><td></td><td></td></tr>";
            }
        }else{
            $tble .= "<tr><td colspan='$colsLen'>No content</td></tr>";
        }
    }else{
        $err =  mysqli_error($conn);
        $tble .= "<tr><td colspan='$colsLen'>sql error:  $err</td></tr>";
    }    
    $tble .= "</table>";
    return $tble;
}
?>