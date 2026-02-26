<?php
$qs = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
header('Location: pages/register.php' . $qs, true, 301);
exit;
