<?php
/**
 * about.php — About Deepak Studios.
 *
 * Standalone premium page (same luxury dark + gold language as reels.php /
 * cinematography.php). All styles below are self-contained (`ab-` prefix for
 * page sections; the `.rnav` navbar block mirrors the other premium pages).
 * Shared Call Now pill + WhatsApp/mobile bar come from lib_contact.php.
 */
declare(strict_types=1);

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
<title>About Us | Deepak Studios</title>
<meta name="description" content="About Deepak Studios — wedding photography and cinematography team preserving emotions, stories and timeless memories.">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
<?php ds_contact_head(); ?>
<style>
:root{
  --bg:#0b0b0c; --panel:#141416; --card:#18181a; --line:#26262a;
  --ink:#f2ecdf; --mut:#a8a294; --gold:#c9a86a; --gold2:#e6c980;
  --serif:Georgia,"Times New Roman",serif;
  --rx-shell:1200px;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);
  font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;
  -webkit-font-smoothing:antialiased;overflow-x:hidden}
img{max-width:100%}
a{color:var(--gold);text-decoration:none}
::selection{background:rgba(201,168,106,.3);color:var(--gold2)}
.pf{font-family:"Playfair Display",Georgia,serif}

/* ---------- premium navbar (same as reels.php / cinematography.php) ---------- */
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
@media (max-width:380px){
  .rbrand-name{font-size:1.08rem;letter-spacing:.1em}
  .rbrand-sub{font-size:.48rem;letter-spacing:.24em}
}

/* ---------- shared section shell ---------- */
.ab-sec{padding:4.5rem 0;position:relative;overflow:hidden}
.ab-wrap{max-width:1120px;margin:0 auto;padding:0 1.2rem;position:relative;z-index:2}
.ab-eyebrow{margin:0 0 1rem;font-size:.68rem;font-weight:600;letter-spacing:.32em;
  text-transform:uppercase;color:var(--gold)}
.ab-h{font-family:"Playfair Display",Georgia,serif;font-weight:700;
  font-size:clamp(1.9rem,5vw,2.9rem);line-height:1.15;margin:0 0 1rem;color:var(--ink)}
.ab-h .gold{color:var(--gold)}
.ab-lead{color:var(--mut);font-size:1rem;line-height:1.7;margin:0;max-width:44rem}
.ab-center{text-align:center}
.ab-center .ab-lead{margin-left:auto;margin-right:auto}
.ab-rule{width:64px;height:1px;margin:1.5rem auto 0;
  background:linear-gradient(90deg,transparent,var(--gold),transparent)}

/* ---------- hero ---------- */
.ab-hero{position:relative;min-height:88vh;min-height:88svh;display:flex;align-items:flex-end;
  justify-content:center;overflow:hidden;background:#08080a;text-align:center}
.ab-hero-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:center 30%}
.ab-hero-ov{position:absolute;inset:0;
  background:linear-gradient(180deg,rgba(8,8,10,.72) 0%,rgba(8,8,10,.25) 45%,rgba(8,8,10,.94) 100%),
  radial-gradient(60% 45% at 50% 65%,rgba(201,168,106,.12),transparent 70%)}
.ab-hero-in{position:relative;z-index:2;max-width:56rem;padding:7rem 1.25rem 4.5rem}
.ab-hero h1{font-family:"Playfair Display",Georgia,serif;font-weight:700;
  font-size:clamp(2rem,6.5vw,3.9rem);line-height:1.12;margin:0;color:#f6f1e6;
  text-shadow:0 2px 30px rgba(0,0,0,.72)}
.ab-hero h1 .gold{color:var(--gold2)}
.ab-hero p{margin:1.4rem auto 0;max-width:38rem;color:#ddd5c6;font-size:clamp(.95rem,2.2vw,1.15rem);
  line-height:1.7;font-weight:300}
.ab-hero-in>*{animation:abRise .8s both}
.ab-hero-in>*:nth-child(2){animation-delay:.12s}
.ab-hero-in>*:nth-child(3){animation-delay:.24s}
@keyframes abRise{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:none}}
@media(prefers-reduced-motion:reduce){.ab-hero-in>*{animation:none}}

