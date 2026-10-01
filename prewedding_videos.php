<?php
/**
 * prewedding_videos.php — Pre-Wedding couple films.
 *
 * The six couple films from the on-disk PWVIDS set. Click any film card to
 * play it in the modal (YouTube-nocookie embed). Mirrors wedding album pages
 * via lib_prewedding.php.
 */

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '/lib_prewedding.php';

// Shared "Call Now" + WhatsApp floating contact UI (identical to the home page).
require_once __DIR__ . '/lib_contact.php';

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pre-Wedding Videos | Deepak Studios</title>
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
  main { max-width: 1080px; margin: 0 auto; padding: 2rem 1.2rem 3rem; }
  .grid { display: grid; grid-template-columns: 1fr; gap: 1.1rem; }
  @media (min-width: 640px)  { .grid { grid-template-columns: repeat(2, 1fr); } }
  @media (min-width: 1024px) { .grid { grid-template-columns: repeat(3, 1fr); } }
  .vcard { position: relative; display: block; cursor: pointer; border-radius: 10px; overflow: hidden;
           border: 1px solid #2a2a2a; background: #141414; aspect-ratio: 16 / 9; }
  .vcard img { display: block; width: 100%; height: 100%; object-fit: cover;
               transition: transform .4s ease; }
  .vcard:hover img { transform: scale(1.05); }
  .vcard .vplay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                  width: 3.6rem; height: 3.6rem; border-radius: 999px; display: grid;
                  place-items: center; color: #fff; background: rgba(212, 175, 55, .92); }
  .vcard .vmeta { position: absolute; left: 0; right: 0; bottom: 0; padding: 1rem 1.05rem;
                  background: linear-gradient(to top, rgba(5, 5, 8, .8), transparent); }
  .vcard .vmeta h3 { margin: 0; font-size: 1.1rem; }
  .vcard .vmeta small { color: #c9a86a; text-transform: uppercase; letter-spacing: .1em; font-size: .7rem; }
  .modal { display: none; position: fixed; inset: 0; z-index: 60; background: rgba(0,0,0,.94);
           align-items: center; justify-content: center; padding: 1.5rem; }
  .modal .vm-box { max-width: min(1000px, 94vw); aspect-ratio: 16 / 9; width: 100%;
                   position: relative; }
  .modal iframe { width: 100%; height: 100%; border: 0; border-radius: 8px; display: block; }
  .modal .close { position: absolute; top: -2.4rem; right: 0; font-size: 2.2rem; color: #fff;
                  cursor: pointer; line-height: 1; }
  footer { text-align: center; padding: 1.6rem; color: #666; font-size: .85rem; }
  .social { display: flex; gap: .7rem; justify-content: center; margin: 0 0 1rem; }
  .social a { width: 2.4rem; height: 2.4rem; border-radius: 999px; display: inline-flex;
    align-items: center; justify-content: center; border: 1px solid #2a2a2a; color: #8a8a8a;
    transition: all .3s; }
  .social a:hover { color: #0d0d0d; background: #c9a86a; border-color: #c9a86a; transform: translateY(-2px); }
  .social svg { width: 1.1rem; height: 1.1rem; fill: currentColor; }
</style>
<?php ds_contact_head(); ?>
</head>
<body>
<header>
  <h1>Pre-Wedding <span class="gold">Videos</span></h1>
  <p><?= count($videos) ?> couple films &mdash; click any film to play</p>
  <div class="crumbs"><a href="index.html">Home</a> &rsaquo;
    <a href="prewedding.php">Pre-Wedding Photography</a> &rsaquo; Videos</div>
</header>

<main>
  <div class="grid">
    <?php foreach ($videos as $v): ?>
      <div class="vcard" onclick="openVideo('<?= $esc($v['id']) ?>')" role="button" tabindex="0"
           aria-label="Play <?= $esc($v['n']) ?> pre-wedding film"
           onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openVideo('<?= $esc($v['id']) ?>')}">
        <img loading="lazy" src="https://i.ytimg.com/vi/<?= $esc($v['id']) ?>/hqdefault.jpg"
             alt="<?= $esc($v['n']) ?> pre-wedding film">
        <div class="vplay">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg>
        </div>
        <div class="vmeta">
          <small>Pre-Wedding Film</small>
          <h3><?= $esc($v['n']) ?></h3>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<div class="modal" id="vmodal" onclick="if(event.target===this)closeVideo()">
  <div class="vm-box">
    <span class="close" onclick="closeVideo()" aria-label="Close video">&times;</span>
    <iframe id="vframe" src="" title="Pre-Wedding film player" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
  </div>
</div>

<footer><div class="social">
<a href="https://www.instagram.com/deepakstudiosofficial_/" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
<a href="https://www.facebook.com/deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
<a href="https://www.youtube.com/@deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
</div>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

<script>
function openVideo(id) {
  var f = document.getElementById('vframe');
  f.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0';
  document.getElementById('vmodal').style.display = 'flex';
}
function closeVideo() {
  var f = document.getElementById('vframe');
  f.src = '';
  document.getElementById('vmodal').style.display = 'none';
}
document.addEventListener('keydown', function (e) {
  if (e.key === 'Escape') { closeVideo(); }
});
</script>
<?php ds_contact_fab(); ?>
</body>
</html>
