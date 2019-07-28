
function apply(checkboxElement) {
    //this should become universal based on toggle name to update database key automatically
    var onoff = document.getElementsByName(checkboxElement.name);
    var key = onoff[0].name;
    if (onoff[0].checked) { var value = 'true'; } else { var value = 'false'; }
    $.ajax({
        type: "POST",
        url: "updateConfig.php",
        data: {key: key, value: value},
        success: function(response) {
            if (response == '') {
                toastr.success('Updated configuration', 'Success', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            } else {
                toastr.warning(response, 'Error', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            }
        }
    })
}

function getRestorePathsFromDB(data) {
    var json = JSON.parse(JSON.stringify(data));
    for (i=0; i<json.length; i++) {
        var obj = json[i][0];
        var Name = obj["Name"];
        var Path = obj["Path"];
        addPathRow("restoreTable");
        var numColumns = document.getElementsByName('restoreTableName').length;
        document.getElementsByName('restoreTableName')[numColumns-1].value = Name;
        document.getElementsByName('restoreTablePath')[numColumns-1].value = Path;
    }
}

function getPermissionsFromDB(data, table) {
    var json = JSON.parse(JSON.stringify(data));
    var objName = "";
    if (table == "groupTable") {
        objName = "Group Name";
    } else {
        objName = "User Name";
    }
    for (i=0; i<json.length; i++) {
        var obj = json[i];
        var Name = obj[objName];
        var Access = obj["Access"];
        var Details = obj["Details"];
        var Restore = obj["Restore"];
        var Archive = obj["Archive"];
        var Admin = obj["Admin"];
        addPermissionsRow(table);
        var numColumns = document.getElementsByName('AccessPermission').length;
        document.getElementsByName('UserGroupName')[numColumns-1].value = Name;
        document.getElementsByName('AccessPermission')[numColumns-1].value = Access;
        document.getElementsByName('DetailsPermission')[numColumns-1].value = Details;
        document.getElementsByName('RestorePermission')[numColumns-1].value = Restore;
        document.getElementsByName('ArchivePermission')[numColumns-1].value = Archive;
        document.getElementsByName('AdminPermission')[numColumns-1].value = Admin;
    }
}

function getLDAPFromDB(data) {
    var json = JSON.parse(JSON.stringify(data));
    for (i=0; i<json.length; i++) {
        var obj = json[i];
        document.getElementById("ldap_host").value = obj["ldap_host"];
        document.getElementById("ldap_dn").value = obj["ldap_dn"];
        document.getElementById("ldap_domain").value = obj["ldap_domain"];
    }
}

function getDIVAFromDB(data) {
    var json = JSON.parse(JSON.stringify(data));
    for (i=0; i<json.length; i++) {
        var obj = json[i];
        document.getElementById("diva_wsdl").value = obj["diva_wsdl"];
        document.getElementById("diva_endpoint").value = obj["diva_endpoint"];
    }
}

function getPathTableRows() {
    var names = [];
    var paths = [];
    var data = [];
    var numNames = document.getElementsByName('restoreTableName').length;
    for (i=0; i<numNames; i++) {
        names.push(document.getElementsByName('restoreTableName')[i].value);
        paths.push(document.getElementsByName('restoreTablePath')[i].value);
    }
    if ( hasDuplicates(names) ) {
        return "";
    }
    for (i=0; i<names.length; i++) {
        if (names[i] == "" && paths[i] != "") {
            var obj = [];
            obj.push( { Name:paths[i], Path:paths[i] } );
        }
        if (names[i] != "" && paths[i] == "") {
            continue;
        }
        if (names[i] == "" && paths[i] == "") {
            continue;
        }
        if (names[i] != "" && paths[i] != "") {
            var obj = [];
            obj.push( { "Name":names[i], "Path":paths[i] } );
        }
        data.push(obj);
    }
    var json = JSON.stringify(data);

    return json;
}

function hasDuplicates(dataArray) {
    for (i=0; i<dataArray.length; i++) {
        var item = dataArray[i];
        for (p=0; p<dataArray.length; p++) {
            if (item == dataArray[p] && i != p) {
                return true;
            }
        }
    }
    return false;
}

function setRestorePathsToDB() {
    var tableData = getPathTableRows();
    if ( tableData == "" ) {
        toastr.error('Cannot have duplicate names','Restore Paths', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
        return;
    }
    $.ajax({
        type: "POST",
        url: "updateConfig.php",
        data: {key: "restorePaths", value: tableData},
        success: function(response) {
            if (response == '') {
                toastr.success('Configuration successfully updated', 'Restore Paths', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            } else {
                toastr.warning(response, 'Restore Paths', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            }
        }
    })
}

function openConfigPane(evt, paneName) {
    // Declare all variables
    var i, tabcontent, tablinks;

    // Get all elements with class="tabcontent" and hide them
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab, and add an "active" class to the link that opened the tab
    document.getElementById(paneName).style.display = "block";
    evt.currentTarget.className += " active";
} 

function getAccessTableRows(table) {
    var jTable = "#" + table;
    jQuery.fn.shift = [].shift;
    var $rows = $(jTable).find('tr:not(:hidden)');
    var headers = [];
    var data = [];
    
    // Get the headers (add special header logic here)
    $($rows.shift()).find('th:not(:empty)').each(function () {
        headers.push($(this).text());
    });
    
    // Turn all existing rows into a loopable array
    $rows.each(function () {
        var $td = $(this).find('td');
        var h = {};
        
        // Use the headers from earlier to name our hash keys
        headers.forEach(function (header, i) {
            if ($td.eq(i).text() == "DisabledEnabled" || $td.eq(i).text() == "EnabledDisabled") {
                h[header] = $td.eq(i).find("option:selected").val();
            } else {
                h[header] = $td.eq(i).find("input:text").val();
            }
        });
        data.push(h);
    });
    return data;
}

function checkTableDuplicates(table) {
    var jTable = "#" + table;
    jQuery.fn.shift = [].shift;
    var $rows = $(jTable).find('tr:not(:hidden)');
    var headers = [];
    var data = [];
    
    // Get the headers (add special header logic here)
    $($rows.shift()).find('th:not(:empty)').each(function () {
        headers.push($(this).text());
    });
    
    // Turn all existing rows into a loopable array
    $rows.each(function () {
        var $td = $(this).find('td');
        var h = {};
        // Use the headers from earlier to name our hash keys
        headers.forEach(function (header, i) {
            if ($td.eq(i).text() == "DisabledEnabled" || $td.eq(i).text() == "EnabledDisabled") {
                // nothing
            } else {
                h[header] = $td.eq(i).find("input:text").val();
            }
        });
        data.push(h);
    });

    var duplicates = false;
    for (var i=0;i<data.length; i++) {
        for (var j=0;j<data.length; j++) {
            if (i != j) {
                if (JSON.stringify(data[i]) == JSON.stringify(data[j])) {
                    duplicates = true;
                    break;
                }
            }
        }
    }
    return duplicates;
}

function propogatePermissions(jsonData) {
    var json = JSON.parse(JSON.stringify(jsonData));
    var clean = [];
    for (i=0; i<json.length; i++) {
        var obj = json[i];
        if (obj["Group Name"] == "" || obj["User Name"] == "") {
            continue;
        }
        if (obj["Details"] == "true") {
            obj["Access"] = "true";
        }
        if (obj["Restore"] == "true") {
            obj["Details"] = "true";
            obj["Access"] = "true";
        }
        if (obj["Admin"] == "true") {
            obj["Archive"] = "true";
            obj["Restore"] = "true";
            obj["Details"] = "true";
            obj["Access"] = "true";
        }
        clean.push(obj);
    }
    return clean;
}

function setPermissionsToDB(table) {
    var duplicates = checkTableDuplicates(table);
    if ( duplicates ) {
        toastr.error('Cannot have duplicate groups','Group Management', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
        return;
    }
    var tableData = getAccessTableRows(table);
    var cleanData = JSON.stringify(propogatePermissions(tableData));
    var key = "";
    if (table == "groupTable") {
        key = "groupPermissions";
    } else if (table == "usersTable") {
        key = "userPermissions";
    }
    $.ajax({
        type: "POST",
        url: "updateConfig.php",
        data: {key: key, value: cleanData},
        success: function(response) {
            if (response == '') {
                toastr.success('Configuration successfully updated', 'Permissions', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            } else {
                toastr.warning(response, 'Permissions', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            }
        }
    })
}

function addPermissionsRow(table) {
    jTable = "#" + table;
    userGroup = table + "Name";

    $(jTable).append(`<tr>
        <td><input type="text" name="UserGroupName" style="width:98%"/></td>
        <td><select name="AccessPermission" style="width:98%"><option value="true">Enabled</option><option value="false">Disabled</option></select></td>
        <td><select name="DetailsPermission" style="width:98%"><option value="false">Disabled</option><option value="true">Enabled</option></select></td>
        <td><select name="RestorePermission" style="width:98%"><option value="false">Disabled</option><option value="true">Enabled</option></select></td>
        <td><select name="ArchivePermission" style="width:98%"><option value="false">Disabled</option><option value="true">Enabled</option></select></td>
        <td><select name="AdminPermission" style="width:98%"><option value="false">Disabled</option><option value="true">Enabled</option></select></td>
        <td><button type="button" class="removebutton" title="Remove Row">X</button></td></tr>`);
}

function addPathRow(table){
    jTable = "#" + table;
    locationName = table + "Name";
    locationPath = table + "Path";
    $(jTable).append('<tr><td><input type="text" name="' + locationName + '" style="width:98%"/></td><td><input type="text" name="' + locationPath + '" style="width:98%"/></td><td><button type="button" class="removebutton" title="Remove Row">X</button></td></tr>');
}

function getLDAPTableData() {
    var ldap = [];
    var ldap_host = document.getElementById("ldap_host").value;
    var ldap_dn = document.getElementById("ldap_dn").value;
    var ldap_domain = document.getElementById("ldap_domain").value;
    if (ldap_domain.charAt(0) != "@") {
        ldap_domain = "@" + ldap_domain;
    }
    ldap.push({ "ldap_host":ldap_host, "ldap_dn":ldap_dn, "ldap_domain":ldap_domain });
    return JSON.stringify(ldap);
}

function getDIVATableData() {
    var diva = [];
    var diva_wsdl = document.getElementById("diva_wsdl").value;
    var diva_endpoint = document.getElementById("diva_endpoint").value;
    diva.push({ "diva_wsdl":diva_wsdl, "diva_endpoint":diva_endpoint });
    return JSON.stringify(diva);
}

function setLDAPConfigToDB() {
    var data = getLDAPTableData();
    if ( data == "" ) {
        toastr.error('Must enter data before saving','LDAP Management', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
        return;
    }
    $.ajax({
        type: "POST",
        url: "updateConfig.php",
        data: {key: "ldapConfig", value: data},
        success: function(response) {
            if (response == '') {
                toastr.success('Configuration successfully updated', 'LDAP Management', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            } else {
                toastr.warning(response, 'LDAP Management', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            }
        }
    })
}

function setDIVAConfigToDB() {
    var data = getDIVATableData();
    if ( data == "" ) {
        toastr.error('Must enter data before saving','DIVA Management', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
        return;
    }
    $.ajax({
        type: "POST",
        url: "updateConfig.php",
        data: {key: "divaConfig", value: data},
        success: function(response) {
            if (response == '') {
                toastr.success('Configuration successfully updated', 'DIVA Management', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            } else {
                toastr.warning(response, 'DIVA Management', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            }
        }
    })
}

$(document).on('click', 'button.removebutton', function () {
    $(this).closest('tr').remove();
    return false;
})

function refreshAPI() {
    //document.getElementById(id).setAttribute("disabled", "disabled");
    toastr.info('API session key refreshing', 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
    $("#api").attr("disabled", "disabled");
    $.ajax({
        type: "POST",
        url: "getSessionCode.php",
        success: function(response) {
            if (response == '') {
                toastr.success('API session key updated', 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
                updateAPITroubleshoot();
            } else {
                toastr.warning(response, 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            }
            $("#api").removeAttr("disabled");
        }
    })
}

function updateAPITroubleshoot() {
    $.ajax({
        url: "troubleshoot.php?f=getCurrentKey",
        type: "GET",
        data: { },
        success:function(data) {
            document.getElementById("divakey").innerHTML = '(' + data + ')';
        }
    });
}

function cleanSessions() {
    //document.getElementById(id).setAttribute("disabled", "disabled");
    toastr.info('Removing old sessions', 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
    $.ajax({
        type: "POST",
        url: "cleanSessions.php",
        success: function(response) {
            if (response == '') {
                toastr.success('Old sessions removed', 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            } else {
                toastr.warning(response, 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            }
        }
    })
}

function cleanSiteTracker() {
    //document.getElementById(id).setAttribute("disabled", "disabled");
    toastr.info('Removing old sessions', 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
    $.ajax({
        type: "POST",
        url: "cleanSiteTracker.php",
        success: function(response) {
            if (response == '') {
                toastr.success('Old usage logs removed', 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            } else {
                toastr.warning(response, 'Troubleshoot', {"positionClass": "toast-bottom-right", "preventDuplicates": false, "onclick": null});
            }
        }
    })
}