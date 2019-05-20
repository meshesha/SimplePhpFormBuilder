<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['searchTerm'])){
    $select = $_POST["searchTerm"];
    $sql = "SELECT * FROM users_gropes WHERE group_status='1' AND group_name LIKE '%$select%'";
}else{
    $sql = "SELECT * FROM users_gropes WHERE group_status='1'";
}
    $echo_data = "";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();
    $itemResultsAry = array();
    //$sql = "SELECT * FROM users_gropes";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                //$itemResultsAry[] =  array("id"=>$row['indx'], "text"=>$row['group_name']);
                $data_obj = new stdClass();
                $data_obj->id = $row['indx'];
                $data_obj->text = $row['group_name'];
                $itemResultsAry[] = $data_obj;
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


?>