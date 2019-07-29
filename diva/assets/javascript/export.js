$(document).ready( function() {
    $( "#datepicker1" ).datepicker( {
        showOn: "focus",
        minDate: -90,
        maxDate: new Date(),
        dateFormat: "dd-M-yy",
        numberOfMonths: 2,
        showButtonPanel: true
    } );
} );

$(document).ready( function() {
    $( "#datepicker2" ).datepicker( {
        showOn: "focus",
        minDate: -90,
        maxDate: new Date(),
        dateFormat: "dd-M-yy",
        numberOfMonths: 2,
        showButtonPanel: true
    } );
} );

function generateDate(dateString) {
    var strArray = dateString.split(/-| /);
    var month = ("JanFebMarAprMayJunJulAugSepOctNovDec".indexOf(strArray[1]) / 3);
    if (month < 10) { month = '0' + month; }
    var strDate = strArray[2] + "-" + month + "-" + strArray[0];
    return strDate;
}

function validate() {
    var splitDate = generateDate(document.forms["dates"]["startdate"].value).split('-');
    var parseDate = new Date(Date.UTC(splitDate[0], splitDate[1] - 1, splitDate[2]));
    var rawStart = parseDate.getTime() / 1000;
    var splitDate = generateDate(document.forms["dates"]["enddate"].value).split('-');
    var parseDate = new Date(Date.UTC(splitDate[0], splitDate[1] - 1, splitDate[2]));
    var rawEnd = parseDate.getTime() / 1000;

    if ( isNaN(rawStart) || isNaN(rawEnd) ) {
        alert("Must specify both start and end dates");
        return false;
    } else if (rawStart > rawEnd) {
        alert("Start date must less than or equal to end date");
        return false;
    } else {
        document.getElementById("dates").submit();
        document.getElementById("submit").style.display = "none"; // to undisplay
        document.getElementById("text1").style.display = "none"; // to undisplay
        document.getElementById("buttonreplacement").style.display = "";
    }
}