var users_groups_table,
    org_dep_dialog,
    org_tree_chart;

$('#btnRight').click(function (e) {
    $('select').moveToListAndDelete('#available-users', '#selected-users');
    e.preventDefault();
});

$('#btnAllRight').click(function (e) {
    $('select').moveAllToListAndDelete('#available-users', '#selected-users');
    e.preventDefault();
});

$('#btnLeft').click(function (e) {
    $('select').moveToListAndDelete('#selected-users', '#available-users');
    e.preventDefault();
});

$('#btnAllLeft').click(function (e) {
    $('select').moveAllToListAndDelete('#selected-users', '#available-users');
    e.preventDefault();
});
/*
$selected_mngr = "";
$("#dep_manager").on("change",function(){
    $("#available-users").val($(this).val()).attr("selected",true);
    $('select').moveToListAndDelete('#available-users', '#selected-users');
})
*/
org_dep_dialog = $("#org_dep_win").dialog({
    modal: true,
    autoOpen: false,
    /*dialogClass: "formbuilder_dialg_win",*/
    width: 0.5 * $(window).width(),
    height: 0.7 * $(window).height(),
    buttons: [
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
                let new_node_name = $("#dep_name").val();
                let depId = $("#orgtree_dep_id").val();
                let actionType = $("#org_tree_action_type").val();
                let parentId;
                if ($("#parent_dep_id").val() != "") {
                    parentId = $("#parent_dep_id").val();
                } else {
                    parentId = $("#parent_dep_list").val();
                }
                /*
                if(new_node_name != "" && org_tree_chart !== null && org_tree_chart !== undefined){
                    //org_tree_chart.newNode(parentId);
                    var current_nods = org_tree_chart.getData();
                    var max_node_id = 0;
                    $.each(current_nods,function(i,node){
                        if(Number(node.id) > max_node_id){
                            max_node_id = Number(node.id);
                        }
                    })
                    org_tree_chart.addNode({ id: (max_node_id + 1), name: new_node_name, parent: parentId });

                    
                }
                */
                //let slctedUsrs = $("#selected-users").val();
                let selectedUsers = [];
                $('#selected-users option').each(function () {
                    //selected[$(this).val()]=$(this).text();
                    selectedUsers.push($(this).val());
                });

                let availableUsers = [];
                $('#available-users option').each(function () {
                    //selected[$(this).val()]=$(this).text();
                    availableUsers.push($(this).val());
                });

                let depMngr = $("#dep_manager").val();

                //console.log(availableUsers);

                var dep_data = {
                    record_id: depId,
                    dep_name: new_node_name,
                    dep_prnt_id: parentId,
                    dep_manager: depMngr,
                    selected_users: JSON.stringify(selectedUsers),
                    available_users: JSON.stringify(availableUsers)
                };
                var tbl = "org_tree";
                //console.log(usr_data)
                ajaxAction(actionType, tbl, dep_data, org_dep_dialog);

                //$(this).dialog("close");
                $('#users_groups_tree_content').jstree('select_node', "#org", true); //on load open users

            }
        }

    ]/*,
        close: function( event, ui ) {
            isFullMode = false;
        }*/
});
$('#users_groups_tree_content').on('changed.jstree', function (e, data) {
    var slected_id = data.instance.get_node(data.selected[0]).id;
    //$('.users_groups_data_content').html(slected_id)

    if (slected_id != "") {
        if (typeof (Storage) !== "undefined") {
            localStorage.setItem("slected_jstree_id", slected_id);
        }
        loadUsersGroupsTable(slected_id);
    }

}).jstree({
    /*"plugins" : [ "changed"],*/
    "core": {
        "data": [{
            "id": "users",
            "parent": "#",
            "text": "Users",
            "icon": "./include/icons/user.png"
        }, {
            "id": "registration",
            "parent": "#",
            "text": "Registration request",
            "icon": "./include/icons/add_user.png"
        }, {
            "id": "groups",
            "parent": "#",
            "text": "Groups",
            "icon": "./include/icons/groups.png"
        }, {
            "id": "org",
            "parent": "#",
            "text": "Organization tree",
            "icon": "fa fa-sitemap"
        }, {
            "id": "org_table",
            "parent": "org",
            "text": "Table view",
            "icon": "fa fa-table",
            "state": {
                "opened": true
            }
        }]
    }
});

