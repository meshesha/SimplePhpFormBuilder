<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";
if(isset($_POST['depToDel'])){
    $dep_del = $_POST['depToDel'];
    $echo_data = "";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();

    $user_ids = getData($conn, "organization_tree" , "parent_id='$dep_del'" , "id");
    //echo json_encode($user_ids);
    if($user_ids == "-1"){
        $sql = "DELETE FROM organization_tree WHERE id = $dep_del";
        if($conn->query($sql)) {
            $err = array();
            $user_ids = getData($conn, "users" , "dep_id='$dep_del'" , "id");
            if($user_ids != "-1"){
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
            if(empty($err)){
                echo "1|success";
            }else{
                echo "2|success deleting department but error occurred during deleting dep_id from users (". implode(".",$err). ")";
            }
        }else{
            echo "-1|db error: ". mysqli_error($conn) ;
        }
    }else{
        echo "-2|This department has sub-departments, delete the sub-departments first, or use the 'organization tree' view.";
    }

}else{
    echo "-3|error:dep_id is missing!";
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
       return "-1"; 
    }
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
?>