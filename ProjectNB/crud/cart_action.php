<?php
session_start();
require_once __DIR__ . '/../core/connect_database.php';

$id  = intval($_POST['id']);
$qty = intval($_POST['qty']);

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] += $qty;
} else {
    $_SESSION['cart'][$id] = $qty;
}

$base = dirname(dirname($_SERVER['PHP_SELF']));
header("Location: $base/views/cart.php");
exit();
