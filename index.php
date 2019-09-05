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
$isManager = false;
$message = "";
$isUserFormAdmin = "";
$publishTypes = "";
if( isset($_SESSION['user_id']) ){

	$records = $conn->prepare('SELECT id,username,password,groups FROM users WHERE status="1" AND id = :id');
	$records->bindParam(':id', $_SESSION['user_id']);
	$records->execute();
	$results = $records->fetch(PDO::FETCH_ASSOC);

	$user = "";

	if($results != "" && count($results) > 0){
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
                    $isManager = true;
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

    if($user != ""){
        $publishTypes = getPublishTypes($conn);
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

function getPublishTypes($conn){
	$records = $conn->prepare('SELECT * FROM publish_type');
	$records->execute();
	$results = $records->fetchAll(PDO::FETCH_ASSOC);

    $options = "";

	if($results != "" && count($results) > 0){
        foreach($results as $row) {
            $id = $row["id"];
            $name = $row["name"];
            $options .= "<option value='$id'>$name</option>";
        }
    }

    return $options;
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

$defaultFormStyleSettings = getSetting("form_style", "");
$dFrmSets = json_encode($defaultFormStyleSettings);
$dFrmMaxBgImgSize = $defaultFormStyleSettings["max_body_bgImg_size"];
//echo "dFrmMaxBgImgSize: ". $dFrmMaxBgImgSize;

$editForm = getSetting("", "enableFormManagersToEditFormTamplate");
$isEditForm = false;
if(!empty($user) && ($isAdmin || $editForm == "1")){
    $isEditForm = true;
}

include "settings/about.php";
$about_html = ABOUT_APP_AUTHOR;
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta charset="UTF-8">
    
    <title>Form builder</title>
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
    

    <!--///////////// formbuilder jquery ///////////////-->
    <script src="./include/formbuilder/form-builder.min.js"></script>
    <script src="./include/formbuilder/form-render.min.js"></script>
    <script src="./include/formbuilder/control_plugins/buttons.js"></script>
    <script src="./include/formbuilder/control_plugins/table.js"></script>
    <!--<script src="./include/formbuilder/control_plugins/starRating.min.js"></script>-->

    <!--///////////////////////////////////////////////////////

    <link rel="stylesheet" href="./include/editTable-0.2.1/jquery.edittable.min.css">
    <script src="./include/editTable-0.2.1/jquery.edittable.min.js"></script>-->

    <script src="./include/jQueryPopMenu/src/jquery.popmenu.js"></script>

    <!-- color-picker
    <link rel="stylesheet" href="./include/jquery-minicolors-2.3.4/jquery.minicolors.css">
    <script src="./include/jquery-minicolors-2.3.4/jquery.minicolors.min.js"></script>-->
    <link rel="stylesheet" href="./include/spectrum-colorpicker/spectrum.css">
    <script src="./include/spectrum-colorpicker/spectrum.js"></script>

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

    <!-- Number fields handler-->
    <link rel="stylesheet" href="./include/Formstone-1.4.13.1/css/number.css">
    <link href="./include/Formstone-1.4.13.1/css/themes/light.css" rel="stylesheet">
    <script src="./include/Formstone-1.4.13.1/js/core.js"></script>
    <script src="./include/Formstone-1.4.13.1/js/number.js"></script>

    <!-- codemirror - custom form style -->
    <link rel="stylesheet" href="./include/codemirror-5.47.0/lib/codemirror.css">
    <!--<link rel="stylesheet" href="./include/codemirror-5.47.0/addon/hint/show-hint.css">-->
    <script src="./include/codemirror-5.47.0/lib/codemirror.js"></script>
    <script src="./include/codemirror-5.47.0/mode/css/css.js"></script>
    <script src="./include/codemirror-5.47.0/addon/display/autorefresh.js"></script>
    <!--
    <script src="./include/codemirror-5.47.0/addon/hint/show-hint.js"></script>
    <script src="./include/codemirror-5.47.0/addon/hint/css-hint.js"></script>
    -->

    <!-- Org tree -->
    <link rel="stylesheet" href="./include/jquery.orgChart/jquery.orgchart.css">
    <script src="./include/jquery.orgChart/jquery.orgchart.js"></script>

    <!-- full dialog -->
    <script src="./include/fulldialog/jqueryui.dialog.fullmode.js"></script>


    <script src="./include/selectlistactions/jquery.selectlistactions.js"></script>

    <!-- alertifyjs -->
    <link rel="stylesheet" href="./include/alertifyjs/css/alertify.min.css">
    <link rel="stylesheet" href="./include/alertifyjs/css/themes/bootstrap.min.css">
    <script src="./include/alertifyjs/alertify.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="./css/index_main.css">

	<?php if( !empty($user)): ?>
    <script type="text/javascript">
        var formbuilder_dialog,
            formbuilder_content_dialog,
            general_dialog,
            general_style_dialog,
            custom_style_editor_dialog,
            custom_style_editor,
            old_custom_style;

        $(function () {
             var isFullMode = false;
             formbuilder_dialog = $("#formbuilder_form").dialog({
                modal: true,
                autoOpen: false,
                dialogClass: "dialog-full-mode",
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

                ],
                close: function( event, ui ) {
                    isFullMode = false;
                }
            });
            formbuilder_content_dialog = $("#formbuilder_content").dialog({
                modal: true,
                autoOpen: false,
                dialogClass: "dialog-full-mode",
                width: 0.9*$(window).width(),
                height: 0.9*$(window).height(),
                close: function( event, ui ) {
                    isFullMode = false;
                }
            });
            general_dialog = $("#formbuilder_general_dialog").dialog({
                modal: true,
                autoOpen: false,
                dialogClass: "dialog-full-mode",
                width: 0.5*$(window).width(),
                height: 0.5*$(window).height(),
                close: function( event, ui ) {
                    isFullMode = false;
                }
            });
            general_style_dialog = $("#form_general_style").dialog({
                modal: true,
                autoOpen: false,
                dialogClass: "dialog-full-mode",
                width: 0.7*$(window).width(),
                height: 0.8*$(window).height(),
                close: function( event, ui ) {
                    isFullMode = false;
                }
            });
            custom_style_editor_dialog = $("#form_custom_style_editor").dialog({
                modal: true,
                autoOpen: false,
                dialogClass: "dialog-full-mode",
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
                            setCustomFormStyle();
                        }
                    }

                ],
                close: function( event, ui ) {
                    isFullMode = false;
                }
            });
            custom_style_editor = CodeMirror.fromTextArea(document.getElementById("form_custom_style_editor_code"), {
                lineNumbers: true,
                autoRefresh: true,
                lineWrapping: true,
                mode:  "css"
            });
            let $editorResizeHandle = document.querySelector(".style_editor_resize_handle");
            let $editorContainer = document.querySelector(".form_custom_style_editor_warper");

            function height_of($el) {
                return parseInt(window.getComputedStyle($el).height.replace(/px$/, ""));
            }

            const MIN_HEIGHT = 200;

            var start_x;
            var start_y;
            var start_h;

            function on_drag(e) {
                custom_style_editor.setSize(null, Math.max(MIN_HEIGHT, (start_h + e.y - start_y)) + "px");
            }

            function on_release(e) {
                document.body.removeEventListener("mousemove", on_drag);
                window.removeEventListener("mouseup", on_release);
            }

            $editorResizeHandle.addEventListener("mousedown", function (e) {    
                start_x = e.x;
                start_y = e.y;
                start_h = height_of($editorContainer);
                document.body.addEventListener("mousemove", on_drag);
                window.addEventListener("mouseup", on_release);
            });
            
            $(document).dialogfullmode();
            

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
                var publshTyp = $(this).val();
                if(publshTyp == "2" || publshTyp == "4"){ //Grops
                    //groups_list
                    setGroupsList();
                    $("#groups-row").show();
                    $("#deps-row").hide(); //hide departments
                }else if(publshTyp == "5" || publshTyp == "6"){ //departments
                    //departments list
                    setDepartmentsList();
                    $("#deps-row").show();
                    $("#groups-row").hide(); //hide groups
                }else{
                    $("#groups-row").hide(); //hide groups
                    $("#deps-row").hide(); //hide departments
                }
            })
            $("#restrict_multiple_submissions").on("click",function(){
                var rstrcSubmit = $(this).val();
                if(rstrcSubmit == "-1"){ 
                    $("#submit-amoun-row").hide();
                }else{
                     $("#submit-amoun-row").show();
                }
            })

            $("#status_type").on("change",function(){
                var formId = $("#form_id").val();
                if(formId == ""){
                    return false;
                }
                if($(this).val() == "1"){ //Publisheds
                    var formContent = get_form_content(formId);
                    var emptyContent = '[{"type":"hidden","name":"hidden-form-id","id":"hidden-form-id","value":"'+formId+'"},{"type":"Buttons","label":"","className":"buttons-container","name":"","submitBtnColor":"btn btn-primary","clearBtnColor":"btn btn-danger","btnsPos":"","submitLabel":"Submit","cancelLabel":"Clear"}]';
                    //console.log("formId: ",formId,", formContent:",(formContent == emptyContent)?"empty":"not empty",formContent)
                    if(formContent == emptyContent || formContent === undefined || formContent=="" || formContent=="new" || formContent === null){
                        alert("You can't to publish this form because it has no form template.")
                        $("#status_type").val("2");//.change();
                        $("#form_links").hide();
                    }else{
                        if(confirm("Are you sure you want to publish this form now?")){
                            var frm_data  = {
                                record_id: formId,
                                status_type: "1"
                            };
                            ajaxAction("update", "form" , frm_data, null)
                            //show links
                            var webformlink = "form.php?id=" + formId;
                            var adminformlink = "formadmin.php?id=" + formId;
                            $("#web_form_link").attr("href", webformlink);
                            $("#admin_web_form_link").attr("href", adminformlink);
                            $("#form_links").show();
                            $("#form_preview").prop("disabled",true);
                            $(".form_preview_worn").html("<span style='color:red'>You can not edit form template when form status is 'Published' mode</span>")
                        }
                    }
                }else if($(this).val() == "2"){
                    if(confirm("Are you sure you want to unpublish this form now?")){
                        var frm_data  = {
                            record_id: formId,
                            status_type: "2"
                        };
                        ajaxAction("update", "form" , frm_data, null)
                        $("#form_links").hide();
                        $("#form_preview").prop("disabled",false);
                        $(".form_preview_worn").html("");
                        //$(".form_preview_worn").html("<span style='color:red'>First save the settings and then reopen for edit the template")
                    }
                }

            })
            /*
            $(".color_pecker").minicolors({
                format: 'rgb',
                opacity: true,
                theme: 'bootstrap'
            })
            */
            $(".color_pecker").spectrum({
                preferredFormat: "rgb",
                showAlpha: true,
                showInitial: true,
                showInput: true/*,
                show: function(color) {
                    $("#" + $(this)[0].id).change();
                    //console.log("spectrum: ", $(this)[0].id)
                }*/
            });

            $("input[type='number']").number();
        });

        alertify.defaults.transition = "slide";
        alertify.defaults.theme.ok = "btn btn-primary";
        alertify.defaults.theme.cancel = "btn btn-danger";
        alertify.defaults.theme.input = "form-control";

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
                        if (dialogbox !== undefined && dialogbox !== null && dialogbox.hasClass('ui-dialog-content')){
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
                    }else{
                        alert(response)
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
    function openLinkInNewTab(url) {
        var win = window.open(url, '_blank');
        $("#main-vewer-menu ul").hide();
        win.focus();
    }
    function showAbout(){
        var aboutHtml = "<?=$about_html ?>";
        $("#formbuilder_general_content").html(aboutHtml);
        general_dialog.dialog("option","height",0.65*$(window).height());
        general_dialog.dialog("option","title","About");
        general_dialog.dialog("open");
        $("#main-vewer-menu ul").hide();
    }
    function checkNewVer(currentVer){
        //alert("currentVer: " + currentVer)
        $("#app-ver-check-result").html("<img src='images/spinner.gif' />");
        $("#app-ver-check-result").show();
        var appUrl = "https://api.github.com/repos/meshesha/SimplePhpFormBuilder/releases/latest";
        $.ajax({
            type: "GET",
            url: appUrl,
            dataType: "json",
            success: function (response) {
                if(response != "" && response !== undefined && response !== null){
                    var new_ver_str = response.tag_name.toLowerCase();
                    var new_ver_url = response.html_url;
                    var isDraft = response.draft;
                    var isPrerelease = response.prerelease;
                    var note = response.body.replace(/\r\n/g,"<br>");
                    var new_ver_num = "";
                    var verPrefix = ["v","v.","ver","ver."];
                    $.each(verPrefix, function(i,prefix){
                        var prefixLoc = new_ver_str.indexOf(prefix);
                        if(prefixLoc != -1){
                            new_ver_num = new_ver_str.substr((prefixLoc + 1));
                        }
                    })
                    if(currentVer == new_ver_num){
                        $("#app-ver-check-result").html("SimplePhpFormBuilder is up to date.")
                    }else{
                        var currentVerAry = [];
                        if(currentVer.indexOf(".") != -1){
                            currentVerAry = currentVer.split(".");
                        }else{
                            currentVerAry[0] = currentVer;
                        }
                        var newVerNumAry = [];
                        if(new_ver_num.indexOf(".") != -1){
                            newVerNumAry = new_ver_num.split(".");
                        }else{
                            newVerNumAry[0] = new_ver_num;
                        }
                        /**Semantic Versioning:
                        1.0.0
                        1.0.0
                        1.10.5-RC1
                        4.4.4-beta2
                        2.0.0-alpha
                        2.0.4-p1
                         */
                        if(currentVerAry.length == 3 && newVerNumAry.length == 3){
                            var newVerNum3thd = "";
                            var newVerSuffix = "";
                            if(newVerNumAry[2].indexOf("-") != -1){
                                newVernum3thdAry = newVerNumAry[2].split("-");
                                newVerNum3thd = newVernum3thdAry[0];
                                newVerSuffix =  newVernum3thdAry[1];
                            }else{
                                newVerNum3thd = newVerNumAry[2];
                            }
                            var crntVerNum3thd = "";
                            var crntVerSuffix = "";
                            if(currentVerAry[2].indexOf("-") != -1){
                                crntVerNum3thdAry = currentVerAry[2].split("-");
                                crntVerNum3thd = crntVerNum3thdAry[0];
                                crntVerSuffix =  crntVerNum3thdAry[1];
                            }else{
                                crntVerNum3thd = currentVerAry[2];
                            }
                            //console.log(newVerNumAry[0],newVerNumAry[1],newVerNum3thd)
                            //console.log(currentVerAry[0],currentVerAry[1],crntVerNum3thd)
                            if(Number(newVerNumAry[0]) > Number(currentVerAry[0]) ||
                                ((Number(newVerNumAry[0]) == Number(currentVerAry[0])) && (Number(newVerNumAry[1]) > Number(currentVerAry[1]))) ||
                                ((Number(newVerNumAry[0]) == Number(currentVerAry[0])) && (Number(newVerNumAry[1]) == Number(currentVerAry[1])) &&  (Number(newVerNum3thd) > Number(crntVerNum3thd)))){
                                if(isDraft && isPrerelease){
                                    $("#app-ver-check-result").html("<p>There is a new version but this is a pre-release and draft</p>");
                                }else if(!isDraft && isPrerelease){
                                    $("#app-ver-check-result").html("<p>There is a new version but this is a pre-release</p>");
                                }else if(isDraft && !isPrerelease){
                                    $("#app-ver-check-result").html("<p>There is a new version but this is a draft</p>");
                                }else{
                                    if(newVerSuffix == ""){
                                        $("#app-ver-check-result").html("<p>There is a new version</p>");
                                    }else{
                                        $("#app-ver-check-result").html("<p>There is a new version but this is '"  +newVerSuffix + "'</p>");
                                    }
                                }
                                $("#app-ver-check-result").append("<p><a href='"+new_ver_url+"' target='_blank'><span class='btn btn-primary'>Download</span></a></p>");
                                $("#app-ver-check-result").append("<p><u>Details:</u><br>- last version: " + new_ver_num + "<br>" +  note + "</p>");
                            }else if(Number(newVerNumAry[0]) == Number(currentVerAry[0]) &&
                                    Number(newVerNumAry[1]) == Number(currentVerAry[1]) &&
                                    Number(newVerNum3thd) == Number(crntVerNum3thd)){ 
                                if(newVerSuffix != ""  && newVerSuffix != crntVerSuffix){
                                    $("#app-ver-check-result").html("<p>There is a new '"+newVerSuffix+"' version</p>");
                                    $("#app-ver-check-result").append("<p><a href='"+new_ver_url+"' target='_blank'><span class='btn btn-primary'>Download</span></a></p>");
                                    $("#app-ver-check-result").append("<p><u>Details:</u><br>- last version: "  + new_ver_num + "<br>" + note + "</p>");
                                }else if(newVerSuffix == ""  && newVerSuffix != crntVerSuffix){
                                    $("#app-ver-check-result").html("<p>There is a new version</p>");
                                    $("#app-ver-check-result").append("<p><a href='"+new_ver_url+"' target='_blank'><span class='btn btn-primary'>Download</span></a></p>");
                                    $("#app-ver-check-result").append("<p><u>Details:</u><br>- last version: "  + new_ver_num + " (stable)<br>" + note + "</p>");
                                }
                            }else{
                                $("#app-ver-check-result").html("This version is newer than what exists in the Github :)")
                            }
                        }else{
                            $("#app-ver-check-result").html("<span style='color:red;'>Error: Semantic Versioning error.</span>");
                        }
                        //console.log(new_ver_str,new_ver_num, new_ver_url);
                    }
                }else{
                    $("#app-ver-check-result").html("<span style='color:red;'>Error: the github api response no json format</span>");
                }
            },
            error:function (response) {
                $("#app-ver-check-result").html("<span style='color:red;'>Error:" + JSON.stringify(response) + "</span>");
                //alert(response.responseText)
            }
        });
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
                    <li onclick="openLinkInNewTab('https://github.com/meshesha/SimplePhpFormBuilder/wiki')" title="Help"><i class="fa fa-question-circle"></i><div class="menu-icons-text">Help</div></li>
                    <li onclick="showAbout()" title="About"><i class="fa fa-info-circle"></i><div class="menu-icons-text">About</div></li>
                    <li onclick="javascript:location.href='logout.php'" title="Exit from system"><i class="fa fa-power-off"></i><div class="menu-icons-text">Logout</div></li>
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
                    <div id="form_list_content" class="ui-widget-content">
                        <table id="form_list_content_table" class="display" style="width:100%"></table>
                    </div>
                </div>
                 <?php if($isAdmin): ?>
                <div id="users_tab"  style="overflow:auto; height: 90%;">
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
    
    <script src="./js/users_groups_deps.js"></script>
    
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
                        <input type="text" id="form_name" name="form_name" title="Form name" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-25">
                        <label for="form_title">Form title</label>
                    </div>
                    <div class="col-75">
                        <input type="text" id="form_title" name="form_title" title="Form title"  required/>
                    </div>
                </div>
                <!-- //////////////Restrict multiple missions/////////////// -->
                <div class="row">
                    <div class="col-25">
                        <label for="restrict_multiple_submissions">Restrict multiple missions</label>
                    </div>
                    <div class="col-75">
                        <select id="restrict_multiple_submissions" name="restrict_multiple_submissions">
                            <option value="-1">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
                <div class="row" id="submit-amoun-row">
                    <div class="col-25">
                        <label for="submit_amount_allowed">The amount allowed to submit</label>
                    </div>
                    <div class="col-75">
                        <input type="number" id="submit_amount_allowed" name="submit_amount_allowed" min="1" step="1" value="1" title="The amount allowed to submit"  />
                    </div>
                </div>
                <!-- //////////////////////////////////////////////////////// -->
                <div class="row">
                    <div class="col-25">
                        <label for="publish_type">Publish type</label>
                    </div>
                    <div class="col-75">
                        <select id="publish_type" name="publish_type">
                            <?=$publishTypes ?>
                            <!--
                            <option value="1">Public</option>
                            <option value="2">Groups</option>
                            <option value="3">Public-Anonymously</option>
                            <option value="4">Groups-Anonymously</option>
                            <option value="5">Department</option>
                            <option value="6">Department-Anonymously</option>
                            -->
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

                <div class="row" id="deps-row">
                    <div class="col-25">
                        <label for="deps_list">Departments</label>
                    </div>
                    <div class="col-75">
                        <select id="deps_list" name="deps_list" class="depslist js-states form-control" multiple="multiple" style="width: 80%"></select>
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
                        <label for="form_general_style">General Form Style</label>
                    </div>
                    <div class="col-75">
                        <button type="button" id="form_general_style" class="btn btn-info btn-lg" onclick="edit_gneral_style()">Edit</button>
                        <p class="gneral_style_worn"></p>
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
                <div class="row previw_btn_toolbar">
                    <div class="col-25">
                        <label for="form_preview">Form Template</label>
                    </div>
                    <div class="col-75">
                        <button type="button" id="form_preview" class="btn btn-primary btn-lg" onclick="edit_form_file()">Edit</button><p class="form_preview_worn"></p>
                    </div>
                </div>

                <!-- //////////////Customize form style/////////////// -->
                <div class="row previw_btn_toolbar">
                    <div class="col-25">
                        <label for="form_customize_style">Customize Form Style</label>
                    </div>
                    <div class="col-75">
                        <button type="button" id="form_customize_style" class="btn btn-success btn-lg" onclick="edit_custom_style()">Edit</button>
                        <p class="custim_style_worn"></p>
                    </div>
                </div>
                <!-- //////////////////////////////////////////////// -->
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

    <div id="form_general_style" title="General form style settings">
        <div class="form_general_style_warper">
            <div class="prop-area">
                <h5>Form direction: </h5>
                <div class="dialog_form_style">
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_body_direction">Direction:</label>
                        </div>
                        <div class="column-a2">
                            <select class="form_style_input" id="form_body_direction"  title="Form direction">
                                <option value="ltr" selected>Left to Right (LTR)</option>
                                <option value="rtl" selected>Right to Left (RTL)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <hr>
                <h5>Form body background style: </h5>
                <div class="dialog_form_style">
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_body_bgcolor_1">Body BgColor 1:</label>
                        </div>
                        <div class="column-a2">
                            <input type="text" class="color_pecker form_style_input" id="form_body_bgcolor_1"  title="Body Background Color 1" value="rgba(255, 255, 255, 1)" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_body_bgcolor_2">Body BgColor 2:</label>
                        </div>
                        <div class="column-a2">
                            <input type="text" class="color_pecker form_style_input" id="form_body_bgcolor_2"  title="Body Background Color 2" value="rgba(255, 255, 255, 1)" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_body_bgcoloe_angle">Body BgColor Angle:</label>
                        </div>
                        <div class="column-a2">
                            <input type="number" class="form_style_input input_numper_type" id="form_body_bgcoloe_angle" min="0" max="360" setp="1"  title="Linear gradient color angle"  value="0" />(deg)
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_body_bgImage">Body BgImage:</label>
                        </div>
                        <div class="column-a2">
                            <input type="file" class="form_style_input" id="form_body_bgImage"  accept="image/*" title="Body Background Image" />
                        </div>
                    </div>
                    <div class="row">
                        <table>
                            <tr>
                                <td>
                                    <label for="form_body_bgImage_attach">Image attachment:</label>
                                    <select id="form_body_bgImage_attach" class="form_style_input">
                                        <option value="scroll">scroll</option>
                                        <option value="fixed">fixed</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="form_body_bgImage_position">Image position:</label>
                                    <select id="form_body_bgImage_position" class="form_style_input">
                                        <option value="left top">left top</option>
                                        <option value="left center">left center</option>
                                        <option value="left bottom">left bottom</option>
                                        <option value="right top">right top</option>
                                        <option value="right center">right center</option>
                                        <option value="right bottom">right bottom</option>
                                        <option value="center top">center top</option>
                                        <option value="center center" selected >center center</option>
                                        <option value="center bottom">center bottom</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="form_body_bgImage_repet">Image repet:</label>
                                    <select id="form_body_bgImage_repet" class="form_style_input">
                                        <option value="no-repeat">no-repeat</option>
                                        <option value="repeat">repeat both</option>
                                        <option value="repeat-x">repeat-x</option>
                                        <option value="repeat-y">repeat-y</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <label for="form_body_bgImage_size">Image size:</label>
                                    <select id="form_body_bgImage_size" class="form_style_input">
                                        <option value="auto">Orginal size</option>
                                        <option value="contain">contain</option>
                                        <option value="cover">cover</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <hr>
                <h5>Form style: </h5>

                <div class="dialog_form_style">
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_width">width:</label>
                        </div>
                        <div class="column-a2">
                            <input type="number" class="form_style_input" id="form_width" min="0" max="100" setp="1"  title="Form width" value="80"/>(px)
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_vertical_margin">Vertical margin:</label>
                        </div>
                        <div class="column-a2">
                            <input type="number" class="form_style_input" id="form_vertical_margin" min="0" max="100" setp="1"  title="Form Vertical margin" value="10" />(%)
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_Background_color">Form bgcolor:</label>
                        </div>
                        <div class="column-a2">
                            <input type="text" class="color_pecker form_style_input" id="form_Background_color"  title="Form Background Color" value="rgba(255, 255, 255, 1)" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_opacity">Opacity:</label>
                        </div>
                        <div class="column-a2">
                            <input type="number" class="form_style_input" id="form_opacity" min="0" max="100" setp="1"  title="Form Opacity" value="100" />(%)
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_border_size">Form border size:</label>
                        </div>
                        <div class="column-a2">
                            <input type="number" class="form_style_input" id="form_border_size" min="0" setp="1"  title="Form border size" value="1" />(px)
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_border_type">Form border type:</label>
                        </div>
                        <div class="column-a2">
                            <select class="form_style_input" id="form_border_type"  title="Form border type">
                                <option value="solid">solid</option>
                                <option value="dotted">dotted</option>
                                <option value="dashed">dashed</option>
                                <option value="double">double</option>
                                <option value="groove">groove</option>
                                <option value="ridge">ridge</option>
                                <option value="inset">inset</option>
                                <option value="outset">outset</option>
                                <option value="none">none</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_border_color">Form border color:</label>
                        </div>
                        <div class="column-a2">
                            <input type="text" class="color_pecker form_style_input" id="form_border_color"  title="Form border color" value="rgba(0, 0, 0, 1)" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="column-a1">
                            <label for="form_border_radius">Form border radius:</label>
                        </div>
                        <div class="column-a2">
                            <input type="number" class="form_style_input" id="form_border_radius" min="0" setp="1"  title="Form border radius" value="5" />(px)
                        </div>
                    </div>
                </div>
            </div>
            <div class="preview-area">
                <div class="example">
                    <div class="example-body">
                        <div class="example-form-warper-1">
                            <div class="example-form-content" style="text-align:center;"><h4>Form example</h4></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="form_custom_style_editor" title="Custom form style editor">
        <input type="hidden" id="custom_style_editor_type" />
        <div style="padding: 2px 2px 3px 2px;" class="ui-widget-content">
            <button id="form_custom_style_editor_clear_btn" class="btn btn-outline-danger btn-sm" onclick="clearCustomStyleEditor()" >Clear</button>
            <button id="form_custom_style_editor_clear_btn" class="btn btn-outline-primary btn-sm" onclick="undoCustomStyleEditor()" >Undo</button>
            <button id="form_custom_style_editor_clear_btn" class="btn btn-outline-primary btn-sm" onclick="redoCustomStyleEditor()" >Redo</button>
        </div>
        <div class="form_custom_style_editor_warper ui-widget-content" >
            <textarea id="form_custom_style_editor_code"></textarea>
            <div class="style_editor_resize_handle"></div>
        </div>
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
            $("#restrict_multiple_submissions").val("-1").change();
            $("#submit_amount_allowed").val("");
            $("#submit-amoun-row").hide();
            $("#publish_type").val("1").change();
            $("#groups-row").hide();
            $('#groups_list').val("");
            $("#deps-row").hide();
            $('#deps_list').val("");
            $('#form_managers_list').val("");
            $("#form_note").val("");
            $("#status_type").val("2");//.change();
            $("#status_type").prop("disabled",true);
            $(".previw_btn_toolbar").hide();
            formbuilder_dialog.dialog("option","title","Add New");
            //form style
            setDefaultFormStyleObj();
            //groups_list
            setGroupsList();
            //departments list
            setDepartmentsList();
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
            
            $('.groupslist').empty();
            $('.groupslist').select2({
                disabled: isDisabled,
                ajax: {
                    url: 'get_all_groups.php',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        //console.log( params.term)
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

        function setDepartmentsList(selectedAry,readonly){
            var isDisabled = false;
            if(readonly !== undefined && readonly == "true"){
                isDisabled = true;
            }
            $('.depslist').empty();
            $('.depslist').select2({
                disabled: isDisabled,
                ajax: {
                    url: 'get_all_deps.php',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        //console.log( params.term)
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
            $multiSelectDep = $('.depslist');
            $multiSelectDep.val(null).trigger('change');
            $.ajax({
                type: 'POST',
                url: 'get_all_deps.php'
            }).then(function (data) {
                //console.log(selectedAry)
                var selectObj = JSON.parse(data);
                var selectObjAry = selectObj.results;
                $.each(selectObjAry, function(i,val){
                    if(selectedAry.indexOf(val.id) != -1){
                        var option = new Option(val.text,val.id, true, true);
                        $multiSelectDep.append(option).trigger('change');
                    }
                });
                // manually trigger the 'select2:select' event
                $multiSelectDep.trigger({
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
            $('.managerlist').empty();
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
                    if(mor_data.data.restrict_submissions == "-1"){
                        $("#restrict_multiple_submissions").val(mor_data.data.restrict_submissions).change();
                        $("#submit_amount_allowed").val("");
                        $("#submit-amoun-row").hide();
                    }else{
                        $("#restrict_multiple_submissions").val("1").change();
                        $("#submit_amount_allowed").val(mor_data.data.restrict_submissions);
                        $("#submit-amoun-row").show();
                    }
                    $("#publish_type").val(mor_data.data.publ_type).change();
                    if(mor_data.data.publ_type == "2" || mor_data.data.publ_type == "4"){ //Grops
                        $("#groups-row").show();
                        setGroupsList(mor_data.data.publ_grps);
                        $("#deps-row").hide(); //hide depatments
                    }else if(mor_data.data.publ_type == "5" || mor_data.data.publ_type == "6"){ //departments
                        $("#deps-row").show();
                        setDepartmentsList(mor_data.data.publ_deps)
                        $("#groups-row").hide(); //hide groups
                    }else{
                        $("#groups-row").hide(); //hide groups
                        $("#deps-row").hide(); //hide depatments
                    }

                    setUsersManagerList(mor_data.data.admin_users);
                    $("#status_type").prop("disabled",false);
                    $("#status_type").val(mor_data.data.publ_status);//.change();
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
                    $(".previw_btn_toolbar").show();
                    /////Form style object////////////////////////////
                    sessionStorage.formStylObj = mor_data.data.frm_style;
                    //////////////////////////////////////////////////
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
            
            if(form_content != undefined && form_content != null && form_content != "new" && form_content != ""){
                var form_content_obj = JSON.parse(form_content);
                if(form_content_obj.length > 0){
                    form_content_obj.forEach(function(item, index){
                        if(item.label !== undefined){ //item.type == "paragraph" || item.type == "header" //
                            item.label = item.label.replace(/&quot;/g,"\"");
                            item.label = item.label.replace(/&apos;/g, "'"); 
                        }
                        if(item.description !== undefined){ 
                            item.description = item.description.replace(/&quot;/g,"\""); 
                            item.description = item.description.replace(/&apos;/g, "'"); 
                        }
                        if(item.type != "table" && item.placeholder !== undefined){ 
                            item.placeholder = item.placeholder.replace(/&quot;/g,"\""); 
                            item.placeholder = item.placeholder.replace(/&apos;/g, "'"); 
                        }
                        if(item.type != "hidden" && item.value !== undefined){
                            item.value = item.value.replace(/&quot;/g,"\"");
                            item.value = item.value.replace(/&apos;/g, "'"); 
                        }

                        if(item.type == "select" || item.type == "checkbox-group" || item.type == "radio-group"){
                            if(item.values !== undefined && item.values.length > 0){
                                item.values.forEach(function(item2, index){
                                    item2.label = item2.label.replace(/&quot;/g,"\""); 
                                    item2.label = item2.label.replace(/&apos;/g, "'");
                                });
                            }
                        }

                    });
                    form_content = JSON.stringify(form_content_obj);
                }
            }
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
            var tableBtn = {
                title: {
                    label: 'Table Columns',
                    type: "button",
                    value: "Edit",
                    style: "width:80px",
                    onclick: "setTableSettings(this)"
                }
            };
            
            var options = {
                controlPosition: 'left',
                disabledActionButtons: ['data'],
                formData: (form_content=="new")?"":form_content,
                dataType: 'json',
                typeUserAttrs: {
                    header: positionOptions,
                    file: maxFileSize,
                    table: tableBtn,
                    Buttons: {
                        label:{
                            label: 'Container label',
                            type: 'text',
                            value: ''
                        },
                        submitBtnColor: {
                            label: 'Submit button color',
                            multiple: false, // optional, omitting generates normal <select>
                            options: {
                                '':'',
                                'btn btn-primary': 'blue',
                                'btn btn-secondary': 'gray',
                                'btn btn-success': 'green',
                                'btn btn-danger': 'red',
                                'btn btn-warning': 'yellow',
                                'btn btn-info': 'light blue',
                                'btn btn-light': 'white',
                                'btn btn-dark': 'dark',
                                'btn btn-link': 'link'
                            }/*,
                            onchange: 'console.log(this)'*/
                        },
                        clearBtnColor: {
                            label: 'Clear button color',
                            multiple: false, // optional, omitting generates normal <select>
                            options: {
                                '':'',
                                'btn btn-primary': 'blue',
                                'btn btn-secondary': 'gray',
                                'btn btn-success': 'green',
                                'btn btn-danger': 'red',
                                'btn btn-warning': 'yellow',
                                'btn btn-info': 'lightblue',
                                'btn btn-light': 'white',
                                'btn btn-dark': 'dark',
                                'btn btn-link': 'link'
                            }/*,
                            onchange: 'console.log(this)'*/
                        },
                        btnsPos: {
                            label: 'Buttons position',
                            multiple: false, // optional, omitting generates normal <select>
                            options: {
                                '':'',
                                'form-control-buttons-right': 'Right',
                                'form-control-buttons-left': 'Left',
                                'form-control-buttons-center': 'Center'
                            }/*,
                            onchange: 'console.log(this)'*/
                        },
                        submitLabel:{
                            label: 'Submit button label',
                            type: 'text',
                            value: ''
                        },
                        cancelLabel:{
                            label: 'Cancel button label',
                            type: 'text',
                            value: ''
                        }
                    }
                },
                replaceFields: [
                    {
                        type: "table",
                        label:'Table',
                        placeholder: "[{&quot;name&quot;:&quot;Column1&quot;,&quot;type&quot;:&quot;txt&quot;,&quot;attr&quot;:&quot;&quot;},{&quot;name&quot;:&quot;Column2&quot;,&quot;type&quot;:&quot;txt&quot;,&quot;attr&quot;:&quot;&quot;},{&quot;name&quot;:&quot;Column3&quot;,&quot;type&quot;:&quot;txt&quot;,&quot;attr&quot;:&quot;&quot;}]"
                    }
                ],
                disableFields: ['autocomplete','hidden','button','Buttons'],
                controlOrder: [
                    'header',
                    'text',
                    'textarea'
                ],
                disabledAttrs: [
                    'access'
                ],
                disabledSubtypes: {
                    file: ['fineuploader'],
                    textarea: ['quill']
                },
                disabledFieldButtons: {
                    table: ['copy'], // disables the copy button for table fields
                    Buttons: ['copy','remove'],
                    hidden: ['copy','remove']
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
                    },
                    table: {
                        onadd: function (fld) {
                            var $patternField = $(".fld-placeholder", fld);
                            var $patternWrap = $patternField.parents(".placeholder-wrap:eq(0)");
                            $patternWrap.hide();
                        }
                    },
                    Buttons: {
                        onadd: function (fld) {
                            var $patternField = $(".fld-value", fld);
                            var $patternWrap = $patternField.parents(".value-wrap:eq(0)");
                            $patternWrap.hide();
                            var $patternField = $(".fld-required", fld);
                            var $patternWrap = $patternField.parents(".required-wrap:eq(0)");
                            $patternWrap.hide();
                            var $patternField = $(".fld-placeholder", fld);
                            var $patternWrap = $patternField.parents(".placeholder-wrap:eq(0)");
                            $patternWrap.hide();
                            /*
                            var $patternField = $(".fld-name", fld);
                            var $patternWrap = $patternField.parents(".name-wrap:eq(0)");
                            $patternWrap.hide();
                            */
                        }
                    },
                    hidden: {
                        onadd: function (fld) {
                            var $valueField = $(".fld-value", fld);
                            $valueField.attr("readonly",true);
                            var $nameField = $(".fld-name", fld);
                            $nameField.attr("readonly",true);
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
                            previewForm(frm_id,data);
                        }
                    }
                },{
                    id: 'custom_form_stle_btn',
                    className: 'btn btn-warning',
                    label: 'Style',
                    type: 'button',
                    events: {
                        click: function() {
                            edit_custom_style();
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
                    try{
                        var formDataObj =  JSON.parse(formData);
                        if(formDataObj.length > 0){
                            formDataObj.forEach(function(item, index){
                                if(item.label !== undefined){ //item.type == "paragraph" || item.type == "header" //
                                    item.label = item.label.replace(/"/g, "&quot;"); 
                                    item.label = item.label.replace(/'/g, "&apos;"); 
                                }
                                if(item.description !== undefined){ 
                                    item.description = item.description.replace(/"/g, "&quot;"); 
                                    item.description = item.description.replace(/'/g, "&apos;"); 
                                }
                                if(item.type != "table" && item.placeholder !== undefined){ 
                                    item.placeholder = item.placeholder.replace(/"/g, "&quot;"); 
                                    item.placeholder = item.placeholder.replace(/'/g, "&apos;"); 
                                }
                                if(item.type != "hidden" && item.value !== undefined){
                                    item.value = "";
                                }

                                if(item.type == "select" || item.type == "checkbox-group" || item.type == "radio-group"){
                                    if(item.values !== undefined && item.values.length > 0){
                                        item.values.forEach(function(item2, index){
                                            item2.label = item2.label.replace(/"/g, "&quot;"); 
                                            item2.label = item2.label.replace(/'/g, "&apos;");
                                        });
                                    }
                                }

                            });
                            formData = JSON.stringify(formDataObj);
                        }
                        //console.log(formData)
                        formContentJsonObj = formData;
                        setFormJsonObj(formbuilder_content_dialog);
                    }catch(err){
                        console.log(err.message);
                    }
                },
                onOpenFieldEdit: function(editPanel) {
                    //console.log()
                    //console.log($(editPanel).parent()[0].type)
                    if($(editPanel).parent()[0].type == "table"){
                        $($($($(editPanel)[0].children[0]).find(".title-wrap")[0]).find(".input-wrap")[0].children[0]).click();
                    }
                },
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

        function previewForm(frm_id,data){
            console.log(data)
            // Store
            var dataName = "formpreview-" + frm_id; 
            localStorage.setItem(dataName, data);
            var previewLink = "formpreview.php?id="+frm_id;
             window.open(previewLink, 'formPreview', 'height=480,width=640,toolbar=no,scrollbars=yes');
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
            //check requered fields 
            //validate fields
            var fail = false;
            var fail_log = '';
            var name;
            $('#formbuilder_form').find('select, textarea, input').each(function(){
                if(!$(this).prop('required')){
                    //
                } else {
                    if (!$(this).val()) {
                        fail = true;
                        name = $(this).attr('title');
                        if(name == "" || name === null || name == undefined){
                            name = $(this).attr('name');
                        }
                        fail_log += name + " is required \n";
                    }

                }
            });

            if (fail) {
                alert(fail_log);
                return false;
            }
            var action_type = $("#form_action").val(); //new,updte
            if(action_type == "update"){
                if(!confirm("Are you sure you want to update?")){
                    return false;
                }
            }
            ///form style settings
            if(sessionStorage.formStylObj === undefined || sessionStorage.formStylObj === null || sessionStorage.formStylObj == ""){
                setDefaultFormStyleObj();
            }
            var frm_id = $("#form_id").val();
            var frm_name = $("#form_name").val();
            var frm_title = $("#form_title").val();
            var frm_note = $("#form_note").val();
            var frm_rstrct_submit = $("#restrict_multiple_submissions").val();
            if(frm_rstrct_submit != "-1"){ 
                frm_rstrct_submit = $("#submit_amount_allowed").val();
            }
            var frm_pblsh_type = $("#publish_type").val();
            var frm_groups = $("#groups_list").val();
            var frm_deps = $('#deps_list').val();
            var frm_mngrs = $("#form_managers_list").val();
            var frm_pblsh_stt = $("#status_type").val();
            var frm_data  = {
                record_id: frm_id,
                form_name: frm_name,
                form_title: frm_title,
                form_note: frm_note,
                restrict_submit: frm_rstrct_submit,
                publish_type: frm_pblsh_type,
                publish_groups: frm_groups,
                publish_deps: frm_deps,
                form_managers: frm_mngrs,
                status_type: frm_pblsh_stt,
                form_general_style: sessionStorage.formStylObj
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

            var user_data = "", userName, userPass , userEmail, userGroups,userDep, userStatus;
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
                userDep = user_data.data.dep;
                userStatus = user_data.data.status;
            }else{
                userName =  "";
                userPass = "";
                userEmail = "";
                userGroups = "";
                userDep = "";
                userStatus = "";
            }
            var uInput = "<input type='text' id='user_name' value = '" + userName + "'  />";
            var uName = addElement("User Name","user_name", uInput);
            uName.appendTo(gContent);

            var pInput = "<input type='password' id='user_password' value = '" + userPass + "' />";
            var uPass = addElement("Password","user_password", pInput);
            uPass.appendTo(gContent);

            var cPInput = "<input type='password' id='confirm_password' value = '' />";
            var cPPass = addElement("Confirm password","confirm_password", cPInput);
            cPPass.appendTo(gContent);
            

            var eInput = "<input type='text' id='user_email' value = '" + userEmail + "' />";
            var uEmail = addElement("Email","user_email", eInput);
            uEmail.appendTo(gContent);

            var gInput = "<select id='groupList' class='groupslist js-states form-control' multiple='multiple' style='width:80%;'></select>";
            var uGroups = addElement("Groups","groupList", gInput);
            uGroups.appendTo(gContent);
            setGroupsList(userGroups,grpSelctReadonly);
            
            // multiple='multiple'
            var gInput = "<select id='depsList' class='depslist js-states form-control' style='width:80%;'></select>";
            var uDeps = addElement("Department","depsList", gInput);
            uDeps.appendTo(gContent);
            setDepartmentsList(userDep,grpSelctReadonly);

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
            $("#main-vewer-menu ul").hide();
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

        /////////////////////////////Form gneral style//////////////////////////////////////
        function edit_gneral_style(){
            //if update get form style and set form preview and sessionStorage.formStylObj
            //if new set default sessionStorage.formStylObj
            /**
            
            $("#form_body_bgcolor_1").val();
            $("#form_body_bgcolor_2").val();
            $("#form_body_bgcoloe_angle").val();

            $("#form_width").val();
            $("#form_vertical_margin").val();
            $("#form_Background_color").val();
            $("#form_opacity").val();
            $("#form_border_size").val();
            $("#form_border_type").val();
            $("#form_border_color").val();
            $("#form_border_radius").val();

             */
            var form_id = $("#form_id").val();
            if(form_id != ""){
                //update
                var obj = sessionStorage.formStylObj;
                if(obj != "null"){
                    //console.log("update",obj);
                    var parsedObj = JSON.parse(obj);

                    $("#form_body_direction").val(parsedObj.form_body_direction).change();

                    $("#form_body_bgcolor_1").spectrum("set", parsedObj.form_body_bgcolor_1);
                    $("#form_body_bgcolor_2").spectrum("set", parsedObj.form_body_bgcolor_2);
                    $("#form_body_bgcoloe_angle").val(parsedObj.form_body_bgcoloe_angle).change();
                    if(parsedObj.form_body_bgImage !== undefined){
                        $(".example").css("background-image","url('" + parsedObj.form_body_bgImage + "')");
                    }else{
                        $(".example").css("background-image","url()");
                    }
                    $("#form_width").val(parsedObj.form_width).change();
                    $("#form_vertical_margin").val(parsedObj.form_vertical_margin).change();
                    $("#form_Background_color").spectrum("set", parsedObj.form_Background_color);
                    $("#form_opacity").val(parsedObj.form_opacity).change();
                    $("#form_border_size").val(parsedObj.form_border_size).change();
                    $("#form_border_type").val(parsedObj.form_border_type).change();
                    $("#form_border_color").spectrum("set", parsedObj.form_border_color);
                    $("#form_border_radius").val(parsedObj.form_border_radius).change();
                    $("#form_body_bgImage_attach").val(parsedObj.form_body_bgImage_attach).change();
                    $("#form_body_bgImage_position").val(parsedObj.form_body_bgImage_position).change();
                    $("#form_body_bgImage_repet").val(parsedObj.form_body_bgImage_repet).change();
                    $("#form_body_bgImage_size").val(parsedObj.form_body_bgImage_size).change();
                }else{
                    //set dafault
                    setDefaultFormStyleObj();
                }
            }else{
                //new ,set dafault
                setDefaultFormStyleObj();
            }
            general_style_dialog.dialog("open");
        }
        /////////////////////////////////////Custom form style editor////////////////
        function edit_custom_style(){
            var form_id = $("#form_id").val();
            if(form_id != ""){
                clearCustomStyleEditorHestory();
                //get style content from db if exists
                //set the content in $("#form_custom_style_editor_code").text(the_contetn)
                var formStyle = getCustomFormStyle(form_id,"edit");
                if(formStyle == "ERROR"){
                    alert("Error loading custom form style!!!\n see console.log for details.");
                    return false;
                }
                if(formStyle != "" && formStyle != "-1" ){
                    $("#custom_style_editor_type").val("update");
                    custom_style_editor.setValue(formStyle);
                }else{
                    if(formStyle == "-1"){
                        //new
                        $("#custom_style_editor_type").val("new");
                    }else{
                        //update
                        $("#custom_style_editor_type").val("update");
                    }
                    custom_style_editor.setValue("/*custom form style editor*/\n");
                }
                old_custom_style =  custom_style_editor.getValue();

                custom_style_editor_dialog.dialog("open");
                custom_style_editor.refresh();
                custom_style_editor.focus();
                // Set the cursor at the end of existing content
                custom_style_editor.setCursor(custom_style_editor.lineCount(), 0);
            }
        }

        function clearCustomStyleEditorHestory(){
            custom_style_editor.setValue("");
            custom_style_editor.clearHistory();
            //$(".CodeMirror-code div").html("");
        }

        function clearCustomStyleEditor(){
            custom_style_editor.setValue("");
        }

        function undoCustomStyleEditor(){
            custom_style_editor.undo();
        }

        function redoCustomStyleEditor(){
            custom_style_editor.redo();
        }
        function getCustomFormStyle(form_id,type){
            var rt_data = "";
            if(form_id != ""){
                $.ajax({
                    type: "POST",
                    url: "get_custom_form_style.php",
                    async:false,
                    data: {
                        formId : form_id,
                        get_type: type
                    },
                    success: function (response) {
                        //response = JSON.parse(response);
                        rt_data = response;
                    },
                    error:function (response) {
                         rt_data = "ERROR";
                        console.log("Error:",JSON.stringify(response.responseText));
                    }
                });
            }
            return rt_data;
        }

        function setCustomFormStyle(){
            var newFormStyle = custom_style_editor.getValue();
            if(old_custom_style == newFormStyle){
                alert("no change");
                custom_style_editor_dialog.dialog("close");
                return false;
            }
            var frm_id = $("#form_id").val();
            var action_type =  $("#custom_style_editor_type").val();
            //console.log(frm_id,action_type,newFormStyle);
            var frm_data  = {
                record_id: frm_id,
                form_style: newFormStyle,
            };
            var tbl = "form_style";
            ajaxAction(action_type, tbl , frm_data, custom_style_editor_dialog);
        }

        /////////////////////////////////////////////////////////////////////
        function setDefaultFormStyleObj(){
            var defaultSets = '<?=$dFrmSets ?>';
            var defaultFormStylObj = {};
            if(defaultSets != ''){
                defaultFormStylObj = JSON.parse(defaultSets);
            }else{
                defaultFormStylObj = {
                    form_body_direction: "ltr",
                    form_body_bgcolor_1: "rgba(222, 222, 222, 1)", //$("#form_body_bgcolor_1").val(),
                    form_body_bgcolor_2: "rgba(222, 222, 222, 1)", //$("#form_body_bgcolor_2").val(),
                    form_body_bgcoloe_angle: "0", //$("#form_body_bgcoloe_angle").val(),
                    form_width: "80", //$("#form_width").val(),
                    form_vertical_margin: "5", //$("#form_vertical_margin").val(),
                    form_Background_color: "rgba(255, 255, 255, 1)", //$("#form_Background_color").val(),
                    form_opacity: "100", //$("#form_opacity").val(),
                    form_border_size: "1", //$("#form_border_size").val(),
                    form_border_type: "solid", //$("#form_border_type").val(),
                    form_border_color: "rgb(0, 0, 0, 1)", //$("#form_border_color").val(),
                    form_border_radius: "5", //$("#form_border_radius").val(),
                    form_body_bgImage_attach: "scroll",
                    form_body_bgImage_position: "center center",
                    form_body_bgImage_repet: "repeat",
                    form_body_bgImage_size: "auto"
                }
            }
            //console.log(defaultFormStylObj)
             
            sessionStorage.formStylObj = JSON.stringify(defaultFormStylObj);

            $("#form_body_direction").val(defaultFormStylObj.form_body_direction).change();

            $("#form_body_bgcolor_1").spectrum("set", defaultFormStylObj.form_body_bgcolor_1);
            $("#form_body_bgcolor_2").spectrum("set", defaultFormStylObj.form_body_bgcolor_2);
            $("#form_body_bgcoloe_angle").val(defaultFormStylObj.form_body_bgcoloe_angle).change();
            $("#form_width").val(defaultFormStylObj.form_width).change();
            $("#form_vertical_margin").val(defaultFormStylObj.form_vertical_margin).change();
            $("#form_Background_color").spectrum("set", defaultFormStylObj.form_Background_color);
            $("#form_opacity").val(defaultFormStylObj.form_opacity).change();
            $("#form_border_size").val(defaultFormStylObj.form_border_size).change();
            $("#form_border_type").val(defaultFormStylObj.form_border_type).change();
            $("#form_border_color").spectrum("set", defaultFormStylObj.form_border_color);
            $("#form_border_radius").val(defaultFormStylObj.form_border_radius).change();
            
            $("#form_body_bgImage_attach").val(defaultFormStylObj.form_body_bgImage_attach).change();
            $("#form_body_bgImage_position").val(defaultFormStylObj.form_body_bgImage_position).change();
            $("#form_body_bgImage_repet").val(defaultFormStylObj.form_body_bgImage_repet).change();
            $("#form_body_bgImage_size").val(defaultFormStylObj.form_body_bgImage_size).change();
            $("#form_body_bgImage").val(null);

        }
        function getFormStyleObj(name){
            if (sessionStorage.formStylObj) {
                var obj = JSON.parse(sessionStorage.formStylObj);
                if(obj[name] !== undefined && obj[name] !== null){
                    return obj[name];
                }else{
                    return "";
                }
            } else {
                return "";
            }
        }
        function setFormStyleObj(name, val){
            if (sessionStorage.formStylObj) {
                var obj = JSON.parse(sessionStorage.formStylObj);
                obj[name] = val;
                sessionStorage.formStylObj = JSON.stringify(obj);
            }else{
                var obj = {};
                obj[name] = val;
                sessionStorage.formStylObj = JSON.stringify(obj);
            }
        }
        /*
        function rgbToHex(rgbStr) {
            var rgbNumStr = rgbStr.substring(
                rgbStr.lastIndexOf("(") + 1, 
                rgbStr.lastIndexOf(")")
            );
            var rgbNumAry = rgbNumStr.split(",");
            //str = str.replace(/\s+/g, '');
            var r = Number(rgbNumAry[0]);
            var g = Number(rgbNumAry[1]);
            var b = Number(rgbNumAry[2]);
            //r, g, b
            //console.log(rgbStr, "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1))
            return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
        }
        */
       function bytesToSize(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';

            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

            const i = Math.floor(Math.log(bytes) / Math.log(k));

            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
        //preview
        $(".form_style_input").on("click",function(){
            $(this).change();
        });
        $(".form_style_input").on("change",function(){
            var el_id = $(this).attr("id");
            var el_val = $(this).val();
            //console.log(el_id," ,value: ",el_val);
            //form_body_direction,form_body_bgcolor_1, form_body_bgcolor_2 , form_body_bgcoloe_angle , form_body_bgImage  
            //form_width, form_vertical_margin, form_Background_color,form_opacity,form_border_size,form_border_type ,
            //form_border_color,form_border_radius  
            //form_body_bgImage_attach,form_body_bgImage_position,form_body_bgImage_repet,form_body_bgImage_size
            if(el_id == "form_body_direction"){
                //$(".example").css("background-attachment",el_val);
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_body_bgcolor_1"){
                var color1 = el_val;
                var color2 = $("#form_body_bgcolor_2").val();
                var angle = $("#form_body_bgcoloe_angle").val();
                $(".example-body").css("background","linear-gradient(" + angle + "deg, " + color2 + ", " + color1 + ")");
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_body_bgcolor_2"){
                var color1 = $("#form_body_bgcolor_1").val();
                var color2 = el_val;
                var angle = $("#form_body_bgcoloe_angle").val();
                $(".example-body").css("background","linear-gradient(" + angle + "deg, " + color2 + ", " + color1 + ")");
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_body_bgcoloe_angle"){
                var color1 = $("#form_body_bgcolor_1").val();
                var color2 = $("#form_body_bgcolor_2").val();
                var angle = el_val;
                $(".example-body").css("background","linear-gradient(" + angle + "deg, " + color2 + ", " + color1 + ")");
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_body_bgImage"){
                if (this.files && this.files[0]) {
                    var defaultMaxfileSize = '<?=$dFrmMaxBgImgSize ?>';
                    if(defaultMaxfileSize == ''){
                        defaultMaxfileSize = 1048576; //=1MB
                    }else{
                        defaultMaxfileSize = Number(defaultMaxfileSize);
                    }
                    //console.log("file size: ",this.files[0].size,defaultMaxfileSize,);
                    if( this.files[0].size > defaultMaxfileSize){
                        var bToSize = bytesToSize(defaultMaxfileSize, 2);
                        var cbToSize = bytesToSize(this.files[0].size, 2);
                        alert("Sorry, the file is too large.\nFile size must be less than " + defaultMaxfileSize + " Bytes (" +bToSize+ "),\n" +
                            "and your file size is " + this.files[0].size + " Bytes (" +cbToSize+ ").");
                        $(this).val(null);
                        return false;
                    }                 
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var color1 = $("#form_body_bgcolor_1").val();
                        var color2 = $("#form_body_bgcolor_2").val();
                        var angle = $("#form_body_bgcoloe_angle").val();
                        //var img = new Image();
                        //img.src = e.target.result
                        var img = e.target.result.replace(/(\r\n|\n|\r)/gm, "");
                        $(".example").css("background-image","url('" + img + "')");
                        $(".example-body").css("background","linear-gradient(" + angle + "deg, " + color2 + ", " + color1 + ")");
                        //console.log(img);
                        setFormStyleObj(el_id, img);
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            }else if(el_id == "form_body_bgImage_attach"){
                $(".example").css("background-attachment",el_val);
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_body_bgImage_position"){
                $(".example").css("background-position",el_val);
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_body_bgImage_repet"){
                $(".example").css("background-repeat",el_val);
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_body_bgImage_size"){
                $(".example").css("background-size",el_val);
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_width"){
                $(".example-form-warper-1").css("width",el_val + "%");
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_vertical_margin"){
                $(".example-form-warper-1").css("transform", "translateY(" + el_val + "%)");
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_Background_color"){
                $(".example-form-warper-1").css("background-color",el_val);
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_opacity"){
                $(".example-form-warper-1").css("opacity",(el_val/100));
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_border_size"){
                $(".example-form-warper-1").css("border-width",el_val + "px");
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_border_type"){
                $(".example-form-warper-1").css("border-style",el_val);
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_border_color"){
                $(".example-form-warper-1").css("border-color",el_val);
                setFormStyleObj(el_id, el_val);
            }else if(el_id == "form_border_radius"){
                $(".example-form-warper-1").css("border-radius",el_val + "px");
                setFormStyleObj(el_id, el_val);
            }
        })
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