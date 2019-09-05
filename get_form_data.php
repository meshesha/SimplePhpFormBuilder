<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['form_id'])){
    $form_id = $_POST["form_id"];
 
    $db_mntly = new Database("formbuilder");
    $conn = $db_mntly->getConnection();
    
    $sql = "SELECT * FROM form_list WHERE indx=$form_id";
    if($result = $conn->query($sql)) {

        $count = mysqli_num_rows($result);
        
        if($count > 0){
            $params_row = array();
            while($row = mysqli_fetch_assoc($result)){
                //$params_row['form_nik'] =  $row['form_id'] ; 
                $params_row['frm_name'] = $row['form_name']; 
                $params_row['frm_title'] = $row["form_title"];
                $params_row['restrict_submissions'] = $row["amount_form_submission"];
                $params_row['publ_type'] = $row["publish_type"];
                $params_row['publ_type_name'] = getPublishTypes($conn,$row["publish_type"]);
                $params_row['publ_grps'] = $row["publish_groups"];//getGroupsObj($conn,$form_id,$row["publish_groups"]);
                $params_row['publ_deps'] = $row["publish_deps"];//getGroupsObj($conn,$form_id,$row["publish_groups"]);
                $params_row['publ_status'] = $row["publish_status"];
                $params_row['admin_users'] = $row["admin_users"];
                $params_row['frm_note'] = $row["form_note"];
                $params_row['frm_style'] = $row["form_genral_style"]; 
            }
            $data_obj = new stdClass();
            $data_obj->status = 1;
            $data_obj->data = $params_row;

            echo json_encode($data_obj);
        }else{
            echo '{
                "status": 0,
                "data": [
                    ]
                }';
        }
    }else {
        echo '{
            "status": 0,
            "data": [
                ]
            }';
    }
}else{
    echo '{
        "status": 0,
        "data": [
            ]
        }';
}
function getGroupsObj($conn,$form_id,$grops){
    if($grops != ""){
        $gropsAry = explode(",",$grops);
        $itemResultsAry = array();
        foreach($gropsAry as $gropId){
            $sql = "SELECT * FROM users_gropes WHERE indx=$gropId";
            if($result = $conn->query($sql)) {
                $count = mysqli_num_rows($result);
                if($count > 0){
                    while($row = mysqli_fetch_assoc($result)){
                        $data_obj = new stdClass();
                        $data_obj->id = $gropId;
                        $data_obj->text = $row['group_name'];
                        $itemResultsAry[] = $data_obj;
                    }
                    return json_encode($itemResultsAry);
                }else{
                    return "";
                }
            }else{
                return "";
            }
        }
    }else{
        return "";
    }
}
function getPublishTypes($conn,$publish_type_id){
    $sql = "SELECT name FROM publish_type WHERE id=$publish_type_id";
    $typName = "";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            $row = mysqli_fetch_assoc($result);
            $typName = $row['name'];
        }
    }
    return $typName;
}
?>