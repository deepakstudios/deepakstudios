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
</style>
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

<footer>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

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
</body>
</html>
