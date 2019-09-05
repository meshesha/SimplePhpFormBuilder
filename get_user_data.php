<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['user_id'])){
    $user_id = $_POST["user_id"];
 
    $db_mntly = new Database("formbuilder");
    $conn = $db_mntly->getConnection();
    
    $sql = "SELECT * FROM users WHERE id=$user_id";
    if($result = $conn->query($sql)) {

        $count = mysqli_num_rows($result);
        
        if($count > 0){
            $params_row = array();
            while($row = mysqli_fetch_assoc($result)){
                $params_row['usr_name'] = $row['username']; 
                $params_row['email'] = $row["email"];
                $params_row['pass'] = "";//$row["password"];
                $params_row['status'] = $row["status"];
                $params_row['groups'] = $row["groups"];
                $params_row['dep'] = $row["dep_id"];
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

?>