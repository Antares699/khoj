<?php
$qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: pages/claim.php' . $qs, true, 301);
exit;
