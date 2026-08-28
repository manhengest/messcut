# MESSCUT — Agent Guide

WordPress marketing site for **MESSCUT** (brand strategy agency). Hybrid classic theme + `theme.json` v3. Ukrainian default + English via Polylang. Content model from the client brief; visual polish is iterative.

## Source of truth (IMPORTANT)

Theme source lives at **`wp-content/themes/messcut/`**.

Docker bind-mounts the theme into the container (`./wp-content/themes/messcut` → `/var/www/html/wp-content/themes/messcut`), so PHP and compiled CSS edits are live without copying. If the theme folder is missing inside `./wordpress` on first setup, bootstrap once:

```bash
cp -R wp-content/themes/messcut wordpress/wp-content/themes/
```

Do **not** edit only `wordpress/wp-content/themes/messcut/` and forget the repo source — those changes are easy to lose.

**Styles:** edit `assets/scss/`; run `npm run dev` in the theme for SCSS watch + BrowserSync hot reload (http://localhost:3000). Commit compiled `assets/css/main.css` via `npm run build`.

## Stack

| Area | Choice |
|------|--------|
| Runtime | Docker: WordPress PHP 8.3 + Apache, MySQL 8.4, phpMyAdmin |
| Theme | Classic PHP templates + `theme.json` v3 (not FSE / Site Editor) |
| Styles | SCSS (`assets/scss/`) → compiled `assets/css/main.css` (Dart Sass + BrowserSync) |
| Fields | ACF Pro + Local JSON (`acf-json/`) |
| i18n | gettext text domain `messcut` + Polylang (UK/EN content) |
| Forms | REST `POST /wp-json/messcut/v1/lead` (nonce + honeypot) |
| SEO | Yoast (optional); no page builders |

Requires PHP **8.2+**, WP **6.7+** (tested up to 7.0). Prefix: `messcut_`. Text domain: `messcut`.

## Commands

```bash
docker compose up -d          # WP http://localhost:8080 — phpMyAdmin :8081
docker compose down
cd wp-content/themes/messcut && npm run dev   # SCSS watch + BrowserSync :3000
cd wp-content/themes/messcut && npm run build # compile CSS before commit
./scripts/deploy.sh --dry-run # list theme files; upload nothing
./scripts/deploy.sh           # build CSS + upload theme to Hosting Ukraine
./scripts/deploy.sh discover  # find remote WordPress root (sets nothing)
# Cursor: /deploy — review local diff (blocks on must/should fix), build, push, then hosting
docker compose run --rm wpcli plugin list
docker compose run --rm wpcli rewrite flush
docker compose run --rm wpcli theme activate messcut
```

Env defaults: see `.env` (`wordpress` / `wordpress` / DB `wordpress`). Deploy secrets: `.env.deploy` (gitignored).

## Project layout

```
wp-content/themes/messcut/     # THEME SOURCE (edit here)
  assets/scss/                 # SCSS source (edit styles here)
  assets/css/main.css          # Compiled CSS (enqueue target; run npm run build)
  assets/img/logo-*.svg|png    # Black (header) / white (footer)
  acf-json/                    # ACF field group sync
  inc/                         # setup, enqueue, cpt, acf, forms, i18n, helpers, seed*
  template-parts/              # header, footer, forms, sections
  theme.json                   # Editor palette / fonts / gradients
wordpress/                     # Docker WP root (generated + synced theme)
scripts/deploy.sh              # Theme upload to Hosting Ukraine (SSH + tar)
.env.deploy.example            # Deploy env template (copy to .env.deploy)
Project Requirements.md        # Client brief / content source (local, gitignored)
README.md                      # Docker + WP-CLI runbook
```

### CPTs

| Type | Archive slug | Notes |
|------|--------------|--------|
| `case_study` | `/cases/` | Case pages |
| `service` | `/services/` | Service pages |
| `article` | `/articles/` | Blog foundation only |
| `lead` | — | Private UI for form submissions |

Key pages: front-page, approach (`page-approach.php`), services hub (`page-poslugy.php`).

## Brand tokens

Keep CSS variables and `theme.json` in sync.

**Fonts (Google Fonts, Cyrillic-capable)**

| Role | Family | Use |
|------|--------|-----|
| Display | Playfair Display | Titles / headings (Cyrillic via Google Fonts) |
| UI / Body | Golos Text | Nav, body copy (Cyrillic via Google Fonts) |
| Mono | IBM Plex Mono | Buttons, eyebrows, labels, metadata |

**Surfaces:** CSS mesh + grain utilities in `assets/scss/abstracts/_surfaces.scss` — `.surface--gradient-light` (page/sections) and `.surface--gradient-dark` (proof chapter, footer, nav overlay). `.surface--dots` for dotted grids (cases). Do not commit client gradient bitmap refs.

**Canvas:** `--canvas-ivory` / `#f7f9f7` (not pure white). Sync `theme.json` background.

Visual patterns follow a light Sanity-inspired system: Playfair Display titles, pill CTAs, colorimetric depth (hairline borders, no drop shadows), brown hover states. Accent colors unchanged.

**Colors** (sampled from brand board)

| Token | Hex | Role |
|-------|-----|------|
| `--color-black` | `#000000` | Base / text / primary CTA |
| `--color-white` | `#ffffff` | Cards / inputs |
| `--canvas-ivory` | `#f7f9f7` | Default page canvas |
| `--color-brown` | `#3a261f` | Backdrop (footer, hovers) |
| `--color-accent` | `#c7f2e1` | Turquoise fills / surfaces |

Gradients: `--gradient-brand`, `--gradient-accent`, `--gradient-backdrop`. Mint is for **fills**, not low-contrast text links — use brown/black for interactive text.

**Logos:** `assets/img/logo-black.svg` (header), `logo-white.svg` (footer). Prefer SVG; PNG fallbacks exist. Render via `messcut_render_logo( $variant )`.

## Production deploy (Hosting Ukraine)

Uploads **only** `wp-content/themes/messcut/` over SSH. Does not touch WordPress core, plugins, `uploads/`, `wp-config.php`, or the database. Uses tar-over-SSH (not rsync): macOS `openrsync` is incompatible with GNU rsync on this host.

**One-time setup**

```bash
cp .env.deploy.example .env.deploy
# fill DEPLOY_PASS; leave DEPLOY_PATH empty
./scripts/deploy.sh discover
# set DEPLOY_PATH to the printed WordPress root (directory that contains wp-content/)
```

Current production path: `/home/ur624933/messcut.com/www`. Host/user live in `.env.deploy.example`. Never commit `.env.deploy`.

**Each release**

In Cursor, run **`/deploy`**: reviews the local diff (Bugbot), **stops** on must-fix or should-fix, then `npm run build`, commits/pushes, and `./scripts/deploy.sh`. Command lives at `.cursor/commands/deploy.md`.

Or by hand:

```bash
./scripts/deploy.sh --dry-run
./scripts/deploy.sh
```

The script runs `npm run build` first, then swaps the remote theme directory (old tree removed). Excludes `node_modules/`, `assets/scss/`, `*.map`, `browser-sync.config.js`. After CPT or rewrite changes, save **Settings → Permalinks** in WP admin (no WP-CLI on this host). Do not run `inc/seed*.php` on production.

## Conventions

- **No page builders.** Templates + ACF + small JS.
- Escape output (`esc_html`, `esc_url`, `esc_attr`); sanitize REST input.
- Enqueue only via `wp_enqueue_scripts`; version with `filemtime()`.
- Edit SCSS in `assets/scss/`; do not hand-edit `assets/css/main.css`.
- `load_theme_textdomain( 'messcut', … )` on **`init`** (WP 6.7+), not `after_setup_theme`.
- UI strings: Ukrainian msgid + `__()` / `esc_html_e()`; content translation via Polylang.
- ACF field groups: edit in WP, save to `acf-json/` — commit JSON.
- Seed helpers (`inc/seed*.php`) are for local bootstrap — do not treat as production migration.
- Commits: conventional (`feat`, `fix`, `chore`, …).

## Do not

- Put secrets in the repo (beyond local `.env` defaults). `.env.deploy` stays gitignored.
- Deploy Docker `wordpress/`, plugins, uploads, or the local database to production.
- Introduce React/Vue/builders for this theme.
- Use Inter / Roboto / system stack as primary fonts.
- Use the old gold accent (`#c8a96e`) — replaced by turquoise.
- Edit Docker theme copy without syncing back to `wp-content/themes/messcut/`.

## Docs & libraries

Use **Context7** for current WP / ACF / Polylang APIs — do not rely on stale training data alone.

Further reading:

- [README.md](README.md) — Docker, import/export, WP-CLI
- `.env.deploy.example` + `scripts/deploy.sh` — Hosting Ukraine theme upload
- [Project Requirements.md](Project%20Requirements.md) — pages, case/service structure, copy (local; gitignored)
- [.cursor/plans/messcut_wp_theme_9dd8af69.plan.md](.cursor/plans/messcut_wp_theme_9dd8af69.plan.md) — architecture & phases
