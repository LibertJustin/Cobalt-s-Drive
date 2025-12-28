# Cobalt's Drive

A self-made drive — a lightweight PHP file manager for simple file storage and sharing on a LAMP stack.

## Overview

Cobalt's Drive is a small PHP-based file storage web app designed for personal and small-team use. It provides a simple web UI for uploading, organizing, downloading, and managing files and folders. The project is intentionally minimal and easy to deploy to any standard PHP+MySQL (or file-based) web hosting environment.

## Features

- Simple authentication and session handling
- Create folders and upload files
- Download and delete files
- Import and basic admin utilities in `functions/`
- Configurable storage directory (`DATA/`)

## Requirements

- PHP 7.4+ (or newer)
- A web server (Apache, Nginx) with PHP support
- Optional: MySQL/MariaDB if you plan to extend database-backed features

## Quick Install

1. Clone or copy the repository to your web root (for example `/var/www/html`).
2. Ensure the `DATA/` directory is writable by the webserver user:

```bash
sudo chown -R www-data:www-data /var/www/html/DATA
sudo chmod -R 750 /var/www/html/DATA
```

3. Configure database settings if used: edit [db.php](db.php) to match your environment.
4. Access the app in your browser at the server's host (e.g., `http://localhost/`).

## Configuration

- Set any DB credentials inside [db.php](db.php).
- Customize messages and sessions via [functions/set_session_message.php](functions/set_session_message.php).
- Add or modify user creation logic using [functions/create_user.php](functions/create_user.php).

## Usage

- Visit [index.php](index.php) to reach the main UI.
- Use [login.php](login.php) to authenticate, then manage files through [files.php](files.php).
- Create folders with [functions/create_dir.php](functions/create_dir.php) and import files via [functions/import.php](functions/import.php).
- Download files using [download.php](download.php).

## Notable Files and Folders

- [index.php](index.php) : App entry / dashboard
- [login.php](login.php) : Login screen
- [files.php](files.php) : File browser and manager
- [db.php](db.php) : Database connection (if used)
- [functions/](functions/) : Reusable server-side actions (create_user, create_dir, import, etc.)
- [DATA/](DATA/) : Storage root for uploaded files and folders
- [universal_header.php](universal_header.php) : Shared header used across pages

## Security Notes

- Run behind HTTPS in production (configure your webserver with TLS).
- Harden `DATA/` access: do not allow direct directory listing from the webserver. Use an `.htaccess` or webserver rules to deny public browsing and serve files through `download.php`.
- Sanitize and validate all user inputs if you extend the app. Existing code is minimal and may need hardening for multi-user or public deployments.

## Contributing

Contributions are welcome. Open an issue describing your change and submit a pull request. For non-trivial changes, please include tests or a short demo of the feature.

## Troubleshooting

- Permission errors: re-run the `chown`/`chmod` commands above and ensure PHP runs as the configured user.
- Database connection issues: confirm credentials in [db.php](db.php) and that your DB is reachable from the webserver.

## License

This repository does not include a license file by default. Add a `LICENSE` file if you want to grant reuse rights (MIT, Apache-2.0, etc.).

## Contact

If you'd like help customizing this README or the app (branding, screenshots, features), open an issue or reply here.
