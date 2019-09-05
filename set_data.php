<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['data'])){

    ////////get settings ///////
    if(!isset($isGetSetting)){
        require 'get_setting_data.php';
    }
    $setAdministratorUsersAsDefaultFormManager = getSetting("", "setAdministratorUsersAsDefaultFormManager");
    //////////////////////////
    $data_obj = json_decode($_POST['data']);
    $actionType = $data_obj->action;
    $data_table = $data_obj->table;
    $frm_data =  $data_obj->data;
    //echo json_encode($data_obj);
    $tbl = "";
    if($data_table == "form"){
        $tbl = "form_list";
    }else if($data_table == "formTemplate"){
        $tbl = "form_content";
    }else if($data_table == "users"){
        $tbl = "users";
    }else if($data_table == "user_reg_request"){
        $tbl = "registration_request";
    }else if($data_table == "groups"){
        $tbl = "users_gropes";
    }else if($data_table == "user_data"){
        $tbl = "form_data";
    }else if($data_table == "settings"){
        $tbl = "settings";
    }else if($data_table == "form_style"){
        $tbl = "form_custom_style";
    }else if($data_table == "org_tree"){
        $tbl = "organization_tree";
    }

    if($tbl == ""){
        die("data table not set");
    }
    $db = new Database("formbuilder");
    $conn = $db->getConnection();

    if($actionType == "new"){
        $data_ary = get_object_vars($frm_data);
        if($data_table == "form"){
            $set_new_frm_stt = satNewForm($conn,$data_ary,$setAdministratorUsersAsDefaultFormManager);
            echo $set_new_frm_stt;
        }else if($data_table == "formTemplate"){
            $set_new_tpl_stt = satNewTemplate($conn,$data_ary);
            echo $set_new_tpl_stt;
        }else if($data_table == "users"){
            $new_name = $data_ary["user_name"];
            $is_usr_name = getData($conn, $tbl , "username='$new_name'" , "email");
            if($is_usr_name == ""){
                $set_new_usr_stt = satNewUser($conn,$data_ary);
                echo $set_new_usr_stt;
            }else{
                echo "User name already exists";
           }
        }else if($data_table == "user_reg_request"){
            $new_usr_rqst_stt = satNewUserRequest($conn,$data_ary);
            echo $new_usr_rqst_stt;
        }else if($data_table == "groups"){
            //check if group name exists
            $new_name = $data_ary["group_name"];
            $is_grp_name = getData($conn, $tbl , "group_name='$new_name'" , "group_status");
            //echo "$tbl , $new_name ,$is_grp_name";
            if($is_grp_name == ""){
                $set_new_grp_stt = satNewGroup($conn,$data_ary);
                echo $set_new_grp_stt;
            }else{
                echo "Group name already exists";
           }
        }else if($data_table == "settings"){
            //////////////////////////////////////////
        }else if($data_table == "form_style"){
            $set_new_styl_stt = satNewCustomFormStyle($conn,$data_ary);
            echo $set_new_styl_stt;
        }else if($data_table == "org_tree"){
            $set_new_org_stt = satNewOrgDep($conn,$data_ary);
            echo $set_new_org_stt;
        }
    }else if($actionType == "update"){
        $data_ary = get_object_vars($frm_data);
        $update_stt_ary = array();
        $idx = $data_ary["record_id"];
        
        if($data_table == "form"){
            foreach ($data_ary as $name => $value) {
                //$cstm_data_str .=  "$name : $value\n";
                if($name != "record_id"){
                    switch($name){
                        case ("form_name"):
                            $update_stt_ary[] = updateData($conn,$tbl,"form_name", $value,"indx=$idx");
                            break;
                        case ("form_title"):
                            $update_stt_ary[] = updateData($conn,$tbl,"form_title", $value,"indx=$idx");
                            break;
                        case ("restrict_submit"):
                            $update_stt_ary[] = updateData($conn,$tbl,"amount_form_submission", $value,"indx=$idx");
                            break;
                        case ("publish_type"):
                            $update_stt_ary[] = updateData($conn,$tbl,"publish_type", $value,"indx=$idx");
                            break;
                        case ("publish_groups"):
                            //$value = json_encode($value);
                            if($value != ""){
                                $value = implode(",",$value);
                            }else{
                                $value = "";
                            }
                            $update_stt_ary[] = updateData($conn,$tbl,"publish_groups", $value,"indx=$idx");
                            break;
                        case ("publish_deps"):
                            //$value = json_encode($value);
                            if($value != ""){
                                $value = implode(",",$value);
                            }else{
                                $value = "";
                            }
                            $update_stt_ary[] = updateData($conn,$tbl,"publish_deps", $value,"indx=$idx");
                            break;
                        case ("form_managers"):
                            //$value = json_encode($value);
                            if($value != ""){
                                $value = implode(",",$value);
                            }else{
                                if($setAdministratorUsersAsDefaultFormManager == "1"){
                                    $value = getAdminUsrs($conn);//get administrator users
                                }else{
                                    $value = "";
                                }
                            }
                            $update_stt_ary[] = updateData($conn,$tbl,"admin_users", $value,"indx=$idx");
                            break;
                        case ("status_type"):
                            $update_stt_ary[] = updateData($conn,$tbl,"publish_status", $value,"indx=$idx");
                            break;
                        case ("form_note"):
                            $update_stt_ary[] = updateData($conn,$tbl,"form_note", $value,"indx=$idx");
                            break;
                        case ("form_general_style"):
                            $update_stt_ary[] = updateData($conn,$tbl,"form_genral_style", $value,"indx=$idx");
                            break;
                        default:
                            $update_stt_ary[] = "Error: The ver. '$name' is unknown";
                    }
                }
            }
        }else if($data_table == "formTemplate"){
            $form_obj = $data_ary["template"];
            $form_id = $data_ary["record_id"];
            $submitAndLablsAry = getFormSubmitFields($form_obj);
            $fieldObjAry = array();
            $fieldObjAry["form_form"] = $form_obj;
            $fieldObjAry["submit_fields"] = $submitAndLablsAry[0];
            $fieldObjAry["form_labels"] = $submitAndLablsAry[1];
            foreach ($fieldObjAry as $name => $value) {
                //$cstm_data_str .=  "$name : $value\n";
                $update_stt_ary[] = updateData($conn,$tbl,$name, $value,"form_id='$form_id'");
               
            }
        }else if($data_table == "users"){
            foreach ($data_ary as $name => $value) {
                if($name != "record_id" && $name != "changePass"){
                    switch($name){
                        case ("user_name"):
                            $update_stt_ary[] = updateData($conn,$tbl,"username", $value,"id=$idx");
                            break;
                        case ("user_pass"):
                            if($value != "" && $data_ary["changePass"] == "1" ){
                                $value = password_hash($value, PASSWORD_BCRYPT);
                                $update_stt_ary[] = updateData($conn,$tbl,"password", $value,"id=$idx");
                            }else{
                                 $update_stt_ary[] = "success";
                            }
                            break;
                        case ("user_email"):
                            $update_stt_ary[] = updateData($conn,$tbl,"email", $value,"id=$idx");
                            break;
                        case ("publish_groups"):
                            if($value != ""){
                                $value = implode(",",$value);
                            }else{
                                $value = "";
                            }
                            $update_stt_ary[] = updateData($conn,$tbl,"groups", $value,"id=$idx");
                            break;
                        case ("publish_dep"):
                            $update_stt_ary[] = updateData($conn,$tbl,"dep_id", $value,"id=$idx");
                            break;
                        case ("user_status"):
                            $update_stt_ary[] = updateData($conn,$tbl,"status", $value,"id=$idx");
                            break;
                    }
                }
            }
        }else if($data_table == "groups"){
            foreach ($data_ary as $name => $value) {
                if($name != "record_id"){
                    switch($name){
                        case ("group_name"):
                            $update_stt_ary[] = updateData($conn,$tbl,"group_name", $value,"indx=$idx");
                            break;
                        case ("group_status"):
                            $update_stt_ary[] = updateData($conn,$tbl,"group_status", $value,"indx=$idx");
                            break;
                        case ("users_managers"):
                            if($value != ""){
                                $value = implode(",",$value);
                            }else{
                                $value = "";
                            }
                            $update_stt_ary[] = updateData($conn,$tbl,"admin_ids", $value,"indx=$idx");
                            break;
                    }
                }
            }
        }else if($data_table == "settings"){
            ///////////
        }else if($data_table == "form_style"){
            $form_id = $data_ary["record_id"];
            $form_style = $data_ary["form_style"];
            $update_stt_ary[] = updateData($conn,$tbl,"form_style", $form_style,"form_id='$form_id'");
        }else if($data_table == "org_tree"){
            ///////////////////////////////////////////////////////////////////////////////////////////////////////
            foreach ($data_ary as $name => $value) {
                if($name != "record_id"){
                    switch($name){
                        case ("dep_name"):
                            $update_stt_ary[] = updateData($conn,$tbl,"name", $value,"id=$idx");
                            break;
                        case ("dep_prnt_id"):
                            $update_stt_ary[] = updateData($conn,$tbl,"parent_id", $value,"id=$idx");
                            break;
                        case ("dep_manager"):
                            $update_stt_ary[] = updateData($conn,$tbl,"dep_mngr_user_id", $value,"id=$idx");
                            break;
                        case ("selected_users"):
                            //$dep_id = $idx
                            $update_stt_ary[] = updateDepSelectedUsers($conn,$idx,$value);
                            break;
                        case ("available_users"):
                            $update_stt_ary[] = updateDepAvailableUsers($conn,$value);
                            break;
                    }
                }
            }
        }
        $echo_data = "";
        foreach ($update_stt_ary as $stt){
            if($stt != "success"){
                $echo_data .=  $stt."\n";
            }
        }
        if($echo_data == ""){
            $echo_data = "success";
        }
        echo $echo_data;
    }else if($actionType == "delete"){
        $data_ary = get_object_vars($frm_data);
        if($data_table == "form"){
            $deleteSatt = deletForm($conn, $data_ary);
            echo $deleteSatt;
        }else if($data_table == "formTemplate"){
           ////////////
        }else if($data_table == "user_data"){
            $deleteSatt = deletUserFormData($conn, $data_ary);
            echo $deleteSatt;
        }else if($data_table == "users"){
            $deleteSatt = deletUser($conn, $data_ary);
            echo $deleteSatt;
        }else if($data_table == "user_reg_request"){
            $deleteSatt = deletUserRegRequest($conn, $data_ary);
            echo $deleteSatt;
        }else if($data_table == "groups"){
            $deleteSatt = deletGroup($conn, $data_ary);
            echo $deleteSatt;
        }else if($data_table == "settings"){
            ////////////
        }else if($data_table == "form_style"){
            ////////////
        }else if($data_table == "org_tree"){
            ///////////////////////////////////////////////////////////////////////////////////////////////////
            $deleteSatt = deletDeps($conn, $data_ary);
            echo $deleteSatt;
        }
    }
    
}else{
    echo "something wrong";
}

