# DataLabAcademy

DataLabAcademy is an education platform prototype for data, analytics and practical learning content. The repository contains the PHP pages that power the DataLab experience together with HTML theme demos and shared frontend assets.

## What is in this repository

- DataLab pages such as `index.php`, `about.php`, course details, lessons, cart and checkout flows
- PHP includes and API endpoints under `includes/` and `api/`
- Static HTML demos for course, dashboard, account and component views
- Shared styles, scripts and media under `assets/`
- Apache rewrite and security rules in `.htaccess`

## Technology

- PHP 8.2 with Apache routing
- HTML, CSS, SCSS and JavaScript
- Bootstrap and jQuery frontend dependencies
- MySQL/PDO integration for the dynamic PHP flows

## Local preview

The repository includes `start-local-server.bat`, which runs PHP's built-in server at `127.0.0.1:8080`:

    php -S 127.0.0.1:8080

For the complete extensionless PHP routes and rewrite rules, use Apache (for example through XAMPP) with `.htaccess` enabled. The built-in PHP server is useful for a basic local preview, but it does not apply Apache rewrite rules.

Dynamic pages require a local MySQL/PDO configuration. Keep credentials and local configuration outside the public repository; database seed data is environment-specific.

## Licensing and third-party assets

The static demo pages and shared styles identify part of this repository as the **Histudy** education template and contain **RainbowIT** references. No project-level license file is present. Treat those template files, images, fonts and vendor assets as governed by their original terms; their presence here does not grant reuse rights.

Project-specific DataLab code should be separated from those assets before a repository-wide open-source license is considered.

## Contributing

This is an evolving learning project. Before opening an issue or pull request, please check the repository guidance in [AGENTS.md](AGENTS.md) and [CLAUDE.md](CLAUDE.md).
