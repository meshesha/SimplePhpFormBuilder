///////////////////Users///////////////////////////
function addUpdateUser(action, usr_id) {
    var gContent = $("#formbuilder_general_content");
    gContent.addClass("dialog_form_container");
    gContent.html("");
    var hInput = "<input type='hidden' id='action_type' />";
    $(hInput).val(action).appendTo(gContent);
    var uhInput = "<input type='hidden' id='user_id' value = '" + usr_id + "' />";
    $(uhInput).appendTo(gContent);

    var user_data = "", userName, userPass, userEmail, userGroups, userDep, userStatus;
    if (action == "update") {
        user_data = getUserData(usr_id);
    }
    if (user_data !== "" && user_data !== null && user_data !== undefined) {
        //console.log(mor_data.data)
        userName = user_data.data.usr_name;
        userPass = user_data.data.pass;
        userEmail = user_data.data.email;
        userGroups = user_data.data.groups;
        userDep = user_data.data.dep;
        userStatus = user_data.data.status;
    } else {
        userName = "";
        userPass = "";
        userEmail = "";
        userGroups = "";
        userDep = "";
        userStatus = "";
    }
    var uInput = "<input type='text' id='user_name' value = '" + userName + "' disabled />";
    var uName = addElement("User Name", "user_name", uInput);
    uName.appendTo(gContent);

    var pInput = "<input type='password' id='user_password' value = '#@#FFD#3f' />"; //" + userPass + "
    var uPass = addElement("Password", "user_password", pInput);
    uPass.appendTo(gContent);

    var cPInput = "<input type='password' id='confirm_password' value = '' />";
    var cPPass = addElement("Confirm password", "confirm_password", cPInput);
    cPPass.appendTo(gContent);

    var eInput = "<input type='text' id='user_email' value = '" + userEmail + "' />";
    var uEmail = addElement("Email", "user_email", eInput);
    uEmail.appendTo(gContent);

    var gInput = "<select id='groupList' class='groupslist js-states form-control' multiple='multiple' style='width:80%;'></select>";
    var uGroups = addElement("Groups", "groupList", gInput);
    uGroups.appendTo(gContent);
    setGroupsList(userGroups);

    // multiple='multiple'
    var gInput = "<select id='depsList' class='depslist js-states form-control' style='width:80%;'></select>";
    var uDeps = addElement("Department", "depsList", gInput);
    uDeps.appendTo(gContent);
    setDepartmentsList(userDep);

    if (action == "update") {
        var sInput = "<select id='user_status'><option value='0'>Inactive</option><option value='1'>Active</option></select>";
        var uStatus = addElement("Status", "user_status", sInput);
        uStatus.appendTo(gContent);
        $("#user_status").val(userStatus).change().attr("disabled", true);
    } else {
        var usInput = "<input type='hidden' id='user_status' />";
        $(usInput).val("0").appendTo(gContent);
    }
    general_dialog.dialog("option", "buttons",
        [
            {
                text: "Cancel",
                class: "btn btn-primary btn-lg",
                click: function () {
                    $(this).dialog("close");
                }
            },
            {
                text: "Save",
                class: "btn btn-primary btn-lg",
                click: function () {
                    add_update_user(general_dialog);
                }
            }
        ]
    );
    general_dialog.dialog("option", "height", 0.65 * $(window).height());
    general_dialog.dialog("option", "title", "Update user data");
    general_dialog.dialog("open");
    $("#main-vewer-menu ul").hide();
}
function add_update_user(dialogBox) {
    var action = $("#action_type").val(); //new,updte
    if (action == "update") {
        if (!confirm("Are you sure you want to update?")) {
            return false;
        }
    }
    var usr_id = $("#user_id").val();
    var usr_name = $("#user_name").val();
    var usr_pass = $("#user_password").val();
    var conf_pass = $("#confirm_password").val();
    var isPassChange = "-1";
    if (usr_pass != "" && conf_pass != "" && usr_pass != conf_pass) {
        alert("confirm password - Passwords Don't Match");
        return false;
    } else {
        isPassChange = "1";
    }
    var usr_email = $("#user_email").val();
    var usr_groups = $("#groupList").val();
    var usr_pblsh_stt = $("#user_status").val();
    var usr_data = {
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
    ajaxAction(action, tbl, usr_data, dialogBox);
}

function getUserData(user_id) {
    var rt_data = "";
    if (user_id != "") {
        $.ajax({
            type: "POST",
            url: "get_user_data.php",
            async: false,
            data: { user_id: user_id },
            success: function (response) {
                response = JSON.parse(response);
                rt_data = response;
            },
            error: function (response) {
                console.log("Error:", response.responseText);
            },
            failure: function (response) {
                console.log("Error:", JSON.stringify(response));
            }
        });
    }
    return rt_data;
}

function addElement(label, id, element) {
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

function setGroupsList(selectedAry) {
    $('.groupslist').empty();
    $('.groupslist').select2({
        disabled: true,
        ajax: {
            url: 'get_all_groups.php',
            type: "post",
            dataType: 'json',
            delay: 250,
            async: false,
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
    if (selectedAry === undefined || selectedAry == "") {
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
        $.each(selectObjAry, function (i, val) {
            if (selectedAry.indexOf(val.id) != -1) {
                var option = new Option(val.text, val.id, true, true);
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

function setDepartmentsList(selectedAry) {
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
    if (selectedAry === undefined || selectedAry == "") {
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
        $.each(selectObjAry, function (i, val) {
            if (selectedAry.indexOf(val.id) != -1) {
                var option = new Option(val.text, val.id, true, true);
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
function openLinkInNewTab(url) {
    var win = window.open(url, '_blank');
    $("#main-vewer-menu ul").hide();
    win.focus();
}
function showAboutForm(formId) {
    /*
    var publishTypeName = {
        "1":"Public",
        "2":"Users group",
        "3":"Public-Anonymously",
        "4":"Groups-Anonymously"
    };
    */
    var statusTypeName = { "1": "Published", "2": "Unpublished" };
    var allGroups = "", allDeps = "", allFormMngrs = "";
    var gContent = $("#formbuilder_general_content");
    gContent.addClass("dialog_form_container");
    gContent.html("");
    var form_data = getFormData(formId);

    if (form_data !== null && form_data !== undefined) {
        allGroups = getAllGroups();
        allFormMngrs = getAllFormMngrs();
        allDeps = getAllDeps();
    }
    var formName = form_data.data.frm_name;
    var formTitle = form_data.data.frm_title;
    var publishTypeId = form_data.data.publ_type;
    var publishTypeName = form_data.data.publ_type_name;

    var formGroupsIds = form_data.data.publ_grps;
    var formGroupsNames = "";
    if (allGroups != "" && formGroupsIds != "") {
        var groupsIdsAry = [];
        var isSingleGrp = false;
        if (formGroupsIds.indexOf(",") > -1) {
            groupsIdsAry = formGroupsIds.split(",");
        } else {
            isSingleGrp = true;
            groupsIdsAry[0] = formGroupsIds;
        }
        var allGroupsObj = JSON.parse(allGroups);
        var allGroupsObjAry = allGroupsObj.results;
        //console.log(allGroupsObjAry)
        $.each(allGroupsObjAry, function (i, grp) {
            if (isSingleGrp) {
                if (groupsIdsAry[0] == grp.id) {
                    formGroupsNames = grp.text;
                }
            } else {
                $.each(groupsIdsAry, function (i, grpId) {
                    if (grpId == grp.id) {
                        formGroupsNames += grp.text + ", ";
                    }
                });
            }
        });
    }
    var formDepsIds = form_data.data.publ_deps;
    var formDepsNames = "";
    if (allDeps != "" && formDepsIds != "") {
        var depsIdsAry = [];
        var isSingleDep = false;
        if (formDepsIds.indexOf(",") > -1) {
            depsIdsAry = formDepsIds.split(",");
        } else {
            isSingleDep = true;
            depsIdsAry[0] = formDepsIds;
        }
        var allDepsObj = JSON.parse(allDeps);
        var allDepsObjAry = allDepsObj.results;
        //console.log(allDepsObjAry)
        $.each(allDepsObjAry, function (i, dep) {
            if (isSingleDep) {
                if (depsIdsAry[0] == dep.id) {
                    formDepsNames = dep.text;
                }
            } else {
                $.each(depsIdsAry, function (i, depId) {
                    if (depId == dep.id) {
                        formDepsNames += dep.text + ", ";
                    }
                });
            }
        });
    }

    var formMngrsIds = form_data.data.admin_users;
    var formMngrsNames = "";
    if (allFormMngrs != "" && formMngrsIds != "") {
        var mngrsIdsAry = [];
        var isSingleMngr = false;
        if (formMngrsIds.indexOf(",") > -1) {
            mngrsIdsAry = formMngrsIds.split(",");
        } else {
            isSingleMngr = true;
            mngrsIdsAry[0] = formMngrsIds;
        }
        var allFormMngrsObj = JSON.parse(allFormMngrs);
        var allFormMngrsObjAry = allFormMngrsObj.results;
        //console.log(allGroupsObjAry)
        if (allFormMngrsObjAry.length > 0) {
            $.each(allFormMngrsObjAry, function (i, mngr) {
                if (isSingleMngr) {
                    if (mngrsIdsAry[0] == mngr.id) {
                        formMngrsNames = mngr.text;
                    }
                } else {
                    $.each(mngrsIdsAry, function (i, mngrId) {
                        if (mngrId == mngr.id) {
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
    var uName = addElement("Form name", "form_name", uInput);
    uName.appendTo(gContent);

    var uInput = "<input type='text' id='form_title' value = '" + formTitle + "' disabled />";
    var uName = addElement("Form title", "form_title", uInput);
    uName.appendTo(gContent);
    //publishTypeName[publishTypeId]
    var uInput = "<input type='text' id='publish_type' value = '" + publishTypeName + "' disabled  />";
    var uName = addElement("Publish type", "publish_type", uInput);
    uName.appendTo(gContent);

    if (publishTypeId == "2" || publishTypeId == "4") {
        var uInput = "<input type='text' id='groups_list' value = '" + formGroupsNames + "' disabled  />";
        var uName = addElement("Groups", "groups_list", uInput);
        uName.appendTo(gContent);
    } else if (publishTypeId == "5" || publishTypeId == "6") {
        var uInput = "<input type='text' id='deps_list' value = '" + formDepsNames + "' disabled  />";
        var uName = addElement("Departments", "deps_list", uInput);
        uName.appendTo(gContent);
    }

    var uInput = "<input type='text' id='form_managers_list' value = '" + formMngrsNames + "' disabled />";
    var uName = addElement("Form Managers", "form_managers_list", uInput);
    uName.appendTo(gContent);

    var uInput = "<input type='text' id='status_type' value = '" + statusTypeName[statusTypeId] + "' disabled  />";
    var uName = addElement("Status", "status_type", uInput);
    uName.appendTo(gContent);

    var uInput = "<textarea id='form_note' disabled>" + formNots + "</textarea>";
    var uName = addElement("Note", "form_note", uInput);
    uName.appendTo(gContent);

    general_dialog.dialog("option", "buttons",
        [
            {
                text: "Cancel",
                class: "btn btn-primary btn-lg",
                click: function () {
                    $(this).dialog("close");
                }
            }
        ]
    );

    general_dialog.dialog("option", "height", 0.8 * $(window).height());
    general_dialog.dialog("option", "title", "Form Data");
    general_dialog.dialog("open");
    $("#main-vewer-menu ul").hide();
}
function getFormData(form_id) {
    var rt_data = "";
    if (form_id != "") {
        //ajax
        $.ajax({
            type: "POST",
            url: "get_form_data.php",
            async: false,
            data: { form_id: form_id },
            success: function (response) {
                response = JSON.parse(response);
                rt_data = response;
            },
            error: function (response) {
                console.log("Error:", JSON.stringify(response));
                alert(response.responseText)
            }
        });
    }
    return rt_data;
}
function getAllGroups() {
    var rt_data = "";
    //ajax
    $.ajax({
        type: "POST",
        url: "get_all_groups.php",
        async: false,
        success: function (response) {
            //response = JSON.parse(response);
            rt_data = response;
        },
        error: function (response) {
            console.log("Error:", JSON.stringify(response));
            alert(response.responseText)
        }
    });
    return rt_data;
}

function getAllFormMngrs() {
    var rt_data = "";
    //ajax
    $.ajax({
        type: "POST",
        url: "get_all_managers_users.php",
        async: false,
        success: function (response) {
            //response = JSON.parse(response);
            rt_data = response;
        },
        error: function (response) {
            console.log("Error:", JSON.stringify(response));
            alert(response.responseText)
        }
    });
    return rt_data;
}
function getAllDeps() {
    var rt_data = "";
    //ajax
    $.ajax({
        type: "POST",
        url: "get_all_deps.php",
        async: false,
        success: function (response) {
            //response = JSON.parse(response);
            rt_data = response;
        },
        error: function (response) {
            console.log("Error:", JSON.stringify(response));
            alert(response.responseText)
        }
    });
    return rt_data;
}

function ajaxAction(action_type, tbl, data, dialogbox) {
    var url = "set_data.php";
    var data_obj = {
        table: tbl,
        action: action_type,
        data: data
    };

    if (action_type != "" && url != "") {
        //ajax
        $.ajax({
            type: "POST",
            url: url,
            data: { data: JSON.stringify(data_obj) },
            success: function (response) {
                console.log(response);
                if (response == "success") {
                    if (dialogbox !== undefined && dialogbox !== null && dialogbox.hasClass('ui-dialog-content')) {
                        dialogbox.dialog("close");
                    }
                    /* no tables to load in form.php
                    if (tbl == "form") {
                        load_form_list();
                    } else {
                        var selctedTbl = localStorage.getItem('slected_jstree_id');
                        if (selctedTbl != null && selctedTbl != "") {
                            loadUsersGroupsTable(selctedTbl);
                        }
                    }
                    */
                    location.reload();

                } else {
                    alert(response)
                }
            },
            error: function (response) {
                console.log("Error:", JSON.stringify(response));
                alert(response.responseText)
            }
        });
    }
}