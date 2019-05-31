/**
 * Table class v0.0.1 by Tady Meshesha
 */
var editTableObject = {};
// configure the class for runtime loading
if (!window.fbControls) window.fbControls = [];
window.fbControls.push(function (controlClass) {
    /**
     * Table class
     */
    class controlTable extends controlClass {

        /**
         * Class configuration - return the icons & label related to this control
         * @returndefinition object
         */
        static get definition() {
            return {
                icon: '<img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTYuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4IiB2aWV3Qm94PSIwIDAgNDc1LjA4MiA0NzUuMDgxIiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IDAgMCA0NzUuMDgyIDQ3NS4wODE7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4KPGc+Cgk8cGF0aCBkPSJNNDYxLjY2Nyw0OS45NjNjLTguOTQ5LTguOTQ3LTE5LjY5OC0xMy40MTgtMzIuMjY1LTEzLjQxOEg0NS42ODJjLTEyLjU2MiwwLTIzLjMxNyw0LjQ3MS0zMi4yNjQsMTMuNDE4ICAgQzQuNDczLDU4LjkxMiwwLDY5LjY2MywwLDgyLjIyOFYzOTIuODZjMCwxMi41NjYsNC40NzMsMjMuMzA5LDEzLjQxOCwzMi4yNjFjOC45NDcsOC45NDksMTkuNzAxLDEzLjQxNSwzMi4yNjQsMTMuNDE1aDM4My43MiAgIGMxMi41NjYsMCwyMy4zMTUtNC40NjYsMzIuMjY1LTEzLjQxNWM4Ljk0NS04Ljk1MiwxMy40MTUtMTkuNzAxLDEzLjQxNS0zMi4yNjFWODIuMjI4ICAgQzQ3NS4wODIsNjkuNjYzLDQ3MC42MTIsNTguOTA5LDQ2MS42NjcsNDkuOTYzeiBNMTQ2LjE4MywzOTIuODVjMCwyLjY3My0wLjg1OSw0Ljg1Ni0yLjU3NCw2LjU3MSAgIGMtMS43MTIsMS43MTEtMy44OTksMi41NjItNi41NjcsMi41NjJoLTkxLjM2Yy0yLjY2MiwwLTQuODUzLTAuODUyLTYuNTY3LTIuNTYyYy0xLjcxMy0xLjcxNS0yLjU2OC0zLjg5OC0yLjU2OC02LjU3MVYzMzguMDMgICBjMC0yLjY2OSwwLjg1NS00Ljg1MywyLjU2OC02LjU2YzEuNzE0LTEuNzE5LDMuOTA1LTIuNTc0LDYuNTY3LTIuNTc0aDkxLjM2M2MyLjY2NywwLDQuODU4LDAuODU1LDYuNTY3LDIuNTc0ICAgYzEuNzExLDEuNzA3LDIuNTcsMy44OTEsMi41Nyw2LjU2VjM5Mi44NXogTTE0Ni4xODMsMjgzLjIyMWMwLDIuNjYzLTAuODU5LDQuODU0LTIuNTc0LDYuNTY0ICAgYy0xLjcxMiwxLjcxNC0zLjg5OSwyLjU2OS02LjU2NywyLjU2OWgtOTEuMzZjLTIuNjYyLDAtNC44NTMtMC44NTUtNi41NjctMi41NjljLTEuNzEzLTEuNzExLTIuNTY4LTMuOTAxLTIuNTY4LTYuNTY0di01NC44MTkgICBjMC0yLjY2NCwwLjg1NS00Ljg1NCwyLjU2OC02LjU2N2MxLjcxNC0xLjcwOSwzLjkwNS0yLjU2NSw2LjU2Ny0yLjU2NWg5MS4zNjNjMi42NjcsMCw0Ljg1NCwwLjg1NSw2LjU2NywyLjU2NSAgIGMxLjcxMSwxLjcxMywyLjU3LDMuOTAzLDIuNTcsNi41NjdWMjgzLjIyMXogTTE0Ni4xODMsMTczLjU4N2MwLDIuNjY2LTAuODU5LDQuODUzLTIuNTc0LDYuNTY3ICAgYy0xLjcxMiwxLjcwOS0zLjg5OSwyLjU2OC02LjU2NywyLjU2OGgtOTEuMzZjLTIuNjYyLDAtNC44NTMtMC44NTktNi41NjctMi41NjhjLTEuNzEzLTEuNzE1LTIuNTY4LTMuOTAxLTIuNTY4LTYuNTY3VjExOC43NyAgIGMwLTIuNjY2LDAuODU1LTQuODU2LDIuNTY4LTYuNTY3YzEuNzE0LTEuNzEzLDMuOTA1LTIuNTY4LDYuNTY3LTIuNTY4aDkxLjM2M2MyLjY2NywwLDQuODU0LDAuODU1LDYuNTY3LDIuNTY4ICAgYzEuNzExLDEuNzExLDIuNTcsMy45MDEsMi41Nyw2LjU2N1YxNzMuNTg3eiBNMjkyLjM2MiwzOTIuODVjMCwyLjY3My0wLjg1NSw0Ljg1Ni0yLjU2Myw2LjU3MWMtMS43MTEsMS43MTEtMy45LDIuNTYyLTYuNTcsMi41NjIgICBIMTkxLjg2Yy0yLjY2MywwLTQuODUzLTAuODUyLTYuNTY3LTIuNTYyYy0xLjcxMy0xLjcxNS0yLjU2OC0zLjg5OC0yLjU2OC02LjU3MVYzMzguMDNjMC0yLjY2OSwwLjg1NS00Ljg1MywyLjU2OC02LjU2ICAgYzEuNzE0LTEuNzE5LDMuOTA0LTIuNTc0LDYuNTY3LTIuNTc0aDkxLjM2NWMyLjY2OSwwLDQuODU5LDAuODU1LDYuNTcsMi41NzRjMS43MDQsMS43MDcsMi41NiwzLjg5MSwyLjU2LDYuNTZ2NTQuODE5SDI5Mi4zNjJ6ICAgIE0yOTIuMzYyLDI4My4yMjFjMCwyLjY2My0wLjg1NSw0Ljg1NC0yLjU2Myw2LjU2NGMtMS43MTEsMS43MTQtMy45LDIuNTY5LTYuNTcsMi41NjlIMTkxLjg2Yy0yLjY2MywwLTQuODUzLTAuODU1LTYuNTY3LTIuNTY5ICAgYy0xLjcxMy0xLjcxMS0yLjU2OC0zLjkwMS0yLjU2OC02LjU2NHYtNTQuODE5YzAtMi42NjQsMC44NTUtNC44NTQsMi41NjgtNi41NjdjMS43MTQtMS43MDksMy45MDQtMi41NjUsNi41NjctMi41NjVoOTEuMzY1ICAgYzIuNjY5LDAsNC44NTksMC44NTUsNi41NywyLjU2NWMxLjcwNCwxLjcxMywyLjU2LDMuOTAzLDIuNTYsNi41Njd2NTQuODE5SDI5Mi4zNjJ6IE0yOTIuMzYyLDE3My41ODcgICBjMCwyLjY2Ni0wLjg1NSw0Ljg1My0yLjU2Myw2LjU2N2MtMS43MTEsMS43MDktMy45LDIuNTY4LTYuNTcsMi41NjhIMTkxLjg2Yy0yLjY2MywwLTQuODUzLTAuODU5LTYuNTY3LTIuNTY4ICAgYy0xLjcxMy0xLjcxNS0yLjU2OC0zLjkwMS0yLjU2OC02LjU2N1YxMTguNzdjMC0yLjY2NiwwLjg1NS00Ljg1NiwyLjU2OC02LjU2N2MxLjcxNC0xLjcxMywzLjkwNC0yLjU2OCw2LjU2Ny0yLjU2OGg5MS4zNjUgICBjMi42NjksMCw0Ljg1OSwwLjg1NSw2LjU3LDIuNTY4YzEuNzA0LDEuNzExLDIuNTYsMy45MDEsMi41Niw2LjU2N3Y1NC44MTdIMjkyLjM2MnogTTQzOC41MzYsMzkyLjg1ICAgYzAsMi42NzMtMC44NTUsNC44NTYtMi41NjIsNi41NzFjLTEuNzE4LDEuNzExLTMuOTA4LDIuNTYyLTYuNTcxLDIuNTYyaC05MS4zNTRjLTIuNjczLDAtNC44NjItMC44NTItNi41Ny0yLjU2MiAgIGMtMS43MTEtMS43MTUtMi41Ni0zLjg5OC0yLjU2LTYuNTcxVjMzOC4wM2MwLTIuNjY5LDAuODQ5LTQuODUzLDIuNTYtNi41NmMxLjcwOC0xLjcxOSwzLjg5Ny0yLjU3NCw2LjU3LTIuNTc0aDkxLjM1NCAgIGMyLjY2MywwLDQuODU0LDAuODU1LDYuNTcxLDIuNTc0YzEuNzA3LDEuNzA3LDIuNTYyLDMuODkxLDIuNTYyLDYuNTZWMzkyLjg1eiBNNDM4LjUzNiwyODMuMjIxYzAsMi42NjMtMC44NTUsNC44NTQtMi41NjIsNi41NjQgICBjLTEuNzE4LDEuNzE0LTMuOTA4LDIuNTY5LTYuNTcxLDIuNTY5aC05MS4zNTRjLTIuNjczLDAtNC44NjItMC44NTUtNi41Ny0yLjU2OWMtMS43MTEtMS43MTEtMi41Ni0zLjkwMS0yLjU2LTYuNTY0di01NC44MTkgICBjMC0yLjY2NCwwLjg0OS00Ljg1NCwyLjU2LTYuNTY3YzEuNzA4LTEuNzA5LDMuODk3LTIuNTY1LDYuNTctMi41NjVoOTEuMzU0YzIuNjYzLDAsNC44NTQsMC44NTUsNi41NzEsMi41NjUgICBjMS43MDcsMS43MTMsMi41NjIsMy45MDMsMi41NjIsNi41NjdWMjgzLjIyMXogTTQzOC41MzYsMTczLjU4N2MwLDIuNjY2LTAuODU1LDQuODUzLTIuNTYyLDYuNTY3ICAgYy0xLjcxOCwxLjcwOS0zLjkwOCwyLjU2OC02LjU3MSwyLjU2OGgtOTEuMzU0Yy0yLjY3MywwLTQuODYyLTAuODU5LTYuNTctMi41NjhjLTEuNzExLTEuNzE1LTIuNTYtMy45MDEtMi41Ni02LjU2N1YxMTguNzcgICBjMC0yLjY2NiwwLjg0OS00Ljg1NiwyLjU2LTYuNTY3YzEuNzA4LTEuNzEzLDMuODk3LTIuNTY4LDYuNTctMi41NjhoOTEuMzU0YzIuNjYzLDAsNC44NTQsMC44NTUsNi41NzEsMi41NjggICBjMS43MDcsMS43MTEsMi41NjIsMy45MDEsMi41NjIsNi41NjdWMTczLjU4N3oiIGZpbGw9IiMwMDAwMDAiLz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8Zz4KPC9nPgo8L3N2Zz4K" />',
                i18n: {
                    default: 'Table'
                }
            };
        }

        /**
         * javascript & css to load
         */
        configure() {
            this.js = 'include/editTable/jquery.edittable.js';
            this.css = 'include/editTable/jquery.edittable.css';
        }

        /**
         * build a text DOM element, supporting other jquery text form-control's
         * @return {Object} DOM Element to be injected into the form.
         */
        build() {
            //console.log(this)
            let editableFilds = [];
            let mainId = this.config.name;
            let hiddenInputId = "editable-data-" + mainId;
            editableFilds.push(this.markup('input', null, { type: "text", width: "100%", id: hiddenInputId, name: hiddenInputId })); //type: "hidden"
            editableFilds.push(this.markup('div', null, { id: this.config.name }));
            return this.markup('div', editableFilds, { class: "editable-container" });
        }


        /**
         * onRender callback
         */
        onRender() {
            //console.log(this.config)
            let headrAry = [];
            let fieldTypesAry = [];
            let slctOptionsObj = {};
            let numOptionsObj = {};
            const values = this.config.placeholder;
            //console.log(this.config.userData)
            if (values !== undefined) { //this.config.value
                let Columns = values.replace(/&quot;/g, "\"")
                try {
                    let colObj = JSON.parse(Columns);
                    //console.log(colObj)
                    $.each(colObj, function (i, col) {
                        headrAry.push(col.name);
                        fieldTypesAry.push(col.type);
                        if (col.attr != undefined && col.attr != "") {
                            let attrAry = [];
                            if (col.attr.indexOf(",") != -1) {
                                attrAry = col.attr.split(",");
                            } else {
                                attrAry[0] = col.attr;
                            }
                            if (col.type == "select") {
                                let slctOptions = "<option value=''></option>";
                                $.each(attrAry, function (j, opt) {
                                    slctOptions += "<option value='" + opt + "'>" + opt + "</option>";
                                })
                                slctOptionsObj["row-" + i] = slctOptions;
                            } else if (col.type == "number") {
                                let numOptionsAry = [];
                                $.each(attrAry, function (j, opt) {
                                    if (opt.indexOf("=") != -1) {
                                        let optAry = opt.split("=");
                                        if (optAry[0] == "min" || optAry[0] == "max" || optAry[0] == "step") {
                                            numOptionsAry.push([optAry[0], optAry[1]]);
                                        }
                                    }
                                })
                                numOptionsObj["row-" + i] = numOptionsAry;
                            }
                        }
                    });
                }
                catch (err) {
                    headrAry = ['Column1', 'Column2', 'Column3'];
                    fieldTypesAry = ["txt", "txt", "txt"];
                    //console.log(err)
                }
            } else {
                headrAry = ['Column1', 'Column2', 'Column3'];
                fieldTypesAry = ["txt", "txt", "txt"];
            }
            var editTableId = this.config.name;
            editTableObject[editTableId] = $('#' + editTableId).editTable({
                first_row: false,
                headerCols: headrAry,
                row_template: fieldTypesAry,
                field_templates: {
                    'checkbox': {
                        html: '<input type="checkbox" class="form-control editable-input" onclick="editableGetData(this,\'' + editTableId + '\')"/>',
                        getValue: function (input) {
                            return $(input).is(':checked');
                        },
                        setValue: function (input, value) {
                            if (value) {
                                return $(input).attr('checked', true);
                            }
                            return $(input).removeAttr('checked');
                        }
                    },
                    'textarea': {
                        html: '<textarea class="form-control editable-input" onkeyup="editableGetData(this,\'' + editTableId + '\')"/>',
                        getValue: function (input) {
                            return $(input).val();
                        },
                        setValue: function (input, value) {
                            return $(input).text(value);
                        }
                    },
                    'txt': {
                        html: '<input type="text" class="form-control editable-input" onkeyup="editableGetData(this,\'' + editTableId + '\')"/>',
                        getValue: function (input) {
                            return $(input).val();
                        },
                        setValue: function (input, value) {
                            return $(input).val(value);
                        }
                    },
                    'number': {
                        html: '<input type="number" class="form-control editable-input" onkeyup="editableGetData(this,\'' + editTableId + '\')"/>',
                        getValue: function (input) {
                            return $(input).val();
                        },
                        setValue: function (input, value, i) {
                            let num = $(input);
                            //load options
                            if (i !== undefined && numOptionsObj["row-" + i] !== undefined) {
                                let optsAry = numOptionsObj["row-" + i];
                                $.each(optsAry, function (j, opt) {
                                    num.attr(opt[0], opt[1])
                                })
                            }
                            return num.val(value);
                        }
                    },
                    'date': {
                        html: '<input type="date" class="form-control editable-input" onchange="editableGetData(this,\'' + editTableId + '\')"/>',
                        getValue: function (input) {
                            return $(input).val();
                        },
                        setValue: function (input, value) {
                            return $(input).val(value);
                        }
                    },
                    'select': {
                        html: '<select class="form-control editable-input" onchange="editableGetData(this,\'' + editTableId + '\')"></select>',
                        getValue: function (input) {
                            return $(input).val();
                        },
                        setValue: function (input, value, i) {
                            let select = $(input);
                            //load options
                            if (i !== undefined && slctOptionsObj["row-" + i] !== undefined) {
                                select.html(slctOptionsObj["row-" + i])
                            }
                            //selected value
                            select.find('option').filter(function () {
                                return $(this).val() == value;
                            }).attr('selected', true);
                            return select;
                        }
                    }
                }
            });
        }
    }
    controlClass.register('table', controlTable);
    return controlTable;
});