$('#users_groups_tree_content').on("loaded.jstree", function (e, data) {
    $('#users_groups_tree_content').jstree('open_all');
    $('#users_groups_tree_content').jstree('select_node', "#users", true); //on load open users
});

function loadUsersGroupsTable(tbl) {
    var tbl_columns_names;
    var colDefs = [{
        "targets": [0, -1],
        "searchable": false,
        "orderable": false
    }];
    var btns = [];
    if (tbl == "users") {
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
                "title": "Department",
                "index": 6
            },
            {
                "title": "Status",
                "index": 7
            },
            {
                "title": '',
                "index": 8
            }
        ];
        btns = [{
            text: 'Add user',
            className: 'btn btn-primary btn-lg',
            action: function (e, dt, node, config) {
                addUpdateUser("new", "");
            }
        }];
    } else if (tbl == "groups") {
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
                addUpdateGroup("new", "", "", null)
            }
        }];
    } else if (tbl == "registration") {
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
            "render": function (data, type, row, meta) {
                /**https://www.gyrocode.com/projects/jquery-datatables-checkboxes/ */
                if (type === 'display') {
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
                if (arr.length == 0) {
                    alert("No selection!")
                } else {
                    if (confirm("Are you sure you want to delete all selected records?")) {
                        var frm_data = {
                            delType: "multi",
                            data: JSON.stringify(arr)
                        }

                        ajaxAction("delete", "user_reg_request", frm_data);
                    }
                }
            }
        }, {
            text: 'Accept all select',
            className: 'btn btn-info btn-sm',
            action: function (e, dt, node, config) {
                var arr = [];
                var isUnverifiedUsers = 0;
                $('.usr_row_checkbox:checked').each(function (val, i) {
                    var sel = $(this).parents('tr').find($(".registration_ids")).val();
                    var selAry = sel.split(",");
                    if (selAry[1] == "1") {
                        arr.push(selAry[0]);
                    } else {
                        isUnverifiedUsers++;
                    }
                });
                //console.log(arr)
                if (arr.length == 0) {
                    alert("No selected or unverified users!")
                } else {
                    if (isUnverifiedUsers > 0) {
                        alert("Note that unverified users will not be accepted");
                    }
                    var frm_data = {
                        type: "multi",
                        data: JSON.stringify(arr)
                    }
                    ajaxAction("new", "user_reg_request", frm_data);
                }
            }
        }]
    } else if (tbl == "org_table") {
        tbl_columns_names = [
            {
                "title": "#",
                "index": 0
            },
            {
                "title": "dep id",
                "index": 1
            },
            {
                "title": "dep name",
                "index": 2
            },
            {
                "title": "parent dep id",
                "index": 3
            },
            {
                "title": "parent dep name",
                "index": 4
            },
            {
                "title": "dep manager id",
                "index": 5
            },
            {
                "title": "dep manager name",
                "index": 5
            },
            {
                "title": '',
                "index": 6
            }
        ];
        /*
        */
        btns = [{
            text: 'Add Dep',
            className: 'btn btn-primary btn-lg',
            action: function (e, dt, node, config) {
                newOrUpdateDep("new", "", "", "", "", "");
            }
        }];
    } else if (tbl == "org") {
        $('#org_tree_content').show();
        $('#users_groups_data_table').hide();
        var org_tree_data = getOrgTree();
        //console.log(org_tree_data)
        if (org_tree_data != "") {
            var org_obj = JSON.parse(org_tree_data);
            org_tree_chart = $('#org_tree_content').orgChart({
                data: org_obj,
                showControls: true,
                allowEdit: true,
                newNodeText: '',
                nameFontSize: "10px",
                onAddNode: function (node) {
                    newOrUpdateDep("new", node.data.id, "", "", "");
                },
                onDeleteNode: function (node) {
                    //log('Deleted node ' + node.data.id);
                    let beforeDel = org_tree_chart.getData();
                    org_tree_chart.deleteNode(node.data.id);
                    let afterDel = org_tree_chart.getData();
                    let deletedNodes = $(beforeDel).not(afterDel).get();
                    let checkAry = ["1"];
                    $.each(afterDel, function (i, inode) {
                        if (inode.parent != "0") {
                            if (checkAry.indexOf(inode.parent) != -1) {
                                checkAry.push(inode.id)
                            } else {
                                let isFound = false;
                                $.each(deletedNodes, function (y, ynode) {
                                    if (inode.id == ynode.id) {
                                        isFound = true;
                                    }
                                });
                                if (!isFound) {
                                    deletedNodes.push(inode)
                                }
                            }
                        }
                    })
                    //console.log(beforeDel,afterDel,JSON.stringify(deletedNodes))

                    alertify.confirm("Are you sure you want to delete departments?",
                        function () {
                            //OK
                            //console.log("OK");
                            var org_data = {
                                data: JSON.stringify(deletedNodes)
                            }

                            ajaxAction("delete", "org_tree", org_data);
                        },
                        function () {
                            //Cancel
                            //console.log("Cancel");
                            alertify.error('Cancel');
                            $('#users_groups_tree_content').jstree(true).refresh();
                            $('#users_groups_tree_content').jstree('select_node', "#org", true);
                            /*
                            $.each(deletedNodes,function(k,node){
                                org_tree_chart.addNode(node)
                            });
                            */

                        }
                    );
                },
                onClickNode: function (node) {
                    //org_tree_chart.getData()
                    //console.log(node.data)
                    newOrUpdateDep("update", node.data.parent, node.data.id, node.data.name, node.data.mngr);
                }

            });
        }
    } else {
        return false;
    }

    if ($('#users_groups_data_table').html() != "") {
        $('#users_groups_data_table').DataTable().destroy();
        $('#users_groups_data_table').empty();
    }
    if (tbl != "org") {
        $('#org_tree_content').hide();
        $('#users_groups_data_table').show();
        users_groups_table = $('#users_groups_data_table').DataTable({
            language: {
                url: './include/DataTables/i18n/Hebrew.json'
            },
            ajax: {
                url: "users_and_groups.php",
                type: "POST",
                data: {
                    table: tbl
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
                [1, 'asc']
            ],
            dom: 'Bfrtip',
            buttons: btns,
            initComplete: function () {
                $('.dt-button').removeClass("dt-button");
                fixTableHeadScroll();
            },
            rowCallback: function (row, data, index) {
                //
            },
            drawCallback: function () {
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
}
function newOrUpdateDep(action, parentId, depId, depName, managerId) {
    $("#org_tree_action_type").val(action);
    let title = "";
    if (action == "update") {
        title = "Update Organization Department";
    } else if (action == "new") {
        title = "New Organization Department";
    }
    if (parentId != "") {
        $(".parent-row").hide();
        $("#parent_dep_id").val(parentId);
    } else {
        $(".parent-row").show();
        $("#parent_dep_id").val("");
        //get dep list -> #parent_dep_list
        setDepParentOptionList("", "", false);
    }
    $("#orgtree_dep_id").val(depId);
    $("#dep_name").val(depName);

    //$("#dep_manager").val("");
    setDepAdminOptionList([managerId], depId, false); //selectedAry,readonly
    //get users
    getUserOptinLists(depId);
    org_dep_dialog.dialog("option", "title", title);
    org_dep_dialog.dialog("open");
}
function getOrgTree() {
    data = "";
    $.ajax({
        type: "POST",
        url: "get_org_data.php",
        data: {
            data_type: "all"
        },
        async: false,
        success: function (response) {
            data = response;
        },
        error: function (response) {
            console.log("Error:", JSON.stringify(response));
            alert(response.responseText)
        }
    });
    return data;
}
function delSingleDepFromOrg(dep_id) {
    alertify.confirm("Are you sure you want to delete departments?",
        function () {
            //OK
            $.ajax({
                type: "POST",
                url: "del_single_dep_from_org.php",
                data: {
                    depToDel: dep_id
                },
                success: function (response) {
                    console.log(response);
                    if (response != "") {
                        if (response.indexOf("|") != -1) {
                            var dataAry = response.split("|");
                            if (dataAry[0] == "1") {
                                alertify.success('success');
                                loadUsersGroupsTable("org_table");
                            } else if (dataAry[0] == "2") {
                                alertify.warning(dataAry[1]);
                                loadUsersGroupsTable("org_table");
                            } else {
                                alertify.error('error: ' + dataAry[1]);
                            }
                        } else {
                            alertify.error('error: unknown error: ' + response);
                        }
                    } else {
                        alertify.error('error: empty response!');
                    }
                },
                error: function (response) {
                    console.log("Error:", JSON.stringify(response));
                    alert(response.responseText)
                }
            });
        },
        function () {
            //Cancel
            //console.log("Cancel");
            alertify.error('Cancel');


        }
    );
}
function getUserOptinLists(dep_id) {
    data = "";
    $.ajax({
        type: "POST",
        url: "get_all_users.php",
        data: {
            selectType: "users",
            selecDepId: dep_id
        },
        /*async:false,*/
        success: function (response) {
            if (response != "") {
                $("#available-users").html("");
                $("#selected-users").html("");
                var dataObj = JSON.parse(response);
                //console.log(dataObj)
                $("#available-users").html(dataObj.available);
                $("#selected-users").html(dataObj.selected);

            }

        },
        error: function (response) {
            console.log("Error:", JSON.stringify(response));
            alert(response.responseText)
        }
    });
    //return data;
}

function setDepAdminOptionList(selectedAry, depId, readonly) {
    var isDisabled = false;
    if (readonly !== undefined && readonly == "true") {
        isDisabled = true;
    }
    $('#dep_manager').empty();
    $('#dep_manager').select2({
        disabled: isDisabled,
        ajax: {
            url: 'get_all_users.php',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term, // search term
                    selectType: "admins",
                    selecDepId: depId
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
    $multiSelectDepMngrs = $('#dep_manager');
    $multiSelectDepMngrs.val(null).trigger('change');
    $.ajax({
        type: 'POST',
        url: 'get_all_users.php',
        data: {
            selectType: "admins",
            selecDepId: depId
        }
    }).then(function (data) {
        //console.log(data)
        var selectObj = JSON.parse(data);
        var selectObjAry = selectObj.results;
        $.each(selectObjAry, function (i, val) {
            if (selectedAry.indexOf(val.id) != -1) {
                var option = new Option(val.text, val.id, true, true);
                $multiSelectDepMngrs.append(option).trigger('change');
            }
        });
        // manually trigger the 'select2:select' event
        $multiSelectDepMngrs.trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
    });
}
////////////////////Dep parent list ///////////////////

function setDepParentOptionList(selectedAry, depId, readonly) {
    var isDisabled = false;
    if (readonly !== undefined && readonly == "true") {
        isDisabled = true;
    }
    $('#parent_dep_list').empty();
    $('#parent_dep_list').select2({
        disabled: isDisabled,
        ajax: {
            url: 'get_all_deps.php',
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchTerm: params.term, // search term
                    /*selectType: "admins",
                    selecDepId: depId*/
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
    $multiSelectDepPrnt = $('#parent_dep_list');
    $multiSelectDepPrnt.val(null).trigger('change');
    $.ajax({
        type: 'POST',
        url: 'get_all_deps.php',
        data: {
            selectType: "admins",
            selecDepId: depId
        }
    }).then(function (data) {
        //console.log(data)
        var selectObj = JSON.parse(data);
        var selectObjAry = selectObj.results;
        $.each(selectObjAry, function (i, val) {
            if (selectedAry.indexOf(val.id) != -1) {
                var option = new Option(val.text, val.id, true, true);
                $multiSelectDepPrnt.append(option).trigger('change');
            }
        });
        // manually trigger the 'select2:select' event
        $multiSelectDepPrnt.trigger({
            type: 'select2:select',
            params: {
                data: data
            }
        });
    });
}
///////////////////Users///////////////////////////

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
    console.log(usr_pass, conf_pass, isPassChange)
    var usr_email = $("#user_email").val();
    var usr_groups = $("#groupList").val();
    var usr_dep = $("#depsList").val();
    var usr_pblsh_stt = $("#user_status").val();
    var usr_data = {
        changePass: isPassChange,
        record_id: usr_id,
        user_name: usr_name,
        user_pass: usr_pass,
        user_email: usr_email,
        publish_groups: usr_groups,
        publish_dep: usr_dep,
        user_status: usr_pblsh_stt
    };
    var tbl = "users";
    //console.log(usr_data)
    ajaxAction(action, tbl, usr_data, dialogBox);
}
function delete_user(user_id) {
    if (confirm("Are you sure you want to delete this user?")) {
        var frm_data = {
            record_id: user_id
        }
        ajaxAction("delete", "users", frm_data);
    }
}
function delete_registration(idx, isConfirmed) {
    var isVerifiedStr = "";
    if (isConfirmed == "1") {
        isVerifiedStr = "verified";
    } else {
        isVerifiedStr = "unverified";
    }
    if (confirm("Are you sure you want to delete this " + isVerifiedStr + " user request?")) {
        var frm_data = {
            record_id: idx,
            delType: "single"
        }
    }
    ajaxAction("delete", "user_reg_request", frm_data);
}
function accept_user_request(idx, isConfirmed) {
    if (isConfirmed == "1") {
        var frm_data = {
            record_id: idx,
            type: "single"
        }
        ajaxAction("new", "user_reg_request", frm_data);
    } else {
        alert("The user not verified");
    }
}
//////////Groups///////////////////////////////////////////////
function addUpdateGroup(action, grp_id, admins_id, obj) {
    var dialogTitle = "Add new group";
    var gContent = $("#formbuilder_general_content");
    gContent.addClass("dialog_form_container");
    gContent.html("");
    var hInput = "<input type='hidden' id='grp_action_type' />";
    $(hInput).val(action).appendTo(gContent);
    var uhInput = "<input type='hidden' id='group_id' value = '" + grp_id + "' />";
    $(uhInput).appendTo(gContent);
    var group_data = "", groupName, groupStatus;
    if (action == "update") {
        dialogTitle = "Update group data";
        group_data = users_groups_table.row($(obj).parents('tr')).data();;
    }
    if (group_data !== "" && group_data !== null && group_data !== undefined) {
        //console.log(mor_data.data)
        groupName = group_data[2];
        groupStatus = group_data[3];
    } else {
        groupName = "";
        groupStatus = "";
    }

    var uInput = "<input type='text' id='group_name' value = '" + groupName + "' />";
    var uName = addElement("Group Name", "group_name", uInput);
    uName.appendTo(gContent);

    //////////////////////////////
    var gInput = "<select id='userMngrList' class='managerlist js-states form-control' multiple='multiple' style='width:80%;'></select>";
    var uGroups = addElement("Managers", "userMngrList", gInput);
    uGroups.appendTo(gContent);
    setUsersManagerList(admins_id);
    /////////////////////////////////////////////

    if (action == "update") {
        var sInput = "<select id='group_status'><option value='0'>Inactive</option><option value='1'>Active</option></select>";
        var uStatus = addElement("Status", "group_status", sInput);
        uStatus.appendTo(gContent);
        $("#group_status").val(groupStatus).change();
    } else {
        var usInput = "<input type='hidden' id='group_status' />";
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
                    set_group(general_dialog);
                }
            }
        ]
    );
    general_dialog.dialog("option", "height", 0.5 * $(window).height());
    general_dialog.dialog("option", "title", dialogTitle);
    general_dialog.dialog("open");
}
function set_group(dialogBox) {
    var action = $("#grp_action_type").val(); //new,updte
    if (action == "update") {
        if (!confirm("Are you sure you want to update?")) {
            return false;
        }
    }
    var grp_id = $("#group_id").val();
    var grp_name = $("#group_name").val();
    var grp_stt = $("#group_status").val();
    var usr_mngers = $("#userMngrList").val();
    var grp_data = {
        record_id: grp_id,
        group_name: grp_name,
        users_managers: usr_mngers,
        group_status: grp_stt
    };
    var tbl = "groups";
    ajaxAction(action, tbl, grp_data, dialogBox);
}
function delete_group(grop_id) {
    if (confirm("Are you sure you want to delete this group?")) {
        var frm_data = {
            record_id: grop_id
        }
        ajaxAction("delete", "groups", frm_data);
    }
}
