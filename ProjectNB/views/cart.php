<?php
ob_start();
session_start();
require_once __DIR__ . '/../core/connect_database.php';

/* 1️⃣ ลบสินค้า */
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_to_delete = $_GET['id'];
    if (isset($_SESSION['cart'][$id_to_delete])) {
        unset($_SESSION['cart'][$id_to_delete]);
    }
    header("Location: cart.php");
    exit();
}

/* 2️⃣ อัปเดตจำนวน */
if (isset($_POST['update_cart'])) {
    if (isset($_POST['amounts'])) {
        foreach ($_POST['amounts'] as $id => $qty) {
            if ($qty <= 0) {
                unset($_SESSION['cart'][$id]);
            } else {
                $_SESSION['cart'][$id] = (int)$qty;
            }
        }
    }
    header("Location: cart.php");
    exit();
}

/* 3️⃣ เพิ่มสินค้า */
if (isset($_POST['add_to_cart'])) {
    $p_id = $_POST['id'];
    $qty = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$p_id])) {
        $_SESSION['cart'][$p_id] += $qty;
    } else {
        $_SESSION['cart'][$p_id] = $qty;
    }
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cart — NB Store</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --black: #0a0a0a;
    --white: #f5f2ee;
    --red: #cf2b2b;
    --gray: #8a8a8a;
    --border: #222;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: var(--black);
    color: var(--white);
    font-family: 'DM Sans', sans-serif;
    font-weight: 300;
    min-height: 100vh;
  }

  /* HEADER */
  header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 48px;
    height: 64px;
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    background: var(--black);
    z-index: 10;
  }

  .logo {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 24px;
    letter-spacing: 4px;
    color: var(--white);
    text-decoration: none;
  }

  .logo span { color: var(--red); }

  .back-link {
    color: #666;
    text-decoration: none;
    font-size: 12px;
    letter-spacing: 2px;
    text-transform: uppercase;
    transition: color .2s;
  }

  .back-link:hover { color: var(--white); }

  /* PAGE TITLE */
  .page-header {
    padding: 56px 48px 40px;
    border-bottom: 1px solid var(--border);
  }

  .page-eyebrow {
    font-size: 10px;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: var(--red);
    margin-bottom: 10px;
  }

  .page-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 72px;
    letter-spacing: 2px;
    line-height: .9;
  }

  /* LAYOUT */
  .content {
    display: grid;
    grid-template-columns: 1fr 360px;
    min-height: calc(100vh - 200px);
  }

  /* LEFT: CART ITEMS */
  .cart-items {
    border-right: 1px solid var(--border);
  }

  .cart-row {
    display: grid;
    grid-template-columns: 100px 1fr auto auto auto;
    gap: 24px;
    align-items: center;
    padding: 24px 48px;
    border-bottom: 1px solid var(--border);
    transition: background .2s;
  }

  .cart-row:hover { background: #111; }

  .cart-row img {
    width: 100px;
    height: 80px;
    object-fit: cover;
    background: #1a1a1a;
  }

  .item-info {}

  .item-color {
    font-size: 10px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--gray);
    margin-bottom: 4px;
  }

  .item-name {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 24px;
    letter-spacing: 1px;
    line-height: 1;
  }

  .item-price {
    color: var(--gray);
    font-size: 13px;
    margin-top: 4px;
  }

  .qty-wrap {
    display: flex;
    align-items: center;
    gap: 0;
    border: 1px solid var(--border);
  }

  .qty-input {
    width: 52px;
    padding: 10px 8px;
    background: transparent;
    border: none;
    color: var(--white);
    text-align: center;
    font-family: 'DM Sans', sans-serif;
    font-size: 15px;
    outline: none;
  }

  .item-subtotal {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 22px;
    letter-spacing: 1px;
    color: var(--red);
    min-width: 120px;
    text-align: right;
  }

  .delete-btn {
    color: #444;
    text-decoration: none;
    font-size: 18px;
    transition: color .2s;
    padding: 4px 8px;
  }

  .delete-btn:hover { color: var(--red); }

  .update-row {
    padding: 20px 48px;
    border-bottom: 1px solid var(--border);
  }

  .update-btn {
    background: transparent;
    border: 1px solid #444;
    color: #888;
    padding: 10px 24px;
    font-family: 'DM Sans', sans-serif;
    font-size: 11px;
    letter-spacing: 2px;
    text-transform: uppercase;
    cursor: pointer;
    transition: all .2s;
  }

  .update-btn:hover { border-color: var(--white); color: var(--white); }

  /* RIGHT: SUMMARY */
  .summary {
    padding: 40px 40px;
    display: flex;
    flex-direction: column;
    gap: 0;
  }

  .summary-title {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 32px;
    letter-spacing: 2px;
    margin-bottom: 32px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border);
  }

  .summary-line {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 0;
    border-bottom: 1px solid #1a1a1a;
    font-size: 13px;
    color: var(--gray);
  }

  .summary-total {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 24px 0;
    margin-top: 8px;
  }

  .summary-total .label {
    font-size: 11px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--gray);
  }

  .summary-total .amount {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 40px;
    color: var(--red);
    letter-spacing: 1px;
  }

  .checkout-btn {
    display: block;
    text-align: center;
    padding: 18px;
    background: var(--red);
    color: var(--white);
    text-decoration: none;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 22px;
    letter-spacing: 4px;
    margin-top: 24px;
    transition: background .2s, transform .1s;
  }

  .checkout-btn:hover { background: #e83535; }
  .checkout-btn:active { transform: scale(.99); }

  .secure-note {
    text-align: center;
    font-size: 11px;
    letter-spacing: 1px;
    color: #444;
    margin-top: 16px;
  }

  /* EMPTY STATE */
  .empty-state {
    grid-column: 1/-1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    gap: 24px;
  }

  .empty-state h2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 64px;
    letter-spacing: 2px;
    color: #222;
  }

  .empty-state a {
    padding: 16px 48px;
    background: var(--red);
    color: var(--white);
    text-decoration: none;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px;
    letter-spacing: 3px;
    transition: background .2s;
  }

  .empty-state a:hover { background: #e83535; }

  @media (max-width: 900px) {
    .content { grid-template-columns: 1fr; }
    .summary { border-top: 1px solid var(--border); }
    header, .page-header, .cart-row, .update-row { padding-left: 24px; padding-right: 24px; }
    .cart-row { grid-template-columns: 80px 1fr; gap: 16px; }
    .item-subtotal, .delete-btn { display: none; }
  }
</style>
</head>
<body>

<!-- HEADER -->
<header>
  <a href="showproduct.php" class="logo">NB <span>STORE</span></a>
  <a href="showproduct.php" class="back-link">← Continue Shopping</a>
</header>

<!-- PAGE TITLE -->
<div class="page-header">
  <p class="page-eyebrow">Review your selection</p>
  <h1 class="page-title">Your Cart</h1>
</div>

<?php if (!empty($_SESSION['cart'])): ?>
<form method="post">
<div class="content">

  <!-- ITEMS -->
  <div class="cart-items">
  <?php
    $total_price = 0;
    foreach ($_SESSION['cart'] as $id => $qty):
      $stmt = $conn->prepare("SELECT * FROM Product WHERE id = ?");
      $stmt->bind_param("i", $id);
      $stmt->execute();
      $product = $stmt->get_result()->fetch_assoc();
      if ($product):
        $subtotal = $product['price'] * $qty;
        $total_price += $subtotal;
  ?>
  <div class="cart-row">
    <img src="<?= htmlspecialchars($product['img']) ?>"
         alt="<?= htmlspecialchars($product['productname']) ?>"
         onerror="this.src='https://via.placeholder.com/100x80/1a1a1a/444?text=NB'">

    <div class="item-info">
      <p class="item-color"><?= htmlspecialchars($product['colors']) ?></p>
      <p class="item-name"><?= htmlspecialchars($product['productname']) ?></p>
      <p class="item-price">฿<?= number_format($product['price'], 2) ?> each</p>
    </div>

    <div class="qty-wrap">
      <input class="qty-input" type="number"
             name="amounts[<?= $id ?>]"
             value="<?= $qty ?>" min="1">
    </div>

    <p class="item-subtotal">฿<?= number_format($subtotal, 2) ?></p>

    <a class="delete-btn"
       href="cart.php?action=delete&id=<?= $id ?>"
       onclick="return confirm('Remove this sneaker?')">×</a>
  </div>
  <?php endif; endforeach; ?>

  <div class="update-row">
    <button type="submit" name="update_cart" class="update-btn">Update Quantities</button>
  </div>
  </div>

  <!-- SUMMARY -->
  <div class="summary">
    <h2 class="summary-title">Order Summary</h2>

    <div class="summary-line">
      <span>Subtotal</span>
      <span>฿<?= number_format($total_price, 2) ?></span>
    </div>
    <div class="summary-line">
      <span>Shipping</span>
      <span>Free</span>
    </div>
    <div class="summary-line">
      <span>Tax</span>
      <span>Included</span>
    </div>

    <div class="summary-total">
      <span class="label">Total</span>
      <span class="amount">฿<?= number_format($total_price, 2) ?></span>
    </div>

    <button type="button" class="checkout-btn" onclick="openModal()">Checkout →</button>
    <p class="secure-note">🔒 Secure Checkout</p>
  </div>

</div>
</form>

<?php else: ?>
<div class="content">
  <div class="empty-state">
    <h2>Cart is Empty</h2>
    <a href="showproduct.php">Shop Now</a>
  </div>
</div>
<?php endif; ?>

<!-- ══════════════════════════════════════
     CHECKOUT MODAL
══════════════════════════════════════ -->
<div id="checkoutModal" style="display:none; position:fixed; inset:0; z-index:999; align-items:center; justify-content:center;">
  <!-- Backdrop -->
  <div onclick="closeModal()" style="position:absolute; inset:0; background:rgba(0,0,0,.85); backdrop-filter:blur(4px);"></div>

  <!-- Modal Box -->
  <div id="modalBox" style="
    position:relative; z-index:1;
    background:#141414; border:1px solid #2a2a2a;
    width:520px; max-width:95vw;
    font-family:'DM Sans',sans-serif;
    animation: modalIn .3s cubic-bezier(.34,1.56,.64,1);
  ">

    <!-- STEP 1: Choose Payment -->
    <div id="step1">
      <div style="background:#cf2b2b; padding:28px 36px;">
        <p style="font-size:10px; letter-spacing:4px; text-transform:uppercase; color:rgba(255,255,255,.6); margin-bottom:6px;">Order Summary</p>
        <div style="font-family:'Bebas Neue',sans-serif; font-size:48px; color:#fff; letter-spacing:2px; line-height:1;">
          ฿<?= number_format($total_price ?? 0, 2) ?>
        </div>
        <p style="color:rgba(255,255,255,.7); font-size:13px; margin-top:6px;">Total amount to pay</p>
      </div>

      <div style="padding:32px 36px;">
        <p style="font-size:10px; letter-spacing:4px; text-transform:uppercase; color:#666; margin-bottom:20px;">Select Payment Method</p>

        <div style="display:flex; flex-direction:column; gap:12px;">

          <!-- PromptPay -->
          <label class="pay-option">
            <input type="radio" name="payment" value="promptpay" style="display:none;">
            <div class="pay-card" onclick="selectPay(this,'promptpay')">
              <div style="font-size:28px;">📱</div>
              <div>
                <div style="font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:1px;">PromptPay</div>
                <div style="font-size:12px; color:#888; margin-top:2px;">Scan QR Code to pay instantly</div>
              </div>
              <div class="check-dot">✓</div>
            </div>
          </label>

          <!-- Credit Card -->
          <label class="pay-option">
            <input type="radio" name="payment" value="credit" style="display:none;">
            <div class="pay-card" onclick="selectPay(this,'credit')">
              <div style="font-size:28px;">💳</div>
              <div>
                <div style="font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:1px;">Credit / Debit Card</div>
                <div style="font-size:12px; color:#888; margin-top:2px;">Visa, Mastercard, JCB</div>
              </div>
              <div class="check-dot">✓</div>
            </div>
          </label>

          <!-- Cash on Delivery -->
          <label class="pay-option">
            <input type="radio" name="payment" value="cod" style="display:none;">
            <div class="pay-card" onclick="selectPay(this,'cod')">
              <div style="font-size:28px;">💵</div>
              <div>
                <div style="font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:1px;">Cash on Delivery</div>
                <div style="font-size:12px; color:#888; margin-top:2px;">Pay when your order arrives</div>
              </div>
              <div class="check-dot">✓</div>
            </div>
          </label>

        </div>

        <button id="confirmPayBtn" onclick="confirmPayment()" style="
          width:100%; margin-top:28px; padding:16px;
          background:#333; color:#666;
          border:none; font-family:'Bebas Neue',sans-serif;
          font-size:22px; letter-spacing:3px;
          cursor:not-allowed; transition:.3s;
        " disabled>Confirm Payment</button>

        <button onclick="closeModal()" style="
          width:100%; margin-top:10px; padding:12px;
          background:transparent; color:#555;
          border:1px solid #2a2a2a; font-family:'DM Sans',sans-serif;
          font-size:12px; letter-spacing:2px; text-transform:uppercase;
          cursor:pointer; transition:.2s;
        " onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#555'">Cancel</button>
      </div>
    </div>

    <!-- STEP 2: Thank You -->
    <div id="step2" style="display:none; padding:60px 36px; text-align:center;">
      <div style="
        width:72px; height:72px; border:2px solid #cf2b2b;
        border-radius:50%; display:flex; align-items:center;
        justify-content:center; font-size:32px; margin:0 auto 28px;
        animation: popIn .4s cubic-bezier(.34,1.56,.64,1);
      ">✓</div>
      <p style="font-size:10px; letter-spacing:4px; text-transform:uppercase; color:#cf2b2b; margin-bottom:10px;">Payment Received</p>
      <h2 style="font-family:'Bebas Neue',sans-serif; font-size:56px; letter-spacing:2px; color:#f5f2ee; line-height:.9; margin-bottom:16px;">Thank You!</h2>
      <p id="payMethodText" style="color:#666; font-size:14px; margin-bottom:8px;"></p>
      <p style="color:#444; font-size:13px; margin-bottom:36px;">Your sneakers are on their way 👟</p>
      <a id="backShopBtn" href="#" style="
        display:inline-block; padding:14px 48px;
        background:#cf2b2b; color:#fff; text-decoration:none;
        font-family:'Bebas Neue',sans-serif; font-size:20px; letter-spacing:3px;
        transition:.2s;
      " onmouseover="this.style.background='#e83535'" onmouseout="this.style.background='#cf2b2b'">
        Back to Store
      </a>
    </div>

  </div>
</div>

<style>
@keyframes modalIn {
  from { opacity:0; transform:scale(.92) translateY(20px); }
  to   { opacity:1; transform:scale(1) translateY(0); }
}
@keyframes popIn {
  from { transform:scale(.4); opacity:0; }
  to   { transform:scale(1); opacity:1; }
}
.pay-card {
  display:flex; align-items:center; gap:16px;
  padding:16px 20px; border:1px solid #2a2a2a;
  cursor:pointer; transition:.2s; color:#f5f2ee;
  background:#1a1a1a;
}
.pay-card:hover { border-color:#555; background:#222; }
.pay-card.selected { border-color:#cf2b2b; background:#1e0f0f; }
.check-dot {
  margin-left:auto; width:24px; height:24px;
  border-radius:50%; border:1px solid #333;
  display:flex; align-items:center; justify-content:center;
  font-size:12px; color:transparent; background:transparent; transition:.2s;
}
.pay-card.selected .check-dot {
  background:#cf2b2b; border-color:#cf2b2b; color:#fff;
}
</style>

<script>
var selectedMethod = '';
var totalPrice = <?= $total_price ?? 0 ?>;
var confirmUrl = '<?= dirname(dirname($_SERVER['PHP_SELF'])) ?>/crud/confirm_order.php';

function openModal() {
  var m = document.getElementById('checkoutModal');
  m.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  var m = document.getElementById('checkoutModal');
  m.style.display = 'none';
  document.body.style.overflow = '';
}

function selectPay(el, method) {
  // Clear all
  document.querySelectorAll('.pay-card').forEach(function(c){ c.classList.remove('selected'); });
  el.classList.add('selected');
  selectedMethod = method;

  // Enable confirm button
  var btn = document.getElementById('confirmPayBtn');
  btn.disabled = false;
  btn.style.background = '#cf2b2b';
  btn.style.color = '#fff';
  btn.style.cursor = 'pointer';
}

function confirmPayment() {
  if (!selectedMethod) return;

  var labels = {
    'promptpay': 'Paid via PromptPay',
    'credit':    'Paid via Credit / Debit Card',
    'cod':       'Payment on Delivery'
  };

  // Show step 2
  document.getElementById('step1').style.display = 'none';
  document.getElementById('step2').style.display = 'block';
  document.getElementById('payMethodText').textContent = labels[selectedMethod];

  // Set back button to confirm order (clears cart)
  document.getElementById('backShopBtn').href = confirmUrl;
}

// Close on ESC
document.addEventListener('keydown', function(e){
  if (e.key === 'Escape') closeModal();
});
</script>

</body>
</html>