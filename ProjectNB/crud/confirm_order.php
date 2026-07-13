<?php
session_start();
unset($_SESSION['cart']);
$base = dirname(dirname($_SERVER['PHP_SELF']));
header("Location: $base/views/result.php");
exit();
