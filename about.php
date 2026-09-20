<?php
/**
 * about.php - About Us — the Deepak Studios story.
 *
 * Who we are, why couples trust us, our values and how to reach us.
 * Mirrors the wedding/prewedding visual language (dark + gold + serif)
 * so the site stays one cohesive brand.
 */

declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

header('Content-Type: text/html; charset=UTF-8');
$gold = '#c9a86a';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About Us | Deepak Studios</title>
<meta name="description" content="Deepak Studios — Bokaro's trusted team for wedding photography, cinematography, pre-wedding films and cinematic shorts. Read our story, meet the team and get in touch.">
<style>
* { box-sizing: border-box; }
html, body { margin: 0; padding: 0; background: #0d0d0d; color: #f5efe0;
  font-family: Georgia, "Times New Roman", serif; }
body { display: flex; flex-direction: column; min-height: 100vh; }
a { color: #c9a86a; }
header { padding: 2.2rem 1rem 1.3rem; text-align: center; background: #161616;
  border-bottom: 1px solid #2a2a2a; }
header h1 { margin: 0; font-size: 2.2rem; letter-spacing: 2px; }
header p { margin: .6rem 0 0; color: #c9a86a; }
.crumbs { margin-top: .9rem; font-size: .88rem; color: #8a8a8a; }
.crumbs a { text-decoration: none; }
.crumbs a:hover { text-decoration: underline; }
main { max-width: 920px; margin: 0 auto; padding: 2.4rem 1.2rem 3rem; }
.sec h2 { display: flex; align-items: center; gap: .7rem; font-size: 1.5rem;
  letter-spacing: 1px; margin: 0 0 1.2rem; }
.sec h2::after { content: ""; flex: 1; height: 1px; background: #2a2a2a; }
.sec .lead { font-size: 1.05rem; line-height: 1.75; color: #cfcfcf; }
.sec .lead b { color: #c9a86a; }
.box { border: 1px solid #2a2a2a; border-radius: 10px; padding: 1.4rem 1.5rem;
  background: #141414; }
.grid2 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
@media (min-width: 640px) { .grid2 { grid-template-columns: 1fr 1fr; } }
.feat { border: 1px solid #2a2a2a; border-radius: 10px; padding: 1.3rem 1.2rem;
  background: #141414; }
.feat b { display: block; color: #c9a86a; font-family: "Playfair Display",
  Georgia, serif; font-size: 1.12rem; margin-bottom: .4rem; }
.feat small { color: #9a9a9a; line-height: 1.6; display: block; }
.contact { display: grid; gap: .5rem; margin-top: .6rem; font-size: .98rem; }
.contact a { text-decoration: none; }
.contact a:hover { text-decoration: underline; }
footer { margin-top: auto; text-align: center; padding: 1.6rem; color: #666;
  font-size: .85rem; border-top: 1px solid #1e1e1e; }
</style>
</head>
<body>

<header>
  <h1>About <span style="color:<?= $gold ?>">Us</span></h1>
  <p>The story behind Deepak Studios</p>
  <div class="crumbs"><a href="index.html">Home</a> &rsaquo; About Us</div>
</header>

<main>
  <section class="sec">
    <h2>Our <span style="color:<?= $gold ?>">Story</span></h2>
    <p class="lead">
      Deepak Studios began with one camera, one promise — to capture weddings the
      way they are <b>felt</b>, not just seen. Based in <b>Bokaro</b>, our small but
      obsessive team has grown into a studio trusted by hundreds of families across
      Bokaro, Dhanbad, Ranchi and beyond.
    </p>
    <p class="lead">
      We are storytellers first, filmmakers second. Every wedding is a one-time
      poem, and our job is to make sure it reads beautifully — <b>genuine
      emotions, real laughter and cinematic elegance</b> — exactly the way it
      happened.
    </p>
  </section>

  <section class="sec">
    <h2>Why Couples <span style="color:<?= $gold ?>">Choose Us</span></h2>
    <div class="grid2">
      <div class="feat"><b>Experienced Team</b><small>Years of wedding work — we
        know the rituals, the light and the perfect moments before they happen.</small></div>
      <div class="feat"><b>Cinematic Storytelling</b><small>Weddings are stories,
        not just albums. Every film is edited like a movie, not a slideshow.</small></div>
      <div class="feat"><b>Premium Photography</b><small>Timeless, editorial-style
        frames that your family will treasure for generations.</small></div>
      <div class="feat"><b>Drone Photography</b><small>Stunning aerial coverage of
        your venue, your baraat and your celebration.</small></div>
    </div>
  </section>

  <section class="sec">
    <h2>Get in <span style="color:<?= $gold ?>">Touch</span></h2>
    <div class="box">
      <p style="margin:0 0 1rem;color:#cfcfcf">Have a date in mind? Let's talk about
        your dream wedding film.</p>
      <div class="contact">
        <a href="tel:+919031700464"><b>Call:</b> +91 90317 00464</a>
        <a href="https://wa.me/919031700464?text=Hi%20Deepak%20Studios%2C%20I%27d%20like%20to%20discuss%20booking">
          <b>WhatsApp:</b> Deepak Studios</a>
        <a href="index.html#contact"><b>Booking:</b> Send an enquiry on our homepage</a>
      </div>
    </div>
  </section>
</main>

<footer>&copy; <?= date('Y') ?> Deepak Studios. All rights reserved.</footer>
</body>
</html>
