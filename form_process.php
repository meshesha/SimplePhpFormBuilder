<?php

require 'settings/tracy-2.6.2/src/tracy.php';
use Tracy\Debugger;

//include "chech_restricted.php";

require "settings/database.class.php";

////////get settings ///////
if(!isset($isGetSetting)){
    require 'get_setting_data.php';
}
$appMode = getSetting("", "appMode");
if($appMode == "0"){
    //Debug mode
    Debugger::enable();
}
//GET default max file size
$defaultMaxFileSize = getSetting("", "maxFileSeize");

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$formId = $_SESSION['form_id'];
if(!isset($_SESSION['is_form_reload']) || $_SESSION['is_form_reload']==""){
    $_SESSION['is_form_reload'] = "0";
}

$errors = array();

if(isset($_POST['button-submit-form']) &&  $_SESSION['is_form_reload'] == "0"){
    $_SESSION['is_form_reload'] = "1";
    $form_id = $_POST["hidden-form-id"];
    $userId = $_POST["user_id"];
    $userName = $_POST["user_name"];
    $email = $_POST["user_email"];
    
    $echo_data = "";

    $uid = "";
    if($userId != "" && $userName != "" && $email != ""){
       $uid = md5(time()); 
    }else{
        $uid = "public-" . md5(time());
    }
    $db = new Database("formbuilder");
    $conn = $db->getConnection();
    
    $frmFields = getFormFieldsNames($conn, $form_id);
    
    if($frmFields != "" && $frmFields !== null){
        $frmFieldsNamesAry = json_decode($frmFields);
        $frmFieldsValuesAry = array();
        foreach($frmFieldsNamesAry as $field){
            if($field->type != "file"){
                if($field->type == "checkbox-group"){
                    if(isset($_POST[$field->name])){
                        $checkBox = $_POST[$field->name];
                        if(empty($checkBox)) {
                            //echo "You didn't select any ".$field->name;
                            $frmFieldsValuesAry[] = [$field->name,"",$field->type];
                        }else{
                            $checkCount = count($checkBox);
                            $checkAry = array();
                            $checkStr = "";
                            //echo "You selected $checkCount : ".$field->name."(s)";
                            for($i=0; $i < $checkCount; $i++){
                                //echo $checkBox[$i] . "<br>";
                                $checkAry[] = $checkBox[$i];
                            }
                            //$checkStr = implode(",",$checkAry);
                            $checkStr = json_encode($checkAry);
                            $frmFieldsValuesAry[] = [$field->name,$checkStr,$field->type];
                        }
                    }else{
                        //echo "You didn't select any ".$field->name;
                        $frmFieldsValuesAry[] = [$field->name,"",$field->type];
                    }
                }else if($field->type == "radio-group"){
                    if(isset($_POST[$field->name])){
                        $frmFieldsValuesAry[] = [$field->name, $_POST[$field->name],$field->type];
                    }else{
                         //echo "You didn't select any ".$field->name;
                        $frmFieldsValuesAry[] = [$field->name, "",$field->type];
                    }
                }else if($field->type == "table"){
                    $tbl_name = $field->name;
                    $tbl_data_field_name = "editable-data-$tbl_name";
                    $tbl_data_field_val = $_POST[$tbl_data_field_name];
                    //$conn,$userId,$uid,$form_id,$tbl_name,$tbl_data_field_val
                    //echo $tbl_name . ", " . $field->type .  ", $tbl_data_field_val<br>";
                    $tbl_stt = setTableInDB($conn,$form_id,$uid,$tbl_name,$tbl_data_field_val);
                    if($tbl_stt != "success"){
                        $frmFieldsValuesAry[] = [$tbl_name, "error",$field->type];
                        $errors[] = "upload error (single) : ". $upload_stt_ary[1]."<br>";
                    }else{
                        $frmFieldsValuesAry[] = [$tbl_name, "yes",$field->type];
                    }
                }else{
                    $frmFieldsValuesAry[] = [$field->name, $_POST[$field->name],$field->type];
                }
               
            }else{
                //files upload hndler 
               if(!empty($_FILES)){ //isset($_FILES[$field->name]) => !empty($_FILES[$field->name])
                    if(isset($field->multiple) && $field->multiple){
                        $multiFiles = true;
                    }else{
                        $multiFiles = false;
                    }
                    if(!$multiFiles){
                        $error = $_FILES[$field->name]['error'];
                        if($error == 4 || $error == UPLOAD_ERR_NO_FILE){
                            $frmFieldsValuesAry[] = [$field->name, "no",$field->type];
                            //echo "No file was uploaded<br>";
                        }else{
                            //1KB = 1024 Bytes;
                            //1MB = 1048576 Bytes
                            $AllowedMaxFileSeize = (int)$defaultMaxFileSize;//1048576; //Bytes
                            if(isset($field->fileSize)){
                                $fileSeizeUnit = $field->sizeUnits;
                                $maxFileSeize = $field->fileSize;
                                if($fileSeizeUnit == "bytes"){
                                    $AllowedMaxFileSeize = (int)$maxFileSeize;
                                }else if($fileSeizeUnit == "kB"){
                                    $AllowedMaxFileSeize = (1024 * (int)$maxFileSeize);
                                }else if($fileSeizeUnit == "MB"){
                                    $AllowedMaxFileSeize = (1048576 * (int)$maxFileSeize);
                                }
                            }
                            $upload_stt_ary = uploadFile($conn,$form_id,$uid,$_FILES[$field->name],false,$AllowedMaxFileSeize);
                            if($upload_stt_ary[0] == 0){
                                $frmFieldsValuesAry[] = [$field->name, "error",$field->type];
                                $errors[] = "upload error (single) : ". $upload_stt_ary[1]."<br>";
                            }else{
                                $frmFieldsValuesAry[] = [$field->name, "yes",$field->type];
                                //$errors[] = "upload success (single) : ". $upload_stt_ary[1]."<br>";
                            }
                        }
                    }else{
                        $error = $_FILES[$field->name]['error'];
                        //echo "Check error (multi): ". json_encode($error);
                        if($error[0] == 4 ){
                            $frmFieldsValuesAry[] = [$field->name, "no",$field->type];
                        }else{
                            //1KB = 1024 Bytes;
                            //1MB = 1048576 Bytes
                            $AllowedMaxFileSeize = (int)$defaultMaxFileSize;//1048576; //Bytes
                            if(isset($field->fileSize)){
                                $fileSeizeUnit = $field->sizeUnits;
                                $maxFileSeize = $field->fileSize;
                                if($fileSeizeUnit == "bytes"){
                                    $AllowedMaxFileSeize = (int)$maxFileSeize;
                                }else if($fileSeizeUnit == "kB"){
                                    $AllowedMaxFileSeize = (1024 * (int)$maxFileSeize);
                                }else if($fileSeizeUnit == "MB"){
                                    $AllowedMaxFileSeize = (1048576 * (int)$maxFileSeize);
                                }
                            }
                            $upload_stt_ary = uploadFile($conn,$form_id,$uid,$_FILES[$field->name],true,$AllowedMaxFileSeize);
                            if($upload_stt_ary[0] == 0){
                                $frmFieldsValuesAry[] = [$field->name, "error",$field->type];
                                $errors[] = "upload error (multi) : ". $upload_stt_ary[1]."<br>";
                            }else{
                                $frmFieldsValuesAry[] = [$field->name, "yes",$field->type];
                                //$errors[] = "upload success (multi) : ". $upload_stt_ary[1]."<br>";
                            }
                        }
                    }
               }else{
                    $frmFieldsValuesAry[] = [$field->name, "no",$field->type];
                    //echo "file upload error (empty)<br>";
               }
                        
            }
        }
        //echo "<br>";
        foreach($frmFieldsValuesAry as $field){
            //echo "name: ".$field[0].", val: ".$field[1].",type: ".$field[2]."<br>";
            $setFormStt = setFormData($conn,$userId,$uid,$form_id,$field[0] ,$field[2] ,$field[1]);
            if($setFormStt != "success"){
                $errors[] = $setFormStt;
            }else{
                $setFormDateStt = setDataDatetime($conn,$uid,$form_id);
                if($setFormDateStt != "success"){
                    $errors[] = $setFormDateStt;
                }
            }
        }
    }
}else if(isset($_POST['button-submit-form']) &&  $_SESSION['is_form_reload'] == "1"){
    unset($_SESSION['is_form_reload']);
    header("Location: form.php?id=$formId");
}else{
    $errors[] = 'Error uploading form';
}

