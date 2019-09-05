<?php
require 'settings/tracy-2.6.2/src/tracy.php';
use Tracy\Debugger;
if(!isset($isGetSetting)){
    require 'get_setting_data.php';
}
$appMode = getSetting("", "appMode");
if($appMode == "0"){
    //Debug mode
    Debugger::enable();
}
$formId = "";
if(isset($_GET["id"])){
    $formId = $_GET["id"];
}
//if($formId == ""){
 //   die("No form to Render!!!");
//}
if (!preg_match('/^[0-9]+$/', $formId) || $formId == "") {
    die("Error: wrong or missing form id!!!");
}
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['rederect_url'] = "form_admin";

$_SESSION['form_id'] = $formId;

require 'settings/database.login.php';

$formDataAry = getFormData($conn, $formId);
$formType = $formDataAry[0];
$formStatus = $formDataAry[1];
$formAmins = $formDataAry[2];
$formTitle = $formDataAry[3];
$formStyle = $formDataAry[4];


//echo "Typr:$formType , Status:$formStatus, formAdmins: $formAmins, title: $formTitle <br>";

$message = '';
if($formStatus == "2"){
     //die("This form is unpublished!!!");
}

$isAdmin = true;
 $user = "";
if(isset($_SESSION['user_id'])){
    $userId = $_SESSION['user_id'];
    $records = $conn->prepare('SELECT * FROM users WHERE status="1" AND id = :userid');
    $records->bindParam(':userid', $userId);
    $records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    $message = '';
    if($results != "" && count($results) > 0){
        $user = $_SESSION['user_id'];
        //$email = $results['email'];
        //$userName = $results['username'];
        $init_data_ary = init($conn, $formId, $user,$formAmins);
        
        if($formType == "" && $formStatus == "" && $formAmins == "" && $formTitle == ""){
            $isAdmin = false;
            $message = '<label class="text-danger">Sorry, form not exist</label>';
            $col_names = "";
        }else{
            $init_data_ary = init($conn, $formId, $user,$formAmins);
            $isAdmin = (($init_data_ary[0] == "adminuser")?true:false);
            $message = $init_data_ary[1];
            $col_names = $init_data_ary[2];
        }
    }else{
        $message = '<label class="text-danger">Sorry, Username does not exist or is suspended</label>';
    }


}
if($isAdmin && (isset($col_names) && $col_names == "")){
    $message .= '<label class="text-danger">Sorry, There are no records for this form</label><br>';
}
function init($conn, $formId, $user,$formAmins){
    //check if this user is admin of this form
    $admins_ary = array();
    if(strpos($formAmins,",") !== false){
        $admins_ary = explode(",",$formAmins);
    }else{
        $admins_ary[] = $formAmins;
    }
    if(in_array($user, $admins_ary)){
        $formLabelsAry = getFormLabels($conn, $formId);
        //echo "formLabelsAry: ".json_encode($formLabelsAry);
        if(!empty($formLabelsAry)){
            $labeld_ary = array();
            //table index
            $labeldObj0 = new stdClass();
            $labeldObj0->index = 0;
            $labeldObj0->title = "";
            $labeld_ary[] = $labeldObj0;

            $labeldObjUIdIp = new stdClass();
            $labeldObjUIdIp->index = 1;
            $labeldObjUIdIp->title = "User ID/IP";
            $labeld_ary[] = $labeldObjUIdIp;

            $labeldObjdatetime = new stdClass();
            $labeldObjdatetime->index = 2;
            $labeldObjdatetime->title = "Date-time";
            $labeld_ary[] = $labeldObjdatetime;

            $lastIdx = 0;
            foreach($formLabelsAry as $key=>$label){
                $labeldObj = new stdClass();
                $labeldObj->index = ($key + 3);
                $labeldObj->title = $label;
                $labeld_ary[] = $labeldObj;
                $lastIdx = ($key + 3);
            }
            $labeldObjz = new stdClass();
            $labeldObjz->index = $lastIdx + 1;
            $labeldObjz->title = "";
            $labeld_ary[] = $labeldObjz;
            $labels_str = json_encode($labeld_ary, JSON_UNESCAPED_UNICODE);
            return ["adminuser","",$labels_str];
        }else{
            return ["adminuser","",""];
        }
    }else{
        $msg = '<label class="text-danger">Sorry, You are not a Admin of this form</label>';
        return ["noadmin",$msg,""];
    }
}

