<?php
session_start();
session_destroy();
$base = dirname(dirname($_SERVER['PHP_SELF']));
header("Location: $base/views/showproduct.php");
exit;
