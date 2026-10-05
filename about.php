<?php
/**
 * about.php - About Us | Deepak Studios
 *
 * Self-contained "About Us" page in the studio's luxury dark + gold language.
 *
 * Design system reuse
 * -------------------
 * The navbar reuses the EXACT class names and CSS of the other premium pages
 * (photography.php / reels.php / cinematography.php -> .rnav, .rnav-in, .rnav-desk,
 * .rbrand, .rburger, .rmobmenu) so the header, its mobile burger menu and the shared
 * gold "Call Now" pill are pixel-identical to the rest of the site. The floating
 * WhatsApp button and the fixed mobile bottom bar come from lib_contact.php, which is the
 * same shared renderer every other page uses - so positioning and styling match by
 * construction rather than by copy-paste.
 *
 * Everything new on this page is prefixed `ab-` and lives in its own <style> block. No
 * global selector here can reach another page: each .php file is its own document, and
 * every rule this file adds is either `ab-`-prefixed or a byte-identical copy of a rule
 * the other pages already carry.
 *
 * Images - all real, existing assets, nothing generated:
 *   photos/Team/deepak.webp                  founder portrait  (1080x1451)
 *   photos/Team/rajesh.webp                  team             (1599x1066)
 *   photos/Team/ujjwal.webp                  team             (1512x1006)
 *   photos/Team/deepakstudiosteam.webp       behind the scenes(2048x1363)
 *   photos/hero/deepakstudiosbokaro.webp     hero             (1920x1080)
 *   photos/hero/hero-bg.webp                 vision           (2069x1381)
 *   photos/prewedding/*.jpg                  behind the scenes(480x360)
 *
 * Aryan, Jogen and Jaitun have no photograph yet, so their cards carry a designed
 * monogram placeholder. No other person's photo is reused for them and nothing is
 * fabricated - drop a file named aryan.webp / jogen.webp / jaitun.webp into
 * photos/Team/ and the card renders the real photo automatically (see $TEAM below).
 *
 * To swap the hero or vision picture, edit ONLY the config block below.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: text/html; charset=UTF-8');

/* =====================================================================
 * IMAGE CONFIG - the only place these paths appear.
 * ===================================================================== */
$abHeroImage = 'photos/hero/deepakstudiosbokaro.webp';   // hero  (1920x1080)
$abHeroPos   = 'center 42%';
$abHeroPosMb = 'center 38%';

$abVisionImage = 'photos/hero/hero-bg.webp';             // vision(2069x1381)

$abBtsLead  = 'photos/Team/deepakstudiosteam.webp';      // behind the scenes lead
$abBtsThumbs = [                                          // behind the scenes mosaic
    'photos/prewedding/yvW6COhgwV0.jpg',
    'photos/prewedding/YfccOo4h4Kk.jpg',
    'photos/prewedding/LhjChNCyl9E.jpg',
    'photos/prewedding/Tcm2kFq0-sA.jpg',
];

/* =====================================================================
 * TEAM
 *   photo : path under photos/Team, or '' for the monogram placeholder
 *   mono  : initials shown in the placeholder
 *
 * A member only needs a file dropped in photos/Team/<photo> to switch from the
 * placeholder to their real photograph - no other edit.
 * ===================================================================== */
$TEAM = [
    [
        'name'  => 'Rajesh Dhibar',
        'role'  => 'Photographer &amp; Editor',
        'photo' => 'rajesh.webp',
        'mono'  => 'RD',
        'bio'   => 'With a sharp eye for detail and an instinct for the right moment, Rajesh rarely lets a special frame slip away. Honest, focused, and deeply committed to his work, he stays on the job until everything is done just right.',
    ],
    [
        'name'  => 'Ujjwal Dey',
        'role'  => 'Cinematographer',
        'photo' => 'ujjwal.webp',
        'mono'  => 'UD',
        'bio'   => 'The cheerful and approachable member of our team, Ujjwal has a natural ability to make clients feel comfortable. Once his gimbal starts moving, ordinary moments can take on a truly cinematic, movie-like feel.',
    ],
    [
        'name'  => 'Aryan',
        'role'  => 'Drone Operator',
        'photo' => 'aryan.webp',
        'mono'  => 'AR',
        'bio'   => 'Cool, creative, and always ready for the perfect aerial perspective, Aryan brings a premium touch to our films. His cinematic drone shots add a grand, royal feel that makes every celebration look even more spectacular.',
    ],
    [
        'name'  => 'Jogen',
        'role'  => 'Photographer',
        'photo' => '',            // no photograph yet - placeholder by design
        'mono'  => 'JO',
        'bio'   => 'Punctual, dedicated, and always prepared, Jogen brings a dependable energy to every event. With the latest photography gadgets at hand and a strong commitment to his work, he is always ready to capture the moments that matter.',
    ],
    [
        'name'  => 'Jaitun Aind',
        'role'  => 'Senior Editor &amp; Drone Operator',
        'photo' => '',            // no photograph yet - placeholder by design
        'mono'  => 'JA',
        'bio'   => 'With experience in both post-production and aerial cinematography, Jaitun brings two important creative skills to the team. As our senior editor and drone operator, he helps turn captured moments into polished visual stories with impactful aerial perspectives.',
    ],
];

/* Founder photograph (photos/Team). */
$abFounderImage = 'photos/Team/deepak.webp';

/* True when the referenced file is really on disk - keeps the placeholder honest if a
   photo is renamed or removed, instead of rendering a broken image. */
$abHasPhoto = static function (string $rel): bool {
    return $rel !== '' && is_file(__DIR__ . '/photos/Team/' . $rel);
};

/* Does a hero/vision/bts asset exist? */
$abHasAsset = static function (string $rel): bool {
    return is_file(__DIR__ . '/' . $rel);
};

$esc = static function (string $v): string {
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
};

/* Shared "Call Now" + WhatsApp floating contact UI - identical to every other page. */
require_once __DIR__ . '/lib_contact.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us | Deepak Studios</title>
<meta name="description" content="The story behind Deepak Studios - our founder Deepak Modak, our team, our vision and our philosophy. Preserving real emotions, not just moments.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
/* ==========================================================================
   TOKENS - deep maroon / brown with champagne gold.
   ========================================================================== */
:root{
  --ab-shell:1200px;  --ab-head:74px;
  --ab-bg:#140d0c;    --ab-bg2:#1b1210;   --ab-panel:#1f1513;
  --ab-maroon:#3d1a1c; --ab-maroon-2:#54222a;
  --ab-line:rgba(201,168,106,.20);
  --ab-line-soft:rgba(255,255,255,.07);
  --ab-ink:#f4ede2;   --ab-mut:#b5a595;   --ab-dim:#8d7f71;
  --ab-gold:#c9a86a;  --ab-gold2:#e8cd93;
  --ab-serif:"Playfair Display",Georgia,"Times New Roman",serif;
  --ab-sans:"Inter",system-ui,-apple-system,"Segoe UI",sans-serif;
}
*,*::before,*::after{box-sizing:border-box}
html,body{margin:0;padding:0;background:var(--ab-bg);color:var(--ab-ink);overflow-x:hidden}
body{font-family:var(--ab-sans);font-weight:300;line-height:1.7;
  -webkit-font-smoothing:antialiased;text-rendering:optimizeLegibility}
img{max-width:100%;display:block}
a{color:inherit;text-decoration:none}
h1,h2,h3,h4{font-family:var(--ab-serif);font-weight:400;margin:0;letter-spacing:.02em}
p{margin:0}
::selection{background:rgba(201,168,106,.3);color:#fff}
:focus-visible{outline:2px solid var(--ab-gold);outline-offset:3px}

.ab-shell{max-width:var(--ab-shell);margin:0 auto;padding:0 1.25rem}
@media(min-width:768px){.ab-shell{padding:0 2rem}}

/* film-grain veil used on the big photographic sections */
.ab-grain{position:absolute;inset:0;pointer-events:none;opacity:.04;z-index:1;
  background-image:radial-gradient(rgba(255,255,255,.9) .5px,transparent .5px);
  background-size:3px 3px}

/* ==========================================================================
   NAVBAR - same class names + rules as photography.php so the header,
   the burger menu and the shared gold "Call Now" pill match the site exactly.
   ========================================================================== */
.rnav{position:fixed;top:0;left:0;right:0;z-index:1200;border-bottom:1px solid transparent;
      transition:background .35s ease,box-shadow .35s ease,border-color .35s ease}
.rnav.solid{background:rgba(20,13,12,.92);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);
      border-bottom-color:rgba(201,168,106,.22);box-shadow:0 18px 44px -34px rgba(0,0,0,.95)}
