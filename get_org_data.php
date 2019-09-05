<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['data_type'])){
    $dataType = $_POST["data_type"];
 
    $db_ = new Database("formbuilder");
    $conn = $db_->getConnection();
    
    $sql = "SELECT * FROM organization_tree";
    if($result = $conn->query($sql)) {

        $count = mysqli_num_rows($result);
        
        if($count > 0){
            $params_row = array();
            while($row = mysqli_fetch_assoc($result)){
                $data_obj = new stdClass();
                $data_obj->id = $row['id'];
                $data_obj->name = $row["name"];
                $data_obj->parent = $row["parent_id"];
                $data_obj->mngr = $row["dep_mngr_user_id"];
                $params_row[] = $data_obj;
            }

            echo json_encode($params_row);
        }
    }
}

?>