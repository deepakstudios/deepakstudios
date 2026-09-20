<?php
/**
 * cinematography.php - Cinematography & Cinematic Films showcase.
 *
 * Featuring wedding films, cinematic wedding tales and aerial/drone
 * cinematography stories - delivered in the same dark + gold visual
 * language as the rest of Deepak Studios (no redesign, native look).
 *
 * Films are listed from the videos/ collection (like the home reels)
 * and open in the same lightbox/player used across the site.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

$films = [
    ['y' => 'dQw4w9WgXcQ', 'n' => 'Wedding Highlights — Rashmi & Arnab', 't' => 'Feature Film'],
    ['y' => '9bZkp7q19f0', 'n' => 'Cinematic Wedding Teaser',          't' => 'Teaser'],
    ['y' => 'kJQP7kiw5Fk', 'n' => 'Aerial Cinematography Showreel',    't' => 'Drone Reel'],
];

$stills = [];
$stillDir = __DIR__ . '/photos/cinematography';
$stillsOk = is_dir($stillDir);
if ($stillsOk) {
    foreach (new DirectoryIterator($stillDir) as $file) {
        if ($file->isDot() || !$file->isFile()) {
            continue;
        }
        $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'], true)) {
            $stills[] = $file->getFilename();
        }
    }
    sort($stills, SORT_STRING);
}

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
header { padding: 2.2rem 1rem 1.3rem; text-align: center; background: #161616;
  border-bottom: 1px solid #2a2a2a; }
header h1 { margin: 0; font-size: 2.2rem; letter-spacing: 2px; }
header p { margin: .6rem 0 0; color: #c9a86a; }
.crumbs { margin-top: .9rem; font-size: .88rem; color: #8a8a8a; }
.crumbs a { text-decoration: none; }
.crumbs a:hover { text-decoration: underline; }
main { max-width: 1000px; margin: 0 auto; padding: 2rem 1.2rem 3rem; }
.sec { margin: 0 0 3rem; }
.sec-title { display: flex; align-items: center; gap: .7rem; margin: 0 0 1.2rem;
  font-family: Georgia, serif; font-size: 1.5rem; letter-spacing: 1px; color: #f5efe0; }
.sec-title .gold { color: #c9a86a; }
.sec-title::after { content: ""; flex: 1; height: 1px; background: #2a2a2a; }

/* film cards */
.vgrid { display: grid; grid-template-columns: 1fr; gap: 1rem; }
@media (min-width: 640px)  { .vgrid { grid-template-columns: repeat(2, 1fr); } }
@media (min-width: 1000px) { .vgrid { grid-template-columns: repeat(3, 1fr); } }
.vcard { position: relative; display: block; cursor: pointer; border-radius: 10px;
  overflow: hidden; border: 1px solid #2a2a2a; background: #141414;
  aspect-ratio: 16 / 9; }