function satNewForm($conn, $data_ary,$setAdminUsersAsDefaultFormManager){
    $rtrn_stt = "";
    $frm_name = $data_ary["form_name"];
    $frm_title = $data_ary["form_title"];
    $frm_restrict_submit = $data_ary["restrict_submit"];
    $frm_publish_type = $data_ary["publish_type"];
    if($data_ary["publish_groups"] != ""){
        $frm_grps = implode(",",$data_ary["publish_groups"]);
    }else{
        $frm_grps = "";
    }

    if($data_ary["publish_deps"] != ""){
        $frm_deps = implode(",",$data_ary["publish_deps"]);
    }else{
        $frm_deps = "";
    }

    if($data_ary["form_managers"] != ""){
        $frm_mngrs = implode(",",$data_ary["form_managers"]);
    }else{
        if($setAdminUsersAsDefaultFormManager == "1"){
            $frm_mngrs = getAdminUsrs($conn);//get administrator users
        }else{
            $frm_mngrs = "";
        }
    }
    $frm_status = "2";
    $frm_note = $data_ary["form_note"];
    $frm_gnrl_style = $data_ary["form_general_style"];

    $frm_name = mysqli_real_escape_string($conn, $frm_name);
    $frm_title = mysqli_real_escape_string($conn, $frm_title);
    $frm_note = mysqli_real_escape_string($conn, $frm_note);
    $sql = "INSERT INTO form_list (
        form_name,
        form_title,
        publish_type,
        publish_groups,
        publish_deps,
        publish_status,
        amount_form_submission,
        admin_users,
        form_note,
        form_genral_style)  VALUES (
            '{$frm_name}',
            '{$frm_title}',
            '{$frm_publish_type}',
            '{$frm_grps}',
            '{$frm_deps}',
            '{$frm_status}',
            '{$frm_restrict_submit}',
            '{$frm_mngrs}',
            '{$frm_note}',
            '{$frm_gnrl_style}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;
}
function satNewTemplate($conn,$data_ary){
    $form_obj = $data_ary["template"];
    //$form_obj = mysqli_real_escape_string($conn, $form_obj);
    $form_id = $data_ary["record_id"];
    $submitAndLablsAry = getFormSubmitFields($form_obj);
    $fields_str = $submitAndLablsAry[0];
    $form_labels_str = $submitAndLablsAry[1];
    //$form_labels_str = mysqli_real_escape_string($conn, $form_labels_str);
    $sql = "INSERT INTO  form_content (form_id,form_form,submit_fields,form_labels) VALUES ( 
        '{$form_id}','{$form_obj}','{$fields_str}','{$form_labels_str}')";

    if($result = $conn->query($sql)) {
        $echo_data = "success";
    }else{
        $echo_data = 'Error: ' . mysqli_error($conn);
    }
    return $echo_data;
}

