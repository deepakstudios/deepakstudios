<?php
/**
 * photography.php - Photography Hub | Deepak Studios
 *
 * Premium photography portfolio in the same luxury dark + gold studio language as
 * reels.php and cinematography.php: fixed premium navbar (Photography active),
 * full-bleed hero with a replaceable hero image, a clean category filter and a
 * photo / album card grid that opens the existing working lightbox in-page.
 *
 * This page only READS the existing photo folders - it stores no copies and deletes
 * nothing, so every photograph, album, link and folder below keeps working exactly as
 * before on wedding.php, prewedding_photos.php and the album pages a.php - e.php:
 *
 *   photos/prewedding/       -> Pre-Wedding photographs (flat folder)
 *   photos/wedding/a .. e/   -> Wedding albums (one folder per album page)
 *
 * PHOTOS: just upload image files into those folders (BaoTa File Manager / FTP) and
 * they appear here automatically - no code edit, no deploy. Supported:
 * jpg, jpeg, png, webp, gif, avif.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: text/html; charset=UTF-8');

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

/* =====================================================================
 * HERO IMAGE - SINGLE CONFIG POINT
 * ------------------------------------------------------------------------
 * To change the hero picture: edit ONLY the $photoHeroImage path below.
 * No HTML, CSS or JS change is needed anywhere else on this page - the path
 * is not repeated anywhere else in the file.
 *   $photoHeroImage : image path (jpg / jpeg / png / webp all work)
 *   $photoHeroPos   : desktop object-position  (keeps the subject in frame)
 *   $photoHeroPosMb : phone object-position    (stops the subject being cropped)
 * ===================================================================== */
$photoHeroImage = 'photos/hero/deepakstudiosbokaro.webp';
$photoHeroPos   = 'center center';
$photoHeroPosMb = 'center center';

/* =====================================================================
 * PHOTO SOURCES - the existing folders, untouched.
 * ===================================================================== */
$EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'avif'];

/** Scan one photo folder and return its image files, name-sorted. */
$scanFolder = static function (string $dir) use ($EXTENSIONS): array {
    $files = [];
    if (is_dir($dir)) {
        foreach (new DirectoryIterator($dir) as $file) {
            if ($file->isDot() || !$file->isFile()) {
                continue;
            }
            $ext = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));
            if (in_array($ext, $EXTENSIONS, true)) {
                $files[] = $file->getFilename();
            }
        }
    }
    sort($files, SORT_STRING | SORT_FLAG_CASE);
    return $files;
};

/* The existing pre-wedding photographs are stored under their own YouTube ID.
   Give them the same names used on cinematography.php so the two hubs read as one
   brand; any newly uploaded file still works and simply falls back to a generic
   label, so the folder stays owner-managed. */
$PREW_TITLES = [
    'yvW6COhgwV0.jpg' => 'The First Glimpse',
    'Tcm2kFq0-sA.jpg' => 'Before We Say Yes',
    'YfccOo4h4Kk.jpg' => 'Rooftop Confessions',
    'LhjChNCyl9E.jpg' => 'Golden Hour Walk',
    'NEJ4yZ-cs6g.jpg' => 'Frames of Forever',
    'r0EflaC0a_U.jpg' => 'Before the Bells',
];

/* The existing wedding albums keep their own pages (a.php - e.php). */
$ALBUMS = [
    'a' => ['label' => 'Album A', 'tint' => '#141210'],
    'b' => ['label' => 'Album B', 'tint' => '#1a1410'],
    'c' => ['label' => 'Album C', 'tint' => '#131018'],
    'd' => ['label' => 'Album D', 'tint' => '#161210'],
    'e' => ['label' => 'Album E', 'tint' => '#10141a'],
];

/* =====================================================================
 * CARD DATA - one entry per photograph and per album.
 *   type : photo (opens the lightbox) | album (opens its existing album page)
 *   cat  : prewedding | wedding
 * ===================================================================== */
$ITEMS = [];
$photoCount = 0;
$albumCount = 0;

