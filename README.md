# WordPress Local Development (Docker)

A local WordPress development environment using Docker Compose. One command starts WordPress, MySQL, and phpMyAdmin. WP-CLI is available on demand.

Works on Apple Silicon (M1/M2/M3/M4) and Intel Macs.

## Prerequisites

- [Docker Desktop for Mac](https://www.docker.com/products/docker-desktop/) (includes Docker Compose V2)
- Docker Desktop running before you start the stack
- [Node.js](https://nodejs.org/) 18+ (for theme SCSS build and hot reload)

Verify your setup:

```bash
docker --version
docker compose version
```

You should see Docker Compose V2 (`docker compose`, not `docker-compose`).

## Project structure

```text
.
├── docker-compose.yml   # Service definitions
├── .env                 # Database credentials (local defaults)
├── .gitignore
├── wordpress/           # WordPress files (mounted into the container)
└── README.md
```

## Quick start

From the project root:

```bash
docker compose up -d
cd wp-content/themes/messcut && npm run dev
```

First startup may take a minute while MySQL initializes and becomes healthy.

Open WordPress: **http://localhost:8080**

On first visit, complete the WordPress installation wizard in the browser.

## Stop

Stop containers but keep data:

```bash
docker compose down
```

## Remove everything

Stop containers **and** delete the MySQL volume (all database data is lost):

```bash
docker compose down -v
```

To also remove WordPress files from `./wordpress`:

```bash
rm -rf wordpress/*
```

## URLs

| Service     | URL                        |
|-------------|----------------------------|
| WordPress   | http://localhost:8080      |
| BrowserSync | http://localhost:3000      |
| phpMyAdmin  | http://localhost:8081      |

## Theme development (SCSS + hot reload)

Theme source lives at `wp-content/themes/messcut/`. Docker bind-mounts that folder into the container, so PHP and compiled CSS changes appear without manual copying.

**Edit styles in** `assets/scss/` (not `assets/css/main.css` directly). Compiled output is `assets/css/main.css`, which WordPress enqueues.

```bash
# Terminal 1 — WordPress stack
docker compose up -d

# Terminal 2 — SCSS watch + BrowserSync
cd wp-content/themes/messcut
npm install
npm run dev
```

Open **http://localhost:3000** (or :8080 — the theme injects the BrowserSync client when `npm run dev` is running):

- Edit `assets/scss/**` → CSS injects without full page reload
- Edit `**/*.php` or `theme.json` → full page reload

Production build (commit compiled CSS):

```bash
cd wp-content/themes/messcut
npm run build
```

If the theme folder is missing inside `./wordpress` on first setup, bootstrap once:

```bash
cp -R wp-content/themes/messcut wordpress/wp-content/themes/
```

## Default credentials

From `.env`:

| Setting              | Value      |
|----------------------|------------|
| Database name        | `wordpress` |
| Database user        | `wordpress` |
| Database password    | `wordpress` |
| MySQL root password  | `root`     |

phpMyAdmin connects automatically using the `wordpress` user.

WordPress admin credentials are set during the browser installation wizard.

## WP-CLI

WP-CLI does not start with `docker compose up -d`. Run commands on demand:

```bash
# List installed plugins
docker compose run --rm wpcli plugin list

# Show WordPress version
docker compose run --rm wpcli core version

# Search and replace URLs in the database
docker compose run --rm wpcli search-replace 'http://old-domain.com' 'http://localhost:8080'

# Dry-run before replacing
docker compose run --rm wpcli search-replace 'http://old-domain.com' 'http://localhost:8080' --dry-run

# Install a plugin
docker compose run --rm wpcli plugin install wordpress-seo --activate

# Flush rewrite rules
docker compose run --rm wpcli rewrite flush
```

## Useful Docker commands

```bash
# Start in foreground (see logs)
docker compose up

# View logs for all services
docker compose logs -f

# View logs for one service
docker compose logs -f wordpress

# Check service status
docker compose ps

# Restart a single service
docker compose restart wordpress

# Open a shell inside the WordPress container
docker compose exec wordpress bash

# Validate compose file syntax
docker compose config
```

## Import an existing WordPress site

If you already have WordPress files (for example, `wp-content/` with a custom theme):

1. Start the stack and complete the WordPress install wizard once (creates `wp-config.php` and base files):

   ```bash
   docker compose up -d
   ```

2. Stop WordPress to avoid file conflicts:

   ```bash
   docker compose stop wordpress
   ```

3. Copy your existing files into `./wordpress`:

   ```bash
   # Example: copy theme from repo root into the Docker wordpress directory
   cp -R wp-content/themes/messcut wordpress/wp-content/themes/

   # Or copy the full wp-content directory
   cp -R wp-content/* wordpress/wp-content/
   ```

4. Import the database (see next section).

5. Update URLs in the database:

   ```bash
   docker compose run --rm wpcli search-replace 'https://production-site.com' 'http://localhost:8080'
   ```

6. Start WordPress again:

   ```bash
   docker compose start wordpress
   ```

## Import a SQL dump

With the stack running:

```bash
# Import into the wordpress database
docker compose exec -T mysql mysql -u wordpress -pwordpress wordpress < backup.sql

# Or using root
docker compose exec -T mysql mysql -u root -proot wordpress < backup.sql
```

Via phpMyAdmin:

1. Open http://localhost:8081
2. Select the `wordpress` database
3. Go to **Import** and upload your `.sql` file

After importing, run a URL search-replace if the dump came from another domain:

```bash
docker compose run --rm wpcli search-replace 'https://old-site.com' 'http://localhost:8080'
```

## Export a database

```bash
# Export to a file (wordpress user needs --no-tablespaces on MySQL 8+)
docker compose exec -T mysql mysqldump -u wordpress -pwordpress --no-tablespaces wordpress > backup.sql

# Or with root (no extra flags needed)
docker compose exec -T mysql mysqldump -u root -proot wordpress > backup.sql
```

## Search and replace URLs with WP-CLI

Always dry-run first:

```bash
docker compose run --rm wpcli search-replace 'https://old-site.com' 'http://localhost:8080' --dry-run
```

Then apply:

```bash
docker compose run --rm wpcli search-replace 'https://old-site.com' 'http://localhost:8080'
```

For serialized PHP data (common in WordPress options), WP-CLI handles serialization automatically. Add `--all-tables` to search every table:

```bash
docker compose run --rm wpcli search-replace 'https://old-site.com' 'http://localhost:8080' --all-tables
```

## Troubleshooting

**Port already in use**

Change host ports in `docker-compose.yml` (for example, `8082:80` for WordPress).

**Permission errors in `./wordpress`**

WP-CLI runs as `www-data` (UID 33). If file permissions break, fix ownership:

```bash
docker compose run --rm --user root wpcli chown -R www-data:www-data /var/www/html
```

**MySQL not ready**

WordPress waits for MySQL healthchecks. Check MySQL logs:

```bash
docker compose logs mysql
```

**Reset and start fresh**

```bash
docker compose down -v
rm -rf wordpress/*
docker compose up -d
```

## Services overview

| Service    | Image                      | Port  | Notes                          |
|------------|----------------------------|-------|--------------------------------|
| wordpress  | `wordpress:php8.3-apache`  | 8080  | Mounts `./wordpress` + theme bind-mount |
| mysql      | `mysql:8.4`                | —     | Data in `messcut_mysql_data`   |
| phpmyadmin | `phpmyadmin:latest`        | 8081  | Auto-connects to MySQL         |
| wpcli      | `wordpress:cli-php8.3`     | —     | On-demand via `compose run`  |