function getFormSubmitFields($formObj){
    $formFields = json_decode($formObj);
    $fields_ary = array();
    $form_labels_ary = array();
    foreach($formFields as $field){
        if($field->type != "paragraph" && 
            $field->type != "header" && 
            $field->type != "Buttons" && 
            $field->type != "button" && $field->type != "hidden"){
            $fieldObj = new stdClass();
            $fieldObj->type = $field->type;
            $fieldObj->name = $field->name;
            if($field->type == "file"){
                if(isset($field->multiple)){
                    $fieldObj->multiple = $field->multiple;
                }
                if(isset($field->fileSize) && isset($field->sizeUnits)){
                    $fieldObj->fileSize = $field->fileSize;
                    $fieldObj->sizeUnits = $field->sizeUnits;
                }
            }
            $fields_ary[] = $fieldObj;
            $form_labels_ary[] = $field->label;
        }
    }
    $form_labels_str = json_encode($form_labels_ary, JSON_UNESCAPED_UNICODE);
    $fields_str = json_encode($fields_ary);
    return [$fields_str,$form_labels_str];
}

function satNewCustomFormStyle($conn,$data_ary){
    $rtrn_stt = "";
    $frm_id = $data_ary["record_id"];
    $frm_style = $data_ary["form_style"];
    $sql = "INSERT INTO form_custom_style (
        form_id,
        form_style)  VALUES (
        '{$frm_id}',
        '{$frm_style}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;
}
function satNewOrgDep($conn,$data_ary){
    $rtrn_stt = "";
    $dep_name = $data_ary["dep_name"];
    $prnt_id = $data_ary["dep_prnt_id"];
    $dep_mngr = $data_ary["dep_manager"];
    $sUsrs = $data_ary["selected_users"];
    $avlUsrs = $data_ary["available_users"];

    $dep_name = mysqli_real_escape_string($conn, $dep_name);
    $prnt_id = mysqli_real_escape_string($conn, $prnt_id);
    $dep_mngr = mysqli_real_escape_string($conn, $dep_mngr);

    $sql = "INSERT INTO organization_tree (
        name,
        parent_id,
        dep_mngr_user_id)  VALUES (
        '{$dep_name}',
        '{$prnt_id}',
        '{$dep_mngr}')";
    if($result = $conn->query($sql)) {
        //$rtrn_stt = "success";
        $dep_id = mysqli_insert_id($conn);
        $rtrn_stt = updateDepSelectedUsers($conn,$dep_id,$sUsrs);
        if($rtrn_stt == "success"){
            $rtrn_stt = updateDepAvailableUsers($conn,$avlUsrs);
        }
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;

}
function updateDepSelectedUsers($conn,$dep_id,$usrs){
    $usrsObj = json_decode($usrs);
    $update_stt_ary = array();
    foreach($usrsObj as $usrsId){
        $update_stt_ary[] = updateData($conn,"users","dep_id", $dep_id,"id=$usrsId");
    }
    $echo_data = "";
    foreach ($update_stt_ary as $stt){
        if($stt != "success"){
            $echo_data .=  $stt."\n";
        }
    }
    if($echo_data == ""){
        $echo_data = "success";
    }
    return $echo_data;

}
function updateDepAvailableUsers($conn,$usrs){
    $usersObj = json_decode($usrs);
    $update_stt_ary = array();
    foreach($usersObj as $usrsId){
        $update_stt_ary[] = updateData($conn,"users","dep_id", "","id=$usrsId");
    }
    $echo_data = "";
    foreach ($update_stt_ary as $stt){
        if($stt != "success"){
            $echo_data .=  $stt."\n";
        }
    }
    if($echo_data == ""){
        $echo_data = "success";
    }
    return $echo_data;

}
function satNewUser($conn,$data_ary){
    $rtrn_stt = "";
    $usr_name = $data_ary["user_name"];
    $usr_email = $data_ary["user_email"];
    $usr_pass = $data_ary["user_pass"];
    $usr_pass = password_hash($usr_pass, PASSWORD_BCRYPT);
    $usr_status = $data_ary["user_status"];
    $usr_grps = implode(",",$data_ary["publish_groups"]);
    $usr_dep = $data_ary["publish_dep"];
    //$fldName = mysqli_real_escape_string($conn, $fldName);
    $sql = "INSERT INTO users (
        username,
        email,
        password,
        status,
        groups,
        dep_id)  VALUES (
            '{$usr_name}',
            '{$usr_email}',
            '{$usr_pass}',
            '{$usr_status}',
            '{$usr_grps}',
            '{$usr_dep}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;
}
function satNewUserRequest($conn,$data_ary){
    $err = array();
    $type = $data_ary["type"];
    if($type == "single"){
        $idx = $data_ary["record_id"];
        //1.get user request data from table 'registration_request'
        $userRequestData = getUserRequestData($conn, $idx);
        if(!empty($userRequestData)){
            //2. copy  user request data to table 'users'
            $uName = $userRequestData["user_name"];
            $email = $userRequestData["email"];
            $pass =  $userRequestData["password"];
            $set_stt = satUserRequest($conn,$uName,$email,$pass);
            if($set_stt != "success"){
                $err[] = $set_stt;
            }else{
                //3. delete user request data from table 'registration_request'
                $delData["delType"] = "single";
                $delData["record_id"] = $idx;
                $del_stt = deletUserRegRequest($conn, $delData);
                if($del_stt != "success"){
                    $err[] = $del_stt;
                }
            }
        }else{
            $err[] = "Error:  user request data not found or sql error.";
        }
    }else if($type == "multi"){
        $selected = $data_ary["data"];
        $selectedObj = json_decode($selected);
        $stt_ary = array();
        foreach($selectedObj as $sel){
            //1.get user request data from table 'registration_request'
            $userRequestData = getUserRequestData($conn, $sel);
            if(!empty($userRequestData)){
                //2. copy  user request data to table 'users'
                $uName = $userRequestData["user_name"];
                $email = $userRequestData["email"];
                $pass =  $userRequestData["password"];
                $set_stt = satUserRequest($conn,$uName,$email,$pass);
                if($set_stt != "success"){
                    $err[] = $set_stt;
                }else{
                    //3. delete user request data from table 'registration_request'
                    $delData["delType"] = "single";
                    $delData["record_id"] = $sel;
                    $del_stt = deletUserRegRequest($conn, $delData);
                    if($del_stt != "success"){
                        $err[] = $del_stt;
                    }
                }
            }else{
                $err[] = "Error:  user request data not found or sql error.";
            }
        }
    }
    if(empty($err)){
        return "success";
    }else{
        return implode("|", $err);
    }
}
function getUserRequestData($conn, $idx){
    $dataAry = array();
    $sql = "SELECT * FROM registration_request WHERE indx=$idx";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $dataAry["user_name"]  = $row["user_name"];
                $dataAry["email"]  = $row["email"];
                $dataAry["password"]  = $row["password"];
            }
        }
    }
    return $dataAry;

}