.rnav-in{max-width:var(--ab-shell);margin:0 auto;padding:1.15rem 1.25rem;display:flex;
        align-items:center;justify-content:space-between;gap:1.4rem}
.rbrand{text-decoration:none;display:flex;flex-direction:column;line-height:1.15}
.rbrand-name{font-family:var(--ab-serif);font-size:1.3rem;font-weight:400;letter-spacing:.15em;
        text-transform:uppercase;color:var(--ab-ink);transition:color .3s ease;white-space:nowrap}
.rbrand:hover .rbrand-name{color:var(--ab-gold2)}
.rbrand-sub{margin-top:.32rem;font-size:.55rem;letter-spacing:.34em;text-transform:uppercase;
        color:var(--ab-gold);opacity:.88}
.rnav-desk{display:none;align-items:center;gap:2rem}
.rnav-desk a{position:relative;text-decoration:none;font-size:.7rem;letter-spacing:.24em;
        text-transform:uppercase;color:var(--ab-mut);padding:.45rem 0;transition:color .3s ease}
.rnav-desk a::after{content:"";position:absolute;left:0;bottom:0;height:1px;width:0;
        background:var(--ab-gold);transition:width .35s ease}
.rnav-desk a:hover{color:var(--ab-ink)}
.rnav-desk a:hover::after{width:100%}
.rnav-desk a.on{color:var(--ab-gold2)}
.rnav-desk a.on::after{width:100%;height:1px;background:var(--ab-gold);
        box-shadow:0 0 12px rgba(232,205,147,.8)}
.rburger{display:inline-flex;flex-direction:column;justify-content:center;gap:5px;
        width:2.5rem;height:2.5rem;padding:0 .58rem;background:transparent;cursor:pointer;
        border:1px solid rgba(201,168,106,.34);border-radius:999px}
.rburger span{display:block;height:1px;background:var(--ab-gold2);
        transition:transform .3s ease,opacity .3s ease}
.rburger.x span:nth-child(1){transform:translateY(6px) rotate(45deg)}
.rburger.x span:nth-child(2){opacity:0}
.rburger.x span:nth-child(3){transform:translateY(-6px) rotate(-45deg)}
.rmobmenu{display:none;background:rgba(20,13,12,.97);backdrop-filter:blur(14px);
        -webkit-backdrop-filter:blur(14px);border-top:1px solid rgba(201,168,106,.18)}
.rmobmenu.open{display:block}
.rmobmenu a{display:block;padding:1.05rem 1.5rem;text-decoration:none;font-size:.72rem;
        letter-spacing:.24em;text-transform:uppercase;color:var(--ab-mut);
        border-bottom:1px solid rgba(255,255,255,.045)}
.rmobmenu a.on{color:var(--ab-gold2)}
@media (min-width:900px){
  .rnav-desk{display:flex}
  .rburger{display:none}
  .rmobmenu{display:none!important}
}

/* ==========================================================================
   BUTTONS - the site's pill style, in this page's maroon/gold palette.
   ========================================================================== */
.ab-btn{display:inline-flex;align-items:center;justify-content:center;gap:.55rem;cursor:pointer;
        font-family:var(--ab-sans);font-size:.72rem;letter-spacing:.26em;text-transform:uppercase;
        padding:1rem 2.3rem;border-radius:999px;border:1px solid transparent;
        transition:transform .3s ease,box-shadow .3s ease,background .3s ease,color .3s ease}
