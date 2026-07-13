<?php
session_start();

$conn = new mysqli("localhost", "std6730202084", "D1w!uYqF", "it_std6730202084");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$sql = "SELECT * FROM Product";
$result = $conn->query($sql);

// Dynamic base path - works regardless of folder depth
$scriptDir = dirname($_SERVER['PHP_SELF']); // e.g. /std.../Project/ProjectNB/views
$base = dirname($scriptDir);               // e.g. /std.../Project/ProjectNB
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>NB STORE — New Balance</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
  :root {
    --black: #0a0a0a;
    --white: #f5f2ee;
    --red: #cf2b2b;
    --gray: #8a8a8a;
    --border: #d8d3cc;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: var(--white);
    color: var(--black);
    font-family: 'DM Sans', sans-serif;
    font-weight: 300;
    min-height: 100vh;
    overflow-x: hidden;
  }

  header {
    position: sticky; top: 0; z-index: 100;
    background: var(--black);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 48px; height: 64px;
    border-bottom: 1px solid #222;
  }

  .logo {
    font-family: 'Bebas Neue', sans-serif; font-size: 26px;
    letter-spacing: 4px; color: var(--white); text-decoration: none;
  }
  .logo span { color: var(--red); }

  nav { display: flex; align-items: center; gap: 32px; }
  nav a { color: #aaa; text-decoration: none; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; transition: color .2s; }
  nav a:hover { color: var(--white); }
  .cart-btn { background: var(--red); color: var(--white) !important; padding: 8px 20px; border-radius: 2px; font-weight: 500 !important; }

  .hero-band { background: var(--black); color: var(--white); padding: 80px 48px 60px; position: relative; overflow: hidden; }
  .hero-band::before { content: 'NB'; position: absolute; right: -20px; top: -40px; font-family: 'Bebas Neue', sans-serif; font-size: 280px; color: rgba(255,255,255,.04); line-height: 1; pointer-events: none; }
  .hero-label { font-size: 11px; letter-spacing: 4px; text-transform: uppercase; color: var(--red); margin-bottom: 16px; }
  .hero-title { font-family: 'Bebas Neue', sans-serif; font-size: clamp(56px, 8vw, 110px); line-height: .9; letter-spacing: 2px; }
  .hero-title em { font-style: italic; color: var(--red); }

  .ticker-wrap { background: var(--red); overflow: hidden; white-space: nowrap; padding: 10px 0; }
  .ticker { display: inline-block; animation: ticker 18s linear infinite; font-family: 'Bebas Neue', sans-serif; font-size: 15px; letter-spacing: 4px; color: var(--white); }
  @keyframes ticker { from { transform: translateX(0); } to { transform: translateX(-50%); } }

  .section-bar { display: flex; align-items: center; justify-content: space-between; padding: 40px 48px 24px; border-bottom: 1px solid var(--border); }
  .section-bar h2 { font-family: 'Bebas Neue', sans-serif; font-size: 36px; letter-spacing: 2px; }
  .section-bar span { font-size: 12px; letter-spacing: 2px; text-transform: uppercase; color: var(--gray); }

  .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1px; background: var(--border); border-top: 1px solid var(--border); }

  .card { background: var(--white); display: flex; flex-direction: column; position: relative; overflow: hidden; transition: background .2s; opacity: 0; transform: translateY(20px); animation: fadeUp .5s forwards; }
  .card:hover { background: #eeece8; }

  @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
  <?php for($i=0;$i<20;$i++): ?>.card:nth-child(<?= $i+1 ?>) { animation-delay: <?= $i*.06 ?>s; }<?php endfor; ?>

  .card-img-wrap { position: relative; overflow: hidden; background: #e8e4de; aspect-ratio: 4/3; }
  .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .5s cubic-bezier(.25,.46,.45,.94); }
  .card:hover .card-img-wrap img { transform: scale(1.06); }
  .card-num { position: absolute; top: 14px; left: 14px; font-family: 'Bebas Neue', sans-serif; font-size: 13px; letter-spacing: 2px; color: var(--white); background: var(--black); padding: 2px 8px; }

  .card-body { padding: 24px; flex: 1; display: flex; flex-direction: column; }
  .card-color { font-size: 11px; letter-spacing: 3px; text-transform: uppercase; color: var(--gray); margin-bottom: 6px; }
  .card-name { font-family: 'Bebas Neue', sans-serif; font-size: 26px; letter-spacing: 1px; line-height: 1; margin-bottom: 16px; }
  .card-price { font-size: 22px; font-weight: 500; color: var(--red); margin-bottom: 20px; }

  .card-form { margin-top: auto; display: flex; gap: 10px; align-items: center; }
  .qty-input { width: 56px; padding: 10px 8px; border: 1px solid var(--border); background: transparent; text-align: center; font-family: 'DM Sans', sans-serif; font-size: 14px; outline: none; }
  .qty-input:focus { border-color: var(--black); }
  .add-btn { flex: 1; padding: 11px; background: var(--black); color: var(--white); border: none; font-family: 'DM Sans', sans-serif; font-size: 12px; letter-spacing: 2px; text-transform: uppercase; cursor: pointer; transition: background .2s; }
  .add-btn:hover { background: var(--red); }

  .admin-actions { margin-top: 10px; display: flex; gap: 8px; }
  .btn-edit { flex: 1; padding: 8px; border: 1px solid #ccc; text-align: center; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; text-decoration: none; color: #555; transition: .2s; }
  .btn-edit:hover { background: #0a0a0a; color: #fff; border-color: #0a0a0a; }
  .btn-delete { flex: 1; padding: 8px; border: 1px solid #ffcccc; text-align: center; font-size: 11px; letter-spacing: 1px; text-transform: uppercase; text-decoration: none; color: #c00; transition: .2s; }
  .btn-delete:hover { background: var(--red); color: #fff; border-color: var(--red); }

  .empty { grid-column: 1/-1; padding: 100px; text-align: center; background: var(--white); }
  .empty h3 { font-family: 'Bebas Neue', sans-serif; font-size: 48px; letter-spacing: 2px; color: #ccc; }

  footer { background: var(--black); color: #444; text-align: center; padding: 28px; font-size: 11px; letter-spacing: 2px; text-transform: uppercase; margin-top: 1px; }

  /* SEARCH */
  .search-wrap { background: var(--black); padding: 0 48px; display: flex; align-items: center; gap: 0; border-bottom: 1px solid #222; }
  .search-icon { color: #555; font-size: 18px; padding: 0 16px 0 0; flex-shrink: 0; }
  .search-input { flex: 1; background: transparent; border: none; outline: none; color: var(--white); font-family: 'DM Sans', sans-serif; font-size: 15px; font-weight: 300; padding: 18px 0; letter-spacing: .5px; }
  .search-input::placeholder { color: #444; }
  .search-divider { width: 1px; height: 20px; background: #333; margin: 0 20px; flex-shrink: 0; }
  .price-inputs { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
  .price-field { background: transparent; border: none; border-bottom: 1px solid #333; outline: none; color: #aaa; font-family: 'DM Sans', sans-serif; font-size: 13px; width: 80px; padding: 4px 0; text-align: center; transition: border-color .2s; }
  .price-field:focus { border-color: var(--red); color: var(--white); }
  .price-field::placeholder { color: #444; }
  .price-sep { color: #444; font-size: 12px; }
  .clear-btn { background: transparent; border: none; color: #444; font-size: 20px; cursor: pointer; padding: 0 0 0 16px; transition: color .2s; flex-shrink: 0; }
  .clear-btn:hover { color: var(--red); }
  .search-count { font-size: 11px; letter-spacing: 2px; text-transform: uppercase; color: var(--gray); }
  .no-result { display: none; grid-column: 1/-1; padding: 80px; text-align: center; background: var(--white); }
  .no-result h3 { font-family: 'Bebas Neue', sans-serif; font-size: 40px; letter-spacing: 2px; color: #ccc; }
  .no-result p { color: #aaa; margin-top: 8px; font-size: 14px; }
</style>
</head>
<body>

<header>
  <a href="<?= $scriptDir ?>/showproduct.php" class="logo">NB <span>STORE</span></a>
  <nav>
    <?php if (isset($_SESSION['admin'])): ?>
      <a href="<?= $base ?>/crud/addproduct.php">+ Add</a>
      <a href="<?= $base ?>/auth/logout.php">Logout</a>
    <?php else: ?>
      <a href="<?= $base ?>/auth/login.php">Admin</a>
    <?php endif; ?>
    <a href="<?= $scriptDir ?>/cart.php" class="cart-btn">Cart 🛒</a>
  </nav>
</header>

<div class="hero-band">
  <p class="hero-label">Official Store — Season 2025</p>
  <h1 class="hero-title">NEW<br><em>BALANCE</em></h1>
</div>

<div class="ticker-wrap">
  <span class="ticker">
    &nbsp;&nbsp;&nbsp;NEW BALANCE STORE &nbsp;—&nbsp; PREMIUM SNEAKERS &nbsp;—&nbsp; FREE SHIPPING &nbsp;—&nbsp; AUTHENTIC ONLY &nbsp;—&nbsp; NEW BALANCE STORE &nbsp;—&nbsp; PREMIUM SNEAKERS &nbsp;—&nbsp; FREE SHIPPING &nbsp;—&nbsp; AUTHENTIC ONLY &nbsp;—&nbsp;
    &nbsp;&nbsp;&nbsp;NEW BALANCE STORE &nbsp;—&nbsp; PREMIUM SNEAKERS &nbsp;—&nbsp; FREE SHIPPING &nbsp;—&nbsp; AUTHENTIC ONLY &nbsp;—&nbsp; NEW BALANCE STORE &nbsp;—&nbsp; PREMIUM SNEAKERS &nbsp;—&nbsp; FREE SHIPPING &nbsp;—&nbsp; AUTHENTIC ONLY &nbsp;—&nbsp;
  </span>
</div>

<!-- SEARCH BAR -->
<div class="search-wrap">
  <span class="search-icon">🔍</span>
  <input class="search-input" id="searchInput" type="text"
         placeholder="Search by name or color... e.g. &quot;574&quot; or &quot;black&quot;"
         oninput="filterProducts()">
  <div class="search-divider"></div>
  <div class="price-inputs">
    <input class="price-field" id="priceMin" type="number" placeholder="Min ฿" oninput="filterProducts()">
    <span class="price-sep">—</span>
    <input class="price-field" id="priceMax" type="number" placeholder="Max ฿" oninput="filterProducts()">
  </div>
  <button class="clear-btn" onclick="clearSearch()" title="Clear search">×</button>
</div>

<div class="section-bar">
  <h2>All Sneakers</h2>
  <span id="resultCount"><?= $result ? $result->num_rows : 0 ?> Models</span>
</div>

<div class="grid" id="productGrid">
<?php if ($result && $result->num_rows > 0):
  $n = 0;
  while ($row = $result->fetch_assoc()):
    $n++;
?>
  <div class="card"
       data-name="<?= strtolower(htmlspecialchars($row['productname'])) ?>"
       data-color="<?= strtolower(htmlspecialchars($row['colors'])) ?>"
       data-price="<?= $row['price'] ?>">
    <div class="card-img-wrap">
      <img src="<?= htmlspecialchars($row['img']) ?>" alt="<?= htmlspecialchars($row['productname']) ?>"
           onerror="this.src='https://via.placeholder.com/400x300/e8e4de/8a8a8a?text=NB'">
      <span class="card-num"><?= str_pad($n, 2, '0', STR_PAD_LEFT) ?></span>
    </div>
    <div class="card-body">
      <p class="card-color"><?= htmlspecialchars($row['colors']) ?></p>
      <h3 class="card-name"><?= htmlspecialchars($row['productname']) ?></h3>
      <p class="card-price">฿<?= number_format($row['price'], 2) ?></p>

      <form class="card-form" method="POST" action="<?= $base ?>/crud/cart_action.php">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <input class="qty-input" type="number" name="qty" value="1" min="1">
        <button class="add-btn" type="submit">Add to Cart</button>
      </form>

      <?php if (isset($_SESSION['admin'])): ?>
      <div class="admin-actions">
        <a class="btn-edit" href="<?= $base ?>/crud/editproduct.php?id=<?= $row['id'] ?>">Edit</a>
        <a class="btn-delete" href="<?= $base ?>/crud/deleteproduct.php?id=<?= $row['id'] ?>"
           onclick="return confirm('Delete this sneaker?')">Delete</a>
      </div>
      <?php endif; ?>
    </div>
  </div>
<?php endwhile; else: ?>
  <div class="empty"><h3>No Sneakers Found</h3></div>
<?php endif; ?>

  <!-- No result from search -->
  <div class="no-result" id="noResult">
    <h3>No Results</h3>
    <p>Try searching with a different keyword or price range</p>
  </div>
</div>

<footer>© 2025 New Balance Store &nbsp;—&nbsp; All Rights Reserved</footer>

<script>
function filterProducts() {
  var keyword = document.getElementById('searchInput').value.toLowerCase().trim();
  var minPrice = parseFloat(document.getElementById('priceMin').value) || 0;
  var maxPrice = parseFloat(document.getElementById('priceMax').value) || Infinity;

  var cards = document.querySelectorAll('#productGrid .card');
  var visible = 0;

  cards.forEach(function(card) {
    var name  = card.dataset.name  || '';
    var color = card.dataset.color || '';
    var price = parseFloat(card.dataset.price) || 0;

    var matchText  = !keyword || name.includes(keyword) || color.includes(keyword);
    var matchPrice = price >= minPrice && price <= maxPrice;

    if (matchText && matchPrice) {
      card.style.display = '';
      visible++;
    } else {
      card.style.display = 'none';
    }
  });

  // Update count
  document.getElementById('resultCount').textContent = visible + (visible === 1 ? ' Model' : ' Models');

  // Show/hide no-result
  document.getElementById('noResult').style.display = visible === 0 ? 'block' : 'none';
}

function clearSearch() {
  document.getElementById('searchInput').value = '';
  document.getElementById('priceMin').value = '';
  document.getElementById('priceMax').value = '';
  filterProducts();
}
</script>

</body>
</html>