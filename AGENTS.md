# AI Project Memory (AGENTS.md)

This file is the permanent documentation and memory for AI agents working on this project. Any AI agent should be able to open ONLY this file and immediately understand the full project.

---

## Project Overview

- **Repository:** `deepakstudios/deepakstudios`
- **What it is:** A GitHub profile README repository that also hosts a PHP-based production deployment system.
- **Purpose:** Displays the GitHub profile page for **Deepak** (deepakstudios) and provides an event-driven, webhook-based deployment pipeline for production.
- **Stack:** Pure PHP (no framework). Markdown profile README. MySQL/MariaDB database.

---

## Architecture

### Repository Structure

```
/
├── AGENTS.md          # This file — AI agent memory
├── Version.txt        # Current semantic version
├── config.php         # Immutable environment configuration (NOT committed)
├── config.php.example # Template for config.php
├── Install.php        # CLI installer — runs migrations via lib_migrations.php
├── lib_migrations.php # Shared migrations registry + runner (Install.php + deploy.php)
├── deploy.php         # GitHub webhook deployment endpoint (git OR archive mode)
├── health.php         # Health check endpoint
├── server-setup.sh    # One-time server bootstrap (VPS/shell hosts)
├── index.html         # Live site (Deepak Studios homepage)
├── lib_contact.php    # Shared "Call Now" + WhatsApp floating contact UI (see 1.9.0)
├── README.md          # GitHub profile README
├── .gitignore         # Protects secrets & runtime state
├── storage/
│   ├── deploy.lock    # Deployment lock file (created at runtime)
│   └── logs/
│       └── deployment.log  # Deployment history (created at runtime)
```

### How the application works

1. The GitHub profile README (`README.md`) is displayed on the user's public GitHub profile.
2. `index.html` is the live production website (Deepak Studios — cinematic wedding photography).
3. PHP scripts (`Install.php`, `lib_migrations.php`, `deploy.php`, `health.php`) provide the deployment framework:
   - **lib_migrations.php** holds the migrations registry and the idempotent runner (`runMigrations()`, `connectDb()`, `ensureStorageDirectories()`).
   - **Install.php** CLI entry point — runs all pending migrations.
   - **deploy.php** receives GitHub webhooks, verifies signatures, and deploys `main` to production using either method:
     - `archive` (default) — pure PHP, no shell/git needed, works on shared hosting; downloads the GitHub zip and swaps files while preserving `config.php` + `storage/`.
     - `git` — uses the git binary via shell_exec (VPS/dedicated hosts).
   - **health.php** verifies the application and database are operational.
4. `server-setup.sh` is the one-time bootstrap: clones the repo, creates `config.php`, runs migrations, and prints the GitHub webhook setup steps.

---

## Database

- **Engine:** MySQL/MariaDB (via PDO)
- **Connection:** Configured in `config.php` (never committed to git)
- **Tables created by migrations:**

### `migrations`
Tracks which migrations have been executed.

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PRIMARY KEY |
| migration | VARCHAR(255) | Migration identifier (e.g. `001_initial_schema`) |
| executed_at | DATETIME | When the migration ran |

### `deployment_logs`
Records deployment history (written by `deploy.php`).

| Column | Type | Notes |
|---|---|---|
| id | INT AUTO_INCREMENT | PRIMARY KEY |
| started_at | DATETIME | Deployment start |
| finished_at | DATETIME | Deployment end |
| previous_version | VARCHAR(20) | Prior deployed version |
| new_version | VARCHAR(20) | Version after deployment |
| previous_commit | VARCHAR(40) | Prior commit hash |
| new_commit | VARCHAR(40) | Commit hash after fetch |
| migration_result | VARCHAR(255) | Result of migrations |
| status | VARCHAR(20) | success / failure |
| error_message | TEXT | Error details (no secrets) |

### Migration system

- Migrations are idempotent — running `php Install.php` multiple times is safe.
- Each migration runs exactly once (tracked in `migrations` table).
- Migration identifiers follow the pattern: `NNN_description` (e.g. `001_initial_schema`).
- **NEVER modify an already-executed migration.** Add a new one instead.
- Current migrations:

| ID | Description |
|---|---|
| 001_initial_schema | Creates `migrations` and `deployment_logs` tables |

---

## Configuration System

- `config.php` holds environment-specific credentials (database, app URL, environment, deployment webhook secret).
- `config.php` **must NEVER** be modified by the deployment process.
- `config.php` is git-ignored — it is **NOT** committed to the repository.
- A `config.php.example` template IS committed so fresh servers know the required structure.
- Credentials must never be hard-coded anywhere else in the project.
- Real production credentials must never be committed to GitHub.

---

## Deployment

### How updates reach production (event-driven, no cron)

```
Developer / AI Agent
        |
        v
GitHub main branch (push)
        |
        v
GitHub webhook (push event)
        |
        v
Production server /deploy.php
        |
        v
HMAC signature verification
        |
        v
Branch == main check
        |
        v
Acquire deployment lock
        |
        v
git fetch origin main
        |
        v
Update working tree to origin/main
        |
        v
Run Install.php (migrations)
        |
        v
Validate health check
        |
        v
Release lock & log deployment
        |
        v
Application updated
```

### Deployment endpoint

- **URL:** `https://YOUR-DOMAIN/deploy.php`
- **Method:** POST
- **Secured by:** GitHub webhook HMAC-SHA256 signature (secret from `config.php`)
- **Behavior:**
  1. Rejects any request without a valid `X-Hub-Signature-256`.
  2. Rejects non-`push` events.
  3. Rejects pushes to any branch except `main`.
  4. Acquires a deployment lock (single deployment at a time).
  5. Deploys `main` via the configured method (`archive` or `git`).
  6. Runs pending migrations (in-process — no shell needed).
  7. Validates the database connection (health).
  8. Logs the deployment in the database (`deployment_logs`) and to `storage/logs/deployment.log`.
  9. Returns JSON response.

### Deployment methods

- **`archive` (default, shared-hosting friendly):** `deploy.php` downloads
  `https://codeload.github.com/{owner}/{repo}/zip/refs/heads/main`, extracts it, and syncs
  every file EXCEPT `config.php` and `storage/`. Pure PHP (cURL or streams + ZipArchive) —
  works on cPanel/etc. where shell and git are disabled. Database migrations run in-process.
- **`git` (VPS/dedicated):** `deploy.php` runs `git fetch origin main` + `git reset --hard origin/main`
  via shell_exec. Migrations run in-process too.

Both modes are triggered *only* by verified push webhooks — no cron, and arbitrary commits/branches
from the payload are never trusted or executed.

### Production setup for GitHub webhook

```text
1. Server: install PHP 8.x + PDO MySQL, git, and make the PHP user able to run `git` commands.
2. Create `config.php` from `config.php.example` and fill in real values.
3. In GitHub → repo → Settings → Webhooks → Add webhook:
   - Payload URL: https://YOUR-DOMAIN/deploy.php
   - Content type: application/json
   - Secret: (same value as `webhook.secret` in config.php)
   - Events: Just the push event
4. Point the web root to this directory (or copy it there).
```

### Zero-downtime consideration

The current server architecture is unknown/fresh. The safe practical strategy used here deploys in place with a fetch + reset workflow (`git fetch origin main` then `git reset --hard origin/main`). See `AGENTS.md` → "Important decisions" for notes. If the production setup later supports it, a `releases/` + `current` symlink strategy can be adopted without breaking this design.

---

## Security Requirements

- HMAC-SHA256 webhook signature verification is **mandatory** (constant-time comparison).
- HTTPS is required on the production server.
- `config.php` must never be exposed by the web server.
- No credentials in git, logs, or shell commands.
- Deployment lock prevents concurrent deployments.
- Shell commands are built with `escapeshellarg()` — no untrusted payload values are interpolated.
- Only `origin/main` is deployable. Arbitrary commits from webhooks are never executed.

---

## How to run/test the project

### Local testing (PHP CLI)

```bash
# Syntax-check all PHP files
php -l config.php
php -l Install.php
php -l deploy.php
php -l health.php

# Run database migrations (requires config.php with working DB)
php Install.php

# Check health (requires running web server / config)
php health.php
```

### Test a webhook locally (optional)

```bash
# Generate a valid signature for the secret 'testsecret'
php -r "echo hash_hmac('sha256', file_get_contents('php://stdin'), 'testsecret');"
```

Requires **PHP 8.0+** with PDO MySQL (`extension=pdo_mysql`).

---

## Versioning

### Current Version

```
1.18.0
```

### Semver rules

