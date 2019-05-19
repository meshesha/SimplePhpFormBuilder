<?php

include "chech_restricted.php";

if(!isset($isSetDBclass)){
  require 'settings/database.class.php';
}
$db_ = new Database("formbuilder");
$conn = $db_->getConnection();

if(isset($_POST["setting_id"])){
  $settingGroup = $_POST["setting_id"];
  $echo_data = "";
  if($settingGroup == "general_setting"){
    $settings = getSettings($conn,"general");
  }else if($settingGroup == "email_setting"){
    $settings = getSettings($conn,"email");
  }else if($settingGroup == "form_style_setting"){
    $settings = getSettings($conn,"form_style");
  }
  if(!empty($settings)){
    //$echo_data = json_encode($settings);
    $echo_data = getHrmlSetting($conn,$settingGroup,$settings);
  }
  echo $echo_data;
}
if(isset($_POST["setting_change"])){
  $settingName = $_POST["setting_name"];
  $settingValue = $_POST["new_value"];
  $settingName = mysqli_real_escape_string($conn, $settingName); 
  $settingValue = mysqli_real_escape_string($conn, $settingValue); 
  $sql = "UPDATE settings SET setting_value = '$settingValue' WHERE setting_name='$settingName'";
  if($result = $conn->query($sql)) {
    $echo_data = "success";
  }else{
    $echo_data = 'Error: ' . mysqli_error($conn);
  }
  echo $echo_data;
}
function getSettings($conn,$sGroup){
  $data = array();
  $sql = "SELECT * FROM settings WHERE setting_group='$sGroup'";
  if($result = $conn->query($sql)) {
    $count = mysqli_num_rows($result);
    if($count > 0){
      while($row = mysqli_fetch_assoc($result)){
        $locAry = array();
        $locAry[] = $row["indx"];
        $locAry[] = $row["setting_name"];
        $locAry[] = $row["setting_nik"];
        $locAry[] = $row["setting_value"];
        $locAry[] = $row["options"];
        $locAry[] = $row["note"];
        $data[] = $locAry;
      }
    }
  }
  return $data;
}
function getHrmlSetting($conn,$settingGroup,$settingObj){
  $htmlAry = array();
  foreach($settingObj as $settingAry){
    $idx = $settingAry[0];
    $name = $settingAry[1];
    $nik = $settingAry[2];
    $value = $settingAry[3];
    $option = $settingAry[4];
    $desc = $settingAry[5];
    $elId = $settingGroup."_".$idx;
    $optionObj = json_decode($option);
    $optionType = $optionObj->type;
    $input = "";
    if($optionType == "text"){
      $input = "<input type='text' id='$elId' class='form-control' value='$value' />";
    }else if($optionType == "number"){
      //add min , max , step if exists
      $num_range = "";
      if(isset($optionObj->min)){
        $rMin = $optionObj->min;
        $num_range .= "min='$rMin' ";
      }
      if(isset($optionObj->max)){
        $rMax = $optionObj->max;
        $num_range .= "max='$rMax' ";
      }
      if(isset($optionObj->step)){
        $rStep = $optionObj->step;
        $num_range .= "step='$rStep' ";
      }
      $input = "<input type='number' id='$elId' class='form-control input-type-number' $num_range value='$value' />";
    }else if($optionType == "password"){
      $input = "<input type='password' id='$elId' class='form-control' value='$value' />";
    }else if($optionType == "color"){
      $input = "<input type='text' id='$elId' class='form-control general-setting-color' value='$value' />";
    }else if($optionType == "select"){
      $input = "<select id='$elId' class='custom-select'>";
      $optionValsAry = $optionObj->values;
      foreach($optionValsAry as $opt){
        $text = $opt->text;
        $val = $opt->value;
        $isSelected = '';
        if($value == $val){
          $isSelected = 'selected';
        }
        $input .= "<option value='$val' $isSelected>$text</option>"; 
      }
      $input .= "</select>";
    }else if($optionType == "sqlselect"){
      $input = "<select id='$elId' class='custom-select'>";
      $tbl = $optionObj->table;
      $term = $optionObj->term;
      $colTextName = $optionObj->column_text;
      $colValName = $optionObj->column_value;
      $sqldata = getSqlData($conn, $tbl , $term);
      foreach($sqldata as $data){
          $text = $data[$colTextName];
          $val = $data[$colValName];
          $isSelected = '';
          if($value == $val){
            $isSelected = 'selected';
          }
          $input .= "<option value='$val' $isSelected>$text</option>";
      }
      $input .= "</select>";
    }
    $htmlAry[] = "<tr><td><label for='$elId'>$nik:</label><td>
                  <td>$input</td>
                  <td><button type='botton' class='btn btn-info btn-sm' onclick='changeSetting(\"$elId\",\"$name\",\"$value\")'>Save change</td>
                  <td><span>$desc</span></td></tr>";
  }
  $htmlStr = "<table class='table table-sm table-striped setting_table'>";
  $htmlStr .= implode("", $htmlAry);
  $htmlStr .= "</table>
    <script>
      $('.general-setting-color').spectrum({
          preferredFormat: 'rgb',
          showAlpha: true,
          showInitial: true,
          showInput: true
      });
      $('.input-type-number').number();
    </script>
  ";
  return $htmlStr;
}

