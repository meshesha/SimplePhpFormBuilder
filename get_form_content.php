<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//include "chech_restricted.php";

require "settings/database.class.php";

if(isset($_POST['form_id'])){
    $form_id = $_POST["form_id"];
    $echo_data = "new";
    $db = new Database("formbuilder");
    $conn = $db->getConnection();
    
    $sql = "SELECT * FROM form_content WHERE form_id='$form_id'";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
            while($row = mysqli_fetch_assoc($result)){
               $echo_data =  $row['form_form'] ; 
            }
        }
    }
    if($echo_data != "new" && $echo_data != ""){
        $submitBtnObj = new stdClass();
        $submitBtnObj->type = "button";
        $submitBtnObj->subtype = "submit";
        $submitBtnObj->label = "Submit";
        $submitBtnObj->className = "btn-primary btn";
        $submitBtnObj->name = "button-submit-form";
        $submitBtnObj->id = "button-submit-form";
        $submitBtnObj->style = "primary";

        
        $hiddenObj = new stdClass();
        $hiddenObj->type = "hidden";
        $hiddenObj->name = "hidden-form-id";
        $hiddenObj->id = "hidden-form-id";
        $hiddenObj->value = $form_id;

        $form = json_decode($echo_data);
        $frm_ary = array();
        //remove button if exists
        foreach($form as $fild){
            if($fild->type != "button" && $fild->type != "hidden"){
                $frm_ary[] = $fild;
            }
        }
        array_push($frm_ary,$hiddenObj);
        array_push($frm_ary,$submitBtnObj);
        $echo_data = json_encode($frm_ary);
    }
    echo $echo_data;
}else{
    echo 'Error: missing form_id';
}

?>