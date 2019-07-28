
function eventFire(el, etype){
    if (el.fireEvent) {
        el.fireEvent('on' + etype);
    } else {
        var evObj = document.createEvent('Events');
        evObj.initEvent(etype, true, false);
        el.dispatchEvent(evObj);
    }
}

function generateDate(dateString) {
    var strArray = dateString.split(/-| /);
    var month = ("JanFebMarAprMayJunJulAugSepOctNovDec".indexOf(strArray[1]) / 3);
    if (month < 10) { month = '0' + month; }
    var strDate = strArray[2] + "-" + month + "-" + strArray[0];
    return strDate;
}

function validate() {
    if (document.getElementById("advanced").value == "Advanced Search") {
        document.forms["search"]["categoryterm"].value = "*";
        document.forms["search"]["startdate"].value = "";
        document.forms["search"]["sindex"].value = "1";
    }
    if (document.getElementById("advanced").value == "Simple Search") {
        var rawDate = generateDate(document.forms["search"]["startdate"].value);
        var splitDate = rawDate.split('-');
        var parseDate = new Date(Date.UTC(splitDate[0], splitDate[1] - 1, splitDate[2]));
        document.forms["search"]["sindex"].value = parseDate.getTime() / 1000;
    }

    if (document.forms["search"]["searchterm"].value == "") {
        alert("Must Enter a valid search");
        return;
    } 
    if (document.forms["search"]["categoryterm"].value == "") {
        alert("Must Enter a valid object category");
        return;
    }
    document.getElementById("search").submit();
    document.getElementById("submit").style.display = "none"; // to undisplay
    document.getElementById("advanced").style.display = "none"; // to undisplay
    document.getElementById("buttonreplacement").style.display = "";
}

function advancedsearch() {
    if (document.getElementById("advancedopts").style.display == "none") {
        document.getElementById("advancedopts").style.display = "";
        document.getElementById("advanced").value = "Simple Search";
    } else {
        document.getElementById("advancedopts").style.display = "none";
        document.getElementById("advanced").value = "Advanced Search";
    }
}

function sort() {
    if (document.getElementById('coldat')) {
        eventFire(document.getElementById('coldat'), 'click');
        eventFire(document.getElementById('coldat'), 'click');
    }
}

function wasAdvanced() {
    if (document.forms["search"]["categoryterm"].value == "*") {
        if (document.forms["search"]["startdate"].value != "") {
            advancedsearch();
        }
    } else {
        advancedsearch();
    }
}

$(window).load( function() {
    $( ".datepicker" ).datepicker( {
        showOn: "focus",
        minDate: new Date(2007, 1 - 1, 1),
        maxDate: new Date(),
        dateFormat: "dd-M-yy",
        numberOfMonths: 2,
        showButtonPanel: true
    } );
} );

$(function () {
    $("table").sortpaginate({pageSize: 50});
});