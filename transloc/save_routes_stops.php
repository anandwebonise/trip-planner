<?php
include_once('curl.php');
include_once('connection.php');

$agencyId = $_GET['agency_id'];

$curl = new CURLCalls();

$curlRoutesUrl = "api.transloc.com/1.1/routes.json?agencies=" . $agencyId . "";

$routes = $curl->getResponse($curlRoutesUrl);

$ttaRoutes = $routes->data->{$agencyId};

//save routes
if (!empty($ttaRoutes)) {

    //drop old entries
    mysql_query('TRUNCATE routes');
    mysql_query('TRUNCATE stop_routes');

    foreach ($ttaRoutes as $ttaRoute) {

        $routes = "insert into routes (id, name, short_name, type) values('" . $ttaRoute->route_id . "',
        '" . $ttaRoute->long_name . "','" . $ttaRoute->short_name . "', '" . $ttaRoute->type . "')";
        $query = mysql_query($routes);

        if (!empty($ttaRoute->stops)) {

            foreach ($ttaRoute->stops as $key => $stopRoute) {
                $stopsRoutes = "insert into stop_routes (stop_id, route_id) values('" . $ttaRoute->stops[$key] . "',
                '" . $ttaRoute->route_id . "')";

                if (mysql_query($stopsRoutes)) {

                }
            }
        }
    }
}


//get save stops
$curlStopsUrl = "api.transloc.com/1.1/stops.json?agencies=" . $agencyId . "";

$stops = $curl->getResponse($curlStopsUrl);

$ttaStops = $stops->data;

if (!empty($ttaStops)) {
    //drop old entries
    mysql_query('TRUNCATE stops');

    foreach ($ttaStops as $ttaStop) {

        $stops = "insert into stops (id,name, code, type, lat, lng)
        values('" . $ttaStop->stop_id . "','" . $ttaStop->name . "','" . $ttaStop->code . "',
        '" . $ttaStop->location_type . "','" . $ttaStop->location->lat . "','" . $ttaStop->location->lng . "')";

        $query = mysql_query($stops);
    }
}