$message = "";
if(empty($errors)){
    $message = "<label class='text-success'>Form uploaded successfully </label>";
}else{
    $msg = implode("<br>", $errors);
    $message = "<label class='text-danger'>$msg</label>";
}

function getUserIp(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    if($ip == "::1"){
        $ip = "127.0.0.1";
    }
    return $ip;
}

function checkIfFilesUpload($fielName, $isMulti){
    // bail if there were no upload forms
   if(empty($_FILES))
        return false;

    // check for uploaded files
    $files = $_FILES[$fielName]['tmp_name'];
    foreach( $files as $field_title => $temp_name ){
        if( !empty($temp_name) && is_uploaded_file( $temp_name )){
            // found one!
            return true;
        }
    }   
    // return false if no files were found
   return false;
}

function getFormFieldsNames($conn, $form_id){
    $echo_data = "";
    $sql = "SELECT * FROM form_content WHERE form_id='$form_id'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
               $echo_data =  $row['submit_fields'] ; 
            }
        }
    }
    return $echo_data;
}
function setFormData($conn,$usrId,$uid,$frmId,$fldName,$fldType,$fldVal){
    $rtrn_stt = "";
    $fldName = mysqli_real_escape_string($conn, $fldName);
    $fldVal = mysqli_real_escape_string($conn, $fldVal); 
    $sql = "INSERT INTO form_data (
        user_id,
        UID,
        form_id,
        field_name,
        field_type,
        field_value)  VALUES (
            '{$usrId}',
            '{$uid}',
            '{$frmId}',
            '{$fldName}',
            '{$fldType}',
            '{$fldVal}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;
}
function setDataDatetime($conn,$uid,$frmId){
    $rtrn_stt = "";
    $sql = "INSERT INTO form_data_datetimes (
        UID,
        form_id)  VALUES (
        '{$uid}',
        '{$frmId}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;
}

