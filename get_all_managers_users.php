<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['searchTerm'])){
    $select = $_POST["searchTerm"];
    $sql = "SELECT * FROM users WHERE status='1'"; // AND username LIKE '%$select%'
}else{
    $sql = "SELECT * FROM users WHERE status='1'";
}
    $echo_data = "";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();
    $itemResultsAry = array();
        $mngrsId = getManagersGroupId($conn,"managers");
        $adminId = getManagersGroupId($conn,"administrator");
            
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
                if(in_array($mngrsId, $groups_ary) || in_array($adminId, $groups_ary)){
                    $data_obj = new stdClass();
                    $data_obj->id = $row['id'];
                    $data_obj->text = $row['username'];
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
        $data_obj = new stdClass();
        $data_obj->results = [];
        
        $pagination_obj = new stdClass();
        $pagination_obj->more = false;

        $data_obj->pagination = $pagination_obj;

        $echo_data = json_encode($data_obj);
        echo $echo_data;
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