<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['user_id'])){
    unset($_SESSION['user_id']);
}
if(isset($_SESSION['rederect_url'])){
    if($_SESSION['rederect_url'] == "main_page"){
        header("Location: index.php");
    }else if($_SESSION['rederect_url'] == "form_admin"){
        $formId = $_SESSION['form_id'];
        header("Location: formadmin.php?id=$formId");
    }else if($_SESSION['rederect_url'] == "form"){
        $formId = $_SESSION['form_id'];
        header("Location: form.php?id=$formId");
    }else{
        header("Location: index.php");
    }
}else{
    header("Location: index.php");
}


//session_destroy();

//header("Location: index.php");