function getSqlData($conn, $tbl , $term){
    $data = array();
    $sql = "SELECT * FROM $tbl WHERE $term";
    if($result = $conn->query($sql)) {
        $count = mysqli_num_rows($result);
        if($count > 0){
          $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
    return $data;
}
if(!isset($echo_data)){
?>

<div class="settings_warper">
    <div class="settings_column settings_tree"  style="overflow:auto;">
        <span>Settings</span><br>
        <div class="settings_tree_content"></div>
    </div>
    <div class="settings_column settings_data" style="overflow:auto;">
        <div class="settings_data_content" style="overflow:auto;"></div>
    </div>
</div>
<script>
    var settings_table;
$('.settings_tree_content').on('changed.jstree', function (e, data) {
    var slected_id = data.instance.get_node(data.selected[0]).id;
    
    if (slected_id != "") {
        getSettings(slected_id);
    }

  }).jstree({
    "plugins" : [ "changed"],
    'core' : {
        'data' : [{
                "id" : "general_setting",
                "parent" : "#",
                "text" : "General",
                "icon": "fa fa-cogs"
            },{
                "id" : "email_setting",
                "parent" : "#",
                "text" : "Email",
                "icon": "fa fa-envelope"
            },{
                "id" : "form_style_setting",
                "parent" : "#",
                "text" : "Default form style",
                "icon": "fa fa-file"
            }]
    } 
});
$('.settings_tree_content').on("loaded.jstree", function (e, data) {
    $('.settings_tree_content').jstree('select_node', "#general_setting",true);//on load open users
});


function getSettings(slected_id){
    if(slected_id != ""){
        $.ajax({
            type: "POST",
            url: "settings_section.php",
            data: {
                setting_id : slected_id},
            success: function (response) {
              if(response != ""){
                $(".settings_data_content").html(response);
              }
            },
            error:function (response) {
              console.log("Error:",JSON.stringify(response));
              alert(response.responseText)
            }
        });
    }
}
function changeSetting(inputId, name, oldVal){
  var newVal = $("#" + inputId).val()
  //console.log(name,", old value: ",oldVal,", new value: ",newVal );
  if(oldVal != newVal){
    $.ajax({
        type: "POST",
        url: "settings_section.php",
        data: {
          setting_change : "setting_change",
          setting_name: name,
          new_value: newVal
        },
        success: function (response) {
          alert(response);
        },
        error:function (response) {
            console.log("Error:",response.responseText);
        },
        failure: function (response) {
            console.log("Error:" , JSON.stringify(response));
        }
    });
}else{
    alert("You have not made any changes to the value.")
  }
}
</script>

<?php } ?>