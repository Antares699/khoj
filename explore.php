<?php
$qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: pages/explore.php' . $qs, true, 301);
exit;
