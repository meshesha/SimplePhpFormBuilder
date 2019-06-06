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
    }else if("registration"){
        $echo_data = getRegistrationRequests($conn);
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
                $params_cell = array();
                $params_cell[] =  $count_indx;
                $row_id = $row['id'] ; 
                $params_cell[] =  $row_id; 
                $params_cell[] = $row['username'];
                $params_cell[] = $row["email"]; 
                $params_cell[] = $row["groups"];
                $params_cell[] = $row["status"]; 
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
if(!isset($echo_data)){
?>
<div class="users_groups_warper">
    <div class="users_groups_column users_groups_tree">
        <span>Users and groups</span><br>
        <div class="users_groups_tree_content"></div>
    </div>
    <div class="users_groups_column users_groups_data">
        <div class="users_groups_data_content"></div>
        <table id="users_groups_data_table" class="display" style="width:100%"></table>
    </div>
</div>
<script>
    var users_groups_table;
$('.users_groups_tree_content').on('changed.jstree', function (e, data) {
    var slected_id = data.instance.get_node(data.selected[0]).id;
    //$('.users_groups_data_content').html(slected_id)
    
    if (slected_id != "") {
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem("slected_jstree_id", slected_id);
        }
       loadUsersGroupsTable(slected_id); 
    }

  }).jstree({
    "plugins" : [ "changed"],
    'core' : {
        'data' : [{
                "id" : "users",
                "parent" : "#",
                "text" : "Users",
                "icon": "./include/icons/user.png"
            },{
                "id" : "registration",
                "parent" : "#",
                "text" : "Registration request",
                "icon": "./include/icons/add_user.png"
            },{
            "id" : "groups",
            "parent" : "#", 
            "text" : "Groups",
            "icon": "./include/icons/groups.png"
        }]
    } 
});

$('.users_groups_tree_content').on("loaded.jstree", function (e, data) {
    $('.users_groups_tree_content').jstree('select_node', "#users",true);; //on load open users
});

function loadUsersGroupsTable(tbl){
    var tbl_columns_names;
    var colDefs = [{
        "targets": [0,-1],
        "searchable": false,
        "orderable": false
    }];
    var btns = [];
    if(tbl == "users"){
        tbl_columns_names = [
            { 
                "title": "#",
                "index": 0
            },
            { 
                "title": "User id",
                "index": 1
            },
            { 
                "title": "User name",
                "index": 3
            },
            { 
                "title": "Email",
                "index": 4
            },
            { 
                "title": "Groups",
                "index": 5
            },
            { 
                "title": "Status",
                "index": 6
            },
            { 
                "title": '',
                "index": 7
            }
        ];
        btns = [{ 
                text: 'Add user',
                className: 'btn btn-primary btn-lg',
                action: function (e, dt, node, config) {
                    addUpdateUser("new","");
                }
            }];
    }else if(tbl == "groups"){
        tbl_columns_names = [
            { 
                "title": "#",
                "index": 0
            },
            { 
                "title": "Group id",
                "index": 1
            },
            { 
                "title": "Group name",
                "index": 2
            },
            { 
                "title": "Status id",
                "index": 3
            },
            { 
                "title": "Status title",
                "index": 4
            },
            { 
                "title": "Group Managers",
                "index": 5
            },
            { 
                "title": '',
                "index": 6
            }
        ];
        btns = [{ 
            text: 'Add group',
            className: 'btn btn-primary btn-lg',
            action: function (e, dt, node, config) {
                addUpdateGroup("new","","",null)
            }
        }];
    }else if(tbl == "registration"){
        tbl_columns_names = [
            { 
                "title": "#",
                "index": 0
            },
            { 
                "title": "User id",
                "index": 1
            },
            { 
                "title": "User name",
                "index": 2
            },
            { 
                "title": "Email",
                "index": 3
            },
            { 
                "title": 'Is verified?',
                "index": 4
            },
            { 
                "title": '',
                "index": 5
            }
        ];
        colDefs.push({
            "targets": [0],
            "render": function(data, type, row, meta){
                /**https://www.gyrocode.com/projects/jquery-datatables-checkboxes/ */
                if(type === 'display'){
                    data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes usr_row_checkbox"><label></label></div>';
                }
                return data;
            },
            'checkboxes': {
                'selectRow': false,
                'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
            }
        });
        btns = [{ 
            text: 'Delete all select',
            className: 'btn btn-danger btn-sm',
            action: function (e, dt, node, config) {
                var arr = [];
                $('.usr_row_checkbox:checked').each(function (val, i) {
                    arr.push($(this).parents('tr').find($(".registration_ids")).val());
                }); 
                //console.log(arr)
                if(arr.length == 0){
                    alert("No selection!")
                }else{
                    if(confirm("Are you sure you want to delete all selected records?")){
                        var frm_data = {
                            delType: "multi",
                            data: JSON.stringify(arr)
                        }

                        ajaxAction("delete", "user_reg_request" , frm_data);
                    }
                }
            }
        },{
            text: 'Accept all select',
            className: 'btn btn-info btn-sm',
            action: function (e, dt, node, config) {
                var arr = [];
                var isUnverifiedUsers = 0;
                $('.usr_row_checkbox:checked').each(function (val, i) {
                    var sel = $(this).parents('tr').find($(".registration_ids")).val();
                    var selAry = sel.split(",");
                    if(selAry[1] == "1"){
                        arr.push(selAry[0]);
                    }else{
                        isUnverifiedUsers++;
                    }
                }); 
                //console.log(arr)
                if(arr.length == 0){
                    alert("No selected or unverified users!")
                }else{
                    if(isUnverifiedUsers > 0){
                        alert("Note that unverified users will not be accepted");
                    }
                    var frm_data = {
                        type: "multi",
                        data: JSON.stringify(arr)
                    }
                    ajaxAction("new", "user_reg_request" , frm_data);
                }
            }
        }]
    }
    if($('#users_groups_data_table').html() != "" ){
        $('#users_groups_data_table').DataTable().destroy();
        $('#users_groups_data_table').empty();
    }
    
    users_groups_table = $('#users_groups_data_table').DataTable({
        language: {
            url: './include/DataTables/i18n/Hebrew.json'
        },
        ajax: {
            url: "users_and_groups.php",
            type: "POST",
            data: {
                table : tbl
            }
        },
        destroy: true,
        columns: tbl_columns_names,
        scrollResize: true,
        scrollY: 100,/*getTblContainerHeight() + "px",*/
        scrollX: true,
        paging: false,
        info: false,
        scrollCollapse: true,
        /*searching: false,*/
        columnDefs: colDefs,
        order: [
            [ 1, 'asc' ]
        ],
        dom: 'Bfrtip',
        buttons: btns,
        initComplete: function() {
            $('.dt-button').removeClass("dt-button");
            fixTableHeadScroll();
        },
        rowCallback: function(row, data, index) {
            //
        },
        drawCallback: function() {
            //
        }
    });
    /*
    if(tbl != "registration"){
        console.log(tbl)
        $('#users_groups_data_table').DataTable().on( 'order.dt search.dt', function(){
            $('#users_groups_data_table').DataTable().column(0, {search:'applied', order:'applied'}).nodes().each(function(cell, i){
                cell.innerHTML = i+1;
            });
        }).draw();
    }
    */
}
///////////////////Users///////////////////////////

function add_update_user(dialogBox){
    var action = $("#action_type").val(); //new,updte
    if(action == "update"){
        if(!confirm("Are you sure you want to update?")){
            return false;
        }
    }
    var usr_id = $("#user_id").val();
    var usr_name = $("#user_name").val();
    var usr_pass = $("#user_password").val();
    var conf_pass = $("#confirm_password").val();
    if(usr_pass != "" && usr_pass != conf_pass){
        alert("confirm password - Passwords Don't Match");
        return false;
    }
    var usr_email = $("#user_email").val();
    var usr_groups = $("#groupList").val();
    var usr_pblsh_stt = $("#user_status").val();
    var usr_data  = {
        record_id: usr_id,
        user_name: usr_name,
        user_pass: usr_pass,
        user_email: usr_email,
        publish_groups: usr_groups,
        user_status: usr_pblsh_stt
    };
    var tbl = "users";
    //console.log(usr_data)
    ajaxAction(action, tbl, usr_data, dialogBox);
}
function delete_user(user_id){
    if(confirm("Are you sure you want to delete this user?")){
        var frm_data = {
            record_id: user_id
        }
        ajaxAction("delete", "users" , frm_data);
    }
}
function delete_registration(idx,isConfirmed){
    var isVerifiedStr = "";
    if(isConfirmed == "1"){
        isVerifiedStr = "verified";
    }else{
        isVerifiedStr = "unverified";
    }
    if(confirm("Are you sure you want to delete this " + isVerifiedStr + " user request?")){
        var frm_data = {
            record_id: idx,
            delType: "single"
        }
    }
    ajaxAction("delete", "user_reg_request" , frm_data);
}
function accept_user_request(idx,isConfirmed){
    if(isConfirmed == "1"){
        var frm_data = {
            record_id: idx,
            type: "single"
        }
        ajaxAction("new", "user_reg_request" , frm_data);
    }else{
        alert("The user not verified");
    }
}
//////////Groups///////////////////////////////////////////////
function addUpdateGroup(action,grp_id,admins_id,obj){
    var dialogTitle = "Add new group";
    var gContent = $("#formbuilder_general_content");
    gContent.addClass("dialog_form_container");
    gContent.html("");
    var hInput = "<input type='hidden' id='grp_action_type' />";
    $(hInput).val(action).appendTo(gContent);
    var uhInput = "<input type='hidden' id='group_id' value = '" + grp_id + "' />";
    $(uhInput).appendTo(gContent);
    var group_data = "", groupName, groupStatus;
    if(action == "update"){
        dialogTitle = "Update group data";
        group_data = users_groups_table.row( $(obj).parents('tr') ).data();;
    }
    if(group_data !== "" && group_data !== null && group_data !== undefined){
        //console.log(mor_data.data)
        groupName =  group_data[2];
        groupStatus = group_data[3];
    }else{
        groupName =  "";
        groupStatus = "";
    }

    var uInput = "<input type='text' id='group_name' value = '" + groupName +"' />";
    var uName = addElement("Group Name","group_name", uInput);
    uName.appendTo(gContent);
    
    //////////////////////////////
    var gInput = "<select id='userMngrList' class='managerlist js-states form-control' multiple='multiple' style='width:80%;'></select>";
    var uGroups = addElement("Managers","userMngrList", gInput);
    uGroups.appendTo(gContent);
    setUsersManagerList(admins_id);
    /////////////////////////////////////////////

    if(action == "update"){
        var sInput = "<select id='group_status'><option value='0'>Inactive</option><option value='1'>Active</option></select>";
        var uStatus = addElement("Status","group_status", sInput);
        uStatus.appendTo(gContent);
        $("#group_status").val(groupStatus).change();
    }else{
        var usInput = "<input type='hidden' id='group_status' />";
        $(usInput).val("0").appendTo(gContent);
    }

    general_dialog.dialog("option","buttons",
        [
            {
                text: "Cancel",
                class: "btn btn-primary btn-lg",
                click: function() {
                    $( this ).dialog( "close" );
                }
            },
            {
                text: "Save",
                class: "btn btn-primary btn-lg",
                click: function() {
                    set_group(general_dialog);
                }
            }
        ]
    );
    general_dialog.dialog("option","height",0.5*$(window).height());
    general_dialog.dialog("option","title",dialogTitle);
    general_dialog.dialog("open");
}
function set_group(dialogBox){
    var action = $("#grp_action_type").val(); //new,updte
    if(action == "update"){
        if(!confirm("Are you sure you want to update?")){
            return false;
        }
    }
    var grp_id = $("#group_id").val();
    var grp_name = $("#group_name").val();
    var grp_stt = $("#group_status").val();
    var usr_mngers = $("#userMngrList").val();
    var grp_data  = {
        record_id: grp_id,
        group_name: grp_name,
        users_managers: usr_mngers,
        group_status: grp_stt
    };
    var tbl = "groups";
    ajaxAction(action, tbl , grp_data, dialogBox);
}
function delete_group(grop_id){
    if(confirm("Are you sure you want to delete this group?")){
        var frm_data = {
            record_id: grop_id
        }
        ajaxAction("delete", "groups" , frm_data);
    }
}

</script>
<?php } ?>