=== zer0sh Site Health Monitor ===
Contributors: zer0sh
Tags: site health, performance, security, audit, health check
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Ultra lightweight WordPress plugin to monitor your website's performance, health, and security.

== Description ==

zer0sh Site Health Monitor is an open-source, ultra lightweight plugin that evaluates the overall state of your WordPress installation on a single screen: performance, core and extension updates, security vulnerabilities, and site-health fundamentals. It answers one question: *is my WordPress updated, supported, secure and well configured?*

* **PHP checks** — version and support status, memory limit, display_errors, required extensions, and PECL assumptions.
* **WordPress core** — installed version vs latest available.
* **Updates** — plugins and themes with pending updates.
* **Security** — HTTPS, XML-RPC, file editing (DISALLOW_FILE_EDIT), debug mode and security headers.
* **Site health** — cron and loopback checks.
* **Vulnerability scanning** — checks installed components against the public WPVulnerability database.

Diagnostic checks run locally on your own server with no accounts, no API keys, and no telemetry. To identify known security vulnerabilities in your installed components, the plugin queries the public WPVulnerability API. Results are cached with WordPress transients (12 hours by default) so it has negligible overhead.

== External services ==

This plugin connects to an external API to obtain security vulnerability information for your WordPress installation.

* **Service name:** WPVulnerability API (provided by ROBOTSTXT, Santa Coloma de Gramenet, Barcelona, Spain).
* **Service URL:** https://www.wpvulnerability.net/
* **What data is sent and when:** Only software identifiers (slugs) and installed version numbers of WordPress core, installed plugins, and active themes are transmitted. Data is sent via HTTPS GET requests only when an administrator triggers a manual scan or when a scheduled diagnostic scan runs.
* **Privacy and data protection:** No personal data, user credentials, passwords, cookies, or site content are transmitted or stored.
* **Terms of Service:** https://www.robotstxt.es/legal/
* **Privacy Policy:** https://www.wpvulnerability.com/privacy/

== Installation ==

1. Upload the `zer0sh-site-health-monitor` folder into `/wp-content/plugins/` (or install the ZIP via the WordPress Plugins screen).
2. Activate the plugin through the *Plugins* screen in WordPress.
3. Go to **Health Monitor** in the admin sidebar and click **Scan now**.

Requirements: WordPress 6.5+ and PHP 7.4+.

== Frequently Asked Questions ==

= Does this plugin collect personal data from my site? =

No. No personal data or sensitive information leaves your installation. The plugin only queries the public WPVulnerability API with software slugs and version numbers to detect known security advisories. No user accounts, no API keys, and no telemetry.

= Does it slow down my site? =

Negligible. Results are cached in WordPress transients (12 hours by default) and the front end of your website is never touched.

= Is it available in my language? =

The plugin is properly internationalized and ready to receive community translations through translate.wordpress.org.

== Changelog ==

= 0.1.3 =
* Renamed plugin to zer0sh Site Health Monitor.
* Updated text domain and internationalization for translate.wordpress.org.
* Added documentation for WPVulnerability external service.
* Aligned descriptions and removed unused references.

= 0.1.2 =
* First public release.
* Initial set of health, security and performance checks.
* Internationalization ready (English + Spanish).