foreach ($scanFolder(__DIR__ . '/photos/prewedding') as $file) {
    $ITEMS[] = [
        'type' => 'photo',
        'cat'  => 'prewedding',
        'src'  => 'photos/prewedding/' . rawurlencode($file),
        't'    => $PREW_TITLES[$file] ?? 'Pre-Wedding Photograph',
        'meta' => 'Pre-Wedding',
    ];
    $photoCount++;
}

foreach ($ALBUMS as $letter => $album) {
    $inAlbum = count($scanFolder(__DIR__ . '/photos/wedding/' . $letter));
    $ITEMS[] = [
        'type' => 'album',
        'cat'  => 'wedding',
        'href' => $letter . '.php',
        't'    => $album['label'],
        'meta' => $inAlbum . ' photo' . ($inAlbum === 1 ? '' : 's'),
        'tint' => $album['tint'],
    ];
    $albumCount++;
}

/* =====================================================================
 * CATEGORY FILTER - only the categories that actually hold content are
 * rendered, so no empty tab is ever shown.
 * ===================================================================== */
$CATS = [];
foreach ($ITEMS as $item) {
    if (!isset($CATS[$item['cat']])) {
        $CATS[$item['cat']] = 0;
    }
    $CATS[$item['cat']]++;
}

$CATLABEL = [
    'all'        => 'All',
    'prewedding' => 'Pre-Wedding',
    'wedding'    => 'Wedding',
];

$MAIN = ['all'];
foreach (['prewedding', 'wedding'] as $key) {
    if (isset($CATS[$key])) {
        $MAIN[] = $key;
    }
}

$grandTotal = $photoCount + $albumCount;

// Shared "Call Now" + WhatsApp floating contact UI (identical to the home page).
require_once __DIR__ . '/lib_contact.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Photography | Deepak Studios</title>
<meta name="description" content="Luxury wedding, pre-wedding and celebration photography by Deepak Studios. Timeless photographs, authentic emotions, beautifully preserved.">
<style>
  :root{
    --bg:#0b0b0c; --panel:#141416; --card:#18181a; --line:#26262a;
    --ink:#f2ecdf; --mut:#a8a294; --gold:#c9a86a; --gold2:#e6c980;
    --serif:Georgia,"Times New Roman",serif;
  }
  *{box-sizing:border-box}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);
        font-family:var(--serif);overflow-x:hidden}
  a{color:var(--gold);text-decoration:none}
  img{max-width:100%}

/* ======== LUXURY NAV + PHOTOGRAPHY HERO (shared design language with reels.php + cinematography.php) ======== */
:root{--rx-shell:1200px; --rx-head:74px;}

/* ---------- premium navbar ---------- */
.rnav{position:fixed;top:0;left:0;right:0;z-index:1200;border-bottom:1px solid transparent;
      transition:background .35s ease,box-shadow .35s ease,border-color .35s ease}
.rnav.solid{background:rgba(9,9,10,.9);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
      border-bottom-color:rgba(201,168,106,.22);box-shadow:0 18px 44px -34px rgba(0,0,0,.95)}
.rnav-in{max-width:var(--rx-shell);margin:0 auto;padding:1.15rem 1.25rem;display:flex;
        align-items:center;justify-content:space-between;gap:1.4rem}
.rbrand{text-decoration:none;display:flex;flex-direction:column;line-height:1.15}
.rbrand-name{font-family:var(--serif);font-size:1.3rem;font-weight:400;letter-spacing:.15em;
        text-transform:uppercase;color:var(--ink);transition:color .3s ease;white-space:nowrap}
.rbrand:hover .rbrand-name{color:var(--gold2)}
.rbrand-sub{margin-top:.32rem;font-size:.55rem;letter-spacing:.34em;text-transform:uppercase;
        color:var(--gold);opacity:.88}
.rnav-desk{display:none;align-items:center;gap:2rem}
.rnav-desk a{position:relative;text-decoration:none;font-size:.7rem;letter-spacing:.24em;
        text-transform:uppercase;color:var(--mut);padding:.45rem 0;transition:color .3s ease}
.rnav-desk a::after{content:"";position:absolute;left:0;bottom:0;height:1px;width:0;
        background:var(--gold);transition:width .35s ease}
