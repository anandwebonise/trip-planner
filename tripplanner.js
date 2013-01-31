
var directions = new google.maps.DirectionsService();
var renderer = new google.maps.DirectionsRenderer();
var startLocationAutocomplete;
var endLocationAutocomplete;
var map, transitLayer;

function initialize() {
    var mapOptions = {
        zoom:14,
        center:new google.maps.LatLng(51.538551, -0.016633),
        mapTypeId:google.maps.MapTypeId.ROADMAP
    };

    map = new google.maps.Map(document.getElementById('map'), mapOptions);

    google.maps.event.addDomListener(document.getElementById('go'), 'click', route);


    var defaultBounds = new google.maps.LatLngBounds(
        new google.maps.LatLng(35.371359, -79.319916),
        new google.maps.LatLng(36.231424, -77.752991));


    var autocompleteOptions = {
        bounds:defaultBounds,
        types:[ "locality", "political", "geocode" ]
    };
    startLocationAutocomplete = new google.maps.places.Autocomplete(document.getElementById('from'));
    endLocationAutocomplete = new google.maps.places.Autocomplete(document.getElementById('to'));

    startLocationAutocomplete.bindTo('bounds', map);
    endLocationAutocomplete.bindTo('bounds', map);

    transitLayer = new google.maps.TransitLayer();

    var control = document.getElementById('transit-wpr');
    map.controls[google.maps.ControlPosition.TOP_RIGHT].push(control);

    google.maps.event.addDomListener(control, 'click', function () {
        transitLayer.setMap(transitLayer.getMap() ? null : map);
    });

    //addDepart();
    route();
}

/*function addDepart() {
    var departHr = document.getElementById('departHr');
    var departMin = document.getElementById('departMin');
    for (var hr = 1; hr < 12; hr++) {
        departHr.innerHTML += '<option value = "'+hr+'">' + hr + '</option>';
    }

    for (var i = 0; i < 12; i++) {
        for (var j = 0; j < 60; j += 5) {
            var x = i < 10 ? '0' + i : i;
            var y = j < 10 ? '0' + j : j;
            departMin.innerHTML += '<option value = "'+y+'">' + y + '</option>';
        }
    }
}*/

/*function formatAMPM() {
    var date = new Date();
    var hours = document.getElementById('departHr').value;
    var minutes =  document.getElementById('departMin').value;
    var ampm =  document.getElementById('timeFormat').value;
    *//*hours = hours % 12;

     hours = hours ? hours : 12; // the hour '0' should be '12'
     minutes = minutes < 10 ? '0' + minutes : minutes;*//*

    if(ampm == 'pm'){
        hours = 12 + parseInt(hours);        }
    var strTime = hours + ':' + minutes;
    return strTime;
}*/

function route() {
    var startLocation = document.getElementById('from').value;
    var endLocation = document.getElementById('to').value;
    var selectedMode = document.getElementById('modeOfTransportation').value;
 /*   var departure = formatAMPM();
    var bits = departure.split(':');
    var now = new Date();
    var tzOffset = (now.getTimezoneOffset() + 60) * 60 * 1000;

    var time = ($('#travelDate').val() != '') ? new Date($('#travelDate').val()) : new Date();

    time.setHours(bits[0]);
    time.setMinutes(bits[1]);

    var ms = time.getTime() - tzOffset;

    var departureTime = time;*/
    var timeSelected = document.getElementById("travelTime").value;
    var finalDate = new Date('Thu Jan 31 2013 00:00:00 EST-0500');
    console.log(finalDate);

    if (document.getElementById("arrivalRadio").checked) {
        console.log('in the arrivalRadio');
        var request = {
            origin:startLocation,
            destination:endLocation,
            travelMode:google.maps.TravelMode[selectedMode],
            provideRouteAlternatives:true,
            transitOptions:{
                arrivalTime: finalDate
            }
        };
    } else {
        console.log('in the departure');

        var request = {
            origin:startLocation,
            destination:endLocation,
            travelMode:google.maps.TravelMode[selectedMode],
            provideRouteAlternatives:true,
            transitOptions:{
                departureTime: finalDate
            }
        };
    }

    console.log(request);
    var panel = document.getElementById('panel');
    panel.innerHTML = '';
    directions.route(request, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            renderer.setDirections(response);
            renderer.setMap(map);
            renderer.setPanel(panel);
            console.log(status);
        } else {
            renderer.setPanel(null);
            alert(status);
        }
    });

}



$(document).ready(function() {
    //$('#travelTime').val('Thu Jan 31 2013 00:00:00 EST-0500');

  //call api on page ready
    google.maps.event.addDomListener(window, 'load', initialize);

    $("#travelDate" ).datepicker({
        showOn: "button",
        buttonImage: "images/calendar.gif",
        buttonImageOnly: true,
        dateFormat:'yy-mm-dd',
        minDate : new Date()
    });

    $('#travelTime').timepicker({
        timeFormat: 'HH:mm:ss z',
        showTimezone: true,
        minDateTime : new Date(),
        timezoneList: [
            { value: 'EST-0500', label: 'Eastern'}
        ]
    });
});