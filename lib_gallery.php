<?php
/**
 * lib_gallery.php
 *
 * Shared renderer for the five wedding-photography album pages (a.php – e.php).
 * Each of those files only sets `$gallery` ('a'..'e') and requires this file.
 *
 * PHOTOS: drop image files into  photos/wedding/<letter>/   and they appear
 * automatically — no code edits. Supported: jpg, jpeg, png, webp, gif, avif.
 * (See the on-page instructions and AGENTS.md.)
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Shared "Call Now" + WhatsApp floating contact UI (identical to the home page).
// a.php - e.php render through this file, so all five album pages pick it up here.
require_once __DIR__ . '/lib_contact.php';

$gallery = preg_replace('/[^a-e]/', '', (string) ($gallery ?? 'a')) ?: 'a';

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

$dir = __DIR__ . '/photos/wedding/' . $gallery;

$extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];
$photos = [];
if (is_dir($dir)) {
    foreach (new DirectoryIterator($dir) as $file) {
        if ($file->isDot() || !$file->isFile()) {
            continue;
        }
        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        if (in_array($ext, $extensions, true)) {
            $photos[] = $file->getFilename();
        }
    }
}
sort($photos, SORT_STRING | SORT_FLAG_CASE);

$titles = [
    'a' => 'Album A',
    'b' => 'Album B',
    'c' => 'Album C',
    'd' => 'Album D',
    'e' => 'Album E',
];
$title = $titles[$gallery] ?? ('Album ' . strtoupper($gallery));
$letters = ['a', 'b', 'c', 'd', 'e'];
$total = count($photos) ?: 0;

header('Content-Type: text/html; charset=UTF-8');

function gal_photoUrl(string $gallery, string $file): string
{
    return 'photos/wedding/' . $gallery . '/' . rawurlencode($file);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Wedding Photography — <?= $esc($title) ?> | Deepak Studios</title>
<style>
  * { box-sizing: border-box; }
  html, body { margin: 0; padding: 0; background: #0d0d0d; color: #f5efe0;
               font-family: Georgia, "Times New Roman", serif; }
  a { color: #c9a86a; }
  header { padding: 2.2rem 1rem 1.3rem; text-align: center; background: #161616;
           border-bottom: 1px solid #2a2a2a; }
  header h1 { margin: 0; font-size: 2.2rem; letter-spacing: 2px; }
  header p { margin: .55rem 0 0; color: #c9a86a; }
  .crumbs { margin-top: .9rem; font-size: .88rem; color: #8a8a8a; }
  .crumbs a { text-decoration: none; }
  .crumbs a:hover { text-decoration: underline; }
  nav.ab { display: flex; justify-content: center; flex-wrap: wrap; gap: .8rem;
           padding: 1.3rem 1rem .9rem; }
  nav.ab a { color: #c9a86a; text-decoration: none; padding: .45rem .95rem;
             border: 1px solid #3a3a3a; border-radius: 5px; font-size: .92rem; }
  nav.ab a.here, nav.ab a:hover { border-color: #c9a86a; background: rgba(201,168,106,.08); }
  main { max-width: 1100px; margin: 0 auto; padding: .4rem 1.2rem 3rem; }
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
  footer { text-align: center; padding: 1.6rem; color: #666; font-size: .85rem; }
  .social { display: flex; gap: .7rem; justify-content: center; margin: 0 0 1rem; }
  .social a { width: 2.4rem; height: 2.4rem; border-radius: 999px; display: inline-flex;
    align-items: center; justify-content: center; border: 1px solid #2a2a2a; color: #8a8a8a;
    transition: all .3s; }
  .social a:hover { color: #0d0d0d; background: #c9a86a; border-color: #c9a86a; transform: translateY(-2px); }
  .social svg { width: 1.1rem; height: 1.1rem; fill: currentColor; }
  .lightbox { display: none; position: fixed; inset: 0; z-index: 50; background: rgba(0,0,0,.94);
              align-items: center; justify-content: center; padding: 2rem; cursor: zoom-out; }
  .lightbox img { max-width: 100%; max-height: 100%; border-radius: 6px; }
  .lb-close { position: absolute; top: .8rem; right: 1.3rem; font-size: 2.4rem; color: #fff;
              cursor: pointer; line-height: 1; }
</style>
<script>
function openLightbox(src) {
  var img = document.getElementById('lb-img');
  img.src = src;
  document.getElementById('lightbox').style.display = 'flex';
}
function closeLightbox() {
  document.getElementById('lightbox').style.display = 'none';
}
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') { closeLightbox(); }
});
</script>
<?php ds_contact_head(); ?>
</head>
<body>
<header>
  <h1>Wedding Photography</h1>
  <p><?= $esc($title) ?> &middot; <?= $total ?> photo<?= $total === 1 ? '' : 's' ?></p>
  <div class="crumbs"><a href="index.html">Home</a> &rsaquo; <a href="wedding.php">Wedding Photography</a> &rsaquo; <?= $esc($title) ?></div>
</header>

<nav class="ab">
  <?php foreach ($letters as $letter): ?>
    <a href="<?= $letter ?>.php"<?= $letter === $gallery ? ' class="here"' : '' ?>><?= strtoupper($letter) ?></a>
  <?php endforeach; ?>
</nav>

<main>
  <div class="intro">
    <b>Kaise photos add karein (site owner):</b> apne photos <code>photos/wedding/<?= $esc($gallery) ?>/</code>
    folder me upload karein (BaoTa File Manager ya FTP). Files turant yahan aa jaati hain —
    koi code change nahi, koi deploy nahi. Supported: <code>jpg, jpeg, png, webp, gif, avif</code>.
  </div>

  <?php if ($total === 0): ?>
    <div class="empty">
      No photos yet in Album <?= strtoupper($gallery) ?>.<br>
      Upload images into <b><?= $esc($dir) ?></b> and refresh this page.
    </div>
  <?php else: ?>
    <div class="gallery">
      <?php foreach ($photos as $file): ?>
        <a class="item" href="javascript:void(0)" onclick="openLightbox('<?= $esc(gal_photoUrl($gallery, $file)) ?>')">
          <img loading="lazy" src="<?= $esc(gal_photoUrl($gallery, $file)) ?>" alt="<?= $esc($title) ?> photo">
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<div class="lightbox" id="lightbox" onclick="closeLightbox()">
  <span class="lb-close" onclick="event.stopPropagation(); closeLightbox()">&times;</span>
  <img id="lb-img" src="" alt="">
</div>

<footer><div class="social">
<a href="https://www.instagram.com/deepakstudiosofficial_/" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
<a href="https://www.facebook.com/deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
<a href="https://www.youtube.com/@deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
</div>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

<?php ds_contact_fab(); ?>
</body>
</html>