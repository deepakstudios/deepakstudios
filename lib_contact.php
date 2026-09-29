<?php
/**
 * lib_contact.php - shared "Call Now" + WhatsApp floating contact UI.
 *
 * Purpose
 * -------
 * index.html (the home page) already ships two working contact controls:
 *
 *   1. the gold "Call Now" pill in the desktop header (`nav.desk` -> `a.btn.btn-gold`),
 *      which is `display:none` below 768px;
 *   2. the bottom-right WhatsApp floating button (`a.wa` with the `ping` ripple) plus
 *      the fixed mobile bottom bar (`.mobbar` -> "Call Now" + "Book Now").
 *
 * This file re-exports those exact controls so every other page can render the same
 * design, size, icon, colour, position, spacing and animation instead of copy-pasting
 * the markup into eight separate files. index.html itself is deliberately NOT wired to
 * this file - its own implementation stays exactly as it is.
 *
 * Reuse contract
 * --------------
 * The CSS below is copied from index.html verbatim. The only change is that the six theme
 * variables index.html reads (`--bg`, `--fg`, `--card`, `--border`, `--primary`,
 * `--primary-fg`) are inlined with the values they resolve to on the home page's dark
 * theme, because the pages that include this file do not define those variables.
 * `#D4AF37` / `#09090b` is the home page's gold pair and is identical in its light and
 * dark themes, so the rendered result is the same on every page.
 *
 * Class names are intentionally identical to index.html's. All target pages were checked
 * and none of them defines `.wa`, `.ping`, `@keyframes ping`, `.mobbar`, `.btn` or
 * `.btn-gold`, so there is no collision.
 *
 * Usage
 * -----
 *   require_once __DIR__ . '/lib_contact.php';
 *   ds_contact_head();          // once, inside <head>
 *   ds_contact_header_call();   // optional: desktop gold pill (premium pages only)
 *   ds_contact_fab();           // WhatsApp button + mobile bottom bar, before </body>
 *
 * @package DeepakStudios
 */

if (!defined('DS_CONTACT_FILE')) {
    define('DS_CONTACT_FILE', __FILE__);
}

/**
 * Emits the shared stylesheet. Call once, inside <head>.
 *
 * Nothing here redefines an existing page rule: the new selectors are self-contained and
 * only ever style the elements rendered by ds_contact_header_call() / ds_contact_fab().
 */
if (!function_exists('ds_contact_head')) {
    function ds_contact_head()
    {
        ?>
<style>
/* ==========================================================================
   Shared contact UI - lifted from index.html so every page matches the home page.
   ========================================================================== */

/* WhatsApp floating button (index.html L284-289) - bottom-right, above the mobile bar. */
.wa{position:fixed;right:1.5rem;bottom:6rem;z-index:50;width:3.5rem;height:3.5rem;border-radius:999px;background:#25D366;
  display:flex;align-items:center;justify-content:center;box-shadow:0 0 20px rgba(37,211,102,.4);transition:transform .3s}
.wa:hover{transform:scale(1.1)}
.wa .ping{position:absolute;inset:0;border-radius:999px;background:#25D366;animation:ping 1.5s cubic-bezier(0,0,.2,1) infinite}
@keyframes ping{75%,100%{transform:scale(1.8);opacity:0}}
@media(min-width:768px){.wa{bottom:2rem}}

/* Fixed mobile bottom bar (index.html L290-296) - hidden on desktop, like the home page. */
.mobbar{display:flex;position:fixed;left:0;right:0;bottom:0;z-index:40;background:#09090b;border-top:1px solid rgba(255,255,255,.10);
  box-shadow:0 -10px 30px rgba(0,0,0,.15);padding-bottom:env(safe-area-inset-bottom)}
@media(min-width:768px){.mobbar{display:none}}
.mobbar>*{flex:1;display:flex;align-items:center;justify-content:center;gap:.5rem;padding:1rem;font-weight:600;
  font-family:inherit;font-size:1rem;border:0;cursor:pointer;text-decoration:none}
.mobbar .call{background:#18181b;color:#fafafa;border-right:1px solid rgba(255,255,255,.10)}
.mobbar .book{background:#D4AF37;color:#09090b;font-weight:700}

/* Desktop "Call Now" gold pill. Rendered inside .rnav-desk on the premium pages, which is
   itself display:none below 768px - so the pill is desktop-only, exactly like the home page,
   where it lives inside nav.desk. The rules below out-specify `.rnav-desk a` so the pill is
   not affected by the nav link styling (letter-spacing, uppercase, animated underline). */
.rnav-desk a.ds-call{position:static;display:none;align-items:center;gap:.5rem;text-decoration:none;
  padding:.5rem 1.5rem;font-size:.875rem;letter-spacing:normal;text-transform:none;font-weight:600;
  color:#09090b;background:#D4AF37;border-radius:999px;white-space:nowrap;
  box-shadow:0 0 15px rgba(212,175,55,.4);transition:all .3s}
.rnav-desk a.ds-call::after{display:none}
.rnav-desk a.ds-call:hover{color:#09090b;background:color-mix(in srgb,#D4AF37 90%,black);box-shadow:0 0 25px rgba(212,175,55,.6)}
@media(min-width:768px){.rnav-desk a.ds-call{display:inline-flex}}

/* Clears the fixed mobile bottom bar so it never covers the last line of the footer.
   Matches what index.html does with `footer{padding:3rem 0 6rem}` on mobile, without
   touching any page's own footer rule. Invisible: every target page renders this spacer
   in the same colour as the page background. */
.ds-contact-pad{height:0}
@media(max-width:767px){.ds-contact-pad{height:4.25rem}}
</style>
        <?php
    }
}

/**
 * Emits the desktop gold "Call Now" pill. Call inside the page's desktop nav element,
 * after the last nav link. Not used by the classic centred-title pages, which have no
 * horizontal nav bar.
 */
if (!function_exists('ds_contact_header_call')) {
    function ds_contact_header_call()
    {
        ?>
        <a href="tel:+919031700464" class="ds-call">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
          Call Now
        </a>
        <?php
    }
}

/**
 * Emits the bottom-right WhatsApp button, the fixed mobile bottom bar and the mobile
 * spacer. Call once, near the end of <body>.
 */
if (!function_exists('ds_contact_fab')) {
    function ds_contact_fab()
    {
        ?>
<!-- WHATSAPP -->
<a class="wa" target="_blank" rel="noopener noreferrer"
   href="https://wa.me/919031700464?text=Hi%20Deepak%20Studios,%20I%20would%20like%20to%20inquire%20about%20a%20photoshoot...">
  <span class="ping"></span>
  <svg style="position:relative;z-index:10" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
</a>

<!-- MOBILE BAR -->
<div class="mobbar">
  <a class="call" href="tel:+919031700464">
    <svg width="20" height="20" style="color:#D4AF37" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>
    Call Now
  </a>
  <a class="book" href="index.html#contact">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v3M16 2v3"/><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18"/></svg>
    Book Now
  </a>
</div>

<div class="ds-contact-pad" aria-hidden="true"></div>
        <?php
    }
}
