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
        /*
        {
            "type": "Buttons",
            "label": "לחצנים",
            "placeholder": "btn btn-primary",
            "className": "buttons-container",
            "name": "Buttons-1560116053064",
            "submitBtnColor": "btn btn-primary",
            "clearBtnColor": "btn btn-danger",
            "btnsPos": "form-control-buttons-center",
            "submitLabel": "שלח",
            "cancelLabel": "נקה"
        },
        */
        $btnsObj = new stdClass();
        $btnsObj->type = "Buttons";
        $btnsObj->label = "";
        $btnsObj->className = "buttons-container";
        $btnsObj->name = "";
        $btnsObj->submitBtnColor = "btn btn-primary";
        $btnsObj->clearBtnColor = "btn btn-danger";
        $btnsObj->btnsPos = "";
        $btnsObj->submitLabel = "Submit";
        $btnsObj->cancelLabel = "Clear";

        
        $hiddenObj = new stdClass();
        $hiddenObj->type = "hidden";
        $hiddenObj->name = "hidden-form-id";
        $hiddenObj->id = "hidden-form-id";
        $hiddenObj->value = $form_id;

        $form = json_decode($echo_data);
        $frm_ary = array();
        //remove button and hidden field if exists
        $isButtons = false;
        foreach($form as $fild){
            if($fild->type != "button" && $fild->type != "hidden"){
                $frm_ary[] = $fild;
            }
            if($fild->type == "Buttons"){
                $isButtons = true;
            }
        }
        array_push($frm_ary,$hiddenObj);
        //array_push($frm_ary,$submitBtnObj);
        if(!$isButtons){
            array_push($frm_ary,$btnsObj);
        }
        $echo_data = json_encode($frm_ary);
    }else{
        $frm_ary = array();
   
        $hiddenObj = new stdClass();
        $hiddenObj->type = "hidden";
        $hiddenObj->name = "hidden-form-id";
        $hiddenObj->id = "hidden-form-id";
        $hiddenObj->value = $form_id;

        $btnsObj = new stdClass();
        $btnsObj->type = "Buttons";
        $btnsObj->label = "";
        $btnsObj->className = "buttons-container";
        $btnsObj->name = "";
        $btnsObj->submitBtnColor = "btn btn-primary";
        $btnsObj->clearBtnColor = "btn btn-danger";
        $btnsObj->btnsPos = "";
        $btnsObj->submitLabel = "Submit";
        $btnsObj->cancelLabel = "Clear";

        array_push($frm_ary,$hiddenObj);
        array_push($frm_ary,$btnsObj);
        $echo_data = json_encode($frm_ary);
    }
    echo $echo_data;
}else{
    echo 'Error: missing form_id';
}

?>