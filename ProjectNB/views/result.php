<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Confirmed — NB Store</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    background: #0a0a0a;
    color: #f5f2ee;
    font-family: 'DM Sans', sans-serif;
    font-weight: 300;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 40px;
    position: relative;
    overflow: hidden;
  }

  /* BIG BG TEXT */
  body::before {
    content: '✓';
    position: absolute;
    font-size: 600px;
    color: rgba(207,43,43,.04);
    line-height: 1;
    pointer-events: none;
    top: 50%;
    left: 50%;
    transform: translate(-50%,-50%);
    font-family: 'Bebas Neue', sans-serif;
  }

  .check-ring {
    width: 80px;
    height: 80px;
    border: 2px solid #cf2b2b;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    margin-bottom: 40px;
    animation: popIn .5s cubic-bezier(.34,1.56,.64,1) forwards;
    opacity: 0;
  }

  @keyframes popIn {
    to { opacity: 1; transform: scale(1); }
    from { opacity: 0; transform: scale(.5); }
  }

  .eyebrow {
    font-size: 10px;
    letter-spacing: 4px;
    text-transform: uppercase;
    color: #cf2b2b;
    margin-bottom: 16px;
    animation: fadeUp .5s .2s both;
  }

  h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(64px, 10vw, 120px);
    letter-spacing: 2px;
    line-height: .9;
    margin-bottom: 24px;
    animation: fadeUp .5s .3s both;
  }

  p {
    color: #666;
    font-size: 15px;
    line-height: 1.6;
    max-width: 400px;
    margin-bottom: 48px;
    animation: fadeUp .5s .4s both;
  }

  .cta {
    display: inline-block;
    padding: 16px 56px;
    background: #cf2b2b;
    color: #f5f2ee;
    text-decoration: none;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 22px;
    letter-spacing: 4px;
    animation: fadeUp .5s .5s both;
    transition: background .2s;
  }

  .cta:hover { background: #e83535; }

  @keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  .order-id {
    margin-top: 24px;
    font-size: 11px;
    letter-spacing: 2px;
    color: #333;
    animation: fadeUp .5s .6s both;
  }
</style>
</head>
<body>

<div class="check-ring">✓</div>
<p class="eyebrow">Order Complete</p>
<h1>Thank<br>You!</h1>
<p>Your sneakers are on their way. We'll send you a confirmation shortly. Keep moving.</p>
<a href="showproduct.php" class="cta">Back to Store</a>
<p class="order-id">New Balance Store — <?= date('Y') ?></p>

</body>
</html>
