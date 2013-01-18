<?php
include_once('curl.php');

$curl = new CURLCalls();

$curlUrl = "api.transloc.com/1.1/agencies.json?agencies=".$_GET['agency_id']."";

$agencies = $curl->getResponse($curlUrl);

/**
 * show the list of agencies
 */
?>
<table border="1">
    <tr>
        <td width="5%">Agency Id</td>
        <td width="20%">Agency Name</td>
        <td width="10%">Short Name</td>
        <td width="10%">Lat</td>
        <td width="10%">Lng</td>
        <td width="10%">Phone</td>
        <td width="25%">URL</td>
        <td width="10%"></td>
    </tr>
    <?php
    if (isset($agencies->data) && !empty($agencies->data)) {

        foreach ($agencies->data as $agency) {
            ?>
            <tr>
                <td><?php echo $agency->agency_id;?></td>
                <td><?php echo $agency->long_name;?></td>
                <td><?php echo $agency->short_name;?></td>
                <td><?php echo $agency->position->lat;?></td>
                <td><?php echo $agency->position->lng;?></td>
                <td><?php echo $agency->phone;?></td>
                <td><a href="<?php echo $agency->url?>"><?php echo $agency->url;?></a></td>
                <td><a href="routes.php?agency_id=<?php echo $agency->agency_id?>">Click to get Routes</a></td>
            </tr>
            <?php
        }
    } else {
        ?>
        <tr>
            <td colspan="8">No data to display</td>
        </tr>
        <?php
    }
    ?>
</table>