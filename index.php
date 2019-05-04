<?php

require 'settings/tracy-2.6.2/src/tracy.php';
use Tracy\Debugger;


session_start();
$_SESSION['rederect_url'] = "main_page";

//check if config.inc.php exisit
if(!file_exists("settings/config.inc.php")){
    if(file_exists("install/start.php")){
        header("Location: install/start.php");
    }else{
        die("Installation files not exists");
    }
}else{
    include "settings/config.inc.php";
    if(true){

    }
}
require 'settings/database.login.php';

$isAdmin = false;
$message = "";
$isUserFormAdmin = "";
if( isset($_SESSION['user_id']) ){

	$records = $conn->prepare('SELECT id,username,password,groups FROM users WHERE status="1" AND id = :id');
	$records->bindParam(':id', $_SESSION['user_id']);
	$records->execute();
	$results = $records->fetch(PDO::FETCH_ASSOC);

	$user = "";

	if( count($results) > 0){
		$myuser = $results["id"];
        $groups = $results["groups"];
        $groups_ary = array();
        $adminId = getAdminGroupId($conn);
        if($adminId != ""){
            if(strpos($groups,",") !== false){
                $groups_ary = explode(",",$groups);
            }else{
                $groups_ary[] = $groups;
            }
            if(in_array($adminId, $groups_ary)){
		        $user = $myuser;
                $isAdmin = true;
            }else{
                //check if exist forms that this user allowed to eccess
                $isUserFormAdmin = getFormsIds($conn, $_SESSION['user_id']);
                if($isUserFormAdmin != ""){
		            $user = $myuser;
                }else{                    
                    $message = "<label class='text-danger'>Sorry, You don't have a system Administrator credentials or form manger credentials</label>";
                }
            }
        }else{
            $message = "<label class='text-danger'>Sorry, No Administrator group found</label>";
        }
	}else{
        $message = "<label class='text-danger'>Sorry,  Username does not exist or is suspended</label>";
    }

}
function getAdminGroupId($conn){
    $adminGroupName = "administrator";
	$records = $conn->prepare('SELECT indx FROM users_gropes WHERE group_name = :group');
	$records->bindParam(':group', $adminGroupName);
	$records->execute();
	$results = $records->fetch(PDO::FETCH_ASSOC);

    $adminGrpId = "";

	if($results != "" && count($results) > 0){
        $adminGrpId = $results["indx"];
    }
    return $adminGrpId;
}
function getFormsIds($conn, $userId){
	$records = $conn->prepare('SELECT indx,admin_users FROM form_list');
	$records->execute();
	$results = $records->fetchAll(PDO::FETCH_ASSOC);

    $formIds = array();

	if($results != "" && count($results) > 0){
        foreach($results as $row) {
            //echo $row["indx"]." - ".$row["admin_users"]."<br>";
            $adminGrpIds = $row["admin_users"];
            if($adminGrpIds != ""){
                $adminGroupsAry = array();
                if(strpos($adminGrpIds,",") !== false){
                    $adminGroupsAry = explode(",",$adminGrpIds);
                }else{
                    $adminGroupsAry[] = $adminGrpIds;
                }

                if(in_array($userId, $adminGroupsAry)){
                    $formIds[] = $row["indx"];
                }
            }
        }
    }
    //echo "Forms: ". implode(",",$formIds);
    if(empty($formIds)){
        return "";
    }else{
        return implode(",",$formIds);
    }
}
////////get settings ///////
if(!isset($isGetSetting)){
    require 'get_setting_data.php';
}
$appMode = getSetting("", "appMode");
if($appMode == "0"){
    //Debug mode
    Debugger::enable();
}