.rnav-desk a:hover{color:var(--ink)}
.rnav-desk a:hover::after{width:100%}
.rnav-desk a.on{color:var(--gold2)}
.rnav-desk a.on::after{width:100%;height:1px;background:var(--gold);
        box-shadow:0 0 12px rgba(230,201,128,.8)}
.rburger{display:inline-flex;flex-direction:column;justify-content:center;gap:5px;
        width:2.5rem;height:2.5rem;padding:0 .58rem;background:transparent;cursor:pointer;
        border:1px solid rgba(201,168,106,.34);border-radius:999px}
.rburger span{display:block;height:1px;background:var(--gold2);
        transition:transform .3s ease,opacity .3s ease}
.rburger.x span:nth-child(1){transform:translateY(6px) rotate(45deg)}
.rburger.x span:nth-child(2){opacity:0}
.rburger.x span:nth-child(3){transform:translateY(-6px) rotate(-45deg)}
.rmobmenu{display:none;background:rgba(9,9,10,.97);backdrop-filter:blur(14px);
        -webkit-backdrop-filter:blur(14px);border-top:1px solid rgba(201,168,106,.18)}
.rmobmenu.open{display:block}
.rmobmenu a{display:block;padding:1.05rem 1.5rem;text-decoration:none;font-size:.72rem;
        letter-spacing:.24em;text-transform:uppercase;color:var(--mut);
        border-bottom:1px solid rgba(255,255,255,.045)}
.rmobmenu a.on{color:var(--gold2)}
@media (min-width:900px){
  .rnav-desk{display:flex}
  .rburger{display:none}
  .rmobmenu{display:none!important}
}