.ab-btn-gold{background:linear-gradient(180deg,var(--ab-gold2),var(--ab-gold));
        color:#1a1010;box-shadow:0 14px 38px -14px rgba(232,205,147,.55)}
.ab-btn-gold:hover{transform:translateY(-2px);box-shadow:0 20px 46px -14px rgba(232,205,147,.7)}
.ab-btn-ghost{background:transparent;color:var(--ab-gold2);border-color:rgba(201,168,106,.45)}
.ab-btn-ghost:hover{transform:translateY(-2px);background:rgba(201,168,106,.1);
        border-color:var(--ab-gold2);box-shadow:0 16px 40px -18px rgba(201,168,106,.5)}

/* ==========================================================================
   SECTION FURNITURE
   ========================================================================== */
.ab-sec{position:relative;padding:5.2rem 0}
@media(min-width:900px){.ab-sec{padding:7.4rem 0}}
.ab-sec-head{text-align:center;max-width:44rem;margin:0 auto 3rem}
.ab-eyebrow{margin:0;font-size:.58rem;letter-spacing:.44em;text-transform:uppercase;
        color:var(--ab-gold);opacity:.92}
.ab-rule{display:block;width:64px;height:1px;margin:1.4rem auto;
        background:linear-gradient(90deg,transparent,var(--ab-gold),transparent);
        box-shadow:0 0 12px rgba(232,205,147,.5)}
.ab-h2{margin:0;font-size:clamp(1.5rem,4.6vw,2.5rem);line-height:1.2;color:var(--ab-ink)}
.ab-h2 em{font-style:normal;color:var(--ab-gold2)}
.ab-sup{margin:1rem 0 0;color:var(--ab-mut);font-size:.98rem;font-style:italic}

/* hairline + tint that separates the big photographic bands */
.ab-band{background:var(--ab-bg2);border-top:1px solid var(--ab-line-soft);
        border-bottom:1px solid var(--ab-line-soft)}

/* scroll reveal - the hidden start state is applied ONLY once JS has confirmed it can run,
   by the `ab-js` class added in <head>. With JavaScript disabled or erroring, no class is
   added, every section renders visible and nothing on the page can be lost. */
.ab-reveal{opacity:1;transform:none}
.ab-js .ab-reveal{opacity:0;transform:translateY(26px);
        transition:opacity .9s cubic-bezier(.22,.61,.36,1),transform .9s cubic-bezier(.22,.61,.36,1)}
.ab-js .ab-reveal.in{opacity:1;transform:none}

/* ==========================================================================
   HERO
   ========================================================================== */
.ab-hero{position:relative;min-height:100vh;min-height:100svh;display:flex;
        align-items:center;justify-content:center;overflow:hidden;isolation:isolate;
        background:#0d0807;text-align:center}
.ab-hero-img{position:absolute;inset:0;z-index:-3;width:100%;height:100%;object-fit:cover;
        object-position:<?= $esc($abHeroPos) ?>}
.ab-hero-ov{position:absolute;inset:0;z-index:-2;pointer-events:none;background:
        linear-gradient(180deg,rgba(13,8,7,.9) 0%,rgba(20,10,10,.42) 42%,rgba(13,8,7,.95) 100%),
        radial-gradient(ellipse at 50% 44%,rgba(61,26,28,.06) 0%,rgba(10,6,6,.84) 76%),
        linear-gradient(120deg,rgba(84,34,42,.3),rgba(20,13,12,0) 62%)}
.ab-hero-in{position:relative;z-index:2;max-width:var(--ab-shell);margin:0 auto;
        padding:calc(var(--ab-head) + 3.4rem) 1.35rem 6.6rem}
.ab-hero-eyebrow{margin:0;font-size:.6rem;letter-spacing:.46em;text-transform:uppercase;
        color:var(--ab-gold2);opacity:.95}
.ab-hero-h{margin:1.6rem auto 0;max-width:22ch;font-size:clamp(1.95rem,6.6vw,4.15rem);
        font-weight:400;line-height:1.14;letter-spacing:.015em;color:#fbf6ec;
        text-shadow:0 2px 34px rgba(0,0,0,.75),0 0 70px rgba(201,168,106,.16)}
.ab-hero-h em{font-style:italic;color:var(--ab-gold2)}
.ab-hero-sub{margin:1.8rem auto 0;max-width:37rem;font-size:clamp(.98rem,2.2vw,1.16rem);
        line-height:1.78;color:#e2d6c6}
.ab-hero-cta{display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center;margin-top:2.6rem}
.ab-hero-scroll{position:absolute;left:0;right:0;bottom:1.6rem;margin:0;text-align:center;
        font-size:.55rem;letter-spacing:.42em;text-transform:uppercase;color:var(--ab-dim);opacity:.8}
.ab-hero-scroll i{font-style:normal;display:inline-block;margin-left:.45em;color:var(--ab-gold)}

/* ==========================================================================
   OUR STORY
   ========================================================================== */
.ab-story{column-gap:3.4rem}
@media(min-width:1000px){.ab-story{display:grid;grid-template-columns:1fr;max-width:52rem}}
.ab-story-body p{margin:0 0 1.35rem;color:#ded2c2;font-size:1.02rem;line-height:1.95}
.ab-story-body p:last-child{margin-bottom:0}
.ab-story-quote{margin:1.5rem 0 2.4rem;padding:1.35rem 1.6rem;text-align:center;
        border-radius:14px;border:1px solid rgba(201,168,106,.28);
        background:linear-gradient(180deg,rgba(84,34,42,.34),rgba(31,21,19,.5));
        box-shadow:0 22px 54px -30px rgba(0,0,0,.9)}
.ab-story-quote p{margin:0;font-family:var(--ab-serif);font-style:italic;
        font-size:clamp(1.08rem,2.7vw,1.5rem);line-height:1.5;color:var(--ab-gold2)}
/* the one pull-quote inside the story body */
.ab-story-pull{margin:1.6rem 0;padding:1.5rem 0 1.5rem 1.6rem;
        border-left:2px solid var(--ab-gold);font-family:var(--ab-serif);font-style:italic;
        font-size:clamp(1.02rem,2.5vw,1.3rem);line-height:1.55;color:var(--ab-gold2)}
.ab-close{margin:2.6rem auto 0;max-width:46rem;padding:2.1rem 1.9rem;text-align:center;
        border-radius:16px;border:1px solid rgba(201,168,106,.34);position:relative;
        background:
          radial-gradient(ellipse at 50% 0%,rgba(232,205,147,.12),transparent 70%),
          linear-gradient(180deg,rgba(61,26,28,.55),rgba(20,13,12,.72));
        box-shadow:0 30px 70px -34px rgba(0,0,0,.95),inset 0 1px 0 rgba(232,205,147,.14)}
.ab-close::before{content:"";position:absolute;inset:.55rem;border-radius:11px;
        border:1px solid rgba(201,168,106,.16);pointer-events:none}
.ab-close p{margin:0;font-family:var(--ab-serif);font-style:italic;
        font-size:clamp(1.12rem,3vw,1.6rem);line-height:1.5;color:#fdf6e8;
        position:relative;z-index:1}

/* ==========================================================================
   FOUNDER
   ========================================================================== */
.ab-founder{display:grid;gap:2.6rem;align-items:center}
@media(min-width:900px){.ab-founder{grid-template-columns:.85fr 1.15fr;gap:3.6rem}}
.ab-founder-photo{position:relative;margin:0;overflow:hidden;border-radius:18px;
        border:1px solid var(--ab-line);background:var(--ab-panel);
        box-shadow:0 34px 80px -40px rgba(0,0,0,.95)}
/* object-fit:cover keeps the original 2:3 portrait undistorted while filling the box */
.ab-founder-photo img{width:100%;height:100%;object-fit:cover;object-position:50% 28%;
        aspect-ratio:4/5;display:block}
.ab-founder-photo::after{content:"";position:absolute;inset:0;pointer-events:none;
        background:linear-gradient(180deg,rgba(13,8,7,.28) 0%,rgba(13,8,7,0) 34%,rgba(13,8,7,.72) 100%)}
.ab-founder-frame{position:absolute;inset:.75rem;border-radius:12px;pointer-events:none;z-index:2;
        border:1px solid rgba(232,205,147,.28)}
.ab-founder-tag{position:absolute;left:1.15rem;bottom:1.15rem;z-index:3;margin:0;
        font-size:.55rem;letter-spacing:.3em;text-transform:uppercase;color:var(--ab-gold2)}
.ab-founder-name{margin:0;font-size:clamp(1.7rem,4.4vw,2.5rem);line-height:1.2;color:var(--ab-ink)}
.ab-founder-role{margin:.6rem 0 0;font-size:.66rem;letter-spacing:.32em;text-transform:uppercase;
        color:var(--ab-gold)}
.ab-founder-bio{margin:1.5rem 0 0;color:#ded2c2;font-size:1.01rem;line-height:1.9}
.ab-founder-bio p{margin:0 0 1.2rem}
.ab-founder-bio p:last-child{margin-bottom:0}

/* ==========================================================================
   OUR TEAM
   --------------------------------------------------------------------------
   Every card's photo area is the SAME size: a 1:1 box with object-fit:cover.
   The source photos are mixed orientations - deepak/rajesh/ujjwal in the
   published set are portrait 0.667 and landscape 1.50 - and a square is the
   box that minimises the worst-case crop for both (about 33% either way).
   Nothing is stretched: cover preserves the original pixels and aspect ratio,
   and the crop is centred so no face is pushed out of frame.
   ========================================================================== */
.ab-team-grid{display:grid;gap:1.5rem;grid-template-columns:1fr}
@media(min-width:600px){.ab-team-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.ab-team-grid{grid-template-columns:repeat(3,1fr);gap:1.75rem}}
.ab-tcard{position:relative;display:flex;flex-direction:column;overflow:hidden;
        border-radius:16px;border:1px solid var(--ab-line);background:var(--ab-panel);
        box-shadow:0 22px 54px -34px rgba(0,0,0,.9);
        transition:transform .45s cubic-bezier(.22,.61,.36,1),border-color .45s ease,box-shadow .45s ease}
.ab-tcard:hover{transform:translateY(-6px);border-color:rgba(232,205,147,.42);
        box-shadow:0 34px 72px -34px rgba(0,0,0,.95)}
/* identical visual photo area on every card */
.ab-tcard-photo{position:relative;width:100%;aspect-ratio:1/1;overflow:hidden;
        background:linear-gradient(160deg,#241715,#150d0c)}
.ab-tcard-photo img{width:100%;height:100%;object-fit:cover;object-position:50% 50%;
        transition:transform .8s cubic-bezier(.22,.61,.36,1)}
.ab-tcard:hover .ab-tcard-photo img{transform:scale(1.05)}
/* Per-member framing. The class is taken from the photo filename, so this hook needs no
   edit when a member's file changes. Ujjwal is framed from the top and Rajesh is zoomed in
   slightly; both were dialled in by hand and are kept exactly as they were. The hover rule
   is repeated for .rajesh because the generic :hover scale above is the more specific
   selector and would otherwise pull Rajesh back from 1.12 to 1.05 on hover. */
.ab-tcard-photo img.ujjwal{object-position:center top}
.ab-tcard-photo img.rajesh{transform:scale(1.12)}
.ab-tcard:hover .ab-tcard-photo img.rajesh{transform:scale(1.17)}
.ab-tcard-shade{position:absolute;inset:0;pointer-events:none;
        background:linear-gradient(180deg,rgba(13,8,7,.3) 0%,rgba(13,8,7,0) 40%,rgba(13,8,7,.55) 100%)}
.ab-tcard-num{position:absolute;top:.85rem;right:.9rem;z-index:2;font-family:var(--ab-serif);
        font-size:.78rem;letter-spacing:.1em;color:rgba(232,205,147,.85);
        border:1px solid rgba(201,168,106,.4);border-radius:999px;padding:.16rem .55rem;
        background:rgba(13,8,7,.55);backdrop-filter:blur(4px)}

/* monogram placeholder - used only where a real photograph does not exist yet */
.ab-tcard-mono{position:absolute;inset:0;display:grid;place-items:center;text-align:center;
        background:
          radial-gradient(circle at 50% 38%,rgba(232,205,147,.14),transparent 62%),
          repeating-linear-gradient(135deg,rgba(201,168,106,.05) 0 9px,transparent 9px 18px),
          linear-gradient(160deg,#2a1a17,#160e0d)}
.ab-tcard-mono .mi{display:grid;place-items:center;width:5.4rem;height:5.4rem;border-radius:50%;
        border:1px solid rgba(232,205,147,.4);color:var(--ab-gold2);
        font-family:var(--ab-serif);font-size:1.65rem;letter-spacing:.08em;
        box-shadow:0 0 34px rgba(201,168,106,.2),inset 0 0 22px rgba(201,168,106,.1)}
.ab-tcard-mono .mn{margin-top:.85rem;font-size:.53rem;letter-spacing:.3em;text-transform:uppercase;
        color:var(--ab-dim)}
.ab-tcard-body{padding:1.35rem 1.4rem 1.5rem;display:flex;flex-direction:column;flex:1}
.ab-tcard-name{margin:0;font-size:1.16rem;line-height:1.3;color:var(--ab-ink)}
.ab-tcard-role{margin:.42rem 0 0;font-size:.58rem;letter-spacing:.26em;text-transform:uppercase;
        color:var(--ab-gold)}
.ab-tcard-bio{margin:.95rem 0 0;color:var(--ab-mut);font-size:.9rem;line-height:1.78}
.ab-tcard-note{margin:.85rem 0 0;padding-top:.7rem;border-top:1px dashed rgba(201,168,106,.22);
        font-size:.62rem;letter-spacing:.14em;text-transform:uppercase;color:var(--ab-dim)}

/* ==========================================================================
   OUR VISION - full-bleed photographic band
   ========================================================================== */
.ab-vision{position:relative;padding:6.4rem 0;overflow:hidden;isolation:isolate;
        background:#0d0807}
@media(min-width:900px){.ab-vision{padding:8.6rem 0}}
.ab-vision-img{position:absolute;inset:0;z-index:-3;width:100%;height:100%;object-fit:cover;
        object-position:center 45%}
.ab-vision-ov{position:absolute;inset:0;z-index:-2;pointer-events:none;background:
        linear-gradient(180deg,rgba(13,8,7,.93),rgba(20,10,10,.78) 46%,rgba(13,8,7,.95)),
        radial-gradient(ellipse at 50% 50%,rgba(84,34,42,.26),rgba(10,6,6,.9) 78%)}
.ab-vision-in{position:relative;z-index:2;max-width:50rem;margin:0 auto;text-align:center;
        padding:0 1.25rem}
.ab-vision-h{margin:1.5rem 0 0;font-size:clamp(1.6rem,5vw,2.85rem);line-height:1.22;color:#fbf5ea}
.ab-vision-lead{margin:1.7rem auto 0;max-width:40rem;font-family:var(--ab-serif);font-style:italic;
        font-size:clamp(1.06rem,2.7vw,1.42rem);line-height:1.6;color:var(--ab-gold2)}
.ab-vision-p{margin:1.5rem auto 0;max-width:41rem;color:#ded2c2;font-size:1rem;line-height:1.9}

/* ==========================================================================
   PHILOSOPHY - four points
   ========================================================================== */
.ab-phil-grid{display:grid;gap:1.3rem;grid-template-columns:1fr}
@media(min-width:620px){.ab-phil-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.ab-phil-grid{grid-template-columns:repeat(4,1fr);gap:1.5rem}}
.ab-pcard{position:relative;padding:2rem 1.5rem 1.85rem;border-radius:14px;
        border:1px solid var(--ab-line-soft);background:linear-gradient(180deg,var(--ab-panel),#190f0e);
        box-shadow:0 20px 48px -34px rgba(0,0,0,.9);overflow:hidden;
        transition:transform .45s cubic-bezier(.22,.61,.36,1),border-color .45s ease}
.ab-pcard::before{content:"";position:absolute;top:0;left:0;right:0;height:1px;
        background:linear-gradient(90deg,transparent,var(--ab-gold),transparent);
        transform:scaleX(.28);transform-origin:center;opacity:.7;
        transition:transform .5s ease,opacity .5s ease}
.ab-pcard:hover{transform:translateY(-5px);border-color:rgba(201,168,106,.3)}
.ab-pcard:hover::before{transform:scaleX(1);opacity:1}
.ab-pcard-ico{display:grid;place-items:center;width:2.9rem;height:2.9rem;border-radius:50%;
        border:1px solid rgba(201,168,106,.34);color:var(--ab-gold2);margin-bottom:1.15rem}
.ab-pcard-ico svg{width:1.25rem;height:1.25rem}
.ab-pcard h3{margin:0;font-size:1.1rem;line-height:1.35;color:var(--ab-ink)}
.ab-pcard p{margin:.7rem 0 0;color:var(--ab-mut);font-size:.9rem;line-height:1.78}

/* ==========================================================================
   HOW WE WORK - five-step timeline
   ========================================================================== */
.ab-steps{position:relative;display:grid;gap:1.6rem;grid-template-columns:1fr;margin-top:.5rem}
@media(min-width:760px){.ab-steps{grid-template-columns:repeat(5,1fr);gap:1.1rem}}
/* the connecting gold line only exists once the steps sit in one row */
@media(min-width:760px){
  .ab-steps::before{content:"";position:absolute;left:8%;right:8%;top:1.55rem;height:1px;
        background:linear-gradient(90deg,transparent,rgba(201,168,106,.5),transparent)}
}
.ab-step{position:relative;text-align:center;padding:0 .4rem}
.ab-step-num{position:relative;z-index:2;display:grid;place-items:center;width:3.1rem;height:3.1rem;
        margin:0 auto 1.15rem;border-radius:50%;font-family:var(--ab-serif);font-size:1.02rem;
        letter-spacing:.06em;color:var(--ab-gold2);background:#170e0d;
        border:1px solid rgba(201,168,106,.42);box-shadow:0 0 26px rgba(201,168,106,.18)}
.ab-step h3{margin:0;font-size:1.06rem;letter-spacing:.06em;color:var(--ab-ink)}
.ab-step p{margin:.6rem 0 0;color:var(--ab-mut);font-size:.87rem;line-height:1.75}

/* ==========================================================================
   MORE THAN PHOTOGRAPHY
   ========================================================================== */
.ab-more-grid{display:grid;gap:.95rem;grid-template-columns:repeat(2,1fr)}
@media(min-width:700px){.ab-more-grid{grid-template-columns:repeat(3,1fr)}}
@media(min-width:1024px){.ab-more-grid{grid-template-columns:repeat(5,1fr);gap:1.05rem}}
.ab-mcard{position:relative;display:flex;flex-direction:column;align-items:center;text-align:center;
        gap:.75rem;padding:1.5rem .8rem 1.35rem;border-radius:13px;
        border:1px solid var(--ab-line-soft);background:linear-gradient(180deg,var(--ab-panel),#170e0d);
        box-shadow:0 18px 44px -32px rgba(0,0,0,.9);overflow:hidden;
        transition:transform .45s cubic-bezier(.22,.61,.36,1),border-color .45s ease,box-shadow .45s ease}
/* the slow sheen that runs across each card on hover */
.ab-mcard::before{content:"";position:absolute;top:0;left:-60%;width:60%;height:100%;
        background:linear-gradient(90deg,transparent,rgba(232,205,147,.14),transparent);
        transform:skewX(-18deg);transition:left .75s cubic-bezier(.22,.61,.36,1)}
.ab-mcard:hover{transform:translateY(-6px);border-color:rgba(232,205,147,.4);
        box-shadow:0 30px 64px -32px rgba(0,0,0,.95)}
.ab-mcard:hover::before{left:130%}
.ab-mcard-ico{position:relative;display:grid;place-items:center;width:2.85rem;height:2.85rem;
        border-radius:50%;color:var(--ab-gold2);border:1px solid rgba(201,168,106,.3);
        background:radial-gradient(circle at 50% 30%,rgba(232,205,147,.16),transparent 70%);
        transition:transform .5s cubic-bezier(.22,.61,.36,1),box-shadow .5s ease}
.ab-mcard:hover .ab-mcard-ico{transform:translateY(-3px) scale(1.06);
        box-shadow:0 0 30px rgba(232,205,147,.3)}
.ab-mcard-ico svg{width:1.2rem;height:1.2rem}
.ab-mcard span{position:relative;font-size:.68rem;letter-spacing:.16em;text-transform:uppercase;
        line-height:1.55;color:var(--ab-ink)}

/* ==========================================================================
   BEHIND THE SCENES
   ========================================================================== */
.ab-bts{display:grid;gap:1rem;grid-template-columns:repeat(2,1fr);grid-auto-rows:9.5rem}
@media(min-width:900px){
  .ab-bts{grid-template-columns:repeat(4,1fr);grid-auto-rows:11rem;gap:1.1rem}
}
.ab-bts figure{position:relative;margin:0;overflow:hidden;border-radius:12px;
        border:1px solid var(--ab-line-soft);background:var(--ab-panel)}
.ab-bts img{width:100%;height:100%;object-fit:cover;transition:transform .9s cubic-bezier(.22,.61,.36,1)}
.ab-bts figure:hover img{transform:scale(1.06)}
.ab-bts figcaption{position:absolute;inset:auto 0 0 0;padding:1.9rem .9rem .8rem;
        font-size:.56rem;letter-spacing:.24em;text-transform:uppercase;color:#e8dcc9;
        background:linear-gradient(180deg,transparent,rgba(13,8,7,.88))}
/* the lead shot spans two columns on desktop */
@media(min-width:900px){.ab-bts .ab-bts-lead{grid-column:span 2;grid-row:span 2}}
.ab-bts-note{margin:1.6rem auto 0;max-width:42rem;text-align:center;color:var(--ab-dim);
        font-size:.82rem;line-height:1.8}

/* ==========================================================================
   OUR PROMISE
   ========================================================================== */
.ab-prom-grid{display:grid;gap:1.3rem;grid-template-columns:1fr}
@media(min-width:620px){.ab-prom-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.ab-prom-grid{grid-template-columns:repeat(4,1fr);gap:1.5rem}}
.ab-promise{position:relative;padding:1.85rem 1.45rem;border-radius:14px;
        border:1px solid var(--ab-line-soft);background:var(--ab-panel);
        box-shadow:0 20px 48px -34px rgba(0,0,0,.9);
        transition:transform .45s cubic-bezier(.22,.61,.36,1),border-color .45s ease}
.ab-promise:hover{transform:translateY(-5px);border-color:rgba(201,168,106,.32)}
.ab-promise-n{font-family:var(--ab-serif);font-size:.78rem;letter-spacing:.14em;
        color:var(--ab-gold);opacity:.85}
.ab-promise h3{margin:.7rem 0 0;font-size:1.08rem;color:var(--ab-ink)}
.ab-promise p{margin:.6rem 0 0;color:var(--ab-mut);font-size:.9rem;line-height:1.75}

/* ==========================================================================
   TRUST STRIP - the site's Google-review presentation.
   The link is the studio's own existing Google Maps reviews URL (the same one
   index.html already uses); the existing review system is not modified.
   ========================================================================== */
.ab-trust{position:relative;margin:0 auto;max-width:44rem;padding:2rem 1.6rem;border-radius:16px;
        display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:1.1rem 2rem;
        border:1px solid rgba(201,168,106,.28);text-align:center;
        background:
          radial-gradient(ellipse at 50% 0%,rgba(232,205,147,.1),transparent 70%),
          linear-gradient(180deg,var(--ab-panel),#180f0e);
        box-shadow:0 30px 70px -36px rgba(0,0,0,.95)}
.ab-stars{display:flex;gap:.28rem;color:var(--ab-gold2)}
.ab-stars svg{width:1.22rem;height:1.22rem;fill:currentColor}
.ab-trust-score{font-family:var(--ab-serif);font-size:2.1rem;line-height:1;color:var(--ab-gold2);
        position:relative;top:.1rem}
.ab-trust-meta{display:flex;flex-direction:column;gap:.2rem;text-align:left}
.ab-trust-count{margin:0;font-size:1rem;font-weight:500;color:var(--ab-ink)}
.ab-trust-name{margin:0;font-size:.62rem;letter-spacing:.26em;text-transform:uppercase;color:var(--ab-dim)}

/* ==========================================================================
   FINAL CTA
   ========================================================================== */
.ab-cta{position:relative;padding:6.2rem 0 7rem;overflow:hidden;isolation:isolate;
        text-align:center;background:#0d0807}
@media(min-width:900px){.ab-cta{padding:8rem 0 8.6rem}}
.ab-cta-ov{position:absolute;inset:0;z-index:-2;pointer-events:none;background:
        linear-gradient(180deg,rgba(13,8,7,.94),rgba(30,14,15,.72) 50%,rgba(13,8,7,.97)),
        radial-gradient(ellipse at 50% 58%,rgba(84,34,42,.42),transparent 70%)}
.ab-cta-in{position:relative;z-index:2;max-width:46rem;margin:0 auto;padding:0 1.25rem}
.ab-cta-h{margin:1.5rem 0 0;font-size:clamp(1.65rem,5.4vw,3.05rem);line-height:1.2;color:#fbf5ea}
.ab-cta-p{margin:1.5rem auto 0;max-width:34rem;color:#ded2c2;font-size:1.02rem;line-height:1.85}
.ab-cta-btns{display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center;margin-top:2.5rem}

/* ==========================================================================
   FOOTER
   ========================================================================== */
footer{text-align:center;padding:2.2rem 1rem;color:#6d6156;font-size:.85rem;
  border-top:1px solid var(--ab-line-soft);background:#100a09}
.ab-social{display:flex;gap:.7rem;justify-content:center;margin:0 0 1.1rem}
.ab-social a{width:2.4rem;height:2.4rem;border-radius:999px;display:inline-flex;
  align-items:center;justify-content:center;border:1px solid rgba(201,168,106,.32);
  color:#8d7f71;transition:all .3s}
.ab-social a:hover{color:#100a09;background:var(--ab-gold);border-color:var(--ab-gold);
  transform:translateY(-2px);box-shadow:0 0 18px rgba(201,168,106,.4)}
.ab-social svg{width:1.05rem;height:1.05rem;fill:currentColor}

/* ==========================================================================
   RESPONSIVE
   ========================================================================== */
@media(max-width:900px){
  .ab-sec{padding:4.2rem 0}
  .ab-hero-in{padding-top:calc(var(--ab-head) + 2.4rem);padding-bottom:5.4rem}
  .ab-founder-photo img{aspect-ratio:3/4}
}
@media(max-width:640px){
  .ab-story-quote{padding:1.15rem 1.15rem}
  .ab-story-pull{padding-left:1.15rem}
  .ab-close{padding:1.6rem 1.3rem}
  .ab-close::before{inset:.4rem}
  .ab-hero-cta .ab-btn{width:100%;max-width:20rem}
  .ab-cta-btns .ab-btn{width:100%;max-width:20rem}
  .ab-bts{grid-auto-rows:7.5rem;gap:.8rem}
  .ab-trust{flex-direction:column;padding:1.7rem 1.2rem}
  .ab-trust-meta{text-align:center;align-items:center}
  .ab-more-grid{grid-template-columns:repeat(2,1fr);gap:.8rem}
  .ab-mcard{padding:1.2rem .5rem 1.1rem}
  .ab-mcard span{font-size:.6rem;letter-spacing:.1em}
}
@media(max-width:380px){
  .rbrand-name{font-size:1.08rem;letter-spacing:.1em}
  .rbrand-sub{font-size:.48rem;letter-spacing:.24em}
  .ab-hero-eyebrow{font-size:.53rem;letter-spacing:.32em}
  .ab-more-grid{grid-template-columns:1fr}
}
@media(prefers-reduced-motion:reduce){
  .ab-reveal{opacity:1;transform:none;transition:none}
  .ab-tcard,.ab-pcard,.ab-mcard,.ab-promise,.ab-btn,.ab-mcard-ico{transition:none}
  .ab-tcard:hover img,.ab-tcard:hover,.ab-pcard:hover,.ab-mcard:hover,
  .ab-promise:hover,.ab-btn:hover,.ab-mcard:hover .ab-mcard-ico{transform:none}
  .ab-bts figure:hover img{transform:none}
}
</style>
<script>document.documentElement.classList.add('ab-js');</script>
<?php ds_contact_head(); ?>
</head>
<body>

<!-- ======================= NAVBAR ======================= -->
<header class="rnav" id="rnav">
  <div class="rnav-in">
    <a href="index.html" class="rbrand">
      <span class="rbrand-name">Deepak Studios</span>
      <span class="rbrand-sub">Wedding Photography</span>
    </a>
    <nav class="rnav-desk" aria-label="Primary">
      <a href="index.html">Home</a>
      <a href="photography.php">Photography</a>
      <a href="cinematography.php">Cinematography</a>
      <a href="reels.php">Reels</a>
      <a href="about.php" class="on" aria-current="page">About Us</a>
      <?php ds_contact_header_call(); ?>
    </nav>
    <button type="button" class="rburger" id="rburger" aria-label="Open menu"
            aria-expanded="false" aria-controls="rmobmenu">
      <span></span><span></span><span></span>
    </button>
  </div>
  <div class="rmobmenu" id="rmobmenu">
    <a href="index.html">Home</a>
    <a href="photography.php">Photography</a>
    <a href="cinematography.php">Cinematography</a>
    <a href="reels.php">Reels</a>
    <a href="about.php" class="on" aria-current="page">About Us</a>
  </div>
</header>

<main>

<!-- ======================= HERO ======================= -->
<section class="ab-hero" aria-labelledby="abHeroH">
  <img class="ab-hero-img" src="<?= $esc($abHeroImage) ?>"
       alt="A Deepak Studios wedding photograph" width="1920" height="1080"
       fetchpriority="high" decoding="async">
  <span class="ab-hero-ov" aria-hidden="true"></span>
  <span class="ab-grain" aria-hidden="true"></span>
  <div class="ab-hero-in">
    <p class="ab-hero-eyebrow">About Deepak Studios</p>
    <h1 class="ab-hero-h" id="abHeroH">We Don&rsquo;t Just Capture Moments. <em>We Preserve Them.</em></h1>
    <span class="ab-rule" aria-hidden="true"></span>
    <p class="ab-hero-sub">Behind every frame from Deepak Studios is a relationship, not just a
      shutter click. We are a wedding photography and cinematography studio from Bokaro, Jharkhand,
      built on one simple belief &mdash; that a photograph should give back the feeling of the day,
      not only the record of it.</p>
    <div class="ab-hero-cta">
      <a class="ab-btn ab-btn-gold" href="index.html#contact">Book Your Date</a>
      <a class="ab-btn ab-btn-ghost" id="abHeroStory" href="#story">Our Story</a>
    </div>
  </div>
  <p class="ab-hero-scroll" aria-hidden="true">Scroll <i>&#8595;</i></p>
</section>

<!-- ======================= OUR STORY ======================= -->
<section class="ab-sec" id="story" aria-labelledby="abStoryH">
  <div class="ab-shell">
    <div class="ab-sec-head ab-reveal">
      <p class="ab-eyebrow">Our Story</p>
      <span class="ab-rule" aria-hidden="true"></span>
      <h2 class="ab-h2" id="abStoryH">It Started With a Camera. <em>It Grew With Love.</em></h2>
    </div>

    <div class="ab-story ab-reveal">
      <div class="ab-story-body">
        <p>Deepak Studios ki kahani sirf photography se shuru nahi hui thi&mdash;ye shuru hui thi
          logon ke pyaar, bharose aur apnapan se.</p>
        <p>Shuruaat mein humara kaam tha khoobsurat moments ko camera mein capture karna. Lekin
          jaise-jaise humne families ke saath waqt bitaya, unki khushiyon ka hissa bane aur unke
          special moments ko apni aankhon se dekha, humare liye photography ka matlab bhi badalta
          gaya.</p>
        <p>Logon ka pyaar milta gaya, aur Deepak Studios aage badhta gaya.</p>
        <p>Har happy client ka feedback humare liye sirf ek review nahi tha&mdash;it was a reminder
          that we were moving in the right direction. Har event ke baad milne wali appreciation ne
          humari team ke junoon aur lagan ko aur strong kiya.</p>
        <p>Humara aim kabhi sirf ek normal photograph dena nahi tha.</p>
        <p>Hum chahte the ki saalon baad jab koi apni photographs dekhe, toh use sirf ye yaad na aaye
          ki us din kya hua tha, balki woh tasveer dekhte hi us pal ki hansi, khushi, emotions aur
          apnapan phir se mehsoos kar sake.</p>
        <p>Aur us moment ko yaad karte hue, unhe woh photographer bhi yaad aaye&mdash;jisne us khushi
          ko usi waqt mehsoos karke, us ek perfect frame mein hamesha ke liye sambhal liya tha.</p>

        <p class="ab-story-pull">Isi soch ke saath humara ek aur sapna tha:</p>

        <div class="ab-story-quote">
          <p>&ldquo;Log humein sirf apna photographer na samjhein, balki apne ghar ke ek apne family
            member ki tarah samjhein.&rdquo;</p>
        </div>

        <p>Isliye humne sirf photographs lene par focus nahi kiya. Humne relationships banane par
          focus kiya&mdash;clients ko comfortable feel karana, unki needs samajhna, unke celebrations
          mein genuinely involved rehna aur har important moment ke liye poori dedication ke saath
          khade rehna.</p>
        <p>Aaj jab hum peeche mudkar dekhte hain, toh humein sirf photographs, albums aur films nahi
          dikhte. Humein un families ke chehre, unki khushiyan, unka trust aur woh countless memories
          dikhai deti hain jinka humein hissa banne ka mauka mila.</p>
        <p>Aur shayad isi wajah se Deepak Studios ka ye karwaan dheere-dheere badhta gaya&mdash;ek
          client se doosre client tak, ek family se doosri family tak, aur ek beautiful story se
          doosri story tak.</p>
      </div>

      <div class="ab-close">
        <p>&ldquo;Aisi tasveerein banana, jinhe dekhkar yaadein sirf yaad na aayein &mdash; balki phir
          se jeeti hui mehsoos hon.&rdquo;</p>
      </div>
    </div>
  </div>
</section>

<!-- ======================= FOUNDER ======================= -->
<section class="ab-sec ab-band" aria-labelledby="abFounderH">
  <div class="ab-shell">
    <div class="ab-sec-head ab-reveal">
      <p class="ab-eyebrow">Founder</p>
      <span class="ab-rule" aria-hidden="true"></span>
      <h2 class="ab-h2" id="abFounderH">The Person Behind <em>Deepak Studios</em></h2>
    </div>

    <div class="ab-founder ab-reveal">
      <figure class="ab-founder-photo">
        <img src="<?= $esc($abFounderImage) ?>"
             alt="Deepak Modak, founder and lead photographer of Deepak Studios"
             width="1066" height="1599" loading="lazy" decoding="async">
        <span class="ab-founder-frame" aria-hidden="true"></span>
        <figcaption class="ab-founder-tag">Founder &amp; Lead Photographer</figcaption>
      </figure>

      <div>
        <h3 class="ab-founder-name">Deepak Modak</h3>
        <p class="ab-founder-role">Founder &amp; Lead Photographer</p>
        <div class="ab-founder-bio">
          <p>Hi, I&rsquo;m Deepak Modak, the founder and creative eye behind Deepak Studios.</p>
          <p>For me, photography is more than just capturing beautiful pictures&mdash;it&rsquo;s about
            preserving the emotions, connections, and little moments that make every celebration
            truly yours.</p>
          <p>With a passion for wedding and event photography, I focus on capturing genuine emotions,
            vibrant celebrations, intimate candid moments, and the details that often go unnoticed.
            Every couple and every family has a different story, and my goal is to document that story
            in a way that feels authentic, cinematic, and timeless.</p>
          <p>At Deepak Studios, we believe that years from now, your photographs should do more than
            remind you of how the day looked&mdash;they should bring back how it felt.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ======================= OUR TEAM ======================= -->
<section class="ab-sec" aria-labelledby="abTeamH">
  <div class="ab-shell">
    <div class="ab-sec-head ab-reveal">
      <p class="ab-eyebrow">Our Team</p>
      <span class="ab-rule" aria-hidden="true"></span>
      <h2 class="ab-h2" id="abTeamH">The People Behind <em>Every Frame</em></h2>
      <p class="ab-sup">A small, dedicated team &mdash; each person behind a different kind of eye.</p>
    </div>

    <div class="ab-team-grid">
      <?php foreach ($TEAM as $i => $m):
          $hasPhoto = $abHasPhoto($m['photo']);
          $num = str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT);
      ?>
      <article class="ab-tcard ab-reveal">
        <div class="ab-tcard-photo">
          <?php if ($hasPhoto): ?>
            <img src="photos/Team/<?= $esc($m['photo']) ?>" alt="<?= $esc(strip_tags($m['name'])) ?>"
                 class="<?= $esc(pathinfo($m['photo'], PATHINFO_FILENAME)) ?>"
                 loading="lazy" decoding="async">
          <?php else: ?>
            <div class="ab-tcard-mono" role="img"
                 aria-label="Photograph of <?= $esc(strip_tags($m['name'])) ?> coming soon">
              <span class="mi"><?= $esc($m['mono']) ?></span>
              <span class="mn">Photo coming soon</span>
            </div>
          <?php endif; ?>
          <span class="ab-tcard-shade" aria-hidden="true"></span>
          <span class="ab-tcard-num" aria-hidden="true"><?= $num ?></span>
        </div>
        <div class="ab-tcard-body">
          <h3 class="ab-tcard-name"><?= $m['name'] ?></h3>
          <p class="ab-tcard-role"><?= $m['role'] ?></p>
          <p class="ab-tcard-bio"><?= $m['bio'] ?></p>
          <?php if (!$hasPhoto): ?>
            <p class="ab-tcard-note">Photograph to be added</p>
          <?php endif; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======================= OUR VISION ======================= -->
<section class="ab-vision" aria-labelledby="abVisionH">
  <?php if ($abHasAsset($abVisionImage)): ?>
    <img class="ab-vision-img" src="<?= $esc($abVisionImage) ?>"
         alt="" aria-hidden="true" loading="lazy" decoding="async">
  <?php endif; ?>
  <span class="ab-vision-ov" aria-hidden="true"></span>
  <span class="ab-grain" aria-hidden="true"></span>
  <div class="ab-vision-in">
    <p class="ab-eyebrow">Our Vision</p>
    <h2 class="ab-vision-h" id="abVisionH">Our Vision</h2>
    <span class="ab-rule" aria-hidden="true"></span>
    <p class="ab-vision-lead">&ldquo;To create timeless visual stories that let people relive their
      most beautiful moments for years to come.&rdquo;</p>
    <p class="ab-vision-p">This is what every photograph and every film we make is measured against.
      A frame can record how a moment looked, but our work goes one step further &mdash; it is meant
      to hold how that moment <em>felt</em>. The laugh that could not be staged, the quiet glance
      nobody noticed at the time, the warmth of a room full of people who love each other: those are
      the things we are really trying to keep. Decades from now, when someone opens an album from
      Deepak Studios, we want them to feel the day again &mdash; not simply recognise it.</p>
  </div>
</section>

<!-- ======================= OUR PHILOSOPHY ======================= -->
<section class="ab-sec" aria-labelledby="abPhilH">
  <div class="ab-shell">
    <div class="ab-sec-head ab-reveal">
      <p class="ab-eyebrow">Our Philosophy</p>
      <span class="ab-rule" aria-hidden="true"></span>
      <h2 class="ab-h2" id="abPhilH">What We <em>Believe In</em></h2>
    </div>

    <div class="ab-phil-grid">
      <article class="ab-pcard ab-reveal">
        <span class="ab-pcard-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.7s-3.6 3.9-3.6 7a3.6 3.6 0 0 0 7.2 0c0-3.1-3.6-7-3.6-7Z"/></svg>
        </span>
        <h3>Real Emotions</h3>
        <p>We believe the most beautiful photographs often come from genuine, unscripted moments.</p>
      </article>
      <article class="ab-pcard ab-reveal">
        <span class="ab-pcard-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="12" cy="12" r="3.4"/><path d="M8.5 5 10 2.6M15.5 5 14 2.6"/></svg>
        </span>
        <h3>Authentic Moments</h3>
        <p>We capture your celebration as it truly happens, while being ready for the moments that
          cannot be planned.</p>
      </article>
      <article class="ab-pcard ab-reveal">
        <span class="ab-pcard-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 6.8V12l3.4 2"/></svg>
        </span>
        <h3>Timeless Visuals</h3>
        <p>Our goal is to create photographs and films that continue to feel meaningful years from now.</p>
      </article>
      <article class="ab-pcard ab-reveal">
        <span class="ab-pcard-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3.2 13.9 9l5.9.2-4.7 3.4 1.7 5.7L12 15.4 7.2 18.3l1.7-5.7L4.2 9.2 10.1 9Z"/></svg>
        </span>
        <h3>Attention to Every Detail</h3>
        <p>From the smallest detail to the biggest celebration, every frame deserves care.</p>
      </article>
    </div>
  </div>
</section>

<!-- ======================= HOW WE WORK ======================= -->
<section class="ab-sec ab-band" aria-labelledby="abWorkH">
  <div class="ab-shell">
    <div class="ab-sec-head ab-reveal">
      <p class="ab-eyebrow">How We Work</p>
      <span class="ab-rule" aria-hidden="true"></span>
      <h2 class="ab-h2" id="abWorkH">From First Call to <em>Final Album</em></h2>
    </div>

    <div class="ab-steps ab-reveal">
      <div class="ab-step">
        <span class="ab-step-num">01</span>
        <h3>Understand</h3>
        <p>We understand your event, expectations and requirements.</p>
      </div>
      <div class="ab-step">
        <span class="ab-step-num">02</span>
        <h3>Plan</h3>
        <p>We coordinate the team, timeline, locations and coverage.</p>
      </div>
      <div class="ab-step">
        <span class="ab-step-num">03</span>
        <h3>Capture</h3>
        <p>Photography, cinematography, candid moments and aerial perspectives come together.</p>
      </div>
      <div class="ab-step">
        <span class="ab-step-num">04</span>
        <h3>Craft</h3>
        <p>Professional editing, colour grading and cinematic finishing.</p>
      </div>
      <div class="ab-step">
        <span class="ab-step-num">05</span>
        <h3>Deliver</h3>
        <p>Albums, films and final memories delivered with care and attention to timelines.</p>
      </div>
    </div>
  </div>
</section>

<!-- ======================= MORE THAN PHOTOGRAPHY ======================= -->
<section class="ab-sec" aria-labelledby="abMoreH">
  <div class="ab-shell">
    <div class="ab-sec-head ab-reveal">
      <p class="ab-eyebrow">More Than Photography</p>
      <span class="ab-rule" aria-hidden="true"></span>
      <h2 class="ab-h2" id="abMoreH">Everything We <em>Bring To Your Event</em></h2>
      <p class="ab-sup">One team, one point of contact &mdash; and every element of your day covered.</p>
    </div>

    <div class="ab-more-grid">
      <?php
      $AB_MORE = [
        ['Wedding Photography', '<path d="M3 7.5h18v12H3z"/><circle cx="12" cy="13.5" r="3.6"/><path d="M8.6 7.5 10.2 5h3.6l1.6 2.5"/>'],
        ['Cinematography',    '<rect x="2.5" y="6" width="14" height="12" rx="2"/><path d="m16.5 11 5-3v8l-5-3Z"/>'],
        ['Reels',             '<rect x="6" y="2.6" width="12" height="18.8" rx="2.4"/><path d="M10.4 18.4h3.2"/>'],
        ['Drone',             '<path d="m12 3 8 4.5-3 8.5H7l-3-8.5Z"/><path d="M9.6 16h4.8"/><path d="M12 3v4"/>'],
        ['Wedding Live Telecast', '<circle cx="12" cy="12" r="2.6"/><path d="M8.4 8.4a5 5 0 0 0 0 7.2M15.6 15.6a5 5 0 0 0 0-7.2"/><path d="M5.6 5.6a9 9 0 0 0 0 12.8M18.4 18.4a9 9 0 0 0 0-12.8"/>'],
        ['55" LED TV',        '<rect x="2.6" y="4" width="18.8" height="12.4" rx="1.8"/><path d="M8.5 20.4h7M12 16.4v4"/>'],
        ['LED Wall',          '<rect x="2.4" y="3.4" width="19.2" height="10.4" rx="1.2"/><path d="M6 17.6h12M8 21h8M12 13.8v3.8"/><path d="M9.2 6.8h5.6M9.2 10.4h5.6"/>'],
        ['Standy',            '<rect x="4.4" y="3" width="15.2" height="18" rx="1.4"/><path d="M8.4 7.4h7.2M8.4 11h7.2M8.4 14.6h4.4"/>'],
        ['Pre-Wedding Photo Walkway', '<path d="M4.4 20.6 9 4.2l3 16.4 3-16.4 4.6 16.4"/>'],
        ['Event Photography', '<path d="M12 3.4 14.6 9l6.2.7-4.6 4.2 1.3 6.1L12 16.9 6.5 20l1.3-6.1L3.2 9.7 9.4 9Z"/>'],
      ];
      foreach ($AB_MORE as [$label, $paths]):
      ?>
      <div class="ab-mcard ab-reveal">
        <span class="ab-mcard-ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
               stroke-linecap="round" stroke-linejoin="round"><?= $paths ?></svg>
        </span>
        <span><?= $esc($label) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ======================= BEHIND THE SCENES ======================= -->
<section class="ab-sec ab-band" aria-labelledby="abBtsH">
  <div class="ab-shell">
    <div class="ab-sec-head ab-reveal">
      <p class="ab-eyebrow">Behind The Scenes</p>
      <span class="ab-rule" aria-hidden="true"></span>
      <h2 class="ab-h2" id="abBtsH">Behind Every Beautiful Frame Is a <em>Dedicated Team.</em></h2>
    </div>

    <div class="ab-bts ab-reveal">
      <?php if ($abHasAsset($abBtsLead)): ?>
      <figure class="ab-bts-lead">
        <img src="<?= $esc($abBtsLead) ?>" alt="The Deepak Studios team at work on an event"
             loading="lazy" decoding="async">
        <figcaption>Our Team On Location</figcaption>
      </figure>
      <?php endif; ?>
      <?php foreach ($abBtsThumbs as $t): if (!$abHasAsset($t)) { continue; } ?>
      <figure>
        <img src="<?= $esc($t) ?>" alt="Deepak Studios event photography" loading="lazy" decoding="async">
        <figcaption>Event Photography</figcaption>
      </figure>
      <?php endforeach; ?>
    </div>

    <p class="ab-bts-note">Every celebration is covered by a coordinated crew &mdash; photographers,
      a cinematographer working with a gimbal, and drone operators for the aerial perspective &mdash;
      so the same day is documented from every angle at once.</p>
  </div>
</section>

<!-- ======================= OUR PROMISE ======================= -->
<section class="ab-sec" aria-labelledby="abPromiseH">
  <div class="ab-shell">
    <div class="ab-sec-head ab-reveal">
      <p class="ab-eyebrow">Our Promise</p>
      <span class="ab-rule" aria-hidden="true"></span>
      <h2 class="ab-h2" id="abPromiseH">What We Promise <em>Every Client</em></h2>
    </div>

    <div class="ab-prom-grid">
      <article class="ab-promise ab-reveal">
        <p class="ab-promise-n">01</p>
        <h3>Be Present</h3>
        <p>We stay attentive to the moments that matter.</p>
      </article>
      <article class="ab-promise ab-reveal">
        <p class="ab-promise-n">02</p>
        <h3>Respect Your Time</h3>
        <p>We value planning, coordination and punctuality.</p>
      </article>
      <article class="ab-promise ab-reveal">
        <p class="ab-promise-n">03</p>
        <h3>Capture With Purpose</h3>
        <p>Every frame should have a reason to be remembered.</p>
      </article>
      <article class="ab-promise ab-reveal">
        <p class="ab-promise-n">04</p>
        <h3>Deliver With Care</h3>
        <p>Your final photographs, films and albums deserve the same attention as the day itself.</p>
      </article>
    </div>
  </div>
</section>

<!-- ======================= TRUST ======================= -->
<section class="ab-sec" style="padding-top:0" aria-label="Google reviews">
  <div class="ab-shell">
    <div class="ab-trust ab-reveal" role="img"
         aria-label="Deepak Studios is rated 4.9 stars from 249 Google reviews">
      <span class="ab-stars" aria-hidden="true">
        <svg viewBox="0 0 24 24"><path d="m12 2.6 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.5 6.1 20.6l1.2-6.5L2.5 9.5l6.6-.9Z"/></svg>
        <svg viewBox="0 0 24 24"><path d="m12 2.6 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.5 6.1 20.6l1.2-6.5L2.5 9.5l6.6-.9Z"/></svg>
        <svg viewBox="0 0 24 24"><path d="m12 2.6 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.5 6.1 20.6l1.2-6.5L2.5 9.5l6.6-.9Z"/></svg>
        <svg viewBox="0 0 24 24"><path d="m12 2.6 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.5 6.1 20.6l1.2-6.5L2.5 9.5l6.6-.9Z"/></svg>
        <svg viewBox="0 0 24 24"><path d="m12 2.6 2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.5 6.1 20.6l1.2-6.5L2.5 9.5l6.6-.9Z"/></svg>
      </span>
      <span class="ab-trust-score">4.9</span>
      <span class="ab-trust-meta">
        <span class="ab-trust-count">249+ Google Reviews</span>
        <span class="ab-trust-name">Deepak Studios</span>
      </span>
    </div>
  </div>
</section>

<!-- ======================= FINAL CTA ======================= -->
<section class="ab-cta" aria-labelledby="abCtaH">
  <span class="ab-cta-ov" aria-hidden="true"></span>
  <span class="ab-grain" aria-hidden="true"></span>
  <div class="ab-cta-in">
    <p class="ab-eyebrow">Let's Begin</p>
    <h2 class="ab-cta-h" id="abCtaH">Your Story Deserves to Be <em>Remembered Beautifully.</em></h2>
    <span class="ab-rule" aria-hidden="true"></span>
    <p class="ab-cta-p">Let&rsquo;s create photographs and films that bring your favourite moments
      back to life.</p>
    <div class="ab-cta-btns">
      <a class="ab-btn ab-btn-gold" href="index.html#contact">Book Your Date</a>
      <a class="ab-btn ab-btn-ghost" href="wedding.php">View Our Work</a>
    </div>
  </div>
</section>

</main>

<!-- ======================= FOOTER ======================= -->
<footer>
  <div class="ab-social">
    <a href="https://www.instagram.com/deepakstudiosofficial_" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-.9-.4-.4-.7-.8-.9-1.4-.2-.4-.4-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.3-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4C8.4 2.2 8.8 2.2 12 2.2Zm0 1.8c-3.1 0-3.5 0-4.8.1-1.1.1-1.7.2-2.1.4-.5.2-.9.4-1.2.8-.4.3-.6.7-.8 1.2-.2.4-.3 1-.4 2.1C2.6 8.5 2.6 8.9 2.6 12s0 3.5.1 4.8c.1 1.1.2 1.7.4 2.1.2.5.4.9.8 1.2.3.4.7.6 1.2.8.4.2 1 .3 2.1.4 1.3.1 1.7.1 4.8.1s3.5 0 4.8-.1c1.1-.1 1.7-.2 2.1-.4.5-.2.9-.4 1.2-.8.4-.3.6-.7.8-1.2.2-.4.3-1 .4-2.1.1-1.3.1-1.7.1-4.8s0-3.5-.1-4.8c-.1-1.1-.2-1.7-.4-2.1a3.2 3.2 0 0 0-.8-1.2 3.2 3.2 0 0 0-1.2-.8c-.4-.2-1-.3-2.1-.4C15.5 4 15.1 4 12 4Zm0 3a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.8a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4Zm5.2-3.1a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4Z"/></svg>
    </a>
    <a href="https://www.facebook.com/deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H7.9V12h2.5V9.8c0-2.5 1.5-3.9 3.8-3.9 1.1 0 2.2.2 2.2.2v2.5h-1.3c-1.2 0-1.6.8-1.6 1.6V12h2.8l-.4 2.9h-2.4v7A10 10 0 0 0 22 12Z"/></svg>
    </a>
    <a href="https://www.youtube.com/@deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="YouTube">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23 12s0-3.8-.5-5.6a2.9 2.9 0 0 0-2-2C18.6 4 12 4 12 4s-6.6 0-8.5.4a2.9 2.9 0 0 0-2 2C1 8.2 1 12 1 12s0 3.8.5 5.6a2.9 2.9 0 0 0 2 2C5.4 20 12 20 12 20s6.6 0 8.5-.4a2.9 2.9 0 0 0 2-2C23 15.8 23 12 23 12ZM9.8 15.3V8.7l5.8 3.3Z"/></svg>
    </a>
  </div>
  <p>&copy; <?= date('Y') ?> Deepak Studios &middot; Wedding Photography &amp; Cinematography &middot; Bokaro, Jharkhand</p>
</footer>

<?php ds_contact_fab(); ?>

<script>
(function () {
  'use strict';

  /* ---- fixed navbar: gains its blurred background once the page scrolls ---- */
  var nav    = document.getElementById('rnav');
  var burger = document.getElementById('rburger');
  var menu   = document.getElementById('rmobmenu');

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

  /* ---- smooth-scroll the hero's "Our Story" button (same pattern as the other pages) ---- */
  var storyBtn = document.getElementById('abHeroStory');
  if (storyBtn) {
    storyBtn.addEventListener('click', function (e) {
      var t = document.getElementById('story');
      if (!t) return;
      e.preventDefault();
      var top = t.getBoundingClientRect().top + (window.pageYOffset || 0) - 70;
      if ('scrollBehavior' in document.documentElement.style) {
        window.scrollTo({ top: top, behavior: 'smooth' });
      } else { window.scrollTo(0, top); }
    });
  }

  /* ---- scroll reveal ----------------------------------------------------
     Driven by plain scroll-position maths instead of IntersectionObserver, so no section
     can be stranded invisible by a browser that never fires an observer callback: a section
     is revealed as soon as it reaches the viewport, every time the page is scrolled.
     The short timer sweep after load is a safety net in case a scroll event is missed. */
  var items = document.querySelectorAll('.ab-reveal');
  if (items.length) {
    var revealPass = function () {
      var y = window.pageYOffset || 0;
      var limit = y + window.innerHeight * 0.92;
      Array.prototype.forEach.call(items, function (el) {
        if (el.classList.contains('in')) return;
        if (el.getBoundingClientRect().top + y <= limit) { el.classList.add('in'); }
      });
    };
    Array.prototype.forEach.call(items, function (el, i) {
      el.style.transitionDelay = (i % 4) * 90 + 'ms';
    });
    window.addEventListener('scroll', revealPass, { passive: true });
    window.addEventListener('resize', revealPass);
    revealPass();
    var sweeps = 0;
    var sweep = setInterval(function () {
      revealPass();
      if (++sweeps >= 15) { clearInterval(sweep); }
    }, 400);
  }
})();
</script>
</body>
</html>