$editForm = getSetting("", "enableFormManagersToEditFormTamplate");
$isEditForm = false;
if(!empty($user) && ($isAdmin || $editForm == "1")){
    $isEditForm = true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form builder</title>

    <link rel="stylesheet" type="text/css" href="./css/index_main.css">
    <link rel="stylesheet" href="./include/fonts/fontawesome/css/fontawesome-all.min.css">

    <link rel="stylesheet" href="./include/bootstrap/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="./include/jquery_ui/themes/start/jquery-ui.min.css">
    
    <link rel="stylesheet" href="./include/select2/dist/css/select2.min.css">

    <script src="./include/jquery/jquery-1.12.4.min.js"></script>
    <!--<script src="./include/jquery/jquery-3.3.1.min.js"></script>
    <script src="./include/jquery/jquery-migrate-1.4.1.min.js"></script>-->
    <script src="./include/jquery_ui/jquery-ui.min.js"></script>

    <!--///////////// For Internet Explorer 11 polyfill ///////////////-->
    <script type="text/javascript">
    if(/MSIE \d|Trident.*rv:/.test(navigator.userAgent))
        document.write('<script src="./include/formbuilder/polyfill-4ie11.js"><\/script>');
    </script>
    <!--///////////////////////////////////////////////////////-->
    <script src="./include/formbuilder/form-builder.min.js"></script>
    <script src="./include/formbuilder/form-render.min.js"></script>

    <script src="./include/jQueryPopMenu/src/jquery.popmenu.js"></script>

    <!--DataTable-->
    <link rel="stylesheet" href="./include/DataTables/datatables.min.css">
    <link rel="stylesheet" href="./include/DataTables/Styling/css/dataTables.jqueryui.min.css">
    <link rel="stylesheet" href="./include/DataTables/Buttons-1.5.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="./include/DataTables/Buttons-1.5.1/css/buttons.jqueryui.min.css">
    <link rel="stylesheet" href="./include/DataTables/checkboxes-1.2.11/css/dataTables.checkboxes.css">
    <script type="text/javascript" src="./include/DataTables/datatables.min.js"></script>
    <script type="text/javascript" src="./include/DataTables/Styling/js/dataTables.jqueryui.min.js"></script>
    <script type="text/javascript" src="./include/DataTables/Buttons-1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="./include/DataTables/Buttons-1.5.1/js/buttons.jqueryui.min.js"></script>
    <script type="text/javascript" src="./include/DataTables/checkboxes-1.2.11/js/dataTables.checkboxes.min.js"></script>
    <script type="text/javascript" src="./include/DataTables/dataTables.scrollResize.min.js"></script>


    <script type="text/javascript" src="./include/select2/dist/js/select2.min.js"></script>
        
    <link rel="stylesheet" href="./include/jstree/themes/default/style.min.css" />
    <script src="./include/jstree/jstree.min.js"></script>

	<?php if( !empty($user)): ?>
    <script type="text/javascript">
        var formbuilder_dialog,formbuilder_content_dialog,general_dialog;
        $(function () {
             formbuilder_dialog = $("#formbuilder_form").dialog({
                modal: true,
                autoOpen: false,
                width: 0.7*$(window).width(),
                height: 0.8*$(window).height(),
                buttons: [
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
                        setFormbuilderData(formbuilder_dialog);
                        }
                    }

                ]
            });
            formbuilder_content_dialog = $("#formbuilder_content").dialog({
                modal: true,
                autoOpen: false,
                width: 0.9*$(window).width(),
                height: 0.9*$(window).height()/*,
                buttons: [
                    {
                    text: "Cancel",
                    class: "btn btn-primary btn-lg",
                    click: function() {
                        $( this ).dialog( "close" );
                        }
                    }

                ]*/
            });
            general_dialog = $("#formbuilder_general_dialog").dialog({
                modal: true,
                autoOpen: false,
                width: 0.5*$(window).width(),
                height: 0.5*$(window).height()
            });
            
            var index = 'qpsstats-active-tab';
            //  Define friendly data store name
            var dataStore = window.sessionStorage;
            var oldIndex = 0;
            //  Start magic!
            try {
                // getter: Fetch previous value
                oldIndex = dataStore.getItem(index);
            } catch(e) {}

            $("#customer_tabs").tabs({
                active: oldIndex,
                activate: function(event, ui) {
                    //  Get future value
                    var newIndex = ui.newTab.parent().children().index(ui.newTab);
                    //  Set future value
                    try {
                        dataStore.setItem( index, newIndex );
                    } catch(e) {}
                    fixTableHeadScroll();
                }
            });

            $("#main-vewer-menu").popmenu({
                'width': '100px',         // width of menu
                'top': '0',              // pixels that move up
                'left': '0',              // pixels that move left
                'iconSize': '50px' // size of menu's buttons
            });
            $("#publish_type").on("click",function(){
                //alert($(this).val())
                if($(this).val() == "1"){ //if publick
                    $("#groups-row").hide();
                }else{
                     $("#groups-row").show();
                }
            })
            $("#status_type").on("click",function(){
                var formId = $("#form_id").val();
                if(formId == ""){
                    return false;
                }
                if($(this).val() == "1"){ //Publisheds
                    var formContent = get_form_content(formId);
                    var emptyContent = '[{"type":"hidden","name":"hidden-form-id","id":"hidden-form-id","value":"'+formId+'"},{"type":"button","subtype":"submit","label":"Submit","className":"btn-primary btn","name":"button-submit-form","id":"button-submit-form","style":"primary"}]';
                    //console.log("formId: ",formId,", formContent:",(formContent == emptyContent)?"empty":"not empty",formContent)
                    if(formContent == emptyContent || formContent === undefined || formContent=="" || formContent=="new" || formContent === null){
                        alert("You can't to publish this form because it has no form template.")
                        $("#status_type").val("2").change();
                        $("#form_links").hide();
                    }else{
                        //show links
                        var webformlink = "form.php?id=" + formId;
                        var adminformlink = "formadmin.php?id=" + formId;
                        $("#web_form_link").attr("href", webformlink);
                        $("#admin_web_form_link").attr("href", adminformlink);
                        $("#form_links").show();
                        $("#form_preview").prop("disabled",true);
                        $(".form_preview_worn").html("<span style='color:red'>You can not edit form template when form status is 'Published' mode</span>")
                    }
                }else if($(this).val() == "2"){
                    $("#form_links").hide();
                    $("#form_preview").prop("disabled",true);
                    $(".form_preview_worn").html("<span style='color:red'>First save the settings and then reopen for edit the template")
                    
                }

            })
    });
    function ajaxAction(action_type, tbl , data, dialogbox){
        var url = "set_data.php";
        var data_obj = {
            table: tbl,
            action: action_type,
            data: data
        };

        if(action_type != "" && url != ""){
            //ajax
            $.ajax({
                type: "POST",
                url: url,
                data: {data : JSON.stringify(data_obj)},
                success: function (response) {
                    console.log(response);
                    if(response == "success"){
                        if (dialogbox !== undefined && dialogbox.hasClass('ui-dialog-content')){
                            dialogbox.dialog("close");
                        }
                        if(tbl == "form"){
                            load_form_list();
                        }else{
                            var selctedTbl = localStorage.getItem('slected_jstree_id');
                            if(selctedTbl != null && selctedTbl != ""){
                                loadUsersGroupsTable(selctedTbl);
                            }
                        }
                    }
                },
                error:function (response) {
                    console.log("Error:",JSON.stringify(response));
                    alert(response.responseText)
                }
            });
        }
    }
    //fix tables header
    function fixTableHeadScroll(){
        $($.fn.dataTable.tables(true)).DataTable()
        .columns.adjust();
            
    }
    </script>
