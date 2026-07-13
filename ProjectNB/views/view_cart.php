<?php
session_start();
require_once __DIR__ . '/../core/connect_database.php';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
</head>
<body>

<h2>Your Selected Shoes</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Shoe Model</th>
    <th>Quantity</th>
    <th>Price</th>
    <th>Subtotal</th>
</tr>

<?php
$total = 0;

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $id => $qty){

        $sql = "SELECT * FROM Product WHERE id = $id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        $subtotal = $row['price'] * $qty;
        $total += $subtotal;
?>
<tr>
    <td><?= $row['productname']; ?></td>
    <td><?= $qty; ?></td>
    <td><?= number_format($row['price'],2); ?></td>
    <td><?= number_format($subtotal,2); ?></td>
</tr>
<?php
    }
}
?>

<tr>
    <td colspan="3"><strong>Total</strong></td>
    <td><strong><?= number_format($total,2); ?></strong></td>
</tr>
</table>

<br>
<a href="showproduct.php">Back to Shop</a>

</body>
</html>