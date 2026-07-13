<?php
session_start();
$base = dirname(dirname($_SERVER['PHP_SELF']));
if (!isset($_SESSION['admin'])) {
    header("Location: $base/auth/login.php");
    exit;
}
require_once __DIR__ . '/../core/connect_database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productname = $_POST['productname'];
    $colors = $_POST['colors'];
    $price = floatval($_POST['price']);
    $img = $_POST['img_url'];

    $stmt = $conn->prepare("INSERT INTO Product (productname, colors, price, img) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $productname, $colors, $price, $img);
    $stmt->execute();

    header("Location: $base/views/showproduct.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product — NB Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: #0a0a0a;
    color: #f5f2ee;
    font-family: 'DM Sans', sans-serif;
    font-weight: 300;
    min-height: 100vh;
    display: grid;
    grid-template-columns: 1fr 1fr;
  }

  /* FORM SIDE */
  .form-side {
    padding: 64px 72px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    border-right: 1px solid #1e1e1e;
  }

  .eyebrow {
    font-size: 10px;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: #cf2b2b;
    margin-bottom: 12px;
  }

  h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 56px;
    letter-spacing: 2px;
    line-height: .9;
    margin-bottom: 48px;
  }

  .field {
    margin-bottom: 24px;
  }

  .field label {
    display: block;
    font-size: 10px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #555;
    margin-bottom: 8px;
  }

  .field input {
    width: 100%;
    background: #111;
    border: 1px solid #2a2a2a;
    color: #f5f2ee;
    padding: 14px 16px;
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    font-weight: 300;
    outline: none;
    transition: border-color .2s;
  }

  .field input:focus { border-color: #cf2b2b; }
  .field input::placeholder { color: #333; }

  .actions {
    display: flex;
    gap: 12px;
    margin-top: 8px;
  }

  .save-btn {
    flex: 1;
    padding: 16px;
    background: #cf2b2b;
    color: #fff;
    border: none;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px;
    letter-spacing: 3px;
    cursor: pointer;
    transition: background .2s;
  }

  .save-btn:hover { background: #e83535; }

  .cancel-link {
    padding: 16px 24px;
    border: 1px solid #2a2a2a;
    color: #666;
    text-decoration: none;
    font-size: 11px;
    letter-spacing: 2px;
    text-transform: uppercase;
    display: flex;
    align-items: center;
    transition: all .2s;
  }

  .cancel-link:hover { border-color: #555; color: #f5f2ee; }

  /* PREVIEW SIDE */
  .preview-side {
    padding: 64px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #0d0d0d;
  }

  .preview-label {
    font-size: 10px;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: #333;
    margin-bottom: 24px;
  }

  .preview-card {
    width: 100%;
    max-width: 360px;
    background: #141414;
    border: 1px solid #1e1e1e;
  }

  .preview-img {
    width: 100%;
    aspect-ratio: 4/3;
    object-fit: cover;
    background: #1a1a1a;
    display: block;
  }

  .preview-body {
    padding: 24px;
  }

  .preview-color {
    font-size: 10px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: #555;
    margin-bottom: 6px;
  }

  .preview-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 30px;
    letter-spacing: 1px;
    margin-bottom: 8px;
    color: #f5f2ee;
    min-height: 36px;
  }

  .preview-price {
    font-size: 22px;
    font-weight: 500;
    color: #cf2b2b;
  }

  @media (max-width: 900px) {
    body { grid-template-columns: 1fr; }
    .preview-side { display: none; }
    .form-side { padding: 48px 32px; }
  }
</style>
</head>
<body>

<!-- FORM -->
<div class="form-side">
  <p class="eyebrow">Admin Panel</p>
  <h1>Add New<br>Sneaker</h1>

  <form method="post" id="productForm">
    <div class="field">
      <label>Model Name</label>
      <input name="productname" id="inp_name" placeholder="e.g. New Balance 530" required
             oninput="document.getElementById('prev_name').textContent=this.value||'Model Name'">
    </div>

    <div class="field">
      <label>Available Colors</label>
      <input name="colors" id="inp_colors" placeholder="White / Black / Grey"
             oninput="document.getElementById('prev_color').textContent=this.value||'Colors'">
    </div>

    <div class="field">
      <label>Price (฿)</label>
      <input type="number" step="0.01" name="price" id="inp_price" placeholder="0.00" required
             oninput="let v=parseFloat(this.value); document.getElementById('prev_price').textContent=isNaN(v)?'฿ —':'฿'+v.toLocaleString('th-TH',{minimumFractionDigits:2})">
    </div>

    <div class="field">
      <label>Image URL</label>
      <input name="img_url" id="inp_img" placeholder="Paste image URL here"
             oninput="let img=document.getElementById('prev_img'); img.src=this.value; img.onerror=function(){this.src='https://via.placeholder.com/360x270/1a1a1a/333?text=NB'}">
    </div>

    <div class="actions">
      <button type="submit" class="save-btn">Save Sneaker</button>
      <a href="<?= $base ?>/views/showproduct.php" class="cancel-link">Cancel</a>
    </div>
  </form>
</div>

<!-- PREVIEW -->
<div class="preview-side">
  <p class="preview-label">Live Preview</p>
  <div class="preview-card">
    <img class="preview-img"
         id="prev_img"
         src="https://via.placeholder.com/360x270/1a1a1a/333?text=NB"
         alt="Preview">
    <div class="preview-body">
      <p class="preview-color" id="prev_color">Colors</p>
      <p class="preview-name" id="prev_name">Model Name</p>
      <p class="preview-price" id="prev_price">฿ —</p>
    </div>
  </div>
</div>

</body>
</html>