function setTableSettings(obj) {
    //console.log()
    let cols = $(obj).parent().parent().parent().find(".placeholder-wrap input").val()
    var elmInputId = $(obj).attr("id");
    //$(obj).attr("type", "button")
    let colObjAry = [];
    if (cols != undefined && cols != null && cols != "") {
        let Columns = cols.replace(/&quot;/g, "\"");
        colObjAry = JSON.parse(Columns);
    }
    var elmInputIdAry = elmInputId.split("-");
    var elmInputIdIndex = elmInputIdAry.pop();
    var elName = "cols-conf-" + elmInputIdIndex;
    //$(".fld-value").hide();
    let slectOptionHtml = function (selected) {
        let types = ["txt", "number", "date", "textarea", "select", "checkbox"];
        let option = "";
        $.each(types, function (i, typ) {
            let slcted = "";
            if (selected !== undefined && selected == typ) {
                slcted = "selected";
            }
            option += "<option value='" + typ + "' " + slcted + ">" + typ + "</option>";
        });
        return option;
    };
    if ($("#" + elName).length == 0) { // //.participant-table
        $("#" + elmInputId).parent().append("<table border=1 class='participant-table' id='" + elName + "' width='100%'>" +
            "<thead><tr><td>Column name</td><td>Column field type</td><td>field attr</td><td></td></tr></thead><tbody></tbody><tfoot>" +
            "<tr id='addButtonRow'><td colspan='3'><center>" +
            "<button class='btn-large btn-success' type='button' onclick='addRowParticipantTable(\"" + elName + "\",this)'>Add</button>" +
            "</center></td></tr>" +
            "</tfoot></table>");
        if (colObjAry.length > 0) {
            $.each(colObjAry, function (i, col) {
                let colName = col.name;
                let colAttr = col.attr;
                let colType = col.type;
                let isAttrDisabled = "";
                if (colType != "select" && colType != "number") {
                    isAttrDisabled = "disabled";
                }
                let option = slectOptionHtml(colType);
                let tr = "<tr class='participantRow_" + elName + "'>" +
                    "<td><input name='" + elmInputId + "' id='txt-" + elmInputId + "' type='text' data-type='colname' placeholder='Column Name' value='" + colName + "' class='form-control' onkeyup='setNewTblColumnName(\"" + elName + "\",this,\"" + obj + "\")' /></td>" +
                    "<td><select name='" + elmInputId + "' id='slct-" + elmInputId + "' type='select' data-type='coltype' class='form-control' onchange='setNewTblColumnName(\"" + elName + "\",this,\"" + obj + "\")'>" + option + "</select></td>" +
                    "<td><input name='" + elmInputId + "' id='attr-" + elmInputId + "' type='text' data-type='colattr' placeholder='Attr' value='" + colAttr + "' class='form-control' onkeyup='setNewTblColumnName(\"" + elName + "\",this,\"" + obj + "\")' " + isAttrDisabled + " /></td>" +
                    "<td><button class='btn-danger remove icon-cancel' type='button' onclick='removeRowParticipantTable(\"" + elName + "\",this)'>Remove</button></td></tr>";
                $("#" + elName + " tbody").append(tr);
            })
        } else {
            let option = slectOptionHtml("text");
            let tr = "<tr class='participantRow_" + elName + "'>" +
                "<td><input name='" + elmInputId + "' id='txt-" + elmInputId + "' type='text' data-type='colname' placeholder='Column Name' class='form-control' onkeyup='setNewTblColumnName(\"" + elName + "\",this,\"" + obj + "\")' /></td>" +
                "<td><select name='" + elmInputId + "' id='slct-" + elmInputId + "' type='select' data-type='coltype' class='form-control' onchange='setNewTblColumnName(\"" + elName + "\",this,\"" + obj + "\")'>" + option + "</select></td>" +
                "<td><input name='" + elmInputId + "' id='attr-" + elmInputId + "' type='text' data-type='colattr' placeholder='Attr' class='form-control' onkeyup='setNewTblColumnName(\"" + elName + "\",this,\"" + obj + "\")' /></td>" +
                "<td><button class='btn-danger remove icon-cancel' type='button' onclick='removeRowParticipantTable(\"" + elName + "\",this)'>Remove</button></td></tr>";
            $("#" + elName + " tbody").append(tr);
        }
    }
}

