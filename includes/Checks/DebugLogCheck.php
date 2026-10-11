<?php
/**
 * Check de WP_DEBUG_LOG.
 *
 * @package Zer0shSiteHealthMonitor
 */

declare( strict_types=1 );

namespace Zer0sh\SiteHealthMonitor\Checks;

use Zer0sh\SiteHealthMonitor\Result;
use Zer0sh\SiteHealthMonitor\ScoreDiscounts;

/**
 * Comprueba si el registro de errores de WordPress (WP_DEBUG_LOG) está
 * activado, lo que puede dejar un debug.log accesible con datos sensibles.
 */
final class DebugLogCheck extends AbstractCheck {

	/**
	 * Fuente que indica si WP_DEBUG_LOG está activado.
	 *
	 * @var callable
	 */
	private $config_source;

	/**
	 * Constructor con fuente inyectable para tests.
	 *
	 * @param callable|null $config_source Devuelve bool: WP_DEBUG_LOG activado.
	 */
	public function __construct( ?callable $config_source = null ) {
		$this->config_source = $config_source ?? static function (): bool {
			return \defined( 'WP_DEBUG_LOG' ) && \WP_DEBUG_LOG;
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_id(): string {
		return 'security/debug_log';
	}

	/**
	 * {@inheritDoc}
	 */
	public function get_title(): string {
		return __( 'WordPress error log', 'zer0sh-site-health-monitor' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function run(): Result {
		$logging = (bool) call_user_func( $this->config_source );

		if ( ! $logging ) {
			return $this->result( Result::SEVERITY_OK, $logging );
		}

		return $this->result(
			Result::SEVERITY_WARNING,
			$logging,
			__( 'WP_DEBUG_LOG is enabled; the debug.log file can contain sensitive data and be publicly accessible. Disable it in production.', 'zer0sh-site-health-monitor' ),
			ScoreDiscounts::MINOR
		);
	}
}
