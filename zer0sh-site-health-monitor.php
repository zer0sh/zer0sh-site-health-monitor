<?php
/**
 * Plugin Name: zer0sh Site Health Monitor
 * Plugin URI:  https://github.com/zer0-sh/zer0sh-site-health-monitor
 * Description: Ultra lightweight WordPress plugin to diagnose site health, security vulnerabilities, and configuration status.
 * Version:     0.1.3
 * Requires at least: 6.5
 * Requires PHP: 7.4
 * Author:      zer0sh
 * Author URI:  https://profiles.wordpress.org/zer0sh/
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: zer0sh-site-health-monitor
 * Domain Path: /languages
 *
 * @package Zer0shSiteHealthMonitor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'ZER0SH_SHM_PLUGIN_FILE' ) ) {
	define( 'ZER0SH_SHM_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'ZER0SH_SHM_VERSION' ) ) {
	define( 'ZER0SH_SHM_VERSION', '0.1.3' );
}

$zer0sh_shm_autoload = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $zer0sh_shm_autoload ) ) {
	require_once $zer0sh_shm_autoload;
}

unset( $zer0sh_shm_autoload );

add_action( \Zer0sh\SiteHealthMonitor\Cron::HOOK, 'zer0sh_shm_run_scheduled_scan' );
add_filter( 'cron_schedules', 'zer0sh_shm_cron_schedules' );

register_activation_hook( __FILE__, 'zer0sh_shm_activate' );
register_deactivation_hook( __FILE__, 'zer0sh_shm_deactivate' );

if ( is_admin() ) {
	zer0sh_shm_init_admin();
}

/**
 * Las traducciones de `zer0sh-site-health-monitor` se cargan automáticamente según el slug
 * del plugin (WordPress 4.6+ / 6.7+: carga just-in-time por textdomain y
 * traducciones del directorio de idiomas), por lo que no se necesita una
 * llamada manual a `load_plugin_textdomain()`. El filtro de fallback
 * regional español se conserva más abajo.
 */

/**
 * Redirige los archivos `.mo` de cualquier locale regional de español
 * (`es_CO`, `es_MX`, `es_AR`…) al genérico `es_ES` cuando no existe un
 * `.mo` específico para esa región.
 *
 * Con la carga just-in-time de traducciones (WordPress 6.7+), el registro de
 * textdomains (`WP_Textdomain_Registry`) resuelve el `.mo` por locale exacto
 * (`zer0sh-site-health-monitor-es_CO.mo`) y no aplica fallback regional ni pasa por el filtro
 * `plugin_locale`. Este filtro `load_textdomain_mofile` (aplicado siempre,
 * dentro de `load_textdomain()`) sustituye la región por `es_ES` y mantiene
 * el resto de idiomas intactos.
 *
 * @param string $mofile Ruta del archivo .mo que WordPress va a cargar.
 * @param string $domain Text domain del filtro.
 *
 * @return string Ruta .mo ajustada.
 */
function zer0sh_shm_mofile_spanish_fallback( string $mofile, string $domain ): string {
	if ( 'zer0sh-site-health-monitor' !== $domain || ! preg_match( '/zer0sh-site-health-monitor-es_[A-Z]{2}\.mo$/i', $mofile ) ) {
		return $mofile;
	}

	if ( is_readable( $mofile ) ) {
		return $mofile;
	}

	return preg_replace( '/zer0sh-site-health-monitor-es_[A-Z]{2}\.mo$/i', 'zer0sh-site-health-monitor-es_ES.mo', $mofile );
}

add_filter( 'load_textdomain_mofile', 'zer0sh_shm_mofile_spanish_fallback', 10, 2 );

/**
 * Inicializa el panel de administración de zer0sh Site Health Monitor.
 *
 * Se ejecuta al cargar el plugin dentro del panel: el menú se registra en
 * `admin_menu`, que en admin.php ya se ha disparado cuando corre admin_init.
 *
 * @return void
 */
function zer0sh_shm_init_admin(): void {
	$zer0sh_shm_admin = new \Zer0sh\SiteHealthMonitor\Admin\AdminPage(
		zer0sh_shm_runner(),
		zer0sh_shm_scan_store(),
		zer0sh_shm_cron(),
		zer0sh_shm_settings_store(),
		zer0sh_shm_ignored_store()
	);

	$zer0sh_shm_admin->register();
}

/**
 * Devuelve el checker con todos los checks locales registrados.
 *
 * @return \Zer0sh\SiteHealthMonitor\Checker
 */
function zer0sh_shm_checker(): \Zer0sh\SiteHealthMonitor\Checker {
	static $zer0sh_shm_checker = null;

	if ( null === $zer0sh_shm_checker ) {
		$zer0sh_shm_checker = new \Zer0sh\SiteHealthMonitor\Checker();

		foreach ( \Zer0sh\SiteHealthMonitor\Checks\LocalChecks::all() as $zer0sh_shm_check ) {
			$zer0sh_shm_checker->add_check( $zer0sh_shm_check );
		}
	}

	return $zer0sh_shm_checker;
}