function satUserRequest($conn,$uName,$email,$pass){
    $rtrn_stt = "";
    $usr_status = "1";
    $usr_grps = "";
    //$fldName = mysqli_real_escape_string($conn, $fldName);
    $sql = "INSERT INTO users (
        username,
        email,
        password,
        status,
        groups)  VALUES (
            '{$uName}',
            '{$email}',
            '{$pass}',
            '{$usr_status}',
            '{$usr_grps}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;
}
function satNewGroup($conn,$data_ary){
    $rtrn_stt = "";
    $grp_name = $data_ary["group_name"];
    $grp_status = $data_ary["group_status"];
    $mngrs = $data_ary["users_managers"];
    if(is_array($mngrs) && count($mngrs) > 1){
        $mngrstr = impode(",",$mngrs);
    }else{
        $mngrstr = $mngrs[0];
    }
    $grp_name = mysqli_real_escape_string($conn, $grp_name);
    $grp_status = mysqli_real_escape_string($conn, $grp_status);
    $mngrstr = mysqli_real_escape_string($conn, $mngrstr);
    $sql = "INSERT INTO users_gropes (
        group_name,
        group_status,
        admin_ids)  VALUES (
            '{$grp_name}',
            '{$grp_status}',
            '{$mngrstr}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;
}
function updateData($conn,$table, $col_name, $val, $idx_term){
    $rtrn_stt = "";
    //$fldName = mysqli_real_escape_string($conn, $fldName);
    $sql = "UPDATE $table SET $col_name = '$val' WHERE $idx_term";

    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;
}

