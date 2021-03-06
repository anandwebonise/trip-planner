<!DOCTYPE html>
<html>
<head>
<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
<meta content="utf-8" http-equiv="encoding">
<title>Google Maps JavaScript API v3 Example: Transit</title>
<link href="jquery-ui-1.9.2.custom.min.css" media="screen" rel="Stylesheet" type="text/css"/>
<script src="jquery-1.8.2.min.js" type="text/javascript"></script>
<script src="jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#travelDate").datepicker({
            showOn:"button",
            buttonImage:"images/calendar.gif",
            buttonImageOnly:true,
            dateFormat:'yy-mm-dd',
            minDate:new Date()
        });
    });
</script>
<style type="text/css">
    html, body {
        height: 100%;
        padding: 0;
        margin: 0;
        color: black;
        font-family: arial, sans-serif;
        font-size: 13px;
    }

    #map {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 50%;
    }

    #panel-wpr {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        right: 0;
        overflow: auto;
    }

    #panel {
        font-family: arial;
        padding: 0 5px;
    }

    #info {
        padding: 5px;
    }

    #from {
        width: 90%;
        font-size: 1.2em;
    }

    #to {
        width: 90%;
        font-size: 1.2em;
    }

    .adp-directions {
        width: 100%;
    }

    .input {
        background-color: white;
        padding-left: 8px;
        border: 1px solid #D9D9D9;
        border-top: 1px solid silver;
        -webkit-border-radius: 1px;
        -moz-border-radius: 1px;
        border-radius: 1px;
    }

    .time {
        margin: 0;
        height: 17px;
        border: 1px solid;
        border-top-color: #CCC;
        border-right-color: #999;
        border-left-color: #999;
        border-bottom-color: #CCC;
        padding: 2px 15px 1px 1px;
    }

    button {
        border: 1px solid #3079ED;
        color: white;
        background-color: #4D90FE;
        background-image: -webkit-gradient(linear, left top, left bottom, from(#4D90FE), to(#4787ED));
        background-image: -webkit-linear-gradient(top, #4D90FE, #4787ED);
        background-image: -moz-linear-gradient(top, #4D90FE, #4787ED);
        background-image: -ms-linear-gradient(top, #4D90FE, #4787ED);
        background-image: -o-linear-gradient(top, #4D90FE, #4787ED);
        background-image: linear-gradient(top, #4D90FE, #4787ED);
        filter: progid:DXImageTransform.Microsoft.gradient(startColorStr = '#4d90fe', EndColorStr = '#4787ed');
        display: inline-block;
        min-width: 54px;
        text-align: center;
        font-weight: bold;
        padding: 0 8px;
        line-height: 27px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        border-radius: 2px;
        -webkit-transition: all 0.218s;
        -moz-transition: all 0.218s;
        -o-transition: all 0.218s;
        transition: all 0.218s;
    }

    #info div {
        line-height: 22px;
        font-size: 110%;
    }

    .btn {
    }

    #panel-wpr {
        border-left: 1px solid #e6e6e6;
    }

    #info {
        border-bottom: 1px solid #E6E6E6;
        margin-bottom: 5px;
    }

    h2 {
        margin: 0;
        padding: 0;
    }

</style>

<?php
date_default_timezone_set('America/New_York');
//Thu Jan 17 2013 19:13:45 GMT+0530
$today = date('Y-m-d');//show it in textbox

?>

<script type="text/javascript">

    $(document).ready(function () {
        $('#travelDate').val('<?php echo $today;?>');
    });

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

        addDepart();
        route();
    }

    function addDepart() {
        var departHr = document.getElementById('departHr');
        var departMin = document.getElementById('departMin');
        for (var hr = 1; hr <= 12; hr++) {
            departHr.innerHTML += '<option value = "'+hr+'">' + hr + '</option>';
        }

        for (var i = 0; i < 12; i++) {
            for (var j = 0; j < 60; j += 5) {
                var x = i < 10 ? '0' + i : i;
                var y = j < 10 ? '0' + j : j;
                departMin.innerHTML += '<option value = "'+y+'">' + y + '</option>';
            }
        }
    }

    function formatAMPM() {
        var hours = document.getElementById('departHr').value;
        var minutes = document.getElementById('departMin').value;

        if (document.getElementById('timeFormat').value == 'pm') {
            hours = 12 + parseInt(hours);
        }
        console.log('hours ==== ' + hours);
        console.log('minutes ==== ' + minutes);
        var strTime = hours + ':' + minutes;
        return strTime;
    }

    function route() {
        var startLocation = document.getElementById('from').value;
        var endLocation = document.getElementById('to').value;
        var selectedMode = document.getElementById('modeOfTransportation').value;
        var timeChosen = formatAMPM();

        //create date format
       // var dateIs = createDate(document.getElementById('travelDate').value, timeChosen);
        $.ajax({
            url:'date.php',
            type:'POST',
            data:{'date':document.getElementById('travelDate').value, 'time':timeChosen},
            success:function(data){
                var dateIs = data;
                console.log('return by ajax==='+dateIs);

                var finalDate = new Date(dateIs);

                console.log('date sdsd ====' + finalDate);

                if (document.getElementById("arrivalRadio").checked) {
                    console.log('in the arrivalRadio');
                    var request = {
                        origin:startLocation,
                        destination:endLocation,
                        travelMode:google.maps.TravelMode[selectedMode],
                        provideRouteAlternatives:true,
                        transitOptions:{
                            arrivalTime:finalDate
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
                            departureTime:finalDate
                        }
                    };
                }

                var panel = document.getElementById('panel');
                panel.innerHTML = '';
                directions.route(request, function (response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        renderer.setDirections(response);
                        renderer.setMap(map);
                        renderer.setPanel(panel);
                    } else {
                        renderer.setPanel(null);
                        //alert(status);
                    }
                });
            }
        });

    }
    google.maps.event.addDomListener(window, 'load', initialize);



</script>
</head>
<body>
<div id="transit-wpr">
    <button id="transit">Toggle transit layer</button>
</div>
<div id="map"></div>
<div id="panel-wpr">
    <div id="info">
        <div>Mode</div>
        <div><select id="modeOfTransportation">
            <option value="DRIVING">Driving</option>
            <option value="WALKING">Walking</option>
            <option value="BICYCLING">Bicycling</option>
            <option value="TRANSIT" selected>Transit</option>
        </select></div>
        <div>
            <h2>Transit directions</h2>
        </div>
        <div>
            <label>Start:</label>
            <input class="input" id="from" value="Raleigh Rd at Finley Golf Course Rd, Chapel Hill, NC">
        </div>
        <div>
            <label>End:</label>
            &nbsp;<input class="input" id="to" value="Durham Chapel Hill Blvd at Fosters, Durham, NC">
        </div>
        <div>
            <input type="radio" name="arrive" value="false" id="departureRadio" checked="checked">
            <label>Depart After</label>
            <input type="radio" name="arrive" value="true" id="arrivalRadio">
            <label>Arrive Before</label>
        </div>
        <div>
            <label>Date Of Travel</label>
            <input type="text" name="date" id="travelDate" style="width: 100px">

        </div>
        <div><label>Timing</label>&nbsp;<select id="departHr"></select>&nbsp;<select
            id="departMin"></select>&nbsp;<select id="timeFormat">
            <option value="pm">PM</option>
            <option value="am">AM</option>
        </select></div>
        <div class="btn">
            <button id="go">Get Directions</button>
        </div>
    </div>
    <div id="panel"></div>
</div>
</body>
</html>