/**
 * Devuelve el escáner del sitio.
 *
 * @return \Zer0sh\SiteHealthMonitor\Scanner
 */
function zer0sh_shm_scanner(): \Zer0sh\SiteHealthMonitor\Scanner {
	static $zer0sh_shm_scanner = null;

	if ( null === $zer0sh_shm_scanner ) {
		$zer0sh_shm_scanner = new \Zer0sh\SiteHealthMonitor\Scanner( zer0sh_shm_checker(), new \Zer0sh\SiteHealthMonitor\VulnerabilityClient() );
	}

	return $zer0sh_shm_scanner;
}

/**
 * Devuelve el almacén del último resultado.
 *
 * @return \Zer0sh\SiteHealthMonitor\ScanStore
 */
function zer0sh_shm_scan_store(): \Zer0sh\SiteHealthMonitor\ScanStore {
	static $zer0sh_shm_scan_store = null;

	if ( null === $zer0sh_shm_scan_store ) {
		$zer0sh_shm_scan_store = new \Zer0sh\SiteHealthMonitor\ScanStore();
	}

	return $zer0sh_shm_scan_store;
}

/**
 * Devuelve el almacén de los ajustes del plugin.
 *
 * @return \Zer0sh\SiteHealthMonitor\SettingsStore
 */
function zer0sh_shm_settings_store(): \Zer0sh\SiteHealthMonitor\SettingsStore {
	static $zer0sh_shm_settings_store = null;

	if ( null === $zer0sh_shm_settings_store ) {
		$zer0sh_shm_settings_store = new \Zer0sh\SiteHealthMonitor\SettingsStore();
	}

	return $zer0sh_shm_settings_store;
}

/**
 * Devuelve el almacén de los hallazgos ignorados.
 *
 * @return \Zer0sh\SiteHealthMonitor\IgnoredStore
 */
function zer0sh_shm_ignored_store(): \Zer0sh\SiteHealthMonitor\IgnoredStore {
	static $zer0sh_shm_ignored_store = null;

	if ( null === $zer0sh_shm_ignored_store ) {
		$zer0sh_shm_ignored_store = new \Zer0sh\SiteHealthMonitor\IgnoredStore();
	}

	return $zer0sh_shm_ignored_store;
}

/**
 * Devuelve el enviador de informes por correo.
 *
 * @return \Zer0sh\SiteHealthMonitor\MailReport
 */
function zer0sh_shm_mailer(): \Zer0sh\SiteHealthMonitor\MailReport {
	static $zer0sh_shm_mailer = null;

	if ( null === $zer0sh_shm_mailer ) {
		$zer0sh_shm_mailer = new \Zer0sh\SiteHealthMonitor\MailReport();
	}

	return $zer0sh_shm_mailer;
}

/**
 * Devuelve el gestor del escaneo programado.
 *
 * @return \Zer0sh\SiteHealthMonitor\Cron
 */
function zer0sh_shm_cron(): \Zer0sh\SiteHealthMonitor\Cron {
	static $zer0sh_shm_cron = null;

	if ( null === $zer0sh_shm_cron ) {
		$zer0sh_shm_cron = new \Zer0sh\SiteHealthMonitor\Cron();
	}

	return $zer0sh_shm_cron;
}

/**
 * Devuelve el orquestador del flujo de escaneo.
 *
 * @return \Zer0sh\SiteHealthMonitor\ScanRunner
 */
function zer0sh_shm_runner(): \Zer0sh\SiteHealthMonitor\ScanRunner {
	static $zer0sh_shm_runner = null;

	if ( null === $zer0sh_shm_runner ) {
		$zer0sh_shm_runner = new \Zer0sh\SiteHealthMonitor\ScanRunner(
			zer0sh_shm_scanner(),
			zer0sh_shm_scan_store(),
			zer0sh_shm_mailer()
		);
	}

	return $zer0sh_shm_runner;
}

/**
 * Ejecuta el escaneo programado por WP-Cron.
 *
 * @return void
 */
function zer0sh_shm_run_scheduled_scan(): void {
	zer0sh_shm_runner()->run( \Zer0sh\SiteHealthMonitor\ScanOutcome::ORIGIN_SCHEDULED, zer0sh_shm_settings_store()->get() );
}

/**
 * Registra las recurrencias propias del plugin en WP-Cron.
 *
 * @param array $schedules Registro de recurrencias existente.
 *
 * @return array
 */
function zer0sh_shm_cron_schedules( array $schedules ): array {
	return \Zer0sh\SiteHealthMonitor\Cron::add_schedules( $schedules );
}

/**
 * Programa el escaneo al activar el plugin.
 *
 * @return void
 */
function zer0sh_shm_activate(): void {
	zer0sh_shm_cron()->schedule( zer0sh_shm_settings_store()->get() );
}

/**
 * Limpia el evento cron al desactivar el plugin.
 *
 * @return void
 */
function zer0sh_shm_deactivate(): void {
	zer0sh_shm_cron()->clear();
}