- **Bug fix** → PATCH (1.0.1)
- **Backward-compatible feature** → MINOR (1.1.0)
- **Breaking change** → MAJOR (2.0.0)

### How to update the version

1. Update `Version.txt`.
2. Update `AGENTS.md` (current version + change log).
3. Add a change log entry.
4. Commit everything together.

---

## Change Log

## 1.18.0 - 2026-10-03

- **Services section: 4 categories + interactive Photo Walkway (`index.html`
  only).** Static cards for Weddings, Celebrations, Special Events and Live &
  Event Experience (existing page links kept, priority services keep the gold
  marker), plus a full-width highlighted "Pre-Wedding Photo Walkway" accordion
  (collapsed by default, smooth max-height expand/collapse, rotating chevron,
  aria-expanded, no reload). 4-column desktop ≥1100px, 2-column tablet, stacked
  mobile. No other section touched.

## 1.17.0 - 2026-10-03

- **Services section redesigned (`index.html` only).** The JS-rendered image cards
  are replaced by 3 static premium category cards (01 Photography & Films, 02
  Live Wedding Experience, 03 Event Add-Ons) with serif numbers, line icons, gold
  accents and supporting text. Priority services (Live Telecast, 55" LED TV,
  LED Wall, Drone) get a glowing gold marker. Existing page links preserved
  (wedding.php, cinematography.php, prewedding_photos.php, prewedding.php).
  3-column desktop, stacked compact cards on mobile. No other section touched.

## 1.16.12 - 2026-10-03

- **Removed the Our Portfolio section from the home page (`index.html`).**
  Section HTML, gallery data/filter/render JS and the now-unused lightbox modal
  + helper removed. The shared `url()` helper stays (showcase grid uses it).
  Nav never linked `#portfolio` (Photography points to `photography.php`), so
  no links break.

## 1.16.11 - 2026-10-03

- **Removed CINEMATIC labels from mobile gallery tiles (`index.html`).**
  The category tag is now skipped for Cinematic photos in the portfolio grid;
  Wedding/Pre-Wedding tags unchanged. Desktop unaffected (tile tags are
  desktop-hidden by CSS).

## 1.16.10 - 2026-10-03

- **Removed the mobile-only Cinematography category section (`#m-cine`)** from the
  home page, including its dedicated CSS. The section was mobile-only so desktop
  is unaffected. Cinematography remains reachable via nav, services and footer.

## 1.16.9 - 2026-10-02

- **Mobile Cinematic Films: exact 2×2 teaser grid + 16:9 in-page player
  (`index.html`, mobile only).** The showcase below "View Our Photography" now
  shows exactly the 4 wedding teasers (The Vows, The Mandap, Nidhi & Shubham,
  Mehendi Mornings) as 16:9 cards in a fixed 2-column × 2-row grid (no slider,
  no stacking). Tapping a card opens a dedicated premium modal (dark overlay,
  autoplay via youtube-nocookie embed, 16:9, Play/Pause via IFrame API
  postMessage, Prev/Next in-modal, X + ESC close, video stopped on close, body
  scroll locked with position restored). Desktop untouched.

## 1.16.8 - 2026-10-02

- **Homepage mobile reels → wedding teasers; cinematography page restored.**
  The mobile-only home reels grid now shows the 4 wedding teasers (The Vows,
  The Mandap, Nidhi & Shubham, Mehendi Mornings) instead of the 12 Shorts;
  filters/modal unchanged. `cinematography.php` restored to its pre-1.16.5 state
  (12 wedding films, original grid, no Watch button) — owner's earlier
  cinematography changes were meant for the home page.

## 1.16.7 - 2026-10-02

- **Revert v1.16.6.** Wedding films restored to The Vows, Baraat Nights,
  Sangeet Sessions, The Mandap (owner request).

## 1.16.5 - 2026-10-02

- **Cinematography films trim.** Wedding category reduced to 4 films (The Vows,
  Baraat Nights, Sangeet Sessions, The Mandap); films grid is now 2 columns from
  mobile up (desktop 3-column breakpoint unchanged). Added a "Watch Our Films"
  button below the grid linking to the YouTube channel. Engagement/pre-wedding
  films untouched.

## 1.16.4 - 2026-10-02

- **Mobile-only refinements (`index.html`, `@media(max-width:640px)` only).**
  Why-choose points restyled from numbered cards to a clean editorial list
  (numbers hidden, boxes/borders removed, hairline separators, small gold dash
  accents; point titles and descriptions unchanged). Mobile hero bottom gap
  reduced ~25% (`padding-bottom` 3rem → 2.2rem, short-screen overrides kept
  proportional) so the rating sits naturally closer. Desktop/tablet untouched.

## 1.16.3 - 2026-10-02

- **Fixed the pulsing/flickering hero bottom introduced in 1.16.2.** Animating `.hero-ov2`
  in lockstep with `.hero-bg` stopped the photo escaping the fade but made the bottom visibly
  blink: the gradient is a separate compositing layer, so scaling it forced a re-rasterise
  every frame -- it shimmered against the photo and the ramp boundary drifted up and down
  through the 26s cycle. A static overlay cannot flicker.
- **`.hero-ov2` is no longer animated at all.** Instead it now over-reaches the photo, so the
  fade is correct at every point of the zoom without either layer having to move. At
  `>=768px` the fade is `height:104vh` with `top:0`, giving it **7px of cover at peak zoom**
  (`scale(1.06)`) and 30px at `scale(1)`. `prefers-reduced-motion` was reverted to its 1.16.1
  form since the fade no longer animates.
- **Verified across the whole animation, not just its endpoints.** Sampled `scale(1.00)`,
  `1.015`, `1.03`, `1.045`, `1.06` at 900px and 1440px: `animationName` is `none` and the
  fade covers the photo at **every** step, so there is no frame at which the photo can escape.
- **The fade's overhang is provably invisible.** A pixel diff of the page against the same page
  with `.hero-ov2` removed shows the fade stops exactly at its own bottom edge and spills
  **0px** past it at 900/1023/1440px, while still differing from the no-fade render 120px
  above that edge (so the fade is genuinely present, not a false pass). This matters because
  the hero is `overflow:visible` at `>=768px`, so the overhang paints over the next section.
- **Full regression sweep at 360/390/640/768/900/1023/1280/1440/1920px.** At `>=900px`
  (where the hero is `overflow:visible`) the fade covers the photo with 7px to spare; at
  `<=768px` the hero is `overflow:hidden` and clips the photo's overhang, so the negative
  cover there is contained. No horizontal overflow, `php -l` clean, CSS-only.
- Supersedes the 1.16.2 lockstep approach, which fixed the overhang but caused this flicker.

## 1.16.2 - 2026-10-02

- **Fixed the hero photograph escaping its bottom fade during the Ken Burns zoom.** The
  photo (`.hero-bg`) is animated `kb 26s` to `scale(1.06)` while the bottom fade
  (`.hero-ov2`) was a static sibling, so the image grew past the bottom of the gradient and
  left an un-faded strip sitting below the dark area -- most visible in the 768-1023px band
  where `.hero` is `overflow:visible` to make room for the `#why` panel. `index.html` only.
- **`.hero-ov2` now carries the same `kb` animation as `.hero-bg`**, so image and fade
  always scale together and the fade can never be outrun by the photo it is fading out.
  Measured overhang drops from **22px to 0px** at 360/390/640/768/900/1023/1280/1440/1920.
- **`prefers-reduced-motion` now stops `.hero-ov2` as well.** Without this, reduced-motion
  froze the photo while the fade kept zooming -- reintroducing exactly the desync above.
- **The fade's first stops are now solid** (`var(--bg)` 0-4% desktop, `#0b0b0f` 0-5% mobile).
  Because the photo is scaled, the fade's own foot has to stay solid to remain seamless at
  every point in the animation. The mobile solid zone lands on `#intro`'s `#0c0c0e` and its
  gold hairline, so that handoff is unchanged.
- **Rebuilt the fade as a multi-stop S-curve** (was a 2-stop ramp) so the falloff is
  monotonic and kink-free, clearing by 54% and leaving the upper half of the photo clean.
  Verified at the animation's worst frame: the ramp descends monotonically into the page
  background (mean luminance 14.8 -> 13.3) with no visible step.
- **Known pre-existing issue, not addressed here:** `.hero-bg`'s zoom also escapes the
  `overflow:visible` hero horizontally, adding 27px at 900px and 43px at 1440px. Verified
  independent of this change (identical with and without the `.hero-ov2` animation).

## 1.16.1 - 2026-10-02

