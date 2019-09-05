<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['searchTerm'])){
    $select = $_POST["searchTerm"];
    $sql = "SELECT * FROM organization_tree WHERE  name LIKE '%$select%'";
}else{
    $sql = "SELECT * FROM organization_tree";
}
    $echo_data = "";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();
    $itemResultsAry = array();
    //$sql = "SELECT * FROM organization_tree";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
                $data_obj = new stdClass();
                $data_obj->id = $row['id'];
                $data_obj->text = $row['name'];
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