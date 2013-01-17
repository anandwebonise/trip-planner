<?php
date_default_timezone_set('America/New_York');
$dataIs = date(''.$_POST['date'].' '.$_POST['time'].'');

echo $year = date("D M d Y H:i:s TO", strtotime($dataIs));