function addRowParticipantTable(id, obj) {
    //console.log($($(obj).closest("table")[0]).find(".participantRow_" + id))
    //$($(".participantRow_" + id)[0]).clone(true, true).appendTo("#" + id + " tbody");
    $($(".participantRow_" + id)[0]).clone(true, true).appendTo($($(obj).closest("table")[0]).find("tbody"));
    //console.log($($(obj).closest("table")[0]).find(".participantRow_" + id).length)
    if ($($(obj).closest("table")[0]).find(".participantRow_" + id).length === 1) {
        //$(".remove").hide();
        $($(obj).closest("table")[0]).find(".remove").hide();
    } else {
        //$(".remove").show();
        $($(obj).closest("table")[0]).find(".remove").show();
    }
}

function removeRowParticipantTable(id, obj) {
    if ($($(obj).closest("table")[0]).find(".participantRow_" + id).length === 1) {
        //$(".remove").hide();
        $($(obj).closest("table")[0]).find(".remove").hide();
    } else if ($($(obj).closest("table")[0]).find(".participantRow_" + id).length - 1 == 1) {
        //$(".remove").hide();
        $($(obj).closest("table")[0]).find(".remove").hide();
        $(obj).closest("tr").remove();
    } else {
        $(obj).closest("tr").remove();
    }
}