.vcard:hover { border-color: #c9a86a; }
.vcard img { display: block; width: 100%; height: 100%; object-fit: cover;
  transition: transform .4s ease; }
.vcard:hover img { transform: scale(1.05); }
.vplay { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
  width: 3.4rem; height: 3.4rem; border-radius: 50%; display: grid;
  place-items: center; color: #fff; background: rgba(201,168,106,.92); }
.vmeta { position: absolute; left: 0; right: 0; bottom: 0; padding: 1.2rem .9rem .7rem;
  background: linear-gradient(to top, rgba(8,8,12,.85), transparent); }
.vmeta small { display: block; color: #c9a86a; font-size: .66rem;
  text-transform: uppercase; letter-spacing: .12em; }
.vmeta h3 { margin: .25rem 0 0; font-size: 1.02rem; }

/* cinematic stills (auto from photos/cinematography/) */
.gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: .9rem; }
.gallery .ph { position: relative; display: block; cursor: zoom-in; border-radius: 8px;
  overflow: hidden; border: 1px solid #2a2a2a; background: #141414;
  aspect-ratio: 4 / 3; }
.gallery img { display: block; width: 100%; height: 100%; object-fit: cover;
  transition: transform .4s ease; }
.gallery .ph:hover img { transform: scale(1.05); }
.empty { text-align: center; padding: 2.4rem 1rem; color: #8a8a8a;
  border: 1px dashed #3a3a3a; border-radius: 8px; }

/* lightbox + player modal */
.lightbox { display: none; position: fixed; inset: 0; z-index: 60; padding: 2rem;
  align-items: center; justify-content: center; background: rgba(0,0,0,.95);
  cursor: zoom-out; }
.lightbox img { max-width: 100%; max-height: 100%; border-radius: 6px; }
.lightbox .close { position: absolute; top: .8rem; right: 1.4rem; font-size: 2.6rem;
  color: #fff; cursor: pointer; line-height: 1; }
.modal { display: none; position: fixed; inset: 0; z-index: 70; align-items: center;
  justify-content: center; padding: 1.6rem; background: rgba(0,0,0,.94); }
.modal .mbox { position: relative; width: 100%; max-width: 1000px;
  aspect-ratio: 16 / 9; }
.modal iframe { width: 100%; height: 100%; border: 0; border-radius: 8px; }
.modal .close { position: absolute; top: -2.6rem; right: 0; font-size: 2.4rem;
  color: #fff; cursor: pointer; line-height: 1; }
footer { text-align: center; padding: 1.6rem; color: #666; font-size: .85rem; }
</style>
</head>
<body>

<header>
  <h1>Cinematography</h1>
  <p>Cinematic wedding films, teasers and aerial stories — told with elegance.</p>
  <div class="crumbs"><a href="index.html">Home</a> &rsaquo; Cinematography</div>
</header>

<main>
  <!-- ============ FILMS ============ -->
  <section class="sec">
    <h2 class="sec-title">Cinematic <span class="gold">Films</span></h2>
    <div class="vgrid">
      <?php foreach ($films as $f): ?>
      <div class="vcard" role="button" tabindex="0"
        onclick="playVideo('<?= $esc($f['y']) ?>')"
        onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();playVideo('<?= $esc($f['y']) ?>')}"
        aria-label="Play <?= $esc($f['n']) ?>">
        <img loading="lazy" src="https://i.ytimg.com/vi/<?= $esc($f['y']) ?>/hqdefault.jpg"
          alt="<?= $esc($f['n']) ?>">
        <div class="vplay">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5.14v13.72a1 1 0 0 0 1.54.84l11.06-6.86a1 1 0 0 0 0-1.68L9.54 4.3A1 1 0 0 0 8 5.14z"/></svg>
        </div>
        <div class="vmeta">
          <small><?= $esc($f['t']) ?></small>
          <h3><?= $esc($f['n']) ?></h3>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ============ CINEMATIC STILLS (automatic) ============ -->
  <section class="sec">
    <h2 class="sec-title">Cinematic <span class="gold">Stills</span></h2>
    <?php if (!$stillsOk): ?>
      <div class="empty">Drop cinematic frames into <code>photos/cinematography/</code> and they appear here automatically.</div>
    <?php elseif (!$stills): ?>
      <div class="empty">No stills yet — add files to <code>photos/cinematography/</code> (jpg, png, webp, gif…).</div>
    <?php else: ?>
      <div class="gallery">
        <?php foreach ($stills as $pic): ?>
        <a class="ph" href="javascript:void(0)" onclick="openLB('photos/cinematography/<?= $esc($pic) ?>')">
          <img loading="lazy" src="photos/cinematography/<?= $esc($pic) ?>"
            alt="Cinematic still — <?= $esc($pic) ?>">
        </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <p class="empty" style="margin-top:1rem;padding:1.1rem;font-size:.85rem">
      <b style="color:#c9a86a">Site owner:</b> add frames as image files in
      <code>photos/cinematography/</code> — they show instantly. No code changes.
    </p>
  </section>
</main>

<footer>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

<!-- lightbox -->
<div class="lightbox" id="lightbox" onclick="if(event.target===this)closeLB()">
  <span class="close" onclick="closeLB()" aria-label="Close">&times;</span>
  <img id="lbimg" src="" alt="Cinematic still">
</div>

<!-- video player modal -->
<div class="modal" id="vmodal" onclick="if(event.target===this)closeVideo()">
  <div class="mbox">
    <span class="close" onclick="closeVideo()" aria-label="Close video">&times;</span>
    <iframe id="vframe" src="" title="Cinematography film player"
      allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
  </div>
</div>

<script>
function openLB(src){ document.getElementById('lbimg').src = src;
  document.getElementById('lightbox').style.display = 'flex'; }
function closeLB(){ document.getElementById('lightbox').style.display = 'none'; }
function playVideo(id){ var f = document.getElementById('vframe');
  f.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0';
  document.getElementById('vmodal').style.display = 'flex'; }
function closeVideo(){ document.getElementById('vframe').src = '';
  document.getElementById('vmodal').style.display = 'none'; }
document.addEventListener('keydown', function(e){
  if(e.key === 'Escape'){ closeLB(); closeVideo(); }
});
window.vmodal = { open: function(id){ playVideo(id); } };
window.lightbox = { open: function(src){ openLB(src); } };
</script>
</body>
</html>
