<?php
$qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: pages/about.php' . $qs, true, 301);
exit;