/* ---------- photography hero ---------- */
.rhero{position:relative;min-height:100vh;min-height:100svh;display:flex;
        align-items:center;justify-content:center;overflow:hidden;isolation:isolate;background:#08080a}
.rhero-img{position:absolute;inset:0;z-index:-3;width:100%;height:100%;
        object-fit:cover;object-position:<?= $esc($photoHeroPos) ?>;user-select:none}
.rhero-ov{position:absolute;inset:0;z-index:-2;pointer-events:none;background:
        linear-gradient(180deg,rgba(8,8,10,.88) 0%,rgba(8,8,10,.3) 44%,rgba(8,8,10,.93) 100%),
        radial-gradient(ellipse at 38% 46%,rgba(6,6,8,.05) 0%,rgba(6,6,8,.8) 78%)}
.rhero-grain{position:absolute;inset:0;z-index:-1;pointer-events:none;opacity:.035;
        background-image:radial-gradient(rgba(255,255,255,.9) .5px,transparent .5px);
        background-size:3px 3px}
.rhero-in{position:relative;max-width:var(--rx-shell);margin:0 auto;text-align:center;
        padding:calc(var(--rx-head) + 3.2rem) 1.35rem 6.4rem}
.rhero-eyebrow{margin:0;font-size:.6rem;letter-spacing:.42em;text-transform:uppercase;
        color:var(--gold);opacity:.92}
.rhero-eyebrow i{font-style:normal;opacity:.5;padding:0 .3em}
.rhero-rule{display:block;width:62px;height:1px;margin:1.45rem auto;
        background:linear-gradient(90deg,transparent,var(--gold),transparent);
        box-shadow:0 0 12px rgba(230,201,128,.55)}
.rhero-h{margin:0;font-size:clamp(1.85rem,6.4vw,4.6rem);font-weight:400;line-height:1.08;
        letter-spacing:.1em;text-transform:uppercase;color:#f6f1e6;
        text-shadow:0 2px 30px rgba(0,0,0,.72),0 0 62px rgba(201,168,106,.12)}
.rhero-sub{margin:1.65rem auto 0;max-width:34rem;font-size:clamp(1rem,2.3vw,1.26rem);
        line-height:1.62;color:#ddd5c6;font-style:italic}
.rhero-sup{margin:.9rem auto 0;max-width:31rem;font-size:.85rem;letter-spacing:.05em;
        color:var(--mut)}
.rhero-cta{display:inline-block;margin-top:2.4rem;padding:.96rem 2.5rem;text-decoration:none;
        font-size:.7rem;letter-spacing:.3em;text-transform:uppercase;color:#0b0b0c;
        background:var(--gold2);border:1px solid var(--gold2);border-radius:999px;
        box-shadow:0 14px 38px -14px rgba(230,201,128,.6);
        transition:transform .3s ease,box-shadow .3s ease,background .3s ease}
.rhero-cta:hover{transform:translateY(-2px);background:#f0dcae;
        box-shadow:0 20px 46px -14px rgba(230,201,128,.75)}
.rhero-scroll{position:absolute;left:0;right:0;bottom:1.5rem;margin:0;text-align:center;
        font-size:.55rem;letter-spacing:.4em;text-transform:uppercase;color:var(--mut);opacity:.72}
.rhero-scroll i{font-style:normal;display:inline-block;margin-left:.45em;color:var(--gold)}

/* ---------- section head ---------- */
.rsec{text-align:center;margin:0 auto 1.9rem}
.rsec-eyebrow{margin:0;font-size:.58rem;letter-spacing:.42em;text-transform:uppercase;color:var(--gold);opacity:.9}
.rsec-h{margin:.7rem 0 0;font-size:clamp(1.15rem,3.4vw,1.7rem);font-weight:400;letter-spacing:.16em;
        text-transform:uppercase;color:var(--ink)}
.rsec-sup{margin:.75rem 0 0;color:var(--mut);font-size:.9rem;font-style:italic}

/* ---------- filter bar ---------- */
.filters{display:flex;gap:.6rem .95rem;justify-content:center;flex-wrap:wrap;margin:0 0 2rem}
.filters button{cursor:pointer;color:var(--mut);font-family:var(--serif);font-size:.78rem;
        letter-spacing:.16em;text-transform:uppercase;background:transparent;border:0;
        border-bottom:2px solid transparent;padding:.5rem .15rem;
        transition:color .25s ease,border-color .25s ease}
.filters button:hover{color:var(--ink)}
.filters button.on{color:var(--gold2);border-bottom-color:var(--gold)}

/* ---------- photo / album cards ---------- */
main{max-width:var(--rx-shell);margin:0 auto;padding:3.4rem 1.25rem 4.2rem}
.pgrid{display:grid;grid-template-columns:1fr;gap:1.15rem}
@media (min-width:560px){.pgrid{grid-template-columns:repeat(2,1fr)}}
@media (min-width:900px){.pgrid{grid-template-columns:repeat(3,1fr)}}
.pc{position:relative;display:block;width:100%;padding:0;margin:0;text-align:left;cursor:pointer;
      font-family:var(--serif);color:inherit;border-radius:12px;overflow:hidden;
      border:1px solid var(--line);background:var(--card);aspect-ratio:4/3;
      box-shadow:0 10px 28px -12px rgba(0,0,0,.7);
      transition:border-color .35s ease,box-shadow .35s ease}
.pc:hover{border-color:#3a3a3e;box-shadow:0 18px 40px -16px rgba(0,0,0,.85)}
.pc .th{display:block;width:100%;height:100%;object-fit:cover;
      transition:transform .5s cubic-bezier(.22,.61,.36,1)}
.pc:hover .th{transform:scale(1.04)}
.pc .shade{position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.14) 0%,
      rgba(0,0,0,0) 34%,rgba(0,0,0,.82) 100%)}
.pc .zoom{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:3.2rem;height:3.2rem;
      border-radius:50%;display:grid;place-items:center;background:rgba(201,168,106,.94);
      color:#0b0b0c;opacity:0;transition:opacity .3s ease,transform .3s ease;
      box-shadow:0 6px 18px rgba(0,0,0,.45)}
.pc:hover .zoom{opacity:1;transform:translate(-50%,-50%) scale(1.04)}
.pc .cat{position:absolute;top:.7rem;left:.7rem;font-size:.6rem;letter-spacing:.16em;
      text-transform:uppercase;color:#e9d9b5;background:rgba(10,10,10,.55);
      border:1px solid rgba(201,168,106,.5);padding:.26rem .55rem;border-radius:999px}
.pc .ttl{position:absolute;left:.85rem;right:.85rem;bottom:.72rem;color:var(--ink);
      font-size:.95rem;line-height:1.25}
.pc .ttl small{display:block;color:var(--gold);font-size:.66rem;letter-spacing:.12em;
      text-transform:uppercase;margin-bottom:.22rem}
.pc .letter{display:grid;place-items:center;position:absolute;inset:0;font-size:clamp(2.6rem,9vw,4.2rem);
      font-weight:400;letter-spacing:.06em;color:rgba(201,168,106,.5)}

/* ---------- owner note (kept from the existing gallery pages) ---------- */
.onote{max-width:760px;margin:0 auto 2.2rem;padding:1.1rem 1.35rem;border-radius:12px;
      border:1px dashed var(--line);background:linear-gradient(180deg,rgba(201,168,106,.03),transparent);
      color:var(--mut);font-size:.86rem;line-height:1.7;text-align:center}
.onote b{color:var(--gold2);font-weight:400}
.onote code{color:var(--gold);font-family:var(--serif)}

/* ---------- premium empty state ---------- */
.empty{text-align:center;color:var(--mut);padding:3.4rem 1.4rem;border:1px dashed var(--line);
      border-radius:14px;background:linear-gradient(180deg,rgba(201,168,106,.035),transparent)}
.empty .emark{display:block;font-size:.58rem;letter-spacing:.42em;text-transform:uppercase;
      color:var(--gold);opacity:.85;margin-bottom:.95rem}
.empty strong{display:block;color:var(--gold2);font-weight:400;font-size:1.08rem;letter-spacing:.14em;
      text-transform:uppercase;margin-bottom:.8rem}
.empty p{margin:0 auto;max-width:32rem;font-size:.9rem;line-height:1.75}

footer{text-align:center;padding:1.6rem 1rem;color:#6e6a60;font-size:.85rem}
.social{display:flex;gap:.7rem;justify-content:center;margin:0 0 1rem}
.social a{width:2.4rem;height:2.4rem;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;
  border:1px solid rgba(201,168,106,.35);color:#8a8a8a;transition:all .3s}
.social a:hover{color:#0d0d0d;background:var(--gold);border-color:var(--gold);transform:translateY(-2px);
  box-shadow:0 0 18px rgba(201,168,106,.45)}
.social svg{width:1.1rem;height:1.1rem;fill:currentColor}

/* ---------- premium lightbox (same open/close API as the existing galleries) ---------- */
.lightbox{display:none;position:fixed;inset:0;z-index:1600;align-items:center;justify-content:center;
      padding:1.4rem;cursor:zoom-out;background:rgba(6,6,8,.97);
      backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px)}
.lightbox.open{display:flex}
.lb-shell{position:relative;max-width:min(94vw,1200px);max-height:90vh;display:flex;
      flex-direction:column;gap:.85rem}
.lightbox img.lb-img{display:block;max-width:100%;max-height:82vh;border-radius:12px;
      border:1px solid rgba(201,168,106,.34);box-shadow:0 50px 120px -30px rgba(0,0,0,.95);
      background:#0d0d0d}
.lb-cap{text-align:center;color:var(--ink);font-size:.92rem;line-height:1.4}
.lb-cap small{display:block;color:var(--gold);font-size:.62rem;letter-spacing:.2em;
      text-transform:uppercase;margin-bottom:.24rem}
.lb-close{position:fixed;top:.9rem;right:1.3rem;width:2.6rem;height:2.6rem;border-radius:50%;
      display:grid;place-items:center;font-size:1.2rem;line-height:1;color:var(--mut);
      background:rgba(12,12,14,.7);border:1px solid var(--line);cursor:pointer;transition:.2s ease}
.lb-close:hover{color:var(--ink);border-color:var(--gold);background:rgba(201,168,106,.12)}
body.vlock,html.vlock{overflow:hidden}

/* ---------- mobile polish ---------- */
@media (max-width:640px){
  .rhero-img{object-position:<?= $esc($photoHeroPosMb) ?>}
  .rhero-in{padding-top:calc(var(--rx-head) + 2.1rem);padding-bottom:5.6rem}
  .rhero-h{letter-spacing:.06em}
  .rhero-eyebrow{font-size:.53rem;letter-spacing:.3em}
  .rhero-sub{font-size:.97rem}
  .rhero-cta{width:100%;max-width:19rem;padding:.92rem 1.4rem}
  .rhero-rule{margin:1.15rem auto}
  .rhero-scroll{letter-spacing:.3em}
  main{padding-top:2.6rem}
  .lightbox{padding:.9rem}
  .lb-shell{max-height:88vh}
  .lightbox img.lb-img{max-height:74vh}
}
@media (max-width:380px){
  .rbrand-name{font-size:1.08rem;letter-spacing:.1em}
  .rbrand-sub{font-size:.48rem;letter-spacing:.24em}
  .filters button{font-size:.7rem;letter-spacing:.12em}
  .rhero-sup{font-size:.78rem}
}
/* ======== end luxury nav + hero ======== */
</style>
<?php ds_contact_head(); ?>
</head>
<body>

<!-- ======== PREMIUM NAVBAR ======== -->
<header class="rnav" id="rnav">
  <div class="rnav-in">
    <a href="index.html" class="rbrand">
      <span class="rbrand-name">Deepak Studios</span>
      <span class="rbrand-sub">Wedding Photography</span>
    </a>
    <nav class="rnav-desk" aria-label="Primary">
      <a href="index.html">Home</a>
      <a href="photography.php" class="on" aria-current="page">Photography</a>
      <a href="cinematography.php">Cinematography</a>
      <a href="reels.php">Reels</a>
      <a href="about.php">About Us</a>
      <?php ds_contact_header_call(); ?>
    </nav>
    <button type="button" class="rburger" id="rburger" aria-label="Open menu"
            aria-expanded="false" aria-controls="rmobmenu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="rmobmenu" id="rmobmenu">
    <a href="index.html">Home</a>
    <a href="photography.php" class="on" aria-current="page">Photography</a>
    <a href="cinematography.php">Cinematography</a>
    <a href="reels.php">Reels</a>
    <a href="about.php">About Us</a>
  </div>
</header>

<!-- ======== PHOTOGRAPHY HERO ======== -->
<section class="rhero" aria-labelledby="rhero-h">
  <img class="rhero-img"
       src="<?= $esc($photoHeroImage) ?>"
       alt="Deepak Studios wedding photography"
       width="1920" height="1080"
       fetchpriority="high" decoding="async">
  <span class="rhero-ov" aria-hidden="true"></span>
  <span class="rhero-grain" aria-hidden="true"></span>
  <div class="rhero-in">
    <p class="rhero-eyebrow">DEEPAK STUDIOS <i>&bull;</i> PHOTOGRAPHY</p>
    <span class="rhero-rule" aria-hidden="true"></span>
    <h1 class="rhero-h" id="rhero-h">THE ART OF CAPTURING MOMENTS</h1>
    <p class="rhero-sub">Timeless photographs. Authentic emotions. Beautifully preserved.</p>
    <p class="rhero-sup">Explore our Wedding, Pre-Wedding, Engagement &amp; Celebration Photography.</p>
    <a class="rhero-cta" id="rheroCta" href="#gallery">EXPLORE PHOTOGRAPHY</a>
  </div>
  <p class="rhero-scroll" aria-hidden="true">SCROLL TO EXPLORE <i>&#8595;</i></p>
</section>

<main id="gallery">
  <div class="rsec">
    <p class="rsec-eyebrow">PORTFOLIO</p>
    <h2 class="rsec-h">OUR PHOTOGRAPHY</h2>
    <p class="rsec-sup"><?= $photoCount ?> photograph<?= $photoCount === 1 ? '' : 's' ?> across <?= $albumCount ?> wedding albums.</p>
  </div>

  <div class="filters" id="filters" role="tablist" aria-label="Photography category">
    <?php foreach ($MAIN as $m): ?>
      <button type="button" data-m="<?= $esc($m) ?>"
              class="<?= $m === 'all' ? 'on' : '' ?>"><?= $esc($CATLABEL[$m]) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="onote">
    <b>Kaise photos add karein (site owner):</b> apne photographs
    <code>photos/prewedding/</code> ya <code>photos/wedding/&lt;album&gt;/</code> folder me
    upload karein (BaoTa File Manager ya FTP). Files turant yahan aa jaati hain —
    koi code change nahi, koi deploy nahi. Supported: <code>jpg, jpeg, png, webp, gif, avif</code>.
  </div>

  <div class="pgrid" id="grid">
    <?php foreach ($ITEMS as $it): ?>
      <?php if ($it['type'] === 'photo'): ?>
        <button type="button" class="pc"
data-cat="<?= $esc($it['cat']) ?>"
data-src="<?= $esc($it['src']) ?>"
data-t="<?= $esc($it['t']) ?>"
aria-label="View <?= $esc($it['t']) ?>">
          <img class="th" loading="lazy" src="<?= $esc($it['src']) ?>" alt="<?= $esc($it['t']) ?>">
          <span class="shade"></span>
          <span class="zoom" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg></span>
          <span class="cat"><?= $esc($CATLABEL[$it['cat']]) ?></span>
          <span class="ttl"><small><?= $esc($it['meta']) ?></small><?= $esc($it['t']) ?></span>
        </button>
      <?php else: ?>
        <a class="pc" href="<?= $esc($it['href']) ?>" data-cat="<?= $esc($it['cat']) ?>">
          <span class="th" style="background:<?= $esc($it['tint']) ?>"></span>
          <span class="shade"></span>
          <span class="letter" aria-hidden="true"><?= $esc(strtoupper(basename($it['href'], '.php'))) ?></span>
          <span class="cat">Wedding Album</span>
          <span class="ttl"><small><?= $esc($it['meta']) ?></small><?= $esc($it['t']) ?></span>
        </a>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</main>

<footer><div class="social">
<a href="https://www.instagram.com/deepakstudiosofficial_/" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
<a href="https://www.facebook.com/deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
<a href="https://www.youtube.com/@deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
</div>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>

<?php ds_contact_fab(); ?>

<!-- ======== LIGHTBOX (same #lightbox / #lb-img contract as the existing galleries) ======== -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Photograph viewer">
  <button type="button" class="lb-close" id="lb-close" aria-label="Close viewer">&#10005;</button>
  <div class="lb-shell">
    <img class="lb-img" id="lb-img" src="" alt="">
    <p class="lb-cap" id="lb-cap"><small id="lb-cat"></small><span id="lb-t"></span></p>
  </div>
</div>

<!-- ======== FILTER RENDER ======== -->
<script>
(function () {
  'use strict';
  var DATA = <?= json_encode($ITEMS, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  var LABELS = <?= json_encode($CATLABEL, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  var EMPTY = {
    mark: 'COMING SOON',
    head: 'This collection is being curated',
    body: 'New photographs for this collection are in the edit. Meanwhile, explore the rest of the portfolio.'
  };

  var grid = document.getElementById('grid');
  var cats = document.getElementById('filters');
  if (!grid || !cats) return;

  function esc(s) {
    return String(s == null ? '' : s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function card(it) {
    if (it.type === 'album') {
      return '<a class="pc" href="' + esc(it.href) + '" data-cat="' + esc(it.cat) + '">'
        + '<span class="th" style="background:' + esc(it.tint) + '"></span>'
        + '<span class="shade"></span>'
        + '<span class="letter" aria-hidden="true">' + esc(it.href.replace('.php', '').toUpperCase()) + '</span>'
        + '<span class="cat">Wedding Album</span>'
        + '<span class="ttl"><small>' + esc(it.meta) + '</small>' + esc(it.t) + '</span>'
        + '</a>';
    }
    return '<button type="button" class="pc" data-cat="' + esc(it.cat) + '"'
      + ' data-src="' + esc(it.src) + '" data-t="' + esc(it.t) + '"'
      + ' aria-label="View ' + esc(it.t) + '">'
      + '<img class="th" loading="lazy" src="' + esc(it.src) + '" alt="' + esc(it.t) + '">'
      + '<span class="shade"></span>'
      + '<span class="zoom" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5 6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg></span>'
      + '<span class="cat">' + esc(LABELS[it.cat] || it.cat) + '</span>'
      + '<span class="ttl"><small>' + esc(it.meta) + '</small>' + esc(it.t) + '</span>'
      + '</button>';
  }

  function render() {
    var rows = DATA.filter(function (it) { return main === 'all' || it.cat === main; });
    if (!rows.length) {
      grid.innerHTML = '<div class="empty" style="grid-column:1/-1">'
        + '<span class="emark">' + EMPTY.mark + '</span>'
        + '<strong>' + EMPTY.head + '</strong>'
        + '<p>' + EMPTY.body + '</p></div>';
      return;
    }
    grid.innerHTML = rows.map(card).join('');
  }

  var main = 'all';
  cats.addEventListener('click', function (e) {
    var b = e.target.closest ? e.target.closest('button') : null;
    if (!b) return;
    e.preventDefault();
    e.stopPropagation();
    main = b.getAttribute('data-m');
    Array.prototype.forEach.call(cats.querySelectorAll('button'), function (x) { x.classList.remove('on'); });
    b.classList.add('on');
    render();
  });

  render();
})();
</script>

<!-- ======== LIGHTBOX + PREMIUM NAV / HERO CTA ======== -->
<script>
(function () {
  'use strict';
  var nav    = document.getElementById('rnav');
  var burger = document.getElementById('rburger');
  var menu   = document.getElementById('rmobmenu');
  var cta    = document.getElementById('rheroCta');
  var grid   = document.getElementById('grid');
  var box    = document.getElementById('lightbox');
  var lbImg  = document.getElementById('lb-img');
  var lbT    = document.getElementById('lb-t');
  var lbCat  = document.getElementById('lb-cat');
  var lbX    = document.getElementById('lb-close');
  var lastScroll = 0;

  if (nav) {
    var onScroll = function () { nav.classList.toggle('solid', (window.pageYOffset || 0) > 24); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  if (burger && menu) {
    var setMenu = function (isOpen) {
      menu.classList.toggle('open', isOpen);
      burger.classList.toggle('x', isOpen);
      burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };
    burger.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      setMenu(!menu.classList.contains('open'));
    });
    menu.addEventListener('click', function (e) {
      var a = e.target.closest ? e.target.closest('a') : null;
      if (a) setMenu(false);
    });
  }

  if (cta) {
    cta.addEventListener('click', function (e) {
      var target = document.getElementById('gallery');
      if (!target) return;
      e.preventDefault();
      e.stopPropagation();
      var top = target.getBoundingClientRect().top + (window.pageYOffset || 0) - 70;
      if ('scrollBehavior' in document.documentElement.style) {
        window.scrollTo({ top: top, behavior: 'smooth' });
      } else {
        window.scrollTo(0, top);
      }
    });
  }

  /* ---- lightbox: same behaviour as the existing galleries (open, Esc, backdrop, X) ---- */
  if (box && lbImg) {
    window.openLightbox = function (src) {
      if (!src) return;
      lastScroll = (window.pageYOffset || document.documentElement.scrollTop) || 0;
      lbImg.src = src;
      lbImg.alt = (lbT.textContent || 'Photograph');
      box.classList.add('open');
      document.body.classList.add('vlock');
      document.documentElement.classList.add('vlock');
      window.scrollTo(0, 0);
      if (lbX) lbX.focus();
    };
    window.closeLightbox = function () {
      if (!box.classList.contains('open')) return;
      box.classList.remove('open');
      lbImg.src = '';
      document.body.classList.remove('vlock');
      document.documentElement.classList.remove('vlock');
      window.scrollTo(0, lastScroll);
    };

    if (grid) {
      grid.addEventListener('click', function (e) {
        var c = e.target.closest ? e.target.closest('.pc[data-src]') : null;
        if (!c) return;
        e.preventDefault();
        e.stopPropagation();
        var t = c.getAttribute('data-t') || 'Photograph';
        if (lbT) lbT.textContent = t;
        if (lbCat) lbCat.textContent = 'Deepak Studios';
        window.openLightbox(c.getAttribute('data-src'));
      });
    }

    box.addEventListener('click', function (e) {
      if (e.target === box || e.target.closest('.lb-shell') === null) window.closeLightbox();
    });
    if (lbX) lbX.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); window.closeLightbox(); });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && box.classList.contains('open')) {
        e.preventDefault();
        window.closeLightbox();
      }
    });
  }
})();
</script>
</body>
</html>