- **Desktop "Why Choose Deepak Studios" is now one cohesive panel instead of four stacked
  cards.** Desktop-only visual fix to 1.16.0; `index.html` only. The diff is **CSS-only**
  (+21 / -14) and every changed rule lives inside `@media(min-width:1024px)`.
- **Not markup: the `#why` block is byte-identical to 1.16.0** (1425 bytes, zero line
  differences). All four headings and bodies are unchanged ("Cinematic Storytelling",
  "Premium Albums & Finishing", "Well-Managed & Supportive Team", "On-Time Delivery"). The
  `01-04` labels and gold rules are **hidden with `display:none` at `>=1024px`** rather than
  deleted, so the markup keeps working for the tablet presentation.
- **The four point cards became one surface.** `.hero-why` now carries the panel treatment
  itself -- `background:rgba(10,10,12,.5)` + `backdrop-filter:blur(14px)`, a subtle
  `rgba(212,175,55,.16)` border and `border-radius:1.15rem`. Each `.why-card` is reset to
  `padding:0; background:none; border:0; border-radius:0; box-shadow:none` with hover
  transform/shadow disabled, so there is no per-card box and nothing shifts on hover.
- **No dividers between points.** `.why-n` and `.why-rule` are hidden and the
  `.why-card + .why-card` hairline was removed; the four points are separated by spacing
  alone (`.why-grid` `gap` raised from `.8rem` to `1.35rem`). Hierarchy comes from type
  alone -- Playfair Display 700 `#f6f3ea` titles vs. weight-300 `rgba(255,255,255,.62)`
  descriptions -- plus one hairline under the section head.
- **Tablet and mobile are provably untouched.** An earlier pass in this work also stripped
  the numbers from the shared markup, which changed the `768-1023px` panel; that was
  reverted, so tablet measures identically to the baseline (2 columns, `rgb(24,24,27)`
  cards, 16px radius, numbers visible) and `<768px` still uses the separate `#intro`
  `.ds-point` list with its own 4 numbers.
- **No unrelated change.** The `overflow:hidden` that was briefly added to `.hero` at
  `>=1024px` was removed again: `html` already sets `overflow-x:hidden`, so it was
  unnecessary and out of scope. The occasional 1px `scrollWidth` is the pre-existing `kb`
  `scale(1.06)` animation bleed -- reproducible on 1.16.0 itself and unrelated to this fix.
- **Verified in real headless Chrome at 1920/1600/1440/1366/1280/1152/1100/1024.** Desktop:
  0 of 4 numbers visible, 0 of 4 rules visible, transparent cards, 1 column, panel never
  clipped and never colliding with the `.hero-in` copy column. `768/900`: unchanged from
  baseline. `320/390/430/640/700`: `#intro` visible with its 4 numbers and the desktop panel
  hidden. A pixel-diff of the normal render against a photo-only render over the couple's
  mapped band (source cluster `880,840 -> 1080,1040` of 1920x1080) returned **0 changed
  pixels** at 1440/1366/1024. `php -l` clean on all deploy files.

## 1.16.0 - 2026-10-02

- **Desktop hero rebuilt: the studio name is no longer the headline, and "Why Choose Deepak
  Studios" now lives inside the hero as a left-side panel.** `index.html` only.
- **Headline hierarchy (`@media(min-width:1024px)`).** The desktop `h1` now reads
  **"Wedding Photography & Cinematography"** via a new `.hh-main` span; the old
  `.hh-desktop` "Deepak Studios" span is hidden at this breakpoint but **kept in the DOM**
  (`.hh-mobile` is untouched), so nothing is deleted for SEO or for the smaller breakpoints.
  `.hero-sub` is hidden on desktop to remove the now-duplicate second line.
- **Location split into two spans.** `.hero-loc` now wraps `.loc-desktop`
  ("Bokaro, Jharkhand", rendered as a gold uppercase pill with a blurred dark backdrop at
  >=1024px) and the preserved `.loc-mobile` ("Bokaro · Jharkhand"). `.loc-desktop` is
  `display:none` below 1024px and `.loc-mobile` above it, so exactly one is ever visible.