/* ---------- story ---------- */
.ab-story{background:#0b0b0c}
.ab-story p{color:#cfc9bb;font-size:.98rem;line-height:1.85;margin:0 0 1.2rem;max-width:46rem;font-weight:300}
.ab-quote{margin:2.2rem 0 0;padding:1.8rem 1.6rem;border-radius:1rem;text-align:center;
  border:1px solid rgba(201,168,106,.4);background:linear-gradient(180deg,rgba(201,168,106,.09),rgba(201,168,106,.02))}
.ab-quote p{font-family:"Playfair Display",Georgia,serif;font-style:italic;font-size:clamp(1.05rem,2.6vw,1.4rem);
  line-height:1.6;color:var(--gold2);margin:0}

/* ---------- founder ---------- */
.ab-founder{background:#0d0d0f;border-top:1px solid var(--line)}
.ab-founder-grid{display:grid;grid-template-columns:1fr;gap:2rem;align-items:center}
@media(min-width:860px){.ab-founder-grid{grid-template-columns:320px 1fr;gap:3rem}}
.ab-mono{width:100%;max-width:320px;margin:0 auto;aspect-ratio:4/5;border-radius:1rem;
  display:flex;flex-direction:column;align-items:center;justify-content:center;gap:.6rem;
  background:radial-gradient(circle at 50% 35%,rgba(201,168,106,.16),transparent 65%),#141416;
  border:1px solid rgba(201,168,106,.4)}
.ab-mono b{font-family:"Playfair Display",Georgia,serif;font-size:4.5rem;font-weight:700;color:var(--gold);
  line-height:1}
.ab-mono small{font-size:.68rem;letter-spacing:.22em;text-transform:uppercase;color:var(--mut)}
.ab-mono-img{width:100%;max-width:320px;margin:0 auto;aspect-ratio:4/5;border-radius:1rem;display:block;
  object-fit:cover;object-position:center 25%;border:1px solid rgba(201,168,106,.4)}
.ab-role{color:var(--gold);font-size:.78rem;letter-spacing:.22em;text-transform:uppercase;
  margin:.4rem 0 1rem;font-weight:600}
.ab-founder-grid p{color:#cfc9bb;font-size:.95rem;line-height:1.8;margin:0 0 1.1rem;font-weight:300}

/* ---------- team ---------- */
.ab-team{background:#0b0b0c;border-top:1px solid var(--line)}
.ab-team-grid{display:grid;grid-template-columns:1fr;gap:1.2rem;margin-top:2.5rem}
@media(min-width:640px){.ab-team-grid{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.ab-team-grid{grid-template-columns:repeat(3,1fr)}}
.ab-member{border-radius:1rem;overflow:hidden;background:var(--card);border:1px solid var(--line);
  transition:border-color .3s,transform .3s}
.ab-member:hover{border-color:rgba(201,168,106,.5);transform:translateY(-4px)}
.ab-mphoto{aspect-ratio:4/4.4;display:flex;flex-direction:column;align-items:center;justify-content:center;
  gap:.5rem;background:radial-gradient(circle at 50% 35%,rgba(201,168,106,.14),transparent 62%),#101012;
  border-bottom:1px solid var(--line)}
.ab-mphoto b{font-family:"Playfair Display",Georgia,serif;font-size:3.2rem;color:var(--gold);line-height:1}
.ab-mphoto small{font-size:.6rem;letter-spacing:.2em;text-transform:uppercase;color:var(--mut)}
.ab-mphoto-img{width:100%;aspect-ratio:4/4.4;object-fit:cover;object-position:center 25%;display:block;
  border-bottom:1px solid var(--line)}
.ab-mbody{padding:1.3rem 1.3rem 1.4rem}
.ab-mbody h3{font-family:"Playfair Display",Georgia,serif;font-size:1.15rem;margin:0 0 .2rem;color:var(--ink)}
.ab-mbody .r{font-size:.7rem;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);font-weight:600}
.ab-mbody p{font-size:.85rem;line-height:1.65;color:var(--mut);margin:.8rem 0 0;font-weight:300}

/* ---------- vision ---------- */
.ab-vision{position:relative;text-align:center;background:#08080a}
.ab-vision-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.ab-vision-ov{position:absolute;inset:0;background:rgba(5,5,7,.82)}
.ab-vision h2{font-family:"Playfair Display",Georgia,serif;font-size:clamp(2rem,6vw,3.4rem);
  color:var(--gold2);margin:0 0 1.2rem}
.ab-vision p{color:#e2dccf;font-size:clamp(1rem,2.4vw,1.25rem);font-style:italic;line-height:1.7;
  max-width:46rem;margin:0 auto}
.ab-vision .sub{font-style:normal;font-size:.92rem;color:var(--mut);margin-top:1.4rem;font-weight:300}

/* ---------- philosophy / promise cards ---------- */
.ab-cards{display:grid;grid-template-columns:1fr;gap:1rem;margin-top:2.5rem}
@media(min-width:640px){.ab-cards{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.ab-cards4{grid-template-columns:repeat(4,1fr)}}
.ab-card{border-radius:1rem;background:var(--card);border:1px solid var(--line);padding:1.6rem 1.4rem;
  transition:border-color .3s,transform .3s}
.ab-card:hover{border-color:rgba(201,168,106,.5);transform:translateY(-3px)}
.ab-card .n{font-family:"Playfair Display",Georgia,serif;color:var(--gold);font-size:.85rem;
  letter-spacing:.24em;margin-bottom:.7rem}
.ab-card h3{font-family:"Playfair Display",Georgia,serif;font-size:1.1rem;margin:0 0 .6rem;color:var(--ink)}
.ab-card p{margin:0;font-size:.86rem;line-height:1.65;color:var(--mut);font-weight:300}

/* ---------- steps ---------- */
.ab-steps{display:grid;grid-template-columns:1fr;gap:1rem;margin-top:2.5rem;counter-reset:step}
@media(min-width:640px){.ab-steps{grid-template-columns:repeat(2,1fr)}}
@media(min-width:1024px){.ab-steps{grid-template-columns:repeat(5,1fr)}}
.ab-step{position:relative;border-radius:1rem;background:var(--card);border:1px solid var(--line);
  padding:1.5rem 1.2rem;overflow:hidden}
.ab-step::before{content:"";position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(90deg,transparent,var(--gold),transparent);opacity:.7}
.ab-step .n{font-family:"Playfair Display",Georgia,serif;font-size:1.6rem;color:var(--gold);opacity:.85}
.ab-step h3{font-size:.95rem;letter-spacing:.08em;margin:.6rem 0 .5rem;color:var(--ink)}
.ab-step p{margin:0;font-size:.82rem;line-height:1.6;color:var(--mut);font-weight:300}

/* ---------- more than photography ---------- */
.ab-more{background:#0d0d0f;border-top:1px solid var(--line)}
.ab-chips{display:grid;grid-template-columns:repeat(2,1fr);gap:.8rem;margin-top:2.5rem}
@media(min-width:768px){.ab-chips{grid-template-columns:repeat(5,1fr)}}
.ab-chip{display:flex;flex-direction:column;align-items:center;gap:.7rem;text-align:center;
  border-radius:1rem;background:var(--card);border:1px solid var(--line);padding:1.4rem .8rem;
  transition:border-color .3s,transform .3s}
.ab-chip:hover{border-color:rgba(201,168,106,.5);transform:translateY(-3px)}
.ab-chip .ic{display:inline-flex;padding:.65rem;border-radius:999px;color:var(--gold);
  border:1px solid rgba(201,168,106,.45);background:rgba(201,168,106,.07)}
.ab-chip .ic svg{width:1.3rem;height:1.3rem}
.ab-chip span{font-size:.8rem;font-weight:500;color:var(--ink);line-height:1.45}

/* ---------- bts ---------- */
.ab-bts-grid{display:grid;grid-template-columns:1fr;gap:1rem;margin-top:2.5rem}
@media(min-width:768px){.ab-bts-grid{grid-template-columns:repeat(2,1fr)}}
.ab-bts{position:relative;border-radius:1rem;overflow:hidden;border:1px solid var(--line);
  aspect-ratio:4/3;background:#101012}
.ab-bts img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;transition:transform .6s ease}
.ab-bts:hover img{transform:scale(1.04)}
.ab-bts span{position:absolute;left:.9rem;bottom:.8rem;font-size:.68rem;letter-spacing:.2em;
  text-transform:uppercase;color:#f0d896;background:rgba(9,9,10,.55);
  border:1px solid rgba(201,168,106,.4);border-radius:999px;padding:.3rem .8rem}

/* ---------- trust ---------- */
.ab-trust{background:#0d0d0f;border-top:1px solid var(--line);text-align:center}
.ab-stars{color:var(--gold);font-size:1.4rem;letter-spacing:.2em;margin-bottom:.8rem}
.ab-trust p{margin:0 0 1.4rem;font-size:1.05rem;font-weight:600;color:var(--ink)}
.ab-trust p small{display:block;margin-top:.3rem;font-size:.75rem;font-weight:400;letter-spacing:.14em;
  text-transform:uppercase;color:var(--mut)}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:.5rem;border:0;cursor:pointer;
  font-family:inherit;white-space:nowrap;transition:all .3s;text-decoration:none}
.btn-gold{background:var(--gold);color:#0b0b0c;font-weight:600;border-radius:999px;padding:.7rem 2rem;
  font-size:.9rem;box-shadow:0 0 15px rgba(201,168,106,.35)}
.btn-gold:hover{background:#e6c980;box-shadow:0 0 25px rgba(201,168,106,.55)}
.btn-ghost{background:rgba(24,24,27,.35);border:1px solid rgba(201,168,106,.55);color:#fff;font-weight:600;
  font-size:.9rem;padding:.7rem 2rem;border-radius:999px}
.btn-ghost:hover{border-color:var(--gold2)}

/* ---------- final cta ---------- */
.ab-cta{position:relative;text-align:center;background:radial-gradient(60% 60% at 50% 30%,rgba(201,168,106,.1),transparent 70%),#070709}
.ab-cta h2{font-family:"Playfair Display",Georgia,serif;font-size:clamp(1.7rem,5vw,2.8rem);
  line-height:1.25;margin:0 0 .9rem}
.ab-cta p{color:var(--mut);margin:0 0 1.8rem}
.ab-cta-btns{display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center}

/* ---------- footer ---------- */
footer{border-top:1px solid var(--line);padding:2rem 1rem;text-align:center;color:#6a6a6a;
  font-size:.85rem;background:#060606}
footer .fname{font-family:"Playfair Display",Georgia,serif;color:var(--gold);font-weight:700}
.social{display:flex;gap:.7rem;justify-content:center;margin:1rem 0 0}
.social a{width:2.4rem;height:2.4rem;border-radius:999px;display:inline-flex;align-items:center;justify-content:center;
  border:1px solid rgba(201,168,106,.35);color:#8a8a8a;transition:all .3s}
.social a:hover{color:#0d0d0d;background:var(--gold);border-color:var(--gold);transform:translateY(-2px)}
.social svg{width:1.1rem;height:1.1rem;fill:currentColor}

@media(max-width:640px){
  .ab-sec{padding:3.4rem 0}
  .ab-hero{min-height:92vh;min-height:92svh}
  .ab-chips{grid-template-columns:repeat(2,1fr)}
  .ab-cta-btns .btn{width:100%}
}
</style>
</head>
<body>

<!-- ======== PREMIUM NAVBAR ======== -->
<header class="rnav" id="rnav">
  <div class="rnav-in">
    <a href="index.html" class="rbrand">
      <span class="rbrand-name">Deepak Studios</span>
      <span class="rbrand-sub">Cinematic Photography</span>
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

<!-- ======== HERO ======== -->
<section class="ab-hero" aria-label="About Deepak Studios">
  <img class="ab-hero-bg" src="photos/hero/deepakstudiosbokaro.webp" alt="Cinematic wedding photography by Deepak Studios" fetchpriority="high" decoding="async">
  <div class="ab-hero-ov" aria-hidden="true"></div>
  <div class="ab-hero-in">
    <p class="ab-eyebrow">About Deepak Studios</p>
    <h1>We Don&rsquo;t Just Capture Moments.<br><span class="gold">We Preserve Them.</span></h1>
    <p>Bokaro&rsquo;s wedding storytellers — crafting photographs and films with a cinematic soul, so your most beautiful days stay alive forever.</p>
  </div>
</section>

<!-- ======== OUR STORY ======== -->
<section class="ab-sec ab-story" aria-label="Our story">
  <div class="ab-wrap">
    <p class="ab-eyebrow">Our Story</p>
    <h2 class="ab-h">It Started With a Camera.<br><span class="gold">It Grew With Love.</span></h2>
    <div style="height:1.6rem"></div>
    <p>Deepak Studios ki kahani sirf photography se shuru nahi hui thi—ye shuru hui thi logon ke pyaar, bharose aur apnapan se.</p>
    <p>Shuruaat mein humara kaam tha khoobsurat moments ko camera mein capture karna. Lekin jaise-jaise humne families ke saath waqt bitaya, unki khushiyon ka hissa bane aur unke special moments ko apni aankhon se dekha, humare liye photography ka matlab bhi badalta gaya.</p>
    <p>Logon ka pyaar milta gaya, aur Deepak Studios aage badhta gaya.</p>
    <p>Har happy client ka feedback humare liye sirf ek review nahi tha—it was a reminder that we were moving in the right direction. Har event ke baad milne wali appreciation ne humari team ke junoon aur lagan ko aur strong kiya.</p>
    <p>Humara aim kabhi sirf ek normal photograph dena nahi tha.</p>
    <p>Hum chahte the ki saalon baad jab koi apni photographs dekhe, toh use sirf ye yaad na aaye ki us din kya hua tha, balki woh tasveer dekhte hi us pal ki hansi, khushi, emotions aur apnapan phir se mehsoos kar sake.</p>
    <p>Aur us moment ko yaad karte hue, unhe woh photographer bhi yaad aaye—jisne us khushi ko usi waqt mehsoos karke, us ek perfect frame mein hamesha ke liye sambhal liya tha.</p>
    <p>Isi soch ke saath humara ek aur sapna tha:</p>
    <p>&ldquo;Log humein sirf apna photographer na samjhein, balki apne ghar ke ek apne family member ki tarah samjhein.&rdquo;</p>
    <p>Isliye humne sirf photographs lene par focus nahi kiya. Humne relationships banane par focus kiya—clients ko comfortable feel karana, unki needs samajhna, unke celebrations mein genuinely involved rehna aur har important moment ke liye poori dedication ke saath khade rehna.</p>
    <p>Aaj jab hum peeche mudkar dekhte hain, toh humein sirf photographs, albums aur films nahi dikhte. Humein un families ke chehre, unki khushiyan, unka trust aur woh countless memories dikhai deti hain jinka humein hissa banne ka mauka mila.</p>
    <p>Aur shayad isi wajah se Deepak Studios ka ye karwaan dheere-dheere badhta gaya—ek client se doosre client tak, ek family se doosri family tak, aur ek beautiful story se doosri story tak.</p>
    <div class="ab-quote"><p>&ldquo;Aisi tasveerein banana, jinhe dekhkar yaadein sirf yaad na aayein — balki phir se jeeti hui mehsoos hon.&rdquo;</p></div>
  </div>
</section>

<!-- ======== FOUNDER ======== -->
<section class="ab-sec ab-founder" aria-label="Founder">
  <div class="ab-wrap">
    <div class="ab-founder-grid">
      <img class="ab-mono-img" src="photos/Team/deepak.webp" alt="Deepak Modak, Founder of Deepak Studios" loading="lazy" decoding="async">
      <div>
        <p class="ab-eyebrow">Founder</p>
        <h2 class="ab-h">The Person Behind <span class="gold">Deepak Studios</span></h2>
        <p style="font-family:'Playfair Display',Georgia,serif;font-size:1.3rem;color:var(--ink);margin:.6rem 0 .2rem">Deepak Modak</p>
        <p class="ab-role">Founder &amp; Lead Photographer</p>
        <p>Hi, I&rsquo;m Deepak Modak, the founder and creative eye behind Deepak Studios.</p>
        <p>For me, photography is more than just capturing beautiful pictures—it&rsquo;s about preserving the emotions, connections, and little moments that make every celebration truly yours.</p>
        <p>With a passion for wedding and event photography, I focus on capturing genuine emotions, vibrant celebrations, intimate candid moments, and the details that often go unnoticed. Every couple and every family has a different story, and my goal is to document that story in a way that feels authentic, cinematic, and timeless.</p>
        <p>At Deepak Studios, we believe that years from now, your photographs should do more than remind you of how the day looked—they should bring back how it felt.</p>
      </div>
    </div>
  </div>
</section>

<!-- ======== TEAM ======== -->
<section class="ab-sec ab-team" aria-label="Our team">
  <div class="ab-wrap">
    <div class="ab-center">
      <p class="ab-eyebrow">Our Team</p>
      <h2 class="ab-h">The People Behind <span class="gold">The Frames</span></h2>
      <p class="ab-lead">Photographers, cinematographers and editors who treat your celebration like their own family function.</p>
    </div>
    <div class="ab-team-grid">
      <div class="ab-member">
        <img class="ab-mphoto-img" src="photos/Team/rajesh.webp" alt="Rajesh Dhibar at work" loading="lazy" decoding="async">
        <div class="ab-mbody"><h3>Rajesh Dhibar</h3><p class="r">Photographer &amp; Editor</p><p>&ldquo;With a sharp eye for detail and an instinct for the right moment, Rajesh rarely lets a special frame slip away. Honest, focused, and deeply committed to his work, he stays on the job until everything is done just right.&rdquo;</p></div>
      </div>
      <div class="ab-member">
        <img class="ab-mphoto-img" src="photos/Team/ujjwal.webp" alt="Ujjwal Dey at work" loading="lazy" decoding="async">
        <div class="ab-mbody"><h3>Ujjwal Dey</h3><p class="r">Cinematographer</p><p>&ldquo;The cheerful and approachable member of our team, Ujjwal has a natural ability to make clients feel comfortable. Once his gimbal starts moving, ordinary moments can take on a truly cinematic, movie-like feel.&rdquo;</p></div>
      </div>
      <div class="ab-member">
        <div class="ab-mphoto" role="img" aria-label="Aryan"><b>A</b><small>Drone Operator</small><small>Photo coming soon</small></div>
        <div class="ab-mbody"><h3>Aryan</h3><p class="r">Drone Operator</p><p>&ldquo;Cool, creative, and always ready for the perfect aerial perspective, Aryan brings a premium touch to our films. His cinematic drone shots add a grand, royal feel that makes every celebration look even more spectacular.&rdquo;</p></div>
      </div>
      <div class="ab-member">
        <div class="ab-mphoto" role="img" aria-label="Jogen"><b>J</b><small>Photographer</small><small>Photo coming soon</small></div>
        <div class="ab-mbody"><h3>Jogen</h3><p class="r">Photographer</p><p>&ldquo;Punctual, dedicated, and always prepared, Jogen brings a dependable energy to every event. With the latest photography gadgets at hand and a strong commitment to his work, he is always ready to capture the moments that matter.&rdquo;</p></div>
      </div>
      <div class="ab-member">
        <div class="ab-mphoto" role="img" aria-label="Jaitun Aind"><b>J</b><small>Senior Editor &amp; Drone Operator</small><small>Photo coming soon</small></div>
        <div class="ab-mbody"><h3>Jaitun Aind</h3><p class="r">Senior Editor &amp; Drone Operator</p><p>&ldquo;With experience in both post-production and aerial cinematography, Jaitun brings two important creative skills to the team. As our senior editor and drone operator, he helps turn captured moments into polished visual stories with impactful aerial perspectives.&rdquo;</p></div>
      </div>
    </div>
  </div>
</section>

<!-- ======== VISION ======== -->
<section class="ab-sec ab-vision" aria-label="Our vision">
  <img class="ab-vision-bg" src="assets/images/cinematography-hero.jpg" alt="" loading="lazy" decoding="async">
  <div class="ab-vision-ov" aria-hidden="true"></div>
  <div class="ab-wrap">
    <p class="ab-eyebrow">Our Vision</p>
    <h2>Our Vision</h2>
    <p>&ldquo;To create timeless visual stories that let people relive their most beautiful moments for years to come.&rdquo;</p>
    <p class="sub">Every photograph and film we create is made to preserve not only how the moment looked, but how it felt.</p>
  </div>
</section>

<!-- ======== PHILOSOPHY ======== -->
<section class="ab-sec" aria-label="Our philosophy">
  <div class="ab-wrap">
    <div class="ab-center">
      <p class="ab-eyebrow">Our Philosophy</p>
      <h2 class="ab-h">What We <span class="gold">Believe In</span></h2>
    </div>
    <div class="ab-cards ab-cards4">
      <div class="ab-card"><p class="n">01</p><h3>Real Emotions</h3><p>We believe the most beautiful photographs often come from genuine, unscripted moments.</p></div>
      <div class="ab-card"><p class="n">02</p><h3>Authentic Moments</h3><p>We capture your celebration as it truly happens, while being ready for the moments that cannot be planned.</p></div>
      <div class="ab-card"><p class="n">03</p><h3>Timeless Visuals</h3><p>Our goal is to create photographs and films that continue to feel meaningful years from now.</p></div>
      <div class="ab-card"><p class="n">04</p><h3>Attention to Every Detail</h3><p>From the smallest detail to the biggest celebration, every frame deserves care.</p></div>
    </div>
  </div>
</section>

<!-- ======== HOW WE WORK ======== -->
<section class="ab-sec" style="background:#0d0d0f;border-top:1px solid var(--line)" aria-label="How we work">
  <div class="ab-wrap">
    <div class="ab-center">
      <p class="ab-eyebrow">How We Work</p>
      <h2 class="ab-h">From First Call To <span class="gold">Final Film</span></h2>
    </div>
    <div class="ab-steps">
      <div class="ab-step"><p class="n">01</p><h3>Understand</h3><p>We understand your event, expectations and requirements.</p></div>
      <div class="ab-step"><p class="n">02</p><h3>Plan</h3><p>We coordinate the team, timeline, locations and coverage.</p></div>
      <div class="ab-step"><p class="n">03</p><h3>Capture</h3><p>Photography, cinematography, candid moments and aerial perspectives come together.</p></div>
      <div class="ab-step"><p class="n">04</p><h3>Craft</h3><p>Professional editing, colour grading and cinematic finishing.</p></div>
      <div class="ab-step"><p class="n">05</p><h3>Deliver</h3><p>Albums, films and final memories delivered with care and attention to timelines.</p></div>
    </div>
  </div>
</section>

<!-- ======== MORE THAN PHOTOGRAPHY ======== -->
<section class="ab-sec ab-more" aria-label="More than photography">
  <div class="ab-wrap">
    <div class="ab-center">
      <p class="ab-eyebrow">More Than Photography</p>
      <h2 class="ab-h">Everything Your Celebration <span class="gold">Needs</span></h2>
    </div>
    <div class="ab-chips">
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13.997 4a2 2 0 0 1 1.76 1.05l.486.9A2 2 0 0 0 18.003 7H20a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h1.997a2 2 0 0 0 1.759-1.048l.489-.904A2 2 0 0 1 10.004 4z"/><circle cx="12" cy="13" r="3"/></svg></span><span>Wedding Photography</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 3v18M3 7.5h4M3 12h18M3 16.5h4M17 3v18M17 7.5h4M17 16.5h4"/></svg></span><span>Cinematography</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m16 13 5.223 3.482a.5.5 0 0 0 .777-.416V7.87a.5.5 0 0 0-.752-.432L16 10.5"/><rect x="2" y="6" width="14" height="12" rx="2"/></svg></span><span>Reels</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="5" cy="5" r="2"/><circle cx="19" cy="5" r="2"/><circle cx="5" cy="19" r="2"/><circle cx="19" cy="19" r="2"/><path d="M6.5 6.5 12 12M17.5 6.5 12 12M6.5 17.5 12 12M17.5 17.5 12 12"/><rect x="10" y="10" width="4" height="4" rx="1"/></svg></span><span>Drone</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg></span><span>Wedding Live Telecast</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="12" rx="2"/><path d="M8 20h8M12 16v4"/></svg></span><span>55" LED TV</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M2 8h20M6 21h12"/></svg></span><span>LED Wall</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v10M12 17v4M8 13l4 4 4-4"/><rect x="5" y="3" width="14" height="8" rx="1"/></svg></span><span>Standy</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20 9 4M20 20l-5-16M9 4h6M7.5 12h9"/></svg></span><span>Pre-Wedding Photo Walkway</span></div>
      <div class="ab-chip"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v3M16 2v3"/><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg></span><span>Event Photography</span></div>
    </div>
  </div>
</section>

<!-- ======== BEHIND THE SCENES ======== -->
<section class="ab-sec" aria-label="Behind the scenes">
  <div class="ab-wrap">
    <div class="ab-center">
      <p class="ab-eyebrow">Behind The Scenes</p>
      <h2 class="ab-h">Behind Every Beautiful Frame<br><span class="gold">Is a Dedicated Team.</span></h2>
    </div>
    <div class="ab-bts-grid">
      <div class="ab-bts"><img src="photos/Team/deepakstudiosteam.webp" alt="Deepak Studios team together" loading="lazy" decoding="async"><span>The Team</span></div>
      <div class="ab-bts"><img src="photos/hero/hero-bg.webp" alt="Deepak Studios on location" loading="lazy" decoding="async"><span>On Location</span></div>
      <div class="ab-bts"><img src="assets/images/reels-hero.jpg" alt="Deepak Studios cinematic setup" loading="lazy" decoding="async"><span>The Setup</span></div>
      <div class="ab-bts"><img src="assets/images/cinematography-hero.jpg" alt="Deepak Studios in action" loading="lazy" decoding="async"><span>In Action</span></div>
    </div>
  </div>
</section>

<!-- ======== PROMISE ======== -->
<section class="ab-sec" style="background:#0d0d0f;border-top:1px solid var(--line)" aria-label="Our promise">
  <div class="ab-wrap">
    <div class="ab-center">
      <p class="ab-eyebrow">Our Promise</p>
      <h2 class="ab-h">What You Can <span class="gold">Count On</span></h2>
    </div>
    <div class="ab-cards ab-cards4">
      <div class="ab-card"><p class="n">01</p><h3>Be Present</h3><p>We stay attentive to the moments that matter.</p></div>
      <div class="ab-card"><p class="n">02</p><h3>Respect Your Time</h3><p>We value planning, coordination and punctuality.</p></div>
      <div class="ab-card"><p class="n">03</p><h3>Capture With Purpose</h3><p>Every frame should have a reason to be remembered.</p></div>
      <div class="ab-card"><p class="n">04</p><h3>Deliver With Care</h3><p>Your final photographs, films and albums deserve the same attention as the day itself.</p></div>
    </div>
  </div>
</section>

<!-- ======== TRUST ======== -->
<section class="ab-sec ab-trust" aria-label="Google reviews">
  <div class="ab-wrap">
    <div class="ab-stars" aria-hidden="true">★★★★★</div>
    <p>4.9 (249+ Google Reviews)<small>Deepak Studios · Bokaro</small></p>
    <a class="btn btn-gold" href="index.html#reviews">Read Our Google Reviews</a>
  </div>
</section>

<!-- ======== FINAL CTA ======== -->
<section class="ab-sec ab-cta" aria-label="Book your date">
  <div class="ab-wrap">
    <p class="ab-eyebrow">Book Your Date</p>
    <h2>Your Story Deserves to Be<br>Remembered Beautifully.</h2>
    <p>Let&rsquo;s create photographs and films that bring your favourite moments back to life.</p>
    <div class="ab-cta-btns">
      <a class="btn btn-gold" href="index.html#contact">Book Your Date</a>
      <a class="btn btn-ghost" href="wedding.php">View Our Work</a>
    </div>
  </div>
</section>

<footer><span class="fname">Deepak Studios</span> &nbsp;·&nbsp; &copy; <?= date('Y') ?> All rights reserved.
<div class="social">
<a href="https://www.instagram.com/deepakstudiosofficial_/" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
<a href="https://www.facebook.com/deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
<a href="https://www.youtube.com/@deepakstudiosfotography" target="_blank" rel="noopener noreferrer" aria-label="Deepak Studios on YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
</div></footer>

<?php ds_contact_fab(); ?>

<script>
(function () {
  'use strict';
  var nav = document.getElementById('rnav');
  if (nav) {
    var onScroll = function () { nav.classList.toggle('solid', (window.pageYOffset || 0) > 24); };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }
  var burger = document.getElementById('rburger');
  var menu = document.getElementById('rmobmenu');
  if (burger && menu) {
    burger.addEventListener('click', function (e) {
      e.preventDefault();
      var open = !menu.classList.contains('open');
      menu.classList.toggle('open', open);
      burger.classList.toggle('x', open);
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }
})();
</script>

</body>
</html>