function deletForm($conn, $data_ary){
    $isErr = false;
    $err = array();
    $idx = $data_ary["record_id"];
    $sql_form_list_tbl = "DELETE FROM form_list WHERE indx = $idx";
    $sql_form_content_tbl = "DELETE FROM form_content WHERE form_id = '$idx'";
    $sql_form_data_tbl = "DELETE FROM form_data WHERE form_id = '$idx'";
    $sql_form_datetime_tbl = "DELETE FROM form_data_datetimes WHERE form_id = '$idx'";
    $sql_form_files_tbl = "DELETE FROM form_files WHERE form_id = '$idx'";
    $sql_form_table = "DELETE FROM form_tables WHERE form_id = '$idx'";
    $sql_form_cstm_styl_table = "DELETE FROM form_custom_style WHERE form_id = '$idx'";
    //1. delete from table "form_list".
    //$fldName = mysqli_real_escape_string($conn, $fldName);
    if($conn->query($sql_form_list_tbl)) {
        $isErr = false;
    }else{
        $isErr = true;
        $err[] = "Error delete (UID:'$idx') from table 'form_list' : " . mysqli_error($conn);
    }
    //2. delete from table "form_content".
    if(!$isErr){
        if($conn->query($sql_form_content_tbl)) {
            $isErr = false;
        }else{
            $isErr = true;
            $err[] = "Error delete (UID:'$idx') from table 'form_content' : " . mysqli_error($conn);
        }
        //3. delete from table "form_data".
        if($conn->query($sql_form_data_tbl)) {
            $isErr = false;
        }else{
            $isErr = true;
             $err[] = "Error delete (UID:'$idx') from table 'form_data' : " . mysqli_error($conn);
        }
        //4. delete from table "form_data_datetimes".
        if($conn->query($sql_form_datetime_tbl)) {
            $isErr = false;
        }else{
            $isErr = true;
             $err[] = "Error delete (UID:'$idx') from table 'form_data_datetimes' : " . mysqli_error($conn);
        }
        //5. DELET file
        //5.1 get file names/path
        $files_ary = array();
        $files = getData($conn, "form_files" , "form_id='$idx'" , "file_path");
        if($files != ""){
            if(is_array($files)){
                $files_ary = $files;
            }else{
                $files_ary[] = $files;
            }
            //5.2 delete files from dir
            foreach($files_ary as $file){
                if($file != "" && $file !== null){
                    if (file_exists($file)){
                        if(!unlink($file)){
                            $err[] = "unable delete the file '$file' from dir.";
                        }
                    }
                }
            }
        }
        //5.3 delete from table "form_files".

        if($conn->query($sql_form_files_tbl)) {
            $isErr = false;
        }else{
            $isErr = true;
            $err[] = "Error delete (UID:'$idx') from table 'form_files' : " . mysqli_error($conn);
        }

        //6 delete from table "form_tables".
        if($conn->query($sql_form_table)) {
            $isErr = false;
        }else{
            $isErr = true;
            $err[] = "Error delete (UID:'$idx') from table 'form_tables' : " . mysqli_error($conn);
        }

        //7 delete from table "form_custom_style".
        if($conn->query($sql_form_cstm_styl_table)) {
            $isErr = false;
        }else{
            $isErr = true;
            $err[] = "Error delete (UID:'$idx') from table 'form_custom_style' : " . mysqli_error($conn);
        }
    }
    if(!$isErr && empty($err)){
        return "success";
    }else{
        return implode("|", $err);
    }
}
function deletUserFormData($conn, $data_ary){
    $delType = $data_ary["del_type"];
    if($delType == "single"){
        $formID = $data_ary["form_id"];
        $uid = $data_ary["uid"];
        $del_stt_ary = array();
        $del_stt = "";
        //1. delete from table "form_data".
        $del_stt = delteFormData($conn, $formID, $uid);
        if($del_stt != "success"){
            $del_stt_ary[] = $del_stt;
        }
        //2. delete from table "form_data_datetimes".
        $del_stt = delteFormDataDatetime($conn, $formID, $uid);
        if($del_stt != "success"){
            $del_stt_ary[] = $del_stt;
        }
        //3. delete from table "form_tables".
        $del_stt = delteFormTable($conn, $formID, $uid);
        if($del_stt != "success"){
            $del_stt_ary[] = $del_stt;
        }
        //4. DELET file
        //4.1 get file names/path
        //4.2 delete files from dir
        $del_stt = deleteFilesFromDir($conn, $formID, $uid);
        if($del_stt != "success"){
            if(strpos($del_stt,"|") === false){
                $del_stt_ary[] = $del_stt;
            }else{
                $del_stt_ary[] = explode("|", $del_stt);
            }
        }else{
            //4.3 delete from table "form_files".
            $del_stt = delteFormDataFiles($conn, $formID, $uid);
            if($del_stt != "success"){
                $del_stt_ary[] = $del_stt;
            }
        }
        if(empty($del_stt_ary)){
            return "success";
        }else{
            return implode("|", $del_stt_ary);
        }
    }else if($delType == "multi"){
        $selected = $data_ary["data"];
        $selectedObj = json_decode($selected);
        $del_stt_ary = array();
        foreach($selectedObj as $sel){
            $selAry = explode("," , $sel);
            $formID = $selAry[0];
            $uid = $selAry[1];
            $del_stt = "";
            //1. delete from table "form_data".
            $del_stt = delteFormData($conn, $formID, $uid);
            if($del_stt != "success"){
                $del_stt_ary[] = $del_stt;
            }
            //2. delete from table "form_data_datetimes".
            $del_stt = delteFormDataDatetime($conn, $formID, $uid);
            if($del_stt != "success"){
                $del_stt_ary[] = $del_stt;
            }
            //3. delete from table "form_tables".
            $del_stt = delteFormTable($conn, $formID, $uid);
            if($del_stt != "success"){
                $del_stt_ary[] = $del_stt;
            }
            //4. DELET file
            //4.1 get file names/path
            //4.2 delete files from dir
            $del_stt = deleteFilesFromDir($conn, $formID, $uid);
            if($del_stt != "success"){
                if(strpos($del_stt,"|") === false){
                    $del_stt_ary[] = $del_stt;
                }else{
                    $del_stt_ary[] = explode("|", $del_stt);
                }
            }else{
                //4.3 delete from table "form_files".
                $del_stt = delteFormDataFiles($conn, $formID, $uid);
                if($del_stt != "success"){
                    $del_stt_ary[] = $del_stt;
                }
            }
        }
        if(empty($del_stt_ary)){
            return "success";
        }else{
            return implode("|", $del_stt_ary);
        }
    }
}
function delteFormData($conn, $formID, $uid){
    $sql = "DELETE FROM form_data WHERE UID='$uid' AND form_id = '$formID'";
    if($conn->query($sql)) {
        $isErr = false;
    }else{
        $isErr = true;
        $err = "Error delete (form_id:'$formID') from table 'formID' : " . mysqli_error($conn);
    }
    if(!$isErr){
        return "success";
    }else{
        return $err;
    }
}
function delteFormDataDatetime($conn, $formID, $uid){
    $sql = "DELETE FROM form_data_datetimes WHERE UID='$uid' AND form_id = '$formID'";
    if($conn->query($sql)) {
        $isErr = false;
    }else{
        $isErr = true;
        $err = "Error delete (form_id:'$formID') from table 'form_data_datetimes' : " . mysqli_error($conn);
    }
    if(!$isErr){
        return "success";
    }else{
        return $err;
    }

}
function delteFormTable($conn, $formID, $uid){
    $sql = "DELETE FROM form_tables WHERE UID='$uid' AND form_id = '$formID'";
    if($conn->query($sql)) {
        $isErr = false;
    }else{
        $isErr = true;
        $err = "Error delete (form_id:'$formID') from table 'form_tables' : " . mysqli_error($conn);
    }
    if(!$isErr){
        return "success";
    }else{
        return $err;
    }
}
function delteFormDataFiles($conn, $formID, $uid){
    $sql = "DELETE FROM form_files WHERE UID='$uid' AND form_id = '$formID'";
    if($conn->query($sql)) {
        $isErr = false;
    }else{
        $isErr = true;
        $err = "Error delete (form_id:'$formID') from table 'form_files' : " . mysqli_error($conn);
    }
    if(!$isErr){
        return "success";
    }else{
        return $err;
    }

}

