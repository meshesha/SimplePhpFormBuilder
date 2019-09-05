<?php

include "chech_restricted.php";

if(isset($_POST['table'])){
    require 'settings/database.class.php';
    $table = $_POST["table"];
    $echo_data = "";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();
    if($table == "users"){
        $echo_data = getUser($conn);
    }else if($table == "groups"){
        $echo_data = getGroups($conn);
    }else if($table == "registration"){
        $echo_data = getRegistrationRequests($conn);
    }else if($table == "org_table"){
        $echo_data = getOrgDepartments($conn);
    }

    echo $echo_data;
}
function getUser($conn){
    $sql = "SELECT * FROM users";
    $data = "";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            $params_row = array();
            $count_indx = 1;
            while($row = mysqli_fetch_assoc($result)){
                $statusName = array("Inactive", "Active");
                $params_cell = array();
                $params_cell[] =  $count_indx;
                $row_id = $row['id'] ; 
                $params_cell[] =  $row_id; 
                $params_cell[] = $row['username'];
                $params_cell[] = $row["email"]; 
                $params_cell[] = getGroupsNames($conn, $row["groups"]);
                $params_cell[] = getDepName($conn,$row["dep_id"]);
                $params_cell[] = $statusName[(int)$row["status"]]; 
                $btn = "<button type='button' class='btn btn-primary btn-sm' onclick='addUpdateUser(\"update\",\"$row_id\")' >Setting</button>";
                if($row_id != 1){ //Admin user
                    $btn .=  "<button type='button' class='btn btn-danger btn-sm' onclick='delete_user(\"$row_id\")' >Delete</button>"; 
                }
                                   
                $params_cell[] = $btn;
                $params_row[] = $params_cell;
                $count_indx++;
            }
            $data_obj = new stdClass();
            $data_obj->draw = 1;
            $data_obj->data = $params_row;

            $data = json_encode($data_obj);
        }else{
            $data = '{
                "draw": 0,
                "recordsTotal": 0,
                "data": [
                    ]
                }';
        }
    }else {
        $data = '{
            "draw": 0,
            "recordsTotal": 0,
            "data": [
            ]
        }';
    }
    return $data;
}
function getGroups($conn){
    $sql = "SELECT * FROM users_gropes";
    $data = "";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            $params_row = array();
            $count_indx = 1;
            while($row = mysqli_fetch_assoc($result)){
                $params_cell = array();
                $statusName = array("Inactive", "Active");
                $params_cell[] =  $count_indx;
                $row_id = $row['indx'] ; 
                $params_cell[] =  $row_id;
                $params_cell[] = $row['group_name'];
                $params_cell[] = $row["group_status"]; 
                $params_cell[] = $statusName[(int)$row["group_status"]];
                $admins_id = $row["admin_ids"];
                $params_cell[] = getUserNames($conn,$admins_id);
                $btn = "<button type='button' class='btn btn-primary btn-sm' onclick='addUpdateGroup(\"update\",\"$row_id\",\"$admins_id\",this)' >Setting</button>";
                if($row_id != 1 && $row_id != 2){ //1=administrator group, 2=Managers group
                    $btn .= "<button type='button' class='btn btn-danger btn-sm' onclick='delete_group(\"$row_id\")' >Delete</button>";
                }
                $params_cell[] = $btn;
                $params_row[] = $params_cell;
                $count_indx++;
            }
            $data_obj = new stdClass();
            $data_obj->draw = 1;
            $data_obj->data = $params_row;

            $data = json_encode($data_obj);
        }else{
            $data = '{
                "draw": 0,
                "recordsTotal": 0,
                "data": [
                    ]
                }';
        }
    }else {
        $data = '{
            "draw": 0,
            "recordsTotal": 0,
            "data": [
            ]
        }';
    }
    return $data;
    
}
function getUserNames($conn, $userIds){
    if($userIds != ""){
        $userIds_ary = array();
        $userNames_ary = array();
        if(strpos($userIds,",") !== false){
            $userIds_ary = explode(",",$userIds);
        }else{
            $userIds_ary[] = $userIds;
        }
        foreach($userIds_ary as $userId){
            $sql = "SELECT username FROM users WHERE id=$userId";
            if($result = $conn->query($sql)) {
                while($row = mysqli_fetch_assoc($result)){
                    $userNames_ary[] = $row["username"];
                }
            }
        }
        if(!empty($userNames_ary)){
            return implode(",",$userNames_ary);
        }else{
            return "";
        }
    }else{
        return "";
    }

}
function getGroupsNames($conn, $groupsIds){
    if($groupsIds != ""){
        $groupsIds_ary = array();
        $groupsNames_ary = array();
        if(strpos($groupsIds,",") !== false){
            $groupsIds_ary = explode(",",$groupsIds);
        }else{
            $groupsIds_ary[] = $groupsIds;
        }
        foreach($groupsIds_ary as $groupId){
            $sql = "SELECT group_name FROM users_gropes WHERE indx=$groupId";
            if($result = $conn->query($sql)) {
                while($row = mysqli_fetch_assoc($result)){
                    $groupsNames_ary[] = $row["group_name"];
                }
            }
        }
        if(!empty($groupsNames_ary)){
            return implode(",",$groupsNames_ary);
        }else{
            return "";
        }
    }else{
        return "";
    }

}
function getRegistrationRequests($conn){
    $sql = "SELECT * FROM registration_request";
    $data = "";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            $params_row = array();
            $count_indx = 1;
            while($row = mysqli_fetch_assoc($result)){
                $params_cell = array();
                $params_cell[] =  "";//$count_indx;
                $row_id = $row['indx'] ;
                $isConfirmed = $row["is_confirm"];
                $params_cell[] =  $row_id; 
                $params_cell[] = $row['user_name'];
                $params_cell[] = $row["email"]; 
                $params_cell[] = ($isConfirmed=="1")?"yes":"no"; 
                $params_cell[] = "<button type='button' class='btn btn-info btn-sm' onclick='accept_user_request(\"$row_id\",\"$isConfirmed\")'>Accept</button><button type='button' class='btn btn-danger btn-sm' onclick='delete_registration(\"$row_id\",\"$isConfirmed\")' >Delete</button><input type='hidden' class='registration_ids' value='$row_id,$isConfirmed'>"; 
                $params_row[] = $params_cell;
                $count_indx++;
            }
            $data_obj = new stdClass();
            $data_obj->draw = 1;
            $data_obj->data = $params_row;

            $data = json_encode($data_obj);
        }else{
            $data = '{
                "draw": 0,
                "recordsTotal": 0,
                "data": [
                    ]
                }';
        }
    }else {
        $data = '{
            "draw": 0,
            "recordsTotal": 0,
            "data": [
            ]
        }';
    }
    return $data;
    
}
function getOrgDepartments($conn){
    $sql = "SELECT * FROM organization_tree";
    $data = "";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            $params_row = array();
            $count_indx = 1;
            while($row = mysqli_fetch_assoc($result)){
                $params_cell = array();
                $params_cell[] =  $count_indx;
                $dep_id = $row['id'] ;
                $dep_name = $row['name'] ;
                $prnt_id = $row["parent_id"];
                $mngr_id = $row["dep_mngr_user_id"];
                $params_cell[] =  $dep_id;
                $params_cell[] = $dep_name;
                $params_cell[] = $prnt_id; 
                $params_cell[] = getDepName($conn,$prnt_id);
                $params_cell[] = $mngr_id;
                $params_cell[] = getUserNames($conn,$mngr_id);
                //action,parentId,depId,depName,managerId
                $btn = "<button type='button' class='btn btn-primary btn-sm' onclick='newOrUpdateDep(\"update\",\"$prnt_id\",\"$dep_id\",\"$dep_name\",\"$mngr_id\")' >Details</button>";
                if($prnt_id != "0"){
                    $btn .= "<button type='button' class='btn btn-danger btn-sm' onclick='delSingleDepFromOrg(\"$dep_id\")' >Delete</button>";
                }
                $params_cell[] = $btn;
                $params_row[] = $params_cell;
                $count_indx++;
            }
            $data_obj = new stdClass();
            $data_obj->draw = 1;
            $data_obj->data = $params_row;

            $data = json_encode($data_obj);
        }else{
            $data = '{
                "draw": 0,
                "recordsTotal": 0,
                "data": [
                    ]
                }';
        }
    }else {
        $data = '{
            "draw": 0,
            "recordsTotal": 0,
            "data": [
            ]
        }';
    }
    return $data;
    
}
function getDepName($conn,$dep_id){
    if($dep_id == "0"){
        return "";
    }
    $depName = "";
    $sql = "SELECT name FROM organization_tree WHERE id=$dep_id";
    if($result = $conn->query($sql)) {
        while($row = mysqli_fetch_assoc($result)){
            $depName = $row["name"];
        }
    }
    return $depName;
}
if(!isset($echo_data)){
?>
<div class="users_groups_warper">
    <div class="users_groups_column users_groups_tree">
        <h4>Users and groups</h4><br>
        <div id="users_groups_tree_content"></div>
    </div>
    <div class="users_groups_column users_groups_data">
        <div class="users_groups_data_content"  style="width:100%;height:100%">
            <table id="users_groups_data_table" class="display" style="width:100%"></table>
            <div id="org_tree_content"></div>
        </div>

    </div>
</div>
<div id="org_dep_win">
    <div class="org_dep_warper dialog_form_container">
        <input type="hidden" id="parent_dep_id" />
        <input type="hidden" id="orgtree_dep_id" />
        <input type="hidden" id="org_tree_action_type" />
        <div class="row">
            <div class="col-25">
                <label for="dep_name">Dep name: </label>
            </div>
            <div class="col-75">
                <input type="text" id="dep_name" required />
            </div>
        </div>
        <div class="row parent-row">
            <div class="col-25">
                <label for="parent_dep_list">Dep. Parent: </label>
            </div>
            <div class="col-75">
                <select id="parent_dep_list"  style='width:80%;'></select>
            </div>
        </div>
        <div class="row">
            <div class="col-25">
                <label for="dep_manager">Dep manager: </label>
            </div>
            <div class="col-75">
                <select id="dep_manager" style='width:80%;'></select>
            </div>
        </div>
        <hr>
        <div style="width:100%;text-align:center;font-size:20px;"><u>Select Users</u></div>
        <div class="container">
            <div class="row style-select">
                <div class="col-md-12">
                    <div class="select-users-box-1">
                        <label style="width:100%;text-align:center;font-size:15px;">Available users</label>
                        <select multiple="multiple" class="form-control" id="available-users" style="height:100%">
                        </select>
                    </div>

                    <div class="select-users-arrows text-center">
                        <br/><br/><br/>
                        <input type='button' id='btnAllRight' value='>>' class="btn btn-info btn-sm" /><br />
                        <input type='button' id='btnRight' value='>' class="btn btn-info btn-sm" /><br />
                        <input type='button' id='btnLeft' value='<' class="btn btn-info btn-sm" /><br />
                        <input type='button' id='btnAllLeft' value='<<' class="btn btn-info btn-sm" />
                    </div>

                    <div class="select-users-box-2">
                        <label style="width:100%;text-align:center;font-size:15px;">Selected users</label>
                        <select multiple="multiple" class="form-control" id="selected-users" style="height:100%">
                        </select>
                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

</script>
<?php } ?>