</head>
<body>

    <div id="main_warper"  style="background-image: url('images/bg05.jpg');" >
        <div class="icons_toolbar ui-widget-header">
            <span id="main-vewer-menu">
                <span class="pop_ctrl"><i class="all_btns fa fa-bars"></i></span>
                <ul>
                    <li onclick="addUpdateUser('update','<?=$user ?>','true')" title="User info"><div><i class="fa fa-user"></i></div><div class="menu-icons-text">info</div></li>
                    <li onclick="avascript:location.href='logout.php'" title="Exit from system"><i class="fa fa-power-off"></i><div class="menu-icons-text">Logout</div></li>
                </ul>
            </span>
        </div>
        <div id="viewer_container" class="ui-widget-content">
            <div id="customer_tabs">
                <ul>
                    <li><a href="#form_list_tab">Form list</a></li>
                    <?php if($isAdmin): ?>
                    <li><a href="#users_tab">users & groups</a></li>
                    <li><a href="#settings_tab">Settings</a></li>
                    <?php endif; ?>
                </ul>
                <div id="form_list_tab" style="overflow:auto; height:calc(100vh - 1px);">
                    <!--
                    <div class="btn-toolbar justify-content-between ui-widget-content" role="toolbar" style="padding:5px; border-radius:5px">
                        <button type="button" class="btn btn-primary btn-lg" onclick="addNew()">New</button>
                    </div>
                    -->
                    <div id="form_list_content" class="ui-widget-content">
                        <table id="form_list_content_table" class="display" style="width:100%"></table>
                    </div>
                </div>
                 <?php if($isAdmin): ?>
                <div id="users_tab"  style="overflow:auto; height: 90%;">
                    <!--
                    <div class="btn-toolbar justify-content-between ui-widget-content" role="toolbar" style="padding:5px; border-radius:5px">
                        <button type="button" class="btn btn-primary btn-lg" onclick="addNewUser()">New</button>
                    </div>
                    -->
                    <?php include "users_and_groups.php"; ?>
                </div>
                <div id="settings_tab" style="overflow:auto; height: 100%;">
                    <?php include "settings_section.php"; ?>
                    <!--<div id="settings_content" class="ui-widget-content"></div>-->
                </div>
                 <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div id="formbuilder_form">
        <div class="dialog_form_container">
            <form id="new_file_form" method="POST" enctype="multipart/form-data" accept-charset="UTF-8">
                <input type="hidden" id="form_action" />
                <input type="hidden" id="form_id" />
                <div class="row">
                    <div class="col-25">
                        <label for="form_name">Form name</label>
                    </div>
                    <div class="col-75">
                        <input type="text" id="form_name" name="form_name" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-25">
                        <label for="form_title">Form title</label>
                    </div>
                    <div class="col-75">
                        <input type="text" id="form_title" name="form_title" >
                    </div>
                </div>
                <div class="row">
                    <div class="col-25">
                        <label for="publish_type">Publish type</label>
                    </div>
                    <div class="col-75">
                        <select id="publish_type" name="publish_type">
                            <option value="1">Public</option>
                            <option value="2">Users group</option>
                        </select>
                    </div>
                </div>
                <div class="row" id="groups-row">
                    <div class="col-25">
                        <label for="groups_list">Groups</label>
                    </div>
                    <div class="col-75">
                        <select id="groups_list" name="groups_list" class="groupslist js-states form-control" multiple="multiple" style="width: 80%"></select>
                    </div>
                </div>
                <div class="row" id="managers-row">
                    <div class="col-25">
                        <label for="form_managers_list">Form Managers</label>
                    </div>
                    <div class="col-75">
                        <select id="form_managers_list" name="form_managers_list" class="managerlist js-states form-control" multiple="multiple" style="width: 80%"></select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-25">
                        <label for="status_type">Status</label>
                    </div>
                    <div class="col-75">
                        <select id="status_type" name="status_type">
                            <option value="1">Published</option>
                            <option value="2">Unpublished</option>
                        </select>
                    </div>
                </div>
                <div class="row" id="form_links" style="display:none">
                    <div class="col-25">
                        <label for="status_type"></label>
                    </div>
                    <div class="col-75">
                        <table class="table table-bordered table-sm" style="width:60%">
                            <tr>
                                <td>Web form</td>
                                <td><a id="web_form_link" href="#" target="_blank">link</a></td>
                            </tr>
                            <tr>
                                <td>Admin web form</td>
                                <td><a id="admin_web_form_link" href="#" target="_blank">link</a></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-25">
                        <label for="form_note">Note</label>
                    </div>
                    <div class="col-75">
                        <textarea id="form_note" name="form_note" style="height:100px"></textarea>
                    </div>
                </div>
                <?php if($isEditForm): ?>
                <div class="row" id="previw_btn_toolbar">
                    <div class="col-25">
                        <label for="form_preview">Form Template</label>
                    </div>
                    <div class="col-75">
                        <button type="button" id="form_preview" class="btn btn-primary btn-lg" onclick="edit_form_file()">Edit</button><p class="form_preview_worn"></p>
                    </div>
                </div>
                <?php endif;?>
            </form>
        </div>
    </div>

    
    <div id="formbuilder_content" title="Form builder">
        <input type="hidden" id="form_content_id" />
        <input type="hidden" id="form_content_status" />
        <div id="builder-editor"></div>
    </div>

    <div id="formbuilder_general_dialog">
        <div id="formbuilder_general_content"></div>
    </div>

    <script>
        var form_list_dat;
        var formContentJsonObj = "";
        var $multiSelectGroups;
        load_form_list();
        function load_form_list(){
            var form_list = "<?=$isUserFormAdmin ?>";
            var userId = "<?=$user ?>";
            var tbl_columns_names = [
                { 
                    "title": "#",
                    "index": 0
                },
                { 
                    "title": "Form id",
                    "index": 1
                },
                { 
                    "title": "Form name",
                    "index": 3
                },
                { 
                    "title": "Form Title",
                    "index": 4
                },
                { 
                    "title": "Publish type",
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
            form_list_dat = $('#form_list_content_table').DataTable({
                language: {
                    url: './include/DataTables/i18n/Hebrew.json'
                },
                ajax: {
                    url: "get_form_list_table.php",
                    type: "POST",
                    data: {
                        data_indx : form_list,
                        user_id: userId
                    }
                },
                destroy: true,
                columns: tbl_columns_names,
                scrollResize: true,
                scrollY: 100,
                scrollX: true,
                paging: false,
                info: false,
                scrollCollapse: false,
                searching: true,
                columnDefs: [
                    {
                        "targets": [0,-1],
                        "searchable": false,
                        "orderable": false
                    }
                ],
                order: [
                    [ 1, 'asc' ]/*,
                    [ 4, 'asc' ]*/
                ],
                <?php if($isAdmin): ?>
                dom: 'Bfrtip',
                buttons: [
                    { 
                        text: 'New form',
                        className: 'btn btn-primary btn-lg',
                        action: function (e, dt, node, config) {
                            addNew();
                        }
                    }
                ],
                <?php endif; ?>
                initComplete: function() {
                    $('.dt-button').removeClass("dt-button");
                },
                rowCallback: function(row, data, index) {
                    //
                },
                drawCallback: function() {
                   //
                }
            });
            form_list_dat.on( 'order.dt search.dt', function(){
                form_list_dat.column(0, {search:'applied', order:'applied'}).nodes().each(function(cell, i){
                    cell.innerHTML = i+1;
                });
            }).draw();
        }
        <?php if($isAdmin): ?>
        function addNew(){
            //clear inputs of #formbuilder_form
            $("#form_action").val("new");
            $("#form_id").val("");
            $("#form_name").val("");
            $("#form_title").val("");
            $("#publish_type").val("1").change();
            $('#groups_list').val("");
            $('#form_managers_list').val("");
            $("#form_note").val("");
            $("#status_type").val("2").change();
            $("#status_type").prop("disabled",true);
            $("#previw_btn_toolbar").hide();
            formbuilder_dialog.dialog("option","title","Add New");
            if($("#publish_type").val() == "1"){ //if public
                $("#groups-row").hide();
            }
            //groups_list
            setGroupsList();
            //manager_list
            setUsersManagerList();
            $("#form_links").hide();
            formbuilder_dialog.dialog("open");
        }
        <?php endif; ?>
        function setGroupsList(selectedAry,readonly){
            var isDisabled = false;
            if(readonly !== undefined && readonly == "true"){
                isDisabled = true;
            }
            $('.groupslist').select2({
                disabled: isDisabled,
                ajax: {
                    url: 'get_all_groups.php',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        console.log( params.term)
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.results
                        };
                    },
                    cache: false
                }
            });
            if(selectedAry === undefined || selectedAry == ""){
                return;
            }
            $multiSelectGroups = $('.groupslist');
            $multiSelectGroups.val(null).trigger('change');
            $.ajax({
                type: 'POST',
                url: 'get_all_groups.php'
            }).then(function (data) {
                //console.log(selectedAry)
                var selectObj = JSON.parse(data);
                var selectObjAry = selectObj.results;
                $.each(selectObjAry, function(i,val){
                    if(selectedAry.indexOf(val.id) != -1){
                        var option = new Option(val.text,val.id, true, true);
                        $multiSelectGroups.append(option).trigger('change');
                    }
                });
                // manually trigger the 'select2:select' event
                $multiSelectGroups.trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            });
        }
        
        function setUsersManagerList(selectedStr,readonly){
            var isDisabled = false;
            if(readonly !== undefined && readonly == "true"){
                isDisabled = true;
            }
            $('.managerlist').select2({
                disabled: isDisabled,
                ajax: {
                    url: 'get_all_managers_users.php',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function (response) {
                        return {
                            results: response.results
                        };
                    },
                    cache: false
                }
            });
            if(selectedStr === undefined || selectedStr === null || selectedStr == ""){
                return "";
            }
            var multiSelectGroups = $('.managerlist');
            multiSelectGroups.val(null).trigger('change');
            $.ajax({
                type: 'POST',
                url: 'get_all_managers_users.php'
            }).then(function (data) {
                //console.log(selectedStr)
                var selectObj = JSON.parse(data);
                var selectObjAry = selectObj.results;
                var selectedAry = [];
                if(selectedStr.indexOf(",") != -1){
                    selectedAry = selectedStr.split(",");
                }else{
                    selectedAry.push(selectedStr);
                }
                //console.log(selectedAry)
                $.each(selectObjAry, function(i,val){
                    if(selectedAry.indexOf(val.id) != -1){
                        var option = new Option(val.text,val.id, true, true);
                        multiSelectGroups.append(option).trigger('change');
                    }
                });
                // manually trigger the 'select2:select' event
                multiSelectGroups.trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            });
        }
        function update_form(obj){
            $("#form_action").val("update");
            if(form_list_dat =="" || form_list_dat === null || form_list_dat=== undefined){
                alert("Error on Form table list");
                return false;
            }
            formbuilder_dialog.dialog("option","title","Update");
            var data = form_list_dat.row( $(obj).parents('tr') ).data();
            $("#form_id").val(data[1]);
            $("#form_name").val(data[2]);
            $("#form_title").val(data[3]);
            var mor_data = getFormData(data[1]);
            if(mor_data !== null && mor_data !== undefined){
                if(mor_data.status == 1){
                    //console.log(mor_data.data)
                    // publ_type: "2", publ_grps: "1,2,3", publ_status: "2", frm_note
                    $("#publish_type").val(mor_data.data.publ_type).change();
                    if($("#publish_type").val() == "1"){ //if public
                        $("#groups-row").hide();
                    }else{
                        $("#groups-row").show();
                        setGroupsList(mor_data.data.publ_grps);
                         //console.log(defaultData)
                    }
                    setUsersManagerList(mor_data.data.admin_users);
                    $("#status_type").prop("disabled",false);
                    $("#status_type").val(mor_data.data.publ_status).change();
                    if(mor_data.data.publ_status == "1"){
                        var webformlink = "form.php?id=" + data[1];
                        var adminformlink = "formadmin.php?id=" + data[1];
                        $("#web_form_link").attr("href", webformlink);
                        $("#admin_web_form_link").attr("href", adminformlink);
                        $("#form_links").show();
                        $("#form_preview").prop("disabled",true);
                        $(".form_preview_worn").html("<span style='color:red'>You can not edit form template when form status is 'Published' mode</span>")
                    }else{
                        $("#form_links").hide();
                        $("#form_preview").prop("disabled",false);
                        $(".form_preview_worn").html("");
                    }
                    $("#form_note").val(mor_data.data.frm_note);
                    $("#previw_btn_toolbar").show();
                    formbuilder_dialog.dialog("open");
                }
            }
        }

        function delete_form(form_id){
            if(confirm("Are you sure you want to delete this form?")){
                var frm_data = {
                    record_id: form_id
                }
                ajaxAction("delete", "form" , frm_data);
            }
        }
         <?php if($isEditForm): ?>
        function edit_form_file(){
            $("#builder-editor").html("");
            var frm_id = $("#form_id").val();
            $("#form_content_id").val(frm_id);
            var form_content = get_form_content(frm_id);
            var $fbEditor = $(document.getElementById('builder-editor'));
            var formBuilder;
            var positionOptions = {
                class: {
                    label: 'Position',
                    class: "frm-position",
                    multiple: false, // optional, omitting generates normal <select>
                    options: {
                        '':'',
                        'form-control-element-right': 'Right',
                        'form-control-element-left': 'Left',
                        'form-control-element-center': 'Center'
                    }/*,
                    onchange: 'console.log(this)'*/
                }
            };
            var maxFileSize = {
                fileSize: {
                    label: 'File max size',
                    type: 'number',
                    min: '0',
                    value: '1024'
                },
                sizeUnits: {
                    label: 'File size units',
                    type: 'select',
                    options: {
                        'bytes':'Bytes',
                        'kB':'Kilobyte (kB)',
                        'MB':'Megabyte (MB)'
                    }
                }
            };
            
            var options = {
                controlPosition: 'left',
                disabledActionButtons: ['data'],
                formData: (form_content=="new")?"":form_content,
                dataType: 'json',
                typeUserAttrs: {
                    header: positionOptions,
                    file: maxFileSize
                },
                disableFields: ['autocomplete','hidden','button'],
                controlOrder: [
                    'header',
                    'text',
                    'textarea'
                ],
                disabledAttrs: [
                    'access'
                ],
                disabledSubtypes: {
                    file: ['fineuploader']
                },
                stickyControls: {
                    enable: true
                },
                scrollToFieldOnAdd: true,
                typeUserEvents: {
                    header: {
                        onadd: function(fld) {
                            var orginVal;
                            $('.frm-position', fld).on('focus', function () {
                                orginVal = this.value;
                            }).change(function(e) {
                                var calssVal = $(".fld-className",fld).val();
                                if(calssVal.indexOf(" ") > -1){
                                    var calssAry = calssVal.split(" ");
                                    if(calssAry.indexOf(orginVal) > -1){
                                        calssAry[calssAry.indexOf(orginVal)] = e.target.value;
                                        var newclass = calssAry.join(" ");
                                        $(".fld-className",fld).val(newclass)
                                    }else{
                                        calssAry.push(e.target.value);
                                        var newclass = calssAry.join(" ");
                                        $(".fld-className",fld).val(newclass)
                                    }
                                }else{
                                    $(".fld-className",fld).val(e.target.value)
                                }
                            });
                        }
                    }
                },
                actionButtons: [{
                    id: 'preview_form',
                    className: 'btn btn-success',
                    label: 'Preview',
                    type: 'button',
                    events: {
                        click: function() {
                            var data = formBuilder.actions.getData('json', true);
                    formBuilder.actions.removeField("button-submit-form");
                            showPreview(data);
                        }
                    }
                },{
                    id: 'close_form',
                    className: 'btn btn-danger',
                    label: 'Close',
                    type: 'button',
                    events: {
                        click: function() {
                            if(confirm("Are you sure you want to close form editor?")){
                                formbuilder_content_dialog.dialog("close");
                            }
                        }
                    }
                }],
                onSave: function (e,formData) {
                    //console.log(formData);x
                    formContentJsonObj = formData;
                    setFormJsonObj(formbuilder_content_dialog);
                }
            };
            if(form_content == "new"){
                $("#form_content_status").val("new");
               formBuilder = $fbEditor.formBuilder(options);
            }else{
                $("#form_content_status").val("");
                formBuilder = $fbEditor.formBuilder(options);
            }
            //formContentJsonObj = "";
            
            formbuilder_content_dialog.dialog("open")
        }

        function showPreview(formData) {
            console.log(formData)
            let formRenderOpts = {
                dataType: 'json',
                formData: formData
            };
            let $renderContainer = $('<form/>');
            $renderContainer.formRender(formRenderOpts);
            let html = '<!doctype html><head>' +
                        '<link rel="stylesheet" href="./include/bootstrap/css/bootstrap.min.css">'+
                        '<link rel="stylesheet" href="./css/render_main.css">'+
                         '<title>Form Preview</title></head><body><div class="container"><hr>'+$renderContainer.html()+'</div></body></html>';
            var formPreviewWindow = window.open('', 'formPreview', 'height=480,width=640,toolbar=no,scrollbars=yes');
            formPreviewWindow.document.write(html);
        }
        function setFormJsonObj(dialogBox) {
            var action_type;
            var frm_id = $("#form_content_id").val();
            var content_status =  $("#form_content_status").val();
            var frm_data = {
                record_id: frm_id,
                template: formContentJsonObj
            }
            var tbl = "formTemplate";
            if(content_status == "new"){
                action_type = "new";
            }else{
                action_type = "update";
            }
            //console.log(frm_id,content_status, "\n",formContentJsonObj);
            if (formContentJsonObj != "") {
                ajaxAction(action_type, tbl , frm_data,dialogBox);
            }
        }
        <?php endif; ?>
        function getFormData(form_id){
            var rt_data = "";
            if(form_id != ""){
                //ajax
                $.ajax({
                    type: "POST",
                    url: "get_form_data.php",
                    async:false,
                    data: {form_id : form_id},
                    success: function (response) {
                        response = JSON.parse(response);
                        rt_data = response;
                    },
                    error:function (response) {
                        console.log("Error:",JSON.stringify(response));
                        alert(response.responseText)
                    }
                });
            }
            return rt_data;
        }
        function get_form_content(form_id){
            var rt_data = "";
            if(form_id != ""){
                //ajax
                $.ajax({
                    type: "POST",
                    url: "get_form_content.php",
                    async:false,
                    data: {form_id : form_id},
                    success: function (response) {
                        rt_data = response;
                        //console.log(response);
                    },
                    error:function (response) {
                        console.log("Error:",JSON.stringify(response));
                        alert(response.responseText)
                    }
                });
            }
            return rt_data;
        }
        //////////////////////////////////////
        function setFormbuilderData(dialogBox){
            var action_type = $("#form_action").val(); //new,updte
            if(action_type == "update"){
                if(!confirm("Are you sure you want to update?")){
                    return false;
                }
            }
            var frm_id = $("#form_id").val();
            var frm_name = $("#form_name").val();
            var frm_title = $("#form_title").val();
            var frm_note = $("#form_note").val();
            var frm_pblsh_type = $("#publish_type").val();
            var frm_groups = $("#groups_list").val();
            var frm_mngrs = $("#form_managers_list").val();
            var frm_pblsh_stt = $("#status_type").val();
            var frm_data  = {
                record_id: frm_id,
                form_name: frm_name,
                form_title: frm_title,
                form_note: frm_note,
                publish_type: frm_pblsh_type,
                publish_groups: frm_groups,
                form_managers: frm_mngrs,
                status_type: frm_pblsh_stt
            };
            var tbl = "form";
            ajaxAction(action_type, tbl , frm_data, dialogBox);
        }

        /////////////////////////////general Users setting///////////////////////////
        function addUpdateUser(action,usr_id, grpSelctReadonly){
            var dialogTitle = "Add new user";
            var gContent = $("#formbuilder_general_content");
            gContent.addClass("dialog_form_container");
            gContent.html("");
            var hInput = "<input type='hidden' id='action_type' />";
            $(hInput).val(action).appendTo(gContent);
            var uhInput = "<input type='hidden' id='user_id' value = '" + usr_id + "' />";
            $(uhInput).appendTo(gContent);

            var user_data = "", userName, userPass , userEmail, userGroups, userStatus;
            if(action == "update"){
                dialogTitle = "Update user data";
                user_data = getUserData(usr_id);
            }
            if(user_data !== "" && user_data !== null && user_data !== undefined){
                //console.log(mor_data.data)
                userName =  user_data.data.usr_name;
                userPass = user_data.data.pass;
                userEmail = user_data.data.email;
                userGroups = user_data.data.groups;
                userStatus = user_data.data.status;
            }else{
                userName =  "";
                userPass = "";
                userEmail = "";
                userGroups = "";
                userStatus = "";
            }
            var uInput = "<input type='text' id='user_name' value = '" + userName + "'  />";
            var uName = addElement("User Name","user_name", uInput);
            uName.appendTo(gContent);
            var pInput = "<input type='password' id='user_password' value = '" + userPass + "' />";
            var uPass = addElement("Password","user_password", pInput);
            uPass.appendTo(gContent);
            var eInput = "<input type='text' id='user_email' value = '" + userEmail + "' />";
            var uEmail = addElement("Email","user_email", eInput);
            uEmail.appendTo(gContent);
            var gInput = "<select id='groupList' class='groupslist js-states form-control' multiple='multiple' style='width:80%;'></select>";
            var uGroups = addElement("Groups","groupList", gInput);
            uGroups.appendTo(gContent);
            setGroupsList(userGroups,grpSelctReadonly);
            //setGroupsList(mor_data.data.publ_grps);
            if(action == "update"){
                var sInput = "<select id='user_status'><option value='0'>Inactive</option><option value='1'>Active</option></select>";
                var uStatus = addElement("Status","user_status", sInput);
                uStatus.appendTo(gContent);
                if(grpSelctReadonly !== undefined && grpSelctReadonly == "true"){
                    $("#user_status").val(userStatus).change().attr("disabled", true);
                }else{
                    $("#user_status").val(userStatus).change();
                }
            }else{
                var usInput = "<input type='hidden' id='user_status' />";
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
                            add_update_user(general_dialog);
                        }
                    }
                ]
            );
            general_dialog.dialog("option","height",0.65*$(window).height());
            general_dialog.dialog("option","title",dialogTitle);
            general_dialog.dialog("open");
        }
                
        function getUserData(user_id){
            var rt_data = "";
            if(user_id != ""){
                $.ajax({
                    type: "POST",
                    url: "get_user_data.php",
                    async:false,
                    data: {user_id : user_id},
                    success: function (response) {
                        response = JSON.parse(response);
                        rt_data = response;
                    },
                    error:function (response) {
                        console.log("Error:",response.responseText);
                    },
                    failure: function (response) {
                        console.log("Error:" , JSON.stringify(response));
                    }
                });
            }
            return rt_data;
        }


        function addElement(label,id, element){
            var col25 = $("<div class='col-25'></div>");
            var col75 = $("<div class='col-75'></div>");
            var row = $("<div class='row'></div>");
            $('<label></label>', {
                for: id,
                text: label
            }).appendTo(col25);
            $(element).appendTo(col75);
            row.append(col25);
            row.append(col75);

            return row;
        }
    </script>

    <?php else: ?>
    
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<div class="container-login100" style="background-image: url('images/bg05.jpg');">
		<div>
			<h1>Login</h1>
			<div class="container-login100-form-btn p-t-10">
            <?php if(!empty($message)): ?>
                <br><p class="ui-widget-content" style='text-align:center; padding:3px;'><?= $message ?></p><br>
            <?php endif; ?>
			<a class="login100-form-btn" href="login.php">Login</a> </div><!-- or
			<a href="register.php">Register</a> -->
		</div>
	</div>
	<?php endif; ?>

</body>
</html>