function getFormData($conn, $formId){
    $records = $conn->prepare('SELECT * FROM form_list WHERE indx = :formid');
	$records->bindParam(':formid',$formId);
	$records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);
    if($results != "" && count($results) > 0){
        return [
            $results['publish_type'],
            $results['publish_status'],
            $results['admin_users'],
            $results['form_title'],
            $results['form_genral_style']
        ];
    }else{
        return ["","","","",""];
    }
}

function getFormLabels($conn, $formId){
    $records = $conn->prepare('SELECT form_labels FROM form_content WHERE form_id = :formid');
	$records->bindParam(':formid',$formId);
	$records->execute();
    $results = $records->fetch(PDO::FETCH_ASSOC);

    return json_decode($results['form_labels']);
}



////////get settings ///////
$isRegistrationEnabled = getSetting("", "enableUserRegistration");
$isPassRecoveryEnabled = getSetting("", "enableUserPasswordRecovery");

//echo "isRegistrationEnabled: ".$isRegistrationEnabled;

include "settings/about.php";
$about_html = ABOUT_APP_AUTHOR;

////////////////////////Form Style params/////////////
//echo "Style: $formStyle";
if($formStyle != null && $formStyle != ""){
    $formStyleObj = json_decode($formStyle);

    $formDirection = $formStyleObj->form_body_direction;//ltr,rtl
    if($formDirection == "rtl"){
        $bodyDirection = "right";
    }else{
        $bodyDirection = "left";
    }
}else{
    $formDirection = "ltr";
    $bodyDirection = "left";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <title><?= $formTitle . " - Manager" ?></title>
    
    <?php if($isAdmin && !empty($user) ): ?>


    <link rel="stylesheet" href="./include/fonts/fontawesome/css/fontawesome-all.min.css">
    <!--<link rel="stylesheet" href="./fonts/fontawesome/css/awesome-bootstrap-checkbox.css">-->

    <link rel="stylesheet" href="./include/bootstrap/css/bootstrap.min.css">
    
    <link rel="stylesheet" href="./include/jquery_ui/themes/start/jquery-ui.min.css">
    
    <link rel="stylesheet" href="./include/select2/dist/css/select2.min.css">

    <script src="./include/jquery/jquery-1.12.4.min.js"></script>
    <script src="./include/jquery_ui/jquery-ui.min.js"></script>
    
    <!--///////////// For Internet Explorer 11 polyfill ///////////////-->
    <script type="text/javascript">
    if(/MSIE \d|Trident.*rv:/.test(navigator.userAgent))
        document.write('<script src="./include/formbuilder/polyfill-4ie11.js"><\/script>');
    </script>
    <!--///////////////////////////////////////////////////////-->
    <script src="./include/formbuilder/form-builder.min.js"></script>
    <script src="./include/formbuilder/form-render.min.js"></script>
    <script src="./include/formbuilder/control_plugins/buttons.js"></script>
    <script src="./include/formbuilder/control_plugins/table.js"></script>

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

    <!-- full dialog -->
    <script src="./include/fulldialog/jqueryui.dialog.fullmode.js"></script>

    <script type="text/javascript" src="./include/select2/dist/js/select2.min.js"></script>
    
    <link rel="stylesheet" href="./css/formadmin_main.css">
    <style>
        /*RTL form preview*/
        #form-content-warper{
            direction: <?=$formDirection ?>;
        }
        #form-content-warper form{
            text-align: <?=$bodyDirection ?>;
        }
    </style>
    <script type="text/javascript">
        var form_content_dialog,general_dialog;
        $(function () {
            var isFullMode = false;
            form_content_dialog = $("#form-content-warper").dialog({
                modal: true,
                autoOpen: false,
                dialogClass: "dialog-full-mode",
                width: 0.9*$(window).width(),
                height: 0.9*$(window).height(),
                close: function( event, ui ) {
                    isFullMode = false;
                }/*,
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
                dialogClass: "dialog-full-mode",
                width: 0.5*$(window).width(),
                height: 0.5*$(window).height(),
                close: function( event, ui ) {
                    isFullMode = false;
                }
            });

            
            $(document).dialogfullmode();
            
            
            $("#main-vewer-menu").popmenu({
                'width': '100px',         // width of menu
                'top': '0',              // pixels that move up
                'left': '0',              // pixels that move left
                'iconSize': '50px' // size of menu's buttons
            });
        });
    

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
    <span id="main-vewer-menu">
        <span class="pop_ctrl"><i class="all_btns fa fa-bars"></i></span>
        <ul>
            <li onclick="addUpdateUser('update','<?= $user ?>')" title="User info"><div><i class="fa fa-user"></i></div><div class="menu-icons-text">info</div></li>
            <li onclick="showAboutForm('<?=$formId?>')" title="Form mata-data"><i class="fa fa-file"></i><div class="menu-icons-text">Form</div></li>
            <li onclick="openLinkInNewTab('https://github.com/meshesha/SimplePhpFormBuilder/wiki')" title="Help"><i class="fa fa-question-circle"></i><div class="menu-icons-text">Help</div></li>
            <li onclick="showAbout()" title="About"><i class="fa fa-info-circle"></i><div class="menu-icons-text">About</div></li>
            <li onclick="javascript:location.href='index.php'" title="Go to admin page"><i class="fa fa-cog"></i><div class="menu-icons-text">Admin</div></li>
            <li onclick="javascript:location.href='logout.php'" title="Exit from system"><i class="fa fa-power-off"></i><div class="menu-icons-text">Exit</div></li>
        </ul>
    </span>
    <div class="main_warper">
        <div class="ui-widget-header" style="text-align:center;"><h2><?= $formTitle ?></h2></div>
        <div class="form_data_warper ui-widget-content" style="padding: 5px;">
            <?php if(!empty($message)): ?>
                <p class="ui-widget-content" style='text-align:center;'><?= $message ?></p>
            <?php endif; ?>
            <table id="form_data_table" class="display" style="width:100%;"></table>
        </div>
    </div>
    
    <div class="form-warper" id="form-content-warper"  title="<?= $formTitle ?>">
        <form method="POST" action="form_process.php" enctype="multipart/form-data">
            <input type="hidden" name="user_id" value="" />
            <input type="hidden" name="user_name" value="" />
            <input type="hidden" name="user_email" value="" />
            <div id="form-render-content"></div>
        </form>
    </div>
    
    <div id="formbuilder_general_dialog">
        <div id="formbuilder_general_content"></div>
    </div>

    <script>
        var form_data_tbl;
        load_form_data();

        function load_form_data(){
            var form_id = "<?php echo $formId ?>";
            var columnsStr = '<?php echo $col_names ?>';
            if(columnsStr != ''){
                var tbl_columns_names = JSON.parse(columnsStr);
                var lastIdx = tbl_columns_names.length - 1;
                //console.log("tbl_columns_names: ",tbl_columns_names)
                form_data_tbl = $('#form_data_table').DataTable({
                    language: {
                        url: './include/DataTables/i18n/Hebrew.json'
                    },
                    ajax: {
                        url: "get_form_data_table.php",
                        type: "POST",
                        data: {
                            data_indx : form_id
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
                            "targets": [0],
                            "render": function(data, type, row, meta){
                                /**https://www.gyrocode.com/projects/jquery-datatables-checkboxes/ */
                                if(type === 'display'){
                                    data = '<div class="checkbox"><input type="checkbox" class="dt-checkboxes row_checkbox"><label></label></div>';
                                }
                                return data;
                            },
                            'checkboxes': {
                                'selectRow': true,
                                'selectAllRender': '<div class="checkbox"><input type="checkbox" class="dt-checkboxes"><label></label></div>'
                            }
                        },
                        {
                            "targets": [-1],
                            "searchable": false,
                            "orderable": false
                        }
                    ],
                    'select': {
                        'style': 'multi',
                        'selector': 'td:first-child'
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        { 
                            text: 'Delete all select',
                            className: 'btn btn-danger btn-sm',
                            action: function (e, dt, node, config) {
                                var arr = [];
                                var totalCols = form_data_tbl.columns().nodes().length;
                                $('.row_checkbox:checked').each(function (val, i) {
                                    //var lastCell = form_data_tbl.row( $(this).parents('tr') ).data()[totalCols-1];
                                    //console.log(lastCell)
                                    //arr.push($(".form_id_uid", lastCell).val());
                                    var sel = $(this).parents('tr').find($(".form_id_uid")).val();
                                    //console.log(sel)
                                    arr.push(sel);
                                }); 
                                //console.log(JSON.stringify(arr))
                                if(arr.length == 0){
                                    alert("No selection!")
                                }else{
                                    if(confirm("Are you sure you want to delete all selected records?")){
                                        var frm_data = {
                                            del_type: "multi",
                                            data: JSON.stringify(arr)
                                        }

                                        ajaxAction("delete", "user_data" , frm_data);
                                    }
                                }
                            }
                        },
                        {
                            extend: 'colvis',
                            className: 'btn btn-info btn-sm',
                            text: 'hide/show columns',
                            columns: function(idx, data, node){
                                return (idx == 0 || idx == lastIdx)?false:true;
                            }
                        },
                        {  
                            extend:"excel",
                            className: 'btn btn-info btn-sm',
                            footer: true,
                            text: 'xlsx',
                            exportOptions: {
                                columns: ':visible',
                                columns: function(idx, data, node){
                                    return (idx == 0 || idx == lastIdx)?false:true;
                                }
                            }
                        },
                        {  
                            extend:"copy",
                            className: 'btn btn-info btn-sm',
                            footer: true,
                            text: 'Copy',
                            exportOptions: {
                                columns: ':visible',
                                columns: function(idx, data, node){
                                    return (idx == 0 || idx == lastIdx)?false:true;
                                }
                            }
                        }
                    ],
                    order: [
                        [ 1, 'asc' ]/*,
                        [ 4, 'asc' ]*/
                    ],
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
                /*
                form_data_tbl.on( 'order.dt search.dt', function(){
                    form_data_tbl.column(0, {search:'applied', order:'applied'}).nodes().each(function(cell, i){
                        cell.innerHTML = (i+1) + ' <input type="checkbox" class="row_checkbox" value="' +(i+1)+ '" />';
                    });
                }).draw();
                */
            }
        }
        function getFormDetails(form_id, uid){
            //console.log(form_id, uid)
            if(form_id != "" && uid != ""){
                var form_content = get_form_content(form_id, uid);
                if(form_content != "new" && form_content != "" && form_content != null && form_content !== undefined){
                    var form_content_obj = JSON.parse(form_content);
                    //console.log(form_content_obj);
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
                    $('#form-render-content').formRender({
                        dataType: 'json',
                        formData: form_content
                    });
                    
                    $(".user-data-content-warper").hide();
                    $(".hide-show-user-data").on("click",function(){
                        $(".user-data-content-warper").toggle("slow");
                    })
                    form_content_dialog.dialog("open");
                }else{
                    alert("No form to Render!!!");
                }
            }
        }
        function get_form_content(form_id, uid){
            var rt_data = "";
            if(form_id != ""){
                //ajax
                $.ajax({
                    type: "POST",
                    url: "get_form_content_data.php",
                    async:false,
                    data: {
                        form_id : form_id,
                        form_uid: uid
                    },
                    success: function (response) {
                        rt_data = response;
                        //console.log(response);
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
        function delFormRecord(form_id, uid){
            //console.log(form_id, uid)
            if(confirm("Are you sure you want to delete this record?")){
                var frm_data = {
                    del_type: "single",
                    form_id: form_id,
                    uid: uid
                }

                ajaxAction("delete", "user_data" , frm_data);
            }
        }

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
                        //console.log(response);
                        if(response == "success"){
                            if (dialogbox !== undefined && dialogbox.hasClass('ui-dialog-content')){
                                dialogbox.dialog("close");
                            }
                            if(tbl == "user_data"){
                                load_form_data();
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
        ///////////////////Users///////////////////////////
        function addUpdateUser(action,usr_id){
            var gContent = $("#formbuilder_general_content");
            gContent.addClass("dialog_form_container");
            gContent.html("");
            var hInput = "<input type='hidden' id='action_type' />";
            $(hInput).val(action).appendTo(gContent);
            var uhInput = "<input type='hidden' id='user_id' value = '" + usr_id + "' />";
            $(uhInput).appendTo(gContent);

            var user_data = "", userName, userPass , userEmail, userGroups,userDep, userStatus;
            if(action == "update"){
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
            var uInput = "<input type='text' id='user_name' value = '" + userName + "' disabled  />";
            var uName = addElement("User Name","user_name", uInput);
            uName.appendTo(gContent);
            var pInput = "<input type='password' id='user_password' value = '$%#$$###^$%' />"; //" + userPass + "
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
            setGroupsList(userGroups);
            
            // multiple='multiple'
            var gInput = "<select id='depsList' class='depslist js-states form-control' style='width:80%;'></select>";
            var uDeps = addElement("Department","depsList", gInput);
            uDeps.appendTo(gContent);
            setDepartmentsList(userDep);

            if(action == "update"){
                var sInput = "<select id='user_status'><option value='0'>Inactive</option><option value='1'>Active</option></select>";
                var uStatus = addElement("Status","user_status", sInput);
                uStatus.appendTo(gContent);
                $("#user_status").val(userStatus).change().attr("disabled", true);
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
            general_dialog.dialog("option","title","Update user data");
            
            general_dialog.dialog("open");

            $("#main-vewer-menu ul").hide();
        }
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
            console.log("pass: ",usr_pass)
            var conf_pass = $("#confirm_password").val();
            var isPassChange = "-1";
            if(usr_pass != "" && conf_pass != "" && usr_pass !== conf_pass){
                alert("confirm password - Passwords Don't Match");
                return false;
            }else{
                isPassChange = "1";
            }
            var usr_email = $("#user_email").val();
            var usr_groups = $("#groupList").val();
            var usr_pblsh_stt = $("#user_status").val();
            var usr_data  = {
                changePass: isPassChange,
                record_id: usr_id,
                user_name: usr_name,
                user_pass: usr_pass,
                user_email: usr_email/*,
                publish_groups: usr_groups,
                user_status: usr_pblsh_stt*/
            };
            var tbl = "users";
            //console.log(usr_data)
            ajaxAction(action, tbl , usr_data,dialogBox);
        }
        function delete_user(user_id){
            if(confirm("Are you sure you want to delete this user?")){
                var frm_data = {
                    record_id: user_id
                }
                ajaxAction("delete", "users" , frm_data);
            }
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

        function setGroupsList(selectedAry){
            $('.groupslist').empty();
            $('.groupslist').select2({
                disabled: true,
                ajax: {
                    url: 'get_all_groups.php',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    async:false,
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
                // manually trigger the `select2:select` event
                $multiSelectGroups.trigger({
                    type: 'select2:select',
                    params: {
                        data: data
                    }
                });
            });
        }

        function setDepartmentsList(selectedAry){
            $('.depslist').empty();
            $('.depslist').select2({
                disabled: true,
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
        ///////////////////////////////////////About form /////////////////
        function showAboutForm(formId){
            /*
            var publishTypeName = {
                "1":"Public",
                "2":"Users group",
                "3":"Public-Anonymously",
                "4":"Groups-Anonymously"
            };
            */
            var statusTypeName = {"1":"Published","2":"Unpublished"};
            var allGroups="",allDeps="" , allFormMngrs = "";
            var gContent = $("#formbuilder_general_content");
            gContent.addClass("dialog_form_container");
            gContent.html("");
            var form_data = getFormData(formId);

            if(form_data !== null && form_data !== undefined){
                allGroups = getAllGroups();
                allFormMngrs = getAllFormMngrs();
                allDeps = getAllDeps();
            }
            var formName =  form_data.data.frm_name;
            var formTitle = form_data.data.frm_title;
            var publishTypeId = form_data.data.publ_type;
            var publishTypeName = form_data.data.publ_type_name;

            var formGroupsIds = form_data.data.publ_grps;
            var formGroupsNames = "";
            if(allGroups != "" && formGroupsIds != "" ){
                var groupsIdsAry = [];
                var isSingleGrp = false;
                if(formGroupsIds.indexOf(",") > -1){
                    groupsIdsAry = formGroupsIds.split(",");
                }else{
                    isSingleGrp = true;
                    groupsIdsAry[0] = formGroupsIds;
                }
                var allGroupsObj = JSON.parse(allGroups);
                var allGroupsObjAry = allGroupsObj.results;
                //console.log(allGroupsObjAry)
                $.each(allGroupsObjAry, function(i, grp ) {
                    if(isSingleGrp){
                        if(groupsIdsAry[0] == grp.id){
                            formGroupsNames = grp.text;
                        }
                    }else{
                        $.each(groupsIdsAry, function(i, grpId ) {
                            if(grpId == grp.id){
                                formGroupsNames += grp.text + ", ";
                            }
                        });
                    }
                });
            }
            var formDepsIds = form_data.data.publ_deps;
            var formDepsNames = "";
            if(allDeps != "" && formDepsIds != "" ){
                var depsIdsAry = [];
                var isSingleDep = false;
                if(formDepsIds.indexOf(",") > -1){
                    depsIdsAry = formDepsIds.split(",");
                }else{
                    isSingleDep = true;
                    depsIdsAry[0] = formDepsIds;
                }
                var allDepsObj = JSON.parse(allDeps);
                var allDepsObjAry = allDepsObj.results;
                //console.log(allDepsObjAry)
                $.each(allDepsObjAry, function(i, dep ) {
                    if(isSingleDep){
                        if(depsIdsAry[0] == dep.id){
                            formDepsNames = dep.text;
                        }
                    }else{
                        $.each(depsIdsAry, function(i, depId ) {
                            if(depId == dep.id){
                                formDepsNames += dep.text + ", ";
                            }
                        });
                    }
                });
            }

            var formMngrsIds = form_data.data.admin_users;
            var formMngrsNames = "";
            if(allFormMngrs != "" && formMngrsIds != "" ){
                var mngrsIdsAry = [];
                var isSingleMngr = false;
                if(formMngrsIds.indexOf(",") > -1){
                    mngrsIdsAry = formMngrsIds.split(",");
                }else{
                    isSingleMngr = true;
                    mngrsIdsAry[0] = formMngrsIds;
                }
                var allFormMngrsObj = JSON.parse(allFormMngrs);
                var allFormMngrsObjAry = allFormMngrsObj.results;
                //console.log(allGroupsObjAry)
                if(allFormMngrsObjAry.length > 0){
                    $.each(allFormMngrsObjAry, function(i, mngr ) {
                        if(isSingleMngr){
                            if(mngrsIdsAry[0] == mngr.id){
                                formMngrsNames = mngr.text;
                            }
                        }else{
                            $.each(mngrsIdsAry, function(i, mngrId ) {
                                if(mngrId == mngr.id){
                                    formMngrsNames += mngr.text + ", ";
                                }
                            });
                        }
                    });
                }
            }
            statusTypeId = form_data.data.publ_status;
            formNots = form_data.data.frm_note;

            var uInput = "<input type='text' id='form_name' value = '" + formName + "' disabled />";
            var uName = addElement("Form name","form_name", uInput);
            uName.appendTo(gContent);

            var uInput = "<input type='text' id='form_title' value = '" + formTitle + "' disabled />";
            var uName = addElement("Form title","form_title", uInput);
            uName.appendTo(gContent);
            //publishTypeName[publishTypeId]
            var uInput = "<input type='text' id='publish_type' value = '" + publishTypeName + "' disabled  />";
            var uName = addElement("Publish type","publish_type", uInput);
            uName.appendTo(gContent);

            if(publishTypeId == "2" || publishTypeId == "4"){
                var uInput = "<input type='text' id='groups_list' value = '" + formGroupsNames + "' disabled  />";
                var uName = addElement("Groups","groups_list", uInput);
                uName.appendTo(gContent);
            }else if(publishTypeId == "5" || publishTypeId == "6"){
                var uInput = "<input type='text' id='deps_list' value = '" + formDepsNames + "' disabled  />";
                var uName = addElement("Departments","deps_list", uInput);
                uName.appendTo(gContent);
            }

            var uInput = "<input type='text' id='form_managers_list' value = '" + formMngrsNames + "' disabled />";
            var uName = addElement("Form Managers","form_managers_list", uInput);
            uName.appendTo(gContent);

            var uInput = "<input type='text' id='status_type' value = '" + statusTypeName[statusTypeId] + "' disabled  />";
            var uName = addElement("Status","status_type", uInput);
            uName.appendTo(gContent);

            var frmDir = '<?=$formDirection ?>';
            frmDir = frmDir.toUpperCase() + " (" + ((frmDir == "ltr")?"Left To Right":"Right To Left") + ")";
            var uInput = "<input type='text' id='form_direction' value = '" + frmDir + "' disabled  />";
            var uName = addElement("Form direction","form_direction", uInput);
            uName.appendTo(gContent);

            var uInput = "<textarea id='form_note' disabled>" + formNots + "</textarea>";
            var uName = addElement("Note","form_note", uInput);
            uName.appendTo(gContent);

            general_dialog.dialog("option","buttons",
                [
                    {
                        text: "Cancel",
                        class: "btn btn-primary btn-lg",
                        click: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                ]
            );

            general_dialog.dialog("option","height",0.8*$(window).height());
            general_dialog.dialog("option","title","Form Data");
            general_dialog.dialog("open");
            $("#main-vewer-menu ul").hide();
        }
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

        function getAllGroups(){
            var rt_data = "";
                //ajax
                $.ajax({
                    type: "POST",
                    url: "get_all_groups.php",
                    async:false,
                    success: function (response) {
                        //response = JSON.parse(response);
                        rt_data = response;
                    },
                    error:function (response) {
                        console.log("Error:",JSON.stringify(response));
                        alert(response.responseText)
                    }
                });
            return rt_data;
        }

        function getAllDeps(){
            var rt_data = "";
                //ajax
                $.ajax({
                    type: "POST",
                    url: "get_all_deps.php",
                    async:false,
                    success: function (response) {
                        //response = JSON.parse(response);
                        rt_data = response;
                    },
                    error:function (response) {
                        console.log("Error:",JSON.stringify(response));
                        alert(response.responseText)
                    }
                });
            return rt_data;
        }

        function getAllFormMngrs(){
            var rt_data = "";
                //ajax
                $.ajax({
                    type: "POST",
                    url: "get_all_managers_users.php",
                    async:false,
                    success: function (response) {
                        //response = JSON.parse(response);
                        rt_data = response;
                    },
                    error:function (response) {
                        console.log("Error:",JSON.stringify(response));
                        alert(response.responseText)
                    }
                });
            return rt_data;
        }
    </script>

<?php elseif($isAdmin &&  empty($user)) : ?>
	<link rel="stylesheet" type="text/css" href="css/main.css">
	<div class="container-login100" style="background-image: url('images/bg05.jpg');">
		<div>
			<h1>Login</h1>
            <?php if(!empty($message)): ?>
                <br><p class="ui-widget-content" style='text-align:center; padding:3px;'><?= $message ?></p><br>
            <?php endif; ?>
			<div class="container-login100-form-btn p-t-10">
			<a class="login100-form-btn" href="login.php">Login</a> </div><!-- or
			<a href="register.php">Register</a> -->
		</div>
	</div>

<?php else : ?>

        <?php if(!empty($message)): ?>
            <p><?= $message ?></p>
        <?php endif; ?>

 <?php endif; ?>
</body>
</html>