function uploadFile($conn,$form_id,$uid,$file, $isMulti,$maxFileSeize){
    $target_dir = "uploadfiles/";
    $uploadOk = 1;
    $msg = "";
    $msg_ary = array();
    if($isMulti){
        // Count # of uploaded files in array
        $total = count($file['name']);
        // Loop through each file
        for( $i=0 ; $i < $total ; $i++ ) {
            $file_name = $file["name"][$i];
            $target_file = $target_dir . basename($file["name"][$i]);
            $fileExt = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            $new_file_name = md5(rand()) . "." .$fileExt;
            $target_file =  $target_dir . $new_file_name;
            // Check file size
            if ($file["size"][$i] > $maxFileSeize) {
                $msg_ary[] = "Sorry, your file is too large.";
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                $msg_ary[] = "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($file["tmp_name"][$i], $target_file)) {
                    //$msg_ary[] = "The file '". basename( $file["name"][$i]). "' has been uploaded.";
                    $set_in_db = setFileInDB($conn,$form_id,$uid,$file_name,$target_file);
                    if($set_in_db != "success"){
                        $msg_ary[] = $set_in_db;
                        $uploadOk = 0;
                    }
                } else {
                    $uploadOk = 0;
                    $msg_ary[] = "Sorry, there was an error uploading the file '". basename( $file["name"][$i]). "'";
                    //$msg_ary[] = "file obj: ". json_encode($file);
                    //$msg_ary[] = "target: $target_file";
                    //$msg_ary[] = "max file size: $maxFileSeize";
                }
            }
        }
    }else{
        $file_name = $file["name"];
        $target_file = $target_dir . basename($file["name"]);
        $fileExt = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $new_file_name = md5(rand()) . "." .$fileExt;
        $target_file =  $target_dir . $new_file_name;
        // Check file size
        if ($file["size"] > $maxFileSeize) {
            $msg_ary[] = "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $msg_ary[] = "Sorry, your file was not uploaded.";
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                //$msg_ary[] = "The file ". basename( $file["name"]). " has been uploaded.";
                $set_in_db = setFileInDB($conn,$form_id,$uid,$file_name,$target_file);
                if($set_in_db != "success"){
                    $msg_ary[] = $set_in_db;
                    $uploadOk = 0;
                }
            } else {
                $msg_ary[] = "Sorry, there was an error uploading your file (".basename($file["name"]).")";
            }
        }
    }
    $msg = implode("<br>",$msg_ary);
    return [$uploadOk,$msg];
}

function setFileInDB($conn,$frmId,$uid,$fileName,$filPath){
    $rtrn_stt = "";
    $files = "";
    $sql = "INSERT INTO form_files (
        UID,
        form_id,
        file_name,
        file_path)  VALUES (
            '{$uid}',
            '{$frmId}',
            '{$fileName}',
            '{$filPath}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;

}

function setTableInDB($conn,$frmId,$uid,$tblName,$tblData){
    $rtrn_stt = "";
    $files = "";
    $tblData = mysqli_real_escape_string($conn, $tblData);
    $sql = "INSERT INTO form_tables (
        UID,
        form_id,
        table_name,
        table_data)  VALUES (
            '{$uid}',
            '{$frmId}',
            '{$tblName}',
            '{$tblData}')";
    if($result = $conn->query($sql)) {
        $rtrn_stt = "success";
    }else{
        $rtrn_stt = 'Error: ' . mysqli_error($conn);
    }

    return $rtrn_stt;

}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Form Process</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="css/util.css">
	<link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>
	<div class="limiter">
		<div class="container-login100" style="background-image: url('images/bg05.jpg');">
			<div class="wrap-login100 p-b-100">
				<h1>Form Process</h1>
					<?php if(!empty($message)): ?>
						<p style="background-color:white;"><?= $message ?></p>
					<?php endif; ?>
			</div>
		</div>
	</div>
    <script>
    
        //prevent a resubmit on refresh and back button
        if ( window.history.replaceState ) {
            window.history.replaceState( null, null, window.location.href );
            /*
            var form_id = '<?= $formId ?>';
            if(form_id != ''){
                window.location.href = "form.php?id=" + form_id;
            }else{
                window.history.replaceState( null, null, window.location.href );
            }
            */
        }
    </script>
<!--===============================================================================================-->	
	<script src="./include/jquery/jquery-1.12.4.min.js"></script>
<!--===============================================================================================-->
	<script src="js/main.js"></script>

</body>
</html>