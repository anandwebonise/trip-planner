<?php
include_once('curl.php');

$agencyId = $_GET['agency_id'];

$curl = new CURLCalls();

$curlRoutesUrl = "api.transloc.com/1.1/routes.json?agencies=".$agencyId."";

$routes = $curl->getResponse($curlRoutesUrl);

$ttaRoutes = $routes->data->{$agencyId};

//$curlStopsUrl = "api.transloc.com/1.1/routes.json?agencies=".$agencyId."";
//
//$stops = $curl->getResponse($curlStopsUrl);
//
echo '<pre> object is======';
print_r($ttaRoutes);
echo '</pre>';
/**
 * show the list of agencies
 */
?>
<table border="1">
    <tr>
         <td colspan="4">Routes & Stops for the TTA </td>
    </tr>
    <?php
    if (isset($ttaRoutes) && !empty($ttaRoutes)) {

        foreach ($ttaRoutes as $route) {

            ?>
            <tr>
                <td>Route Id : <?php echo $route->route_id;?></td>
                <td>Route Name : <?php echo $route->long_name;?></td>
                <td>Route Short Name : <?php echo $route->short_name;?></td>
                <td>Route Type : <?php echo $route->type;?></td>
            </tr>

            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>

            <tr>
                <td colspan="4"><b>Stops</b></td>
            </tr>

            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
                <?php
            //if(){

            //}
            ?>

            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="4">No routes data to display</td>
        </tr>
        <?php
    }
    ?>
</table>