function deleteFilesFromDir($conn, $formID, $uid){
    $err = array();
    //1 get file names/path
    $files_ary = array();
    $files = getData($conn, "form_files" , "UID='$uid' AND form_id='$formID'" , "file_path");
    if($files == ""){
        return "success";
    }
    if(is_array($files)){
        $files_ary = $files;
    }else{
        $files_ary[] = $files;
    }
    //2 delete files from dir
    foreach($files_ary as $file){
        if($file != "" && $file !== null){
            if (file_exists($file)){
                if(!unlink($file)){
                    $err[] = "unable delete the file '$file' from dir.";
                }
            }
        }
    }
    if(empty($err)){
        return "success";
    }else{
        return implode("|", $err);
    }

}

function deletUser($conn, $data_ary){
    $del_stt = "";
    $idx = $data_ary["record_id"];
    $sql = "DELETE FROM users WHERE id = $idx";
    //1. delete from table "form_list".
    if($conn->query($sql)) {
        $del_stt = "success";
    }else{
        $del_stt = "Error : " . mysqli_error($conn);
    }

    return $del_stt;
}
function deletUserRegRequest($conn, $data_ary){
    $del_stt = "";
    $type = $data_ary["delType"];
    if($type == "single"){
        $idx = $data_ary["record_id"];
        $sql = "DELETE FROM registration_request WHERE indx = $idx";
        if($conn->query($sql)) {
            $del_stt = "success";
        }else{
            $del_stt = "Error : " . mysqli_error($conn);
        }
    }else if($type == "multi"){
        $selected = $data_ary["data"];
        $selectedObj = json_decode($selected);
        $del_stt_ary = array();
        foreach($selectedObj as $sel){
            $selAry = explode("," , $sel);
            $id = $selAry[0];
            $isVerified = $selAry[1];
            $sql = "DELETE FROM registration_request WHERE indx = $id";
            if(!$conn->query($sql)) {
                $del_stt_ary[] = "Error : " . mysqli_error($conn);
            }
        }
        if(empty($del_stt_ary)){
            $del_stt = "success";
        }else{
            $del_stt = implode("|", $del_stt_ar);
        }
    }
    return $del_stt;
}
function deletGroup($conn, $data_ary){
    $del_stt = "";
    $idx = $data_ary["record_id"];
    $sql = "DELETE FROM users_gropes WHERE indx = $idx";
    //1. delete from table "form_list".
    if($conn->query($sql)) {
        $del_stt = "success";
    }else{
        $del_stt = "Error : " . mysqli_error($conn);
    }

    return $del_stt;
}
function deletDeps($conn, $data_ary){
    $data = $data_ary["data"];
    $dataAry = json_decode($data);
    if($dataAry != "" && !empty($dataAry)){
        $err = array();
        foreach($dataAry as $dep){
            $dep_id = $dep->id;
            $sql = "DELETE FROM organization_tree WHERE id = $dep_id";
            if($conn->query($sql)) {
                //$del_stt = "success";
                $user_ids = getData($conn, "users" , "dep_id='$dep_id'" , "id");
                if($user_ids != ""){
                    if(is_array($user_ids)){
                        $update_stt_ary = array();
                        foreach($user_ids as $user_id){
                            $update_stt_ary[] = updateData($conn,"users","dep_id", "","id=$user_id");
                        }
                        foreach ($update_stt_ary as $stt){
                            if($stt != "success"){
                                $err[] =  $stt;
                            }
                        }
                    }else{
                        $updt_stt = updateData($conn,"users","dep_id", "","id=$user_ids");
                        if($updt_stt != "success"){
                            $err[] = $updt_stt;
                        }
                    }
                }
            }else{
                $err[] = "Error : (delet - ".$dep->name.")" . mysqli_error($conn);
            }
        }
        if(!empty($err)){
            return implode("\n",$err);
        }else{
            return "success";
        }
    }else{
        return "no data to delete";
    }
}
function getData($conn, $tbl , $term , $col){
    $data = array();
    $sql = "SELECT * FROM $tbl WHERE $term";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $data[]  = $row[$col]; 
            }
        }
    }
    if(count($data) > 1){
        return $data;
    }else if(count($data) == 1){
        return $data[0];
    }else{
       return ""; 
    }
}
function getAdminUsrs($conn){
    $itemResultsAry = array();
    $sql = "SELECT * FROM users WHERE status='1'";
    $adminGroupId = getManagersGroupId($conn,"administrator");
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $groups_ary = array();
                $grps = $row['groups'];
                if(strpos($grps,",") !== false){
                    $groups_ary = explode(",",$grps);
                }else{
                    $groups_ary[] = $grps;
                }
                if(in_array($adminGroupId, $groups_ary)){
                    $itemResultsAry[] = $row['id'];
                }
            }
        }
    }
    if(empty($itemResultsAry)){
        return "";
    }else{
        return implode(",",$itemResultsAry);
    }
}

function getManagersGroupId($conn,$group){
    $mngrGrpId = "";
    $sql = "SELECT indx FROM users_gropes WHERE group_name='$group'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $mngrGrpId = $row["indx"];
            }
        }
    }
    
    return $mngrGrpId;
}
?>