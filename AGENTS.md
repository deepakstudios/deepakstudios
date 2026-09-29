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
1.11.0
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