function setNewTblColumnName(id, obj, pObject) {
    //console.log(pObject)
    var elName = $(obj).attr("name"); //values-frmb-1558790017998-fld-1
    var elNameAry = elName.split("-");
    var inputValueId = "placeholder-frmb-" + elNameAry[2] + "-fld-" + elNameAry[4];
    //$("#" + inputValueId).parent().hide();
    var tblColsNamesAry = new Array();
    var allRows = $(".participantRow_" + id);
    $.each(allRows, function (i, row) {
        //console.log($($(row)[0].cells))
        var columnObj = new Object();
        $.each($($(row)[0].cells), function (j, cell) {
            //var td = $(cell);
            var input = $($($(cell)[0])[0].firstChild);
            //console.log(input.attr("type"), input.val())
            if (input.attr("data-type") == "colname") {
                columnObj.name = input.val();
                //tblColsNamesAry[i] = input.val();
            }
            if (input.attr("data-type") == "coltype") {
                columnObj.type = input.val();
            }
            if (input.attr("data-type") == "colattr") {
                columnObj.attr = input.val();
            }
        })
        //console.log(columnObj)
        //tblColsNamesAry[i] = columnObj;
        tblColsNamesAry.push(columnObj);
    })
    var tblColsNamesStr = "";
    if (tblColsNamesAry.length > 0) {
        //tblColsNamesStr = tblColsNamesAry.join(",")
        tblColsNamesStr = JSON.stringify(tblColsNamesAry);
        tblColsNamesStr = tblColsNamesStr.replace(/"/g, "&quot;");
        //console.log(tblColsNamesStr)
    }
    $("#" + inputValueId).val(tblColsNamesStr)
    //console.log(elName, tblColsNamesAry, tblColsNamesStr)
}

function editableGetData(obj, tblElmId) {
    $("#editable-data-" + tblElmId).val(editTableObject[tblElmId].getJsonData())
    //console.log(editTableObject[tblElmId].getJsonData()) //
}