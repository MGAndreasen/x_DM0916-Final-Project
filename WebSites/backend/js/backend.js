$(document).ready(function () {

    $(document).ajaxError(function (event, xhr, settings) {
        ajaxFejl();
    });

    $(document).ajaxSuccess(function (event, xhr, settings) {
        ajaxOk(xhr);
    });

    //$.ajax({
    //    url: "/backend/ajax.php",
    //    dataType: "json",
    //    data: {
    //        ctrl: "project",
    //        func: "getProjects"
    //    }
    //});

    myPost('projects', 'getProject', [1,2]);
});

function myPost(ctrl, func, parms) {
    var mydata = new Array();
    mydata['ctrl'] = ctrl;
    mydata['func'] = func;
    mydata['parms'] = parms;
    $.ajax({
        type: "POST",
        url: "/backend/ajax.php",
        data: JSON.stringify({ Resp: mydata }),
        contentType: "application/json; charset=utf-8",
        dataType: "json" });
}

// Functions
function ajaxOk(result) {
    var rawData = result.responseText;
    var jsonData = JSON.parse(rawData);
    console.log(jsonData);
    if (jsonData['errors']) {
        $.each(jsonData['errors'], function (key, value) {
            alert("Ctrl: " + value['ERRCTRL'] + "\nFunc: " + value['ERRFUNC'] + "\nMSG: " + value['ERRMSG']);
        });
    }
    else {
        $("#status").html("<p>STATUS: OK!</p>");
    }
    
}

function ajaxFejl() {
    alert("Could not load ajax.php");
}