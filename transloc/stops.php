<?php
include_once('curl.php');

$agencyId = $_GET['agency_id'];

$curl = new CURLCalls();

$curlStopsUrl = "api.transloc.com/1.1/stops.json?agencies=" . $agencyId . "";

$stops = $curl->getResponse($curlStopsUrl);

$ttaStops = $stops->data;

/**
 * show the list of Stops
 */
?>
<table border="1">
    <tr>
        <td colspan="5"> Stops for the TTA</td>
    </tr>
    <?php
    if (isset($ttaStops) && !empty($ttaStops)) {

        foreach ($ttaStops as $ttaStop) {
            ?>
            <tr>
                <td>Stop Id : <?php echo $ttaStop->stop_id;?></td>
                <td>Stop Name : <?php echo $ttaStop->name;?></td>
                <td>Stop Code : <?php echo $ttaStop->code;?></td>
                <td>Stop Lat : <?php echo $ttaStop->location->lat;?></td>
                <td>Stop Lng : <?php echo $ttaStop->location->lng;

                    foreach ($ttaStop->routes as $key => $routes) {
                        echo $ttaStop->routes[$key].'<br/>';
                    }
                    ?></td>
            </tr>

            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="5">No stops data to display</td>
        </tr>
        <?php
    }
    ?>
</table>