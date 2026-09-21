<?php
/**
 * cinematography.php — Cinematography | Deepak Studios
 *
 * Main content intentionally EMPTY (blank hub). No category cards, no
 * galleries, no videos, no client data, no pre-wedding showcase here.
 * The pre-wedding content remains fully on prewedding.php (untouched);
 * reels stay on prewedding_videos.php (untouched). This page only keeps
 * the site's dark+gold theme + navbar + footer so the Cinematography
 * navbar entry resolves cleanly.
 */
declare(strict_types=1,1);

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cinematography | Deepak Studios</title>
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0d0d0d; color: #f5efe0;
               font-family: Georgia, "Times New Roman", serif; }
  a { color: #c9a86a; }
  header { padding: 2.2rem 1.2rem 1.3rem; text-align: center; background: #161616;
           border-bottom: 1px solid #2a2a2a; }
  header h1 { margin: 0; font-size: 2.2rem; letter-spacing: 2px; }
  header p { margin: .6rem 0 0; }
  header .sub { color: #c9a86a; }
  .crumbs { margin-top: .9rem; font-size: .88rem; color: #8a8a8a; }
  .crumbs a { text-decoration: none; }
  .crumbs a:hover { text-decoration: underline; }
  nav { margin-top: 1.3rem; display: flex; flex-wrap: wrap; gap: .4rem 1.1rem;
        align-items: center; justify-content: center; }
  nav a { text-decoration: none; font-size: .9rem; letter-spacing: 1px;
          color: #e8e2d4; padding: .3rem .15rem; border-bottom: 1px solid transparent; }
  nav a:hover { color: #c9a86a; }
  nav a.on { color: #c9a86a; border-color: #c9a86a; }
  main { max-width: 1080px; margin: 0 auto; padding: 2rem 1.2rem 3rem; }
</style>
</head>
<body>

<header>
  <h1>Cinematography</h1>
  <p class="sub">Cinematic films &amp; short films</p>
  <div class="crumbs"><a href="index.html">Home</a> &rsaquo; Cinematography</div>
  <nav>
    <a href="index.html">Home</a>
    <a href="index.html#portfolio">Photography</a>
    <a href="cinematography.php" class="on">Cinematography</a>
    <a href="prewedding_videos.php">Reels</a>
    <a href="index.html#contact">Contact Us</a>
  </nav>
</header>

<main>
</main>

<footer>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

</body>
</html>
