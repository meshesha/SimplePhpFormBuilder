<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['data_indx']) && $_POST["user_id"]){
    $data_indx = $_POST["data_indx"];
    $userId = $_POST["user_id"];
    $formIdsAry = array();
    if($data_indx != "" && $userId != ""){
        if(strpos($data_indx,",") !== false){
            $formIdsAry = explode(",",$data_indx);
        }else{
            $formIdsAry[] = $data_indx;
        }
    }
    $db_mntly = new Database("formbuilder");
    $conn = $db_mntly->getConnection();
    
    $sql = "SELECT * FROM form_list";
    if($result = $conn->query($sql)) {

        $count = mysqli_num_rows($result);
        
        if($count > 0){
            $params_row = array();
            $count_indx = 1;
            while($row = mysqli_fetch_assoc($result)){
                $params_cell = array();
                $row_id = $row['indx'] ; 
                if(!empty($formIdsAry)){
                    if(!in_array($row_id, $formIdsAry)){
                        continue;
                    }
                }
                $params_cell[] =  $count_indx; //0
                $params_cell[] =  $row_id; //1
                $params_cell[] = $row['form_name']; //2
                $params_cell[] = $row["form_title"]; //3
                $params_cell[] = getPublishTypes($conn,$row["publish_type"]); //4
                $params_cell[] = getPublishStatus($row["publish_status"]); //5
                //$params_cell[] = $row["admin_users"]; //6
                //$params_cell[] = $row["form_note"]; //7
                $params_cell[] = "<button type='button' class='btn btn-primary btn-sm' onclick='update_form(this)' >Details</button>
                                    <button type='button' class='btn btn-danger btn-sm' onclick='delete_form(\"$row_id\")' >Delete</button>"; //9
                $params_row[] = $params_cell;
                $count_indx++;

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
function getPublishTypes($conn, $publish_type){
    $sql = "SELECT * FROM publish_type WHERE id=$publish_type";
    $type_name = "undefined group";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $type_name = $row["name"];
            }
        }
    }
    return $type_name;
    /*
    switch($publish_type){
        case "0":
            return "";
            break;
        case "1":
            return "public";
            break;
        case "2":
            return "Group";
            break;
        case "3":
            return "Public-Anonymously";
            break;
        case "4":
            return "Groups-Anonymously";
            break;
        default:
            return "undefined group";
    }
    */
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