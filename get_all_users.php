<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";
if(isset($_POST['selectType'])){
    $type = $_POST['selectType'];
    $echo_data = "";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();

    if($type == "admins"){
        if(isset($_POST['selecDepId'])){
            $depId = $_POST['selecDepId'];
        }else{
            $depId = "-1";
        }
        if(isset($_POST['searchTerm'])){
            $select = $_POST["searchTerm"];
            $sql = "SELECT * FROM users WHERE status='1' AND username LIKE '%$select%'";
        }else{
            $sql = "SELECT * FROM users WHERE status='1'";
        }

        $itemResultsAry = array();
        //$sql = "SELECT * FROM users";
        if($result = $conn->query($sql)) {
            $count = mysqli_num_rows($result);
            if($count > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $data_obj = new stdClass();
                    $data_obj->id = $row['id'];
                    $data_obj->text = $row['username'];
                    if($row['dep_id'] == $depId ){
                        $itemResultsAry[] = $data_obj;
                    }else if($row['dep_id'] == ""){
                        $itemResultsAry[] = $data_obj;
                    }
                }
            }
        }
        
        if(!empty($itemResultsAry)){
            $data_obj = new stdClass();
            $data_obj->results = $itemResultsAry;
            
            $pagination_obj = new stdClass();
            $pagination_obj->more = false;

            $data_obj->pagination = $pagination_obj;

            $echo_data = json_encode($data_obj);
            echo $echo_data;
        }else{
            echo "";
        }
    }else if($type == "users"){
        if(isset($_POST['selecDepId'])){
            $depId = $_POST['selecDepId'];
        }else{
            $depId = "-1";
        }
        $sql = "SELECT * FROM users WHERE status='1' ";
        $availableUsers = "";
        $selectedUsers = "";
        if($result = $conn->query($sql)) {
            $count = mysqli_num_rows($result);
            if($count > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $usrId = $row['id'];
                    $usrName = $row['username'];
                    if($row['dep_id'] == $depId){
                        $selectedUsers .= "<option value='$usrId'>$usrName</option>";
                    }else if($row['dep_id'] == ""){
                        $availableUsers .= "<option value='$usrId'>$usrName</option>";
                    }
                }
            }

        }
        
        $data_obj = new stdClass();
        $data_obj->available = $availableUsers;
        $data_obj->selected = $selectedUsers;
        echo json_encode($data_obj);
    }
}

?>