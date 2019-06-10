<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include "chech_restricted.php";
$isGetCustomFormStyle = true;
if(!isset($isSetDBclass)){
  require 'settings/database.class.php';
}

if(isset($_POST['formId'])){
    $form_id = $_POST["formId"];
    $type = $_POST["get_type"];
    $echo_data = getCustomFormStyle($form_id,$type);
    echo $echo_data;
}else{
    echo ''; //must change to value --- TODO
}

function getCustomFormStyle($fromId,$type){
    $data = "";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();
    $sql = "SELECT * FROM form_custom_style WHERE form_id='$fromId'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
               $data =  $row['form_style'] ; 
            }
        }else{
            $data = "-1";
        }
    }else{
        $data = "ERROR";
    }
    if($data != "ERROR" && $data != "-1" && $data != ""){
        if($type == "style"){
            $data = "<style>\n$data\n</style>\n";
        }
    }

    return $data;
}
?>