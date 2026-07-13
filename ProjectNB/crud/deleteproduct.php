<?php
session_start();
if (!isset($_SESSION['admin'])) {
    $base = dirname(dirname($_SERVER['PHP_SELF']));
    header("Location: $base/auth/login.php");
    exit;
}
require_once __DIR__ . '/../core/connect_database.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM Product WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$base = dirname(dirname($_SERVER['PHP_SELF']));
header("Location: $base/views/showproduct.php");
exit;
