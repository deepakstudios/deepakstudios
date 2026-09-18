<?php
/**
 * prewedding_photos.php — Pre-Wedding PHOTOS gallery.
 *
 * Auto-scans  photos/prewedding/  so the site owner just uploads image files
 * (BaoTa File Manager / FTP) and they appear instantly — no code edits.
 * Supported: jpg, jpeg, png, webp, gif, avif.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require __DIR__ . '/lib_prewedding.php';
$gallery = 'photos';
$title   = 'Photos';

$dir = __DIR__ . '/photos/prewedding';
$photos = [];
if (is_dir($dir)) {
    foreach (new DirectoryIterator($dir) as $file) {
        if ($file->isDot() || !$file->isFile()) {
            continue;
        }
        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'], true)) {
            $photos[] = $file->getFilename();
        }
    }
}
sort($photos, SORT_STRING | SORT_FLAG_CASE);
$total = count($photoshörl);

header('Content-Type: text/html; charset=UTF-8');

function pw_photoUrl(string $file): string
{
    return 'photos/prewedding/' . rawurlencode($file);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pre-Wedding <?= $title ?> | Deepak Studios</title>
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0d0d0d; color: #f5efe0;
               font-family: Georgia, "Times New Roman", serif; }
  a { color: #c9a86a; }
  header { padding: 2.2rem 1rem 1.3rem; text-align: center; background: #161616;
           border-bottom: 1px solid #2a2a2a; }
  header h1 { margin: 0; font-size: 2.2rem; letter-spacing: 2px; }
  header p { margin: .6rem 0 0; color: #c9a86a; }
  .crumbs { margin-top: .9rem; font-size: .88rem; color: #8a8a8a; }
  .crumbs a { text-decoration: none; }
  .crumbs a:hover { text-decoration: underline; }
  main { max-width: 1100px; margin: 0 auto; padding: 2rem 1.2rem 3rem; }
  .intro { max-width: 760px; margin: 1.2rem auto 2rem; background: #161616;
           border: 1px dashed #3a3a3a; border-radius: 8px; padding: 1.2rem 1.4rem;
           color: #b5b5b5; font-size: .92rem; line-height: 1.65; }
  .intro b { color: #c9a86a; }
  .gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem; }
  .item { position: relative; display: block; cursor: pointer; border-radius: 8px; overflow: hidden;
          border: 1px solid #2a2a2a; background: #141414; aspect-ratio: 4 / 3; }
  .item img { display: block; width: 100%; height: 100%; object-fit: cover;
              transition: transform .4s ease; }
  .item:hover img { transform: scale(1.05); }
  .empty { text-align: center; padding: 4rem 1.5rem; color: #8a8a8a; border: 1px dashed #3a3a3a;
           border-radius: 8px; }
  .lightbox { display: none; position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,.94);
              align-items: center; justify-content: center; padding: 2rem; cursor: zoom-out; }
  .lightbox img { max-width: 100%; max-height: 100%; border-radius: 6px; }
  .lb-close { position: absolute; top: .8rem; right: 1.3rem; font-size: 2.4rem; color: #fff;
              cursor: pointer; line-height: 1; }
  footer { text-align: center; padding: 1.6rem; color: #666; font-size: .85rem; }
</style>
</head>
<body>
<header>
  <h1>Pre-Wedding <?= $esc($title) ?></h1>
  <p><?= $total ?> photo<?= $total === 1 ? '' : 's' ?></p>
  <div class="crumbs"><a href="index.html">Home</a> &rsaquo;
    <a href="prewedding.php">Pre-Wedding Photography</a> &rsaquo;
    <?= $esc($title) ?></div>
</header>

<main>
  <div class="intro">
    <b>Kaise photos add karein (site owner):</b> apne images
    <code>photos/prewedding/</code> folder me upload karein (BaoTa File Manager ya FTP).
    Files turant yahan aa jaati hain — koi code change nahi, koi deploy nahi.
    Supported: <code>jpg, jpeg, png, webp, gif, avif</code>.
  </div>

  <?php if ($total === 0): ?>
    <div class="empty">
      No photos yet in the Pre-Wedding album.<br>
      Upload images into <b>photos/prewedding/</b> and refresh this page.
    </div>
  <?php else: ?>
    <div class="gallery">
      <?php foreach ($photos as $file): ?>
        <a class="item" href="javascript:void(0)" onclick="openLightbox('<?= $esc(pw_photoUrl($file)) ?>')">
          <img loading="lazy" src="<?= $esc(pw_photoUrl($file)) ?>" alt="Pre-Wedding photo">
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<div class="lightbox" id="lightbox" onclick="closeLightbox()">
  <span class="lb-close" onclick="closeLightbox()">&times;</span>
  <img id="lb-img" src="" alt="">
</div>

<footer>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

<script>
function openLightbox(src) {
  document.getElementById('lb-img').src = src;
  document.getElementById('lightbox').style.display = 'flex';
}
function closeLightbox() {
  document.getElementById('lightbox').style.display = 'none';
}
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') { closeLightbox(); }
});
</script>
</body>
</html>
