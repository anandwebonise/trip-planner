<?php
include_once('connection.php');

$routes = mysql_query("select * from  routes where 1");

?>
<table border="1">
    <?php
    if (!empty($routes)) {

        while ($route = mysql_fetch_assoc($routes)) {

            ?>
            <tr>
                <td>Route Id : <?php echo $route['id'];?></td>
                <td>Route Name : <?php echo $route['name'];?></td>
                <td>Route Short Name : <?php echo $route['short_name'];?></td>
                <td>Route Type : <?php echo $route['type'];?></td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>

            <tr>
                <td colspan="4"><b>Stops</b></td>
            </tr>
            <tr>
                <td colspan="4">
                    <table border="1" width="100%">
                        <tr>
                            <td>Stop Id</td>
                            <td>Stop Name</td>
                            <td>Stop Code</td>
                            <td>Stop Lat</td>
                            <td>Stop Lng</td>
                        </tr>
                        <?php
                        $stops = mysql_query("select Stop.id, Stop.name,Stop.code,Stop.type,Stop.lat,Stop.lng from stops as Stop
            LEFT JOIN  stop_routes as SR ON (SR.stop_id = Stop.id) where SR.route_id = " . $route['id'] . "");

                        if (!empty($stops)) {

                while ($stop = mysql_fetch_assoc($stops)) {
                                ?>
                                <tr>
                                    <td><?php echo $stop['id'];?></td>
                                    <td><?php echo $stop['name'];?></td>
                                    <td><?php echo $stop['code'];?></td>
                                    <td><?php echo $stop['lat'];?></td>
                                    <td><?php echo $stop['lng'];?></td>
                                </tr>

                                <?php
                            }
            } else {
                            ?>
                            <tr>
                                <td colspan="5">No Stops for this route</td>
                            </tr>
                            <?php
                        }?>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="4">&nbsp;</td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="4">No routes to display</td>
        </tr>
        <?php
    }
    ?>
</table>