- **Element order via flex `order` (no DOM reordering).** badge `-2` → tagline `-1` →
  headline → location `1` → CTA row `2`. The two existing CTAs are reused byte-for-byte:
  `CHECK AVAILABILITY` still calls `openBooking()` (still one `#booking` modal, still one of
  the page's two `<form>`s) and `View Our Work` still links to `wedding.php`. No new route,
  no duplicate button, no new asset. Hero CTA row tuned to `gap:.9rem` and
  `min-width:14.5rem` so both buttons fit on one row down to 1024px.
- **Scroll indicator removed.** The `.scroll-ind` markup, its CSS block, the `.scroll-line`
  rule and the `@keyframes scrollDot` animation were all deleted; the CTA row occupies the
  bottom zone instead. Zero references remain in `index.html`.
- **"Why Choose Deepak Studios" moved into the hero (`#why`).** The former standalone
  `<section id="why" class="pad why">` was re-parented as the last child of `<section
  class="hero">` and relabelled `class="why hero-why"`, so `id="why"` and any inbound
  `#why` anchor still resolve. The four card headings and bodies are byte-unchanged
  ("Cinematic Storytelling", "Premium Albums & Finishing", "Well-Managed & Supportive Team",
  "On-Time Delivery"). The `pad`/`glow` wrappers and the `reveal` classes were dropped, so
  the panel renders immediately with no IntersectionObserver dependency.
  - `>=1024px`: absolutely positioned left panel -- `left:clamp(1.5rem,4vw,5rem)`,
    `top:50%`, `translateY(-42%)` (below optical centre), `width:min(25rem,30vw)`, one
    column, per-point translucent cards (`rgba(16,16,19,.62)` + `blur(12px)`).
    `.hero-in` moves right to `width:min(56rem,54%)` so the copy column and the panel sit
    side by side. (Superseded in 1.16.1: the four cards became one cohesive panel.)
  - `768-1023px` (tablet): the panel stays in normal flow under the hero copy as a
    two-column block on an opaque `--bg` fill with a top hairline, so it never becomes a
    translucent box floating over the photograph.
  - `<768px`: still hidden, exactly as before -- the mobile WHY CHOOSE copy in `#intro` is
    the mobile presentation and was not touched.
  - A `min-width:1024px and max-height:780px` pass tightens card padding/type so the panel
    still fits short laptop viewports.
- **Hero text lifted clear of the couple.** `photos/hero/deepakstudiosbokaro.webp` is
  1920x1080; a pixel-luminance probe of the frame locates the subject at roughly
  `y=633-774` of a 844px-tall hero. `.hero-in` gained `padding-bottom:7.5rem` (and the same
  in the tablet pass) so the headline/location/CTA block ends at `y≈580-594` instead of
  overlapping that band. Verified as a measured 0px overlap at every width, for the panel
  and for the text block.
- **Mobile and tablet are measurably unchanged.** The new rules live in
  `@media(min-width:768px)` / `min-width:1024px` only; the only edit below 768px is the
  removal of the `scroll-ind` hide rule (its element is gone). Re-measured against the
  previous `HEAD` build at 320/375/390/430/640px: identical headline text, `.hh-desktop`
  visibility, tagline visibility, location chip text, hero-button visibility, `#intro`
  visibility and overflow state. At 768px and 900px the headline and CTA visibility also
  match the baseline.
- **Verified in real headless Chrome.** `1024/1100/1152/1280/1366/1440/1600/1920`: no
  horizontal overflow, hero height locked to the viewport, panel never overlaps the copy
  column or clips, photo subject uncovered, both CTAs on one row. `768/900`: no overflow,
  stacked panel, subject clear. DOM checks: 0 JS errors, 4 why cards, `#why` is the last
  child of `.hero`, `#why` precedes `#services`, still exactly 2 `<form>` elements and 1
  `#booking` modal, `scroll-ind` count 0, `section`/`div`/`form` tags balanced,
  `php -l` clean.

## 1.15.0 - 2026-10-01

- **Mobile-only premium layer for the home page (`index.html`), desktop untouched.**
  All new CSS lives in `@media(max-width:640px)` (plus `min-width:641px` hides for
  mobile-only blocks); no desktop selector was modified. Mobile hero now shows the
  review badge, "Wedding Photography & Cinematography" serif headline and a
  "Bokaro · Jharkhand" location chip (no long paragraph, no duplicate CTAs — the
  sticky CALL NOW / BOOK YOUR DATE bar is unchanged). Intro became WHY CHOOSE with
  4 points; portfolio got a mobile PHOTOGRAPHY heading and 2-column grid; new
  mobile-only Cinematography (6 linked cards), Reels (12 Shorts, main+sub filters,
  9:16 grid, in-page modal, no new-tab YouTube) and Final CTA sections (all hidden
  on desktop); reviews marquee renders stacked single-set cards on mobile.

## 1.14.0 - 2026-10-01

- **Social media icons in footers.** Added Instagram (`deepakstudiosofficial_`),
  Facebook (`deepakstudiosfotography`) and YouTube (`@deepakstudiosfotography`)
  icon links (official SVG glyphs, no text, `target="_blank" rel="noopener"`)
  to all 9 footers: `index.html` (brand column), `cinematography.php`,
  `photography.php`, `reels.php`, `wedding.php`, `prewedding.php`,
  `prewedding_photos.php`, `prewedding_videos.php` and `lib_gallery.php`
  (also covers album pages `a.php`–`e.php`). Gold circular hover style matching
  each page's theme; no other content touched.

## 1.13.0 - 2026-09-29

- **Mobile-only premium cinematic redesign of the home page; desktop visually untouched.**
  Phones (max-width:640px) now get a luxury layout distinct from desktop. Hero becomes
  `height:100svh` (min-height 100svh) with `align-items:flex-start`, a darker `.hero-ov`
  (`rgba(0,0,0,.32)`), a glass trust badge, and a short serif headline -- "Timeless." /
  "Moments." at `clamp(2.7rem,11vw,3.4rem)` weight 600 -- so the text block ends
  ~394-443px on 360-430px phones, well clear of the couple band (~579px+).
- **Cinematic copy swapped in for mobile only, via paired desktop/mobile spans.** Same
  elements now hold a desktop span and a mobile span; desktop spans are hidden below
  768px, mobile spans hidden at 768px+, so desktop/tablet keep the byte-identical
  original copy while mobile shows: h1 "Timeless. Moments." (desktop span keeps
  "Deepak Studios" for SEO), sub "Wedding Photography & Cinematography", a new
  `.hero-loc` chip "Bokaro Â· Jharkhand", and tagline "Real emotions. Beautiful
  celebrations. Timeless memories." The badge keeps the dynamic 5-star markup and
  reads "4.9 Â· 249+ Google Reviews".
- **CTAs + scroll cue.** Primary "CHECK AVAILABILITY" is a full-width 58px champagne-gold
  pill with a soft gold shadow; ghost CTA is a 44px outline pill with a trailing arrow
  (CSS `::after`); the scroll indicator now reads "Explore" with an animated gold line
  (all CSS-only swaps; desktop DOM unchanged).
- **New intro/stats strip between hero and services (mobile only).** A charcoal `#intro`
  section shows ONLY fully verified figures -- "4.9 Google Rating" and "249+ Google
  Reviews" -- beneath "More Than A Photograph" / "Stories worth remembering.", reusing
  the hero photo as a faint background (opacity .13). Unverified figures (10+ years,
  1000+ weddings) are deliberately not shown.
- **Services and portfolio re-styled for mobile.** Services are image-forward cards
  (photo opacity .55, bottom fade, bottom-aligned ivory serif title + gold rule; icons
  hidden); portfolio tiles are single-column editorial cards with a small category
  `.tag` pill added via JS.
- **Fixed elements tuned.** WhatsApp button 52px with a neutral dark shadow and no ping
  dot, raised above the bar; bottom bar 64px, dark "CALL NOW" / champagne-gold
  "BOOK YOUR DATE" halves (button text changed to "Book Your Date"); footer clearance
  now includes `env(safe-area-inset-bottom)`; header gets a subtle top scrim so the
  logo stays legible over the photo.
- **Typography per brief.** Serif headings via the already-loaded Playfair Display,
  body sizes 15-17px, letterspaced 11px labels, champagne-gold accent on charcoal/ivory.
  No new fonts, no new libraries; below-fold images stay lazy-loaded.
- **Verified in real headless Chrome at 360/375/390/412/430 and 1280px.** Mobile: no
  horizontal overflow; headline 2 line-boxes; CTA bottom 419-443; `#intro` + stats
  visible; tiles/cards/mobbar/wa report correctly; `Book Your Date` shown; 5 dynamic
  stars render; no console errors. At 1280px every measured value (hero 900, h1 112px,
  hero-in 273.7-706.3, badge 37, buttons 248x60 + 248x62, scroll-line block, header
  transparent) matches the 1.12.0 baseline; all visible desktop text is byte-unchanged.

## 1.12.0 - 2026-09-29

- **Home page mobile hero cleaned up -- subject visible, text compact, no overlap.** On
  phones (max-width:640px) the hero no longer inherits the desktop `100vh` full-bleed
  layout: it now has `height:auto; min-height:min(74svh,600px)`, so on a 390px phone the
  visible image window grows from ~27% to ~37% of the photo's width (much less zoom/crop).
  The couple (located bottom-centre of `photos/hero/deepakstudiosbokaro.webp`, found by a
  Chrome pixel-luminance probe of the 1920x1080 frame) reads clearly, and the text block
  sits at the top via `align-items:flex-start` + `padding-top:clamp(4.5rem,11svh,5rem)`
  instead of the desktop centred overlay.
- **Compact typography so text no longer dominates the frame.** On mobile the badge,
  headline and paragraphs are resized (`h1 clamp(2.35rem,11vw,2.85rem)`, `line-height:1.05`,
  sub/tag ~1rem/.85rem at `line-height:1.45`, tighter margins, `max-width` caps) and both
  CTA buttons are shortened (`height:3rem`) so the whole block stays above the subject band
  and the buttons still fit at 360-430px. A second max-width:380px pass trims further
  (smaller h1/tag, 2.8rem buttons, tighter gaps) for small phones.
- **No text was rewritten or deleted and no image added.** The headline, subline, tagline
  and both CTAs are byte-identical; the existing hero photo and its `cover`/`center`
  background are unchanged.
- **Desktop/tablet and the rest of the page are untouched.** All new rules live inside the
  two mobile media-query blocks; the diff is +22 / -0 in `index.html` and nothing else
  changed. Measured in real Chrome at 360/375/390/430px: hero `min-height` becomes 600px
  (was 820 = 100vh), `h1` 37.8-45.6px (was 44), text block bottom 423-429px which is clear
  of the subject band marker (432px; the bright face core is ~470px+), CTA buttons
  45-48px tall and fitting full-width. At 1280px every measured value (hero 820px, `h1`
  112px, margins, badge, CTA layout, scroll indicator) is identical to before.

## 1.11.0 - 2026-09-29

- **The booking modal is now a "Check Availability" enquiry.** The SAME form, opened by
  both the desktop "CHECK AVAILABILITY" hero button and the mobile "Book Now" bar, has:
  heading "Check Availability"; the "Shoot Type" dropdown replaced by **Type of Event**
  with exactly the studio's event options -- Wedding, Pre-Wedding, Destination Wedding,
  Engagement, Anniversary, Birthday, Pre-Birthday, Housewarming, Other Event (no
  cinematic-film or other service types); the phone field relabelled
  "WhatsApp / Phone Number"; two new OPTIONAL fields -- **Venue / Location** (input) and
  **Message** (textarea); and the button now reads **"Check Availability on WhatsApp"**.
  Full Name, WhatsApp / Phone Number and Event Date are unchanged and still required.
- **Desktop and mobile literally share the one updated form.** Both CTAs call the same
  `openBooking()` -> `openModal("booking")` and the same form, so both submit through the
  same `submitBooking()`. No second/mobile form was created; the mobile bar itself
  (position, styling, label) is unchanged; the whole page still has exactly two
  `<form>` elements (this modal + the "Send an Enquiry" contact form).
- **The WhatsApp message now asks for availability.** Format (an optional line is left
  out entirely when its field is blank):
  `Hello Deepak Studios,` / blank / `I would like to check availability for my event.` /
  blank / `Name: ...` / `WhatsApp: ...` / `Event Type: ...` / `Event Date: ...` /
  `Venue/Location: ...` (only if filled) / blank / `Message:` + text (only if filled) /
  blank / `Please share your availability and package details.`
- **Same number, encoding and opening behaviour as before.** Still opens
  `https://wa.me/<number>?text=...` with the number read from the existing floating
  button (`waNumber()`, fallback `WA_FALLBACK = "919031700464"`), one
  `encodeURIComponent()` call (spaces, `&`, `#`, `?`, Devanagari, `%0A` newlines),
  `window.open(link,"_blank")` + `w.opener = null`, and the same-tab fallback when a
  popup is blocked. Native `required` validation, `onsubmit="return submitBooking(event)"`
  wiring, the reset and the toast are all unchanged.
- **Nothing else changed.** The diff is confined to 2 hunks in `index.html`: the booking
  modal markup (heading, label, dropdown, two optional fields, button label) and
  `submitBooking()`. Hero, navbar, photography/cinematography/reels links, portfolio,
  services, reviews, gallery/filters, footer, mobile bar and floating WhatsApp button are
  untouched. `reels.php`, `cinematography.php`, `photography.php`, `wedding.php`,
  `prewedding.php`, `lib_contact.php`, `a.php` - `e.php` and all deploy files are
  byte-identical to 1.10.0.
- **Verified in real Chrome.** The verbatim modal, floating button and shipped WhatsApp
  script were driven with `requestSubmit()` and real clicks: 105 checks, 0 failures
  (new heading; kept fields; exact 9-option list with no service types; optional
  venue/message; button label; ONE shared form whose `openBooking()` really opens the
  `#booking` modal holding the updated fields; submission goes to the SAME number
  919031700464 as the floating button; message byte-equal to the template with and
  without optional fields; Devanagari/`&`/`<`/`>`/newline encoding; validation blocks
  empty or incomplete submits; exactly one `window.open` per valid submit; same-tab
  fallback + `opener=null` source checks; button label fits `white-space:nowrap` at
  320/360/390/414/768/1024px). Plus 10 static same-modal wiring checks (one `#booking`,
  exactly two forms, hero + mobile bar both bound to `openBooking()`, `openBooking()`
  === `openModal("booking")`) and the git diff confined to the 2 intended hunks.

## 1.10.0 - 2026-09-29

- **Both enquiry forms on the home page now submit straight into the studio's WhatsApp chat.**
  Previously `submitEnquiry()` and `submitBooking()` only reset the form and showed a toast
  ("Enquiry sent! We will call you shortly." / "Consultation requested! We will be in touch."),
  so a visitor's details went nowhere. Submitting now builds a formatted, URL-encoded message and
  opens WhatsApp with it pre-filled. No backend, no paid API, no CRM, no third-party service.
- **The existing forms were reused -- no new or duplicate form, no field added or removed.**
  There are still exactly two `<form>` elements and every field id, `type` and `required`
  attribute is unchanged. Only two submit handlers and two button labels changed.
- **Which form feeds which CTA.** The hero **"CHECK AVAILABILITY"** button and the mobile
  **"Book Now"** bar both already call `openBooking()` and share the same `#booking` modal, so
  wiring `submitBooking()` covers both. The `#contact` section form ("Send an Enquiry") is wired
  through `submitEnquiry()`. All three CTAs now reach WhatsApp.
- **The WhatsApp number is reused, never invented.** `waNumber()` reads the number straight off
  the existing floating button (`document.querySelector('a.wa[href*="wa.me"]')`), so the forms
  physically cannot drift away from the button's number. The only literal is
  `WA_FALLBACK = '919031700464'`, used solely if that button is ever removed from the page, and it
  is the same number. Repo-wide there is still exactly one WhatsApp number: **919031700464**
  (`+919031700464` for `tel:`), appearing in `index.html` and `lib_contact.php` only.
- **Message contains only the fields the form actually has.** The booking form has no email and
  no message field, so its message has no `Email:` or `Message:` line; the contact form does, and
  those lines are omitted entirely when left blank so the message stays clean. No `Venue` /
  `Location` line was invented, because neither form has such a field. Example (contact form):
  `Hello Deepak Studios,` / `I would like to check availability for a photography service.` /
  `Name:` / `Phone:` / `Email:` / `Event Date: 05 Dec 2026` / `Shoot Type: Wedding Photography` /
  `Message:` / `Please let me know about availability and package details.`
- **Two details that make the message read professionally.** The `<select>` is reported using the
  visible option label ("Wedding Photography"), never the raw `value` ("wedding"). The
  `<input type="date">` value `2026-12-05` is formatted to `05 Dec 2026`; an unparseable value
  falls back to the raw string rather than crashing.
- **Encoding is correct for spaces, symbols and Hindi.** The whole message goes through a single
  `encodeURIComponent()` call, so spaces, `&`, `#`, `?`, `,`, `:` and Devanagari text are all
  percent-encoded. Line breaks become `%0A`, which WhatsApp renders as real newlines. `&` and `#`
  can no longer split the query string or start a fragment.
- **Opening behaviour is safe and platform-correct.** The link is `https://wa.me/<number>?text=...`,
  the same universal form the floating button already uses, so mobile opens the WhatsApp app when
  it is installed and desktop falls through to WhatsApp Web. It is opened with
  `window.open(url,'_blank')` followed by `w.opener = null` -- the same protection as
  `rel="noopener"` on the floating button, so the new tab cannot navigate this one. If a popup
  blocker stops the new tab, the code falls back to a same-tab navigation instead of silently
  doing nothing.
- **Native validation is untouched and still blocks the submit.** Every `required` attribute is
  still present and no `novalidate` was added, so an empty required field means the browser shows
  its normal validation message and the `submit` event never fires -- WhatsApp cannot open with an
  incomplete enquiry, and no misleading toast is shown. The `onsubmit="return submitXxx(event)"`
  wiring and the `return false` are unchanged, so there is no page reload.
- **UX.** The submit buttons now read **"Send Enquiry via WhatsApp"** and **"Request Callback via
  WhatsApp"**, and the toast says "Opening WhatsApp with your enquiry/booking details…". Only the
  button text changed -- the `class="btn btn-submit"` markup, position, size, colour and hover
  styling are byte-identical, and both labels still fit on one line inside the existing
  `width:100%` button.
- **Nothing else on the page changed.** The diff is confined to 3 hunks in `index.html`: 2 button
  labels and the 2 submit handlers. Removing the new block and restoring the 2 labels reproduces
  the previous `index.html` **byte-for-byte**. The hero, navbar, photography/cinematography/reels
  links, services, portfolio, reviews, footer, map, theme toggle, gallery/lightbox, booking modal
  markup, mobile bar and the floating WhatsApp button are all untouched. No other file was
  modified: `reels.php`, `cinematography.php`, `photography.php`, `wedding.php`, `prewedding.php`,
  `prewedding_photos.php`, `prewedding_videos.php`, `lib_gallery.php`, `a.php` - `e.php`,
  `lib_contact.php` and all deploy files are identical to 1.9.0.
- **Verified three independent ways.** (1) Real Chrome, driving the verbatim form markup with
  `requestSubmit()` and a real submit-button click: 37 checks, 0 failures -- empty required fields
  and a malformed email produce no `window.open` at all, a valid submit produces exactly one
  correctly-formed link, and Devanagari survives encoding and decoding byte-identically.
  (2) The generated links were compared byte-for-byte against URLs rebuilt independently in PHP
  using `encodeURIComponent()` semantics -- identical (342 and 699 bytes).
  (3) Structural diff check: 56 checks, 0 failures, confirming the field/validation/option counts
  and that the rest of the page is unchanged.

## 1.9.0 - 2026-09-29

- **The homepage "Call Now" and WhatsApp contact controls now appear on every page of the site.**
  Until now they existed only inside `index.html`, so a visitor landing directly on
  `reels.php`, `wedding.php`, an album page, etc. had no way to call or WhatsApp the studio
  without navigating back to the home page first. They are now shared by all 12 non-home pages.
- **New `lib_contact.php` — one shared renderer, no duplication.** The homepage's existing CSS +
  markup was moved *verbatim* into a new include exposing three idempotent functions
  (each `function_exists`-guarded, so double-inclusion can never duplicate the UI):
  - `ds_contact_head()` — the contact CSS, called from `<head>`
  - `ds_contact_header_call()` — the gold desktop pill, called inside `nav.rnav-desk`
  - `ds_contact_fab()` — the WhatsApp button + mobile bar + spacer, called before `</body>`
  Pages wire it with `require_once`, so the include emits **0 bytes at include time**.
- **Pixel-for-pixel identical to the homepage.** The WhatsApp `<a>` block is byte-identical to
  the homepage (601 bytes each), and the `.wa` geometry (fixed bottom-right `3.5rem` circle,
  `right:1.5rem`, `bottom:6rem` mobile / `2rem` desktop, `#25D366`, glow, 1.5s `ping` ripple,
  `scale(1.1)` hover) plus the mobile bar are unchanged. The only intentional difference is that
  the other pages inline the resolved dark-theme values the homepage gets from its variables
  (`#18181b` / `#fafafa` / `#09090b` for the two bar halves, `#D4AF37` for the phone icon),
  because those pages do not define `--card` / `--fg` / `--primary`; the rendered colours are
  identical, and gold `#D4AF37` is the same in both homepage themes.
- **Desktop gold "Call Now" pill on the premium pages only** — `reels.php`,
  `cinematography.php` and `photography.php` — placed inside `.rnav-desk` immediately after
  `Contact Us` and nowhere else (never in the mobile menu). The classic centred-header pages
  keep their existing single centred nav, so they get the WhatsApp button and the mobile bar
  only. This was an explicit decision.
- **Mobile bottom bar on every page** — a 50/50 `Call Now` / `Book Now` bar, fixed to the
  bottom, hidden at `min-width:768px`, with `padding-bottom:env(safe-area-inset-bottom)`, plus a
  `4.25rem` `.ds-contact-pad` spacer on `max-width:767px` so the fixed bar never covers the
  footer. `Call Now` uses the same `tel:+919031700464`; `Book Now` links to `index.html#contact`
  rather than calling `openBooking()`, because that modal is defined only on `index.html` — this
  keeps the button working everywhere without dragging a duplicate modal into 12 pages.
- **The homepage is completely untouched.** `index.html` is byte-identical to its previous
  state (no diff at all) and keeps its own inline controls and its own booking modal; it does
  not use `lib_contact.php`, so there is still exactly one WhatsApp button and one mobile bar
  on the home page. Same phone number (`+919031700464`) and same prefilled WhatsApp message as
  before, on every page.
- **Album pages inherit it for free.** `a.php` - `e.php` were **not modified** — they render
  through `lib_gallery.php`, which now includes the contact UI once, so all five albums get it
  without any duplication.
- **Purely additive change.** The diff is **+49 / -0 across 8 files** (7 renderer files plus the
  new `lib_contact.php`): no existing line was removed or rewritten anywhere. `index.html`,
  `README.md`, `config.php.example`, `Install.php`, `deploy.php`, `health.php`,
  `lib_migrations.php` and the data-only `lib_prewedding.php` were not modified.
- **Verified by rendering every page in isolation and diffing against the homepage** (350
  automated checks, 0 failures): exactly one WhatsApp button / `ping` / mobile bar /
  mobile `Call Now` / `Book Now` / spacer per page, no `openBooking()` dependency outside the
  home page, no duplicate modal, no duplicate CSS, correct pill placement (1 on premium, 0 on
  classic), all 17 protected files byte-identical to the previous release, and `php -l` clean on
  every changed file. This check also caught and fixed one real bug: the mobile bar's phone SVG
  had been copied with the wrong arc radius (`a9 9` instead of the homepage's `a1 1`).

## 1.8.2 - 2026-09-29

- **Home page navbar "Photography" now opens the Photography page.** 1.8.1 fixed the link on
  `reels.php` and `cinematography.php`, but `index.html` still sent Photography to the in-page
  `href="#portfolio"` anchor, so Home behaved differently from every other page. The `href` was
  changed to `photography.php` so all pages now behave identically:
  - `index.html` line 351 (desktop nav `nav.desk`) and line 377 (mobile menu `#mobmenu`)
  - `<li><a class="nl" href="#portfolio">Photography</a></li>` -> `<li><a class="nl" href="photography.php">Photography</a></li>`
  - `<a href="#portfolio">Photography</a>` -> `<a href="photography.php">Photography</a>`
- **Only the navbar link target changed.** The diff is exactly 2 lines in 1 file
  (2 removed / 2 added), both the single `href` attribute. No markup structure, `class="nl"`,
  CSS, JS, hero, services, reviews, contact, footer or any other content was touched.
- **The Portfolio section itself is untouched and still works.** `<section id="portfolio">`
  (line 416), the `Our <span class="gold">Portfolio</span>` heading, the `#filters` bar and the
  `#gallery` masonry container are all intact, so the anchor target still resolves for a direct
  `index.html#portfolio` visit. The remaining in-page anchors on the homepage are `#services`
  (Home) and `#contact` (Contact Us), both unchanged.
- **Photography page not modified.** `photography.php` still self-links with
  `.on` + `aria-current="page"`. `Cinematography` and `Reels` links on the homepage are unchanged.
- **No new page was created** - the existing `photography.php` is reused. `prewedding.php`,
  `wedding.php`, `lib_gallery.php`, `lib_prewedding.php`, `a.php` - `e.php`,
  `prewedding_photos.php` and `prewedding_videos.php` were not modified.

## 1.8.1 - 2026-09-29

- **Navbar "Photography" link now opens `photography.php` instead of the homepage portfolio
  section.** After 1.8.0 introduced the dedicated Photography page, the premium navbar on
  `reels.php` and `cinematography.php` was still pointing Photography at `index.html#portfolio`,
  so visitors landed on the homepage rather than the new portfolio. The `href` was changed to
  `photography.php` in all four places (desktop + mobile menu on both pages):
  - `reels.php` line 409 and 421
  - `cinematography.php` line 300 and 312
- **Link summary after this change:** `reels.php`, `cinematography.php` and `photography.php` all
  send Photography to `photography.php`; `index.html` keeps its own in-page `href="#portfolio"`
  nav (unchanged, as required).
- **Active/current state is correct on every page.** `photography.php` marks Photography active
  (`.on` + `aria-current="page"`, exactly 2 -- desktop + mobile); `reels.php` still marks Reels
  active and `cinematography.php` still marks Cinematography active. No page gained or lost an
  `aria-current` marker.
- **Nothing else changed.** The diff is exactly 4 lines (4 removed / 4 added), all of them the
  single `href` attribute -- no markup, CSS, JS, PHP data, hero, filter, modal or video changed.
  The homepage `Our Portfolio` section (`<section id="portfolio">`, heading
  `Our <span class="gold">Portfolio</span>`, `#gallery`) and its own `#portfolio` nav links are
  untouched. `photography.php`, `prewedding.php`, `wedding.php`, `lib_gallery.php`,
  `lib_prewedding.php`, `a.php` - `e.php`, `prewedding_photos.php` and `prewedding_videos.php`
  were not modified.

## 1.8.0 - 2026-09-29

- **New `photography.php` -- premium Photography portfolio.** A new page in the same luxury dark
  + gold studio language as `reels.php` and `cinematography.php`: the identical fixed premium
  navbar with **Photography** marked active (`.on` + `aria-current="page"`, gold hairline
  underline, scrolled `backdrop-filter` state, mobile burger + slide-down menu), plus a
  full-bleed hero with eyebrow "DEEPAK STUDIOS - PHOTOGRAPHY", heading "THE ART OF CAPTURING
  MOMENTS", subheading "Timeless photographs. Authentic emotions. Beautifully preserved.",
  supporting line, an "EXPLORE PHOTOGRAPHY" CTA that smooth-scrolls to the gallery, and a
  "SCROLL TO EXPLORE" indicator. Dark cinematic overlay and very subtle film grain; gold is used
  only as an accent (hairline rule, CTA, hover border, uppercase labels).
- **Replaceable hero image (single config point).** `$photoHeroImage` (plus `$photoHeroPos` and
  `$photoHeroPosMb`) live in one commented block at the top of `photography.php`; the path
  appears **exactly once** in the file, so swapping the picture is a one-line edit with no
  HTML/CSS/JS change. It currently points at the existing real studio photograph
  `photos/hero/deepakstudiosbokaro.webp` (1920x1080) -- no new binary was added. Desktop and the
  <=640px breakpoint reposition the hero via `object-position` so the subject survives the mobile
  crop. Served with `object-fit:cover`, `fetchpriority="high"`, explicit `width`/`height` (no
  layout shift) and no lazy-load.
- **Existing photography content is preserved, not rewritten.** The page only *reads* the
  existing folders and stores no copies: `photos/prewedding/` supplies the 6 Pre-Wedding
  photographs and `photos/wedding/a .. e/` supply the 5 Wedding albums. `wedding.php`,
  `prewedding_photos.php`, `prewedding_videos.php`, `prewedding.php`, `lib_gallery.php`,
  `lib_prewedding.php` and the album pages `a.php` - `e.php` are all untouched, and every album
  link still resolves. The folder-scan convention is unchanged, so the site owner still just
  uploads files (BaoTa / FTP) and they appear with no code edit and no deploy; the Hinglish
  upload instructions are retained on the page.
- **Category filter built from real content only.** `$CATS` is derived by scanning the data, and
  `$MAIN` only lists categories that actually hold items -- currently **ALL | PRE-WEDDING |
  WEDDING**. Empty categories (Engagement / Birthday / Anniversary) are deliberately not rendered,
  and a premium "COMING SOON" empty state remains as the fallback. Filters are `<button>`s with
  `preventDefault()` + `stopPropagation()`, so choosing a category never reloads the page.
  Counts: ALL 11 (6 photos + 5 albums), PRE-WEDDING 6, WEDDING 5.
- **Gallery + lightbox.** 4:3 cards with a large image, a soft shadow, a discreet gold zoom
  affordance on hover and a gentle `scale(1.04)` lift (no excessive animation). Photo cards open
  the same-page lightbox; album cards stay real links to their album pages. The lightbox keeps
  the existing gallery contract exactly -- `openLightbox()` / `closeLightbox()` globals, `#lightbox`
  and `#lb-img` ids, Esc / backdrop / X close -- now restyled premium, with scroll lock, scroll
  restore and a caption. Photo clicks are handled by delegation on `#grid`, so the viewer keeps
  working after a filter re-render.
- **Readable titles without hard-coding content.** The existing pre-wedding files are named after
  their YouTube IDs, so they are mapped to the same names used on `cinematography.php`; any newly
  uploaded file automatically falls back to "Pre-Wedding Photograph". Album cards show their live
  photo count read from disk.
- **No other files changed.** `reels.php`, `cinematography.php`, `prewedding.php`, `index.html`,
  `wedding.php`, `lib_gallery.php`, `lib_prewedding.php`, `a.php` - `e.php` and
  `prewedding_videos.php` were not modified.

## 1.7.1 - 2026-09-29

- **Fixed a fatal error that had killed the Pre-Wedding photo gallery.** `prewedding_photos.php`
  had two pre-existing defects that made the page return a blank/fatal response:
  1. Line 34 read `$total = count($photoshörl);` -- a corrupted variable name (a stray `ö` had
     been typed in), so `count()` received `null` and threw
     `TypeError: count(): Argument #1 ($value) must be of type Countable|array, null given`.
     Fixed to `$total = count($photos);`.
  2. Lines 84, 88, 107 and 108 called `$esc(...)` as if it were a closure, but
     `lib_prewedding.php` provides a **function** named `esc()`, not a `$esc` variable. The page
     therefore also threw `Error: Value of type null is not callable`. Fixed to `esc(...)`.
- The page now renders normally: `6 photos`, all 6 `.item` cards, and the existing
  `#lightbox` / `#lb-img` / `openLightbox()` / `closeLightbox()` viewer intact. No photo, album,
  link, style or behaviour was removed -- the diff is exactly 5 lines (1 variable name + 4 call
  sites).

## 1.7.0 - 2026-09-29

- **Six Pre-Wedding films added to the Cinematography hub.** Six rows were appended to the
  `$FILMS` array in `cinematography.php` with `'cat' => 'prewedding'`: `yvW6COhgwV0`
  ("The First Glimpse"), `Tcm2kFq0-sA` ("Before We Say Yes"), `YfccOo4h4Kk` ("Rooftop
  Confessions"), `LhjChNCyl9E` ("Golden Hour Walk"), `NEJ4yZ-cs6g` ("Frames of Forever") and
  `r0EflaC0a_U` ("Before the Bells").
- **Filter counts are now ALL 19 / PRE-WEDDING 6 / WEDDING 12 / ENGAGEMENT 1**, with BIRTHDAY and
  ANNIVERSARY still empty. The Pre-Wedding collection therefore no longer shows the
  "COMING SOON" placeholder; its now-unreachable `$EMPTYTEXT['prewedding']` copy was removed
  (BIRTHDAY and ANNIVERSARY copy untouched). Filter order and the ALL behaviour are unchanged.
- **Data-only change, no page rewrite.** Six lines added, one line removed, and no change to
  the CSS, the player or the filter logic. Each new row flows through the existing single source
  of truth, so its `data-v`, its `i.ytimg.com/vi/<id>/hqdefault.jpg` thumbnail and the
  `youtube-nocookie.com/embed/<id>?autoplay=1&rel=0` embed are all generated from the ID.
- **No duplicates, no regressions.** All 19 IDs are unique; every rendered card's thumbnail
  matches its own `data-v`; every card still opens the existing in-page 16:9 modal. Still no
  `youtube.com/watch`, no `youtu.be`, no Shorts links and no `target="_blank"`.
- **`prewedding.php` was not modified** (blob verified identical to `HEAD`); its own videos,
  data and functionality are untouched. `reels.php` and `index.html` were not modified either.

## 1.6.0 - 2026-09-29

- **Cinematography page rebuilt as a real films hub.** `cinematography.php` now matches the
  premium "Luxury + Royal" language introduced on `reels.php` in 1.5.0: the same fixed premium
  navbar (Cinematography marked active via `.on` + `aria-current="page"`, gold hairline underline,
  scrolled `backdrop-filter` state, mobile burger + slide-down menu) and a full-bleed cinematic
  hero (eyebrow "DEEPAK STUDIOS - CINEMATOGRAPHY", heading "THE ART OF CINEMATIC STORYTELLING",
  subheading "Every emotion. Every celebration. Every unforgettable frame.", supporting line, an
  "EXPLORE FILMS" CTA that smooth-scrolls to the films section, and a "SCROLL TO EXPLORE"
  indicator). Gold stays an accent only; the page remains deep charcoal with a vignette and fine
  grain. Desktop and the <=640px / <=380px breakpoints reposition the hero via `object-position`.
- **Replaceable hero image (single config point).** New `$cineHeroImage` (plus `$cineHeroPos` and
  `$cineHeroPosMb`) variables live in one commented block near the top of `cinematography.php`.
  Swapping the hero picture means editing only that one path -- no HTML, CSS or JS edits, and the
  path appears exactly once in the file. The image is `assets/images/cinematography-hero.jpg`
  (1920x1080, ~41 KB, `object-fit:cover`, `fetchpriority="high"`, not lazy-loaded; film card
  thumbnails stay `loading="lazy"`).
- **Old "Choose Your Occasion" folder UI removed.** The six folder tiles (Wedding, Pre-Wedding,
  Engagement, Anniversary, Birthday, Housewarming), the LIVE / COMING SOON badges and the
  "Browse Reels" ribbon are gone, replaced by six single-level category filters:
  `ALL | PRE-WEDDING | WEDDING | ENGAGEMENT | BIRTHDAY | ANNIVERSARY` (no sub-filters).
  Filters are `<button>` elements with `preventDefault()` + `stopPropagation()`, so selecting a
  category never triggers a navigation or reload.
- **13 films, single source of truth.** One `$FILMS` array holds 12 Wedding videos
  (`Cztd9udNlSo`, `JRoyOgR0Ckk`, `IyRO4IKhl8g`, `7ZubbaOMFxY`, `vTg374RrzU0`, `WmZLZMhtlOk`,
  `iAsgIhoLAQg`, `qZ3feBEq_d8`, `uVRB2XNLA1w`, `9I9NnX8wAkE`, `lOxZme5S_e4`, `BDBufwZhnK0`) and
  1 Engagement video (`oWrlcf2tb_4`). Counts: ALL 13, WEDDING 12, ENGAGEMENT 1, and
  PRE-WEDDING / BIRTHDAY / ANNIVERSARY 0. Every film is emitted exactly once with a
  `data-v` ID, its own `i.ytimg.com` thumbnail and a category tag; adding a film means adding
  one array row.
- **Premium empty states instead of dead filters.** PRE-WEDDING, BIRTHDAY and ANNIVERSARY render
  a centred "COMING SOON" placeholder with tailored copy from an `$EMPTYCOPY` map, so every
  filter stays clickable and honest.
- **16:9 in-page player.** Exactly one `#cmodal` plays `youtube-nocookie.com/embed/VIDEO_ID`
  with `autoplay=1&rel=0` (no `youtube.com/watch`, no `youtu.be`, no Shorts links, no
  `target="_blank"`), with X and Esc close, backdrop close, Prev/Next, arrow-key navigation, a
  film counter, scroll lock, and `frame.src = 'about:blank'` on close.
- **Player resolves cards from the live DOM.** `liveCards()` re-queries `#grid .fc[data-v]` and
  `openAt(0, el)` takes the clicked element, so indices stay correct after a filter re-render
  (the earlier snapshot approach could map every card to the first film). The page scroll
  position is captured only when the player first opens, so Prev/Next no longer overwrite the
  saved offset and break scroll restore.
- **No other files changed.** `reels.php`, `prewedding.php` and `index.html` were not modified.

## 1.5.0 - 2026-09-29

- **Reels page premium "Luxury + Royal" experience.** `reels.php` gained a fixed premium
  navbar (Deepak Studios brand mark; Home / Photography / Cinematography / Reels / Contact Us;
  Reels shown as the active item via `.on` + `aria-current="page"`; gold hairline underline;
  scrolled `backdrop-filter` state; mobile burger + slide-down menu) plus a new full-bleed
  cinematic hero: eyebrow "DEEPAK STUDIOS - PHOTOGRAPHY", heading "THE STORIES WE CAPTURE",
  subheading, supporting line, a "WATCH OUR REELS" CTA that smooth-scrolls to the reels section,
  and a "SCROLL TO EXPLORE" indicator. Gold is used only as an accent (hairline rule, underline,
  CTA); the page stays deep charcoal with a vignette and fine grain overlay. Desktop and the
  <=640px / <=380px breakpoints reposition the hero via `object-position`.
- **Replaceable hero image (single config point).** New `$reelsHeroImage` (plus `$reelsHeroPos`
  and `$reelsHeroPosMb`) PHP variables live in one clearly commented block near the top of
  `reels.php`. Swapping the hero picture means editing only that one path -- no HTML, CSS or JS
  edits, and the path is not repeated anywhere else in the file. The image lives at
  `assets/images/reels-hero.jpg` (1920x1080, ~39 KB, served with `object-fit:cover` and
  `fetchpriority="high"`, no lazy-load; reel card thumbnails remain `loading="lazy"`).
- **No functional changes.** Reels data, all 12 video IDs, the $MAIN / $SUB / $SUBLABEL /
  $CATLABEL mapping, filter + subfilter rendering, the 9:16 in-page modal, Prev/Next, X/Esc
  close, scroll lock and the YouTube nocookie embed are all untouched. No new libraries, no
  `target="_blank"`, no YouTube Shorts links, and `prewedding.php` / `index.html` were not
  modified.

## 1.4.0 - 2026-09-22

- **Cinematography page premium redesign.** `cinematography.php` rebuilt in the luxury
  dark + gold studio aesthetic: Home-matching fixed header/nav (desktop + mobile menu,
  Cinematography active), cinematic camera hero with overlays/vignette/gold grade
  (keeps "Cinematography" + "Cinematic Films & Short Films"), removed the
  "Choose Your Occasion" section, six cinematic image cards (2 LIVE links:
  `wedding.php`, `prewedding.php`; 4 Coming Soon tiles, no 404s) with gold SVG line
  icons, LIVE/COMING SOON glass badges, hover zoom/lift/glow, staggered scroll reveal,
  3/2/1-column responsive grid, lazy-loaded `w=1200` thumbnails. No PHP logic changes.

## 1.3.0 - 2026-09-15

- Added Google Reviews trust strip + "Read Our Google Reviews" CTA (see `Version.txt`).

## 1.2.0 - 2026-09-15

- **Hero upgrade.** The Google Ratings badge in the hero is now an internal `maps.app.goo.gl`
  badge (still 4.9-stars, still `target="_blank" rel="noopener"`), headline/subheading +
  supporting tagline refreshed, and the hero CTA row now has a gold primary
  "Book a Free Consultation" button plus a ghost-style secondary "View Our Work" button
  linking to the live `wedding.php` gallery.
- No functional or layout breaking changes; purely the headline/messaging + CTA layer.

## 1.0.7 - 2026-09-15

- Added wedding-photography placeholder pages `a.html`–`e.html` (cross-linked A–E nav).

## 1.0.7 - 2026-09-15

- **New Wedding Photography gallery system.** Added `wedding.php` (hub listing albums
  A–E with live photo counts) and five album pages `a.php`–`e.php` sharing the new
  `lib_gallery.php` renderer (auto-grid + click-to-enlarge lightbox).
- Photos are served from `photos/wedding/<letter>/` — the site owner just uploads
  image files there (BaoTa File Manager / FTP) and they appear automatically, with no
  code changes and no deployment. First-page instructions (in Hinglish) are shown
  on each album.
- `index.html`: the "Wedding Photography" service card now links to `wedding.php`.
  Removed the temporary `a.html`–`e.html` demo pages.

## 1.0.6 - 2026-09-15

- Reverted demo heading back to "Deepak Studios" (demo verification complete).

## 1.0.5 - 2026-09-15

- Re-verification push to confirm end-to-end auto-deploy: server was still on 1.0.3
  after `pdo_mysql` + `zip` extensions were installed. This commit exercises the full
  webhook pipeline (health.php now reports DB connected + all endpoints responsive).

## 1.0.4 - 2026-09-15

- Demo change to test the auto-deploy pipeline: hero heading in `index.html` changed to "Satya Studios".

## 1.0.3 - 2026-09-15

- `server-setup.sh` now REFUSES to run in a directory that is already a git repo with a different
  origin (prevents accidentally fetching/pushing the wrong project, e.g. a panel's existing repo).
- Also refuses non-empty directories (unless they are OUR repo). Use a fresh web-root dir instead.

## 1.0.2 - 2026-09-15

- Fixed `server-setup.sh` for the `curl | bash` one-liner: webhook secret is now auto-generated
  (openssl/urandom) instead of an interactive `read` prompt (stdin is consumed by `bash`, so prompts broke).
- The setup script now defaults to the current directory as web root (`PWD`) instead of `/var/www/html`.
- Added web-server ownership/permissions step so the webhook can write the web root (BaoTa/cPanel compatible).
- Migrations are non-fatal during setup (they auto-run on the first verified push).

## 1.0.1 - 2026-09-15

- Added `lib_migrations.php` — shared migration registry + idempotent runner, used by both `Install.php` and `deploy.php`.
- Upgraded `deploy.php` with dual deployment methods:
  - `archive` (default) — pure PHP deploy via GitHub zip download + file swap. Works on shared hosting (cPanel) with NO shell/git. Preserves `config.php` and `storage/`.
  - `git` — original shell-based fetch/reset for VPS/dedicated hosts.
- Migrations and health validation now run in-process (no shell dependency).
- Added `server-setup.sh` — one-time server bootstrap command.
- Added `index.html` — live Deepak Studios website.
- Updated `config.php.example` with `deploy.method`, `deploy.owner`, `deploy.repo`, `deploy.verify_ssl`.
- Deployment remains fully event-driven via GitHub webhooks — **no cron**.

## 1.0.0 - 2026-09-15

- Initial deployment architecture.
- Added `AGENTS.md` (AI project memory).
- Added `Version.txt` (1.0.0).
- Added `config.php.example` (template; `config.php` is git-ignored).
- Added `Install.php` with idempotent migration runner and `migrations` table.
- Added migration `001_initial_schema` (creates `migrations` + `deployment_logs` tables).
- Added `deploy.php` secure GitHub webhook deployment endpoint.
- Added `health.php` health-check endpoint.
- Added deployment locking (`storage/deploy.lock`, auto-expiring).
- Added deployment logging to database and file.
- Added `.gitignore` protecting `config.php`, storage, and runtime state.
- Deployment is event-driven via GitHub webhooks — **no cron**.

---

## Known Issues / Technical Debt

1. **Archive mode does not delete removed files:** `deploy.php` in `archive` mode copies/overwrites files from the GitHub zip but does not delete files that were removed from the repo (avoids accidentally deleting unrelated web-root files on shared hosting). If a file is removed from the repo, delete leftover copies from production manually once.
2. **Working-tree reset (git mode):** The `git` method uses `git reset --hard origin/main` — reliable but not zero-downtime for long-running requests.
3. **Git user for webhook (git mode):** The PHP/web server user must have rights to run `git` commands and write to the repository directory.
4. **Web server configuration:** The live site needs HTTPS and PHP 8+ with `pdo_mysql` enabled. Must be configured by the owner on the hosting provider.
5. **Database:** Migrations require a live MySQL/MariaDB accessible from the server; no DB runs locally.
6. **SSL on Windows test setups:** HTTPS downloads need a CA bundle; production Linux/shared hosts already ship one.

---

## Things AI Agents MUST NOT Change

- **NEVER modify already-executed migrations** (anything in the `migrations` table). Add a new migration instead.
- **NEVER commit `config.php`** or any real credentials.
- **NEVER rewrite git history or force-push**.
- **NEVER create branches**; all work goes directly to `main`.
- **NEVER modify deployment to use cron/scheduled polling.**
- **NEVER remove the webhook signature verification** from `deploy.php`.
- **NEVER hard-code credentials** anywhere.
- **NEVER log secrets** (passwords, keys, tokens).

---

## How to safely modify the project

1. Read this file, `Version.txt`, and `config.php` (if present) first.
2. Understand the current state before changing code.
3. Implement the change directly on `main`.
4. Syntax-check modified PHP files (`php -l`).
5. Update `AGENTS.md` (change log + any architecture notes).
6. Update `Version.txt` if it is a meaningful release (semver).
7. Commit to `main` and push to `origin main`.
8. The GitHub webhook automatically deploys to production. No manual/cron steps.
